<?php
namespace common\models;

use Yii;
use common\models\ProductInvoicingSaleInfo;
use common\models\WarehouseSale;
use common\models\WarehousePlanning;
use common\models\WarehouseBuyingProduct;

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
                return array("state" => 0, "message" => "未知仓库");
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
                $saleProduct = new ProductInvoicingSaleInfo();
                $saleProduct->product_id = $stockItem->product_id;
                $saleProduct->invoicing_sale_id = $this->id;
                $saleProduct->name = $productItem->name;
                $saleProduct->purchase_price = $stockItem->type == WarehousePlanning::TYPE_EXCEPTION ? $productItem->price : $productItem->purchase_price;
                $saleProduct->sale_price = $stockItem->type == WarehousePlanning::TYPE_EXCEPTION ? $productItem->purchase_price : $productItem->sale_price;
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
            if($num == 0) {
                $transaction->rollBack();
                return array("state" => 0, "message" => "请选择出库商品");
            }
            $this->total_amount = $totalAmount;
            $this->save();
            AdminLog::addLog("product_sale_add", "物料销存申请成功：".$this->id);
            $transaction->commit();
            return array("state" => 1);
        } catch (Exception $ex) {
            $transaction->rollBack();
            return array("state" => 0, "message" => $ex->getTraceAsString());
        }
    }
    
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
                    return ["state" => 0, "message" => "商品：".$val->name."的实际销售数量必须为数字"];
                }
                if($val->product_number < $real[$val->id]) {
                    $transaction->rollBack();
                    return ["state" => 0, "message" => "商品：".$val->name."库存小于实际销售数量"];
                }
                $productList[$item->warehouse_id][$val->pstock_id]["product_number"] = $val->product_number;
                $productList[$item->warehouse_id][$val->pstock_id]["buying_number"] = $real[$val->id];
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
            $model = new WarehouseSale();
            $result = $model->addSale($productList);
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
}