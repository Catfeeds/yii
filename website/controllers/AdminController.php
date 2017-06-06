<?php

namespace app_web\controllers;

use common\models\Admin;
use common\models\AdminLog;
use common\models\Department;
use common\models\Role;
use common\models\BusinessAll;
use libs\common\Flow;
use libs\Utils;
use Yii;
use Exception;
use yii\data\ActiveDataProvider;
use yii\helpers\Json;
use app_web\components\CController;
use yii\web\UploadedFile;
use moonland\phpexcel\Excel;
use yii\helpers\Url;
use yii\web\Controller;

/**
 * 业务基础数据管理
 * 员工管理
 * dengxu  2016.5.16
 */
class AdminController extends Controller {
    
    public function actionIndex() {
        $status = Yii::$app->request->get('status');
        $keyword = Yii::$app->request->get('keyword');
        $department_id = Yii::$app->request->get('department_id');
        $role_id = Yii::$app->request->get('role_id');
        $model = new Admin();
        $query = Admin::find();
//        if(!Admin::checkSupperFlowAdmin()){
//      	  $query->andWhere(["department_id" => Admin::getDepId()]);
//        }
        if(is_numeric($department_id)) {
            $query->andWhere(["department_id" => $department_id]);
        }
        if(is_numeric($role_id)) {
            $query->andWhere(["role_id" => $role_id]);
        }
        if (is_numeric($status)) {
            $query->andWhere(['status' => $status]);
        }
        if ($keyword) {
            $query->andWhere(['like', 'username', $keyword]);
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
     * 加载创建或修改页面
     * @param type $id 用户ID
     * @return type
     */
    public function actionForm($id = 0) {
        if ($id) {
            $model = Admin::findOne($id);
        } else {
            $model = new Admin;
        }
        return $this->render('_form', compact(['model']));
    }

    /**
     * 创建新用户
     */
    public function actionCreate() {
        $model = new Admin();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if($model->entry_date && $model->leave_date && strtotime($model->entry_date) > strtotime($model->leave_date)) {
                $return['message'] = "入职时间不能大于离职时间";
                return Json::encode($return);
            } 
            if(!$model->checkOnly()) {
                $return['message'] = "该权限所属用户已存在";
                return Json::encode($return);
            }
            $model->save();
            AdminLog::addLog("新增管理员" . $model->id); 
            $return["message"] = "新增成功";
            $return["type"] = "url";
            $return["url"] = Url::to(["admin/index"]);
            return Json::encode($return);
        } else {
            $errors = $model->getFirstErrors();
            $return['message'] = join("\n", $errors);
        }
        return Json::encode($return);
    }

    /**
     * 修改用户信息
     * @param type $id 用户ID
     * @return type
     */
    public function actionUpdate($id) {
        $model = Admin::findOne($id);
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if($model->entry_date && $model->leave_date && strtotime($model->entry_date) > strtotime($model->leave_date)) {
                $return['message'] = "入职时间不能大于离职时间";
                return Json::encode($return);
            } 
            if(!$model->checkOnly()) {
                $return['message'] = "该权限所属用户已存在";
                return Json::encode($return);
            }
            $model->save();
            AdminLog::addLog("新增管理员" . $model->id); 
            $return["message"] = "新增成功";
            $return["type"] = "url";
            $return["url"] = Url::to(["admin/index"]);
            return Json::encode($return);
        } else {
            $errors = $model->getFirstErrors();
            $return['message'] = join("\n", $errors);
        }
        return Json::encode($return);
    }
    
    /**
     * 设置无效
     * @param type $id 用户ID
     */
    public function actionInvalid($id)
    {
        $model = Admin::findOne($id);
        if(!$model) {
            $return['error'] = 1;
            $return['message'] = '状态异常，请刷新再试！';
            return Json::encode($return);
        }
        $query = BusinessAll::find();
        $query->orWhere(["create_admin_id" => $id]);
        $query->orWhere(["verify_admin_id" => $id]);
        $query->orWhere(["approval_admin_id" => $id]);
        $query->orWhere(["operation_admin_id" => $id]);
        $query->andWhere(["status" => [Flow::STATUS_APPLY_VERIFY, Flow::STATUS_APPLY_APPROVAL, Flow::STATUS_APPLY_FINISH]]);
        $business = $query->one();
        if($business) {
            $return['error'] = 1;
            $return['message'] = '失效的数据在执行流程中，无法设置无效！';
            return Json::encode($return);
        }
        $model->status = 0;
        if($model->save()){
            $return['error'] = 0;
            $return['message'] =  '操作成功！';
        }else{
            $return['error'] = 1;
            $return['message'] = '保存不成功！';
        }
        return Json::encode($return);
    }
    
    /**
     * 设置有效
     * @param type $id 用户ID
     */
    public function actionValid($id)
    {
        $model = Admin::findOne($id);
        if(!$model) {
            $return['error'] = 1;
            $return['message'] = '状态异常，请刷新再试！';
            return Json::encode($return);
        }
        $model->status = 1;
        if($model->save()){
            $return['error'] = 0;
            $return['message'] =  '操作成功！';
        }else{
            $return['error'] = 1;
            $return['message'] = '保存不成功！';
        }
        return Json::encode($return);
    }
    
    /**
     * 删除
     * @param type $id 用户ID
     */
    public function actionDelete($id) {
        $model = Admin::findOne($id);
        if(!$model || $model->status == 1) {
            $return['error'] = 1;
            $return['message'] = '状态异常，请刷新再试！';
            return Json::encode($return);
        }
        $query = BusinessAll::find();
        $query->orWhere(["status" => Flow::STATUS_APPLY_VERIFY, "verify_admin_id" => $id]);
        $query->orWhere(["status" => Flow::STATUS_APPLY_APPROVAL, "approval_admin_id" => $id]);
        $query->orWhere(["status" => Flow::STATUS_APPLY_FINISH, "operation_admin_id" => $id]);
        $business = $query->one();
        if($business) {
            $return['error'] = 1;
            $return['message'] = '该员工还有未完成的流程，无法删除！';
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
     * 重置密码
     * @param type $id 用户ID
     */
    public function actionResetpwd($id) {
        $model = Admin::findOne($id);
        $model->setPassword("123456");
        $model->is_first = 1;
        if(!$model->validate()) {
            $message = $model->getFirstErrors();
            $return['error'] = 1;
            $return['message'] = reset($message);
            return Json::encode($return);
        }
        $model->save();
        AdminLog::addLog("重置用户密码：" . $model->id); 
        $return['error'] = 0;
        $return['message'] = "重置成功";
        $return["type"] = "url";
        $return["url"] = Yii::$app->request->getReferrer();
        return Json::encode($return);
    }
    
    /**
     * 修改密码
     */
    public function actionEditpwd() {
        $model = Admin::findOne(\Yii::$app->user->getId());
        if(\Yii::$app->request->post()) {
            if(!$_POST["oldpwd"] || !$model->validatePassword($_POST["oldpwd"])) {
                $return['error'] = 1;
                $return['message'] = '旧密码输入错误！';
                return Json::encode($return);
            }
            if(!$_POST["newpwd"] || !$_POST["yzpwd"] || $_POST["newpwd"] != $_POST["yzpwd"]) {
                $return['error'] = 1;
                $return['message'] = '新密码与验证密码不相等！';
                return Json::encode($return);
            }
            if(strlen($_POST["newpwd"]) < 6 || strlen($_POST["newpwd"]) > 20) {
                $return['error'] = 1;
                $return['message'] = '新密码的长度必须大于6位小于20位！';
                return Json::encode($return);
            }
            if($model->validatePassword($_POST["newpwd"])) {
                $return['error'] = 1;
                $return['message'] = '新密码的不能等于旧密码！';
                return Json::encode($return);
            }
            $model->setPassword($_POST["newpwd"]);
            $model->is_first = 2;
            if(!$model->validate()) {
                $message = $model->getFirstErrors();
                $return['error'] = 1;
                $return['message'] = reset($message);
                return Json::encode($return);
            }
            $model->save();
            AdminLog::addLog("修改用户密码：" . $model->id); 
            $return['message'] = "修改成功";
            $return["type"] = "url";
            $return["url"] = Url::to(["site/logout"]);
            return Json::encode($return);
        }
        return $this->render("editpwd");
    }
    
    /**
     * 下载导入模板
     */
    public function actionDowntemplate() {
        $datas = [
            [
                "username" => "",
                "job_number" => "",
                "id_card" => "",
                "deparmentName" => "",
                "roleName" => "",
                "entry_date" => "",
                "leave_date" => "",
                "status" => "",
            ]
        ];
        $columns = [
            [ 'attribute' => 'name','header' => '姓名[必填]'],
            [ 'attribute' => 'job_number','header' => '工号'],
            [ 'attribute' => 'id_card','header' => '证件号'],
            [ 'attribute' => 'departmentId','header' => '所属部门[必填]'],
            [ 'attribute' => 'roleName','header' => '角色[必填]'],
            [ 'attribute' => 'entry_date','header' => '入职时间[2016-10-10]'],
            [ 'attribute' => 'leave_date','header' => '离职时间[2016-10-10]'],
            [ 'attribute' => 'status','header' => '状态 1[有效] 0[无效]'],
        ];
        return Utils::downloadExcel($datas, $columns, "导入员工模板");
    }

    /**
     * 导入
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
                    $model = new Admin();
                    $model->create_time = date("Y-m-d H:i:s");
                    foreach ($data as $key => $val) {
                        if($key == "姓名[必填]") {
                            $model->username = "$val";
                        }
                        if($key == "工号") {
                            $model->job_number = "$val";
                        }
                        if($key == "证件号") {
                            $model->id_card = "$val";
                        }
                        if($key == "所属部门[必填]") {
                            $item = Department::findOne(["name" => $val]);
                            if(!$item) {
                                throw new Exception("请填写正确的部门名");
                            }
                            $model->department_id = $item->id;
                        }
                        if($key == "角色[必填]") {
                            $item = Role::findOne(["name" => $val]);
                            if(!$item) {
                                throw new Exception("请填写正确的角色");
                            }
                            $model->role_id = $item->id;
                        }
                        if($key == "入职时间[2016-10-10]") {
                            $entry_date = strtotime($val);
                            if(!$entry_date){
                                $date = explode('-',$val);
                                if(count($date) == 3) {
                                    $entry_date = "20".$date[2]."-".$date[0]."-".$date[1];
                                } 
                            } else {
                                $entry_date = date("Y-m-d", $entry_date);
                            }
                            if(!$entry_date) {
                                throw new Exception($model->username."的入职时间不能为空");
                            }
                            if(strtotime($entry_date) > time()) {
                                throw new Exception($model->username."的入职时间不能大于当前时间");
                            }
                            $model->entry_date = "$entry_date";
                        }
                        if($key == "离职时间[2016-10-10]") {
                            $leave_date = strtotime($val);
                            if(!$leave_date){
                                $date = explode('-',$val);
                                if(count($date) == 3) {
                                    $leave_date = "20".$date[2]."-".$date[0]."-".$date[1];
                                } 
                            } else {
                                $leave_date = date("Y-m-d", $leave_date);
                            }
                            if($leave_date && strtotime($leave_date) < time()) {
                                throw new Exception($model->username."的离职时间不能小于当前时间");
                            }
                            $model->leave_date = $leave_date ? date("Y-m-d", strtotime($leave_date)) : "";
                        }
                        if($key == "状态 1[有效] 0[无效]") {
                            $status = 0;
                            if(in_array($val, [0,1])) {
                                $status = $val;
                            }
                            $model->status = $status;
                        }
                    }
                    if(!$model->checkOnly()) {
                        throw new Exception($model->username."所属的权限用户已存在");
                    }
                    if($model->entry_date && $model->leave_date && strtotime($model->entry_date) > strtotime($model->leave_date)) {
                        throw new Exception($model->username."的入职时间不能大于离职时间");
                    }
                    if(!$model->save()){
                        $errors = $model->getFirstErrors();
                        throw new Exception(reset($errors));
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
     * 导出列表
     * @param type $query 查询对象
     * @return type
     */
    public function downloadIndex($query) {
        $all = $query->all();
        $datas = [];    
        foreach ($all as $key => $val) {
            $datas[] = [
                'key' => $key+1,
                "username" => $val->username,
                "job_number" => $val->job_number,
                "id_card" => $val->id_card,
                "deparmentName" => $val->showDeparmentName(),
                "roleName" => $val->showRoleName(),
                "entry_date" => $val->entry_date,
                "leave_date" => $val->leave_date,
                "status" => $val->showStatus(),
            ];
        }
        $columns = [
            [ 'attribute' => 'key','header' => '序号'],
            [ 'attribute' => 'username','header' => '姓名'],
            [ 'attribute' => 'job_number','header' => '工号'],
            [ 'attribute' => 'id_card','header' => '证件号'],
            [ 'attribute' => 'deparmentName','header' => '所属部门'],
            [ 'attribute' => 'roleName','header' => '角色'],
            [ 'attribute' => 'entry_date','header' => '入职时间'],
            [ 'attribute' => 'leave_date','header' => '离职时间'],
            [ 'attribute' => 'status','header' => '状态'],
        ];
        return Utils::downloadExcel($datas, $columns, "员工表");
    }

}
