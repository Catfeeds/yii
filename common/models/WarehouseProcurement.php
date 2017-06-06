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
use common\models\WarehouseProcurementProduct;
use common\models\AdminLog;
use common\models\OrderProcurement;
use common\models\Warehouse;
use libs\common\Flow;
use common\models\BusinessAll;
use common\models\WarehouseBuying;
use common\models\WarehouseBuyingProduct;

/**
 * This is the model class for table "WarehouseProcurement".
 *
 * @property integer $id
 * @property string $name
 * @property string $procurement_planning_id
 * @property string $sn
 * @property string $order_sn
 * @property integer $warehouse_id
 * @property integer $department_id
 * @property integer $supplier_id
 * @property string $planning_date
 * @property integer $payment
 * @property double $deposit
 * @property double $total_amount
 * @property string $payment_term
 * @property integer $create_admin_id
 * @property integer $verify_admin_id
 * @property string $verify_time
 * @property integer $approval_admin_id
 * @property string $approval_time
 * @property integer $operation_admin_id
 * @property string $operation_time
 * @property integer $status
 * @property string $create_time
 * @property integer $config_id
 * @property string $failCause
 * @property integer $type
 * @property integer $timing_type
 */
class WarehouseProcurement extends namespace\base\WarehouseProcurement
{
    
    public $buckle_amount = 0;
    /**
     * 获取记录支付方式
     */
    public function showPayment() 
    {
        return isset(WarehousePlanning::$_paymentAll[$this->payment]) ? WarehousePlanning::$_paymentAll[$this->payment] : "未知".$this->payment;
    }
    
    /**
     * 完成方法
     */
    public function Finish() {
        $planning = BusinessAll::findOne(["business_id" => $this->procurement_planning_id, "business_type" => [Flow::TYPE_PLANNING, Flow::TYPE_PLANNING_ROUTINE, Flow::TYPE_PLANNING_EXCEPTION]]);
        if($planning) {
            $planning->is_complete = 1;
            $planning->save();
        }
        $procurement = BusinessAll::findOne(["business_id" => $this->id, "business_type" => Flow::TYPE_ORDER]);
        if($procurement) {
            $procurement->is_complete = 1;
            $procurement->save();
        }
        $model = new WarehouseBuying();
        $model->name = $this->name;
        $model->sn = Utils::generateSn(Flow::TYPE_BUYING);
        $model->order_sn = $this->order_sn;
        $model->warehouse_id = $this->warehouse_id;
        $model->department_id = $this->department_id;
        $model->supplier_id = $this->supplier_id;
        $model->planning_date = $this->planning_date;
        $model->payment = $this->payment;
        $model->deposit = $this->deposit;
        $model->total_amount = $this->total_amount;
        $model->payment_term = $this->payment_term;
        $model->type = $this->type;
        $model->create_admin_id = Yii::$app->user->getId();
        $model->verify_admin_id = 0;
        $model->approval_admin_id = 0;
        $model->operation_admin_id = 0;
        $model->status = Flow::STATUS_APPLY_VERIFY;
        $model->create_time = date("Y-m-d H:i:s");
        $model->config_id = 0;
        if(!$model->validate()) {
            return array("state" => 0, "message" => $model->getFirstErrors());
        }
        $model->save();
        $totalAmount = 0;
        $meterialType = $supplier = array();
        $procurementProduct = WarehouseProcurementProduct::findAll(["procurement_id" => $this->id]);
        foreach ($procurementProduct as $productItem) {
            $productModel = new WarehouseBuyingProduct();
            $productModel->product_id = $productItem->product_id;
            $productModel->buying_id = $model->id;
            $productModel->name = $productItem->name;
            $productModel->price = $productItem->price;
            $productModel->purchase_price = $productItem->purchase_price;
            $productModel->product_number = $productItem->product_number;
            $productModel->buying_number = $productItem->product_number;
            $productModel->total_amount = $productModel->purchase_price * $productModel->buying_number;
            $productModel->supplier_id = $productItem->supplier_id;
            $productModel->supplier_product_id = $productItem->supplier_product_id;
            $productModel->num = $productItem->num;
            $productModel->spec = $productItem->spec;
            $productModel->unit = $productItem->unit;
            $productModel->material_type = $productItem->material_type;
            $productModel->warehouse_id = $model->warehouse_id;
            if(!$productModel->save()) {
                return array("state" => 0, "message" => $productModel->getFirstErrors());
            }
            $meterialType[] = $productModel->material_type;
            $supplier[] = $productModel->supplier_id;
            $totalAmount += $productModel->total_amount;
        }
        $areaId = 0;
        $result = Flow::confirmFollowAdminId(Flow::TYPE_BUYING, $model, $totalAmount, time(), $areaId, $supplier, $meterialType);
        if(!$result["state"]) {
            return $result;
        }
        $businessModel = new BusinessAll();
        $business = $businessModel->addBusiness($model, Flow::TYPE_BUYING);
        if(!$business["state"]) {
            return ["error" => 1, "message" => $business["message"]];
        }
        $model->total_amount = $totalAmount;
        if(!$model->save()){
            return array("state" => 0, "message" => $model->getFirstErrors());
        }
        if($model->status == Flow::STATUS_FINISH){
            $result = $model->Finish();
            if(!$result["state"]) {
                return $result;
            }
        }
        $orderProcurementModel = new OrderProcurement();
        $addOrderResult = $orderProcurementModel->addOrderProcurement($this, $procurementProduct, $model->id);
        if(!$addOrderResult["state"]) {
            return $addOrderResult;
        }
        AdminLog::addLog("wbuying_add", "物料采购下定入库申请成功：".$this->id);
        return array("state" => 1);
    }
    
    /**
     * 驳回方法
     */
    public function Reject() {
        $planningItem = WarehousePlanning::findOne($this->procurement_planning_id);
        $planningItem->status = Flow::STATUS_UNION_REJECT;
        $planningItem->failCause = "采购下定联合驳回采购计划";
        if(!$planningItem->save()) {
            $message = $planningItem->getFirstErrors();
            return ["state" => 0, "message" => reset($message)];
        }
        $planning = BusinessAll::findOne(["business_id" => $planningItem->id, "business_type" => [Flow::TYPE_PLANNING, Flow::TYPE_PLANNING_ROUTINE, Flow::TYPE_PLANNING_EXCEPTION]]);
        if($planning) {
            $planning->status = Flow::STATUS_UNION_REJECT;
            $planning->is_complete = 1;
            $planning->save();
        }
//        $productAll = WarehouseProcurementProduct::findAll(["procurement_id" => $this->id]);
//        $materialReturnResult = $this->addMaterialReturn($productAll);
//        if(!$materialReturnResult["state"]) {
//            return $materialReturnResult;
//        }
        return ["state" => 1];
    }
    
    /**
     * 添加采购下定的退款记录
     * @param type $materialReturn 退货列表
     * @return type
     */
    public function addMaterialReturn($materialReturn) {
        $model = new OrderMaterialReturn;
        $model->name = $this->name;
        $model->procurement_id = $this->id;
        $model->sn = Utils::generateSn(Flow::TYPE_ORDER_MATERIAL);
        $model->order_sn = $this->order_sn;
        $model->warehouse_id = $this->warehouse_id;
        $model->supplier_id = $this->supplier_id;
        $model->planning_date = date("Y-m-d");
        $model->payment = 3;
        $model->deposit = 0;
        $model->total_amount = 0;
        $model->payment_term = date("Y-m-d");
        $model->create_admin_id = \Yii::$app->user->getId();
        $model->create_time = date("Y-m-d H:i:s");
        $model->verify_admin_id = 0;
        $model->approval_admin_id = 0;
        $model->operation_admin_id = 0;
        $model->status = Flow::STATUS_APPLY_VERIFY;
        $model->config_id = 0;
        $model->department_id = $this->department_id;
        $model->pay_state = 0;
        if(!$model->validate() || !$model->save()) {
            return ["state" => 0, "message" => $model->getFirstErrors()];
        }
        $meterialType = $supplier = array();
        foreach ($materialReturn as $productItem) {
            $productModel = new OrderMaterialReturnProduct();
            $productModel->order_procurement_id = $model->id;
            $productModel->product_id = $productItem->product_id;
            $productModel->name = $productItem->name;
            $productModel->price = $productItem->price;
            $productModel->purchase_price = $productItem->purchase_price;
            $productModel->product_number = $productItem->product_number;
            $productModel->total_amount = $productModel->product_number * $productModel->price;
            $productModel->supplier_id = $productItem->supplier_id;
            $productModel->supplier_product_id = $productItem->supplier_product_id;
            $productModel->num = $productItem->num;
            $productModel->spec = $productItem->spec;
            $productModel->unit = $productItem->unit;
            $productModel->material_type = $productItem->material_type;
            $productModel->status = $productItem->status;
            $productModel->type = $this->type;
            if(!$productModel->validate() || !$productModel->save()) {
                return ["state" => 0, "message" => $productModel->getFirstErrors()];
            }
            $meterialType[] = $productModel->material_type;
            $supplier[] = $productModel->supplier_id;
            $model->total_amount += $productModel->total_amount;
        }
        $areaId = 0;
        $result = Flow::confirmFollowAdminId(Flow::TYPE_ORDER_MATERIAL, $model, $model->total_amount, time(), $areaId, $supplier, $meterialType);
        if(!$result["state"]) {
            return $result;
        }
        if(!$this->save()){
            return array("state" => 0, "message" => $this->getFirstErrors());
        }
        $businessModel = new BusinessAll();
        $business = $businessModel->addBusiness($model, Flow::TYPE_ORDER_MATERIAL);
        if(!$business["state"]) {
            return ["error" => 0, "message" => $business["message"]];
        }
        if($model->status == Flow::STATUS_FINISH){
            $result = $model->Finish();
            if(!$result["state"]) {
                return $result;
            }
        }
        AdminLog::addLog("oprocurement_add", "退货收款财务记录添加成功：".$this->id);
        return ["state" => 1];
    }
}
