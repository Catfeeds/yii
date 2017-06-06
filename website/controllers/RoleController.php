<?php

namespace app_web\controllers;

use common\models\Role;
use common\models\Admin;
use common\models\AdminLog;
use common\models\Department;
use common\models\FlowConfig;
use libs\Utils;
use Yii;
use Exception;
use yii\data\ActiveDataProvider;
use yii\helpers\Json;
use app_web\components\CController;
use yii\web\UploadedFile;
use moonland\phpexcel\Excel;

/**
 * 部门基础数据 -- 角色管理
 */
class RoleController extends CController {

    /**
     * 角色列表页
     */
    public function actionIndex() {
        $keyword = Yii::$app->request->get('keyword');
        $department_id = Yii::$app->request->get('department_id');
        $model = new Role();
        $query = Role::find();
//        if(!Admin::checkSupperFlowAdmin()){
//            $query->andWhere(["department_id" => Admin::getDepId()]);
//        }
        if($keyword || is_numeric($keyword)){
            $query->andWhere(['like', 'name', $keyword]);
        }
        if(is_numeric($department_id)) {
            $query->andWhere(["department_id" => $department_id]);
        }
        $status = Yii::$app->request->get('status');
        if (is_numeric($status)) {
            $query->andWhere(['status' => $status]);
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
        return $this->render('index', compact(['model', 'listDatas', 'listPages']));
    }

    /**
     * 加载角色创建或修改页
     * @param type $id 记录ID
     * @return type
     */
    public function actionForm($id = 0) {
        if ($id) {
            $model = Role::findOne($id);
        } else {
            $model = new Role;
        }
        return $this->renderPartial('_form', compact(['model']));
    }

    /**
     * 新增角色
     */
    public function actionCreate() {
        $model = new Role();
        if ($model->load(Yii::$app->request->post())) {
            if($model->save()){
                $code = 'add_Role';
                $content = '新增角色' . $model->id;
                AdminLog::addLog($code, $content);
                $return['html'] = $this->renderPartial('_list', ['data' => $model, "key" => -1]);
            } else {
                $errors = $model->getFirstErrors();
                $return['message'] = join("\n", $errors);
            }
            return Json::encode($return);
        }
    }

    /**
     * 修改角色
     * @param type $id 角色ID
     * @return type
     */
    public function actionUpdate($id) {
        $model = Role::findOne($id);
        if ($model->load(Yii::$app->request->post())) {
            if($model->save()){
                //记录日志
                $code = 'update_Role';
                $content = '编辑角色' . $model->id;
                AdminLog::addLog($code, $content);
                $return['html'] = $this->renderPartial('_list', ['data' => $model, "key" => -1]);
            } else {
                $return['message'] = '操作不成功！';
            }
            return Json::encode($return);
        }
    }
    
    /**
     * 设置无效
     * @param type $id 角色ID
     * @return type
     */
    public function actionInvalid($id)
    {
        $model = Role::findOne($id);
        if(!$model) {
            $return['error'] = 1;
            $return['message'] = '网络异常，请刷新再试！';
            return Json::encode($return);
        }
        $query = FlowConfig::find();
        $query->orWhere(["operation_role_id" => $id]);
        $query->orWhere(["verify_role_id" => $id]);
        $query->orWhere(["approval_role_id" => $id]);
        $query->orWhere(["create_role_id" => $id]);
        $flowconfig = $query->one();
        if($flowconfig) {
            $return['error'] = 1;
            $return['message'] = '该角色还有流程使用，无法设置无效！';
            return Json::encode($return);
        }
        $admin = Admin::findOne(["role_id" => $id]);
        if($admin) {
            $return['error'] = 1;
            $return['message'] = '该角色还有员工绑定，无法设置无效！';
            return Json::encode($return);
        }
        $model->status = 0;
        if(!$model->save()){
            $return['error'] = 1;
            $message = $model->getFirstErrors();
            $return['message'] = reset($message);
            return Json::encode($return);
        }
        $code = 'invalid_role';
        $content = '设置角色无效：' . $model->id;
        AdminLog::addLog($code, $content);
        $return['error'] = 0;
        $return['message'] = '设置成功！';
        return Json::encode($return);
    }
    
    /**
     * 删除角色
     * @param type $id 角色ID
     * @return type
     */
    public function actionDelete($id) {
        $model = Role::findOne($id);
        if(!$model || $model->status == 1) {
            $return['error'] = 1;
            $return['message'] = '状态异常，请刷新再试！';
            return Json::encode($return);
        }
        $admin = Admin::findOne(["role_id" => $id]);
        if($admin) {
            $return['error'] = 1;
            $return['message'] = '该角色还有员工绑定，无法删除！';
            return Json::encode($return);
        }
        if(!$model->delete()){
            $return['error'] = 1;
            $message = $model->getFirstErrors();
            $return['message'] = reset($message);
            return Json::encode($return);
        }
        $code = 'delete_role';
        $content = '删除角色' . $model->id;
        AdminLog::addLog($code, $content);
        $return['error'] = 0;
        $return['message'] = '删除成功！';
        return Json::encode($return);
    }
    
    /**
     * 下载导入模板
     */
    public function actionDowntemplate() {
        $datas = [
            [
                "name" => "",
                "departmentId" => "",
                "status" => "",
                "is_sole" => "",
            ]
        ];
        $columns = [
            [ 'attribute' => 'name','header' => '角色[必填]'],
            [ 'attribute' => 'departmentId','header' => '所属部门[必填]'],
            [ 'attribute' => 'is_sole','header' => '是否唯一 1[是] 0[否]'],
            [ 'attribute' => 'status','header' => '状态 1[有效] 0[无效]'],
        ];
        return Utils::downloadExcel($datas, $columns, "导入角色模板");
    }

    /**
     * 导入角色记录
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
                    $model = new Role();
                    foreach ($data as $key => $val) {
                        if($key == "角色[必填]") {
                            $model->name = "$val";
                        }
                        if($key == "所属部门[必填]") {
                            $item = Department::findOne(["name" => $val]);
                            if(!$item) {
                                throw new Exception("请填写正确的部门名");
                            }
                            $model->department_id = $item->id;
                        }
                        if($key == "是否唯一 1[是] 0[否]") {
                            if(!in_array($val, [0,1])) {
                                throw new Exception("请填写正确的是否唯一 1[是] 0[否]");
                            }
                            $model->is_sole = $val;
                        }
                        if($key == "状态 1[有效] 0[无效]") {
                            if(!in_array($val, [0,1])) {
                                throw new Exception("请填写正确的状态 1[有效] 0[无效]");
                            }
                            $model->status = $val;
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

    /**
     * 下载角色列表
     * @param type $query 查询对象
     * @return type
     */
    public function downloadIndex($query) {
        $all = $query->all();
        $datas = [];    
        foreach ($all as $key => $val) {
            $datas[] = [
                'key' => $key+1,
                'name' => $val->name,
                'departmentId' => $val->showDeparmentName(),
                'is_sole' => $val->is_sole ? "是" : "否",
                'status' => $val->showStatus(),
            ];
        }
        $columns = [
            [ 'attribute' => 'key','header' => '序号'],
            [ 'attribute' => 'name','header' => '角色'],
            [ 'attribute' => 'departmentId','header' => '所属部门'],
            [ 'attribute' => 'is_sole','header' => '是否唯一 1[是] 0[否]'],
            [ 'attribute' => 'status','header' => '状态 1[有效] 0[无效]'],
        ];
        return Utils::downloadExcel($datas, $columns, "角色表");
    }
}
