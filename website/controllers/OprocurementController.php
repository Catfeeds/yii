<?php
namespace app_web\controllers;

use Yii;
use app_web\components\CController;
use yii\data\ActiveDataProvider;
use common\models\OrderProcurement;
use common\models\OrderProcurementProduct;
use yii\helpers\Json;
use libs\common\Flow;
use common\models\Admin;
use common\models\Warehouse;
use common\models\FlowConfig;
use common\models\Supplier;
use common\models\WarehousePlanning;
use libs\Utils;
use common\models\DepartmentBalanceLog;
use common\models\CheckFlow;
/**
 * 业务操作 - 资金流水 - 计划下单支付
 */
class OprocurementController extends CController
{
    /**
     * 计划下单支付列表页
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

        $model = new OrderProcurement();
        $query = OrderProcurement::find();
        $query->andWhere(['not in', 'status', [Flow::STATUS_VERIFY_REJECT, Flow::STATUS_APPROVAL_REJECT, Flow::STATUS_FINISH_REJECT, Flow::STATUS_UNION_REJECT,  Flow::STATUS_FINISH, Flow::STATUS_HANG_UP]]);
        $query->orWhere(["status" => Flow::STATUS_FINISH, "pay_state" => [OrderProcurement::PAY_STATE_NO, OrderProcurement::PAY_STATE_DEPOSIT]]);
        if(is_numeric($status)){
            $query->andWhere(['status' => $status]);
        }
        if($keyword || $keyword === '0'){
            $query->andWhere(['like', 'name', $keyword]);
        }
        if($sn || is_numeric($sn)){
            $query->andWhere(['like', 'sn', $sn]);
        }
//        if(!Admin::checkSupperFlowAdmin()){
//            $query->andWhere(["department_id" => Admin::getDepId()]);
//        }
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
        $isDownload = Yii::$app->request->get('isDownload');
        if($isDownload) {
            $this->ExportIndex($query);
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
     * 导出处理 - 订单支付列表
     */
    public function ExportIndex($query)
    {
        $all = $query->all();
        $datas = [];
        foreach ($all as $key => $val) {
            $nextStep = Flow::showNextStepByInfo(Flow::TYPE_ORDER_FINANCE, $val);
            $datas[] = [
                'key' => $key+1,
                'create_time' => $val->create_time,
                'name' => $val->name,
                'sn' => $val->sn,
                'warehouse_id' => Warehouse::getNameById($val->warehouse_id),
                'supplier_id' => Supplier::getNameById($val->supplier_id) ,
                'total_amount' => $val->total_amount,
                'config_id' => FlowConfig::getNameById($val->config_id),                
                'create_admin_id' => Admin::getNameById($val->create_admin_id),                
                'verify_admin_id' => Admin::getNameById($val->verify_admin_id),                
                'approval_admin_id' => Admin::getNameById($val->approval_admin_id),                
                'operation_admin_id' => Admin::getNameById($val->operation_admin_id),                
                'status' => Flow::showStatus($val->status),    
                'nextStep' => isset($nextStep["nextStep"]) ? $nextStep["nextStep"] : "无",
            ];
        }
        $columns = [
            [ 'attribute' => 'key','header' => '序号'],
            [ 'attribute' => 'create_time','header' => '创建时间'],
            [ 'attribute' => 'name','header' => '支付名称'],
            [ 'attribute' => 'sn','header' => '支付编号'],
            [ 'attribute' => 'warehouse_id','header' => '购买仓库'],
            [ 'attribute' => 'supplier_id','header' => '供应商'],
            [ 'attribute' => 'total_amount','header' => '总价'],
            [ 'attribute' => 'config_id','header' => '流程名称'],
            [ 'attribute' => 'create_admin_id','header' => '制表人'],
            [ 'attribute' => 'verify_admin_id','header' => '审核人'],
            [ 'attribute' => 'approval_admin_id','header' => '批准人'],
            [ 'attribute' => 'operation_admin_id','header' => '执行人'],
            [ 'attribute' => 'status','header' => '进展状态'],
            [ 'attribute' => 'nextStep','header' => '下一步操作'],
        ];
        return Utils::downloadExcel($datas, $columns, "订单支付列表");
    }
    
    /**
     * 计划下单支付记录详情页
     * @param integer $id 记录ID
     */
    public function actionInfo($id)
    {
        $model = OrderProcurement::findOne($id);
        $info = OrderProcurementProduct::findAll(["order_procurement_id" => $id]);
        return $this->render('info', compact('model', 'info'));
    }
    
    /**
     * 计划下单支付记录审核
     * @param integer $id 记录ID
     */
    public function actionVerify($id) 
    {
        $model = OrderProcurement::findOne($id);
        $remark = Yii::$app->request->get("remark");
        $result = Flow::Verify(Flow::TYPE_ORDER_FINANCE, $model, $remark);
        return Json::encode($result);
    }
    
    /**
     * 计划下单支付记录批准
     * @param integer $id 记录ID
     */
    public function actionApproval($id)
    {
        $model = OrderProcurement::findOne($id);
        $remark = Yii::$app->request->get("remark");
        $result = Flow::Approval(Flow::TYPE_ORDER_FINANCE, $model, $remark);
        return Json::encode($result);
    }
    
    /**
     * 计划下单支付记录完成
     * @param integer $id 记录ID
     */
    public function actionFinish($id)
    {
        $model = OrderProcurement::findOne($id);
        $remark = Yii::$app->request->get("remark");
        $result = Flow::Finish(Flow::TYPE_ORDER_FINANCE, $model, $remark);
        return Json::encode($result);
    }

    /**
     * 计划下单支付记录驳回
     * @param integer $id 记录ID
     */
    public function actionReject()
    {
        $id = Yii::$app->request->get('id');
        $failCause = Yii::$app->request->get('failCause');
        $model = OrderProcurement::findOne($id);
        $result = Flow::Reject(Flow::TYPE_ORDER_FINANCE, $model, $failCause);
        return Json::encode($result);
    }
    
    /**
     * 部分支付
     */
    public function actionPaydeposit($id) {
        $model = OrderProcurement::findOne($id);
        if($model->status != Flow::STATUS_FINISH || $model->pay_state == OrderProcurement::PAY_STATUS_ALL) {
            $result = ["error" => 1, "message" => "状态错误，无法支付"];
            return Json::encode($result);
        }
        if(in_array($model->payment, [WarehousePlanning::PAYMENT_ADVANCE, WarehousePlanning::PAYMENT_BARGAIN]) && $model->pay_state == OrderProcurement::PAY_STATE_NO) {
            $transaction = Yii::$app->db->beginTransaction();
            try{
                $model->pay_state = OrderProcurement::PAY_STATE_DEPOSIT;
                $model->pay_deposit_time = date("Y-m-d H:i:s");
                if($model->save()) {
                    $isCheck = CheckFlow::amountIsCheckFlow($model->department_id);
                    if($isCheck) {
                        $result = ["error" => 1, "message" => "部门处于资金盘点中，无法执行采购下定支付"];
                        $transaction->rollBack();
                    } else {
                        if($model->deposit == 0) {
                            $result = ["error" => 0, "message" => "支付成功"];
                            $transaction->commit();
                        } else {
                            $logModel = new DepartmentBalanceLog();
                            $logResult = $logModel->addDepartmentBalanceLog($model->department_id, $model->id, DepartmentBalanceLog::BUSINESS_TYPE_ORDER, DepartmentBalanceLog::MOD_OUT, $model->deposit, "计划下单定金支付");
                            if(!$logResult["state"]) {
                                $result = ["error" => 1, "message" => $logResult["message"]];
                                $transaction->rollBack();
                            } else {
                                $result = ["error" => 0, "message" => "支付成功"];
                                $transaction->commit();
                            }
                        }
                    }
                    return Json::encode($result);
                }
                $transaction->rollBack();
                $result = ["error" => 1, "message" => $model->getFirstErrors()];
                return Json::encode($result);
            } catch (Exception $ex) {
                $transaction->rollBack();
                $result = ["error" => 1, "message" => $ex->getTraceAsString()];
                return Json::encode($result);
            }
        }
        $result = ["error" => 1, "message" => "状态错误，无法支付"];
        return Json::encode($result);
    }
    
    /**
     * 全部支付
     */
    public function actionPayall($id) {
        $model = OrderProcurement::findOne($id);
        if($model->status != Flow::STATUS_FINISH || $model->pay_state == OrderProcurement::PAY_STATUS_ALL) {
            $result = ["error" => 1, "message" => "状态错误，无法支付"];
            return Json::encode($result);
        }
        if((in_array($model->payment, [WarehousePlanning::PAYMENT_ADVANCE, WarehousePlanning::PAYMENT_BARGAIN]) && $model->pay_state == OrderProcurement::PAY_STATE_DEPOSIT)
          || ($model->payment == WarehousePlanning::PAYMENT_LATER && $model->pay_state == OrderProcurement::PAY_STATE_NO)) {
            $transaction = Yii::$app->db->beginTransaction();
            try{
                $model->pay_state = OrderProcurement::PAY_STATUS_ALL;
                $model->pay_all_time = date("Y-m-d H:i:s");
                if($model->save()) {
                    $isCheck = CheckFlow::amountIsCheckFlow($model->department_id);
                    if($isCheck) {
                        $result = ["error" => 1, "message" => "部门处于资金盘点中，无法执行采购下定支付"];
                        $transaction->rollBack();
                    }else {
                        $money = in_array($model->payment, [WarehousePlanning::PAYMENT_ADVANCE, WarehousePlanning::PAYMENT_BARGAIN]) ? $model->total_amount - $model->deposit : $model->total_amount;
                        $logModel = new DepartmentBalanceLog();
                        $logResult = $logModel->addDepartmentBalanceLog($model->department_id, $model->id, DepartmentBalanceLog::BUSINESS_TYPE_ORDER, DepartmentBalanceLog::MOD_OUT, $money, "计划下单剩余支付");
                        if(!$logResult["state"]) {
                            $result = ["error" => 1, "message" => $logResult["message"]];
                            $transaction->rollBack();
                        } else {
                            $result = ["error" => 0, "message" => "支付成功"];
                            $transaction->commit();
                        }
                    }
                    return Json::encode($result);
                }
            } catch (Exception $ex) {
                $transaction->rollBack();
                $result = ["error" => 1, "message" => $ex->getTraceAsString()];
                return Json::encode($result);
            }
            
            $result = ["error" => 1, "message" => $model->getFirstErrors()];
            return Json::encode($result);
        }
        $result = ["error" => 1, "message" => "状态错误，无法支付"];
        return Json::encode($result);
    }
}
