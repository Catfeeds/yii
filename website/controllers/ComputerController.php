<?php

namespace app_web\controllers;

use common\models\Computer;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\Json;
use app_web\components\CController;
use yii\web\UploadedFile;
use libs\Utils;
use yii\helpers\Url;
use moonland\phpexcel\Excel;
use common\models\Config;
use common\models\Role;

/**
 * 系统 基础数据管理
 * 业务计算机
 */
class ComputerController extends CController {
    /**
     * 业务计算机列表页
     */
    public function actionIndex() {
        $position = Yii::$app->request->get('position');
        $type = Yii::$app->request->get('type');
        $status = Yii::$app->request->get('status');
        $keyword = Yii::$app->request->get('keyword');
        $model = new Computer();
        $query = Computer::find();
        if (is_numeric($type)) {
            $query->andWhere(['type' => $type]);
        }
        if (is_numeric($position)) {
            $query->andWhere(['position' => $position]);
        }
        if ($keyword || is_numeric($keyword)) {
            $query->andWhere(['like', 'name', $keyword]);
        }
        if (is_numeric($status)) {
            $query->andWhere(['status' => $status]);
        } else {
            $query->andWhere(['status' => [0, 1]]);
        }
        $query->orderBy('status desc, id desc');
        $isDownload = Yii::$app->request->get("isDownload");
        if($isDownload) {
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
        $computerNum = Config::findOne(['set_name' => 'computer_num']);
        return $this->render('index', compact(['model', 'listDatas', 'listPages', 'computerNum']));
    }

    /**
     * 加载新增或修改业务计算机页面
     * @param type $id ID
     * @return type
     */
    public function actionForm($id = 0) {
        if ($id) {
            $model = Computer::findOne($id);
        } else {
            $model = new Computer;
            $model->status = Computer::STATUS_OK;
        }
        return $this->renderPartial('_form', compact(['model']));
    }

    /**
     * 创建新的业务计算机
     */
    public function actionCreate() {
        $model = new Computer();
        if ($model->load(Yii::$app->request->post())) {
            $computerNumModel = Config::findOne(['set_name' => 'computer_num']);
            $computerNum = $computerNumModel ? $computerNumModel->set_value : 20;
            $nowNum = Computer::find()->count();
            if($nowNum >= $computerNum) {
                $return['message'] = "已超过授权台数".$computerNum."台，请联系中荣恒科技有限公司";
                return Json::encode($return);
            }
            if($model->save()){
                $return['html'] = $this->renderPartial('_list', ['data' => $model, "key" => -1]);
            } else {
                $errors = $model->getFirstErrors();
                $return['message'] = join("\n", $errors);
            }
            return Json::encode($return);
        }
    }

    /**
     * 修改业务计算机记录
     * @param type $id ID
     * @return type
     */
    public function actionUpdate($id) {
        $model = Computer::findOne($id);
        if ($model->load(Yii::$app->request->post())) {
            if($model->save()){
                $return['html'] = $this->renderPartial('_list', ['data' => $model, "key" => -1]);
            } else {
                $errors = $model->getFirstErrors();
                $return['message'] = join("\n", $errors);
            }
            return Json::encode($return);
        }
    }

    /**
     * 删除业务计算机
     * @param type $id ID
     * @return type
     */
    public function actionDelete($id) {
        $model = Computer::findOne($id);
        if($model->status == Computer::STATUS_OK) {
            $return['error'] = 1;
            $return['message'] = '状态错误，无法删除！';
            return Json::encode($return);
        }
        if ($model->delete()) {
            $return['error'] = 0;
            $return["message"] = "删除成功";
        } else {
            $return['error'] = 1;
            $return['message'] = '操作不成功！';
        }
        return Json::encode($return);
    }
    
    /**
     * 设置业务计算机授权台数
     */
    public function actionSetnum() {
        $model = Config::findOne(['set_name' => 'computer_num']);
        if (!$model) {
            $model = new Config();
            $model->set_name = 'computer_num';
            $model->set_desc = '业务计算机授权台数';
            $model->set_value = 20;
            $model->group_id = 5;
        }
        if ($model->load(Yii::$app->request->post())) {
            $model->set_value = strip_tags($model->set_value);
            if($model->save()) {
                $return["message"] = "设置成功";
                $return["type"] = "url";
                $return["url"] = Url::to(["computer/index"]);
                return Json::encode($return);
            }
            $return['message'] = reset($model->getFirstErrors());
            return Json::encode($return);
        } else {
            return $this->render('setnum', ['model' => $model]);
        }
    }
    
    /**
     * 数据导出
     * @param type $query 查询对象
     */
    public function downloadIndex($query) {
        $all = $query->all();
        $datas = [];    
        foreach ($all as $key => $val) {
            $datas[] = [
                'id' => $key+1,
                'name' => $val->name,
                'mac' => $val->mac,
                'type' => $val->showType(),
                'role_id' => $val->role_id ? Role::getNameByRoleId($val->role_id) : "全部",
                'address' => $val->showPosition(),
                'status' => $val->showStatus(),
            ];
        }
        $columns = [
            [ 'attribute' => 'id','header' => '序号'],
            [ 'attribute' => 'name','header' => '名称'],
            [ 'attribute' => 'mac','header' => 'mac地址'],
            [ 'attribute' => 'type','header' => '类别'],
            [ 'attribute' => 'role_id','header' => '所属角色'],
            [ 'attribute' => 'address','header' => '位置'],
            [ 'attribute' => 'status','header' => '状态'],
        ];
        return Utils::downloadExcel($datas, $columns, "业务计算机列表");
    }
    
    /**
     * 下载导入模板
     */
    public function actionDowntemplate() {
        $datas[] = [
            'name' => "",
            'mac' => "",
            'type' => "",
            'role_id' => "",
            'address' => "",
            'status' => "",
        ];
        $columns = [
            [ 'attribute' => 'name','header' => '名称[必填]'],
            [ 'attribute' => 'mac','header' => 'mac地址[必填]'],
            [ 'attribute' => 'type','header' => '类别 0[服务器] 1[库管] 2[财务]'],
            [ 'attribute' => 'role_id','header' => '所属角色[必填][0代表全部]'],
            [ 'attribute' => 'address','header' => '位置 0[办公区] 1[库房] 2[柜台]'],
            [ 'attribute' => 'status','header' => '状态 1[有效] 0[无效]'],
        ];
        return Utils::downloadExcel($datas, $columns, "业务计算机模板");
    }
    
    /**
     * 导入业务计算机记录
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
            $computerNumModel = Config::findOne(['set_name' => 'computer_num']);
            $computerNum = $computerNumModel ? $computerNumModel->set_value : 20;
            try {
                Utils::delFile($file);
                foreach($datas as $data){
                    $nowNum = Computer::find()->count();
                    if($nowNum >= $computerNum) {
                        $transaction->rollBack();
                        return Json::encode(["result" => "Error", "message" => "导入的数量已超过授权台数".$computerNum."台，请联系中荣恒科技有限公司"]);
                    }
                    $model = new Computer();
                    $model->create_time = date("Y-m-d H:i:s");
                    foreach ($data as $key => $val) {
                        if($key == "名称[必填]") {
                            $model->name = "$val";
                        }
                        if($key == "mac地址[必填]") {
                            $model->mac = "$val";
                        }
                        if($key == "类别 0[服务器] 1[库管] 2[财务]") {
                            $model->type = in_array($val, [0,1,2]) ? $val : 0;
                        }
                        if($key == "所属角色[必填][0代表全部]") {
                            if($val == "0") {
                                $model->role_id = 0;
                            } else {
                                $roleItem = Role::findOne(["name" => $val]);
                                if(!$roleItem) {
                                    $transaction->rollBack();
                                    return Json::encode(["result" => "Error", "message" => "所属角色名称错误 "]);
                                }
                                $model->role_id = $roleItem->id;
                            }
                        }
                        if($key == "位置 0[办公区] 1[库房] 2[柜台]") {
                            $model->position = in_array($val, [0,1,2]) ? $val : 0;
                        }
                        if($key == "状态 1[有效] 0[无效]") {
                            $model->status = in_array($val, [0,1]) ? $val : 0;
                        }
                    }
                    if(!$model->save()){
                        $errors = $model->getFirstErrors();
                        $transaction->rollBack();
                        return Json::encode(["result" => "Error", "message" => join("\n", $errors)]);
                    }
                }
                $transaction->commit();
            } catch (Exception $exc) {
                $transaction->rollBack();
                return Json::encode(["result" => "Error", "message" => $exc->getTraceAsString()]);
            }
            return Json::encode(["result" => "Success"]);
        }
        return Json::encode(["result" => "Error", "message" => "网络异常"]);
    }
}
