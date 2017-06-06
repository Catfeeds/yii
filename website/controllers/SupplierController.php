<?php

namespace app_web\controllers;

use common\models\Supplier;
use common\models\AdminLog;
use common\models\Product;
use common\models\ProductStock;
use libs\Utils;
use Yii;
use Exception;
use yii\data\ActiveDataProvider;
use yii\helpers\Json;
use app_web\components\CController;
use yii\web\UploadedFile;
use moonland\phpexcel\Excel;

/**
 * 业务基础数据管理 -- 供应商管理
 */
class SupplierController extends CController {
    /**
     * 供应商列表页
     */
    public function actionIndex() {
        $level = Yii::$app->request->get('level');
        $status = Yii::$app->request->get('status');
        $keyword = Yii::$app->request->get('keyword');
        $pay_period = Yii::$app->request->get('pay_period');
        $model = new Supplier();
        $query = Supplier::find();
        if ($level) {
            $query->andWhere(['level' => $level]);
        }
        if (is_numeric($status)) {
            $query->andWhere(['status' => $status]);
        } else {
            $query->andWhere(['status' => [0, 1]]);
        }
        if ($keyword || is_numeric($keyword)) {
            $query->andWhere(['like', 'name', $keyword]);
        }
        if (is_numeric($pay_period)) {
            $query->andWhere(['pay_period' => $pay_period]);
        } 
        $query->orderBy('status desc, id desc');
        $isDownload = Yii::$app->request->get("isDownload");
        if ($isDownload) {
            $this->downloadIndex($query);
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 5,
                'validatePage' => false,
            ],
        ]);
        $listDatas = $dataProvider->getModels();
        $listPages = $dataProvider->getPagination();
        return $this->render('index', compact(['model', 'listDatas', 'listPages']));
    }

    /**
     * 加载供应商创建或修改页
     * @param type $id 记录ID
     * @return type
     */
    public function actionForm($id = 0) {
        if ($id) {
            $model = Supplier::findOne($id);
        } else {
            $model = new Supplier;
            $model->status = Supplier::STATUS_OK;
        }
        return $this->renderPartial('_form', compact(['model']));
    }

    /**
     * 创建新的供应商
     */
    public function actionCreate() {
        $model = new Supplier();
        if ($model->load(Yii::$app->request->post())) {
            if(!$model->save()) {
                $errors = $model->getFirstErrors();
                $return['message'] = join("\n", $errors);
                return Json::encode($return);
            }
            $return['html'] = $this->renderPartial('_list', ['data' => $model, 'key' => -1]);
        } else {
            $errors = $model->getFirstErrors();
            $return['message'] = join("\n", $errors);
        }
        //记录日志
        $code = 'add_supplier';
        $content = '新增供应商' . $model->id;
        AdminLog::addLog($code, $content);
        return Json::encode($return);
    }

    /**
     * 修改供应商记录
     * @param type $id 记录ID
     * @return type
     */
    public function actionUpdate($id) {
        $model = Supplier::findOne($id);
        if ($model->load(Yii::$app->request->post())) {
            if(!$model->save()) {
                $errors = $model->getFirstErrors();
                $return['message'] = join("\n", $errors);
                return Json::encode($return);
            }
            //记录日志
            $code = 'update_supplier';
            $content = '编辑供应商' . $model->id;
            AdminLog::addLog($code, $content);
            $return['html'] = $this->renderPartial('_list', ['data' => $model, "key" => -1]);
        } else {
            $return['message'] = '操作不成功！';
        }
        return Json::encode($return);
    }

    /**
     * 删除供应商
     * @param type $id 记录ID
     * @return type
     */
    public function actionDelete($id) {
        $model = Supplier::findOne($id);
        if(!$model || $model->status == 1) {
            $return['error'] = 1;
            $return['message'] = '无法删除有效的供应商！';
            return Json::encode($return);
        }
        $stock = ProductStock::findOne(["supplier_id" => $id]);
        if($stock) {
            $return['error'] = 1;
            $return['message'] = '该供应商还有物料存在仓库，无法删除！';
            return Json::encode($return);
        }
        $product = Product::findOne(["supplier_id" => $id]);
        if($product) {
            $return['error'] = 1;
            $return['message'] = '该供应商已上传物料，无法删除！';
            return Json::encode($return);
        }
        if($model->delete()) {
            $code = 'delete_supplier';
            $content = '删除供应商' . $model->id;
            AdminLog::addLog($code, $content);
            $return['error'] = 0;
            $return['message'] = '删除成功！';
        } else {
            $return['error'] = 1;
            $return['message'] = '操作不成功！';
        }
        return Json::encode($return);
    }

    /**
     * 获取供应商下出品记录
     * @param type $id 供应商ID
     * @return type
     */
    public function actionProductList($id) {
        $model = new SupplierProduct();
        $query = SupplierProduct::find();
        $query->andWhere(['supplier_id', $id]);
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
        return $this->render('productList', compact(['model', 'listDatas', 'listPages']));
    }
    
    /**
     * 下载记录
     * @param type $query 下载记录条件
     * @author dean
     */
    public function downloadIndex($query) {
        $all = $query->all();
        $datas = [];    
        foreach ($all as $key => $val) {
            $datas[] = [
                'id' => $key+1,
                'name' => $val->showName(),
                'number' => $val->showNumber(),
                'level' => $val->showLevel(),
                'pay_period' => $val->showPayPeriod(),
                'status' => $val->showStatus(),
            ];
        }
        $columns = [
            [ 'attribute' => 'id','header' => '序号'],
            [ 'attribute' => 'name','header' => '供应商'],
            [ 'attribute' => 'number','header' => '供应商编号'],
            [ 'attribute' => 'level','header' => '分级'],
            [ 'attribute' => 'pay_period','header' => '付款账期'],
            [ 'attribute' => 'status','header' => '状态'],
        ];
        return Utils::downloadExcel($datas, $columns, "供应商列表");
    }
    
    /**
     * 下载导入模板
     */
    public function actionDowntemplate() {
        $datas[] = [
            'name' => "",
            'number' => "",
            'level' => "",
            'pay_period' => "",
            'status' => "",
        ];
        $columns = [
            [ 'attribute' => 'name','header' => '供应商[必填]'],
            [ 'attribute' => 'number','header' => '供应商编号[必填]'],
            [ 'attribute' => 'level','header' => '分级 [A] [B] [C] [D]'],
            [ 'attribute' => 'pay_period','header' => '付款账期 1[日结] 2[周结] 3[月结] 4[季度结] 5[年结]'],
            [ 'attribute' => 'status','header' => '状态 1[有效] 0[无效]'],
        ];
        return Utils::downloadExcel($datas, $columns, "供应商模板");
    }
    
    /**
     * 导入供应商记录
     * @return type
     * @throws Exception
     */
    public function actionImport() {
        set_time_limit(0);
        if(Yii::$app->request->getIsPost()){
            $data = UploadedFile::getInstanceByName('excel');
            if(!$data){
                return Json::encode(["result" => "Error", "message" => "上传失败"]);
            }
            if(!in_array($data->type, ["application/vnd.ms-excel", "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet", "application/octet-stream"])) {
                return Json::encode(["result" => "Error", "message" => "上传文件格式错误"]);
            }
            $file = Utils::getFile(Utils::newFileName($data->getExtension()));
            if(!$data->saveAs($file)){
                return Json::encode(["result" => "Error", "message" => "上传失败"]);
            }
            $datas = Excel::import($file);
            $transaction = \Yii::$app->db->beginTransaction();
            try {
                foreach($datas as $data){
                    $model = new Supplier();
                    $model->create_time = date("Y-m-d H:i:s");
                    foreach ($data as $key => $val) {
                        if($key == "供应商[必填]") {
                            $model->name = "$val";
                        }
                        if($key == "供应商编号[必填]") {
                            $model->num = "$val";
                        }
                        if($key == "分级 [A] [B] [C] [D]") {
                            if(!in_array($val, Supplier::getLevelSelectData())) {
                                throw new Exception("请填写正确的供应商分级：A，B，C，D");
                            }
                            $model->level = $val;
                        }
                        if($key == "付款账期 1[日结] 2[周结] 3[月结] 4[季度结] 5[年结]") {
                            if(!in_array($val, array_keys(Supplier::getPayPeriodSelectData()))) {
                                throw new Exception("请填写正确的付款账期：1[日结] 2[周结] 3[月结] 4[季度结] 5[年结]");
                            }
                            $model->pay_period = $val;
                        }
                        if($key == "状态 1[有效] 0[无效]") {
                            $status = 0;
                            if(in_array($val, [0,1])) {
                                $status = $val;
                            }
                            $model->status = $status;
                        }
                    }
                    if(!$model->save()){
                        $errors = $model->getFirstErrors();
                        Utils::delFile($file);
                        $transaction->rollBack();
                        return Json::encode(["result" => "Error", "message" => join("\n", $errors)]);
                    }
                }
                Utils::delFile($file);
                $transaction->commit();
            } catch (Exception $exc) {
                Utils::delFile($file);
                $transaction->rollBack();
                return Json::encode(["result" => "Error", "message" => $exc->getMessage() ? $exc->getMessage() : $exc->getTraceAsString()]);
            }
            return Json::encode(["result" => "Success"]);
        }
        return Json::encode(["result" => "Error", "message" => "网络异常"]);
    }
}
