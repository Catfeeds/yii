<?php
namespace app_web\controllers;

use libs\Utils;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\Json;
use app_web\components\CController;
use yii\web\UploadedFile;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use moonland\phpexcel\Excel;
use Exception;
use PHPExcel_Shared_Date;

use common\models\FlowConfig;
use common\models\FlowCondition;
use common\models\Admin;
use common\models\AdminLog;
use common\models\Department;
use common\models\Role;
use common\models\ProductCategory;
use common\models\Supplier;
use libs\common\Flow;
use common\models\BusinessAll;

/**
 * 业务设置 -- 业务流程列表
 */
class FlowconfigController extends CController
{
    /**
     * 业务流程列表页
     */
    public function actionIndex()
    {
        $keyword = Yii::$app->request->get('keyword');
        $status = Yii::$app->request->get('status');
        $type = Yii::$app->request->get('type');
        $model = new FlowConfig();
        $query = FlowConfig::find();
        if(is_numeric($status)){
            $query->andWhere(['status' => $status]);
        }
        if(is_numeric($type)){
            $query->andWhere(['type' => $type]);
        }
        if($keyword){
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
     * 设置无效
     * @param type $id 业务流程ID
     * @return type
     */
    public function actionInvalid($id) {
        $status = \Yii::$app->request->get("status");
        $model = FlowConfig::findOne($id);
//        if(!($model->create_admin_id == Yii::$app->user->getId() || Admin::checkSupperAdmin() || Admin::checkBusinAdmin())) {
//            $return['error'] = 1;
//            $return['message'] = '没有权限设置！';
//            return Json::encode($return);
//        }
        $model->status = $status;
        if($model->save()){
            $return['error'] = 0;
            $return['message'] = '设置成功！';
        }else{
            $return['error'] = 1;
            $return['message'] = '设置失败！';
        }
        return Json::encode($return);
    }

    /**
     * 删除业务流程
     * @param type $id 业务流程ID
     * @return type
     */
    public function actionDelete($id)
    {
        $model = FlowConfig::findOne($id);
        if(!$model || $model->status == 1) {
            $return['error'] = 1;
            $return['message'] = '状态异常，请刷新再试！';
            return Json::encode($return);
        }
        $business = BusinessAll::findOne(["config_id" => $id, "status" => [Flow::STATUS_APPLY_VERIFY, Flow::STATUS_APPLY_APPROVAL, Flow::STATUS_APPLY_FINISH]]);
        if($business) {
            $return['error'] = 1;
            $return['message'] = '该业务流程还有未完成的流程，无法删除！';
            return Json::encode($return);
        }
        if(!$model->delete()){
            $return['error'] = 1;
            $message = $model->getFirstErrors();
            $return['message'] = reset($message);
            return Json::encode($return);
        }
        $code = 'delete_config';
        $content = '删除流程' . $model->id;
        AdminLog::addLog($code, $content);
        FlowCondition::deleteAll(["config_id" => $id]);
        $return['error'] = 0;
        $return['message'] = '删除成功！';
        return Json::encode($return);
    }
    
    /**
     * 添加或修改业务流程记录
     */
    public function actionAddedit() {
        $id = Yii::$app->request->get('id');
        $model = new FlowConfig;
        $model->name = "采购计划";
        $model->type = Flow::TYPE_PLANNING;
        $model->status = FlowConfig::STATUS_YES;
        $info = array();
        if($id) {
            $item = FlowConfig::findOne($id);
            $model->id = $id;
            $model->attributes = $item->attributes;
//            $arr = [$item->create_department_id, $item->verify_department_id, $item->approval_department_id, $item->operation_department_id];
//            $arr = array_filter($arr);
//            $arr = array_flip(array_flip($arr));
//            if(!((count($arr) == 1 && reset($arr) == Yii::$app->user->getIdentity()->department_id) || Admin::checkSupperFlowAdmin() || Admin::checkBusinAdmin())){
//                $this->redirect("index.php?r=site/nojure");
//                Yii::$app->end();
//            }
            $info = FlowCondition::findAll(["config_id" => $id]);
            $info = ArrayHelper::index($info, "type");
        } else {
            $model->create_name = "创建";
            $model->verify_name = "审核";
            $model->approval_name = "批准";
            $model->operation_name = "执行";
        }
        if(Yii::$app->request->post()) {
            $result = $model->addEdit($id, Yii::$app->request->post());
            if($result["state"]) {
                $return["message"] = $id ? "修改成功" : "新增成功";
                $return["type"] = "url";
                $return["url"] = Url::to(["flowconfig/index"]);
                return Json::encode($return);
            }
            $return['message'] = is_array($result["message"]) ? reset($result["message"]) : $result["message"];
            return Json::encode($return);
        }
        echo $this->render("addedit", compact("model", "info"));
    }
    
    /**
     * 业务流程详情
     * @param type $id 业务流程ID
     */
    public function actionInfo($id) {
        $item = FlowConfig::findOne($id);
        $info = FlowCondition::findAll(["config_id" => $id]);
        $info = ArrayHelper::index($info, "type");
        $condition = Flow::getTypeCheckCondition($item->type);
        echo $this->render("info", compact("item", "info", "condition"));
    }
    
    
    /**
     * 数据导出
     * @param type $query 查询对象
     */
    public function downloadIndex($query) {
        $all = $query->all();
        $datas = [];    
        foreach ($all as $key => $val) {
            $info = FlowCondition::findAll(["config_id" => $val->id]);
            $info = ArrayHelper::index($info, "type");
            $datas[] = [
                'id' => $key+1,
                'name' => $val->name,
                'type' => Flow::showType($val->type),
                'create_role_id' => $val->create_role_id ? Role::getNameByRoleId($val->create_role_id) : "无",
                'create_name' => $val->create_name ? $val->create_name : "无",
                'create_department_id' => $val->create_department_id ? Department::getNameById($val->create_department_id) : "无",
                'verify_role_id' => $val->verify_role_id ? Role::getNameByRoleId($val->verify_role_id) : "无",
                'verify_name' => $val->verify_name ? $val->verify_name : "无",
                'verify_department_id' => $val->verify_department_id ? Department::getNameById($val->verify_department_id) : "无",
                'approval_role_id' => $val->approval_role_id ? Role::getNameByRoleId($val->approval_role_id) : "无",
                'approval_name' => $val->approval_name ? $val->approval_name : "无",
                'approval_department_id' => $val->approval_department_id ? Department::getNameById($val->approval_department_id) : "无",
                'operation_role_id' => $val->operation_role_id ? Role::getNameByRoleId($val->operation_role_id) : "无",
                'operation_name' => $val->operation_name ? $val->operation_name : "无",
                'operation_department_id' => $val->operation_department_id ? Department::getNameById($val->operation_department_id) : "无",
                'status' => $val->showStatus(),
                'lower_limit_money' => isset($info[FlowCondition::TYPE_PRICE]) ? $info[FlowCondition::TYPE_PRICE]["lower_limit"] : "",
                'upper_limit_money' => isset($info[FlowCondition::TYPE_PRICE]) ? $info[FlowCondition::TYPE_PRICE]["upper_limit"] : "",
                'lower_limit_date' => isset($info[FlowCondition::TYPE_TIME]) ? $info[FlowCondition::TYPE_TIME]["lower_limit"] : "",
                'upper_limit_date' => isset($info[FlowCondition::TYPE_TIME]) ? $info[FlowCondition::TYPE_TIME]["upper_limit"] : "",
                'departemt' => isset($info[FlowCondition::TYPE_AREA]) && $info[FlowCondition::TYPE_AREA]["lower_limit"] ? Department::getNameById($info[FlowCondition::TYPE_AREA]["lower_limit"]) : "全部",
                'supplier' => isset($info[FlowCondition::TYPE_SUPPLIER]) && $info[FlowCondition::TYPE_SUPPLIER]["lower_limit"] ? Supplier::getNameById($info[FlowCondition::TYPE_SUPPLIER]["lower_limit"]) : "全部",
                'productCate' => isset($info[FlowCondition::TYPE_CATEGORY]) && $info[FlowCondition::TYPE_CATEGORY]["lower_limit"] ? ProductCategory::getNameById($info[FlowCondition::TYPE_CATEGORY]["lower_limit"]) : "全部",
            ];
        }
        $columns = [
            [ 'attribute' => 'id','header' => '序号'],
            [ 'attribute' => 'name','header' => '流程名称'],
            [ 'attribute' => 'type','header' => '流程类型'],
            [ 'attribute' => 'create_role_id','header' => '创建角色'],
            [ 'attribute' => 'create_name','header' => '创建名称'],
            [ 'attribute' => 'create_department_id','header' => '创建部门'],
            [ 'attribute' => 'verify_role_id','header' => '审核角色'],
            [ 'attribute' => 'verify_name','header' => '审核名称'],
            [ 'attribute' => 'verify_department_id','header' => '审核部门'],
            [ 'attribute' => 'approval_role_id','header' => '批准角色'],
            [ 'attribute' => 'approval_name','header' => '批准名称'],
            [ 'attribute' => 'approval_department_id','header' => '批准部门'],
            [ 'attribute' => 'operation_role_id','header' => '执行角色'],
            [ 'attribute' => 'operation_name','header' => '执行名称'],
            [ 'attribute' => 'operation_department_id','header' => '执行部门'],
            [ 'attribute' => 'status','header' => '状态'],
            [ 'attribute' => 'lower_limit_money','header' => '下限金额'],
            [ 'attribute' => 'upper_limit_money','header' => '上限金额'],
            [ 'attribute' => 'lower_limit_date','header' => '下限时间'],
            [ 'attribute' => 'upper_limit_date','header' => '上限时间'],
            [ 'attribute' => 'departemt','header' => '部门'],
            [ 'attribute' => 'supplier','header' => '供应商'],
            [ 'attribute' => 'productCate','header' => '商品分类'],
        ];
        return Utils::downloadExcel($datas, $columns, "业务流程列表");
    }
    
    /**
     * 导出流程类型列表
     */
    public function actionDownflowtype() {
        $datas = [];
        foreach (Flow::getTypeSelectData() as $type => $name) {
            $datas[] = [
                    "id" => $type, 
                    "name" => $name,
            ];
        }
        $columns = [
            [ 'attribute' => 'id','header' => '流程类型ID'],
            [ 'attribute' => 'name','header' => '流程类型名称'],
        ];
        return Utils::downloadExcel($datas, $columns, "流程类型列表");
    }
    
    /**
     * 下载导入模板
     */
    public function actionDowntemplate() {
        $datas[] = [
            'name' => "",
            'type' => "",
            'create_role_id' => "",
            'create_name' => "",
            'create_department_id' => "",
            'verify_role_id' => "",
            'verify_name' => "",
            'verify_department_id' => "",
            'approval_role_id' => "",
            'approval_name' => "",
            'approval_department_id' => "",
            'operation_role_id' => "",
            'operation_name' => "",
            'operation_department_id' => "",
            'status' => "",
            'lower_limit_money' => "",
            'upper_limit_money' => "",
            'lower_limit_date' => "",
            'upper_limit_date' => "",
            'departemt' => "",
            'supplier' => "",
            'productCate' => "",
        ];
        $columns = [
            [ 'attribute' => 'name','header' => '流程名称[必填]'],
            [ 'attribute' => 'type','header' => '流程类型ID[必填]'],
            [ 'attribute' => 'create_role_id','header' => '创建角色'],
            [ 'attribute' => 'create_name','header' => '创建名称'],
            [ 'attribute' => 'create_department_id','header' => '创建部门'],
            [ 'attribute' => 'verify_role_id','header' => '审核角色'],
            [ 'attribute' => 'verify_name','header' => '审核名称'],
            [ 'attribute' => 'verify_department_id','header' => '审核部门'],
            [ 'attribute' => 'approval_role_id','header' => '批准角色'],
            [ 'attribute' => 'approval_name','header' => '批准名称'],
            [ 'attribute' => 'approval_department_id','header' => '批准部门'],
            [ 'attribute' => 'operation_role_id','header' => '执行角色'],
            [ 'attribute' => 'operation_name','header' => '执行名称'],
            [ 'attribute' => 'operation_department_id','header' => '执行部门'],
            [ 'attribute' => 'status','header' => '状态[1：有效 0：无效]'],
            [ 'attribute' => 'lower_limit_money','header' => '下限金额[必填]'],
            [ 'attribute' => 'upper_limit_money','header' => '上限金额[必填]'],
            [ 'attribute' => 'lower_limit_date','header' => '下限时间[必填2016-10-01]'],
            [ 'attribute' => 'upper_limit_date','header' => '上限时间[必填2016-10-01]'],
            [ 'attribute' => 'departemt','header' => '验证部门[0或空为全部]'],
            [ 'attribute' => 'supplier','header' => '验证供应商[0或空为全部]'],
            [ 'attribute' => 'productCate','header' => '验证商品分类[0或空为全部]'],
        ];
        return Utils::downloadExcel($datas, $columns, "业务流程导入模板");
    }
    
    /**
     * 导入业务流程记录
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
                $flowTypeAll = Flow::getTypeSelectData();
                foreach($datas as $data){
                    $data = array_filter($data);
                    if(!$data) {
                        continue;
                    }
                    Utils::delFile($file);
                    $model = new FlowConfig();
                    $model->status = FlowConfig::STATUS_NO;
                    $model->create_admin_id = \Yii::$app->user->getId();
                    $priceCondtion = $timeCondtion = $departmentCondition = $supplierCondition = $cateCondition = false;
                    foreach ($data as $key => $val) {
                        if($key == "流程名称[必填]") {
                            $model->name ="$val";
                        }
                        if($key == "流程类型ID[必填]") {
                            if(!isset($flowTypeAll[$val])) {
                                throw new Exception("请下载流程类型填写流程类型ID".$val);
                            }
                            $model->type = $val;
                        }
                        if($key == "创建角色") {
                            $item = Role::findOne(['name' => $val]);
                            if(!$item) {
                                throw new Exception("请填写正确的创建角色名称");
                            }
                            $model->create_role_id = $item->id;
                        }
                        if($key == "创建名称") {
                            $model->create_name = "$val";
                        }
                        if($key == "创建部门") {
                            $item = Department::findOne(['name' => $val]);
                            if(!$item) {
                                throw new Exception("请填写正确的创建部门名称");
                            }
                            $model->create_department_id = $item->id;
                        }
                        if($key == "审核角色") {
                            if($val) {
                                $item = Role::findOne(['name' => $val]);
                                if(!$item) {
                                    throw new Exception("请填写正确的审核角色名称");
                                }
                                if(!$item->is_sole) {
                                    throw new Exception("审核角色必须是唯一的");
                                }
                                $model->verify_role_id = $item->id;
                            }
                        }
                        if($key == "审核名称") {
                            $model->verify_name = "$val";
                        }
                        if($key == "审核部门") {
                            if($val) {
                                $item = Department::findOne(['name' => $val]);
                                if(!$item) {
                                    throw new Exception("请填写正确的审核部门名称");
                                }
                                $model->verify_department_id = $item->id;
                            }
                        }
                        if($key == "批准角色") {
                            if($val) {
                                $item = Role::findOne(['name' => $val]);
                                if(!$item) {
                                    throw new Exception("请填写正确的批准角色名称");
                                }
                                if(!$item->is_sole) {
                                    throw new Exception("批准角色必须是唯一的");
                                }
                                $model->approval_role_id = $item->id;
                            }
                        }
                        if($key == "批准名称") {
                            $model->approval_name = "$val";
                        }
                        if($key == "批准部门") {
                            if($val) {
                                $item = Department::findOne(['name' => $val]);
                                if(!$item) {
                                    throw new Exception("请填写正确的批准部门名称");
                                }
                                $model->approval_department_id = $item->id;
                            }
                        }
                        if($key == "执行角色") {
                            if($val) {
                                $item = Role::findOne(['name' => $val]);
                                if(!$item) {
                                    throw new Exception("请填写正确的执行角色名称");
                                }
                                if(!$item->is_sole) {
                                    throw new Exception("执行角色必须是唯一的");
                                }
                                $model->operation_role_id = $item->id;
                            }
                        }
                        if($key == "执行名称") {
                            $model->operation_name = "$val";
                        }
                        if($key == "执行部门") {
                            if($val) {
                                $item = Department::findOne(['name' => $val]);
                                if(!$item) {
                                    throw new Exception("请填写正确的执行部门名称");
                                }
                                $model->operation_department_id = $item->id;
                            }
                        }
                        if($key == "状态[1：有效 0：无效]") {
                            $model->status = in_array($val, [0,1]) ? $val : 0;
                        }
                        if($key == "下限金额[必填]") {
                            if(!is_numeric($val) || $val <= 0) {
                                throw new Exception("请填写正确的下限金额，必须为大于零的数字");
                            }
                            $priceCondtion = new FlowCondition;
                            $priceCondtion->name = "流程条件";
                            $priceCondtion->type = FlowCondition::TYPE_PRICE;
                            $priceCondtion->status = 1;
                            $priceCondtion->lower_limit = "$val";
                        }
                        if($key == "上限金额[必填]") {
                            if(!is_numeric($val) || $val <= 0) {
                                throw new Exception("请填写正确的上限金额，必须为大于零的数字");
                            }
                            $priceCondtion->upper_limit = "$val";
                        }
                        if($key == "下限时间[必填2016-10-01]") {
                            $leave_date = strtotime($val);
                            if(!$leave_date){
                                $date = explode('-',$val);
                                if(count($date) == 3) {
                                    $leave_date = "20".$date[2]."-".$date[0]."-".$date[1];
                                } 
                            } else {
                                $leave_date = date("Y-m-d", $leave_date);
                            }
                            if(!$leave_date) {
                                throw new Exception("请填写正确格式的下限时间[2016-10-01]");
                            }
                            $timeCondtion = new FlowCondition;
                            $timeCondtion->type = FlowCondition::TYPE_TIME;
                            $timeCondtion->name = "流程条件";
                            $timeCondtion->status = 1;
                            $timeCondtion->lower_limit = "$leave_date";
                        }
                        if($key == "上限时间[必填2016-10-01]") {
                            $leave_date = strtotime($val);
                            if(!$leave_date){
                                $date = explode('-',$val);
                                if(count($date) == 3) {
                                    $leave_date = "20".$date[2]."-".$date[0]."-".$date[1];
                                } 
                            } else {
                                $leave_date = date("Y-m-d", $leave_date);
                            }
                            if(!$leave_date) {
                                throw new Exception("请填写正确格式的上限时间[2016-10-01]");
                            }
                            $timeCondtion->upper_limit = "$leave_date";
                        }
                        if($key == "验证部门[0或空为全部]") {
                            $departmentCondition = new FlowCondition();
                            $departmentCondition->type = FlowCondition::TYPE_AREA;
                            $departmentCondition->name = "流程条件";
                            $departmentCondition->status = 1;
                            $departmentCondition->lower_limit = "0";
                            $departmentCondition->upper_limit = "0";
                            if($val) {
                                $item = Department::findOne(['name' => $val]);
                                if(!$item) {
                                    throw new Exception("请填写正确的验证部门名称");
                                }
                                $departmentCondition->lower_limit = "$item->id";
                            }
                        }
                        if($key == "验证供应商[0或空为全部]") {
                            $supplierCondition = new FlowCondition();
                            $supplierCondition->type = FlowCondition::TYPE_SUPPLIER;
                            $supplierCondition->name = "流程条件";
                            $supplierCondition->status = 1;
                            $supplierCondition->lower_limit = "0";
                            $supplierCondition->upper_limit = "0";
                            if($val) {
                                $item = Supplier::findOne(['name' => $val]);
                                if(!$item) {
                                    throw new Exception("请填写正确的验证供应商名称");
                                }
                                $supplierCondition->lower_limit = "$item->id";
                            }
                        }
                        if($key == "验证商品分类[0或空为全部]") {
                            $cateCondition = new FlowCondition();
                            $cateCondition->type = FlowCondition::TYPE_CATEGORY;
                            $cateCondition->name = "流程条件";
                            $cateCondition->status = 1;
                            $cateCondition->lower_limit = "0";
                            $cateCondition->upper_limit = "0";
                            if($val) {
                                $item = ProductCategory::findOne(['name' => $val]);
                                if(!$item) {
                                    throw new Exception("请填写正确的验证商品分类名称");
                                }
                                $cateCondition->lower_limit = "$item->id";
                            }
                        }
                    }
                    if(!(($model->create_role_id && $model->create_name && $model->create_department_id) || (!$model->create_role_id && !$model->create_name && !$model->create_department_id))){
                        throw new Exception("创建组合错误，要不都有值，要不都为空");
                    }
                    if(!(($model->verify_role_id && $model->verify_name && $model->verify_department_id) || (!$model->verify_role_id && !$model->verify_name && !$model->verify_department_id))) {
                        throw new Exception("审核组合错误，要不都有值，要不都为空");
                    }
                    if(!(($model->approval_role_id && $model->approval_name && $model->approval_department_id) || (!$model->approval_role_id && !$model->approval_name && !$model->approval_department_id))) {
                        throw new Exception("批准组合错误，要不都有值，要不都为空");
                    }
                    if(!(($model->operation_role_id && $model->operation_name && $model->operation_department_id) || (!$model->operation_role_id && !$model->operation_name && !$model->operation_department_id))) {
                        throw new Exception("执行组合错误，要不都有值，要不都为空");
                    }
                    if(!in_array($model->type, Flow::getNoCreateType()) && !($model->create_role_id && $model->create_name && $model->create_department_id)) {
                        throw new Exception("该流程类型必须有创建操作");
                    }
                    if(($model->approval_role_id && $model->approval_name && $model->approval_department_id) && (!$model->verify_role_id || !$model->verify_name || !$model->verify_department_id)) {
                        throw new Exception("流程操作规则有批准就必须有审核");
                    }
                    if(in_array($model->type, Flow::getOperationType()) && !($model->operation_role_id && $model->operation_name && $model->operation_department_id)) {
                        throw new Exception("该流程类型必须有执行操作");
                    }
                    if(!$model->save()){
                        $errors = $model->getFirstErrors();
                        throw new Exception(reset($errors));
                    }
                    if($priceCondtion){
                        $priceCondtion->config_id = $model->id;
                        if(!$priceCondtion->save()){
                            $errors = $priceCondtion->getFirstErrors();
                            throw new Exception(reset($errors));
                        }
                    }
                    if($timeCondtion){
                        $timeCondtion->config_id = $model->id;
                        if(!$timeCondtion->save()){
                            $errors = $timeCondtion->getFirstErrors();
                            throw new Exception(reset($errors));
                        }
                    }
                    if($departmentCondition){
                        $departmentCondition->config_id = $model->id;
                        if(!$departmentCondition->save()){
                            $errors = $departmentCondition->getFirstErrors();
                            throw new Exception(reset($errors));
                        }
                    }
                    if($supplierCondition){
                        $supplierCondition->config_id = $model->id;
                        if(!$supplierCondition->save()){
                            $errors = $supplierCondition->getFirstErrors();
                            throw new Exception(reset($errors));
                        }
                    }
                    if($cateCondition){ 
                        $cateCondition->config_id = $model->id;
                        if(!$cateCondition->save()){
                            $errors = $cateCondition->getFirstErrors();
                            throw new Exception(reset($errors));
                        }
                    }
                }
                Utils::delFile($file);
                $transaction->commit();
            } catch (Exception $exc) {
                Utils::delFile($file);
                $transaction->rollBack();
                return Json::encode(["result" => "Error", "message" => $exc->getMessage()]);
            }
            return Json::encode(["result" => "Success"]);
        }
        return Json::encode(["result" => "Error", "message" => "网络异常"]);
    }
}
