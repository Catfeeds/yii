<?php
namespace common\models;

use Yii;
use libs\Utils;
use Exception;

use common\models\WarehousePlanning;
use common\models\WarehouseBuyingProduct;
use common\models\AdminLog;
use libs\common\Flow;
use common\models\Product;
use common\models\BusinessAll;
use common\models\Warehouse;
use common\models\CommonRemark;
use common\models\ProductStock;
use common\models\WarehouseGateway;
use common\models\WarehouseSale;
use common\models\WarehouseSaleProduct;

/**
 * This is the model class for table "SaleCheck".
 *
 * @property integer $id
 * @property string $name
 * @property string $sn
 * @property double $total_amount
 * @property double $sale_total_amount
 * @property integer $department_id
 * @property integer $warehouse_id
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
 * @property integer $timing_type
 * @property string $remark
 * @property double $paid_amount
 * @property double $compensation_amount
 */
class SaleCheck extends namespace\base\SaleCheck
{    
    /**
     * 添加新的销存盘点记录
     * @param type $post POST提交数据
     * @return type
     */
    public function addSaleCheck($productList, $post)
    {
        foreach ($productList as $warehouseId => $productVal) {
            $warehouseItem = Warehouse::findOne($warehouseId);
            if(!$warehouseItem) {
                return ["state" => 0, "message" => "销售仓库未知"];
            }
            $model = new SaleCheck();
            $model->name = $post["name"];
            $model->sn = Utils::generateSn(Flow::TYPE_SALE_CHECK);
            $model->total_amount = 0;
            $model->sale_total_amount = 0;
            $model->department_id = $warehouseItem->department_id;
            $model->warehouse_id = $warehouseId;
            $model->create_admin_id = Yii::$app->user->getId();
            $model->status = Flow::STATUS_APPLY_VERIFY;
            $model->create_time = date("Y-m-d H:i:s");
            $model->config_id = 0;
            $model->remark = $post["profitLossCause"];
            $model->paid_amount = 0;
            $model->compensation_amount = $post["compensationAmount"];
            if(!$model->save()) {
                $message = $model->getFirstErrors();
                return ["state" => 0, "message" => reset($message)];
            }
            $totalAmount = $saleTotalAmount = $paidAmount = 0;
            $meterialType = $supplier = array();
            foreach ($productVal as $val) {
                $stockItem = ProductStock::findOne($val["pstock_id"]);
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
                $productModel = new SaleCheckProduct();
                $productModel->product_id = $stockItem->product_id;
                $productModel->sale_check_id = $model->id;
                $productModel->name = $productItem->name;
                $productModel->purchase_price = $stockItem->purchase_price;
                $productModel->sale_price = $val["sale_price"];
                $productModel->product_number = $val["product_number"];
                $productModel->buying_number = $val["buying_number"];
                $productModel->total_amount = $productModel->sale_price * $productModel->buying_number;
                $productModel->supplier_id = $productItem->supplier_id;
                $productModel->supplier_product_id = $productItem->supplier_product_id;
                $productModel->num = $stockItem->type == WarehousePlanning::TYPE_EXCEPTION ? $productItem->num : $productItem->barcode;
                $productModel->spec = $productItem->spec;
                $productModel->unit = $productItem->unit;
                $productModel->material_type = $stockItem->type == WarehousePlanning::TYPE_EXCEPTION ? $productItem->material_type : $productItem->product_category_id;
                $productModel->warehouse_id = $warehouseId;
                $productModel->type = $stockItem->type;
                $productModel->pstock_id = $val["pstock_id"];
                $productModel->batches = $stockItem->batches;
                if(!$productModel->save()) {
                    $message = $productModel->getFirstErrors();
                    return ["state" => 0, "message" => reset($message)];
                }
                $meterialType[] = $productModel->material_type;
                $supplier[] = $productModel->supplier_id;
                $saleTotalAmount += $productModel->total_amount;
                $totalAmount += $productModel->sale_price * $productModel->product_number;
                $paidAmount += $productModel->sale_price * $productModel->product_number;;
            }
            $model->total_amount = $totalAmount;
            $model->sale_total_amount = $saleTotalAmount;
            $model->paid_amount = $paidAmount;
            if(!$model->save()){
            	 return ["state" => 0, "message" => $model->getFirstErrors()];
            }
            $result = Flow::confirmFollowAdminId(Flow::TYPE_SALE_CHECK, $model, $saleTotalAmount, time(), [], $supplier, $meterialType);
            if(!$result["state"]) {
                return $result;
            }
            $businessModel = new BusinessAll();
            $business = $businessModel->addBusiness($model, Flow::TYPE_SALE_CHECK);
            if(!$business["state"]) {
                return ["error" => 1, "message" => $business["message"]];
            }
            if($model->status == Flow::STATUS_FINISH){
                $result = $model->Finish();
                if(!$result["state"]) {
                    return $result;
                }
            }
            AdminLog::addLog("wsale_add", "物料销售申请成功：".$model->id);
        }
        return array("state" => 1);
    }
    
    /**
     * 完成方法
     */
    public function Finish() {
        $model = new WarehouseSale();
        $model->name = $this->name;
        $model->sn = Utils::generateSn(Flow::TYPE_SALE);
        $model->sale_check_id = $this->id;
        $model->total_amount = $this->sale_total_amount;
        $model->department_id = $this->department_id;
        $model->warehouse_id = $this->warehouse_id;
        $model->create_admin_id = Yii::$app->user->getId();
        $model->create_time = date("Y-m-d H:i:s");
        $model->status = Flow::STATUS_APPLY_VERIFY;
        $model->config_id = 0;
        if(!$model->validate()) {
            $message = $model->getFirstErrors();
            return ["state" => 0, "message" => reset($message)];
        }
        $model->save();
        $productList = SaleCheckProduct::findAll(["sale_check_id" => $this->id]);
        foreach ($productList as $product) {
            $stockItem = ProductStock::findOne($product->pstock_id);
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
            $productModel = new WarehouseSaleProduct();
            $productModel->product_id = $stockItem->product_id;
            $productModel->sale_id = $model->id;
            $productModel->name = $productItem->name;
            $productModel->purchase_price = $product->purchase_price;
            $productModel->sale_price = $product->sale_price;
            $productModel->product_number = $product->product_number;
            $productModel->buying_number = $product->buying_number;
            $productModel->total_amount = $productModel->sale_price * $productModel->buying_number;
            $productModel->supplier_id = $productItem->supplier_id;
            $productModel->supplier_product_id = $productItem->supplier_product_id;
            $productModel->num = $stockItem->type == WarehousePlanning::TYPE_EXCEPTION ? $productItem->num : $productItem->barcode;
            $productModel->spec = $productItem->spec;
            $productModel->unit = $productItem->unit;
            $productModel->material_type = $stockItem->type == WarehousePlanning::TYPE_EXCEPTION ? $productItem->material_type : $productItem->product_category_id;
            $productModel->warehouse_id = $stockItem->warehouse_id;
            $productModel->type = $stockItem->type;
            $productModel->pstock_id = $stockItem->id;
            $productModel->batches = $stockItem->batches;
            if(!$productModel->save()) {
                return array("state" => 0, "message" => $productModel->getFirstErrors());
            }
            $meterialType[] = $productModel->material_type;
            $supplier[] = $productModel->supplier_id;
            $totalAmount += $productModel->total_amount;
        }
        $result = Flow::confirmFollowAdminId(Flow::TYPE_SALE, $model, $totalAmount, time(), [], $supplier, $meterialType);
        if(!$result["state"]) {
            return $result;
        }
        $businessModel = new BusinessAll();
        $business = $businessModel->addBusiness($model, Flow::TYPE_SALE);
        if(!$business["state"]) {
            return ["error" => 1, "message" => $business["message"]];
        }
        $model->total_amount = $totalAmount;
        if(!$model->save()){
             return ["state" => 0, "message" => $model->getFirstErrors()];
        }
        if($model->status == Flow::STATUS_FINISH){
            $result = $model->Finish();
            if(!$result["state"]) {
                return $result;
            }
        }
        AdminLog::addLog("wsale_add", "物料销售申请成功：".$model->id);
        return array("state" => 1);
    }
    
    /**
     * 驳回方法
     */
    public function Reject() {
        $productList = SaleCheckProduct::findAll(["sale_check_id" => $this->id]);
        foreach ($productList as $productVal) {
            $stockOutItem = ProductStock::findOne($productVal->pstock_id);
            $result = WarehouseGateway::addWarehouseGateway($this->warehouse_id, $productVal->product_id, WarehouseGateway::TYPE_IN, $stockOutItem->number, $productVal->buying_number, $this->id, WarehouseGateway::GATEWAY_TYPE_SALECHECK, $productVal->type, $productVal->batches);
            if(!$result["state"]) {
                return $result;
            }
            $stockOutItem->number = $stockOutItem->number + $productVal->buying_number;
            $stockOutItem->save();
            
        }
        return ["state" => 1];
    }
}
