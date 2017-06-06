<?php
namespace app_web\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\Json;
use app_web\components\CController;
use yii\helpers\Url;
use common\models\WarehouseMaterialReturn;
use common\models\WarehouseMaterialReturnProduct;
use common\models\WarehouseBuying;
use common\models\WarehouseBuyingProduct;
use common\models\Admin;
use libs\common\Flow;

/**
 * 业务操作 -- 库存管理 -- 物料退货管理
 */
class WmaterialController extends CController
{
    /**
     * 物料退货列表页
     */
    public function actionIndex()
    {
        $status = Yii::$app->request->get('status');
        $keyword = Yii::$app->request->get('keyword');
        $sn = Yii::$app->request->get('sn');
        $warehouse_id = Yii::$app->request->get('warehouse_id');
        $supplier_id = Yii::$app->request->get('supplier_id');
        $beginDate = Yii::$app->request->get('beginDate');
        $endDate = Yii::$app->request->get('endDate');

        $model = new WarehouseMaterialReturn();
        $query = WarehouseMaterialReturn::find();
//        if(!Admin::checkSupperFlowAdmin()){
//            $query->andWhere(["department_id" => Admin::getDepId()]);
//        }
        $query->andWhere(['not in', 'status', [Flow::STATUS_VERIFY_REJECT, Flow::STATUS_APPROVAL_REJECT, Flow::STATUS_FINISH_REJECT, Flow::STATUS_UNION_REJECT,  Flow::STATUS_FINISH, Flow::STATUS_HANG_UP]]);
        if(is_numeric($status)){
            $query->andWhere(['status' => $status]);
        }
        $is_buckle = Yii::$app->request->get('is_buckle');
        if(is_numeric($is_buckle)){
            $query->andWhere(['is_buckle' => $is_buckle]);
        }
        if($keyword || $keyword ==='0'){
            $query->andWhere(['like', 'name', $keyword]);
        }
        if($sn || is_numeric($sn)){
           $query->andWhere(['like', 'sn', $sn]);
        }
        if($beginDate){
            $query->andFilterCompare('create_time', $beginDate." 00:00:00", '>=');
        }
        if($endDate){
            $query->andFilterCompare('create_time', $endDate." 23:59:59",  '<=');
        }
        $message = "";
        if($beginDate && $endDate && strtotime($beginDate) > strtotime($endDate)) {
            $message = "开始时间不能大于结束时间";
        }
        if(is_numeric($warehouse_id)) {
            $query->andWhere(['warehouse_id' => $warehouse_id]);
        }
        if(is_numeric($supplier_id)) {
            $query->andWhere(['supplier_id' => $supplier_id]);
        }
        
        $query->orderBy('id desc');
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 5,
                'validatePage' => false,
            ],
        ]);

        $listDatas = $dataProvider->getModels();
        $listPages = $dataProvider->getPagination();
        return $this->render('index', compact(['model', 'listDatas', 'listPages','message']));
    }

    /**
     * 新增退货申请
     * @param int $buyingId 记录ID
     */
    public function actionAddbuying()
    {
        $buyingId = Yii::$app->request->get("buyingId");
        $buyingItem = WarehouseBuying::findOne($buyingId);
        $buyingProduct = WarehouseBuyingProduct::findAll(["buying_id" => $buyingId]);
        $model = new WarehouseMaterialReturn();
        $model->warehouse_id = $buyingItem->warehouse_id;
        $model->supplier_id = $buyingItem->supplier_id;
        $model->buying_id = $buyingId;
        $model->name = $buyingItem->name."退货";
        if(Yii::$app->request->post()) {
            if($buyingItem->status == Flow::STATUS_FINISH ) {
                $return['message'] = "采购入库状态错误";
                return Json::encode($return);
            }
            $result = $model->addMateial(Yii::$app->request->post());
            if($result["state"]) {
                $return["message"] = "新增成功";
                $return["type"] = "url";
                $return["url"] = Url::to(["wmaterial/index"]);
                return Json::encode($return);
            }
            $return['message'] = is_array($result["message"]) ? reset($result["message"]) : $result["message"];
            return Json::encode($return);
        }
        return $this->render("addorupdate", compact("buyingItem", "buyingProduct", "model"));
    }
    
    /**
     * 新增退货申请
     */
    public function actionAdd() {
        $model = new WarehouseMaterialReturn();
        if(Yii::$app->request->post()) {
            $result = $model->addMateial(Yii::$app->request->post());
            if($result["state"]) {
                $return["message"] = "新增成功";
                $return["type"] = "url";
                $return["url"] = Url::to(["wmaterial/index"]);
                return Json::encode($return);
            }
            $return['message'] = is_array($result["message"]) ? reset($result["message"]) : $result["message"];
            return Json::encode($return);
        }
        return $this->render("add", compact("model"));
    }
    
    /**
     * 物料退货详情页
     * @param type $id 记录ID
     */
    public function actionInfo($id)
    {
        $model = WarehouseMaterialReturn::findOne($id);
        $info = WarehouseMaterialReturnProduct::findAll(["material_return_id" => $id]);
        return $this->render('info', compact('model', 'info'));
    }
    
    /**
     * 审核操作
     * @param type $id 记录ID
     */
    public function actionVerify($id) 
    {
        $model = WarehouseMaterialReturn::findOne($id);
        $remark = Yii::$app->request->get('remark');
        $result = Flow::Verify(Flow::TYPE_MATERIALRETURN, $model, $remark);
        return Json::encode($result);
    }
    
    /**
     * 批准操作
     * @param type $id 记录ID
     */
    public function actionApproval($id)
    {
        $model = WarehouseMaterialReturn::findOne($id);
        $remark = Yii::$app->request->get('remark');
        $result = Flow::Approval(Flow::TYPE_MATERIALRETURN, $model, $remark);
        return Json::encode($result);
    }
    
    /**
     * 完成详情页
     * @param type $id 记录ID
     */
    public function actionFinish($id)
    {
        $model = WarehouseMaterialReturn::findOne($id);
        $remark = Yii::$app->request->get('remark');
        $result = Flow::Finish(Flow::TYPE_MATERIALRETURN, $model, $remark);
        return Json::encode($result);
    }

    /**
     * 驳回操作
     * @param type $id 记录ID
     */
    public function actionReject()
    {
        $id = Yii::$app->request->get('id');
        $failCause = Yii::$app->request->get('failCause');
        $model = WarehouseMaterialReturn::findOne($id);
        $result = Flow::Reject(Flow::TYPE_MATERIALRETURN, $model, $failCause);
        return Json::encode($result);
    }
}
