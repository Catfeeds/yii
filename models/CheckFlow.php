<?php

namespace common\models;

use Yii;
use Exception;
use yii\helpers\ArrayHelper;

use common\models\BusinessAll;
use common\models\AdminLog;
use common\models\CheckPlanningFlow;
use common\models\CheckFlowAmount;
use common\models\CheckFlowProduct;
use common\models\DepartmentBalance;
use common\models\ProductStock;
use common\models\DepartmentBalanceLog;
use libs\Utils;
use libs\common\Flow;

/**
 * This is the model class for table "CheckFlow".
 *
 * @property integer $id
 * @property integer $check_planning_id
 * @property integer $type
 * @property string $name
 * @property string $sn
 * @property string $total_buying_amount
 * @property string $check_buying_amount
 * @property integer $status
 * @property integer $config_id
 * @property integer $create_admin_id
 * @property string $create_time
 * @property integer $verify_admin_id
 * @property string $verify_time
 * @property integer $approval_admin_id
 * @property string $approval_time
 * @property integer $operation_admin_id
 * @property string $operation_time
 * @property string $failCause
 * @property integer $department_id
 * @property integer $warehouse_id
 * @property string $end_time
 * @prpperty integer $timing_type
 */
class CheckFlow extends namespace\base\CheckFlow 
{
    public function addCheckFlow($checkPlanningItem, $checkInfo, $post) {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if($checkPlanningItem->is_check_amount) {
                if(!isset($post["checkAmount"]) || !count($post["checkAmount"])) {
                    throw new Exception("请填写部门真实余额");
                }
            }
            $this->attributes = $post["CheckFlow"];
            switch ($checkPlanningItem->type) {
                case CheckPlanningFlow::TYPE_PLANNING:
                    $flowType = Flow::TYPE_CHECK_PLANNING_PROOF;
                    $message = "总盘点计划校对申请成功";
                    break;
                case CheckPlanningFlow::TYPE_DEPARTMENT:
                    $flowType = Flow::TYPE_CHECK_DEPARTMENT_PROOF;
                    $message = "部门盘点计划校对申请成功";
                    break;
                case CheckPlanningFlow::TYPE_WAREHOUSE:
                    $flowType = Flow::TYPE_CHECK_WAREHOUSE_PROOF;
                    $message = "仓库盘点计划校对申请成功";
                    break;
                default :
                    throw new Exception("网络异常");
            }
            $this->sn = Utils::generateSn($flowType);
            if(count(array_filter($checkInfo["productList"])) > 0 && (!isset($post["checkStock"]) || !count($post["checkStock"]))) {
                throw new Exception("请填写部门物料真实库存");
            }
            $this->check_planning_id = $checkPlanningItem->id;
            $this->type = $checkPlanningItem->type;
            $this->end_time = $checkPlanningItem->end_time;
            $this->total_buying_amount = 0;
            $this->check_buying_amount = 0;
            $this->status = Flow::STATUS_APPLY_VERIFY;
            $this->config_id = 0;
            $this->create_admin_id = Yii::$app->user->getId();
            $this->create_time = date("Y-m-d H:i:s");
            if(!$this->validate()) {
                $message = $this->getFirstErrors();
                throw new Exception(reset($message));
            }
            $this->save();
            if(isset($post["checkAmount"])){ 
                foreach ($post["checkAmount"] as $departmentId => $checkAmount) {
                    $childModel = new CheckFlowAmount();
                    $childModel->check_flow_id = $this->id;
                    $childModel->check_department_id = $departmentId;
                    $childModel->amount = isset($checkInfo["amountList"][$departmentId]) ? $checkInfo["amountList"][$departmentId] : 0;
                    $childModel->check_amount = $checkAmount;
                    if(!$childModel->validate()) {
                        $message = $childModel->getFirstErrors();
                        throw new Exception(reset($message));
                    }
                    $childModel->save();
                }
            }
            $total_buying_amount = $check_buying_amount = 0;
            $supplier = $productCate = [];
            if(isset($post["checkStock"])){
                foreach ($post["checkStock"] as $dataId => $checkStock) {
                    if(!isset($checkInfo["productList"][$dataId])) {
                        continue;
                    }
                    $productList = $checkInfo["productList"][$dataId];
                    foreach ($checkStock as $pstockId => $stockNum) {
                        if(!isset($productList[$pstockId])) {
                            continue;
                        }
                        $childModel = new CheckFlowProduct();
                        $childModel->check_flow_id = $this->id;
                        $childModel->pstock_id = $pstockId;
                        $childModel->batches = $productList[$pstockId]["batches"];
                        $childModel->product_id = $productList[$pstockId]["product_id"];
                        $childModel->name = $productList[$pstockId]["name"];
                        $childModel->purchase_price = $productList[$pstockId]["purchase_price"];
                        $childModel->sale_price = $productList[$pstockId]["sale_price"];
                        $childModel->product_number = $productList[$pstockId]["number"];
                        $childModel->buying_number = $stockNum;
                        $childModel->total_amount = $childModel->purchase_price * $childModel->buying_number;
                        $childModel->supplier_id = $productList[$pstockId]["supplier_id"];
                        $childModel->supplier_product_id = $productList[$pstockId]["supplier_product_id"];
                        $childModel->barcode = $productList[$pstockId]["barcode"];
                        $childModel->spec = $productList[$pstockId]["spec"];
                        $childModel->unit = $productList[$pstockId]["unit"];
                        $childModel->material_type = $productList[$pstockId]["material_type"];
                        $childModel->warehouse_id = $productList[$pstockId]["warehouse_id"];
                        $childModel->department_id = $productList[$pstockId]["department_id"];
                        $childModel->status = 1;
                        $childModel->type = $productList[$pstockId]["type"];
                        if(!$childModel->validate()) {
                            $message = $childModel->getFirstErrors();
                            throw new Exception(reset($message));
                        }
                        $childModel->save();
                        $total_buying_amount += $childModel->purchase_price * $childModel->product_number;
                        $check_buying_amount += $childModel->purchase_price * $childModel->buying_number;
                        $supplier = $productList[$pstockId]["supplier_id"];
                        $productCate = $productList[$pstockId]["material_type"];
                    }
                }
            }
            $result = Flow::confirmFollowAdminId($flowType, $this, 0, time(), [], $supplier, $productCate);
            if(!$result["state"]) {
                throw new Exception($result["message"]);
            }
            $this->total_buying_amount = $total_buying_amount;
            $this->check_buying_amount = $check_buying_amount;
            if(!$this->save()) {
                $message = $this->getFirstErrors();
                throw new Exception(reset($message));
            }
            $checkPlanningItem->is_proof = 1;
            if(!$checkPlanningItem->save()) {
                $message = $checkPlanningItem->getFirstErrors();
                throw new Exception(reset($message));
            }
            $businessModel = new BusinessAll();
            $business = $businessModel->addBusiness($this, $flowType);
            if(!$business["state"]) {
                $message = reset($business["message"]);
                throw new Exception($message);
            }
            AdminLog::addLog("checkFlow_add", $message." ：".$this->id);
            $transaction->commit();
            return array("state" => 1);
        } catch (Exception $ex) {
            $transaction->rollBack();
            return ["state" => 0, "message" => $ex->getMessage()];
        }
    }
    
    public function Finish() {
        $amount = CheckFlowAmount::findAll(["check_flow_id" => $this->id]);
        foreach ($amount as $val) {
            $departmentBalance = DepartmentBalance::findOne($val->check_department_id);
            if(!$departmentBalace) {
                $departmentBalace = new DepartmentBalance();
                $departmentBalace->department_id = $val->check_department_id;
                $departmentBalace->income_amount = 0;
                $departmentBalace->expenses_amount = 0;
                $departmentBalace->balance = 0;
            }
            $departmentBalace->balance = $departmentBalace->balance + $val->check_amount - $val->amount;
            if(!$departmentBalace->validate()) {
                $message = $departmentBalace->getFirstErrors();
                return ["state" => 0, "message" => reset($message)];
            }
            $departmentBalace->save();
            $balanceLog = new DepartmentBalanceLog();
            $result = $balanceLog->addDepartmentBalanceLog($val->check_department_id, $val->id, DepartmentBalanceLog::BUSINESS_TYPE_AMOUNT_CHECK, DepartmentBalanceLog::MOD_CHECK, $departmentBalace->balance, '盘点资金变动');
            if(!$result["state"]) {
                return $result;
            }
        }
        $product = CheckFlowProduct::findAll(["check_flow_id" => $this->id]);
        foreach ($product as $val) {
            $stockOutItem = ProductStock::findOne($val->pstock_id);
            if(!$stockOutItem) {
                continue;
            }
            $result = WarehouseGateway::addWarehouseGateway($val->warehouse_id, $val->product_id, WarehouseGateway::TYPE_IN, $stockOutItem->number, $val->buying_number, $this->id, WarehouseGateway::GATEWAY_TYPE_CHECK, $val->type, $stockOutItem->batches);
            if(!$result["state"]) {
                return $result;
            }
            $stockOutItem->number = $stockOutItem->number + $val->buying_number -  $val->product_number;
            $stockOutItem->save();
        }
        return ["state" => 1];
    }
}
