<?php
namespace app_web\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\Json;
use app_web\components\CController;
use yii\helpers\ArrayHelper;

use common\models\CheckPlanningFlow;
use common\models\CheckFlow;
use common\models\CheckFlowAmount;
use common\models\CheckFlowProduct;
use common\models\CheckPlanningFlowData;
use common\models\Admin;
use common\models\Warehouse;
use common\models\FlowConfig;
use libs\common\Flow;
use libs\Utils;

/**
 * 业务操作 -- 库存管理 -- 仓库盘点校对
 */
class WarehousecheckproofController extends CController {
    
    /**
     * 仓库盘点校对列表
     */
    public function actionIndex() {
        $status = Yii::$app->request->get('status');
        $keyword = Yii::$app->request->get('keyword');
        $sn = Yii::$app->request->get('sn');
        $warehouse_id = Yii::$app->request->get('warehouse_id');
        $beginDate = Yii::$app->request->get('beginDate');
        $endDate = Yii::$app->request->get('endDate');
        $model = new CheckFlow();
        $query = CheckFlow::find();
        $query->andWhere(['not in', 'status', [Flow::STATUS_VERIFY_REJECT, Flow::STATUS_APPROVAL_REJECT, Flow::STATUS_FINISH_REJECT, Flow::STATUS_UNION_REJECT,  Flow::STATUS_FINISH, Flow::STATUS_HANG_UP]]);
        $query->andWhere(['type' => CheckPlanningFlow::TYPE_WAREHOUSE]);
//        if(!Admin::checkSupperFlowAdmin()) {
//             $query->andWhere(['warehouse_id' => Admin::getWarehouseIdsById()]);
//        }
        if(is_numeric($warehouse_id)){
            $query->andWhere(['warehouse_id' => $warehouse_id]);
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
     * 仓库盘点校对记录详情页
     * @param type $id 记录ID
     * @return type
     */
    public function actionInfo($id) {
        $model = CheckFlow::findOne($id);
        $amount = CheckFlowAmount::findAll(["check_flow_id" => $id]);
        $amount = ArrayHelper::index($amount, "check_department_id");
        $product = CheckFlowProduct::findAll(["check_flow_id" => $id]);
        $product = ArrayHelper::index($product, null,"warehouse_id");
        $data = CheckPlanningFlowData::findAll(["check_planning_flow_id" => $model->check_planning_id]);
        return $this->render("info", compact("model", "amount", "product", "data"));
    }
    
    /**
     * 审核操作
     * @param type $id 记录ID
     */
    public function actionVerify($id) 
    {
        $model = CheckFlow::findOne($id);
        $remark = Yii::$app->request->get('remark');
        $result = Flow::Verify(Flow::TYPE_CHECK_WAREHOUSE_PROOF, $model, $remark);
        return Json::encode($result);
    }
    
    /**
     * 批准操作
     * @param type $id 记录ID
     */
    public function actionApproval($id)
    {
        $model = CheckFlow::findOne($id);
        $remark = Yii::$app->request->get('remark');
        $result = Flow::Approval(Flow::TYPE_CHECK_WAREHOUSE_PROOF, $model, $remark);
        return Json::encode($result);
    }
    
    /**
     * 完成操作
     * @param type $id 记录ID
     */
    public function actionFinish($id)
    {
        $model = CheckFlow::findOne($id);
        $remark = Yii::$app->request->get('remark');
        $result = Flow::Finish(Flow::TYPE_CHECK_WAREHOUSE_PROOF, $model, $remark);
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
        $model = CheckFlow::findOne($id);
        $result = Flow::Reject(Flow::TYPE_CHECK_WAREHOUSE_PROOF, $model, $failCause);
        return Json::encode($result);
    }
    
    /**
     * 导出仓库盘点校对记录
     * @param type $query 查询对象
     * @return type
     */
    public function downloadIndex($query) {
        $all = $query->all();
        $datas = [];    
        foreach ($all as $key => $val) {
            $nextStep = Flow::showNextStepByInfo(Flow::TYPE_CHECK_WAREHOUSE_PROOF, $val);
            $datas[] = [
                'key' => $key+1,
                "name" => $val->name,
                "sn" => $val->sn,
                "create_time" => $val->create_time,
                "create_admin_id" => Admin::getNameById($val->create_admin_id) ,
                "warehouse_id" => Warehouse::getNameById($val->warehouse_id),
                "total_buying_amount" => $val->total_buying_amount,
                "check_buying_amount" => $val->check_buying_amount,
                "status" => Flow::showStatusAll($val->status),
                "config_id" => FlowConfig::getNameById($val->config_id),
                "nextStep" => isset($nextStep["nextStep"]) ? $nextStep["nextStep"] : "无",
                "nextStepAdmin" => isset($nextStep["nextStepAdmin"]) ? $nextStep["nextStepAdmin"] : "无",
            ];
        }
        $columns = [
            [ 'attribute' => 'key','header' => '序号'],
            [ 'attribute' => 'name','header' => '校队名称'],
            [ 'attribute' => 'sn','header' => '校队单号'],
            [ 'attribute' => 'create_time','header' => '盘点时间'],
            [ 'attribute' => 'create_admin_id','header' => '盘点人'],
            [ 'attribute' => 'warehouse_id','header' => '盘点仓库'],
            [ 'attribute' => 'total_buying_amount','header' => '盘点前物料金额'],
            [ 'attribute' => 'check_buying_amount','header' => '盘点后物料金额'],
            [ 'attribute' => 'status','header' => '状态'],
            [ 'attribute' => 'config_id','header' => '流程名'],
            [ 'attribute' => 'nextStep','header' => '下一步操作'],
            [ 'attribute' => 'nextStepAdmin','header' => '下一步操作人'],
        ];
        return Utils::downloadExcel($datas, $columns, "仓库盘点计划校对列表");
    }
}

