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
use common\models\OrderMaterialReturnProduct;
use common\models\AdminLog;
use libs\common\Flow;
use common\models\DepartmentBalance;
use common\models\DepartmentBalanceLog;
use common\models\BusinessAll;

/**
 * This is the model class for table "OrderMaterialReturn".
 *
 * @property integer $id
 * @property string $name
 * @property integer $procurement_id
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
 * @property string $remark
 * @property string $operation_time
 * @property string $verify_time
 * @property string $approval_time
 * @property integer $department_id
 * @property integer $pay_state
 * @property string $pay_deposit_time
 * @property string $pay_all_time
 * @prpperty integer $timing_type
 */
class OrderMaterialReturn extends namespace\base\OrderMaterialReturn
{    
    public $noReceipt;
    public $receipt;
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
        self::PAY_STATE_NO => "未收款",
        self::PAY_STATE_DEPOSIT => "部分收款",
        self::PAY_STATUS_ALL => "全部收款",
    ];
    
    /**
     * 展示收款状态
     */
    public function showPayState() {
        return isset(self::$_payStateAll[$this->pay_state]) ? self::$_payStateAll[$this->pay_state] : "未知" . $this->pay_state;
    }
    
    /**
     * 展示收款连接
     */
    public function showPayUrl() {
        if($this->status != Flow::STATUS_FINISH || $this->pay_state == self::PAY_STATUS_ALL) {
            return [];
        }
        if($this->payment == WarehousePlanning::PAYMENT_LATER && $this->pay_state == self::PAY_STATE_NO) {
            return ["payUrl" => '<a class="button blue-button" get-update-reload="'.Url::to(['omaterialreturn/payall', "id" => $this->id]).'">确定已收款</a> '];
        }
        return [];
    }
    
    /**
     * 添加退货收款记录
     * @param type $procurementItem 退货记录
     * @param type $procurementProduct 退货物料记录
     * @return type
     */
    public function addOrderMaterial($procurementItem, $procurementProduct) 
    {
        $this->name = $procurementItem->name;
        $this->procurement_id = $procurementItem->id;
        $this->sn = Utils::generateSn(Flow::TYPE_ORDER_MATERIAL);
        $this->order_sn = isset($procurementItem->order_sn) ? $procurementItem->order_sn : $procurementItem->sn;
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
        $this->payment = 3;
        $this->deposit = 0;
        $this->payment_term = $procurementItem->payment_term;
        $this->config_id = 0;
        $this->pay_state = self::PAY_STATE_NO;
        $this->remark = $procurementItem->common;
        if(!$this->validate()) {
            return array("state" => 0, "message" => $this->getFirstErrors());
        }
        $this->save();
        $meterialType = $supplier = array();
        foreach ($procurementProduct as $productItem) {
            $productModel = new OrderMaterialReturnProduct();
            $productModel->product_id = $productItem->product_id;
            $productModel->order_procurement_id = $this->id;
            $productModel->name = $productItem->name;
            $productModel->price = $productItem->price;
            $productModel->purchase_price = $productItem->purchase_price;
            $productModel->product_number = $productItem->buying_number;
            $productModel->total_amount = $productModel->purchase_price * $productItem->buying_number;
            $productModel->supplier_id = $productItem->supplier_id;
            $productModel->supplier_product_id = $productItem->supplier_product_id;
            $productModel->num = $productItem->num;
            $productModel->spec = $productItem->spec;
            $productModel->unit = $productItem->unit;
            $productModel->material_type = $productItem->material_type;
            $productModel->type = $productItem->type;
            if(!$productModel->save()) {
                return array("state" => 0, "message" => $productModel->getFirstErrors());
            }
            $meterialType[] = $productModel->material_type;
            $supplier[] = $productModel->supplier_id;
        }
        $date = date("m", strtotime($this->create_time));
        $areaId = 0;
        $result = Flow::confirmFollowAdminId(Flow::TYPE_ORDER_MATERIAL, $this, $this->total_amount, $date, $areaId, $supplier, $meterialType);
        if(!$result["state"]) {
            return $result;
        }
        if(!$this->save()){
            return array("state" => 0, "message" => $this->getFirstErrors());
        }
        $businessModel = new BusinessAll();
        $business = $businessModel->addBusiness($this, Flow::TYPE_ORDER_MATERIAL);
        if(!$business["state"]) {
            return ["error" => 0, "message" => $business["message"]];
        }
        if($this->status == Flow::STATUS_FINISH){
            $result = $this->Finish();
            if(!$result["state"]) {
                return $result;
            }
        }
        AdminLog::addLog("oprocurement_add", "退货收款财务记录添加成功：".$this->id);
        return array("state" => 1);
    }
   
    
    /**
     * 完成操作
     */
    public function Finish()
    {
        $wMaterial = BusinessAll::findOne(["business_id" => $this->procurement_id, "business_type" => Flow::TYPE_MATERIALRETURN]);
        if($wMaterial) {
            $wMaterial->is_complete = 1;
            $wMaterial->save();
        }
        $orderMaterial = BusinessAll::findOne(["business_id" => $this->id, "business_type" => Flow::TYPE_ORDER_MATERIAL]);
        if($orderMaterial) {
            $orderMaterial->is_complete = 1;
            $orderMaterial->save();
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
        $materialItem = WarehouseMaterialReturn::findOne($this->procurement_id);
        $materialItem->status = Flow::STATUS_UNION_REJECT;
        $materialItem->failCause = "订单退款联合驳回仓库退货";
        if(!$materialItem->save()) {
            $message = $materialItem->getFirstErrors();
            return ["state" => 0, "message" => reset($message)];
        }
        $buying = BusinessAll::findOne(["business_id" => $materialItem->id, "business_type" => Flow::TYPE_MATERIALRETURN]);
        if($buying) {
            $buying->status = Flow::STATUS_UNION_REJECT;
            $buying->is_complete = 1;
            $buying->save();
        }
        return ["state" => 1];
    }
}
