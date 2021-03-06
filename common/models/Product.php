<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\BaseActiveRecord;
use yii\db\Expression;
use yii\helpers\Url;
use common\models\Supplier;
use common\models\SupplierProduct;
use libs\common\Flow;
use common\models\BusinessRemind;
use common\models\Admin;
use common\models\CommonRemark;
use common\models\BusinessAll;
use Exception;
use yii\helpers\ArrayHelper;

use common\models\CheckFlowProduct;
use common\models\CombinationProduct;
use common\models\OrderMaterialReturnProduct;
use common\models\OrderProcurementProduct;
use common\models\OrderTemplateProduct;
use common\models\ProductInvoicingSale;
use common\models\ProductInvoicingSaleInfo;
use common\models\ProductUpdate;
use common\models\SaleCheckProduct;
use common\models\WarehouseBackProduct;
use common\models\WarehouseBuyingProduct;
use common\models\WarehouseCheckoutProduct;
use common\models\WarehouseMaterialReturnProduct;
use common\models\WarehousePlanningProduct;
use common\models\WarehouseProcurementProduct;
use common\models\WarehouseSaleProduct;
use common\models\WarehouseTransferProduct;
use common\models\WarehouseTransferDepProduct;
use common\models\WarehouseWastageProduct;
/**
 * This is the model class for table "Product".
 *
 * @property integer $id
 * @property string $name
 * @property integer $product_category_id
 * @property string $barcode
 * @property double $purchase_price
 * @property double $sale_price
 * @property integer $supplier_id
 * @property integer $supplier_product_id
 * @property string $num
 * @property string $spec
 * @property string $unit
 * @property integer $material_type
 * @property integer $inventory_warning
 * @property integer $status
 * @property integer $create_admin_id
 * @property string $create_time
 * @property integer $verify_admin_id
 * @property string $verify_time
 * @property integer $approval_admin_id
 * @property string $approval_time
 * @property integer $modify_status
 * @property integer $operation_admin_id
 * @property string $operation_time
 * @property integer $config_id
 * @property string $failCause
 * @prpperty integer $is_batches
 * @prpperty integer $is_update
 * @prpperty integer $timing_type
 */
class Product extends namespace\base\Product
{
    /**
     * 无效
     */
    const STATUS_INVALID = 0;
    /**
     * 有效
     */
    const STATUS_VALID = 1;
    /**
     * 已删除
     */
    const STATUS_DEL = 99;
   
    /**
     * 待录入
     */
    const MODIFY_STATUS_APPLY_UPDATE = -1;
    /**
     * 待审核
     */
    const MODIFY_STATUS_APPLY_VERIFY = 0;
    /**
     * 待批准
     */
    const MODIFY_STATUS_APPLY_APPROVAL = 1;
    /**
     * 待完成
     */
    const MODIFY_STATUS_APPLY_FINISH = 2;
    /**
     * 完成
     */
    const MODIFY_STATUS_FINISH = 3;
    /**
     * 审核驳回
     */
    const MODIFY_STATUS_VERIFY_REJECT = 10;
    /**
     * 批准驳回
     */
    const MODIFY_STATUS_APPROVAL_REJECT = 11;
    /**
     * 完成驳回
     */
    const MODIFY_STATUS_FINISH_REJECT = 12;
    /**
     * 挂起
     */
    const MODIFY_STATUS_HANG_UP = 99;
    
    const TYPE_PRODUCT = 1; //商品
    const MODIFY_ASSETS = 2; // 资产
    /**
     * 是否需要批次号 -- 需要
     */
    const IS_BATCHES_YES = 1;
    /**
     * 是否需要批次号 -- 不需要
     */
    const IS_BATCHES_NO = 0;    
 
    private static $_status = [
        self::STATUS_INVALID => "无效",
        self::STATUS_VALID => '有效',
        self::STATUS_DEL => '挂起',
    ];
    
    private static $_type = [
        self::TYPE_PRODUCT => '商品',
        self::MODIFY_ASSETS => '资产',
    ];
    
    private static $_batchesAll = [
        self::IS_BATCHES_NO => '不需要',
        self::IS_BATCHES_YES => '需要',
    ];
    
   private static $_modifyStatus = [
        self::MODIFY_STATUS_APPLY_UPDATE => '待录入',
        self::MODIFY_STATUS_APPLY_VERIFY => '待审核',
        self::MODIFY_STATUS_APPLY_APPROVAL => '待批准',
        self::MODIFY_STATUS_APPLY_FINISH => '待完成',
        self::MODIFY_STATUS_FINISH => '完成',
        self::MODIFY_STATUS_VERIFY_REJECT => '审核驳回',
        self::MODIFY_STATUS_APPROVAL_REJECT => '批准驳回',
        self::MODIFY_STATUS_FINISH_REJECT => '完成驳回',
        self::MODIFY_STATUS_HANG_UP => '挂起',
    ];

   /**
    * 保存默认值
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
     * 字段规则
     */
    public function rules()
    {
        $rules = parent::rules();
        $childRules = [
            [['sale_price'], 'number', 'max' => 99999999, 'tooBig'=>'{attribute}的长度不能超过8个字符'],
//            [['barcode'], 'unique', 'targetClass' => '\common\models\Product', 'message' => '{attribute}已存在！'],
        ];
        return array_merge($rules, $childRules);
    }

    /**
     * 展示状态
     */
    public function showStatus()
    {
        return $this->is_update ? "修改中" : (isset(self::$_status[$this->status]) ? self::$_status[$this->status] : "未知".$this->status);
    }
    
    /**
     * 展示流程状态
     */
    public function showModifyStatus()
    {
        return  isset(self::$_modifyStatus[$this->modify_status]) ? self::$_modifyStatus[$this->modify_status] : "未知".$this->modify_status;
    }
    
    /**
     * 展示供应商名称
     */
    public function showSupplierName()
    {
        $model = Supplier::findOne($this->supplier_id);
        return isset($model->name) ?  $model->name: '无';
    }
    
    /**
     * 获取状态列表
     */
    public static function getStatusSelectData()
    {
        return self::$_status;
    }
    
    /**
     * 获取类型数据
     */
    public static function getTypeSelectData()
    {
        return self::$_type;
    }
    /**
     * 展示数据类型
     */
     public function showType()
    {
        return isset(self::$_type[$this->material_type]) ? self::$_status[$this->material_type] : "未知".$this->material_type;
    }
    /**
     * 展示数据类型
     */
    public static function showBatchesName($is_batches)
    {
        return isset(self::$_batchesAll[$is_batches]) ? self::$_batchesAll[$is_batches] : "未知".$is_batches;
    }
    
    /**
     * 获取是否需要批次号
     */
    public function getBatchesSelectData()
    {
        return self::$_batchesAll;
    }
    
    /**
     * 展示数据类型
     */
    public static function showTypeName($type)
    {
        return isset(self::$_type[$type]) ? self::$_type[$type] : "未知".$type;
    }
    
    /**
     * 展示库存警告
     */
    public function showInventoryWarning()
    {
        return isset($this->inventory_warning) && $this->inventory_warning > 0 ? $this->inventory_warning : "不需要";
    }
    
    /**
     * 通过物料ID获取物料名称
     * @param type $id 物料ID
     * @return type
     */
    public static function getNameById($id) 
    {
        $model = self::findOne($id);
        return isset($model->name) ?  $model->name: '无';
    } 
    
    /**
     * 添加供应商出品进物料信息
     * @param type $item 供应商出品信息
     * @return type
     */
    public function addProduct($item)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $item->status = SupplierProduct::STATUS_YES;
            $item->save();
//            $cateItem = ProductCategory::findOne($item->pro)
            $productItem = Product::findOne(["supplier_product_id" => $item->id]);
          
            if($productItem) {
                $productItem->status = self::STATUS_INVALID;
                $productItem->modify_status = self::MODIFY_STATUS_APPLY_UPDATE;
                $productItem->config_id = 0;
                $productItem->save();
                $transaction->commit();
                return array("state" => 1);
            }
            $this->name = $item->name;
            $this->product_category_id = 0;
            $this->barcode = "";
            $this->supplier_id = $item->supplier_id;
            $this->purchase_price = $item->purchase_price;
            $this->sale_price = $item->purchase_price;
            $this->supplier_product_id = $item->id;
            $this->num = $item->num;
            $this->spec = $item->spec;
            $this->unit = $item->unit;
            $this->material_type = $item->material_type;
            $this->inventory_warning = 0;
            $this->status = self::STATUS_INVALID;
            $this->create_admin_id = Yii::$app->user->getId();
            $this->create_time = date("Y-m-d H:i:s");
            $this->verify_admin_id = 0;
            $this->approval_admin_id = 0;
            $this->modify_status = self::MODIFY_STATUS_APPLY_UPDATE;
            $this->operation_admin_id = 0;
            $this->config_id = 0;
            $this->is_batches = self::IS_BATCHES_NO;
            if(!$this->save()){
                $transaction->rollBack();
                return array("state" => 0, "message" => $this->getFirstErrors());
            }
            //记录日志
            $code = 'add_product';
            $content = '供应商出品加入物料' .$this->id;
            AdminLog::addLog($code, $content);
            $transaction->commit();
            return array("state" => 1);
        } catch (Exception $exc) {
            $transaction->rollBack();
            return array("state" => 0, "message" => $exc->getTraceAsString());
        }
    }
    
    /**
     * 供应商出品无效
     * @param type $item
     */
    public function invalidProduct($item)
    {
        $productFlow = CheckFlowProduct::findAll(["product_id" => $this->id]);
        if($productFlow) {
            $businessIds = ArrayHelper::getColumn($productFlow, "check_flow_id");
            $business = BusinessAll::findAll(["business_id" => $businessIds, "business_type" => [Flow::TYPE_CHECK_PLANNING_PROOF, Flow::TYPE_CHECK_DEPARTMENT_PROOF, Flow::TYPE_CHECK_WAREHOUSE_PROOF], "status" => [Flow::STATUS_APPLY_VERIFY, Flow::STATUS_APPLY_APPROVAL, Flow::STATUS_APPLY_FINISH]]);
            if($business){
                return ["state" => 0, "message" => "失效的数据在执行流程中，无法设置无效！"];
            }            
        }
        $productFlow = CombinationProduct::findAll(["product_id" => $this->id]);
        if($productFlow) {
            return ["state" => 0, "message" => "失效的数据在执行流程中，无法设置无效！"];          
        }
        $productFlow = OrderMaterialReturnProduct::findAll(["product_id" => $this->id]);
        if($productFlow) {
            $businessIds = ArrayHelper::getColumn($productFlow, "order_procurement_id");
            $business = BusinessAll::findAll(["business_id" => $businessIds, "business_type" => Flow::TYPE_ORDER_MATERIAL, "status" => [Flow::STATUS_APPLY_VERIFY, Flow::STATUS_APPLY_APPROVAL, Flow::STATUS_APPLY_FINISH]]);
            if($business){
                return ["state" => 0, "message" => "失效的数据在执行流程中，无法设置无效！"];
            }            
        }
        $productFlow = OrderProcurementProduct::findAll(["product_id" => $this->id]);
        if($productFlow) {
            $businessIds = ArrayHelper::getColumn($productFlow, "order_procurement_id");
            $business = BusinessAll::findAll(["business_id" => $businessIds, "business_type" => Flow::TYPE_ORDER_FINANCE, "status" => [Flow::STATUS_APPLY_VERIFY, Flow::STATUS_APPLY_APPROVAL, Flow::STATUS_APPLY_FINISH]]);
            if($business){
                return ["state" => 0, "message" => "失效的数据在执行流程中，无法设置无效！"];
            }            
        }
        $productFlow = OrderTemplateProduct::findAll(["product_id" => $this->id]);
        if($productFlow) {
            return ["state" => 0, "message" => "失效的数据在执行流程中，无法设置无效！"];          
        }
        $productFlow = ProductInvoicingSaleInfo::findAll(["product_id" => $this->id]);
        if($productFlow) {
            $businessIds = ArrayHelper::getColumn($productFlow, "invoicing_sale_id");
            $business = ProductInvoicingSale::findAll(["id" => $businessIds, "status" => ProductInvoicingSale::STATUS_NO_SALE]);
            if($business){
                return ["state" => 0, "message" => "失效的数据在执行流程中，无法设置无效！"];
            }  
        }
        $productFlow = ProductUpdate::findAll(["product_id" => $this->id]);
        if($productFlow) {
            $businessIds = ArrayHelper::getColumn($productFlow, "id");
            $business = BusinessAll::findAll(["business_id" => $businessIds, "business_type" => Flow::TYPE_PRODUCT_UPDATE, "status" => [Flow::STATUS_APPLY_VERIFY, Flow::STATUS_APPLY_APPROVAL, Flow::STATUS_APPLY_FINISH]]);
            if($business){
                return ["state" => 0, "message" => "失效的数据在执行流程中，无法设置无效！"];
            }            
        }
        $productFlow = SaleCheckProduct::findAll(["product_id" => $this->id]);
        if($productFlow) {
            $businessIds = ArrayHelper::getColumn($productFlow, "sale_check_id");
            $business = BusinessAll::findAll(["business_id" => $businessIds, "business_type" => Flow::TYPE_SALE_CHECK, "status" => [Flow::STATUS_APPLY_VERIFY, Flow::STATUS_APPLY_APPROVAL, Flow::STATUS_APPLY_FINISH]]);
            if($business){
                return ["state" => 0, "message" => "失效的数据在执行流程中，无法设置无效！"];
            }            
        }
        $productFlow = SaleCheckProduct::findAll(["product_id" => $this->id]);
        if($productFlow) {
            $businessIds = ArrayHelper::getColumn($productFlow, "sale_check_id");
            $business = BusinessAll::findAll(["business_id" => $businessIds, "business_type" => Flow::TYPE_SALE_CHECK, "status" => [Flow::STATUS_APPLY_VERIFY, Flow::STATUS_APPLY_APPROVAL, Flow::STATUS_APPLY_FINISH]]);
            if($business){
                return ["state" => 0, "message" => "失效的数据在执行流程中，无法设置无效！"];
            }            
        }
        $productFlow = WarehouseBackProduct::findAll(["product_id" => $this->id]);
        if($productFlow) {
            $businessIds = ArrayHelper::getColumn($productFlow, "back_id");
            $business = BusinessAll::findAll(["business_id" => $businessIds, "business_type" => Flow::TYPE_BACK, "status" => [Flow::STATUS_APPLY_VERIFY, Flow::STATUS_APPLY_APPROVAL, Flow::STATUS_APPLY_FINISH]]);
            if($business){
                return ["state" => 0, "message" => "失效的数据在执行流程中，无法设置无效！"];
            }            
        }
        $productFlow = WarehouseBuyingProduct::findAll(["product_id" => $this->id]);
        if($productFlow) {
            $businessIds = ArrayHelper::getColumn($productFlow, "buying_id");
            $business = BusinessAll::findAll(["business_id" => $businessIds, "business_type" => Flow::TYPE_BUYING, "status" => [Flow::STATUS_APPLY_VERIFY, Flow::STATUS_APPLY_APPROVAL, Flow::STATUS_APPLY_FINISH]]);
            if($business){
                return ["state" => 0, "message" => "失效的数据在执行流程中，无法设置无效！"];
            }            
        }
        $productFlow = WarehouseCheckoutProduct::findAll(["product_id" => $this->id]);
        if($productFlow) {
            $businessIds = ArrayHelper::getColumn($productFlow, "checkout_id");
            $business = BusinessAll::findAll(["business_id" => $businessIds, "business_type" => Flow::TYPE_CHECKOUT, "status" => [Flow::STATUS_APPLY_VERIFY, Flow::STATUS_APPLY_APPROVAL, Flow::STATUS_APPLY_FINISH]]);
            if($business){
                return ["state" => 0, "message" => "失效的数据在执行流程中，无法设置无效！"];
            }            
        }
        $productFlow = WarehouseMaterialReturnProduct::findAll(["product_id" => $this->id]);
        if($productFlow) {
            $businessIds = ArrayHelper::getColumn($productFlow, "material_return_id");
            $business = BusinessAll::findAll(["business_id" => $businessIds, "business_type" => Flow::TYPE_MATERIALRETURN, "status" => [Flow::STATUS_APPLY_VERIFY, Flow::STATUS_APPLY_APPROVAL, Flow::STATUS_APPLY_FINISH]]);
            if($business){
                return ["state" => 0, "message" => "失效的数据在执行流程中，无法设置无效！"];
            }            
        }
        $productFlow = WarehousePlanningProduct::findAll(["product_id" => $this->id]);
        if($productFlow) {
            $businessIds = ArrayHelper::getColumn($productFlow, "planning_id");
            $business = BusinessAll::findAll(["business_id" => $businessIds, "business_type" => [Flow::TYPE_PLANNING, Flow::TYPE_PLANNING_ROUTINE, Flow::TYPE_PLANNING_EXCEPTION], "status" => [Flow::STATUS_APPLY_VERIFY, Flow::STATUS_APPLY_APPROVAL, Flow::STATUS_APPLY_FINISH]]);
            if($business){
                return ["state" => 0, "message" => "失效的数据在执行流程中，无法设置无效！"];
            }            
        }
        $productFlow = WarehouseProcurementProduct::findAll(["product_id" => $this->id]);
        if($productFlow) {
            $businessIds = ArrayHelper::getColumn($productFlow, "procurement_id");
            $business = BusinessAll::findAll(["business_id" => $businessIds, "business_type" => Flow::TYPE_ORDER, "status" => [Flow::STATUS_APPLY_VERIFY, Flow::STATUS_APPLY_APPROVAL, Flow::STATUS_APPLY_FINISH]]);
            if($business){
                return ["state" => 0, "message" => "失效的数据在执行流程中，无法设置无效！"];
            }            
        }
        $productFlow = WarehouseSaleProduct::findAll(["product_id" => $this->id]);
        if($productFlow) {
            $businessIds = ArrayHelper::getColumn($productFlow, "sale_id");
            $business = BusinessAll::findAll(["business_id" => $businessIds, "business_type" => Flow::TYPE_SALE, "status" => [Flow::STATUS_APPLY_VERIFY, Flow::STATUS_APPLY_APPROVAL, Flow::STATUS_APPLY_FINISH]]);
            if($business){
                return ["state" => 0, "message" => "失效的数据在执行流程中，无法设置无效！"];
            }            
        }
        $productFlow = WarehouseTransferProduct::findAll(["product_id" => $this->id]);
        if($productFlow) {
            $businessIds = ArrayHelper::getColumn($productFlow, "transfer_id");
            $business = BusinessAll::findAll(["business_id" => $businessIds, "business_type" => Flow::TYPE_TRANSFEF, "status" => [Flow::STATUS_APPLY_VERIFY, Flow::STATUS_APPLY_APPROVAL, Flow::STATUS_APPLY_FINISH]]);
            if($business){
                return ["state" => 0, "message" => "失效的数据在执行流程中，无法设置无效！"];
            }            
        }
        $productFlow = WarehouseTransferDepProduct::findAll(["product_id" => $this->id]);
        if($productFlow) {
            $businessIds = ArrayHelper::getColumn($productFlow, "transfer_dep_id");
            $business = BusinessAll::findAll(["business_id" => $businessIds, "business_type" => Flow::TYPE_TRANSFEFDEP, "status" => [Flow::STATUS_APPLY_VERIFY, Flow::STATUS_APPLY_APPROVAL, Flow::STATUS_APPLY_FINISH]]);
            if($business){
                return ["state" => 0, "message" => "失效的数据在执行流程中，无法设置无效！"];
            }            
        }
        $productFlow = WarehouseWastageProduct::findAll(["product_id" => $this->id]);
        if($productFlow) {
            $businessIds = ArrayHelper::getColumn($productFlow, "wastage_id");
            $business = BusinessAll::findAll(["business_id" => $businessIds, "business_type" => Flow::TYPE_WASTAGE, "status" => [Flow::STATUS_APPLY_VERIFY, Flow::STATUS_APPLY_APPROVAL, Flow::STATUS_APPLY_FINISH]]);
            if($business){
                return ["state" => 0, "message" => "失效的数据在执行流程中，无法设置无效！"];
            }            
        }
        $item->status = SupplierProduct::STATUS_NO;
        $item->save();
        $this->status = self::STATUS_DEL;
        $this->modify_status = self::MODIFY_STATUS_APPLY_UPDATE;
        $this->save();
        return array("state" => 1);
    }
    
    /**
     * 获取下一步操作
     */    
    public function showNextStepByInfo()
    {
        if(in_array($this->modify_status, [self::MODIFY_STATUS_FINISH, self::MODIFY_STATUS_APPROVAL_REJECT, self::MODIFY_STATUS_HANG_UP]) || $this->status == self::STATUS_DEL){
            return ["nextStep" => "无", "nextStepAdmin" => "无"];
        }
        if($this->modify_status == self::MODIFY_STATUS_APPLY_UPDATE) {
            return [
                        "nextStep" => '录入',
                        "nextStepAdmin" => "无",
                        "nextStepA" => '<a class="button blue-button" href="'.Url::to(['product/update',"id"=>$this->id]).'">录入</a>',
                    ];
        }
        $configItem = FlowConfig::findOne(["id" => $this->config_id]);
        if(!$configItem) {
            return ["nextStep" => "无", "nextStepAdmin" => "无"];
        }
        if($this->modify_status == self::MODIFY_STATUS_APPLY_FINISH) {
            if($configItem->operation_role_id > 0 && $configItem->operation_name && $configItem->operation_department_id > 0) {
                if($this->timing_type == Flow::TIMING_TYPE_VERIFY){
                    $return = [
                        "nextStep" => "确定定时".$configItem->operation_name."通过",
                        "nextStepAdmin" => Admin::getNameById($this->operation_admin_id),
                    ];
                    if($this->operation_admin_id == Yii::$app->user->getId() || Admin::checkSupperAdmin() || Admin::checkFlowAdmin()){
                        $class = Admin::checkFlowAdmin() && $this->operation_admin_id != Yii::$app->user->getId() ? "authOperation" : "confirmPass";
                        $return["nextStepA"] = '<a class="button blue-button '.$class.'" confirm-url="'.Url::to(['product/finish', "id" => $this->id]).'" i="confirmPass">'.$return["nextStep"].'</a>';
                    }
                    return $return;
                } 
                if($this->timing_type == Flow::TIMING_TYPE_VERIFY_REJECT){
                    $return = [
                        "nextStep" => "确定".$configItem->operation_name."过期驳回",
                        "nextStepAdmin" => Admin::getNameById($this->operation_admin_id),
                    ];
                    if($this->operation_admin_id == Yii::$app->user->getId() || Admin::checkSupperAdmin() || Admin::checkFlowAdmin()){
                        $class = Admin::checkFlowAdmin() && $this->operation_admin_id != Yii::$app->user->getId() ? "authOperation" : "confirmReject";
                        $return["nextStepA"] = '<a class="button blue-button '.$class.'" confirm-url="'.Url::to(['product/reject', "id" => $this->id]).'" i="confirmReject">'.$return["nextStep"].'</a>';
                    }
                    return $return;
                }
                $return = [
                    "nextStep" => $configItem->operation_name,
                    "nextStepAdmin" => Admin::getNameById($this->operation_admin_id),
                ];
                if($this->operation_admin_id == Yii::$app->user->getId() || Admin::checkSupperAdmin() || Admin::checkFlowAdmin()){
                    $class = Admin::checkFlowAdmin() && $this->operation_admin_id != Yii::$app->user->getId() ? "authOperation" : "operation";
                    $rejectClass = Admin::checkFlowAdmin() && $this->operation_admin_id != Yii::$app->user->getId() ? "authOperation" : "reject";
                    $return["nextStepA"] = '<a class="button blue-button '.$class.'" operator_url="'.Url::to(['product/finish', "id" => $this->id]).'" i="operation">'.$configItem->operation_name.'</a> | '
                    .'<a class="button blue-button '.$rejectClass.'" reject_href="'.Url::to(['product/reject', "id" => $this->id]).'" i="reject">'.$configItem->operation_name.'驳回</a> | ';
                }
                return $return;
            } 
        } else if($this->modify_status == self::MODIFY_STATUS_APPLY_APPROVAL) {
            if ($configItem->approval_role_id > 0 && $configItem->approval_name && $configItem->approval_department_id > 0) {
                if($this->timing_type == Flow::TIMING_TYPE_APPROVAL){
                    $return = [
                        "nextStep" => "确定定时".$configItem->approval_name."通过",
                        "nextStepAdmin" => Admin::getNameById($this->approval_admin_id),
                    ];
                    if($this->approval_admin_id == Yii::$app->user->getId() || Admin::checkSupperAdmin() || Admin::checkFlowAdmin()){
                        $class = Admin::checkFlowAdmin() && $this->approval_admin_id != Yii::$app->user->getId() ? "authOperation" : "confirmPass"; 
                        $return["nextStepA"] = '<a class="button blue-button '.$class.'" confirm-url="'.Url::to(['product/approval', "id" => $this->id]).'" i="confirmPass">'.$return["nextStep"].'</a>';
                    }
                    return $return;
                } 
                if($this->timing_type == Flow::TIMING_TYPE_APPROVAL_REJECT){
                    $return = [
                        "nextStep" => "确定".$configItem->approval_name."过期驳回",
                        "nextStepAdmin" => Admin::getNameById($this->approval_admin_id),
                    ];
                    if($this->approval_admin_id == Yii::$app->user->getId() || Admin::checkSupperAdmin() || Admin::checkFlowAdmin()){
                        $class = Admin::checkFlowAdmin() && $this->approval_admin_id != Yii::$app->user->getId() ? "authOperation" : "confirmReject";
                        $return["nextStepA"] = '<a class="button blue-button '.$class.'" confirm-url="'.Url::to(['product/reject', "id" => $this->id]).'" i="confirmReject">'.$return["nextStep"].'</a>';
                    }
                    return $return;
                }
                $return = [
                    "nextStep" => $configItem->approval_name,
                    "nextStepAdmin" => Admin::getNameById($this->approval_admin_id),
                ];
                if($this->approval_admin_id == Yii::$app->user->getId() || Admin::checkSupperAdmin() || Admin::checkFlowAdmin()) {
                    $class = Admin::checkFlowAdmin() && $this->approval_admin_id != Yii::$app->user->getId() ? "authOperation" : "operation";
                    $rejectClass = Admin::checkFlowAdmin() && $this->approval_admin_id != Yii::$app->user->getId() ? "authOperation" : "reject";
                    $return["nextStepA"] = '<a class="button blue-button '.$class.'" operator_url="'.Url::to(['product/approval', "id" => $this->id]).'" i="operation">'.$configItem->approval_name.'</a> | '
                    .'<a class="button blue-button '.$rejectClass.'" href="javascript:void(0)"  reject_href="'.Url::to(['product/reject', "id" => $this->id]).'" i="reject">'.$configItem->approval_name.'驳回</a> | ';
                }
                return $return;
            } 
        } else if($this->modify_status == self::MODIFY_STATUS_APPLY_VERIFY){
            if ($configItem->verify_role_id > 0 && $configItem->verify_name && $configItem->verify_department_id > 0) {
                if($this->timing_type == Flow::TIMING_TYPE_VERIFY){
                    $return = [
                        "nextStep" => "确定定时".$configItem->verify_name."通过",
                        "nextStepAdmin" => Admin::getNameById($this->verify_admin_id),
                    ];
                    if($this->verify_admin_id == Yii::$app->user->getId() || Admin::checkSupperAdmin() || Admin::checkFlowAdmin()){
                        $class = Admin::checkFlowAdmin() && $this->verify_admin_id != Yii::$app->user->getId() ? "authOperation" : "confirmPass";
                        $return["nextStepA"] = '<a class="button blue-button '.$class.'" confirm-url="'.Url::to(['product/verify', "id" => $this->id]).'" i="confirmPass">'.$return["nextStep"].'</a>';
                    }
                    return $return;
                } 
                if($this->timing_type == Flow::TIMING_TYPE_VERIFY_REJECT){
                    $return = [
                        "nextStep" => "确定".$configItem->verify_name."过期驳回",
                        "nextStepAdmin" => Admin::getNameById($this->verify_admin_id),
                    ];
                    if($this->verify_admin_id == Yii::$app->user->getId() || Admin::checkSupperAdmin() || Admin::checkFlowAdmin()){
                        $class = Admin::checkFlowAdmin() && $this->verify_admin_id != Yii::$app->user->getId() ? "authOperation" : "confirmReject";
                        $return["nextStepA"] = '<a class="button blue-button '.$class.'" confirm-url="'.Url::to(['product/reject', "id" => $this->id]).'" i="confirmReject">'.$return["nextStep"].'</a>';
                    }
                    return $return;
                }
                $return = [
                    "nextStep" => $configItem->verify_name,
                    "nextStepAdmin" => Admin::getNameById($this->verify_admin_id),
                ];
                if($this->verify_admin_id == Yii::$app->user->getId() || Admin::checkSupperAdmin() || Admin::checkFlowAdmin()) {
                    $class = Admin::checkFlowAdmin() && $this->verify_admin_id != Yii::$app->user->getId() ? "authOperation" : "operation";
                    $rejectClass = Admin::checkFlowAdmin() && $this->verify_admin_id != Yii::$app->user->getId() ? "authOperation" : "reject";
                    $return["nextStepA"] = '<a class="button blue-button '.$class.'"  operator_url="'.Url::to(['product/verify', "id" => $this->id]).'" i="operation">'.$configItem->verify_name.'</a> | '
                    .'<a class="button blue-button '.$rejectClass.'" reject_href="'.Url::to(['product/reject', "id" => $this->id]).'" i="reject">'.$configItem->verify_name.'驳回</a> | ';
                }
                return $return;
            }
        }   
        return ["nextStep" => "无"];
    }
    
    /**
     * 审核操作
     */
    public function Verify($remark = "") 
    {
        $configItem = FlowConfig::findOne(["id" => $this->config_id]);
        if(!$configItem) {
            return ["error" => 1, "message" => "没有审核操作"];
        }
        if(!($configItem->verify_role_id > 0 && $configItem->verify_name && $configItem->verify_department_id > 0)){
            return ["error" => 1, "message" => "没有审核操作"];
        }
        if($this->modify_status != self::MODIFY_STATUS_APPLY_VERIFY) {
            return ["error" => 1, "message" => "操作状态错误"];
        }
        if($this->verify_admin_id != Yii::$app->user->getId() && !Admin::checkSupperAdmin() && !Admin::checkFlowAdmin()) {
            return ["error" => 1, "message" => "没有权限操作"];
        }
        $transaction = Yii::$app->db->beginTransaction();
        try{
            $item = BusinessAll::findOne(["business_id" => $this->id, "business_type" => Flow::TYPE_ADDPRODUCT]);
            BusinessRemind::disposeRemind($this->id, 'product', $this->modify_status);
            if($configItem->approval_role_id > 0 && $configItem->approval_name && $configItem->approval_department_id > 0){
                $this->modify_status = self::MODIFY_STATUS_APPLY_APPROVAL;
                $this->verify_time = date("Y-m-d H:i:s");
                $this->status = self::STATUS_INVALID;
                $item->status = self::MODIFY_STATUS_APPLY_APPROVAL;
                $item->verify_time = date("Y-m-d H:i:s");
                BusinessRemind::addRemind($this->id, 'product', $this->modify_status, $this->approval_admin_id, $this->name.'需要您的批准');
                $remarkResult = CommonRemark::addCommonRemark($this->id, Flow::TYPE_ADDPRODUCT, $remark, CommonRemark::TYPE_VERIFY);
                if(!$remarkResult["state"]) {
                    $transaction->rollBack();
                    return ["error" => 1, "message" => $remarkResult["message"]];
                }
            } else if($configItem->operation_role_id > 0 && $configItem->operation_name && $configItem->operation_department_id > 0) {
                $this->modify_status = self::MODIFY_STATUS_APPLY_FINISH;
                BusinessRemind::addRemind($this->id, 'product', $this->modify_status, $this->operation_admin_id, $this->name.'需要您的执行');
                $this->approval_time = date("Y-m-d H:i:s");
                $this->status = self::STATUS_INVALID;
                $item->status = self::MODIFY_STATUS_APPLY_FINISH;
                $item->approval_time = date("Y-m-d H:i:s");
                $remarkResult = CommonRemark::addCommonRemark($this->id, Flow::TYPE_ADDPRODUCT, $remark, CommonRemark::TYPE_APPROVAL);
                if(!$remarkResult["state"]) {
                    $transaction->rollBack();
                    return ["error" => 1, "message" => $remarkResult["message"]];
                }
            } else {
                $this->status = self::STATUS_VALID;
                $this->modify_status = self::MODIFY_STATUS_FINISH;
                $this->operation_time = date("Y-m-d H:i:s");
                $this->approval_time = date("Y-m-d H:i:s");
                $this->operation_admin_id = \Yii::$app->user->getId();
                $item->status = self::MODIFY_STATUS_FINISH;
                $item->operation_time = date("Y-m-d H:i:s");
                $remarkResult = CommonRemark::addCommonRemark($this->id, Flow::TYPE_ADDPRODUCT, $remark, CommonRemark::TYPE_OPERATOR);
                if(!$remarkResult["state"]) {
                    $transaction->rollBack();
                    return ["error" => 1, "message" => $remarkResult["message"]];
                }
            }
            $this->timing_type = Flow::TIMING_TYPE_NONE;
            $this->save();
            if(!$item->save()) {
                $transaction->rollBack();
                return ["error" => 1, "message" => $item->getFirstErrors()];
            }
            AdminLog::addLog("product_verify", "物料审核通过：".$this->id);
            $transaction->commit();
            return ["error" => 0, "message" => "操作成功"];
        } catch (Exception $ex) {
            $transaction->rollBack();
            return ["error" => 1, "message" => $ex->getTraceAsString()];
        }
    }
    
    /**
     * 批准操作
     */
    public function Approval($remark = "")
    {
        $configItem = FlowConfig::findOne(["id" => $this->config_id]);
        if(!$configItem) {
            return ["error" => 1, "message" => "没有批准操作"];
        }
        if(!($configItem->approval_role_id > 0 && $configItem->approval_name && $configItem->approval_department_id > 0)){
            return ["error" => 1, "message" => "没有批准操作"];
        }
        if($this->modify_status != self::MODIFY_STATUS_APPLY_APPROVAL) {
            return ["error" => 1, "message" => "操作状态错误"];
        }
        if($this->approval_admin_id != Yii::$app->user->getId() && !Admin::checkSupperAdmin() && !Admin::checkFlowAdmin()) {
            return ["error" => 1, "message" => "没有权限操作"];
        }
        $transaction = Yii::$app->db->beginTransaction();
        try{
            $item = BusinessAll::findOne(["business_id" => $this->id, "business_type" => Flow::TYPE_ADDPRODUCT]);
            BusinessRemind::disposeRemind($this->id, 'product', $this->modify_status);
            if($configItem->operation_role_id > 0 && $configItem->operation_name && $configItem->operation_department_id > 0) {
                $this->modify_status = self::MODIFY_STATUS_APPLY_FINISH;
                $this->approval_time = date("Y-m-d H:i:s");
                $this->status = self::STATUS_INVALID;
                $item->status = self::MODIFY_STATUS_APPLY_FINISH;
                $item->approval_time = date("Y-m-d H:i:s");
                BusinessRemind::addRemind($this->id, 'product', $this->modify_status, $this->operation_admin_id, $this->name.'需要您的执行');
                $remarkResult = CommonRemark::addCommonRemark($this->id, Flow::TYPE_ADDPRODUCT, $remark, CommonRemark::TYPE_APPROVAL);
                if(!$remarkResult["state"]) {
                    $transaction->rollBack();
                    return ["error" => 1, "message" => $remarkResult["message"]];
                }
            } else {
                $this->status = self::STATUS_VALID;
                $this->modify_status = self::MODIFY_STATUS_FINISH;
                $this->operation_time = date("Y-m-d H:i:s");
                $this->approval_time = date("Y-m-d H:i:s");
                $this->operation_admin_id = \Yii::$app->user->getId();
                $item->status = self::MODIFY_STATUS_FINISH;
                $item->operation_time = date("Y-m-d H:i:s");
                $remarkResult = CommonRemark::addCommonRemark($this->id, Flow::TYPE_ADDPRODUCT, $remark, CommonRemark::TYPE_OPERATOR);
                if(!$remarkResult["state"]) {
                    $transaction->rollBack();
                    return ["error" => 1, "message" => $remarkResult["message"]];
                }
            }
            $this->timing_type = Flow::TIMING_TYPE_NONE;
            $this->save();
            if(!$item->save()) {
                $transaction->rollBack();
                return ["error" => 1, "message" => $item->getFirstErrors()];
            }
            AdminLog::addLog("wtransfer_approval", "物料调仓申请批准通过：".$this->id);
            $transaction->commit();
            return ["error" => 0, "message" => "操作成功"];
        } catch (Exception $ex) {
            $transaction->rollBack();
            return ["error" => 1, "message" => $ex->getTraceAsString()];
        }
    }
    
    /**
     * 驳回操作
     * @param string $failCause 驳回理由
     */
    public function Reject($failCause)
    {
        if(!trim($failCause) && !is_numeric($failCause)) {
            return ["error" => 1, "message" => "驳回理由不能为空"];
        }
        if(!in_array($this->modify_status, [self::MODIFY_STATUS_APPLY_VERIFY, self::MODIFY_STATUS_APPLY_APPROVAL, self::MODIFY_STATUS_APPLY_FINISH])) {
            return ["error" => 1, "message" => "状态错误，无法驳回"];
        }
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $item = BusinessAll::findOne(["business_id" => $this->id, "business_type" => Flow::TYPE_ADDPRODUCT]);
            BusinessRemind::disposeRemind($this->id, 'product', $this->modify_status);
            if($this->modify_status == self::MODIFY_STATUS_APPLY_VERIFY) {
                $this->modify_status = self::MODIFY_STATUS_VERIFY_REJECT;
                $item->status = self::MODIFY_STATUS_VERIFY_REJECT;
                $item->verify_time = date("Y-m-d H:i:s");
            }
            if($this->modify_status == self::MODIFY_STATUS_APPLY_APPROVAL) {
                $this->modify_status = self::MODIFY_STATUS_APPROVAL_REJECT;
                $item->status = self::MODIFY_STATUS_APPROVAL_REJECT;
                $item->approval_time = date("Y-m-d H:i:s");
            }
            if($this->modify_status == self::MODIFY_STATUS_APPLY_FINISH) {
                $this->modify_status = self::MODIFY_STATUS_FINISH_REJECT;
                $item->status = self::MODIFY_STATUS_FINISH_REJECT;
                $item->operation_time = date("Y-m-d H:i:s");
            }
            $this->status = self::STATUS_INVALID;
            $this->failCause = $failCause;
            $this->timing_type = Flow::TIMING_TYPE_NONE;
            $this->save();
            if(!$item->save()) {
                $transaction->rollBack();
                return ["error" => 1, "message" => $item->getFirstErrors()];
            }
            $supplierProduct = SupplierProduct::findOne($this->supplier_product_id);
            $supplierProduct->status = SupplierProduct::STATUS_NO;
            if(!$supplierProduct->save()) {
                $transaction->rollBack();
                return ["error" => 1, "message" => $supplierProduct->getFirstErrors()];
            }
            AdminLog::addLog("wtransfer_reject", "驳回物料调仓申请：".$this->id);
            $transaction->commit();
            return ["error" => 0, "message" => "操作成功"];
        } catch (Exception $ex) {
            $transaction->rollBack();
            return ["error" => 1, "message" => $ex->getTraceAsString()];
        }
    }
    
    /**
     * 完成操作
     */
    public function Finish($remark = "")
    {
        $configItem = FlowConfig::findOne(["id" => $this->config_id]);
        if(!$configItem) {
            return ["state" => 0, "message" => "没有执行操作"];
        }
        if($this->operation_admin_id != Yii::$app->user->getId() && !Admin::checkSupperAdmin() && !Admin::checkFlowAdmin()) {
            return ["state" => 0, "message" => "没有权限操作"];
        }
        $transaction = Yii::$app->db->beginTransaction();
        try {
            BusinessRemind::disposeRemind($this->id, 'product', $this->modify_status);
            $this->modify_status = self::MODIFY_STATUS_FINISH;
            $this->status = self::STATUS_VALID;
            $this->operation_time = date("Y-m-d H:i:s");
            $this->timing_type = Flow::TIMING_TYPE_NONE;
            $this->save();
            $remarkResult = CommonRemark::addCommonRemark($this->id, Flow::TYPE_ADDPRODUCT, $remark, CommonRemark::TYPE_OPERATOR);
            if(!$remarkResult["state"]) {
                $transaction->rollBack();
                return ["state" => 0, "message" => $remarkResult["message"]];
            }
            $item = BusinessAll::findOne(["business_id" => $this->id, "business_type" => Flow::TYPE_ADDPRODUCT]);
            if($item) {
                $item->status = self::MODIFY_STATUS_FINISH;
                $item->operation_time = date("Y-m-d H:i:s");
                if(!$item->save()) {
                    $transaction->rollBack();
                    return ["state" => 0, "message" => $item->getFirstErrors()];
                }
            }
            AdminLog::addLog("wtransfer_finish", "物料加入成功完成：".$this->id);
            $transaction->commit();
            return ["state" => 1, "message" => "操作成功"];
        } catch (Exception $exc) {
            $transaction->rollBack();
            return ["state" => 0, "message" => $exc->getTraceAsString()];
        }
    }
}
