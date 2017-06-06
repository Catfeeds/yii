<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\BaseActiveRecord;
use yii\db\Expression;
use yii\base\Exception;
use yii\helpers\ArrayHelper;

use common\models\OrderTemplateProduct;
use common\models\WarehousePlanning;
use common\models\SupplierProduct;
/**
 * This is the model class for table "OrderTemplate".
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
class OrderTemplate extends namespace\base\OrderTemplate
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
    public function addOrderTemplate($post)
    {
        if(!isset($post["goodsId"]) || count($post["goodsId"]) == 0) {
            return array("state" => 0, "message" => "请选择订单商品");
        }
        $transaction = Yii::$app->db->beginTransaction();
        try{
            $this->attributes = $post["OrderTemplate"];
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
                if(!$goodsId) {
                    continue;
                }
                if(!isset($post["goodsNum"][$key])) {
                    continue;
                }
                if($post["goodsNum"][$key] == 0) {
                    $transaction->rollBack();
                    return ["state" => 0, "message" => "订单物料数量必须大于零".$key];
                }
                $productItem = Product::findOne($goodsId);
                if(!$productItem) {
                    continue;
                }
                $templateProduct = new OrderTemplateProduct();
                $templateProduct->product_id = $productItem->id;
                $templateProduct->order_template_id = $this->id;
                $templateProduct->name = $productItem->name;
                $templateProduct->price = $productItem->purchase_price;
                $templateProduct->purchase_price = $productItem->purchase_price;
                $templateProduct->sale_price = $productItem->sale_price;
                $templateProduct->product_number = 0;
                $templateProduct->buying_number = $post["goodsNum"][$key];
                $templateProduct->total_amount = $templateProduct->sale_price * $templateProduct->buying_number;
                $templateProduct->supplier_id = $productItem->supplier_id;
                $templateProduct->supplier_product_id = $productItem->id;
                $templateProduct->num = $productItem->num;
                $templateProduct->spec = $productItem->spec;
                $templateProduct->unit = $productItem->unit;
                $templateProduct->material_type = $productItem->material_type;
                $templateProduct->status = 1;
                $templateProduct->product_cate_id = $productItem->product_category_id;
                if(!$templateProduct->save()) {
                    $transaction->rollBack();
                    return array("state" => 0, "message" => $templateProduct->getFirstErrors());
                }
                $num++;
                $totalAmount += $templateProduct->total_amount;
            }
            if($num == 0) {
                $transaction->rollBack();
                return array("state" => 0, "message" => "请选择订单物料");
            }
            $this->total_amount = $totalAmount;
            if(!$this->save()){
            	 return array("state" => 0, "message" => $this->getFirstErrors());
            }
            AdminLog::addLog("template_add", "订单模板添加成功：".$this->id);
            $transaction->commit();
            return array("state" => 1);
        } catch (Exception $ex) {
            $transaction->rollBack();
            return array("state" => 0, "message" => $ex->getMessage());
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
            return array("state" => 0, "message" => "请选择订单商品");
        }
        $transaction = Yii::$app->db->beginTransaction();
        try{
            $this->attributes = $post["OrderTemplate"];
            $this->total_amount = 0;
            $this->create_admin_id = Yii::$app->user->getId();
            $this->create_time = date("Y-m-d H:i:s");
            if(!$this->validate()) {
                $transaction->rollBack();
                return array("state" => 0, "message" => $this->getFirstErrors());
            }
            $this->save();
            $num = $totalAmount = 0;
            OrderTemplateProduct::deleteAll(["order_template_id" => $this->id]);
            foreach ($post["goodsId"] as $key => $goodsId) {
                if(!isset($post["goodsNum"][$key])) {
                    continue;
                }
                $productItem = Product::findOne($goodsId);
                if(!$productItem) {
                    continue;
                }
                $templateProduct = new OrderTemplateProduct();
                $templateProduct->product_id = $productItem->id;
                $templateProduct->order_template_id = $this->id;
                $templateProduct->name = $productItem->name;
                $templateProduct->price = $productItem->purchase_price;
                $templateProduct->purchase_price = $productItem->purchase_price;
                $templateProduct->sale_price = $productItem->sale_price;
                $templateProduct->product_number = 0;
                $templateProduct->buying_number = $post["goodsNum"][$key];
                $templateProduct->total_amount = $templateProduct->purchase_price * $templateProduct->buying_number;
                $templateProduct->supplier_id = $productItem->supplier_id;
                $templateProduct->supplier_product_id = $productItem->id;
                $templateProduct->num = $productItem->num;
                $templateProduct->spec = $productItem->spec;
                $templateProduct->unit = $productItem->unit;
                $templateProduct->material_type = $productItem->material_type;
                $templateProduct->product_cate_id = $productItem->product_category_id;
                $templateProduct->status = 1;
                if(!$templateProduct->save()) {
                    $transaction->rollBack();
                    return array("state" => 0, "message" => $templateProduct->getFirstErrors());
                }
                $num++;
                $totalAmount += $templateProduct->total_amount;
            }
            if($num == 0) {
                $transaction->rollBack();
                return array("state" => 0, "message" => "请选择订单商品");
            }
            $this->total_amount = $totalAmount;
            if(!$this->save()){
            	 return array("state" => 0, "message" => $this->getFirstErrors());
            }
            AdminLog::addLog("template_add", "订单模板添加成功：".$this->id);
            $transaction->commit();
            return array("state" => 1);
        } catch (Exception $ex) {
            $transaction->rollBack();
            return array("state" => 0, "message" => $ex->getTraceAsString());
        }
    }
    
    /**
     * 获取所有的订单模版列表
     */
    public static function getAllSelectData()
    {
        $orderTemplate = OrderTemplate::find()->all();
        return ArrayHelper::map($orderTemplate, "id", "name");
    }
}
