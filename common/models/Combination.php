<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\BaseActiveRecord;
use yii\db\Expression;
use yii\base\Exception;
use yii\helpers\ArrayHelper;

use common\models\CombinationProduct;
use common\models\WarehousePlanning;
use common\models\SupplierProduct;
use common\models\WarehouseBuyingProduct;

/**
 * This is the model class for table "Combination".
 *
 * @property integer $id
 * @property string $name
 * @property string $sn
 * @property double $total_amount
 * @property integer $payment
 * @property double $deposit
 * @property integer $warehouse_id
 * @property integer $create_admin_id
 * @property string $create_time
 * @property string $approval_time
 * @property string $operation_time
 * @property string $operation_cause
 * @property integer $supplier_id
 * @property string $common
 */
class Combination extends namespace\base\Combination
{
    /**
     * 获取记录支付方式
     */
    public function showPayment() 
    {
        return isset(WarehousePlanning::$_paymentAll[$this->payment]) ? WarehousePlanning::$_paymentAll[$this->payment] : "未知".$this->payment;
    }
    
    /**
     * 添加新的订单模版
     * @param type $post 表单提交
     * @return type
     */
    public function addCombination($post)
    {
        if(!isset($post["goodsId"]) || count($post["goodsId"]) == 0) {
            return array("state" => 0, "message" => "请选择组合物料");
        }
        $transaction = Yii::$app->db->beginTransaction();
        try{
            $this->attributes = $post["Combination"];
            $this->total_amount = 0;
            $this->create_admin_id = Yii::$app->user->getId();
            $this->create_time = date("Y-m-d H:i:s");
            if(in_array($this->payment, [1,2]) && (!$this->deposit || $this->deposit <= 0)) {
                $transaction->rollBack();
                return array("state" => 0, "message" => "定金不能为空或等于零");
            }
            if(!$this->validate()) {
                $transaction->rollBack();
                return array("state" => 0, "message" => $this->getFirstErrors());
            }
            $this->save();
            $num = $totalAmount = 0;
            foreach ($post["goodsId"] as $key => $goodsId) {
                if(!$goodsId){
                    continue;
                }
                if(!isset($post["goodsNum"][$key])) {
                    continue;
                }
                if($post["goodsNum"][$key] == 0) {
                    $transaction->rollBack();
                    return array("state" => 0, "message" => "出库数量必须大于0");
                }
                $stockItem = ProductStock::findOne($goodsId);
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
                $templateProduct = new CombinationProduct();
                $templateProduct->product_id = $productItem->id;
                $templateProduct->order_template_id = $this->id;
                $templateProduct->name = $productItem->name;
                $templateProduct->price = $stockItem->type == WarehousePlanning::TYPE_EXCEPTION ? $productItem->price : $productItem->purchase_price;
                $templateProduct->purchase_price = $stockItem->purchase_price;
                $templateProduct->sale_price = $stockItem->type == WarehousePlanning::TYPE_EXCEPTION ? $productItem->purchase_price : $productItem->sale_price;
                $templateProduct->product_number = $post["goodsNum"][$key];
                $templateProduct->total_amount = $templateProduct->purchase_price * $templateProduct->product_number;
                $templateProduct->supplier_id = $productItem->supplier_id;
                $templateProduct->supplier_product_id = $productItem->id;
                $templateProduct->num = $stockItem->type == WarehousePlanning::TYPE_EXCEPTION ? $productItem->num : $productItem->barcode;
                $templateProduct->spec = $productItem->spec;
                $templateProduct->unit = $productItem->unit;
                $templateProduct->material_type = $stockItem->type == WarehousePlanning::TYPE_EXCEPTION ? $productItem->material_type : $productItem->product_category_id;
                $templateProduct->status = 1;
                $templateProduct->pstock_id = $goodsId;
                $templateProduct->batches = $stockItem->batches;
                if(!$templateProduct->validate()) {
                    $transaction->rollBack();
                    return array("state" => 0, "message" => $templateProduct->getFirstErrors());
                }
                $templateProduct->save();
                $num++;
                $totalAmount += $templateProduct->total_amount;
            }
            if($num == 0) {
                $transaction->rollBack();
                return array("state" => 0, "message" => "请选择组合物料");
            }
            if($num == 1) {
                $transaction->rollBack();
                return array("state" => 0, "message" => "组合出库物料必须大于一个");
            }
            $this->total_amount = $totalAmount;
            if(!$this->save()){
                $transaction->rollBack();
                return array("state" => 0, "message" => $this->getFirstErrors());
            }
            AdminLog::addLog("template_add", "组合物料模板添加成功：".$this->id);
            $transaction->commit();
            return array("state" => 1);
        } catch (Exception $ex) {
            $transaction->rollBack();
            return array("state" => 0, "message" => $ex->getTraceAsString());
        }
    }
    
    /**
     * 修改订单模版
     * @param type $post 表单提交
     * @return type
     */
    public function updateTemplate($post) 
    {
        if(!isset($post["goodsId"]) || count($post["goodsId"]) == 0) {
            return array("state" => 0, "message" => "请选择组合物料");
        }
        $transaction = Yii::$app->db->beginTransaction();
        try{
            $this->attributes = $post["Combination"];
            $this->total_amount = 0;
            $this->create_admin_id = Yii::$app->user->getId();
            $this->create_time = date("Y-m-d H:i:s");
            if(!$this->validate()) {
                $transaction->rollBack();
                return array("state" => 0, "message" => $this->getFirstErrors());
            }
            $this->save();
            $num = $totalAmount = 0;
            CombinationProduct::deleteAll(["order_template_id" => $this->id]);
            foreach ($post["goodsId"] as $key => $goodsId) {
                if(!$goodsId){
                    continue;
                }
                if(!isset($post["goodsNum"][$key])) {
                    continue;
                }
                if($post["goodsNum"][$key] == 0) {
                    $transaction->rollBack();
                    return array("state" => 0, "message" => "出库数量必须大于0");
                }
                $stockItem = ProductStock::findOne($goodsId);
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
                $templateProduct = new CombinationProduct();
                $templateProduct->product_id = $productItem->id;
                $templateProduct->order_template_id = $this->id;
                $templateProduct->name = $productItem->name;
                $templateProduct->price = $stockItem->type == WarehousePlanning::TYPE_EXCEPTION ? $productItem->price : $productItem->purchase_price;
                $templateProduct->purchase_price = $productItem->purchase_price;
                $templateProduct->product_number = $post["goodsNum"][$key];
                $templateProduct->total_amount = $templateProduct->purchase_price * $templateProduct->product_number;
                $templateProduct->supplier_id = $productItem->supplier_id;
                $templateProduct->supplier_product_id = $productItem->id;
                $templateProduct->num = $stockItem->type == WarehousePlanning::TYPE_EXCEPTION ? $productItem->num : $productItem->barcode;
                $templateProduct->spec = $productItem->spec;
                $templateProduct->unit = $productItem->unit;
                $templateProduct->material_type = $stockItem->type == WarehousePlanning::TYPE_EXCEPTION ? $productItem->material_type : $productItem->product_category_id;
                $templateProduct->status = 1;
                $templateProduct->pstock_id = $goodsId;
                $templateProduct->batches = $stockItem->batches;
                if(!$templateProduct->validate()) {
                    $transaction->rollBack();
                    return array("state" => 0, "message" => $templateProduct->getFirstErrors());
                }
                $templateProduct->save();
                $num++;
                $totalAmount += $templateProduct->total_amount;
            }
            if($num == 0) {
                $transaction->rollBack();
                return array("state" => 0, "message" => "请选择组合物料");
            }            
            if($num == 1) {
                $transaction->rollBack();
                return array("state" => 0, "message" => "组合出库物料必须大于一个");
            }
            $this->total_amount = $totalAmount;
            if(!$this->save()){
            	 return array("state" => 0, "message" => $this->getFirstErrors());
            }
            AdminLog::addLog("template_add", "组合物料模板添加成功：".$this->id);
            $transaction->commit();
            return array("state" => 1);
        } catch (Exception $ex) {
            $transaction->rollBack();
            return array("state" => 0, "message" => $ex->getTraceAsString());
        }
    }
    
    /**
     * 获取所有订单模板列表
     */
    public static function getAllSelectData()
    {
        $orderTemplate = Combination::find()->all();
        return ArrayHelper::map($orderTemplate, "id", "name");
    }
}
