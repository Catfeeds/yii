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
use common\models\WarehousePlanningProduct;
use common\models\AdminLog;
use common\models\Warehouse;
use common\models\Department;
use common\models\WarehouseProcurement;
use libs\common\Flow;
use common\models\OrderProcurement;
use libs\Utils;

/**
 * This is the model class for table "WarehousePlanning".
 *
 * @property integer $id
 * @property string $name
 * @property string $sn
 * @property integer $warehouse_id
 * @property integer $department_id
 * @property string $order_sn
 * @property string $planning_date
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
 * @property string $total_money
 * @property string $failCause
 * @property integer $type
 * @property integer $supplier_id
 * @property integer $payment
 * @property double $deposit
 * @property string $payment_term
 * @property integer $buckle_amount
 * @property integer $timing_type
 */
class WarehousePlanning extends namespace\base\WarehousePlanning
{
    /**
     * 采购计划类型 -- 正常
     */
    const TYPE_NORMAL = 1;
    /**
     * 采购计划类型 -- 例行
     */
    const TYPE_ROUTINE = 2;
    /**
     * 采购计划类型 -- 例外
     */
    const TYPE_EXCEPTION = 3;
    /**
     * 付款方式 -- 预付
     */
    const PAYMENT_ADVANCE = 1;
    /**
     * 付款方式 -- 定金
     */
    const PAYMENT_BARGAIN = 2;
    /**
     * 付款方式 -- 后付
     */
    const PAYMENT_LATER = 3;
    
    public static $_paymentAll = [
            self::PAYMENT_ADVANCE => "预付",
            self::PAYMENT_BARGAIN => "定金",
            self::PAYMENT_LATER => "后付",
    ];
    
    /**
     * 获取记录支付方式
     * @author dean feng851028@163.com
     */
    public function showPayment() 
    {
        return isset(self::$_paymentAll[$this->payment]) ? self::$_paymentAll[$this->payment] : "未知".$this->payment;
    }
    
    /**
     * 获取支付方式列表
     * @author dean feng851028@163.com
     */
    public static function getPaymentSelectData()
    {
        return self::$_paymentAll;
    }    
    
    /**
     * 设置默认值
     * @author 
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
     * 添加或修改采购计划
     * @param type $post 表单提交数据
     * @author 
     */
    public function addOrUpdatePlanning($post) 
    {
        if(!isset($post["goodsId"]) || count($post["goodsId"]) == 0) {
            return array("state" => 0, "message" => "请选择采购商品");
        }
        //事务开始
        $transaction = Yii::$app->db->beginTransaction();
        try{
        	//接受参数
            $this->attributes = $post["WarehousePlanning"];
            $warehouseItem = Warehouse::findOne($this->warehouse_id);
            if(!$warehouseItem) {
                $transaction->rollBack();
                return array("state" => 0, "message" => "请选择采购入库仓库");
            }
            $ftype = $this->type == self::TYPE_NORMAL ? Flow::TYPE_PLANNING : Flow::TYPE_PLANNING_ROUTINE;
            $this->sn = Utils::generateSn($ftype);
            $this->department_id = $warehouseItem->department_id;
            $this->order_sn = $this->generateOrderSn($this->warehouse_id);
            $this->status = Flow::STATUS_APPLY_VERIFY;
            $this->create_time = date("Y-m-d H:i:s");
            $this->create_admin_id = Yii::$app->user->getId();
            $this->verify_admin_id = 0;
            $this->verify_time = date("Y-m-d H:i:s");
            $this->approval_admin_id = 0;
            $this->approval_time = date("Y-m-d H:i:s");
            $this->operation_admin_id = 0;
            $this->operation_time = date("Y-m-d H:i:s");
            $this->config_id = 0;
            $this->total_money = 0;
            //保存主表 WarehousePlanning  
            if(!($newId = $this->save())) {
                $transaction->rollBack();
                return array("state" => 0, "message" => $this->getFirstErrors());
            }
            $productInfo = Product::findAll(["id" => $post["goodsId"]]);
            $productInfo = ArrayHelper::index($productInfo, "id");
            $num = $totalMoney = 0;
            $meterialType = $supplier = array();
            WarehousePlanningProduct::deleteAll(["planning_id" => $this->id]);
            
            //循环保存 从表 WarehousePlanningProduct
            foreach ($post["goodsId"] as $key => $productId) {
                if(!isset($productInfo[$productId])) {
                    continue;
                }
                if(!isset($post["goodsNum"][$key])) {
                    continue;
                }
                if($post["goodsPrice"][$key] == 0) {
                    $transaction->rollBack();
                    return array("state" => 0, "message" => "真实采购价格必须大于0");
                }
                if($post["goodsNum"][$key] == 0) {
                    $transaction->rollBack();
                    return array("state" => 0, "message" => "采购物料数量必须大于0");
                }
                $productItem = $productInfo[$productId];
                $planningProduct = new WarehousePlanningProduct();
                $planningProduct->product_id = $productId;
                $planningProduct->planning_id = $this->id;
                $planningProduct->name = $productItem->name;
                $planningProduct->price = $productItem->purchase_price;
                $planningProduct->purchase_price = $post["goodsPrice"][$key];
                $planningProduct->product_number = $post["goodsNum"][$key];
                $planningProduct->total_amount = $planningProduct->purchase_price * $planningProduct->product_number;
                $planningProduct->supplier_id = $productItem->supplier_id;
                $planningProduct->supplier_product_id = $productItem->supplier_product_id;
                $planningProduct->num = $productItem->barcode;
                $planningProduct->spec = $productItem->spec;
                $planningProduct->unit = $productItem->unit;
                $planningProduct->material_type = $productItem->material_type;
                $planningProduct->product_cate_id = $productItem->product_category_id;
                if(!$planningProduct->save()) {
                    $transaction->rollBack();
                    return array("state" => 0, "message" => $planningProduct->getFirstErrors());
                }
                $num++;
                $totalMoney += $planningProduct->total_amount;
                $meterialType[] = $planningProduct->product_cate_id;
                $supplier[] = $planningProduct->supplier_id;
            }
            if($num == 0) {
                $transaction->rollBack();
                return array("state" => 0, "message" => "请选择采购物料");
            }
            
            //根据条件去判断符合某个流程
            $date = date("m", strtotime($this->planning_date));
            $areaId = 0;
            $result = Flow::confirmFollowAdminId($ftype, $this, $totalMoney, $date, $areaId, $supplier, $meterialType);
            if(!$result["state"]) {
                $transaction->rollBack();
                return $result;
            }
            $businessModel = new BusinessAll();
            $business = $businessModel->addBusiness($this, $ftype);
            if(!$business["state"]) {
                $transaction->rollBack();
                return ["error" => 0, "message" => $business["message"]];
            }
            //把金额的信息再次保存
            $this->total_money = $totalMoney;
            if(!$this->save()){
                $transaction->rollBack();
                return array("state" => 0, "message" => $this->getFirstErrors());
            }
            //记录管理员日志
            AdminLog::addLog("wplanning", "物料采购计划申请成功：".$this->id);
            $transaction->commit();
            return array("state" => 1);
        } catch (Exception $ex) {
            $transaction->rollBack();
            //如果失败返回失败的一些信息
            return array("state" => 0, "message" => $ex->getTraceAsString());
        } 
    }
    
    /**
     * 添加或修改例外订单
     * @param type $post 表单提交数据
     * @author 
     */
    public function addOrUpdateExceptionPlanning($post)
    {
        if(!isset($post["goodsName"]) || count($post["goodsName"]) == 0) {
            return array("state" => 0, "message" => "请选择采购商品");
        }
        $transaction = Yii::$app->db->beginTransaction();
        try{
            $this->attributes = $post["WarehousePlanning"];
            $warehouseItem = Warehouse::findOne($this->warehouse_id);
            if(!$warehouseItem) {
                $transaction->rollBack();
                return array("state" => 0, "message" => "请选择采购入库仓库");
            }
            $this->sn = Utils::generateSn(Flow::TYPE_PLANNING_EXCEPTION);
            $this->department_id = $warehouseItem->department_id;
            $this->order_sn = $this->generateOrderSn($this->warehouse_id);
            $this->status = Flow::STATUS_APPLY_VERIFY;
            $this->create_time = date("Y-m-d H:i:s");
            $this->create_admin_id = Yii::$app->user->getId();
            $this->verify_admin_id = 0;
            $this->approval_admin_id = 0;
            $this->operation_admin_id = 0;
            $this->config_id = 0;
            $this->total_money = 0;
            if(!$this->save()) {
                $transaction->rollBack();
                return array("state" => 0, "message" => $this->getFirstErrors());
            }
            $num = $totalMoney = 0;
            $meterialType = $supplier = array();
            WarehousePlanningProduct::deleteAll(["planning_id" => $this->id]);
            foreach ($post["goodsName"] as $key => $goodsName) {
                if(!$goodsName || !$post["goodsNum"][$key] || !$post["goodsBarcode"][$key] || !$post["goodsCate"][$key] || !$post["goodsPrice"][$key] || !$post["goodsSpec"][$key] || !$post["goodsUnit"][$key]) {
                    continue;
                }
                $planningProduct = new WarehousePlanningProduct();
                $planningProduct->product_id = 0;
                $planningProduct->planning_id = $this->id;
                $planningProduct->name = $goodsName;
                $planningProduct->price = $post["goodsPrice"][$key];
                $planningProduct->purchase_price = $post["goodsPrice"][$key];
                $planningProduct->product_number = $post["goodsNum"][$key];
                $planningProduct->total_amount = $planningProduct->purchase_price * $planningProduct->product_number;
                $planningProduct->supplier_id = $this->supplier_id;
                $planningProduct->supplier_product_id = 0;
                $planningProduct->num = $post["goodsBarcode"][$key];
                $planningProduct->spec = $post["goodsSpec"][$key];
                $planningProduct->unit = $post["goodsUnit"][$key];
                $planningProduct->material_type = $post["goodsType"][$key];
                $planningProduct->product_cate_id = $post["goodsCate"][$key];
                if(!$planningProduct->save()) {
                    $transaction->rollBack();
                    return array("state" => 0, "message" => $planningProduct->getFirstErrors());
                }
                $num++;
                $totalMoney += $planningProduct->total_amount;
                $meterialType[] = $planningProduct->product_cate_id;
                $supplier[] = $planningProduct->supplier_id;
            }
            if($num == 0) {
                $transaction->rollBack();
                return array("state" => 0, "message" => "请填写例外商品");
            }
            $date = date("m", strtotime($this->planning_date));
            $areaId = 0;
            $result = Flow::confirmFollowAdminId(Flow::TYPE_PLANNING_EXCEPTION, $this, $totalMoney, $date, $areaId, $supplier, $meterialType);
            if(!$result["state"]) {
                $transaction->rollBack();
                return $result;
            }
            $this->total_money = $totalMoney;
            if(!$this->save()){
                $transaction->rollBack();
            	return array("state" => 0, "message" => $this->getFirstErrors());
            }
            $businessModel = new BusinessAll();
            $business = $businessModel->addBusiness($this, Flow::TYPE_PLANNING_EXCEPTION);
            if(!$business["state"]) {
                $transaction->rollBack();
                return ["error" => 0, "message" => $business["message"]];
            }
            AdminLog::addLog("wplanning_add", "物料例外采购计划申请成功：".$this->id);
            $transaction->commit();
            return array("state" => 1);
        } catch (Exception $ex) {
            $transaction->rollBack();
            return array("state" => 0, "message" => $ex->getTraceAsString());
        } 
    }

    /**
     * 生成订单号
     * @param int $warehouse_id 仓库ID
     * @author 
     */
    public function generateOrderSn($warehouse_id)
    {
        $item = self::findAll(['like', 'create_time', date("Y-m-d").'%']);
        $warehouseItem = Warehouse::findOne($warehouse_id);
        if(!$warehouseItem) {
            return "O";
        }
        $department = Department::findOne($warehouseItem->department_id);
        if(!$department) {
            return "O";
        }
        return "O".$department->acronym . date("YmdHis").sprintf("%03d", (count($item) + 1));
    }
    
    /**
     * 完成操作 -- 加入采购入定记录中
     * @return type
     */
    public function Finish()
    {
        $model = new WarehouseProcurement();
        $model->name = $this->name;
        $model->procurement_planning_id = $this->id;
        $model->sn = Utils::generateSn(Flow::TYPE_ORDER);
        $model->order_sn = $this->order_sn;
        $model->warehouse_id = $this->warehouse_id;
        $model->department_id = $this->department_id;
        $model->supplier_id = $this->supplier_id;
        $model->planning_date = $this->planning_date;
        $model->payment = $this->payment;
        $model->deposit = $this->deposit;
        $model->total_amount = $this->total_money;
        $model->payment_term = $this->payment_term;
        $model->create_admin_id = Yii::$app->user->getId();
        $model->verify_admin_id = 0;
        $model->approval_admin_id = 0;
        $model->operation_admin_id = 0;
        $model->status = Flow::STATUS_APPLY_VERIFY;
        $model->create_time = date("Y-m-d H:i:s");
        $model->config_id = 0;
        $model->type = $this->type;
        if(!$model->save()) {
            return ["state" => 0, "message" => $model->getFirstErrors()];
        }
        $this->save();
        $meterialType = $supplier = array();
        $wplanningProduct = WarehousePlanningProduct::findAll(["planning_id" => $this->id]);
        foreach ($wplanningProduct as $productItem) {
            $productModel = new WarehouseProcurementProduct();
            $productModel->product_id = $productItem->product_id;
            $productModel->procurement_id = $model->id;
            $productModel->name = $productItem->name;
            $productModel->price = $productItem->price;
            $productModel->purchase_price = $productItem->purchase_price;
            $productModel->product_number = $productItem->product_number;
            $productModel->total_amount = $productModel->purchase_price * $productModel->product_number;
            $productModel->supplier_id = $productItem->supplier_id;
            $productModel->supplier_product_id = $productItem->supplier_product_id;
            $productModel->num = $productItem->num;
            $productModel->spec = $productItem->spec;
            $productModel->unit = $productItem->unit;
            $productModel->material_type = $productItem->product_cate_id;
            if(!$productModel->save()) {
                return ["state" => 0, "message" => $productModel->getFirstErrors()];
            }
            $meterialType[] = $productModel->material_type;
            $supplier[] = $productModel->supplier_id;
        }
        $date = date("m", strtotime($this->create_time));
        $areaId = 0;
        $result = Flow::confirmFollowAdminId(Flow::TYPE_ORDER, $model, $model->total_amount, $date, $areaId, $supplier, $meterialType);
        if(!$result["state"]) {
            return $result;
        }
        $businessModel = new BusinessAll();
        $business = $businessModel->addBusiness($model, Flow::TYPE_ORDER);
        if(!$business["state"]) {
            return $business;
        }
        AdminLog::addLog("wprocurement", "物料采购计划下定申请成功：".$this->id);
//        $orderProcurementModel = new OrderProcurement();
//        $addOrderResult = $orderProcurementModel->addNewOrderProcurement($this, $wplanningProduct);
//        if(!$addOrderResult["state"]) {
//            return $addOrderResult;
//        }
        return ["state" => 1];
    }
}
