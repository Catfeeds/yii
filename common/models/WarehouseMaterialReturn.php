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
use common\models\WarehouseMaterialReturnProduct;
use common\models\AdminLog;
use libs\common\Flow;
use common\models\DepartmentBalanceLog;
use common\models\Warehouse;
use common\models\BusinessAll;
use common\models\WarehousePlanning;
use common\models\OrderMaterialReturn;
/**
 * This is the model class for table "WarehouseMaterialReturn".
 *
 * @property integer $id
 * @property string $name
 * @property string $sn
 * @property integer $warehouse_id
 * @property integer $supplier_id
 * @property integer $department_id
 * @property double $total_amount
 * @property integer $create_admin_id
 * @property integer $verify_admin_id
 * @property string $verify_time
 * @property integer $approval_admin_id
 * @property string $approval_time
 * @property integer $status
 * @property string $create_time
 * @property integer $operation_admin_id
 * @property string $operation_time
 * @property integer $config_id
 * @property string $failCause
 * @property string $common
 * @property integer $buying_id
 * @property string $planning_date
 * @property string $payment_term
 * @property integer $is_buckle
 * @property integer $timing_type
 */
class WarehouseMaterialReturn extends namespace\base\WarehouseMaterialReturn
{
    /**
     * 添加新的耗损申请
     * @param type $post 表单提交数据
     * @return type
     */
    public function addMateial($post)
    {
        if(!isset($post["stockId"]) || count($post["stockId"]) == 0) {
            return array("state" => 0, "message" => "请选择退货商品");
        }
        $transaction = Yii::$app->db->beginTransaction();
        try{
            $this->attributes = $post["WarehouseMaterialReturn"];
            $this->total_amount = 0;
            $warehouseItem = Warehouse::findOne($this->warehouse_id);
            $this->department_id = $warehouseItem ? $warehouseItem->department_id : 0;
            $this->sn = Utils::generateSn(Flow::TYPE_MATERIALRETURN);
            $this->create_admin_id = Yii::$app->user->getId();
            $this->create_time = date("Y-m-d H:i:s");
            $this->status = Flow::STATUS_APPLY_VERIFY;
            $this->config_id = 0;
            if(!$this->validate()) {
                $transaction->rollBack();
                return array("state" => 0, "message" => $this->getFirstErrors());
            }
            $this->save();
            $num = $totalAmount = 0;
            $meterialType = $supplier = array();
            foreach ($post["stockId"] as $key => $stockId) {
                if(!isset($post["goodsNum"][$key])) {
                    continue;
                }
                $batches = "";
                if($this->buying_id) {
                    $buyingItem = WarehouseBuying::findOne($this->buying_id);
                    $type = $buyingItem->type;
                    $productId = $key;
                    if($buyingItem->type == WarehousePlanning::TYPE_EXCEPTION) {
                        $productItem = WarehouseBuyingProduct::findOne($key);
                        $productNum = $productItem ? $productItem->product_number : 0;
                    } else {
                        $item = WarehouseBuyingProduct::findOne(["buying_id" => $this->buying_id, "product_id" => $key]);
                        $productItem = Product::findOne($stockId);
                        $productNum = $item ? $item->product_number : 0;
                    }
                } else {
                    $item = ProductStock::findOne($stockId);
                    if(!$item) {
                        continue;
                    }
                    $productId = $item->product_id;
                    $productNum = $item->number;
                    $type = $item->type;
                    $batches = $item->batches;
                    if($type == WarehousePlanning::TYPE_EXCEPTION) {
                        $productItem = WarehouseBuyingProduct::findOne($item->product_id);
                    } else {
                        $productItem = Product::findOne($item->product_id);
                    }
                }
                if(!$productItem) {
                    continue;
                }
                if(!is_numeric($post["goodsNum"][$key]) || $post["goodsNum"][$key] <= 0) {
                    $transaction->rollBack();
                    return array("state" => 0, "message" => "商品：".$productItem->name."的退货数量必须大于0");
                }
                if($productNum < $post["goodsNum"][$key]) {
                    $transaction->rollBack();
                    return array("state" => 0, "message" => "商品：".$productItem->name."的退货数量必须小于采购数量或库存");
                }
                $isCheck = CheckFlow::productIsCheckFlow($stockId);
                if($isCheck) {
                    $transaction->rollBack();
                    return array("state" => 0, "message" => $productItem->name."处于盘点中，无法申请退货");
                }
                $wastageProduct = new WarehouseMaterialReturnProduct();
                $wastageProduct->product_id = $productId;
                $wastageProduct->material_return_id = $this->id;
                $wastageProduct->name = $productItem->name;
                $wastageProduct->price = $type == WarehousePlanning::TYPE_EXCEPTION ? $productItem->price : $productItem->purchase_price;
                $wastageProduct->purchase_price = $type == WarehousePlanning::TYPE_EXCEPTION ? $productItem->price : $productItem->purchase_price;
                $wastageProduct->sale_price = $type == WarehousePlanning::TYPE_EXCEPTION ? $productItem->purchase_price : $productItem->sale_price;
                $wastageProduct->product_number = $productNum;
                $wastageProduct->buying_number = $post["goodsNum"][$key];
                $wastageProduct->total_amount = $wastageProduct->purchase_price * $wastageProduct->buying_number;
                $wastageProduct->supplier_id = $productItem->supplier_id;
                $wastageProduct->supplier_product_id = $productItem->supplier_product_id;
                $wastageProduct->num = $type->type == WarehousePlanning::TYPE_EXCEPTION ? $productItem->num : $productItem->barcode;
                $wastageProduct->spec = $productItem->spec;
                $wastageProduct->unit = $productItem->unit;
                $wastageProduct->material_type = $type == WarehousePlanning::TYPE_EXCEPTION ? $productItem->material_type : $productItem->product_category_id;
                $wastageProduct->warehouse_id = $this->warehouse_id;
                $wastageProduct->status = 1;
                $wastageProduct->type = $type;
                $wastageProduct->pstock_id = $stockId;
                $wastageProduct->batches = $batches;
                if(!$wastageProduct->validate()) {
                    $transaction->rollBack();
                    return array("state" => 0, "message" => $wastageProduct->getFirstErrors());
                }
                $wastageProduct->save();
                $num++;
                $totalAmount += $wastageProduct->total_amount;
                $meterialType[] = $wastageProduct->material_type;
                $supplier[] = $wastageProduct->supplier_id;
            }
            if($totalAmount > 99999999) {
                $transaction->rollBack();
                return array("state" => 0, "message" => "退货总金额不能超过限制金额一亿");
            }
            if($num == 0) {
                $transaction->rollBack();
                return array("state" => 0, "message" => "请选择退货商品");
            }
            $date = date("m", strtotime($this->create_time));
            $areaId = 0;
            $result = Flow::confirmFollowAdminId(Flow::TYPE_MATERIALRETURN, $this, $totalAmount, $date, $areaId, $supplier, $meterialType);
            if(!$result["state"]) {
                $transaction->rollBack();
                return $result;
            }
            $this->total_amount = $totalAmount;
            if(!$this->save()){
                $transaction->rollBack();
                return array("state" => 0, "message" => $this->getFirstErrors());
            }
            if($this->is_buckle) {
                $buckleResult = Flow::buckleStock(Flow::TYPE_MATERIALRETURN, $this);
                if(!$buckleResult["state"]) {
                    $transaction->rollBack();
                    return $result;
                }
            }
            $businessModel = new BusinessAll();
            $business = $businessModel->addBusiness($this, Flow::TYPE_MATERIALRETURN);
            if(!$business["state"]) {
                $transaction->rollBack();
                return ["error" => 1, "message" => $business["message"]];
            }
            AdminLog::addLog("wmaterial_add", "物料退货申请成功：".$this->id);
            if($this->buying_id) {
                $result = WarehouseBuying::updateAll(["status" => Flow::STATUS_HANG_UP], ["id" => $this->buying_id]);
                if(!$result) {
                    $transaction->rollBack();
                    return array("state" => 0, "message" => "计划入库状态错误");
                }
            }
            if($this->status == Flow::STATUS_FINISH){
                $result = $this->Finish();
                if(!$result["state"]) {
                    $transaction->rollBack();
                    return $result;
                }
            }
            $transaction->commit();
            return array("state" => 1);
        } catch (Exception $ex) {
            $transaction->rollBack();
            return array("state" => 0, "message" => $ex->getTraceAsString());
        }
    }
   
    /**
     * 完成操作
     */
    public function Finish()
    {
        $materialReturnProduct = WarehouseMaterialReturnProduct::findAll(["material_return_id" => $this->id]);
        if(!$this->is_buckle) {
            $productIds = [];
            foreach ($materialReturnProduct as $productVal) {
                $stockOutItem = ProductStock::findOne($productVal->pstock_id);
                if($this->buying_id) {
                    $num = $productVal->product_number - $productVal->buying_number;
                    $result = WarehouseGateway::addWarehouseGateway($this->warehouse_id, $productVal->product_id, WarehouseGateway::TYPE_IN, $stockOutItem->number, $num, $this->id, WarehouseGateway::GATEWAY_TYPE_BUYING, $productVal->type, $stockOutItem->batches);
                    if(!$result["state"]) {
                        return $result;
                    }
                    $stockOutItem->number = $stockOutItem->number + $num;
                    if(!$stockOutItem->save()) {
                        return ["state" => 0, "message" => $stockOutItem->getFirstErrors()];
                    }
                    continue;
                } else {
                    $result = WarehouseGateway::addWarehouseGateway($this->warehouse_id, $productVal->product_id, WarehouseGateway::TYPE_OUT, $stockOutItem->number, $productVal->buying_number, $this->id, WarehouseGateway::GATEWAY_TYPE_MATERIALRETURN, $productVal->type, $stockOutItem->batches);
                    if(!$result["state"]) {
                        return $result;
                    }
                    if($productVal->buying_number > $stockOutItem->number) {
                        return ["state" => 0, "message" => "退货数量大于库存数量，无法退货"];
                    }
                    $stockOutItem->number = $stockOutItem->number - $productVal->buying_number;
                    if(!$stockOutItem->save()) {
                        return ["state" => 0, "message" => $stockOutItem->getFirstErrors()];
                    }
                    //获取退货除例外物料的其他物料ID
                    if($stockOutItem->type != WarehousePlanning::TYPE_EXCEPTION) {
                        $productIds[] = $stockOutItem->product_id;
                    }
                    continue;
                }
            }
            //验证物料是否已到库存警告
            $checkStockWarningResult = ProductStock::checkStockWarning($productIds);
            if(!$checkStockWarningResult["state"]) {
                return $checkStockWarningResult;
            }
        }

        $orderMaterialModel = new OrderMaterialReturn();
        $orderMaterialResult = $orderMaterialModel->addOrderMaterial($this, $materialReturnProduct);
        if(!$orderMaterialResult["state"]) {
            return $orderMaterialResult;
        }
        AdminLog::addLog("wmaterial_finish", "物料退货流程成功完成：".$this->id);
        return ["state" => 1, "message" => "操作成功"];
    }
}
