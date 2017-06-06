<?php

namespace app_web\controllers;

use common\models\Product;
use common\models\AdminLog;
use libs\Utils;
use Yii;
use yii\helpers\Url;
use yii\data\ActiveDataProvider;
use yii\helpers\Json;
use Exception;
use app_web\components\CController;
use common\models\Admin;
use common\models\ProductUpdate;
use common\models\FlowConfig;
use libs\common\Flow;
use common\models\BusinessAll;

/**
 * 业务基础数据 -- 物料管理
 */
class ProductController extends CController {
    /**
     * 物料列表页
     */
    public function actionIndex() {
        $level = Yii::$app->request->get('level');
        $status = Yii::$app->request->get('status');
        $modify_status= Yii::$app->request->get('modify_status');
        $keyword = Yii::$app->request->get('keyword');
        $model = new Product();
        $query = Product::find();
        if(is_numeric($modify_status)) {
            $query->andWhere(['modify_status' => $modify_status]);
        } else {
            $query->andWhere(['not in', 'modify_status', [Flow::STATUS_VERIFY_REJECT, Flow::STATUS_APPROVAL_REJECT, Flow::STATUS_FINISH_REJECT, Flow::STATUS_UNION_REJECT, Flow::STATUS_HANG_UP]]);
        }
        if ($level) {
            $query->andWhere(['level' => $level]);
        }
        if (is_numeric($status)) {
            $query->andWhere(['status' => $status]);
        }
        if ($keyword || is_numeric($keyword)) {
            $query->andWhere(['like', 'name', $keyword]);
        }
        $query->orderBy('modify_status asc, status desc, id desc');
        $isDownload = Yii::$app->request->get("isDownload");
        if ($isDownload) {
            $this->downloadIndex($query);
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10,
                'validatePage' => false,
            ],
        ]);
        $listDatas = $dataProvider->getModels();
        $listPages = $dataProvider->getPagination();

        return $this->render('index', compact(['model', 'listDatas', 'listPages']));
    }

    /**
     * 物料导出
     */
    public function downloadIndex($query) {
        $all = $query->all();
        $datas = [];    
        foreach ($all as $key => $val) {
            $datas[] = [
                'key' => $key+1,
                'name' => $val->name,
                'id' => $val->id,
                'supplierName' => $val->showSupplierName(),
                'supplier_product_id' => $val->supplier_product_id,
                'material_type' => Product::showTypeName($val->material_type),
                'barcode' => $val->barcode,
                'spec' => $val->spec,
                'unit' => $val->unit,
                'purchase_price' => $val->purchase_price,
                'sale_price' => $val->sale_price,
                'inventoryWarning' => $val->showInventoryWarning(),
                'verify_admin_id' => Admin::getNameById($val->verify_admin_id),
                'verify_time' => $val->verify_time,
                'approval_admin_id' => Admin::getNameById($val->approval_admin_id),
                'approval_time' => $val->approval_time,
                'operation_admin_id' => Admin::getNameById($val->operation_admin_id),
                'operation_time' => $val->operation_time,
                'status' => $val->showStatus(),
                'modifyStatus' => $val->showModifyStatus(),
            ];
        }
        $columns = [
            [ 'attribute' => 'key','header' => '序号'],
            [ 'attribute' => 'name','header' => '物料名'],
            [ 'attribute' => 'id','header' => '物料ID'],
            [ 'attribute' => 'supplierName','header' => '供应商'],
            [ 'attribute' => 'supplier_product_id','header' => '供应商出品ID'],
            [ 'attribute' => 'material_type','header' => '物料类型'],
            [ 'attribute' => 'barcode','header' => '条形码ID'],
            [ 'attribute' => 'spec','header' => '规格'],
            [ 'attribute' => 'unit','header' => '单位'],
            [ 'attribute' => 'purchase_price','header' => '采购价格'],
            [ 'attribute' => 'sale_price','header' => '销售价格'],
            [ 'attribute' => 'inventoryWarning','header' => '库存预警'],
            [ 'attribute' => 'verify_admin_id','header' => '审核人'],
            [ 'attribute' => 'verify_time','header' => '审核时间'],
            [ 'attribute' => 'approval_admin_id','header' => '批准人'],
            [ 'attribute' => 'approval_time','header' => '批准时间'],
            [ 'attribute' => 'operation_admin_id','header' => '执行人'],
            [ 'attribute' => 'operation_time','header' => '执行时间'],
            [ 'attribute' => 'status','header' => '当前状态'],
            [ 'attribute' => 'modifyStatus','header' => '流程状态'],            
        ];
        return Utils::downloadExcel($datas, $columns, "物料列表");
    }

    /**
     * 物料详情页
     */
    public function actionInfo($id) {
        $model = Product::findOne($id);
        echo $this->render("info", compact('model'));
    }

    /**
     * 物料录入操作
     * @param type $id 物料ID
     */
    public function actionUpdate($id) {
        $model = Product::findOne($id);
        if($model->modify_status != Product::MODIFY_STATUS_APPLY_UPDATE) {
            $this->redirect("index.php?r=product/index");
            \Yii::$app->end();
        }
        if ($model->load(Yii::$app->request->post())) {
            $transaction = \Yii::$app->db->beginTransaction();
            try{
                if(!$model->barcode) {
                    throw new Exception("条形码不能为空");
                }
                $model->modify_status = Product::MODIFY_STATUS_APPLY_VERIFY;
                $model->status = Product::STATUS_INVALID;
                if (!$model->save()) {
                    $message = $model->getFirstErrors();
                    throw new Exception(reset($message));
                }
                $supplier = array($model->supplier_id);
                $meterialType = array($model->product_category_id);
                $result = Flow::confirmFollowAdminId(Flow::TYPE_ADDPRODUCT, $model, $model->sale_price, time(), 0, $supplier, $meterialType);
                if(!$result["state"]) {
                    throw new Exception($result["message"]);
                }
                if($model->modify_status != Flow::STATUS_FINISH) {
                    $model->modify_status = $model->status;
                    $model->status = Product::STATUS_INVALID;
                    if (!$model->save()) {
                        $message = $model->getFirstErrors();
                        throw new Exception(reset($message));
                    }
                }
                $businessModel = new BusinessAll();
                $business = $businessModel->addBusiness($model, Flow::TYPE_ADDPRODUCT);
                if(!$business["state"]) {
                    $transaction->rollBack();
                    throw new Exception(reset($business["message"]));
                }
                if($model->modify_status == Flow::STATUS_FINISH){
                    $result = $model->Finish();
                    if(!$result["state"]) {
                        $transaction->rollBack();
                        throw new Exception(reset($business["message"]));
                    }
                }
                $code = 'update_product';
                $content = '物料录入成功' . $model->id;
                AdminLog::addLog($code, $content);
                $return["message"] = "录入成功";
                $return["type"] = "url";
                $return["url"] = Url::to(["product/info", "id" => $id]);
                $transaction->commit();
                return Json::encode($return);
            } catch (Exception $ex) {
                $transaction->rollBack();
                return Json::encode(["message" => $ex->getMessage()]);
            }
        }
        echo $this->render("update", compact('model'));
    }
    
    /**
     * 物料修改页面
     * @param type $id 物料ID
     * @return type
     */
    public function actionEdit($id) {
        $model = Product::findOne($id);
        if(Yii::$app->request->post()) {
            $productUpdate = new ProductUpdate();
            $result = $productUpdate->addUpdate($model, Yii::$app->request->post());
            if($result["state"]) {
                $return["message"] = "修改申请成功";
                $return["type"] = "url";
                $return["url"] = Url::to(["productupdate/index"]);
                return Json::encode($return);
            }
            $return['message'] = is_array($result["message"]) ? reset($result["message"]) : $result["message"];
            return Json::encode($return);
        } 
        return $this->render("edit", compact("model"));
    }

    /**
     * 审核
     * @param type $id 物料ID
     * @return type
     */
    public function actionVerify($id) {
        $model = Product::findOne($id);
        $remark = \Yii::$app->request->get("remark");
        $result = $model->Verify($remark);
        return Json::encode($result);
    }

    /**
     * 批准
     * @param type $id 物料ID
     * @return type
     */
    public function actionApproval($id) {
        $model = Product::findOne($id);
        $remark = \Yii::$app->request->get("remark");
        $result = $model->Approval($remark);
        return Json::encode($result);
    }

    /**
     * 执行
     * @param type $id 物料ID
     * @return type
     */
    public function actionFinish($id) {
        $model = Product::findOne($id);
        $remark = \Yii::$app->request->get("remark");
        $result = $model->Finish($remark);
        $result["error"] = $result["state"] ? 0 : 1;
        return Json::encode($result);
    }

    /**
     * 完成
     * @param type $id 物料ID
     * @return type
     */
    public function actionReject() {
        $id = Yii::$app->request->get('id');
        $failCause = Yii::$app->request->get('failCause');
        $model = Product::findOne($id);
        $result = $model->Reject($failCause);
        return Json::encode($result);
    }

    /**
     * 异步获取物料列表
     */
    public function actionAjaxproductlist() {
        $barcode = Yii::$app->request->get('barcode');
        $keyword = Yii::$app->request->get('keyword');
        $model = new Product();
        $query = Product::find();
        $query->andWhere(['status' => Product::STATUS_VALID]);
        if ($barcode) {
            $query->andWhere(['barcode' => $barcode]);
        }
        if ($keyword) {
            $query->andWhere(['like', 'name', $keyword]);
        }
        $query->orderBy('id desc');
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10,
                'validatePage' => false,
            ],
        ]);
        $listDatas = $dataProvider->getModels();
        $listPages = $dataProvider->getPagination();
        return $this->renderPartial('_productlist', compact(['model', 'listDatas', 'listPages']));
    }

}
