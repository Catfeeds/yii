<?php
namespace common\models;

use Yii;
use common\models\ProductInvoicingSaleInfo;
use common\models\SaleCheck;
use common\models\WarehousePlanning;
use common\models\WarehouseBuyingProduct;
use common\models\WarehouseGateway;
use common\models\Product;

use Exception;
/**
 * This is the model class for table "ProductInvoicingSale".
 *
 * @property integer $id
 * @property string $name
 * @property string $sn
 * @property integer $department_id
 * @property double $total_amount
 * @property integer $warehouse_id
 * @property integer $status
 * @property integer $create_admin_id
 * @property string $create_time
 * @property double $sale_amount
 * @property double $check_sale_amount
 * @property string $profit_loss_cause
 * @property double $last_invoic_amount
 * @property double $predict_invoic_amount
 * @property double $paid_amount
 * @property double $compensation_amount
 */
class ProductInvoicingSale extends namespace\base\ProductInvoicingSale
{
    //未销存
    const STATUS_NO_SALE = 0;
    //已销存
    const STATUS_YES_SALE = 1;
    //已取消
    const STATUS_CANCEL = -1;
    
    public static $_status = [
    	self::STATUS_NO_SALE => '未销存',
        self::STATUS_YES_SALE => '已销存',
        self::STATUS_CANCEL => '已取消',
    ];
    
    /**
     * 展示状态
     */
    public function showStatus() {
        return isset(self::$_status[$this->status]) ? self::$_status[$this->status] : "未知" . $this->status;
    }
    
    /**
     * 获取所有状态列表
     */
    public static function getStatusSelectData() {
        return self::$_status;
    }

    /**
     * 添加物料销存申请
     * @param type $post 销存表单提交数据
     * @return type
     */
    public function addInvoicingSale($post) {
        if(!isset($post["stockId"]) || count($post["stockId"]) == 0) {
            return array("state" => 0, "message" => "请选择销存商品");
        }
        $transaction = Yii::$app->db->beginTransaction();
        try{
            $this->attributes = $post["ProductInvoicingSale"];
            $warehouseItem = Warehouse::findOne($this->warehouse_id);
            if(!$warehouseItem) {
                $transaction->rollBack();
                return array("state" => 0, "message" => "请选择收银订单仓库");
            }
            $this->department_id = $warehouseItem->department_id;
            $this->total_amount = 0;
            $this->create_admin_id = Yii::$app->user->getId();
            $this->create_time = date("Y-m-d H:i:s");
            $this->status = self::STATUS_NO_SALE;
            if(!$this->validate()) {
                $transaction->rollBack();
                return array("state" => 0, "message" => $this->getFirstErrors());
            }
            $this->save();
            $num = $totalAmount = 0;
            foreach ($post["stockId"] as $key => $stockId) {
                if(!$stockId){
                    continue;
                }
                if(!isset($post["goodsNum"][$key])) {
                    continue;
                }
                if(!isset($post["salePrice"][$key])) {
                    continue;
                }
                if(!is_numeric($post["salePrice"][$key]) || $post["salePrice"][$key] <= 0) {
                    $transaction->rollBack();
                    return ["state" => 0, "message" => "销存物料实际销售价格必须大于零"];
                }
                if($post["goodsNum"][$key] == 0) {
                    $transaction->rollBack();
                    return ["state" => 0, "message" => "销存物料数量必须大于零"];
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
                    return ["state" => 0, "message" => "商品未知".$stockItem->product_id];
                }
                if($stockItem->number < $post["goodsNum"][$key]) {
                    $transaction->rollBack();
                    return array("state" => 0, "message" => "商品：".$productItem->name."的库存小于销存数量");
                }
                $isCheck = CheckFlow::productIsCheckFlow($stockId);
                if($isCheck) {
                    $transaction->rollBack();
                    return array("state" => 0, "message" => $productItem->name."处于盘点中，无法申请销存");
                }
                $saleProduct = new ProductInvoicingSaleInfo();
                $saleProduct->product_id = $stockItem->product_id;
                $saleProduct->invoicing_sale_id = $this->id;
                $saleProduct->name = $productItem->name;
                $saleProduct->purchase_price = $stockItem->purchase_price;
                $saleProduct->sale_price = $post["salePrice"][$key];
                $saleProduct->product_number = $stockItem->number;
                $saleProduct->buying_number = $post["goodsNum"][$key];
                $saleProduct->total_amount = $saleProduct->sale_price * $saleProduct->buying_number;
                $saleProduct->supplier_id = $productItem->supplier_id;
                $saleProduct->supplier_product_id = $productItem->supplier_product_id;
                $saleProduct->num = $stockItem->type == WarehousePlanning::TYPE_EXCEPTION ? $productItem->num : $productItem->barcode;
                $saleProduct->spec = $productItem->spec;
                $saleProduct->unit = $productItem->unit;
                $saleProduct->material_type = $stockItem->type == WarehousePlanning::TYPE_EXCEPTION ? $productItem->material_type : $productItem->product_category_id;
                $saleProduct->status = 1;
                $saleProduct->pstock_id = $stockId;
                $saleProduct->batches = $stockItem->batches;
                if(!$saleProduct->validate()) {
                    $transaction->rollBack();
                    return array("state" => 0, "message" => $saleProduct->getFirstErrors());
                }
                $saleProduct->save();
                $totalAmount += $saleProduct->total_amount;
                $num++;
            }
            if($totalAmount > 99999999) {
                $transaction->rollBack();
                return array("state" => 0, "message" => "销存总金额不能超过限制金额一亿");
            }
            if($num == 0) {
                $transaction->rollBack();
                return array("state" => 0, "message" => "请选择出库商品");
            }
            $this->total_amount = $totalAmount;
            $this->save();
            $finishResult = $this->Finish();
            if(!$finishResult["state"]) {
                $transaction->rollBack();
                return $finishResult;
            }
            AdminLog::addLog("product_sale_add", "物料销存申请成功：".$this->id);
            $transaction->commit();
            return array("state" => 1);
        } catch (Exception $ex) {
            $transaction->rollBack();
            return array("state" => 0, "message" => $ex->getTraceAsString());
        }
    }
    
    /**
     * 完成方法
     */
    public function Finish() {
        $productAll = ProductInvoicingSaleInfo::findAll(["invoicing_sale_id" => $this->id]);
        $productIds = [];
        foreach ($productAll as $productVal) {
            $stockOutItem = ProductStock::findOne($productVal->pstock_id);
            if(!$stockOutItem) {
                continue;
            }
            if($productVal->buying_number > $stockOutItem->number) {
                return ["state" => 0, "message" => "商品：".$productVal->name."库存不足"];
            }
            $result = WarehouseGateway::addWarehouseGateway($this->warehouse_id, $productVal->product_id, WarehouseGateway::TYPE_OUT, $stockOutItem->number, $productVal->buying_number, $this->id, WarehouseGateway::GATEWAY_TYPE_SALE, $stockOutItem->type, $stockOutItem->batches, "物料实时销存");
            if(!$result["state"]) {
                return $result;
            }
            $stockOutItem->number = $stockOutItem->number  -  $productVal->buying_number;
            $stockOutItem->save();
            if($stockOutItem->type != WarehousePlanning::TYPE_EXCEPTION) {
                $productIds[] = $stockOutItem->product_id;
            }
        }
        $checkStockWarningResult = ProductStock::checkStockWarning($productIds);
        if(!$checkStockWarningResult["state"]) {
            return $checkStockWarningResult;
        }
        return ["state" => 1];
    }
    
    /**
     * 销存确定盘点
     * @param type $post 表单提交数据
     * @return type
     */
    public function checkSale($post) {
        $real = $post["real"];
        if(count($real) == 0) {
            return array("state" => 1);
        }
        $stockIds = array_keys($real);
        $storeItem = ProductInvoicingSaleInfo::findAll($stockIds);
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $productList = array();
            foreach ($storeItem as $val) {
                $item = self::findOne($val->invoicing_sale_id);
                if(!$item || $item->status != self::STATUS_NO_SALE) {
                    continue;
                }
                if(!is_numeric($real[$val->id])) {
                    $transaction->rollBack();
                    return ["state" => 0, "message" => "商品：".$val->name."的实际销售数量不能为空"];
                }
                if($real[$val->id] == 0) {
                    $transaction->rollBack();
                    return ["state" => 0, "message" => "商品：".$val->name."的实际销售数量必须大于零"];
                }
//                if($val->product_number < $real[$val->id]) {
//                    $transaction->rollBack();
//                    return ["state" => 0, "message" => "商品：".$val->name."库存小于实际销售数量"];
//                }
                $num = $val->buying_number - $real[$val->id];
                if($num != 0) {
                    $result = $this->operateProductStock($val->invoicing_sale_id, $val->pstock_id, $num);
                    if(!$result["state"]) {
                        $transaction->rollBack();
                        return $result;
                    }
                }
                $productList[$item->warehouse_id][$val->id]["pstock_id"] = $val->pstock_id;
                $productList[$item->warehouse_id][$val->id]["sale_price"] = $val->sale_price;
                $productList[$item->warehouse_id][$val->id]["product_number"] = $val->buying_number;
                $productList[$item->warehouse_id][$val->id]["buying_number"] = $real[$val->id];
                $val->check_buying_num = $real[$val->id];
                if(!$val->save()) {
                    $transaction->rollBack();
                    return ["state" => 0, "message" => $val->getFirstErrors()];
                }
            }
            foreach ($storeItem as $val) {
                $item = self::findOne($val->invoicing_sale_id);
                $item->status = self::STATUS_YES_SALE;
                $item->profit_loss_cause = $post["profitLossCause"];
                $item->compensation_amount = $post["compensationAmount"];
                if(!$item->save()) {
                    $transaction->rollBack();
                    return ["state" => 0, "message" => $item->getFirstErrors()];
                }
            }
            if(count($productList) == 0) {
                $transaction->rollBack();
                return ["state" => 0, "message" => "网络异常，请刷新再试"];
            }
            $model = new SaleCheck();
            $result = $model->addSaleCheck($productList, $post);
            if(!$result["state"]) {
                $transaction->rollBack();
                return $result;
            }
            $transaction->commit();
            return ["state" => 1];
        } catch (Exception $exc) {
            $transaction->rollBack();
            return ["state" => 0, "message" => $exc->getTraceAsString()];
        }
    }
    
    /**
     * 取消销存方法
     * @return type
     * @throws Exception
     */
    public function cancel() {
        if($this->status != ProductInvoicingSale::STATUS_NO_SALE) {
            return ["state" => 0, "message" => "状态错误, 无法取消，请刷新再试"];
        }
        $transcation = Yii::$app->db->beginTransaction();
        try{
            $this->status = self::STATUS_CANCEL;
            if(!$this->save()) {
                $message = $this->getFirstErrors();
                throw new Exception(reset($message));
            }
            $productAll = ProductInvoicingSaleInfo::findAll(["invoicing_sale_id" => $this->id]);
            foreach ($productAll as $productVal) {
                $stockOutItem = ProductStock::findOne($productVal->pstock_id);
                if(!$stockOutItem) {
                    continue;
                }
                $result = WarehouseGateway::addWarehouseGateway($this->warehouse_id, $productVal->product_id, WarehouseGateway::TYPE_IN, $stockOutItem->number, $productVal->buying_number, $this->id, WarehouseGateway::GATEWAY_TYPE_SALE, $stockOutItem->type, $stockOutItem->batches, "物料实时销存取消入库");
                if(!$result["state"]) {
                    return $result;
                }
                $stockOutItem->number = $stockOutItem->number  +  $productVal->buying_number;
                $stockOutItem->save();
            }
            $transcation->commit();
            return ["state" => 1, "message" => "取消成功"];
        } catch (Exception $ex) {
            $transcation->rollBack();
            return ["state" => 0, "message" => $ex->getMessage()];
        }
    }
    
    /**
     * 操作物料库存
     * @param type $pstockId 库存ID
     * @param type $num 变动数量
     */
    public function operateProductStock($invoiceId, $pstockId, $num) {
        $stockItem = ProductStock::findOne($pstockId);
        if(!$stockItem) {
            return ["state" => 0, "message" => "数据异常"];
        }
        $type = $num > 0 ? WarehouseGateway::TYPE_IN : WarehouseGateway::TYPE_OUT;
        $comment = $num > 0 ? "销存盘点确定物料数量小于实时销存数据" : "销存盘点确定物料数量大于实时销存数据";
        $num = $num > 0 ? $num : 0 - $num;
        if($type == WarehouseGateway::TYPE_OUT && $num > $stockItem->number) {
            return ["state" => 0, "message" => "库存数据小于销存数据，无法确定销存盘点"];
        }
        $result = WarehouseGateway::addWarehouseGateway($stockItem->warehouse_id, $stockItem->product_id, $type, $stockItem->number, $num, $invoiceId, WarehouseGateway::GATEWAY_TYPE_SALE, $stockItem->type, $stockItem->batches, $comment);
        $stockItem->number = $stockItem->number  +  ($type == WarehouseGateway::TYPE_OUT ? 0 - $num : $num);
        $stockItem->save();
        return $result;
    }
}