<?php
namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\BaseActiveRecord;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use libs\Utils;

use common\models\WarehousePlanning;
use common\models\WarehouseBuyingProduct;
use common\models\AdminLog;
use common\models\OrderProcurement;
use libs\common\Flow;
use common\models\BusinessAll;
use common\models\Warehouse;
use common\models\CommonRemark;
use common\models\ProductStock;
use common\models\WarehouseGateway;
use common\models\OrderMaterialReturn;
use common\models\OrderMaterialReturnProduct;

/**
 * This is the model class for table "WarehouseBuying".
 *
 * @property integer $id
 * @property string $name
 * @property string $sn
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
 * @property integer $type
 * @property integer $timing_type
 */
class WarehouseBuying extends namespace\base\WarehouseBuying
{
    /**
     * 设置默认值
     * @author dean feng851028@163.com
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    BaseActiveRecord::EVENT_BEFORE_INSERT => 'create_time',
                ],
                'value' => new Expression('NOW()'),
            ]
        ];
    }
    
    /**
     * 获取记录支付方式
     * @author dean feng851028@163.com
     */
    public function showPayment() 
    {
        return isset(WarehousePlanning::$_paymentAll[$this->payment]) ? WarehousePlanning::$_paymentAll[$this->payment] : "未知".$this->payment;
    }
    
    
    /**
     * 添加新的入库记录
     * @param type $procurementItem  采购下单记录
     * @param type $procurementProduct 采购下单物料记录
     * @param type $post POST提交数据
     * @return type
     * @author dean feng851028@163.com
     */
    public function addBuying($procurementItem, $procurementProduct, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $warehouseItem = Warehouse::findOne($procurementItem->warehouse_id);
            if(!$warehouseItem) {
                $transaction->rollBack();
                return array("state" => 0, "message" => "未知仓库");
            }
            $this->name = $procurementItem->name;
            $this->sn = Utils::generateSn(Flow::TYPE_BUYING);
            $this->order_sn = $procurementItem->order_sn;
            $this->warehouse_id = $procurementItem->warehouse_id;
            $this->department_id = $warehouseItem->department_id;
            $this->supplier_id = $procurementItem->supplier_id;
            $this->planning_date = $procurementItem->planning_date;
            $this->payment = $procurementItem->payment;
            $this->deposit = $procurementItem->deposit;
            $this->total_amount = $procurementItem->total_amount;
            $this->payment_term = $procurementItem->payment_term;
            $this->type = $procurementItem->type;
            $this->create_admin_id = Yii::$app->user->getId();
            $this->verify_admin_id = 0;
            $this->approval_admin_id = 0;
            $this->operation_admin_id = 0;
            $this->status = Flow::STATUS_APPLY_VERIFY;
            $this->create_time = date("Y-m-d H:i:s");
            $this->config_id = 0;
            if(!$this->validate()) {
                $transaction->rollBack();
                return array("state" => 0, "message" => $this->getFirstErrors());
            }
            $this->save();
            $totalAmount = 0;
            $meterialType = $supplier = array();
            foreach ($procurementProduct as $productItem) {
                if(!is_numeric($post["buyingNum"][$productItem->id])) {
                    $transaction->rollBack();
                    return array("state" => 0, "message" => "实际采购数量不能为空");
                }
                $productModel = new WarehouseBuyingProduct();
                $productModel->product_id = $productItem->product_id;
                $productModel->buying_id = $this->id;
                $productModel->name = $productItem->name;
                $productModel->price = $productItem->price;
                $productModel->purchase_price = $productItem->purchase_price;
                $productModel->product_number = $productItem->product_number;
                $productModel->buying_number = $post["buyingNum"][$productItem->id];
                $productModel->total_amount = $productModel->purchase_price * $productModel->buying_number;
                $productModel->supplier_id = $productItem->supplier_id;
                $productModel->supplier_product_id = $productItem->supplier_product_id;
                $productModel->num = $productItem->num;
                $productModel->spec = $productItem->spec;
                $productModel->unit = $productItem->unit;
                $productModel->material_type = $productItem->material_type;
                $productModel->warehouse_id = $this->warehouse_id;
                if(!$productModel->save()) {
                    $transaction->rollBack();
                    return array("state" => 0, "message" => $productModel->getFirstErrors());
                }
                $meterialType[] = $productModel->material_type;
                $supplier[] = $productModel->supplier_id;
                $totalAmount += $productModel->total_amount;
            }
            $date = date("m", strtotime($this->payment_term));
            $areaId = 0;
            $result = Flow::confirmFollowAdminId(Flow::TYPE_BUYING, $this, $totalAmount, $date, $areaId, $supplier, $meterialType);
            if(!$result["state"]) {
                $transaction->rollBack();
                return $result;
            }
            $businessModel = new BusinessAll();
            $business = $businessModel->addBusiness($this, Flow::TYPE_BUYING);
            if(!$business["state"]) {
                $transaction->rollBack();
                return $business;
            }
            $this->total_amount = $totalAmount;
            if(!$this->save()){
                $transaction->rollBack();
            	return array("state" => 0, "message" => $this->getFirstErrors());
            }
            $procurementItem->status = Flow::STATUS_FINISH;
            $procurementItem->update();
            BusinessRemind::disposeRemind($procurementItem->id, Flow::showTypeUrl(Flow::TYPE_ORDER), Flow::STATUS_FINISH);
            $remarkResult = CommonRemark::addCommonRemark($procurementItem->id, Flow::TYPE_ORDER, $post["remark"], CommonRemark::TYPE_OPERATOR);
            if(!$remarkResult["state"]) {
                $transaction->rollBack();
                return $remarkResult;
            }
            AdminLog::addLog("wbuying_add", "物料采购下定入库申请成功：".$this->id);
            $transaction->commit();
            return array("state" => 1);
        } catch (Exception $exc) {
            $transaction->rollBack();
            return array("state" => 0, "message" => $exc->getTraceAsString());
        }
    }
    
    /**
     * 完成方法
     */
    public function Finish() {
        $buyingProduct = WarehouseBuyingProduct::findAll(["buying_id" => $this->id]);
        foreach ($buyingProduct as $productItem) {
            if($this->type == WarehousePlanning::TYPE_EXCEPTION) {
                $stockItem = false;
            } else {
                $productModel = Product::findOne($productItem->product_id);
                if($productModel->is_batches) {
                    $stockItem = false;
                } else{
                    $stockItem = ProductStock::findOne(["product_id" => $productItem->product_id, 'warehouse_id' => $productItem->warehouse_id, 'type' => $this->type]);
                }
            }
            if($stockItem) {
                $result = WarehouseGateway::addWarehouseGateway($productItem->warehouse_id, $productItem->product_id, WarehouseGateway::TYPE_IN, $stockItem->number, $productItem->buying_number, $this->id, WarehouseGateway::GATEWAY_TYPE_BUYING, $this->type, $stockItem->batches);
                if(!$result["state"]) {
                    return $result;
                }
                $stockItem->number = $stockItem->number + $productItem->buying_number;
                $stockItem->update();
                continue;
            }
            $model = new ProductStock();
            $model->batches = $this->sn;
            $model->product_id = $this->type == WarehousePlanning::TYPE_EXCEPTION ? $productItem->id : $productItem->product_id;
            $model->number = $productItem->buying_number;
            $model->warehouse_id = $productItem->warehouse_id;
            $model->supplier_id = $productItem->supplier_id;
            $model->type = $this->type;
            if(!$model->save()) {
                return array("state" => 0, "message" => $model->getFirstErrors());
            }
            $result = WarehouseGateway::addWarehouseGateway($productItem->warehouse_id, $productItem->product_id, WarehouseGateway::TYPE_IN, 0, $productItem->buying_number, $this->id, WarehouseGateway::GATEWAY_TYPE_BUYING, $this->type, $model->batches);
            if(!$result["state"]) {
                return $result;
            }
        }
        return array("state" => 1);
    }
    
    /**
     * 驳回方法
     */
    public function Reject() {
        $orderProcurement = OrderProcurement::findOne(["procurement_id" => $this->id]);
        if(!$orderProcurement) {
            return ["state" => 0, "message" => "网络异常"];
        }
        if(in_array($orderProcurement->status, [Flow::STATUS_APPLY_VERIFY, Flow::STATUS_APPLY_APPROVAL, Flow::STATUS_APPLY_FINISH])) {
            $orderProcurement->timing_type = Flow::TIMING_TYPE_UNION_REJECT;
            if(!$orderProcurement->save()) {
                $message = $orderProcurement->getFirstErrors();
                return ["state" => 0, "message" => reset($message)];
            }
            return ["state" => 1];
        }
        $productAll = WarehouseBuyingProduct::findAll(["buying_id" => $this->id]);
        $materialReturnResult = $this->addMaterialReturn($productAll, 2);
        if(!$materialReturnResult["state"]) {
            return $materialReturnResult;
        }
        return ["state" => 1];
    }
    
    /**
     * 添加入库退货退款记录
     * @param type $materialReturn 退货列表
     * @param type $returnType 退货类型 1：部分退货 2：全部退货
     * @return type
     */
    public function addMaterialReturn($materialReturn, $returnType = 1) {
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
            $productModel->product_number = $returnType == 1 ? ($productItem->product_number - $productItem->buying_number) : $productItem->buying_number;
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
        AdminLog::addLog("oprocurement_add", "退货退款财务记录添加成功：".$this->id);
        return ["state" => 1];
    }
}
