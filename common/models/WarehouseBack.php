<?php

namespace common\models;

use Yii;
use Exception;
use libs\common\Flow;

use common\models\ProductStock;
use common\models\WarehouseBackProduct;
use common\models\AdminLog;
use common\models\Warehouse;
use common\models\BusinessAll;
use common\models\WarehousePlanning;
use common\models\WarehouseBuyingProduct;
use common\models\Product;
use libs\Utils;

/**
 * This is the model class for table "WarehouseBack".
 *
 * @property integer $id
 * @property string $name
 * @property string $sn
 * @property integer $warehouse_id
 * @property integer $receive_warehouse_id
 * @property double $total_amount
 * @property integer $create_admin_id
 * @property string $create_time
 * @property integer $verify_admin_id
 * @property string $verify_time
 * @property integer $approval_admin_id
 * @property string $approval_time
 * @property integer $operation_admin_id
 * @property string $operation_time
 * @property integer $status
 * @property integer $config_id
 * @property string $failCause
 * @property string $remark
 * @property integer $is_buckle
 * @property integer $timing_type
 */
class WarehouseBack extends namespace\base\WarehouseBack
{
    /**
     * 添加新的退仓申请
     * @param array $post 表单提交数据
     */
    public function addBack($post)
    {
        if(!isset($post["stockId"]) || count($post["stockId"]) == 0) {
            return array("state" => 0, "message" => "请选择退仓商品");
        }
        $transaction = Yii::$app->db->beginTransaction();
        try{
            $this->attributes = $post["WarehouseBack"];
            if($this->warehouse_id == $this->receive_warehouse_id) {
                $transaction->rollBack();
                return array("state" => 0, "message" => "退出仓库不能等于退入仓库");
            }
            $this->sn = Utils::generateSn(Flow::TYPE_BACK);
            $this->total_amount = 0;
            $this->create_admin_id = Yii::$app->user->getId();
            $this->create_time = date("Y-m-d H:i:s");
            $this->status = Flow::STATUS_APPLY_VERIFY;
            $this->config_id = 0;
            if(!$this->validate()) {
                $transaction->rollBack();
                return array("state" => 0, "message" => $this->getFirstErrors());
            }
            $this->save();
            $warehouseItem = Warehouse::findOne($this->warehouse_id);
            $num = $totalAmount = 0;
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
                    return array("state" => 0, "message" => "退仓物料数量必须大于0");
                }
                $stockItem = ProductStock::findOne($stockId);
                if(!$stockItem) {
                    continue;
                }
                if($warehouseItem->is_sale == Warehouse::SALE_YES && strstr($stockItem->batches, "Ruk")) {
                    $transaction->rollBack();
                    return array("state" => 0, "message" => "销售仓库的入库物料无法退仓");
                }
                if($stockItem->type == WarehousePlanning::TYPE_EXCEPTION) {
                    $productItem = WarehouseBuyingProduct::findOne($stockItem->product_id);
                } else {
                    $productItem = Product::findOne($stockItem->product_id);
                }
                if(!$productItem) {
                    continue;
                }
                if($post["goodsNum"][$key] > $stockItem->number){
                    $transaction->rollBack();
                    return array("state" => 0, "message" => $productItem->name."的退仓数量不能大于库存");
                }
                $isCheck = CheckFlow::productIsCheckFlow($stockId);
                if($isCheck) {
                    $transaction->rollBack();
                    return array("state" => 0, "message" => $productItem->name."处于盘点中，无法申请退仓");
                }
                $backProduct = new WarehouseBackProduct();
                $backProduct->product_id = $stockItem->product_id;
                $backProduct->back_id = $this->id;
                $backProduct->name = $productItem->name;
                $backProduct->price = $stockItem->type == WarehousePlanning::TYPE_EXCEPTION ? $productItem->price : $productItem->purchase_price;
                $backProduct->purchase_price = $stockItem->purchase_price;
                $backProduct->sale_price = $stockItem->type == WarehousePlanning::TYPE_EXCEPTION ? $productItem->purchase_price : $productItem->sale_price;
                $backProduct->product_number = $stockItem->number;
                $backProduct->buying_number = $post["goodsNum"][$key];
                $backProduct->total_amount = $backProduct->purchase_price * $backProduct->buying_number;
                $backProduct->supplier_id = $productItem->supplier_id;
                $backProduct->supplier_product_id = $productItem->supplier_product_id;
                $backProduct->num = $stockItem->type == WarehousePlanning::TYPE_EXCEPTION ? $productItem->num : $productItem->barcode;
                $backProduct->spec = $productItem->spec;
                $backProduct->unit = $productItem->unit;
                $backProduct->material_type = $stockItem->type == WarehousePlanning::TYPE_EXCEPTION ? $productItem->material_type : $productItem->product_category_id;
                $backProduct->warehouse_id = $stockItem->warehouse_id;
                $backProduct->status = 1;
                $backProduct->type = $stockItem->type;
                $backProduct->pstock_id = $stockId;
                $backProduct->batches = $stockItem->batches;
                if(!$backProduct->validate()) {
                    $transaction->rollBack();
                    return array("state" => 0, "message" => $backProduct->getFirstErrors());
                }
                $backProduct->save();
                $num++;
                $totalAmount += $backProduct->total_amount;
                $meterialType[] = $backProduct->material_type;
                $supplier[] = $backProduct->supplier_id;
            }
            
            if($totalAmount > 99999999) {
                $transaction->rollBack();
                return array("state" => 0, "message" => "退仓总金额不能超过限制金额一亿");
            }
            if($num == 0) {
                $transaction->rollBack();
                return array("state" => 0, "message" => "请选择退仓商品");
            }
            $result = Flow::confirmFollowAdminId(Flow::TYPE_BACK, $this, $totalAmount, time(), 0, $supplier, $meterialType);
            if(!$result["state"]) {
                $transaction->rollBack();
                return $result;
            }
            if($this->is_buckle) {
                $buckleResult = Flow::buckleStock(Flow::TYPE_BACK, $this);
                if(!$buckleResult["state"]) {
                    $transaction->rollBack();
                    return $result;
                }
            }
            $businessModel = new BusinessAll();
            $business = $businessModel->addBusiness($this, Flow::TYPE_BACK);
            if(!$business["state"]) {
                $transaction->rollBack();
                return ["error" => 1, "message" => $business["message"]];
            }
            $this->total_amount = $totalAmount;
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
            AdminLog::addLog("wback_add", "物料退仓申请成功：".$this->id);
            $transaction->commit();
            return array("state" => 1);
        } catch (Exception $ex) {
            $transaction->rollBack();
            return array("state" => 0, "message" => $ex->getTraceAsString());
        }
    }
   
    /**
     * 调仓完成操作
     */
    public function Finish()
    {
        $backProduct = WarehouseBackProduct::findAll(["back_id" => $this->id]);
        foreach ($backProduct as $productVal) {
            $stockOutItem = ProductStock::findOne($productVal->pstock_id);
            if(!$this->is_buckle) {
                if(!$stockOutItem) {
                    continue;
                }
                if($stockOutItem->number < $productVal->buying_number) {
                    return ["state" => 0, "message" => "当前退出仓库的商品：".$productVal->name."的库存不足退出数量"];
                }
                $result = WarehouseGateway::addWarehouseGateway($this->warehouse_id, $productVal->product_id, WarehouseGateway::TYPE_OUT, $stockOutItem->number, $productVal->buying_number, $this->id, WarehouseGateway::GATEWAY_TYPE_BACK, $productVal->type, $stockOutItem->batches);
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
                $result = WarehouseGateway::addWarehouseGateway($this->receive_warehouse_id, $productVal->product_id, WarehouseGateway::TYPE_IN, $stockInItem->number, $productVal->buying_number, $this->id, WarehouseGateway::GATEWAY_TYPE_BACK, $productVal->type, $stockInItem->batches);
                if(!$result["state"]) {
                    return $result;
                }
                $stockInItem->number = $stockInItem->number + $productVal->buying_number;
                $stockInItem->save();
            } else {
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
                $result = WarehouseGateway::addWarehouseGateway($this->receive_warehouse_id, $productVal->product_id, WarehouseGateway::TYPE_IN, 0, $productVal->buying_number, $this->id, WarehouseGateway::GATEWAY_TYPE_BACK, $productVal->type, $stockInItem->batches);
                if(!$result["state"]) {
                    return $result;
                }
            }
        }
        AdminLog::addLog("wback_finish", "物料退仓申请成功完成：".$this->id);
        return ["state" => 1, "message" => "操作成功"];
    }
}

