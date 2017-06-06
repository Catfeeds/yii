<?php

namespace libs\common;

use Yii;
use yii\helpers\Html;
use yii\base\InvalidConfigException;
use yii\base\Widget;
use yii\helpers\Url;
use yii\base\ErrorException;
use common\models\FlowConfig;
use common\models\Admin;
use common\models\BusinessRemind;
use common\models\AbnormalBalance;
use common\models\AdminLog;
use common\models\BusinessAll;
use common\models\CommonRemark;
use common\models\Warehouse;
use common\models\ProductStock;
use common\models\WarehouseGateway;
use common\models\WarehouseTransferDepProduct;
use common\models\WarehouseMaterialReturnProduct;
use common\models\WarehouseWastageProduct;
use common\models\WarehouseBackProduct;
use common\models\WarehouseTransferProduct;
use common\models\WarehouseCheckoutProduct;
use common\models\CheckFlowProduct;
use common\models\FlowCondition;
use common\models\WarehousePlanning;

class Flow {

    /**
     * 待审核
     */
    const STATUS_APPLY_VERIFY = 0;

    /**
     * 待批准
     */
    const STATUS_APPLY_APPROVAL = 1;

    /**
     * 待完成
     */
    const STATUS_APPLY_FINISH = 2;

    /**
     * 完成
     */
    const STATUS_FINISH = 3;

    /**
     * 审核驳回
     */
    const STATUS_VERIFY_REJECT = 10;

    /**
     * 批准驳回
     */
    const STATUS_APPROVAL_REJECT = 11;

    /**
     * 完成驳回
     */
    const STATUS_FINISH_REJECT = 12;
    /**
     * 联合驳回
     */
    const STATUS_UNION_REJECT = 13;

    /**
     * 挂起
     */
    const STATUS_HANG_UP = 99;

    private static $_status = [
        self::STATUS_APPLY_VERIFY => '待审核',
        self::STATUS_APPLY_APPROVAL => '待批准',
        self::STATUS_APPLY_FINISH => '待完成',
        self::STATUS_FINISH => '完成'
    ];
    
    private static $_statusAll = [
        self::STATUS_APPLY_VERIFY => '待审核',
        self::STATUS_APPLY_APPROVAL => '待批准',
        self::STATUS_APPLY_FINISH => '待完成',
        self::STATUS_FINISH => '完成',
        self::STATUS_VERIFY_REJECT => '审核驳回',
        self::STATUS_APPROVAL_REJECT => '批准驳回',
        self::STATUS_FINISH_REJECT => '完成驳回',
        self::STATUS_UNION_REJECT => '联合驳回',
        self::STATUS_HANG_UP => '挂起',
    ];

    /**
     * 采购计划 - 生成订单
     */
    const TYPE_PLANNING = 1;
    /**
     * 采购下订
     */
    const TYPE_ORDER = 2;
    /**
     * 订单入库
     */
    const TYPE_BUYING = 3;
    /**
     * 退仓申请
     */
    const TYPE_BACK = 4;
    /**
     * 出库申请
     */
    const TYPE_CHECKOUT = 5;
    /**
     * 转货申请
     */
    const TYPE_TRANSFEFDEP = 6;
    /**
     * 调仓申请
     */
    const TYPE_TRANSFEF = 7;
    /**
     * 物料退货申请
     */
    const TYPE_MATERIALRETURN = 8;
    /**
     * 物料耗损申请
     */
    const TYPE_WASTAGE = 9;
    /**
     * 新增物料
     */
    const TYPE_ADDPRODUCT = 11;
    /**
     * 订单支付
     */
    const TYPE_ORDER_FINANCE = 12;
    /**
     * 销存入账
     */
    const TYPE_SALE = 13;
    /**
     * 业务收支
     */
    const TYPE_ABNORMAL_FUND = 15;
    /**
     *  退货收款
     */
    const TYPE_ORDER_MATERIAL = 16;
    /**
     * 总盘点计划
     */
    const TYPE_CHECK_PLANNING = 18;
    /**
     * 部门盘点计划
     */
    const TYPE_CHECK_DEPARTMENT = 19;
    /**
     * 仓库盘点计划
     */
    const TYPE_CHECK_WAREHOUSE = 20;
    /**
     * 总盘点计划 -- 校对
     */
    const TYPE_CHECK_PLANNING_PROOF = 21;
    /**
     * 部门盘点计划  -- 校对
     */
    const TYPE_CHECK_DEPARTMENT_PROOF = 22;
    /**
     * 仓库盘点计划  -- 校对
     */
    const TYPE_CHECK_WAREHOUSE_PROOF = 23;
    /**
     * 采购计划 - 模板订单
     */
    const TYPE_PLANNING_ROUTINE = 24;
    /**
     * 采购计划 - 例外订单
     */
    const TYPE_PLANNING_EXCEPTION = 25;
    /**
     * 物料修改
     */
    const TYPE_PRODUCT_UPDATE = 26;
    /**
     * 销存盘点
     */
    const TYPE_SALE_CHECK = 27;
        /**
    *生成订单  2017年2月21日 16:41:19肖波
    */
     const STATUS_CREATE_ORDER = 28;

    public static $_type = [
        self::TYPE_PLANNING => "采购生成计划",
        self::TYPE_PLANNING_ROUTINE => "采购例行计划",
        self::TYPE_PLANNING_EXCEPTION => "采购例外计划",
        self::TYPE_ORDER => "采购下订",
        self::TYPE_BUYING => "订单入库",
        self::TYPE_BACK => "退仓申请",
        self::TYPE_CHECKOUT => "出库申请",
        self::TYPE_TRANSFEFDEP => "转货申请",
        self::TYPE_TRANSFEF => "调仓申请",
        self::TYPE_MATERIALRETURN => "物料退货申请",
        self::TYPE_WASTAGE => "物料耗损申请",
        self::TYPE_ADDPRODUCT => "新增物料",
        self::TYPE_ORDER_FINANCE => '订单支付',
        self::TYPE_SALE => '销存入账',
        self::TYPE_ABNORMAL_FUND => '业务收支',
        self::TYPE_ORDER_MATERIAL => '退货收款',
        self::TYPE_CHECK_PLANNING => '总盘点计划',
        self::TYPE_CHECK_DEPARTMENT => '部门盘点计划',
        self::TYPE_CHECK_WAREHOUSE => '仓库盘点计划',
        self::TYPE_CHECK_PLANNING_PROOF => '总盘点计划校对',
        self::TYPE_CHECK_DEPARTMENT_PROOF => '部门盘点计划校对',
        self::TYPE_CHECK_WAREHOUSE_PROOF => '仓库盘点计划校对',
        self::TYPE_PRODUCT_UPDATE => '物料修改',
        self::TYPE_SALE_CHECK => '销存盘点',
        self::STATUS_CREATE_ORDER=>'制作订单'
    ];
    
    public static $_typeUrl = [
        self::TYPE_PLANNING => "wplanning",
        self::TYPE_PLANNING_ROUTINE => "wplanning",
        self::TYPE_PLANNING_EXCEPTION => "wplanning",
        self::TYPE_ORDER => "wprocurement",
        self::TYPE_BUYING => "wbuying",
        self::TYPE_BACK => "wback",
        self::TYPE_CHECKOUT => "wcheckout",
        self::TYPE_TRANSFEFDEP => "wtransferdep",
        self::TYPE_TRANSFEF => "wtransfer",
        self::TYPE_MATERIALRETURN => "wmaterial",
        self::TYPE_WASTAGE => "wwastage",
        self::TYPE_ADDPRODUCT => "product",
        self::TYPE_ORDER_FINANCE => 'oprocurement',
        self::TYPE_SALE => 'wsale',
        self::TYPE_ABNORMAL_FUND => 'abnormalbalance',
        self::TYPE_ORDER_MATERIAL => 'omaterialreturn',
        self::TYPE_CHECK_PLANNING => 'checkplanning',
        self::TYPE_CHECK_PLANNING_PROOF => 'checkproof',
        self::TYPE_CHECK_DEPARTMENT => 'departmentcheckplanning',
        self::TYPE_CHECK_DEPARTMENT_PROOF => 'departmentcheckproof',
        self::TYPE_CHECK_PLANNING_PROOF => 'checkproof',
        self::TYPE_CHECK_WAREHOUSE => 'warehousecheckplanning',
        self::TYPE_CHECK_WAREHOUSE_PROOF => 'warehousecheckproof',
        self::TYPE_PRODUCT_UPDATE => 'productupdate',
        self::TYPE_SALE_CHECK => 'salecheck',
       self:: STATUS_CREATE_ORDER=>'order'

    ];
    
    public static $_config = [
        self::TYPE_PLANNING => [
            'create' => true,  //创建
            'verify' => true,  //审核
            'approval' => true,  //批准
            'operation' => true, //完成
            'reject' => true, //驳回
        ], //"采购计划",
        self::TYPE_PLANNING_ROUTINE => [
            'create' => true,  //创建
            'verify' => true,  //审核
            'approval' => true,  //批准
            'operation' => true, //完成
            'reject' => true, //驳回
        ], //"采购计划",
        self::TYPE_PLANNING_EXCEPTION => [
            'create' => true,  //创建
            'verify' => true,  //审核
            'approval' => true,  //批准
            'operation' => true, //完成
            'reject' => true, //驳回
        ], //"采购计划",
        self::TYPE_ORDER =>  [
            'create' => true,  //创建
            'verify' => true,  //审核
            'approval' => true,  //批准
            'operation' => true, //完成
            'reject' => true, //驳回
        ], //"采购下订",
        self::TYPE_BUYING => [
            'create' => true,  //创建
            'verify' => true,  //审核
            'approval' => true,  //批准
            'operation' => true, //完成
            'reject' => true, //驳回
        ],//"订单入库",
        self::TYPE_BACK =>  [
            'create' => true,  //创建
            'verify' => true,  //审核
            'approval' => true,  //批准
            'operation' => true, //完成
            'reject' => true, //驳回
        ], //"退仓申请",
        self::TYPE_CHECKOUT =>  [
            'create' => true,  //创建
            'verify' => true,  //审核
            'approval' => true,  //批准
            'operation' => true, //完成
            'reject' => true, //驳回
        ], //"出库申请",
        self::TYPE_TRANSFEFDEP =>  [
            'create' => true,  //创建
            'verify' => true,  //审核
            'approval' => true,  //批准
            'operation' => true, //完成
            'reject' => true, //驳回
        ], //"转货申请",
        self::TYPE_TRANSFEF =>  [
            'create' => true,  //创建
            'verify' => true,  //审核
            'approval' => true,  //批准
            'operation' => true, //完成
            'reject' => true, //驳回
        ], //"调仓申请",
        self::TYPE_MATERIALRETURN =>  [
            'create' => true,  //创建
            'verify' => true,  //审核
            'approval' => true,  //批准
            'operation' => true, //完成
            'reject' => true, //驳回
        ], //"物料退货申请",
        self::TYPE_WASTAGE =>  [
            'create' => true,  //创建
            'verify' => true,  //审核
            'approval' => true,  //批准
            'operation' => true, //完成
            'reject' => true, //驳回
        ], //"物料耗损申请",
        self::TYPE_ADDPRODUCT => [
            'create' => true,  //创建
            'verify' => true,  //审核
            'approval' => true,  //批准
            'operation' => true, //完成
            'reject' => true, //驳回
        ], //"新增物料",
        self::TYPE_ORDER_FINANCE => [
            'create' => true,  //创建
            'verify' => true,  //审核
            'approval' => true,  //批准
            'operation' => true, //完成
            'reject' => true, //驳回
        ], //'下单财务流程',
        self::TYPE_SALE => [
            'create' => true,  //创建
            'verify' => true,  //审核
            'approval' => true,  //批准
            'operation' => true, //完成
            'reject' => true, //驳回
        ], //'销存入账',
        self::TYPE_ABNORMAL_FUND => [
            'create' => true,  //创建
            'verify' => true,  //审核
            'approval' => true,  //批准
            'operation' => true, //完成
            'reject' => true, //驳回
        ], //'例外资金变动'
        self::TYPE_ORDER_MATERIAL => [
            'create' => true,  //创建
            'verify' => true,  //审核
            'approval' => true,  //批准
            'operation' => true, //完成
            'reject' => true, //驳回
        ], //'退货收款'
        self::TYPE_CHECK_PLANNING => [
            'create' => true,  //创建
            'verify' => true,  //审核
            'approval' => true,  //批准
            'operation' => true, //完成
            'reject' => true, //驳回
        ], //'总盘点计划'
        self::TYPE_CHECK_DEPARTMENT => [
            'create' => true,  //创建
            'verify' => true,  //审核
            'approval' => true,  //批准
            'operation' => true, //完成
            'reject' => true, //驳回
        ], //'部门盘点计划'
        self::TYPE_CHECK_WAREHOUSE => [
            'create' => true,  //创建
            'verify' => true,  //审核
            'approval' => true,  //批准
            'operation' => true, //完成
            'reject' => true, //驳回
        ], //'仓库盘点计划'
        self::TYPE_CHECK_PLANNING_PROOF => [
            'create' => true,  //创建
            'verify' => true,  //审核
            'approval' => true,  //批准
            'operation' => true, //完成
            'reject' => true, //驳回
        ], //'总盘点计划校对'
        self::TYPE_CHECK_DEPARTMENT_PROOF => [
            'create' => true,  //创建
            'verify' => true,  //审核
            'approval' => true,  //批准
            'operation' => true, //完成
            'reject' => true, //驳回
        ], //'总盘点计划校对'
        self::TYPE_CHECK_WAREHOUSE_PROOF => [
            'create' => true,  //创建
            'verify' => true,  //审核
            'approval' => true,  //批准
            'operation' => true, //完成
            'reject' => true, //驳回
        ], //'总盘点计划校对'
        self::TYPE_PRODUCT_UPDATE => [
            'create' => true,  //创建
            'verify' => true,  //审核
            'approval' => true,  //批准
            'operation' => true, //完成
            'reject' => true, //驳回
        ], //'物料修改'
        self::TYPE_SALE_CHECK => [
            'create' => true,  //创建
            'verify' => true,  //审核
            'approval' => true,  //批准
            'operation' => true, //完成
            'reject' => true, //驳回
        ], //'销存盘点'
    ];
    
    /**
     * 无
     */
    CONST TIMING_TYPE_NONE = 0;
    /**
     * 定时审核
     */
    CONST TIMING_TYPE_VERIFY = 1;
    /**
     * 定时批准
     */
    CONST TIMING_TYPE_APPROVAL = 2;
    /**
     * 定时执行
     */
    CONST TIMING_TYPE_OPERATION = 3;
    /**
     * 过期审核驳回
     */
    CONST TIMING_TYPE_VERIFY_REJECT = 4;
    /**
     * 过期批准驳回
     */
    CONST TIMING_TYPE_APPROVAL_REJECT = 5;
    /**
     * 过期执行驳回
     */
    CONST TIMING_TYPE_OPERATION_REJECT = 6;
    /**
     * 联合驳回
     */
    CONST TIMING_TYPE_UNION_REJECT = 11;
    

    /**
     * 展示状态
     */
    public static function showStatus($status) {
        return isset(self::$_status[$status]) ? self::$_status[$status] : "未知" . $status;
    }
    
    /**
     * 展示状态
     */
    public static function showStatusAll($status) {
        return isset(self::$_statusAll[$status]) ? self::$_statusAll[$status] : "未知" . $status;
    }

    /**
     * 获取通过状态列表 
     */
    public static function getStatusSelectData() {
        return self::$_status;
    }

    /**
     * 获取展示流程类型
     */
    public static function showType($type) {
        return isset(self::$_type[$type]) ? self::$_type[$type] : "未知" . $type;
    }

    /**
     * 获取通过的流程类型列表
     */
    public static function getTypeSelectData() {
        return self::$_type;
    }
    
    /**
     * 获取流程的操作步骤
     */
    public static function showFlowConfig($config) {
    	$_config = self::getConfig();
        return isset($_config[$config]) ? $_config[$config] : ['create' => false, 'verify' => false, 'approval' => false, 'operation' => false, 'reject' => false];
    }
    
    /**
     * 获取配置
     */
    public static function getConfig() {
        return self::$_config;
    }
    
    /**
     * 没有有创建操作的类型
     */
    public static function getNoCreateType() {
        return [
            self::TYPE_ORDER, 
            self::TYPE_BUYING, 
            self::TYPE_ORDER_FINANCE, 
            self::TYPE_ORDER_MATERIAL, 
            self::TYPE_CHECK_PLANNING_PROOF, 
            self::TYPE_CHECK_DEPARTMENT_PROOF, 
            self::TYPE_CHECK_WAREHOUSE_PROOF,
            self::TYPE_SALE,
        ];
    }
    
    /**
     * 必须有执行操作的类型
     */
    public static function getOperationType() {
        return [];
    }
    
    /**
     * 根据业务流程类型获取需要的条件
     * @param type $type 业务流程类型
     * @return boolean
     */
    public static function getTypeCheckCondition($type) {
        $result = [
            FlowCondition::TYPE_PRICE => true,
            FlowCondition::TYPE_TIME => true,
            FlowCondition::TYPE_AREA => true,
            FlowCondition::TYPE_SUPPLIER => true,
            FlowCondition::TYPE_CATEGORY => true,
        ];
        if(in_array($type, [self::TYPE_ADDPRODUCT, self::TYPE_PRODUCT_UPDATE])) {
            $result[FlowCondition::TYPE_AREA] = false;
            return $result;
        }
        if(in_array($type, [self::TYPE_ABNORMAL_FUND])) {
            $result[FlowCondition::TYPE_SUPPLIER] = false;
            $result[FlowCondition::TYPE_CATEGORY] = false;
            return $result;
        }
        if(in_array($type, [self::TYPE_CHECKOUT])) {
            $result[FlowCondition::TYPE_AREA] = false;
            return $result;
        }
        if(in_array($type, [self::TYPE_CHECK_PLANNING, self::TYPE_CHECK_PLANNING_PROOF])) {
            $result[FlowCondition::TYPE_AREA] = false;
            $result[FlowCondition::TYPE_PRICE] = false;
            return $result;
        }
        if(in_array($type, [self::TYPE_CHECK_DEPARTMENT, self::TYPE_CHECK_WAREHOUSE, self::TYPE_CHECK_DEPARTMENT_PROOF, self::TYPE_CHECK_WAREHOUSE_PROOF])) {
            $result[FlowCondition::TYPE_PRICE] = false;
            return $result;
        }
        return $result;
    }

    /**
     * 获取配置的类型地址
     * @param type $type 类型ID
     * @return type
     */
    public static function showTypeUrl($type) {
        return isset(self::$_typeUrl[$type]) ? self::$_typeUrl[$type] : "";
    }

    /**
     * 是否扣仓 -- 是
     */
    const BUCKLE_YES = 1;
    /**
     * 是否扣仓 -- 否
     */
    const BUCKLE_NO = 0;    
    private static $_buckleAll = [
        self::BUCKLE_NO => '否',
        self::BUCKLE_YES => '是',
    ];
    
    /**
     * 展示是否扣仓
     * @param type $is_buckle 是否扣仓
     * @return type
     */
    public static function showBuckleName($is_buckle)
    {
        return isset(self::$_buckleAll[$is_buckle]) ? self::$_buckleAll[$is_buckle] : "未知".$is_buckle;
    }
    
    /**
     * 获取是否扣仓列表
     */
    public static function getBuckleSelectData()
    {
        return self::$_buckleAll;
    }

    /**
     * 确定后续操作人员
     */
    public static function confirmFollowAdminId($type, $model, $totalMoney, $date, $areaId, $supplier, $meterialType) {
        if(in_array($type, [self::TYPE_CHECKOUT, self::TYPE_TRANSFEF, self::TYPE_BACK, Flow::TYPE_SALE_CHECK])){
            $warehouseItem = Warehouse::findOne($model->warehouse_id);
            $department = $warehouseItem ? [$warehouseItem->department_id] : [];
        }else if($type == self::TYPE_ABNORMAL_FUND){
            $checkMod = AbnormalBalance::checkModDepartment($model->mod);
            $department = [];
            if($checkMod["departmentList"]["expen"] > 0) {
                $department = [$model->department_id];
            }
            if($checkMod["departmentList"]["income"] > 0) {
                $department = [$model->income_department_id];
            }
        } else {
            $attr = $model->getAttributes();
            $department = isset($attr["department_id"]) ? [$attr["department_id"]] : [];
        }        
        $flowItems = FlowConfig::checkConfigCondition($type, $totalMoney, time(), $department, $supplier, $meterialType); 
        $configInfo = false;
        foreach ($flowItems as $flowVal) {
            if(!$flowVal->create_department_id || !$flowVal->create_role_id) {
                $configInfo = $flowVal;
                continue;
            }
            if(Admin::checkSupperAdmin() || Admin::checkFlowAdmin()) {
                $configInfo = $flowVal;
                continue;
            }
            $userIdentity = Yii::$app->user->getIdentity();
            if($flowVal->create_department_id == $userIdentity->department_id && $flowVal->create_role_id == $userIdentity->role_id) {
                $configInfo = $flowVal;
                break;
            }
        }
        if(!$configInfo) {
            return array("state" => 0, "message" => self::showType($type).":无符合业务流程");
        }
        $model->config_id = $configInfo->id;
        $model->create_admin_id = \Yii::$app->user->getId();
        $model->verify_admin_id = Admin::getAdminId($configInfo->verify_department_id, $configInfo->verify_role_id);
        $model->approval_admin_id = Admin::getAdminId($configInfo->approval_department_id, $configInfo->approval_role_id);
        $model->operation_admin_id = Admin::getAdminId($configInfo->operation_department_id, $configInfo->operation_role_id);
        
        if(in_array($type, self::getOperationType()) && !$model->verify_admin_id && !$model->approval_admin_id && !$model->operation_admin_id) {
           return array("state" => 0, "message" => self::showType($type).":无符合业务的审核或批准或执行人员");
        }
        //提醒
        if($model->verify_admin_id){
            $model->status = self::STATUS_APPLY_VERIFY;
            BusinessRemind::addRemind($model->id, self::showTypeUrl($type), $model->status, $model->verify_admin_id, $model->name.'需要您的审核');
            $model->save();
            return array("state" => 1);
        } 
        if($model->approval_admin_id){
            $model->status = self::STATUS_APPLY_APPROVAL;
            BusinessRemind::addRemind($model->id, self::showTypeUrl($type), $model->status, $model->approval_admin_id, $model->name.'需要您的批准');
            $model->save();
            return array("state" => 1);
        } 
        if($model->operation_admin_id){
            $model->status = self::STATUS_APPLY_FINISH;
            BusinessRemind::addRemind($model->id, self::showTypeUrl($type), $model->status, $model->operation_admin_id, $model->name.'需要您的执行');
            $model->save();
            return array("state" => 1);
        }
        $model->status = self::STATUS_FINISH;
        $model->operation_admin_id = Yii::$app->user->getId();
        $model->operation_time = date("Y-m-d H:i:s");
//        if(method_exists($model, "Finish")){
//            $result = $model->Finish();
//            if(!$result["state"]) {
//                return $result;
//            }
//        }
        $model->save();
        return array("state" => 1);
    }

    /**
     * 获取流程的下一步操作
     * @param type $type 流程类型
     * @param type $data 流程对象
     * @param type $finishType 操作按钮样式
     * @return string
     */
    public static function showNextStepByInfo($type, $data, $finishType = "get-update-reload") {
        if (in_array($data->status, [self::STATUS_FINISH, self::STATUS_VERIFY_REJECT, self::STATUS_APPROVAL_REJECT, self::STATUS_FINISH_REJECT, self::STATUS_HANG_UP])) {
            return ["nextStep" => "无", "nextStepAdmin" => "无"];
        }
        $configItem = FlowConfig::findOne(["id" => $data->config_id]);
        if (!$configItem) {
            return ["nextStep" => "无", "nextStepAdmin" => "无"];
        }
        if($data->status == self::STATUS_APPLY_FINISH) {
            if($configItem->operation_role_id > 0 && $configItem->operation_name && $configItem->operation_department_id > 0) {
                if($data->timing_type == Flow::TIMING_TYPE_OPERATION){
                    $return = [
                        "nextStep" => "确定定时".$configItem->operation_name."通过",
                        "nextStepAdmin" => Admin::getNameById($data->operation_admin_id),
                    ];
                    if($data->operation_admin_id == Yii::$app->user->getId() || Admin::checkSupperAdmin() || Admin::checkFlowAdmin()){
                        $class = Admin::checkFlowAdmin() && $data->operation_admin_id != Yii::$app->user->getId() ? "authOperation" : "confirmPass";
                        $return["nextStepA"] = '<a class="button blue-button '.$class.'" confirm-url="'.Url::to([self::showTypeUrl($type) . '/finish', "id" => $data->id]).'" i="confirmPass">'.$return["nextStep"].'</a>';
                    }
                    return $return;
                } 
                if($data->timing_type == Flow::TIMING_TYPE_OPERATION_REJECT){
                    $return = [
                        "nextStep" => "确定".$configItem->operation_name."过期驳回",
                        "nextStepAdmin" => Admin::getNameById($data->operation_admin_id),
                    ];
                    if($data->operation_admin_id == Yii::$app->user->getId() || Admin::checkSupperAdmin() || Admin::checkFlowAdmin()){
                        $class = Admin::checkFlowAdmin() && $data->operation_admin_id != Yii::$app->user->getId() ? "authOperation" : "confirmReject";
                        $return["nextStepA"] = '<a class="button blue-button '.$class.'" confirm-url="'.Url::to([self::showTypeUrl($type) . '/reject', "id" => $data->id]).'" i="confirmReject">'.$return["nextStep"].'</a>';
                    }
                    return $return;
                }
                if($data->timing_type == Flow::TIMING_TYPE_UNION_REJECT){
                    $return = [
                        "nextStep" => "确定".$configItem->operation_name."联合驳回",
                        "nextStepAdmin" => Admin::getNameById($data->operation_admin_id),
                    ];
                    if($data->operation_admin_id == Yii::$app->user->getId() || Admin::checkSupperAdmin() || Admin::checkFlowAdmin()){
                        $class = Admin::checkFlowAdmin() && $data->operation_admin_id != Yii::$app->user->getId() ? "authOperation" : "unionReject";
                        $return["nextStepA"] = '<a class="button blue-button '.$class.'" confirm-url="'.Url::to([self::showTypeUrl($type) . '/reject', "id" => $data->id]).'" i="unionReject">'.$return["nextStep"].'</a>';
                    }
                    return $return;
                }
                $return = [
                    "nextStep" => $configItem->operation_name,
                    "nextStepAdmin" => Admin::getNameById($data->operation_admin_id),
                ];
                if($data->operation_admin_id == Yii::$app->user->getId() || Admin::checkSupperAdmin() || Admin::checkFlowAdmin()){
                    $class = Admin::checkFlowAdmin() && $data->operation_admin_id != Yii::$app->user->getId() ? "authOperation" : "operation";
                    $rejectClass = Admin::checkFlowAdmin() && $data->operation_admin_id != Yii::$app->user->getId() ? "authOperation" : "reject";
                    $return["nextStepA"] = '<a class="button blue-button '.$class.'" '.$finishType.'="'.Url::to([self::showTypeUrl($type) . '/finish', "id" => $data->id]).'" i="operation">'.$configItem->operation_name.'</a> | '
                    .'<a class="button blue-button '.$rejectClass.'" reject_href="'.Url::to([self::showTypeUrl($type) . '/reject', "id" => $data->id]).'"  i="reject">'.$configItem->operation_name.'驳回</a> | ';
                }
                return $return;
            } 
        } else if($data->status == self::STATUS_APPLY_APPROVAL) {
            if ($configItem->approval_role_id > 0 && $configItem->approval_name && $configItem->approval_department_id > 0) {
                if($data->timing_type == Flow::TIMING_TYPE_APPROVAL){
                    $return = [
                        "nextStep" => "确定定时".$configItem->approval_name."通过",
                        "nextStepAdmin" => Admin::getNameById($data->approval_admin_id),
                    ];
                    if($data->approval_admin_id == Yii::$app->user->getId() || Admin::checkSupperAdmin() || Admin::checkFlowAdmin()){
                        $class = Admin::checkFlowAdmin() && $data->approval_admin_id != Yii::$app->user->getId() ? "authOperation" : "confirmPass";
                        $return["nextStepA"] = '<a class="button blue-button '.$class.'" confirm-url="'.Url::to([self::showTypeUrl($type) . '/approval', "id" => $data->id]).'" i="confirmPass">'.$return["nextStep"].'</a>';
                    }
                    return $return;
                } 
                if($data->timing_type == Flow::TIMING_TYPE_APPROVAL_REJECT){
                    $return = [
                        "nextStep" => "确定".$configItem->approval_name."过期驳回",
                        "nextStepAdmin" => Admin::getNameById($data->approval_admin_id),
                    ];
                    if($data->approval_admin_id == Yii::$app->user->getId() || Admin::checkSupperAdmin() || Admin::checkFlowAdmin()){
                        $class = Admin::checkFlowAdmin() && $data->approval_admin_id != Yii::$app->user->getId() ? "authOperation" : "confirmReject";
                        $return["nextStepA"] = '<a class="button blue-button '.$class.'" confirm-url="'.Url::to([self::showTypeUrl($type) . '/reject', "id" => $data->id]).'" i="confirmReject">'.$return["nextStep"].'</a>';
                    }
                    return $return;
                }
                if($data->timing_type == Flow::TIMING_TYPE_UNION_REJECT){
                    $return = [
                        "nextStep" => "确定".$configItem->approval_name."联合驳回",
                        "nextStepAdmin" => Admin::getNameById($data->approval_admin_id),
                    ];
                    if($data->approval_admin_id == Yii::$app->user->getId() || Admin::checkSupperAdmin() || Admin::checkFlowAdmin()){
                        $class = Admin::checkFlowAdmin() && $data->approval_admin_id != Yii::$app->user->getId() ? "authOperation" : "unionReject";
                        $return["nextStepA"] = '<a class="button blue-button '.$class.'" confirm-url="'.Url::to([self::showTypeUrl($type) . '/reject', "id" => $data->id]).'" i="unionReject">'.$return["nextStep"].'</a>';
                    }
                    return $return;
                }
                $return = [
                    "nextStep" => $configItem->approval_name,
                    "nextStepAdmin" => Admin::getNameById($data->approval_admin_id),
                ];
                if($data->approval_admin_id == Yii::$app->user->getId() || Admin::checkSupperAdmin() || Admin::checkFlowAdmin()) {
                    $class = Admin::checkFlowAdmin() && $data->approval_admin_id != Yii::$app->user->getId() ? "authOperation" : "operation";
                    $rejectClass = Admin::checkFlowAdmin() && $data->approval_admin_id != Yii::$app->user->getId() ? "authOperation" : "reject";
                    $return["nextStepA"] = '<a class="button blue-button '.$class.'" '.$finishType.'="'.Url::to([self::showTypeUrl($type) . '/approval', "id" => $data->id]).'" i="operation">'.$configItem->approval_name.'</a> | '
                    .'<a class="button blue-button '.$rejectClass.'" reject_href="'.Url::to([self::showTypeUrl($type) . '/reject', "id" => $data->id]).'" i="reject">'.$configItem->approval_name.'驳回</a> | ';
                }
                return $return;
            } 
        } else if($data->status == self::STATUS_APPLY_VERIFY){
            if ($configItem->verify_role_id > 0 && $configItem->verify_name && $configItem->verify_department_id > 0) {
                if($data->timing_type == Flow::TIMING_TYPE_VERIFY){
                    $return = [
                        "nextStep" => "确定定时".$configItem->verify_name."通过",
                        "nextStepAdmin" => Admin::getNameById($data->verify_admin_id),
                    ];
                    if($data->verify_admin_id == Yii::$app->user->getId() || Admin::checkSupperAdmin() || Admin::checkFlowAdmin()){
                        $class = Admin::checkFlowAdmin() && $data->verify_admin_id != Yii::$app->user->getId() ? "authOperation" : "confirmPass";
                        $return["nextStepA"] = '<a class="button blue-button '.$class.'" confirm-url="'.Url::to([self::showTypeUrl($type) . '/verify', "id" => $data->id]).'" i="confirmPass">'.$return["nextStep"].'</a>';
                    }
                    return $return;
                } 
                if($data->timing_type == Flow::TIMING_TYPE_VERIFY_REJECT){
                    $return = [
                        "nextStep" => "确定".$configItem->verify_name."过期驳回",
                        "nextStepAdmin" => Admin::getNameById($data->verify_admin_id),
                    ];
                    if($data->verify_admin_id == Yii::$app->user->getId() || Admin::checkSupperAdmin() || Admin::checkFlowAdmin()){
                        $class = Admin::checkFlowAdmin() && $data->verify_admin_id != Yii::$app->user->getId() ? "authOperation" : "confirmReject";
                        $return["nextStepA"] = '<a class="button blue-button '.$class.'" confirm-url="'.Url::to([self::showTypeUrl($type) . '/reject', "id" => $data->id]).'" i="confirmReject">'.$return["nextStep"].'</a>';
                    }
                    return $return;
                }
                if($data->timing_type == Flow::TIMING_TYPE_UNION_REJECT){
                    $return = [
                        "nextStep" => "确定".$configItem->verify_name."联合驳回",
                        "nextStepAdmin" => Admin::getNameById($data->verify_admin_id),
                    ];
                    if($data->verify_admin_id == Yii::$app->user->getId() || Admin::checkSupperAdmin() || Admin::checkFlowAdmin()){
                        $class = Admin::checkFlowAdmin() && $data->verify_admin_id != Yii::$app->user->getId() ? "authOperation" : "unionReject";
                        $return["nextStepA"] = '<a class="button blue-button '.$class.'" confirm-url="'.Url::to([self::showTypeUrl($type) . '/reject', "id" => $data->id]).'" i="unionReject">'.$return["nextStep"].'</a>';
                    }
                    return $return;
                }
                $return = [
                    "nextStep" => $configItem->verify_name,
                    "nextStepAdmin" => Admin::getNameById($data->verify_admin_id),
                ];
                if($data->verify_admin_id == Yii::$app->user->getId() || Admin::checkSupperAdmin() || Admin::checkFlowAdmin()) {
                    $class = Admin::checkFlowAdmin() && $data->verify_admin_id != Yii::$app->user->getId() ? "authOperation" : "operation";
                    $rejectClass = Admin::checkFlowAdmin() && $data->verify_admin_id != Yii::$app->user->getId() ? "authOperation" : "reject";
                    $return["nextStepA"] = '<a class="button blue-button '.$class.'"  '.$finishType.'="'.Url::to([self::showTypeUrl($type) . '/verify', "id" => $data->id]).'" i="operation">'.$configItem->verify_name.'</a> | '
                    .'<a class="button blue-button '.$rejectClass.'" reject_href="'.Url::to([self::showTypeUrl($type) . '/reject', "id" => $data->id]).'" i="reject">'.$configItem->verify_name.'驳回</a> | ';
                }
                return $return;
            }
        }            
        return ["nextStep" => "无", "nextStepAdmin" => "无"];
    }
    
    /**
     * 获取业务流程所有的操作步骤
     * @param type $type 业务流程类型
     * @param type $data 业务流程对象
     * @return int
     */
    public static function showAllStep($type, $data) {
        $configItem = FlowConfig::findOne(["id" => $data->config_id]);
        if (!$configItem) {
            return [];
        }
        $result = [];
        if ($configItem->verify_role_id > 0 && $configItem->verify_name && $configItem->verify_department_id > 0) {
            if(($data->verify_admin_id == Yii::$app->user->getId() || Admin::checkSupperAdmin()) && $data->status == self::STATUS_APPLY_VERIFY) {
                $result["verify"]["state"] = 1;
            } else {
                $result["verify"]["state"] = 0;
            }
            if($data->timing_type == Flow::TIMING_TYPE_VERIFY){
                $result["verify"]["stepName"] = "确定定时".$configItem->verify_name."通过";
                return $result;
            } else if($data->timing_type == Flow::TIMING_TYPE_VERIFY_REJECT){
                $result["verify"]["stepName"] = "确定".$configItem->verify_name."过期驳回";
                return $result;
            } else if($data->timing_type == Flow::TIMING_TYPE_UNION_REJECT){
                $result["verify"]["stepName"] = "确定".$configItem->verify_name."联合驳回";
                return $result;
            } else {
                $result["verify"]["stepName"] = $configItem->verify_name;
            }
        }
        if($configItem->approval_role_id > 0 && $configItem->approval_name && $configItem->approval_department_id > 0) {
            if(($data->approval_admin_id == Yii::$app->user->getId() || Admin::checkSupperAdmin()) && $data->status == self::STATUS_APPLY_APPROVAL) {
                $result["approval"]["state"] = 1;
            } else {
                $result["approval"]["state"] = 0;
            }
            if($data->timing_type == Flow::TIMING_TYPE_APPROVAL){
                $result["approval"]["stepName"] = "确定定时".$configItem->approval_name."通过";
                return $result;
            } else if($data->timing_type == Flow::TIMING_TYPE_APPROVAL_REJECT){
                $result["approval"]["stepName"] = "确定".$configItem->approval_name."过期驳回";
                return $result;
            } else if($data->timing_type == Flow::TIMING_TYPE_UNION_REJECT){
                $result["approval"]["stepName"] = "确定".$configItem->approval_name."联合驳回";
                return $result;
            } else {
                $result["approval"]["stepName"] = $configItem->approval_name;
            }
        } 
        if($configItem->operation_role_id > 0 && $configItem->operation_name && $configItem->operation_department_id > 0) {
            if(($data->operation_admin_id == Yii::$app->user->getId() || Admin::checkSupperAdmin()) && $data->status == self::STATUS_APPLY_FINISH) {
                $result["operation"]["state"] = 1;
            } else {
                $result["operation"]["state"] = 0;
            }
            if($data->timing_type == Flow::TIMING_TYPE_OPERATION){
                $result["operation"]["stepName"] = "确定定时".$configItem->operation_name."通过";
                return $result;
            } else if($data->timing_type == Flow::TIMING_TYPE_OPERATION_REJECT){
                $result["operation"]["stepName"] = "确定".$configItem->operation_name."过期驳回";
                return $result;
            } else if($data->timing_type == Flow::TIMING_TYPE_UNION_REJECT){
                $result["operation"]["stepName"] = "确定".$configItem->operation_name."联合驳回";
                return $result;
            } else {
                $result["operation"]["stepName"] = $configItem->operation_name;
            }
        } 
        return $result;
    }

    /**
     * 审核操作
     * @param integer $type 操作类型
     * @param object $model 操作模块
     */
    public static function Verify($type, $model, $remark = "") 
    {
        $configItem = FlowConfig::findOne(["id" => $model->config_id]);
        if(!$configItem) {
            return ["error" => 1, "message" => "没有审核操作1"];
        }
        if(!($configItem->verify_role_id > 0 && $configItem->verify_name && $configItem->verify_department_id > 0)){
            return ["error" => 1, "message" => "没有审核操作2"];
        }
        if($model->status != self::STATUS_APPLY_VERIFY) {
            return ["error" => 1, "message" => "操作状态错误"];
        }
        if($model->verify_admin_id != Yii::$app->user->getId() && !Admin::checkSupperAdmin() && !Admin::checkFlowAdmin()) {
            return ["error" => 1, "message" => "没有权限操作"];
        }
        $transaction = Yii::$app->db->beginTransaction();
        try {
            BusinessRemind::disposeRemind($model->id, self::showTypeUrl($type), $model->status);
            if($configItem->approval_role_id > 0 && $configItem->approval_name && $configItem->approval_department_id > 0){
                $model->status = self::STATUS_APPLY_APPROVAL;
                $model->verify_time = date("Y-m-d H:i:s");
                $item = BusinessAll::findOne(["business_id" => $model->id, "business_type" => $type]);
                if($item) {
                    $item->status = self::STATUS_APPLY_APPROVAL;
                    $item->verify_time = date("Y-m-d H:i:s");
                    $item->verify_admin_id = Yii::$app->user->getId();
                    if(!$item->save()) {
                        $transaction->rollBack();
                        return ["error" => 1, "message" => $item->getFirstErrors()];
                    }
                }
                $remarkResult = CommonRemark::addCommonRemark($model->id, $type, $remark, CommonRemark::TYPE_VERIFY);
                if(!$remarkResult["state"]) {
                    $transaction->rollBack();
                    return ["error" => 1, "message" => $remarkResult["message"]];
                }
                BusinessRemind::addRemind($model->id, self::showTypeUrl($type), $model->status, $model->approval_admin_id, $model->name.'需要您的批准');
            } else if($configItem->operation_role_id > 0 && $configItem->operation_name && $configItem->operation_department_id > 0) {
                $model->status = self::STATUS_APPLY_FINISH;
                $model->approval_time = date("Y-m-d H:i:s");
                $item = BusinessAll::findOne(["business_id" => $model->id, "business_type" => $type]);
                if($item) {
                    $item->status = self::STATUS_APPLY_FINISH;
                    $item->approval_time = date("Y-m-d H:i:s");
                    $item->approval_admin_id = Yii::$app->user->getId();
                    if(!$item->save()) {
                        $transaction->rollBack();
                        return ["error" => 1, "message" => $item->getFirstErrors()];
                    }
                }
                $remarkResult = CommonRemark::addCommonRemark($model->id, $type, $remark, CommonRemark::TYPE_VERIFY);
                if(!$remarkResult["state"]) {
                    $transaction->rollBack();
                    return ["error" => 1, "message" => $remarkResult["message"]];
                }
                BusinessRemind::addRemind($model->id, self::showTypeUrl($type), $model->status, $model->operation_admin_id, $model->name.'需要您的执行');
            } else {
                $model->status = self::STATUS_FINISH;
                $model->operation_admin_id = Yii::$app->user->getId();
                $model->operation_time = date("Y-m-d H:i:s");
                if(method_exists($model, "Finish")){
                    $result = $model->Finish();
                    if(!$result["state"]) {
                        $transaction->rollBack();
                        return ["error" => 1, "message" => $result["message"]];
                    }
                }
                $item = BusinessAll::findOne(["business_id" => $model->id, "business_type" => $type]);
                if($item) {
                    $item->status = self::STATUS_FINISH;
                    $item->operation_time = date("Y-m-d H:i:s");
                    $item->operation_admin_id = Yii::$app->user->getId();
                    if(!$item->save()) {
                        $transaction->rollBack();
                        return ["error" => 1, "message" => $item->getFirstErrors()];
                    }
                }
                $remarkResult = CommonRemark::addCommonRemark($model->id, $type, $remark, CommonRemark::TYPE_VERIFY);
                if(!$remarkResult["state"]) {
                    $transaction->rollBack();
                    return ["error" => 1, "message" => $remarkResult["message"]];
                }
            }
            $model->timing_type = Flow::TIMING_TYPE_NONE;
            if(!$model->save()){
                return ["error" => 1, "message" => $model->getFirstErrors()];
            }
            AdminLog::addLog(self::showTypeUrl($type), self::showType($type)."审核通过：".$model->id);
            $transaction->commit();
            return ["error" => 0, "message" => "操作成功"];
        } catch (Exception $exc) {
            $transaction->rollBack();
            return ["error" => 1, "message" => $exc->getTraceAsString()];
        }        
    }
    
    /**
     * 批准操作
     * @param integer $type 操作类型
     * @param object $model 操作模块
     */
    public static function Approval($type, $model, $remark = "")
    {
        $configItem = FlowConfig::findOne(["id" => $model->config_id]);
        if(!$configItem) {
            return ["error" => 1, "message" => "没有批准操作"];
        }
        if(!($configItem->approval_role_id > 0 && $configItem->approval_name && $configItem->approval_department_id > 0)){
            return ["error" => 1, "message" => "没有批准操作"];
        }
        if(!in_array($model->status, [self::STATUS_APPLY_VERIFY, self::STATUS_APPLY_APPROVAL])) {
            return ["error" => 1, "message" => "操作状态错误"];
        }
        if($model->approval_admin_id != Yii::$app->user->getId() && !Admin::checkSupperAdmin() && !Admin::checkFlowAdmin()) {
            return ["error" => 1, "message" => "没有权限操作"];
        }
        $transaction = Yii::$app->db->beginTransaction();
        try {
            BusinessRemind::disposeRemind($model->id, self::showTypeUrl($type), $model->status);
            if($configItem->operation_role_id > 0 && $configItem->operation_name && $configItem->operation_department_id > 0) {
                $model->status = self::STATUS_APPLY_FINISH;
                $model->approval_time = date("Y-m-d H:i:s");
                $item = BusinessAll::findOne(["business_id" => $model->id, "business_type" => $type]);
                if($item) {
                    $item->status = self::STATUS_APPLY_FINISH;
                    $item->approval_time = date("Y-m-d H:i:s");
                    $item->approval_admin_id = Yii::$app->user->getId();
                    if(!$item->save()) {
                        $transaction->rollBack();
                        return ["error" => 1, "message" => $item->getFirstErrors()];
                    }
                }
                $remarkResult = CommonRemark::addCommonRemark($model->id, $type, $remark, CommonRemark::TYPE_APPROVAL);
                if(!$remarkResult["state"]) {
                    $transaction->rollBack();
                    return ["error" => 1, "message" => $remarkResult["message"]];
                }
                BusinessRemind::addRemind($model->id, self::showTypeUrl($type), $model->status, $model->operation_admin_id, $model->name.'需要您的执行');
            } else {
                $model->status = self::STATUS_FINISH;
                $model->operation_time = date("Y-m-d H:i:s");
                $model->operation_admin_id = Yii::$app->user->getId();
                if(method_exists($model, "Finish")){
                    $result = $model->Finish();
                    if(!$result["state"]) {
                        $transaction->rollBack();
                        return ["error" => 1, "message" => $result["message"]];
                    }
                }
                $item = BusinessAll::findOne(["business_id" => $model->id, "business_type" => $type]);
                if($item) {
                    $item->status = self::STATUS_FINISH;
                    $item->operation_time = date("Y-m-d H:i:s");
                    $item->operation_admin_id = Yii::$app->user->getId();
                    if(!$item->save()) {
                        $transaction->rollBack();
                        return ["error" => 1, "message" => $item->getFirstErrors()];
                    }
                }
                $remarkResult = CommonRemark::addCommonRemark($model->id, $type, $remark, CommonRemark::TYPE_APPROVAL);
                if(!$remarkResult["state"]) {
                    $transaction->rollBack();
                    return ["error" => 1, "message" => $remarkResult["message"]];
                }
            }
            $model->timing_type = Flow::TIMING_TYPE_NONE;
            $model->save();
            AdminLog::addLog(self::showTypeUrl($type), self::showType($type)."批准通过：".$model->id);
            $transaction->commit();
            return ["error" => 0, "message" => "操作成功"];
        } catch (Exception $exc) {
            $transaction->rollBack();
            return ["error" => 1, "message" => $exc->getTraceAsString()];
        }
    }
    
    /**
     * 完成操作
     * @param integer $type 操作类型
     * @param object $model 操作模块
     */
    public static function Finish($type, $model, $remark = "") 
    {
        $configItem = FlowConfig::findOne(["id" => $model->config_id]);
        if(!$configItem) {
            return ["error" => 1, "message" => "没有执行操作1"];
        }
        if(!in_array($model->status, [self::STATUS_APPLY_VERIFY, self::STATUS_APPLY_APPROVAL, self::STATUS_APPLY_FINISH])) {
            return ["error" => 1, "message" => "操作状态错误"];
        }
        $transaction = Yii::$app->db->beginTransaction();
        try {
            BusinessRemind::disposeRemind($model->id, self::showTypeUrl($type), $model->status);
            $model->status = self::STATUS_FINISH;
            $model->operation_time = date("Y-m-d H:i:s");
            if(method_exists($model, "Finish")){
                $result = $model->Finish();
                if(!$result["state"]) {
                    $transaction->rollBack();
                    return ["error" => 1, "message" => is_array($result["message"]) ? reset($result["message"]) : $result["message"]];
                }
            }
            $model->timing_type = Flow::TIMING_TYPE_NONE;
            $model->save();
            $item = BusinessAll::findOne(["business_id" => $model->id, "business_type" => $type]);
            if($item) {
                $item->status = self::STATUS_FINISH;
                $item->operation_time = date("Y-m-d H:i:s");
                $item->operation_admin_id = Yii::$app->user->getId();
                if(!$item->save()) {
                    $transaction->rollBack();
                    return ["error" => 1, "message" => $item->getFirstErrors()];
                }
            }
            $remarkResult = CommonRemark::addCommonRemark($model->id, $type, $remark, CommonRemark::TYPE_OPERATOR);
            if(!$remarkResult["state"]) {
                $transaction->rollBack();
                return ["error" => 1, "message" => $remarkResult["message"]];
            }
            AdminLog::addLog(self::showTypeUrl($type), self::showType($type)."执行通过：".$model->id);
            $transaction->commit();
            return ["error" => 0, "message" => "操作成功"];
        } catch (Exception $exc) {
            $transaction->rollBack();
            return ["error" => 1, "message" => $exc->getTraceAsString()];
        }
    }
    
    /**
     * 驳回操作
     * @param integer $type 操作类型
     * @param object $model 操作模块
     * @param type $failCause 驳回理由
     */
    public static function Reject($type, $model, $failCause) 
    {
        if(!trim($failCause) && !is_numeric($failCause)) {
            return ["error" => 1, "message" => "驳回不能为空"];
        }
        $transaction = \Yii::$app->db->beginTransaction();
        try{
            BusinessRemind::disposeRemind($model->id, self::showTypeUrl($type), $model->status);
            if($model->status == self::STATUS_APPLY_VERIFY) {
                $model->status = self::STATUS_VERIFY_REJECT;
                $item = BusinessAll::findOne(["business_id" => $model->id, "business_type" => $type]);
                if($item) {
                    $item->status = self::STATUS_VERIFY_REJECT;
                    $item->verify_time = date("Y-m-d H:i:s");
                    $item->is_complete = 1;
                    if(!$item->save()) {
                        $transaction->rollBack();
                        return ["error" => 1, "message" => $item->getFirstErrors()];
                    }
                }
            }
            if($model->status == self::STATUS_APPLY_APPROVAL) {
                $model->status = self::STATUS_APPROVAL_REJECT;
                $item = BusinessAll::findOne(["business_id" => $model->id, "business_type" => $type]);
                if($item) {
                    $item->status = self::STATUS_APPROVAL_REJECT;
                    $item->approval_time = date("Y-m-d H:i:s");
                    $item->is_complete = 1;
                    if(!$item->save()) {
                        $transaction->rollBack();
                        return ["error" => 1, "message" => $item->getFirstErrors()];
                    }
                }
            }
            if($model->status == self::STATUS_APPLY_FINISH) {
                $model->status = self::STATUS_FINISH_REJECT;
                $item = BusinessAll::findOne(["business_id" => $model->id, "business_type" => $type]);
                if($item) {
                    $item->status = self::STATUS_FINISH_REJECT;
                    $item->operation_time = date("Y-m-d H:i:s");
                    $item->is_complete = 1;
                    if(!$item->save()) {
                        $transaction->rollBack();
                        return ["error" => 1, "message" => $item->getFirstErrors()];
                    }
                }
            }
            $model->failCause = $failCause;
            $model->timing_type = Flow::TIMING_TYPE_NONE;
            $model->save();
            $attr = $model->getAttributes();
            if(isset($attr["is_buckle"]) && $attr["is_buckle"]) {
                self::buckleReleaseStock($type, $model);
            }
            if(method_exists($model, "Reject")){
                $result = $model->Reject();
                if(!$result["state"]) {
                    $transaction->rollBack();
                    return ["error" => 1, "message" => is_array($result["message"]) ? reset($result["message"]) : $result["message"]];
                }
            }
            AdminLog::addLog(self::showTypeUrl($type), self::showType($type)."执行驳回：".$model->id);
            $transaction->commit();
            return ["error" => 0, "message" => "操作成功"];
        } catch (Exception $ex) {
            $transaction->rollBack();
            return ["error" => 1, "message" => $ex->getTraceAsString()];
        }
    }
    
    /**
     * 过期操作
     * @param integer $type 操作类型
     * @param object $model 操作模块
     * @param type $failCause 过期理由
     */
    public static function Overdue($type, $model, $failCause) 
    {
        if(!$failCause) {
            return ["error" => 1, "message" => "过期理由不能为空"];
        }
        $transaction = \Yii::$app->db->beginTransaction();
        try{
            BusinessRemind::disposeRemind($model->id, self::showTypeUrl($type), $model->status);
            $model->status = self::STATUS_HANG_UP;
            $item = BusinessAll::findOne(["business_id" => $model->id, "business_type" => $type]);
            if($item) {
                $item->status = self::STATUS_HANG_UP;
                $item->verify_time = date("Y-m-d H:i:s");
                if(!$item->save()) {
                    $transaction->rollBack();
                    return ["error" => 1, "message" => $item->getFirstErrors()];
                }
            }
            $model->failCause = $failCause;
            $model->save();
            $attr = $model->getAttributes();
            if(isset($attr["is_buckle"]) && $attr["is_buckle"]) {
                self::buckleReleaseStock($type, $model);
            }
            if(method_exists($model, "Reject")){
                $result = $model->Reject();
                if(!$result["state"]) {
                    $transaction->rollBack();
                    return ["error" => 1, "message" => is_array($result["message"]) ? reset($result["message"]) : $result["message"]];
                }
            }
            AdminLog::addLog(self::showTypeUrl($type), self::showType($type)."过期挂起：".$model->id);
            $transaction->commit();
            return ["error" => 0, "message" => "操作成功"];
        } catch (Exception $ex) {
            $transaction->rollBack();
            return ["error" => 1, "message" => $ex->getTraceAsString()];
        }
    }
    
    /**
     * 扣仓处理
     * @param type $type 类型
     * @param type $model 流程模块
     * @param type $productList 流程物料模块
     * @return type
     */
    public static function buckleStock($type, $model) {
        $productList = [];
        if($type == self::TYPE_TRANSFEFDEP) {
            $gatewayType = WarehouseGateway::GATEWAY_TYPE_TRANSFERDEP;
            $productList = WarehouseTransferDepProduct::findAll(["transfer_dep_id" => $model->id]);
        } else if($type == self::TYPE_MATERIALRETURN) {
            $gatewayType = WarehouseGateway::GATEWAY_TYPE_MATERIALRETURN;
            $productList = WarehouseMaterialReturnProduct::findAll(["material_return_id" => $model->id]);
        } else if($type == self::TYPE_WASTAGE) {
            $gatewayType = WarehouseGateway::GATEWAY_TYPE_WASTAGE;
            $productList = WarehouseWastageProduct::findAll(["wastage_id" => $model->id]);
        } else if($type == self::TYPE_BACK) {
            $gatewayType = WarehouseGateway::GATEWAY_TYPE_BACK;
            $productList = WarehouseBackProduct::findAll(["back_id" => $model->id]);
        } else if ($type == self::TYPE_TRANSFEF) {
            $gatewayType = WarehouseGateway::GATEWAY_TYPE_TRANSFER;
            $productList = WarehouseTransferProduct::findAll(["transfer_id" => $model->id]);
        } else if ($type == self::TYPE_CHECKOUT) {
            $gatewayType = WarehouseGateway::GATEWAY_TYPE_CHECKOUT;
            $productList = WarehouseCheckoutProduct::findAll(["checkout_id" => $model->id]);
        }
        $productIds = [];
        foreach ($productList as $productVal) {
            $stockOutItem = ProductStock::findOne($productVal->pstock_id);
            if(!$stockOutItem) {
                continue;
            }
            if($stockOutItem->number < $productVal->buying_number) {
                return ["state" => 0, "message" => "当前仓库的商品：".$productVal->name."的库存不足扣仓数量"];
            }
            $result = WarehouseGateway::addWarehouseGateway($model->warehouse_id, $productVal->product_id, WarehouseGateway::TYPE_OUT, $stockOutItem->number, $productVal->buying_number, $model->id, $gatewayType, $productVal->type, $stockOutItem->batches, '扣仓');
            if(!$result["state"]) {
                return $result;
            }
            $stockOutItem->number = $stockOutItem->number - $productVal->buying_number;
            $stockOutItem->save();
            if(in_array($type, [self::TYPE_MATERIALRETURN, self::TYPE_WASTAGE])) {
                //获取退货或耗损除例外物料的其他物料ID
                if($stockOutItem->type != WarehousePlanning::TYPE_EXCEPTION) {
                    $productIds[] = $stockOutItem->product_id;
                }
            }
        }        
        //验证物料是否已到库存警告
        $checkStockWarningResult = ProductStock::checkStockWarning($productIds);
        if(!$checkStockWarningResult["state"]) {
            return $checkStockWarningResult;
        }
        return ["state" => 1];
    }
    
    /**
     * 扣仓释放处理
     * @param type $type 类型
     * @param type $model 流程模块
     * @return type
     */
    public static function buckleReleaseStock($type, $model) {
        $productList = [];
        if($type == self::TYPE_TRANSFEFDEP) {
            $gatewayType = WarehouseGateway::GATEWAY_TYPE_TRANSFERDEP;
            $productList = WarehouseTransferDepProduct::findAll(["transfer_dep_id" => $model->id]);
        } else if($type == self::TYPE_MATERIALRETURN) {
            $gatewayType = WarehouseGateway::GATEWAY_TYPE_MATERIALRETURN;
            $productList = WarehouseMaterialReturnProduct::findAll(["material_return_id" => $model->id]);
        } else if($type == self::TYPE_WASTAGE) {
            $gatewayType = WarehouseGateway::GATEWAY_TYPE_WASTAGE;
            $productList = WarehouseWastageProduct::findAll(["wastage_id" => $model->id]);
        } else if($type == self::TYPE_BACK) {
            $gatewayType = WarehouseGateway::GATEWAY_TYPE_BACK;
            $productList = WarehouseBackProduct::findAll(["back_id" => $model->id]);
        } else if ($type == self::TYPE_TRANSFEF) {
            $gatewayType = WarehouseGateway::GATEWAY_TYPE_TRANSFER;
            $productList = WarehouseTransferProduct::findAll(["transfer_id" => $model->id]);
        } else if ($type == self::TYPE_CHECKOUT) {
            $gatewayType = WarehouseGateway::GATEWAY_TYPE_CHECKOUT;
            $productList = WarehouseCheckoutProduct::findAll(["checkout_id" => $model->id]);
        }
        foreach ($productList as $productVal) {
            $stockOutItem = ProductStock::findOne($productVal->pstock_id);
            if(!$stockOutItem) {
                continue;
            }
            $result = WarehouseGateway::addWarehouseGateway($model->warehouse_id, $productVal->product_id, WarehouseGateway::TYPE_IN, $stockOutItem->number, $productVal->buying_number, $model->id, $gatewayType, $productVal->type, $stockOutItem->batches,'扣仓释放');
            if(!$result["state"]) {
                return $result;
            }
            $stockOutItem->number = $stockOutItem->number + $productVal->buying_number;
            $stockOutItem->save();
        }
        return ["state" => 1];
    }
}
