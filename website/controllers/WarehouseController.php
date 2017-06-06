<?php

namespace app_web\controllers;

use common\models\Warehouse;
use common\models\Admin;
use common\models\AdminLog;
use common\models\WarehouseProduct;
use common\models\Area;
use common\models\Department;
use common\models\ProductStock;
use common\models\BusinessAll;
use libs\Utils;
use Yii;
use Exception;
use yii\data\ActiveDataProvider;
use yii\helpers\Json;
use app_web\components\CController;
use yii\web\UploadedFile;
use yii\helpers\ArrayHelper;
use moonland\phpexcel\Excel;

/**
 * 业务基础数据 -- 仓库分区
 */
class WarehouseController extends CController {

    /**
     * 仓库分区列表
     */
    public function actionIndex() {
        $status = Yii::$app->request->get('status');
        $keyword = Yii::$app->request->get('keyword');
        $department_id = Yii::$app->request->get('department_id');
        $model = new Warehouse();
        $query = Warehouse::find();
//        if(!Admin::checkSupperFlowAdmin()){
//            $query->andWhere(["department_id" => Admin::getDepId()]);
//        }
        if (is_numeric($status)) {
            $query->andWhere(['status' => $status]);
        } else {
            $query->andWhere(['status' => [0, 1]]);
        }
        if ($keyword || is_numeric($keyword)) {
            $query->andWhere(['like', 'name', $keyword]);
        }
        if(is_numeric($department_id)){
            $query->andWhere(['department_id' => $department_id]);
        }
        $query->orderBy('status desc, id desc');
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
     * 加载创建或修改的仓库分区记录页面
     * @param type $id 记录ID
     * @return type
     */
    public function actionForm($id = 0) {
        if ($id) {
            $model = Warehouse::findOne($id);
        } else {
            $model = new Warehouse;
            $model->status = Warehouse::STATUS_OK;
        }
        return $this->renderPartial('_form', compact(['model']));
    }

    /**
     * 创建仓库分区记录
     * @return type
     */
    public function actionCreate() {
        $model = new Warehouse();
        if ($model->load(Yii::$app->request->post())) {
            if(!$model->area_id ||  $model->area_id <= 0) {
                $return['message'] = "请选择仓库所属地区";
                return Json::encode($return);
            }
            if(!$model->save()) {
                $errors = $model->getFirstErrors();
                $return['message'] = join("\n", $errors);
                return Json::encode($return);
            }
            //记录日志
            $code = 'add_Warehouse';
            $content = '新增仓库' . $model->id;
            AdminLog::addLog($code, $content);
            $return['html'] = 1;
            $return['message'] = "新增成功";
            return Json::encode($return);
        } 
    }

    /**
     * 修改仓库分区记录
     * @param type $id 记录ID
     * @return type
     */
    public function actionUpdate($id) {
        $model = Warehouse::findOne($id);
        if ($model->load(Yii::$app->request->post())) {
            if(!$model->area_id ||  $model->area_id <= 0) {
                $return['message'] = "请选择仓库所属地区";
                return Json::encode($return);
            }
            if(!$model->save()) {
                $errors = $model->getFirstErrors();
                $return['message'] = join("\n", $errors);
                return Json::encode($return);
            }
            //记录日志
            $code = 'update_Warehouse';
            $content = '编辑仓库' . $model->id;
            AdminLog::addLog($code, $content);
            $return['html'] = $this->renderPartial('_list', ['data' => $model, "key" => -1]);
        } else {
            $return['message'] = '操作不成功！';
        }
        return Json::encode($return);
    }

    /**
     * 删除仓库分区记录
     * @param type $id 记录ID
     * @return type
     */
    public function actionDelete($id) {
        $model = Warehouse::findOne($id);
        if(!$model || $model->status == 1) {
            $return['error'] = 1;
            $return['message'] = '无法删除有效的仓库，请刷新再试！';
            return Json::encode($return);
        }
        $stock = ProductStock::find()->andWhere(["warehouse_id" => $id])->andWhere([">", "number", "0"])->one();
        if($stock) {
            $return['error'] = 1;
            $return['message'] = '该仓库还存在物料，无法删除！';
            return Json::encode($return);
        }
        if(!$model->delete()){
            $return['error'] = 1;
            $message = $model->getFirstErrors();
            $return['message'] = reset($message);
            return Json::encode($return);
        }
        $code = 'delete_warehouse';
        $content = '删除仓库' . $model->id;
        AdminLog::addLog($code, $content);
        ProductStock::deleteAll(["warehouse_id" => $id]);
        $return['error'] = 0;
        $return['message'] = '删除成功！';
        return Json::encode($return);
    }

    /**
     * 获取仓库库存物料列表
     * @param type $id 仓库ID
     * @return type
     */
    public function actionProductList($id) {
        $model = new WarehouseProduct();
        $query = WarehouseProduct::find();
        $query->andWhere(['Warehouse_id', $id]);
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
     * 异步获取部门下所有的仓库列表
     * @param int $id 部门ID
     */
    public function actionAjaxdepartmentwarehouselist() {
        $id = Yii::$app->request->get("id");
        if (!$id) {
            return 0;
        }
        $item = Warehouse::findAll(["department_id" => $id]);
        return $this->renderPartial("_warehouselist", compact('item'));
    }

    /**
     * 异步获取部门下所有的仓库列表
     * @param int $departmentId 部门ID
     */
    public function actionAjaxwarehousebydepartmentid() {
        $departmentId = Yii::$app->request->get("departmentId");
        $item = Warehouse::findAll(["department_id" => $departmentId]);
        $result = ArrayHelper::map($item, "id", "name");
        echo Json::encode($result);
        Yii::$app->end();
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
                'name' => $val->name,
                'type' => $val->showType(),
                'num' => $val->num,
                'areaList' => Area::getNameById($val->area_id),
                'isSale' => $val->showSale(),
                'department' => Department::getNameById($val->department_id),
                'status' => $val->showStatus(),
            ];
        }
        $columns = [
            [ 'attribute' => 'id','header' => '序号'],
            [ 'attribute' => 'name','header' => '仓库名'],
            [ 'attribute' => 'type','header' => '性质类别'],
            [ 'attribute' => 'num','header' => '分区编号'],
            [ 'attribute' => 'areaList','header' => '所属地区'],
            [ 'attribute' => 'isSale','header' => '是否销售'],
            [ 'attribute' => 'department','header' => '所属部门'],
            [ 'attribute' => 'status','header' => '状态'],
        ];
        return Utils::downloadExcel($datas, $columns, "供应商物料出品列表");
    }
    
    /**
     * 下载导入模板
     */
    public function actionDowntemplate() {
        $datas[] = [
            'name' => "",
            'type' => "",
            'num' => "",
            'province' => "",
            'city' => "",
            'area' => "",
            'isSale' => "",
            'department' => "",
            'status' => "",
        ];
        $columns = [
            [ 'attribute' => 'name','header' => '仓库名[必填]'],
            [ 'attribute' => 'type','header' => '性质类别1[存贮] 2[存贮销售] 3[销售]'],
            [ 'attribute' => 'num','header' => '分区编号[必填]'],
            [ 'attribute' => 'province','header' => '所属地区[省份名称]'],
            [ 'attribute' => 'city','header' => '所属地区[市级名称]'],
            [ 'attribute' => 'area','header' => '所属地区[县级名称]'],
            [ 'attribute' => 'isSale','header' => '是否销售1[销售] 0[不销售]'],
            [ 'attribute' => 'department','header' => '所属部门[必填]'],
            [ 'attribute' => 'status','header' => '状态 1[有效] 0[无效]'],
        ];
        return Utils::downloadExcel($datas, $columns, "仓库分区导入模板");
    }
    
    /**
     * 导入仓库分区记录
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
                    $model = new Warehouse();
                    $model->create_time = date("Y-m-d H:i:s");
                    $province = $city = 0;
                    foreach ($data as $key => $val) {
                        if($key == "仓库名[必填]") {
                            $model->name = "$val";
                        }
                        if($key == "性质类别1[存贮] 2[存贮销售] 3[销售]") {
                            $type = 1;
                            if(in_array($val, [1,2,3])){
                                $type = $val;
                            }
                            $model->type = $type;
                        }
                        if($key == "分区编号[必填]") {
                            $model->num = "$val";
                        }
                        if($key == "所属地区[省份名称]") {
                            $item = Area::findOne(["name" => $val, "status" => 1]);
                            if(!$item) {
                                throw new Exception("请填写正确的省份名称");
                            }
                            $province = $item->id;
                        }
                        if($key == "所属地区[市级名称]") {
                            $item = Area::findOne(["name" => $val, "status" => 1, 'parentId' => $province]);
                            if(!$item) {
                                throw new Exception("请填写正确的市级名称");
                            }
                            $city = $item->id;
                        }
                        if($key == "所属地区[县级名称]") {
                            $item = Area::findOne(["name" => $val, "status" => 1, 'parentId' => $city]);
                            if(!$item) {
                                throw new Exception("请填写正确的县区名称");
                            }
                            $model->area_id = $item->id;
                        }
                        if($key == "是否销售1[销售] 0[不销售]") {
                            $isSale = 0;
                            if(in_array($val, [0,1])){
                                $isSale = $val;
                            }
                            $model->is_sale = $isSale;
                        }
                        if($key == "所属部门[必填]") {
                            $item = Department::findOne(["name" => $val]);
                            if(!$item) {
                                throw new Exception("请填写正确的所属部门");
                            }
                            $model->department_id = $item->id;
                        }
                        if($key == "状态 1[有效] 0[无效]") {
                            $status = 0;
                            if(in_array($val, [0,1])){
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
