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

use common\models\ProductStock;
use common\models\WarehouseCheckoutProduct;
use common\models\FlowConfig;
use common\models\AdminLog;
use libs\common\Flow;
use common\models\BusinessAll;
use common\models\Warehouse;
use common\models\WarehousePlanning;
use common\models\WarehouseBuyingProduct;
use common\models\Product;
/**
 * This is the model class for table "WarehouseCheckout".
 *
 * @property integer $id
 * @property string $name
 * @property string $sn
 * @property integer $warehouse_id
 * @property integer $department_id
 * @property integer $receive_warehouse_id
 * @property double $total_amount
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
 * @property string $remark
 * @property double $total_cost
 * @property integer $is_buckle
 * @property integer $timing_type
 */
class WarehouseCheckout extends namespace\base\WarehouseCheckout
{
    /**
     * 添加新的出库申请
     * @param array $post 表单提交数据
     */
    public function addCheckout($post)
    {
        if(!isset($post["stockId"]) || count($post["stockId"]) == 0) {
            return array("state" => 0, "message" => "请选择出库商品");
        }
        $transaction = Yii::$app->db->beginTransaction();
        try{
            $this->attributes = $post["WarehouseCheckout"];
            $warehouseItem = Warehouse::findOne($this->warehouse_id);
            if(!$warehouseItem) {
                $transaction->rollBack();
                return array("state" => 0, "message" => "入库仓库不能为空");
            }
            if($this->warehouse_id == $this->receive_warehouse_id) {
                $transaction->rollBack();
                return array("state" => 0, "message" => "出库仓库不能等于入库仓库");
            }
            $this->sn = Utils::generateSn(Flow::TYPE_CHECKOUT);
            $this->department_id = $warehouseItem->department_id;
            $this->total_amount = 0;
            $this->total_cost = 0;
            $this->create_admin_id = Yii::$app->user->getId();
            $this->create_time = date("Y-m-d H:i:s");
            $this->operation_admin_id = 0;
            $this->operation_time = date("Y-m-d H:i:s");
            $this->status = Flow::STATUS_APPLY_VERIFY;
            $this->config_id = 0;
            if(!$this->validate()) {
                $transaction->rollBack();
                return array("state" => 0, "message" => $this->getFirstErrors());
            }
            $this->save();
            $num = $totalAmount = $totalCost = 0;
            $meterialType = $supplier = array();
            foreach ($post["stockId"] as $key => $stockId) {
                if(!$stockId){
                    continue;
                }
                if(!isset($post["goodsNum"][$key])) {
                    continue;
                }
                if($post["goodsNum"][$key] == 0) {
                    $transaction->rollBack();
                    return array("state" => 0, "message" => "出库物料数量必须大于0");
                }
                $stockItem = ProductStock::findOne($stockId);
                if(!$stockItem) {
                    continue;
                }
                if($stockItem->type == WarehousePlanning::TYPE_EXCEPTION) {
                    $productItem = WarehouseBuyingProduct::findOne($stockItem->product_id);
                } else {
                    $productItem = Product::findOne($stockItem->product_id);
                }
                if(!$productItem) {
                    continue;
                }
                if($stockItem->number < $post["goodsNum"][$key]) {
                    $transaction->rollBack();
                    return array("state" => 0, "message" => "商品：".$productItem->name."的库存小于出库数量");
                }
                
                $isCheck = CheckFlow::productIsCheckFlow($stockId);
                if($isCheck) {
                    $transaction->rollBack();
                    return array("state" => 0, "message" => $productItem->name."处于盘点中，无法申请出库");
                }
                $transferProduct = new WarehouseCheckoutProduct();
                $transferProduct->product_id = $stockItem->product_id;
                $transferProduct->checkout_id = $this->id;
                $transferProduct->name = $productItem->name;
                $transferProduct->price = $stockItem->type == WarehousePlanning::TYPE_EXCEPTION ? $productItem->price : $productItem->purchase_price;
                $transferProduct->purchase_price = $stockItem->purchase_price;
                $transferProduct->sale_price = $stockItem->type == WarehousePlanning::TYPE_EXCEPTION ? $productItem->purchase_price : $productItem->sale_price;
                $transferProduct->product_number = $stockItem->number;
                $transferProduct->buying_number = $post["goodsNum"][$key];
                $transferProduct->total_amount = $transferProduct->purchase_price * $transferProduct->buying_number;
                $transferProduct->supplier_id = $productItem->supplier_id;
                $transferProduct->supplier_product_id = $productItem->supplier_product_id;
                $transferProduct->num = $stockItem->type == WarehousePlanning::TYPE_EXCEPTION ? $productItem->num : $productItem->barcode;
                $transferProduct->spec = $productItem->spec;
                $transferProduct->unit = $productItem->unit;
                $transferProduct->material_type = $stockItem->type == WarehousePlanning::TYPE_EXCEPTION ? $productItem->material_type : $productItem->product_category_id;
                $transferProduct->warehouse_id = $stockItem->warehouse_id;
                $transferProduct->status = 1;
                $transferProduct->type = $stockItem->type;
                $transferProduct->pstock_id = $stockId;
                $transferProduct->batches = $stockItem->batches;
                if(!$transferProduct->validate()) {
                    $transaction->rollBack();
                    return array("state" => 0, "message" => $transferProduct->getFirstErrors());
                }
                $transferProduct->save();
                $num++;
                $totalAmount += $transferProduct->total_amount;
                $totalCost += $transferProduct->purchase_price * $transferProduct->buying_number;
                $meterialType[] = $transferProduct->material_type;
                $supplier[] = $transferProduct->supplier_id;
            }
            if($totalAmount > 99999999) {
                $transaction->rollBack();
                return array("state" => 0, "message" => "出库总金额不能超过限制金额一亿");
            }
            if($num == 0) {
                $transaction->rollBack();
                return array("state" => 0, "message" => "请选择出库商品");
            }
              //根据条件去判断符合某个流程
            $date = date("m", strtotime($this->create_time));
            $areaId = 0;
            $result = Flow::confirmFollowAdminId(Flow::TYPE_CHECKOUT, $this, $totalAmount, $date, $areaId, $supplier, $meterialType);
            if(!$result["state"]) {
                $transaction->rollBack();
                return $result;
            }
            if($this->is_buckle) {
                $buckleResult = Flow::buckleStock(Flow::TYPE_CHECKOUT, $this);
                if(!$buckleResult["state"]) {
                    $transaction->rollBack();
                    return $result;
                }
            }
            $businessModel = new BusinessAll();
            $business = $businessModel->addBusiness($this, Flow::TYPE_CHECKOUT);
            if(!$business["state"]) {
                $transaction->rollBack();
                return ["error" => 1, "message" => $business["message"]];
            }
            //把金额的信息再次保存
            $this->total_amount = $totalAmount;
            $this->total_cost = $totalCost;
            if(!$this->save()){
                $transaction->rollBack();
                return array("state" => 0, "message" => $this->getFirstErrors());
            }
            if($this->status == Flow::STATUS_FINISH){
                $result = $this->Finish();
                if(!$result["state"]) {
                    $transaction->rollBack();
                    return $result;
                }
            }
            AdminLog::addLog("wcheckout_add", "物料出库申请成功：".$this->id);
            $transaction->commit();
            return array("state" => 1);
        } catch (Exception $ex) {
            $transaction->rollBack();
            return array("state" => 0, "message" => $ex->getTraceAsString());
        }
    }
 
   
    /**
     * 出库完成操作
     */
    public function Finish()
    {
        $transferProduct = WarehouseCheckoutProduct::findAll(["checkout_id" => $this->id]);
        foreach ($transferProduct as $productVal) {
            $stockOutItem = ProductStock::findOne($productVal->pstock_id);
            if(!$this->is_buckle) {
                if(!$stockOutItem) {
                    continue;
                }
                if($stockOutItem->number < $productVal->buying_number) {
                    return ["state" => 0, "message" => "当前出库仓库的商品：".$productVal->name."的库存不足出库数量"];
                }
                $result = WarehouseGateway::addWarehouseGateway($this->warehouse_id, $productVal->product_id, WarehouseGateway::TYPE_OUT, $stockOutItem->number, $productVal->buying_number, $this->id, WarehouseGateway::GATEWAY_TYPE_CHECKOUT, $productVal->type, $stockOutItem->batches);
                if(!$result["state"]) {
                    return $result;
                }
                $stockOutItem->number = $stockOutItem->number - $productVal->buying_number;
                $stockOutItem->save();
            }
            if($productVal->type == WarehousePlanning::TYPE_EXCEPTION) {
                $stockInItem = false;
            } else {
                $productModel = Product::findOne($productVal->product_id);
                if($productModel->is_batches) {
                    $stockInItem = false;
                } else {
                    $stockInItem = ProductStock::findOne(["warehouse_id" => $this->receive_warehouse_id, "product_id" => $productVal->product_id, "type" => $productVal->type]);
                }
            }
            if($stockInItem) {
                $result = WarehouseGateway::addWarehouseGateway($this->receive_warehouse_id, $productVal->product_id, WarehouseGateway::TYPE_IN, $stockInItem->number, $productVal->buying_number, $this->id, WarehouseGateway::GATEWAY_TYPE_CHECKOUT, $productVal->type, $stockInItem->batches);
                if(!$result["state"]) {
                    return $result;
                }
                $stockInItem->number = $stockInItem->number + $productVal->buying_number;
                $stockInItem->save();
            } else {
                $result = WarehouseGateway::addWarehouseGateway($this->receive_warehouse_id, $productVal->product_id, WarehouseGateway::TYPE_IN, 0, $productVal->buying_number, $this->id, WarehouseGateway::GATEWAY_TYPE_CHECKOUT, $productVal->type, $this->sn);
                if(!$result["state"]) {
                    return $result;
                }
                $stockInItem = new ProductStock();
                $stockInItem->batches = $this->sn;
                $stockInItem->product_id = $productVal->product_id;
                $stockInItem->number = $productVal->buying_number;
                $stockInItem->warehouse_id = $this->receive_warehouse_id;
                $stockInItem->supplier_id = $productVal->supplier_id;
                $stockInItem->type = $productVal->type;
                $stockInItem->purchase_price = $stockOutItem->purchase_price;
                if(!$stockInItem->validate()) {
                    return ["state" => 0, "message" => $stockInItem->getFirstErrors()];
                }
                $stockInItem->save();
            }
        }
        AdminLog::addLog("wcheckout_finish", "物料出库申请成功完成：".$this->id);
        return ["state" => 1, "message" => "操作成功"];
    }
}
