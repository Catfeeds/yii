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
use common\models\WarehouseWastageProduct;
use common\models\AdminLog;
use libs\common\Flow;
use common\models\DepartmentBalanceLog;
use common\models\Warehouse;
use common\models\BusinessAll;
use common\models\WarehousePlanning;
use common\models\WarehouseBuyingProduct;
/**
 * This is the model class for table "WarehouseWastage".
 *
 * @property integer $id
 * @property string $name
 * @property string $sn
 * @property double $total_amount
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
 * @property string $remark
 * @property integer $is_buckle
 * @property integer $timing_type
 */
class WarehouseWastage extends namespace\base\WarehouseWastage
{
    public $year;
    public $time;
    public $wastage;
    /**
     * 添加新的耗损申请
     * @param type $post 表单提交数据
     * @return type
     */
    public function addWastage($post)
    {
        if(!isset($post["stockId"]) || count($post["stockId"]) == 0) {
            return array("state" => 0, "message" => "请选择耗损商品");
        }
        $transaction = Yii::$app->db->beginTransaction();
        try{
            $this->attributes = $post["WarehouseWastage"];
            $this->total_amount = 0;
            $warehouseItem = Warehouse::findOne($this->warehouse_id);
            $this->department_id = $warehouseItem ? $warehouseItem->department_id : 0;
            $this->sn = Utils::generateSn(Flow::TYPE_WASTAGE);
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
                    return array("state" => 0, "message" => "报损物料数量必须大于0");
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
                if(!is_numeric($post["goodsNum"][$key]) || $post["goodsNum"][$key] <= 0) {
                    $transaction->rollBack();
                    return array("state" => 0, "message" => "耗损商品：".$productItem->name."耗损数量必须大于0");
                }
                if($post["goodsNum"][$key] > $stockItem->number){
                    $transaction->rollBack();
                    return array("state" => 0, "message" => $productItem->name."的耗损数量不能大于库存");
                }
                $isCheck = CheckFlow::productIsCheckFlow($stockId);
                if($isCheck) {
                    $transaction->rollBack();
                    return array("state" => 0, "message" => $productItem->name."处于盘点中，无法申请耗损");
                }
                $wastageProduct = new WarehouseWastageProduct();
                $wastageProduct->product_id = $stockItem->product_id;
                $wastageProduct->wastage_id = $this->id;
                $wastageProduct->name = $productItem->name;
                $wastageProduct->price = $stockItem->type == WarehousePlanning::TYPE_EXCEPTION ? $productItem->price : $productItem->purchase_price;
                $wastageProduct->purchase_price = $stockItem->purchase_price;
                $wastageProduct->sale_price = $stockItem->type == WarehousePlanning::TYPE_EXCEPTION ? $productItem->purchase_price : $productItem->sale_price;
                $wastageProduct->product_number = $stockItem->number;
                $wastageProduct->buying_number = $post["goodsNum"][$key];
                $wastageProduct->total_amount = $wastageProduct->purchase_price * $wastageProduct->buying_number;
                $wastageProduct->supplier_id = $productItem->supplier_id;
                $wastageProduct->supplier_product_id = $productItem->supplier_product_id;
                $wastageProduct->num = $stockItem->type == WarehousePlanning::TYPE_EXCEPTION ? $productItem->num : $productItem->barcode;
                $wastageProduct->spec = $productItem->spec;
                $wastageProduct->unit = $productItem->unit;
                $wastageProduct->material_type = $stockItem->type == WarehousePlanning::TYPE_EXCEPTION ? $productItem->material_type : $productItem->product_category_id;
                $wastageProduct->warehouse_id = $stockItem->warehouse_id;
                $wastageProduct->status = 1;
                $wastageProduct->type = $stockItem->type;
                $wastageProduct->pstock_id = $stockId;
                $wastageProduct->batches = $stockItem->batches;
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
                return array("state" => 0, "message" => "耗损总金额不能超过限制金额一亿");
            }
            if($num == 0) {
                $transaction->rollBack();
                return array("state" => 0, "message" => "请选择耗损商品");
            }
            $date = date("m", strtotime($this->create_time));
            $areaId = 0;
            $result = Flow::confirmFollowAdminId(Flow::TYPE_WASTAGE, $this, $totalAmount, $date, $areaId, [], $meterialType);
            if(!$result["state"]) {
                $transaction->rollBack();
                return $result;
            }
            if($this->is_buckle) {
                $buckleResult = Flow::buckleStock(Flow::TYPE_WASTAGE, $this);
                if(!$buckleResult["state"]) {
                    $transaction->rollBack();
                    return $result;
                }
            }
            $businessModel = new BusinessAll();
            $business = $businessModel->addBusiness($this, Flow::TYPE_WASTAGE);
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
            AdminLog::addLog("wwastage_add", "物料耗损申请成功：".$this->id);
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
        $wastageProduct = WarehouseWastageProduct::findAll(["wastage_id" => $this->id]);
        if(!$this->is_buckle) {
            $productIds = [];
            foreach ($wastageProduct as $productVal) {
                $stockOutItem = ProductStock::findOne($productVal->pstock_id);
                if(!$stockOutItem) {
                    continue;
                }
                if($stockOutItem->number < $productVal->buying_number) {
                    return ["state" => 0, "message" => "当前耗损仓库的商品：".$productVal->name."的库存不足耗损数量"];
                }
                $result = WarehouseGateway::addWarehouseGateway($this->warehouse_id, $productVal->product_id, WarehouseGateway::TYPE_OUT, $stockOutItem->number, $productVal->buying_number, $this->id, WarehouseGateway::GATEWAY_TYPE_WASTAGE, $productVal->type, $stockOutItem->batches);
                if(!$result["state"]) {
                    return $result;
                }
                $stockOutItem->number = $stockOutItem->number - $productVal->buying_number;
                $stockOutItem->save();
                //获取耗损除例外物料的其他物料ID
                if($stockOutItem->type != WarehousePlanning::TYPE_EXCEPTION) {
                    $productIds[] = $stockOutItem->product_id;
                }
                continue;
            }
            //验证物料是否已到库存警告
            $checkStockWarningResult = ProductStock::checkStockWarning($productIds);
            if(!$checkStockWarningResult["state"]) {
                return $checkStockWarningResult;
            }
        }
        $model = new DepartmentBalanceLog();
        $result = $model->addDepartmentBalanceLog($this->department_id, $this->id, DepartmentBalanceLog::BUSINESS_TYPE_WASTAGE, DepartmentBalanceLog::MOD_OUT, $this->total_amount, "物料耗损");
        if(!$result["state"]) {
            return $result;
        }
        AdminLog::addLog("wwastage_finish", "物料耗损申请成功完成：".$this->id);
        return ["state" => 1, "message" => "操作成功"];
    }
}
