<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\BaseActiveRecord;
use yii\db\Expression;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;


use common\models\Product;
use common\models\Admin;
use common\models\WarehouseCheckProduct;
use common\models\AdminLog;
use common\models\WarehouseGateway;
use common\models\DepartmentCheck;
use libs\common\Flow;
use common\models\BusinessAll;
use common\models\Warehouse;
use common\models\WarehousePlanning;
use common\models\WarehouseBuyingProduct;
/**
 * This is the model class for table "WarehouseCheck".
 *
 * @property integer $id
 * @property integer $check_planning_id
 * @property string $name
 * @property string $sn
 * @property double $total_amount
 * @property double $total_purchase_amount
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
 * @property integer $timing_type
 */
class WarehouseCheck extends namespace\base\WarehouseCheck
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
     * 添加新的盘点申请
     * @param array $post 表单提交数据
     * @author dean feng851028@163.com
     */
    public function addCheck($post) 
    {
        if(!isset($post["stockId"]) || count($post["stockId"]) == 0) {
            return array("state" => 0, "message" => "请选择盘点商品");
        }
        $transaction = Yii::$app->db->beginTransaction();
        try{
            $this->attributes = $post["WarehouseCheck"];
            $warehouseItem = Warehouse::findOne(["id" => $this->warehouse_id]);
            $this->department_id = $warehouseItem->department_id;
            $this->status = Flow::STATUS_APPLY_VERIFY;
            $this->create_time = date("Y-m-d H:i:s");
            $this->create_admin_id = Yii::$app->user->getId();
            $this->verify_admin_id = 0;
            $this->approval_admin_id = 0;
            $this->operation_admin_id = 0;
            $this->operation_time = date("Y-m-d H:i:s");
            $this->config_id = 0;
            $this->total_amount = 0;
            $this->total_purchase_amount = 0;
            if(!($newId = $this->save())) {
                $transaction->rollBack();
                return array("state" => 0, "message" => $this->getFirstErrors());
            }
            $num = $totalAmout = $totalPurchasesAmout = 0;
            $meterialType = $supplier = array();
            foreach ($post["stockId"] as $key => $stockId) {
                if(!isset($post["goodsNum"][$key])) {
                    continue;
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
                $checkProduct = new WarehouseCheckProduct();
                $checkProduct->product_id = $stockItem->product_id;
                $checkProduct->check_id = $this->id;
                $checkProduct->name = $productItem->name;
                $checkProduct->price = $stockItem->type == WarehousePlanning::TYPE_EXCEPTION ? $productItem->price : $productItem->purchase_price;
                $checkProduct->purchase_price = $stockItem->type == WarehousePlanning::TYPE_EXCEPTION ? $productItem->price : $productItem->purchase_price;
                $checkProduct->sale_price = $stockItem->type == WarehousePlanning::TYPE_EXCEPTION ? $productItem->purchase_price : $productItem->sale_price;
                $checkProduct->product_number = $stockItem->number;
                $checkProduct->buying_number = $post["goodsNum"][$key];
                $checkProduct->total_amount = $checkProduct->purchase_price * $checkProduct->buying_number;
                $checkProduct->supplier_id = $productItem->supplier_id;
                $checkProduct->supplier_product_id = $productItem->supplier_product_id;
                $checkProduct->num = $stockItem->type == WarehousePlanning::TYPE_EXCEPTION ? $productItem->num : $productItem->barcode;
                $checkProduct->spec = $productItem->spec;
                $checkProduct->unit = $productItem->unit;
                $checkProduct->material_type = $stockItem->type == WarehousePlanning::TYPE_EXCEPTION ? $productItem->material_type : $productItem->product_category_id;
                $checkProduct->warehouse_id = $this->warehouse_id;
                $checkProduct->status = 1;
                $checkProduct->type = $stockItem->type;
                $checkProduct->pstock_id = $stockId;
                $checkProduct->batches = $stockItem->batches;
                if(!$checkProduct->save()) {
                    $transaction->rollBack();
                    return array("state" => 0, "message" => $checkProduct->getFirstErrors());
                }
                $num++;
                $totalAmout += $checkProduct->total_amount;
                $totalPurchasesAmout += $checkProduct->purchase_price * $checkProduct->product_number;
                $meterialType[] = $checkProduct->material_type;
                $supplier[] = $checkProduct->supplier_id;
            }
            if($num == 0) {
                $transaction->rollBack();
                return array("state" => 0, "message" => "请选择盘点商品");
            }
            $this->total_amount = $totalAmout;
            $this->total_purchase_amount = $totalPurchasesAmout;
           //根据条件去判断符合某个流程
            $date = date("m", strtotime($this->create_time));
            $areaId = 0;
            $result = Flow::confirmFollowAdminId(Flow::TYPE_CHECK, $this, $totalAmout, $date, $areaId, $supplier, $meterialType);
            if(!$result["state"]) {
                $transaction->rollBack();
                return $result;
            }
            $businessModel = new BusinessAll();
            $business = $businessModel->addBusiness($this, Flow::TYPE_CHECK);
            if(!$business["state"]) {
                $transaction->rollBack();
                return ["error" => 1, "message" => $business["message"]];
            }
            if(!$this->save()){
                $transaction->rollBack();
                return array("state" => 0, "message" => $this->getFirstErrors());
            }
            AdminLog::addLog("wcheck_add", "物料盘点申请成功：".$this->id);
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
        $checkProduct = WarehouseCheckProduct::findAll(["check_id" => $this->id]);
        foreach ($checkProduct as $productVal) {
            $stockOutItem = ProductStock::findOne($productVal->pstock_id);
            if(!$stockOutItem) {
                continue;
            }
            $result = WarehouseGateway::addWarehouseGateway($this->warehouse_id, $productVal->product_id, WarehouseGateway::TYPE_IN, $stockOutItem->number, $productVal->buying_number, $this->id, WarehouseGateway::GATEWAY_TYPE_CHECK, $productVal->type, $stockOutItem->batches);
            if(!$result["state"]) {
                return $result;
            }
            $stockOutItem->number = $stockOutItem->number + $productVal->buying_number -  $productVal->product_number;
            $stockOutItem->save();
        }
        if($this->check_planning_id){
            $result = DepartmentCheck::updateCheckStatus($this->check_planning_id, $this->check_department_id, $this->status);
            if(!$result["state"]) {
                return $result;
            }
        }
        AdminLog::addLog("wcheck_finish", "物料盘点成功完成：".$this->id);
        return ["state" => 1];  
    }
}
