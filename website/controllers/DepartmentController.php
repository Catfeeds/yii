<?php
namespace app_web\controllers;

use common\models\Department;
use common\models\DepartmentBalance;
use common\models\DepartmentBalanceLog;
use common\models\Role;
use common\models\Warehouse;
use common\models\AdminLog;
use common\models\BusinessAll;
use libs\common\Flow;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\Json;
use app_web\components\CController;
use yii\web\UploadedFile;
use libs\Utils;
use moonland\phpexcel\Excel;
use Exception;

/**
 * 业务基础数据管理
 * 部门管理
 */
class DepartmentController extends CController
{
    /**
     * 部门列表页
     */
    public function actionIndex()
    {
        $level = Yii::$app->request->get('level');
        $status = Yii::$app->request->get('status');
        $keyword = Yii::$app->request->get('keyword');
        $model = new Department();
        $query = Department::find();
        if($level){
            $query->andWhere(['level' => $level]);
        }
        if(is_numeric($status)){
            $query->andWhere(['status' => $status]);
        }
        if($keyword || is_numeric($keyword)){
            $query->andWhere(['like', 'name', $keyword]);
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
     * 数据导出
     * @param type $query 导出记录条件
     */
    public function downloadIndex($query) {
        $all = $query->all();
        $datas = [];    
        foreach ($all as $key => $val) {
            $datas[] = [
                'id' => $key+1,
                'name' => $val->showName(),
                'number' => $val->showNumber(),
                'acronym' => $val->acronym,
                'parentName' => $val->showParentName(),
                'balance' => DepartmentBalance::getBalanceByDepartmentId($val->id),
                'status' => $val->showStatus(),
            ];
        }
        $columns = [
            [ 'attribute' => 'id','header' => '序号'],
            [ 'attribute' => 'name','header' => '部门名称'],
            [ 'attribute' => 'number','header' => '部门编号'],
            [ 'attribute' => 'acronym','header' => '部门缩写'],
            [ 'attribute' => 'parentName','header' => '上级部门'],
            [ 'attribute' => 'balance','header' => '部门余额'],
            [ 'attribute' => 'status','header' => '状态'],
        ];
        return Utils::downloadExcel($datas, $columns, "部门表");
    }
    
    /**
     * 下载导入模板
     */
    public function actionDowntemplate() {
        $datas = [
            [
                "name" => "",
                "number" => "",
                "acronym" => "",
                "parentName" => "",
                "status" => "",
            ]
        ];
        $columns = [
            [ 'attribute' => 'name','header' => '部门名称[必填]'],
            [ 'attribute' => 'number','header' => '部门编号[必填]'],
            [ 'attribute' => 'acronym','header' => '部门缩写[必填]'],
            [ 'attribute' => 'parentName','header' => '上级部门'],
            [ 'attribute' => 'status','header' => '状态 1[有效] 0[无效]'],
        ];
        return Utils::downloadExcel($datas, $columns, "部门模板");
    }
    
    /**
     * 导入部门记录
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
                Utils::delFile($file);
                foreach($datas as $data){
                    $model = new Department();
                    $model->create_time = date("Y-m-d H:i:s");
                    foreach ($data as $key => $val) {
                        if($key == "部门名称[必填]") {
                            $model->name = "$val";
                        }
                        if($key == "部门编号[必填]") {
                            $model->number = "$val";
                        }
                        if($key == "部门缩写[必填]") {
                            $model->acronym = "$val";
                        }
                        if($key == "上级部门") {
                            $item = Department::findOne(["name" => $val]);
                            $model->parent_id = $item ? $item->id : "";
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

    /**
     * 加载新增或修改部门页面
     * @param type $id ID
     * @return type
     */
    public function actionForm($id = 0)
    {
        if($id){
            $model = Department::findOne($id);
            $balanceModel = DepartmentBalance::findOne(["department_id" => $id]);
            $balance = $balanceModel ? $balanceModel->balance : 0;
        }else{
            $model = new Department;
            $balance = 0;
        }
        return $this->renderPartial('_form', compact(['model', 'balance']));
    }

    /**
     * 新增部门
     * @return type
     * @throws Exception
     */
    public function actionCreate()
    {
        $model = new Department();
        if($model->load(Yii::$app->request->post())){
            $transaction = \Yii::$app->db->beginTransaction();
            try{
                if(!$model->save()){
                    $errors = $model->getFirstErrors();
                    throw new Exception(reset($errors));
                }
                $transaction->commit();
                return Json::encode(["html" => 1, "message" => "新建成功"]);
            }  catch (Exception $ex) {
                $transaction->rollBack();
                return Json::encode(["message" => $ex->getMessage()]);
            }
        }
        $errors = $model->getFirstErrors();
        $return['message'] = join("\n", $errors);
        return Json::encode($return);
    }

    /**
     * 修改部门信息
     * @param type $id 部门ID
     * @return type
     * @throws Exception
     */
    public function actionUpdate($id)
    {
        $model = Department::findOne($id);
        if($model->load(Yii::$app->request->post())){
            $transaction = \Yii::$app->db->beginTransaction();
            try{
                if(!$model->save()){
                    $errors = $model->getFirstErrors();
                    throw new Exception(reset($errors));
                }
                $transaction->commit();
                return Json::encode(["html" => 1, "message" => "修改成功"]);
            } catch (Exception $ex) {
                $transaction->rollBack();
                return Json::encode(["message" => $ex->getMessage()]);
            }
        }
        $errors = $model->getFirstErrors();
        $return['message'] = join("\n", $errors);
        return Json::encode($return);
    }

    /**
     * 删除部门
     * @param type $id ID
     * @return type
     */
    public function actionDelete($id)
    {
        $model = Department::findOne($id);
        if(!$model || $model->status == 1) {
            $return['error'] = 1;
            $return['message'] = '状态异常，请刷新再试！';
            return Json::encode($return);
        }
        $role = Role::findOne(["department_id" => $id]);
        if($role) {
            $return['error'] = 1;
            $return['message'] = '该部门还存在角色，无法删除！';
            return Json::encode($return);
        }
        $warehouse = Warehouse::findOne(["department_id" => $id]);
        if($warehouse) {
            $return['error'] = 1;
            $return['message'] = '该部门还存在仓库，无法删除！';
            return Json::encode($return);
        }
        if(!$model->delete()){
            $return['error'] = 1;
            $message = $model->getFirstErrors();
            $return['message'] = reset($message);
            return Json::encode($return);
        }
        $code = 'delete_department';
        $content = '删除部门' . $model->id;
        AdminLog::addLog($code, $content);
        $return['error'] = 0;
        $return['message'] = '删除成功！';
        return Json::encode($return);
    }
    
    /**
     * 设置无效
     * @param type $id ID
     * @return type
     */
    public function actionInvalid($id)
    {
        $model = Department::findOne($id);
        if(!$model) {
            $return['error'] = 1;
            $return['message'] = '状态异常，请刷新再试！';
            return Json::encode($return);
        }
        $role = Role::findOne(["department_id" => $id]);
        if($role) {
            $return['error'] = 1;
            $return['message'] = '该部门还存在角色，无法设置无效！';
            return Json::encode($return);
        }
        $warehouse = Warehouse::findOne(["department_id" => $id]);
        if($warehouse) {
            $return['error'] = 1;
            $return['message'] = '该部门还存在仓库，无法设置无效！';
            return Json::encode($return);
        }
        $business = BusinessAll::find()->andWhere(["warehouse_id" => $id, "status" => [Flow::STATUS_APPLY_VERIFY, Flow::STATUS_APPLY_APPROVAL, Flow::STATUS_APPLY_FINISH]])->one();
        if($business) {
            $return['error'] = 1;
            $return['message'] = '该部门还有未完成的流程，无法设置无效！';
            return Json::encode($return);
        }
        $model->status = 0;
        if($model->save()){
            $return['error'] = 0;
        }else{
            $return['error'] = 1;
            $message = $model->getFirstErrors();
            $return['message'] = reset($message);
        }
        return Json::encode($return);
    }
    
    /**
     * 异步获取部门余额
     * @param type $id 部门ID
     * @return type
     */
    public function actionAjaxdepartmentbalance($id)
    {
        $item = DepartmentBalance::findOne(["department_id" => $id]);
        return Json::encode($item ? $item->balance : 0);
    }
}
