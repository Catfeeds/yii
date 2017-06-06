<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\BaseActiveRecord;
use yii\db\Expression;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use libs\Utils;

use common\models\WarehousePlanning;
use common\models\OrderProcurementProduct;
use common\models\AdminLog;
use libs\common\Flow;
use common\models\DepartmentBalanceLog;
use common\models\BusinessAll;

/**
 * This is the model class for table "OrderProcurement".
 *
 * @property integer $id
 * @property string $name
 * @property string $procurement_id
 * @property string $sn
 * @property string $order_sn
 * @property integer $warehouse_id
 * @property integer $supplier_id
 * @property string $planning_date
 * @property integer $payment
 * @property double $deposit
 * @property double $total_amount
 * @property string $payment_term
 * @property integer $create_admin_id
 * @property integer $verify_admin_id
 * @property integer $approval_admin_id
 * @property integer $operation_admin_id
 * @property integer $status
 * @property string $create_time
 * @property integer $config_id
 * @property string $failCause
 * @property integer $department_id
 * @property string $verify_time
 * @property string $approval_time
 * @property string $operation_time
 * @property integer $pay_state
 * @property string $pay_deposit_time
 * @property string $pay_all_time
 * @property integer $buckle_amount
 * @property integer $timing_type
 */
class OrderProcurement extends namespace\base\OrderProcurement
{    
    public $noPaySum;
    public $payDeposit;
    public $payAll;
    /**
     * 未支付
     */
    CONST PAY_STATE_NO = 0;
    /**
     * 支付定金
     */
    CONST PAY_STATE_DEPOSIT = 1;
    /**
     * 全部支付
     */
    CONST PAY_STATUS_ALL = 2;
    
    public static $_payStateAll = [
        self::PAY_STATE_NO => "未支付",
        self::PAY_STATE_DEPOSIT => "支付部分",
        self::PAY_STATUS_ALL => "全部支付",
    ];
    
    /**
     * 获取记录支付方式
     */
    public function showPayment() 
    {
        return isset(WarehousePlanning::$_paymentAll[$this->payment]) ? WarehousePlanning::$_paymentAll[$this->payment] : "未知".$this->payment;
    }
    
    /**
     * 展示付款状态
     */
    public function showPayState() {
        return isset(self::$_payStateAll[$this->pay_state]) ? self::$_payStateAll[$this->pay_state] : "未知" . $this->pay_state;
    }
    
    /**
     * 展示付款连接
     */
    public function showPayUrl() {
        if($this->status != Flow::STATUS_FINISH || $this->pay_state == self::PAY_STATUS_ALL) {
            return [];
        }
        if($this->payment == WarehousePlanning::PAYMENT_ADVANCE && $this->pay_state == self::PAY_STATE_NO) {
            return ["payUrl" => '<a class="button blue-button" get-update-reload="'.Url::to(['oprocurement/paydeposit', "id" => $this->id]).'">支付预付</a> '];
        }
        if($this->payment == WarehousePlanning::PAYMENT_BARGAIN && $this->pay_state == self::PAY_STATE_NO) {
            return ["payUrl" => '<a class="button blue-button" get-update-reload="'.Url::to(['oprocurement/paydeposit', "id" => $this->id]).'">支付定金</a> '];
        }
        if(in_array($this->payment, [WarehousePlanning::PAYMENT_ADVANCE, WarehousePlanning::PAYMENT_BARGAIN]) && $this->pay_state == self::PAY_STATE_DEPOSIT) {
            return ["payUrl" => '<a class="button blue-button" get-update-reload="'.Url::to(['oprocurement/payall', "id" => $this->id]).'">支付余款</a> '];
        }
        if($this->payment == WarehousePlanning::PAYMENT_LATER && $this->pay_state == self::PAY_STATE_NO) {
            return ["payUrl" => '<a class="button blue-button" get-update-reload="'.Url::to(['oprocurement/payall', "id" => $this->id]).'">全额支付</a> '];
        }
        return [];
    }
    
    /**
     * 添加采购下单的资金流水记录
     * @param type $procurementItem 采购下定记录
     * @param type $procurementProduct 采购下定物料记录
     * @return type
     */
    public function addOrderProcurement($procurementItem, $procurementProduct, $buyingId) 
    {
        $planningItem = WarehousePlanning::findOne($procurementItem->procurement_planning_id);
        $this->name = $procurementItem->name;
        $this->procurement_id = $buyingId;
        $this->sn = Utils::generateSn(Flow::TYPE_ORDER_FINANCE);
        $this->order_sn = $procurementItem->order_sn;
        $this->warehouse_id = $procurementItem->warehouse_id;
        $this->department_id = $procurementItem->department_id;
        $this->supplier_id = $procurementItem->supplier_id;
        $this->planning_date = $procurementItem->planning_date;
        $this->total_amount = $procurementItem->total_amount;
        $this->create_admin_id = Yii::$app->user->getId();
        $this->verify_admin_id = 0;
        $this->approval_admin_id = 0;
        $this->operation_admin_id = 0;
        $this->status = Flow::STATUS_APPLY_VERIFY;
        $this->create_time = date("Y-m-d H:i:s");
        $this->payment = $procurementItem->payment;
        $this->deposit = is_numeric($procurementItem->deposit) ? $procurementItem->deposit : 0;
        $this->payment_term = $procurementItem->payment_term;
        $this->config_id = 0;
        $this->pay_state = self::PAY_STATE_NO;
        $this->buckle_amount = $planningItem->buckle_amount;
        if(!$this->validate()) {
            return array("state" => 0, "message" => $this->getFirstErrors());
        }
        $this->save();
        $meterialType = $supplier = array();
        foreach ($procurementProduct as $productItem) {
            $productModel = new OrderProcurementProduct();
            $productModel->product_id = $productItem->product_id;
            $productModel->order_procurement_id = $this->id;
            $productModel->name = $productItem->name;
            $productModel->price = $productItem->price;
            $productModel->purchase_price = $productItem->purchase_price;
            $productModel->product_number = $productItem->product_number;
            $productModel->total_amount = $productModel->purchase_price * $productModel->product_number;
            $productModel->supplier_id = $productItem->supplier_id;
            $productModel->supplier_product_id = $productItem->supplier_product_id;
            $productModel->num = $productItem->num;
            $productModel->spec = $productItem->spec;
            $productModel->unit = $productItem->unit;
            $productModel->material_type = $productItem->material_type;
            if(!$productModel->save()) {
                return array("state" => 0, "message" => $productModel->getFirstErrors());
            }
            $meterialType[] = $productModel->material_type;
            $supplier[] = $productModel->supplier_id;
        }
        $date = date("m", strtotime($this->create_time));
        $areaId = 0;
        $result = Flow::confirmFollowAdminId(Flow::TYPE_ORDER_FINANCE, $this, $this->total_amount, $date, $areaId, $supplier, $meterialType);
        if(!$result["state"]) {
            return $result;
        }
        if(!$this->save()){
            return array("state" => 0, "message" => $this->getFirstErrors());
        }
        $businessModel = new BusinessAll();
        $business = $businessModel->addBusiness($this, Flow::TYPE_ORDER_FINANCE);
        if(!$business["state"]) {
            return ["state" => 0, "message" => $business["message"]];
        }
        if($this->status == Flow::STATUS_FINISH){
            $result = $this->Finish();
            if(!$result["state"]) {
                return $result;
            }
        }
        AdminLog::addLog("oprocurement_add", "采购计划下定财务记录添加成功：".$this->id);
        return array("state" => 1);
    }
    
    /**
     * 添加采购下单的资金流水记录
     * @param type $item 采购计划记录
     * @param type $product 采购计划物料记录
     * @return type
     */
    public function addNewOrderProcurement($item, $product) 
    {
        $this->name = $item->name;
        $this->procurement_id = $item->id;
        $this->sn = Utils::generateSn(Flow::TYPE_ORDER_FINANCE);
        $this->order_sn = $item->order_sn;
        $this->warehouse_id = $item->warehouse_id;
        $this->department_id = $item->department_id;
        $this->supplier_id = $item->supplier_id;
        $this->planning_date = $item->planning_date;
        $this->total_amount = $item->total_money;
        $this->create_admin_id = Yii::$app->user->getId();
        $this->verify_admin_id = 0;
        $this->approval_admin_id = 0;
        $this->operation_admin_id = 0;
        $this->status = Flow::STATUS_APPLY_VERIFY;
        $this->create_time = date("Y-m-d H:i:s");
        $this->payment = $item->payment;
        $this->deposit = $item->deposit;
        $this->payment_term = $item->payment_term;
        $this->config_id = 0;
        $this->pay_state = self::PAY_STATE_NO;
        $this->buckle_amount = $item->buckle_amount;
        if(!$this->validate()) {
            return array("state" => 0, "message" => $this->getFirstErrors());
        }
        $this->save();
        $meterialType = $supplier = array();
        foreach ($product as $productItem) {
            $productModel = new OrderProcurementProduct();
            $productModel->product_id = $productItem->product_id;
            $productModel->order_procurement_id = $this->id;
            $productModel->name = $productItem->name;
            $productModel->price = $productItem->price;
            $productModel->purchase_price = $productItem->purchase_price;
            $productModel->product_number = $productItem->product_number;
            $productModel->total_amount = $productModel->purchase_price * $productModel->product_number;
            $productModel->supplier_id = $productItem->supplier_id;
            $productModel->supplier_product_id = $productItem->supplier_product_id;
            $productModel->num = $productItem->num;
            $productModel->spec = $productItem->spec;
            $productModel->unit = $productItem->unit;
            $productModel->material_type = $productItem->material_type;
            if(!$productModel->save()) {
                return array("state" => 0, "message" => $productModel->getFirstErrors());
            }
            $meterialType[] = $productModel->material_type;
            $supplier[] = $productModel->supplier_id;
        }
        $date = date("m", strtotime($this->create_time));
        $areaId = 0;
        $result = Flow::confirmFollowAdminId(Flow::TYPE_ORDER_FINANCE, $this, $this->total_amount, $date, $areaId, $supplier, $meterialType);
        if(!$result["state"]) {
            return $result;
        }
        if(!$this->save()){
            return array("state" => 0, "message" => $this->getFirstErrors());
        }
        $businessModel = new BusinessAll();
        $business = $businessModel->addBusiness($this, Flow::TYPE_ORDER_FINANCE);
        if(!$business["state"]) {
            return ["error" => 0, "message" => $business["message"]];
        }
        if($this->status == Flow::STATUS_FINISH){
            $result = $this->Finish();
            if(!$result["state"]) {
                return $result;
            }
        }
        AdminLog::addLog("oprocurement_add", "采购计划下定财务记录添加成功：".$this->id);
        return array("state" => 1);
    }
   
    
    /**
     * 完成操作
     */
    public function Finish()
    {
        $buyingItem = WarehouseBuying::findOne($this->procurement_id);
        if($buyingItem->status == Flow::STATUS_FINISH) {
            $buying = BusinessAll::findOne(["business_id" => $this->procurement_id, "business_type" => Flow::TYPE_BUYING]);
            if($buying) {
                $buying->is_complete = 1;
                $buying->save();
            }
            $orderFinance = BusinessAll::findOne(["business_id" => $this->id, "business_type" => Flow::TYPE_ORDER_FINANCE]);
            if($orderFinance) {
                $orderFinance->is_complete = 1;
                $orderFinance->save();
            }
        }
//        $warehouseItem = Warehouse::findOne($this->warehouse_id);
//        $model = new DepartmentBalanceLog();
//        $result = $model->addDepartmentBalanceLog($warehouseItem->department_id, $this->id, DepartmentBalanceLog::BUSINESS_TYPE_ORDER, DepartmentBalanceLog::MOD_OUT, $this->total_amount, "计划下单支付");
//        if(!$result["state"]) {
//            return $result;
//        }
        return ["state" => 1];
    }
    
    /**
     * 驳回方法
     */
    public function Reject() {
        $buyingItem = WarehouseBuying::findOne($this->procurement_id);
        $buyingItem->status = Flow::STATUS_UNION_REJECT;
        $buyingItem->failCause = "订单支付联合驳回订单入库";
        if(!$buyingItem->save()) {
            $message = $buyingItem->getFirstErrors();
            return ["state" => 0, "message" => reset($message)];
        }
        $buying = BusinessAll::findOne(["business_id" => $buyingItem->id, "business_type" => Flow::TYPE_BUYING]);
        if($buying) {
            $buying->status = Flow::STATUS_UNION_REJECT;
            $buying->is_complete = 1;
            $buying->save();
        }
        return ["state" => 1];
    }
    
    /**
     * 验证计划下订支付已经支付
     * @param type $procurementId 下订单号
     * @return boolean true 已完成支付 false 未完成
     */
    public static function checkPeocurementIsFinish($procurementId)
    {
        $item = OrderProcurement::findOne(["procurement_id" => $procurementId]);
        if($item && $item->pay_state != self::PAY_STATE_NO) {
            return true;
        }
        return false;
    }
}
