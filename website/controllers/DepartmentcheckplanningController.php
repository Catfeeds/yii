<?php
namespace app_web\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\Json;
use app_web\components\CController;
use yii\helpers\Url;

use common\models\CheckPlanningFlow;
use common\models\Admin;
use common\models\CheckPlanningFlowData;
use common\models\CheckFlow;
use common\models\Department;
use libs\common\Flow;
use libs\CheckInfoService;
use libs\Utils;
use Exception;
use common\models\Supplier;
use common\models\ProductCategory;
use yii\web\UploadedFile;
use moonland\phpexcel\Excel;

class DepartmentcheckplanningController extends CController 
{
    /**
     * 部门盘点计划列表页
     */
    public function actionIndex() {
        $status = Yii::$app->request->get('status');
        $keyword = Yii::$app->request->get('keyword');
        $sn = Yii::$app->request->get('sn');
        $department_id = Yii::$app->request->get('department_id');
        $beginDate = Yii::$app->request->get('beginDate');
        $endDate = Yii::$app->request->get('endDate');
        $model = new CheckPlanningFlow();
        $query = CheckPlanningFlow::find();
        $query->andWhere(['not in', 'status', [Flow::STATUS_VERIFY_REJECT, Flow::STATUS_APPROVAL_REJECT, Flow::STATUS_FINISH_REJECT, Flow::STATUS_UNION_REJECT,  Flow::STATUS_HANG_UP, Flow::STATUS_FINISH]]);
        $query->orWhere(["status" => Flow::STATUS_FINISH, "is_proof" => 0]);
        $query->andWhere(['type' => CheckPlanningFlow::TYPE_DEPARTMENT]);
//        if(!Admin::checkSupperFlowAdmin()) {
//             $query->andWhere(['department_id' => Admin::getDepId()]);
//        }
        if(is_numeric($department_id)){
            $query->andWhere(['department_id' => $department_id]);
        }
        if(is_numeric($status)){
            $query->andWhere(['status' => $status]);
        }
        if($keyword || is_numeric($keyword)){
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
        $query->orderBy('id desc');
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
        return $this->render('index', compact(['model', 'listDatas', 'listPages', 'message']));
    }
    
    /**
     * 添加新的部门盘点计划
     */
    public function actionAddorupdate() 
    {
        $id = Yii::$app->request->get("id");
        if(is_numeric($id)) {
            $model = CheckPlanningFlow::findOne($id);
        } else {
            $model = new CheckPlanningFlow();
        }
        if(Yii::$app->request->post()) {
            $result = $model->addOrUpdateCheckPlanning(Yii::$app->request->post(), CheckPlanningFlow::TYPE_DEPARTMENT);
            if($result["state"]) {
                $return["message"] = "新增成功";
                $return["type"] = "url";
                $return["url"] = Url::to(["departmentcheckplanning/index"]);
                return Json::encode($return);
            }
            $return['message'] = is_array($result["message"]) ? reset($result["message"]) : $result["message"];
            return Json::encode($return);
        }
        return $this->render('addorupdate', compact('model'));
    }
    
    /**
     * 详情页
     */
    public function actionInfo($id)
    {
        $model = CheckPlanningFlow::findOne($id);
        $info = CheckPlanningFlowData::findAll(["check_planning_flow_id" => $id]);
        return $this->render('info', compact('model', 'info'));
    }
    
    /**
     * 审核操作
     * @param type $id 盘点ID
     */
    public function actionVerify($id) 
    {
        $model = CheckPlanningFlow::findOne($id);
        $remark = Yii::$app->request->get('remark');
        $result = Flow::Verify(Flow::TYPE_CHECK_DEPARTMENT, $model, $remark);
        return Json::encode($result);
    }
    
    /**
     * 批准操作
     * @param type $id 盘点ID
     */
    public function actionApproval($id)
    {
        $model = CheckPlanningFlow::findOne($id);
        $remark = Yii::$app->request->get('remark');
        $result = Flow::Approval(Flow::TYPE_CHECK_DEPARTMENT, $model, $remark);
        return Json::encode($result);
    }
    
    /**
     * 完成操作
     * @param type $id 盘点ID
     */
    public function actionFinish($id)
    {
        $model = CheckPlanningFlow::findOne($id);
        $remark = Yii::$app->request->get('remark');
        $result = Flow::Finish(Flow::TYPE_CHECK_DEPARTMENT, $model, $remark);
        return Json::encode($result);
    }

    /**
     * 驳回操作
     * @param type $id 盘点ID
     */
    public function actionReject()
    {
        $id = Yii::$app->request->get('id');
        $failCause = Yii::$app->request->get('failCause');
        $model = CheckPlanningFlow::findOne($id);
        $result = Flow::Reject(Flow::TYPE_CHECK_DEPARTMENT, $model, $failCause);
        return Json::encode($result);
    }
    
    /**
     * 下载部门盘点计划列表
     * @param type $query 查询对象
     * @return type
     */
    public function downloadIndex($query) {
        $all = $query->all();
        $datas = [];    
        foreach ($all as $key => $val) {
            $nextStep = Flow::showNextStepByInfo(Flow::TYPE_CHECK_DEPARTMENT, $val);
            $datas[] = [
                'key' => $key+1,
                "name" => $val->name,
                "sn" => $val->sn,
                "department" => Department::getNameById($val->department_id),
                "check_time" => $val->check_time,
                "product_name" => $val->product_name ? $val->product_name : "全部",
                "product_cate_id" => $val->product_cate_id ? ProductCategory::getNameById($val->product_cate_id): "全部",
                "supplier_id" => $val->supplier_id ? Supplier::getNameById($val->supplier_id) : "全部",
                "is_check_amount" => $val->is_check_amount ? "是" : "否",
                "status" => $val->status == Flow::STATUS_FINISH && !$val->is_proof ? "待校对" : Flow::showStatusAll($val->status),
                "nextStep" => $val->status == Flow::STATUS_FINISH && !$val->is_proof ? "待校对" : isset($nextStep["nextStep"]) ? $nextStep["nextStep"] : "无",
                "nextStepAdmin" => isset($nextStep["nextStepAdmin"]) ? $nextStep["nextStepAdmin"] : "无",
            ];
        }
        $columns = [
            [ 'attribute' => 'key','header' => '序号'],
            [ 'attribute' => 'name','header' => '计划名称'],
            [ 'attribute' => 'sn','header' => '计划单号'],
            [ 'attribute' => 'department','header' => '盘点部门'],
            [ 'attribute' => 'check_time','header' => '预计盘点时间'],
            [ 'attribute' => 'product_name','header' => '盘点物料名称'],
            [ 'attribute' => 'product_cate_id','header' => '盘点商品分类'],
            [ 'attribute' => 'supplier_id','header' => '盘点供应商'],
            [ 'attribute' => 'is_check_amount','header' => '是否盘点资金'],
            [ 'attribute' => 'status','header' => '状态'],
            [ 'attribute' => 'nextStep','header' => '下一步操作'],
            [ 'attribute' => 'nextStepAdmin','header' => '下一步操作人'],
        ];
        return Utils::downloadExcel($datas, $columns, "部门盘点计划列表");
    }
    
    /**
     * 计划校队
     */
    public function actionProof($id) {
        $model = CheckPlanningFlow::findOne($id);
        $data = CheckPlanningFlowData::findAll(["check_planning_flow_id" => $id]);
        $checkService = new CheckInfoService();
        $checkInfo = $checkService->getInfoList($model, $data);
        $checkFlow = new CheckFlow();
        if(Yii::$app->request->post()) {
            $result = $checkFlow->addCheckFlow($model, $checkInfo, $_POST);
            if($result["state"]) {
                $return["message"] = "申请成功";
                $return["type"] = "url";
                $return["url"] = Url::to(["departmentcheckproof/index"]);
                return Json::encode($return);
            }
            $return['message'] = is_array($result["message"]) ? reset($result["message"]) : $result["message"];
            return Json::encode($return);
        }
        return $this->render('proof', compact('model', 'data', 'checkInfo', 'checkFlow'));
    }
    
    /**
     * 下载盘点计划校队物料列表
     * @param type $id 盘点计划ID
     * @return type
     */
    public function actionDownproof($id) {
        $model = CheckPlanningFlow::findOne($id);
        $data = CheckPlanningFlowData::findAll(["check_planning_flow_id" => $id]);
        $checkService = new CheckInfoService();
        $checkInfo = $checkService->getInfoList($model, $data);
        $datas = [];    
        foreach($data as $val){
            if(!isset($checkInfo["productList"][$val->data_id]) || !count($checkInfo["productList"][$val->data_id])){
                continue;
            }
            foreach($checkInfo["productList"][$val->data_id] as $pstockId => $infoVal){
                $datas[] = [
                    "pstockId" => $pstockId,
                    "department_name" => $val->data_name,
                    "warehouse_name" => $infoVal["warehouse_name"],
                    "name" => $infoVal["name"],
                    "batches" => $infoVal["batches"],
                    "supplier_id" => Supplier::getNameById($infoVal["supplier_id"]),
                    "barcode" => $infoVal["barcode"],
                    "spec" => $infoVal["spec"],
                    "unit" => $infoVal["unit"],
                    "sale_price" => $infoVal["sale_price"],
                    "number" => $infoVal["number"],
                    "checkStock" => '',
                ];
            }
        }
        $columns = [
            [ 'attribute' => 'pstockId','header' => '库存ID'],
            [ 'attribute' => 'department_name','header' => '所属部门'],
            [ 'attribute' => 'warehouse_name','header' => '所属仓库'],
            [ 'attribute' => 'name','header' => '物料名称'],
            [ 'attribute' => 'batches','header' => '批次号'],
            [ 'attribute' => 'supplier_id','header' => '供应商'],
            [ 'attribute' => 'barcode','header' => '条形码'],
            [ 'attribute' => 'spec','header' => '规格'],
            [ 'attribute' => 'unit','header' => '单位'],
            [ 'attribute' => 'sale_price','header' => '销售价格'],
            [ 'attribute' => 'number','header' => '库存数量'],
            [ 'attribute' => 'checkStock','header' => '盘点数量'],
        ];
        return Utils::downloadExcel($datas, $columns, $model->name."的盘点物料记录");
    }
    
    /**
     * 导入盘点计划校队物料记录
     * @param type $id 盘点计划ID
     * @return type
     * @throws Exception
     */
    public function actionImportproof($id) {
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
            try {
                $importList = [];
                foreach($datas as $data){
                    $pstockId = 0;
                    foreach ($data as $key => $val) {
                        if($key == "库存ID") {
                            if(!is_numeric($val)) {
                                throw new Exception("无效的库存ID：".$val);
                            }
                            $pstockId = $val;
                        }
                        if($key == "盘点数量") {
                            if(!is_numeric($val)) {
                                throw new Exception("无效的盘点数量：".$val);
                            }
                            $importList[$pstockId] = $val;
                        }
                    }
                }
                Yii::$app->cache->set("importList_".$id, $importList, 60);
                Utils::delFile($file);
            } catch (Exception $exc) {
                Utils::delFile($file);
                return Json::encode(["result" => "Error", "message" => $exc->getMessage() ? $exc->getMessage() : $exc->getTraceAsString()]);
            }
            return Json::encode(["result" => "Success"]);
        }
        return Json::encode(["result" => "Error", "message" => "网络异常"]);
    }
}

