<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\BaseActiveRecord;
use yii\db\Expression;
use libs\Utils;
use libs\common\Flow;
use common\models\WarehousePlanning;
use common\models\WarehouseProcurement;
use common\models\WarehouseBuying;
use common\models\WarehouseBack;
use common\models\WarehouseCheckout;
use common\models\WarehouseTransferDep;
use common\models\WarehouseTransfer;
use common\models\WarehouseMaterialReturn;
use common\models\WarehouseWastage;
use common\models\WarehouseCheck;
use common\models\OrderProcurement;
use common\models\Product;
use common\models\WarehouseSale;
use common\models\DepartmentBalanceLog;
use common\models\AbnormalBalance;
use common\models\OrderMaterialReturn;
use common\models\CheckPlanningFlow;
use common\models\CheckFlow;
use common\models\ProductUpdate;
use common\models\SaleCheck;
use common\models\Salesorder;


/**
 * This is the model class for table "BusinessAll".
 *
 * @property integer $id
 * @property integer $business_id
 * @property string $business_type
 * @property integer $department_id
 * @property integer $warehouse_id
 * @property string $name
 * @property string $sn
 * @property integer $create_admin_id
 * @property integer $verify_admin_id
 * @property string $verify_time
 * @property integer $approval_admin_id
 * @property string $approval_time
 * @property integer $operation_admin_id
 * @property string $operation_time
 * @property integer $status
 * @property string $create_time
 * @property integer $is_complete
 */
class BusinessAll extends namespace\base\BusinessAll
{   
    /**
     * 添加流程的历史记录
     * @param type $item 流程对象
     * @param type $type 流程类型
     * @return type
     */
    public function addBusiness($item, $type) {
        $this->business_id = $item->id;
        $this->business_type = $type;
        $this->warehouse_id = isset($item->warehouse_id) ? $item->warehouse_id : 0;
        $departmentId = isset($item->department_id) ? $item->department_id : 0;
        if(!isset($item->department_id) && isset($item->warehouse_id)) {
            $warehouseItem = Warehouse::findOne($item->warehouse_id);
            $departmentId = $warehouseItem ? $warehouseItem->department_id : 0;
        }
        if($type == Flow::TYPE_ABNORMAL_FUND){
            $checkMod = AbnormalBalance::checkModDepartment($item->mod);
            $departmentId = 0;
            if($checkMod["departmentList"]["expen"] > 0) {
                $departmentId = $item->department_id;
            }
            if($checkMod["departmentList"]["income"] > 0) {
                $departmentId = $item->income_department_id;
            }
        }
        $this->department_id = $departmentId;
        $this->name = $item->name;
        $this->sn = isset($item->sn) ? $item->sn : Utils::generateSn($type);
        $this->create_admin_id = $item->create_admin_id;
        $this->verify_admin_id = $item->verify_admin_id;
        $this->verify_time = $item->verify_time;
        $this->approval_admin_id = $item->approval_admin_id;        
        $this->approval_time = $item->approval_time;        
        $this->operation_admin_id = $item->operation_admin_id;        
        $this->operation_time = $item->operation_time;        
        $this->status = $type == Flow::TYPE_ADDPRODUCT ? $item->modify_status : $item->status;        
        $this->create_time = $item->create_time; 
        $isComplete = 0; 
        if(in_array($type, [Flow::TYPE_TRANSFEFDEP, Flow::TYPE_TRANSFEF, Flow::TYPE_WASTAGE, Flow::TYPE_ADDPRODUCT, 
                Flow::TYPE_ABNORMAL_FUND, Flow::TYPE_PRODUCT_UPDATE,Flow::TYPE_BACK, Flow::TYPE_CHECKOUT])) {
            $isComplete = 1;
        }
        $this->is_complete = $isComplete;
        if(!$this->save()) {
            return ["state" => 0, "message" => $this->getFirstErrors()];
        }
        return ["state" => 1];
    }
    
    /**
     * 通过模块ID和模块类型获取流程模块
     * @param type $businessType 模块类型
     * @param type $businessId 模块ID
     */
    public static function findModelByBusinessIdAndType($businessType, $businessId) {
        switch ($businessType) {
            case Flow::TYPE_PLANNING:
            case Flow::TYPE_PLANNING_ROUTINE:
            case Flow::TYPE_PLANNING_EXCEPTION:
                return WarehousePlanning::findOne($businessId);
            case Flow::TYPE_ORDER:
                return WarehouseProcurement::findOne($businessId);
            case Flow::TYPE_BUYING:
                return WarehouseBuying::findOne($businessId);
            case Flow::TYPE_BACK:
                return WarehouseBack::findOne($businessId);
            case Flow::TYPE_CHECKOUT:
                return WarehouseCheckout::findOne($businessId);
            case Flow::TYPE_TRANSFEFDEP:
                return WarehouseTransferDep::findOne($businessId);
            case Flow::TYPE_TRANSFEF:
                return WarehouseTransfer::findOne($businessId);
            case Flow::TYPE_MATERIALRETURN:
                return WarehouseMaterialReturn::findOne($businessId);
            case Flow::TYPE_WASTAGE:
                return WarehouseWastage::findOne($businessId);
//            case Flow::TYPE_CHECK:
//                return WarehouseCheck::findOne($businessId);
            case Flow::TYPE_ADDPRODUCT:
                return Product::findOne($businessId);
            case Flow::TYPE_ORDER_FINANCE:
                return OrderProcurement::findOne($businessId);
            case Flow::TYPE_SALE:
                return WarehouseSale::findOne($businessId);
//            case Flow::TYPE_FUND:
//                return DepartmentBalanceLog::findOne($businessId);
            case Flow::TYPE_ABNORMAL_FUND:
                return AbnormalBalance::findOne($businessId);
            case Flow::TYPE_ORDER_MATERIAL:
                return OrderMaterialReturn::findOne($businessId);
            case Flow::TYPE_CHECK_PLANNING:
            case Flow::TYPE_CHECK_DEPARTMENT:
            case Flow::TYPE_CHECK_WAREHOUSE:
                return CheckPlanningFlow::findOne($businessId);
            case Flow::TYPE_CHECK_PLANNING_PROOF:
            case Flow::TYPE_CHECK_DEPARTMENT_PROOF:
            case Flow::TYPE_CHECK_WAREHOUSE_PROOF:
                return CheckFlow::findOne($businessId);
            case Flow::TYPE_PRODUCT_UPDATE:
                return ProductUpdate::findOne($businessId);
            case Flow::TYPE_SALE_CHECK:
                return SaleCheck::findOne($businessId);
            default :
                return FALSE;
        }
    }
    
    /**
     * 通过模块类型获取流程模块数量
     * @param type $type 流程类型
     * @return boolean
     */
    public static function findNum($type) {
        $date = date("Y-m-d");
        switch ($type) {
            case Flow::TYPE_PLANNING:
            case Flow::TYPE_PLANNING_ROUTINE:
            case Flow::TYPE_PLANNING_EXCEPTION:
                return WarehousePlanning::find()->andWhere(["like", "create_time", $date])->count();
            case Flow::TYPE_ORDER:
                return WarehouseProcurement::find()->andWhere(["like", "create_time", $date])->count();
            case Flow::TYPE_BUYING:
                return WarehouseBuying::find()->andWhere(["like", "create_time", $date])->count();
            case Flow::TYPE_BACK:
                return WarehouseBack::find()->andWhere(["like", "create_time", $date])->count();
            case Flow::TYPE_CHECKOUT:
                return WarehouseCheckout::find()->andWhere(["like", "create_time", $date])->count();
            case Flow::TYPE_TRANSFEFDEP:
                return WarehouseTransferDep::find()->andWhere(["like", "create_time", $date])->count();
            case Flow::TYPE_TRANSFEF:
                return WarehouseTransfer::find()->andWhere(["like", "create_time", $date])->count();
            case Flow::TYPE_MATERIALRETURN:
                return WarehouseMaterialReturn::find()->andWhere(["like", "create_time", $date])->count();
            case Flow::TYPE_WASTAGE:
                return WarehouseWastage::find()->andWhere(["like", "create_time", $date])->count();
//            case Flow::TYPE_CHECK:
//                return WarehouseCheck::findOne($businessId);
            case Flow::TYPE_ADDPRODUCT:
                return Product::find()->andWhere(["like", "create_time", $date])->count();
            case Flow::TYPE_ORDER_FINANCE:
                return OrderProcurement::find()->andWhere(["like", "create_time", $date])->count();
            case Flow::TYPE_SALE:
                return WarehouseSale::find()->andWhere(["like", "create_time", $date])->count();
//            case Flow::TYPE_FUND:
//                return DepartmentBalanceLog::find()->andWhere(["like", "create_time", $date])->count();
            case Flow::TYPE_ABNORMAL_FUND:
                return AbnormalBalance::find()->andWhere(["like", "create_time", $date])->count();
            case Flow::TYPE_ORDER_MATERIAL:
                return OrderMaterialReturn::find()->andWhere(["like", "create_time", $date])->count();
            case Flow::TYPE_CHECK_PLANNING:
            case Flow::TYPE_CHECK_DEPARTMENT:
            case Flow::TYPE_CHECK_WAREHOUSE:
                return CheckPlanningFlow::find()->andWhere(["like", "create_time", $date])->count();
            case Flow::TYPE_CHECK_PLANNING_PROOF:
            case Flow::TYPE_CHECK_DEPARTMENT_PROOF:
            case Flow::TYPE_CHECK_WAREHOUSE_PROOF:
                return CheckFlow::find()->andWhere(["like", "create_time", $date])->count();
            case Flow::TYPE_PRODUCT_UPDATE:
                return ProductUpdate::find()->andWhere(["like", "create_time", $date])->count();
            case Flow::TYPE_SALE_CHECK:
                return SaleCheck::find()->andWhere(["like", "create_time", $date])->count();
           case Flow::TYPE_SALE_CHECK:
                return SaleCheck::find()->andWhere(["like", "create_time", $date])->count();
           case Flow::STATUS_CREATE_ORDER: //生成订单的添加
                return Salesorder::find()->andWhere(["like", "create_time", $date])->count();

            default :
                return FALSE;
        }
    }
}
