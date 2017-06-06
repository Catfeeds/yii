<?php
namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\BaseActiveRecord;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use libs\Utils;

use common\models\WarehousePlanning;
use common\models\AdminLog;
use common\models\OrderProcurement;
use libs\common\Flow;
use common\models\WarehouseSaleProduct;
use common\models\Warehouse;
use common\models\DepartmentBalanceLog;
use common\models\BusinessAll;
use common\models\WarehouseBuyingProduct;


/**
 * This is the model class for table "WarehouseSale".
 *
 * @property integer $id
 * @property string $name
 * @property string $sn
 * @property integer $sale_check_id
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
 * @property integer $timing_type
 */
class WarehouseSale extends namespace\base\WarehouseSale
{
    public $time;
    public $expen;
    public $income;
    public $year;
    /**
     * 设置默认值
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
     * 获取记录支付方式
     */
    public function showPayment() 
    {
        return isset(WarehousePlanning::$_paymentAll[$this->payment]) ? WarehousePlanning::$_paymentAll[$this->payment] : "未知".$this->payment;
    }
    
    /**
     * 添加新的仓库销售记录
     * @param array $productList 物料销售列表
     * @return type
     */
    public function addSale($productList)
    {
        foreach ($productList as $warehouseId => $productVal) {
            $warehouseItem = Warehouse::findOne($warehouseId);
            if(!$warehouseItem) {
                return ["state" => 0, "message" => "销售仓库未知"];
            }
            $model = new WarehouseSale();
            $model->name = $warehouseItem->name;
            $model->sn = Utils::generateSn(Flow::TYPE_SALE);
            $model->total_amount = 0;
            $model->department_id = $warehouseItem->department_id;
            $model->warehouse_id = $warehouseId;
            $model->create_admin_id = Yii::$app->user->getId();
            $model->verify_admin_id = 0;
            $model->verify_time = date("Y-m-d H:i:s");
            $model->approval_admin_id = 0;
            $model->approval_time = date("Y-m-d H:i:s");
            $model->operation_admin_id = 0;
            $model->operation_time = date("Y-m-d H:i:s");
            $model->status = Flow::STATUS_APPLY_VERIFY;
            $model->create_time = date("Y-m-d H:i:s");
            $model->config_id = 0;
            if(!$model->save()) {
                return ["state" => 0, "message" => $model->getFirstErrors()."1"];
            }
            $totalAmount = 0;
            $meterialType = $supplier = array();
            foreach ($productVal as $pstock_id => $num) {
                $stockItem = ProductStock::findOne($pstock_id);
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
                $productModel->purchase_price = $stockItem->purchase_price;
                $productModel->sale_price = $stockItem->type == WarehousePlanning::TYPE_EXCEPTION ? $productItem->purchase_price : $productItem->sale_price;
                $productModel->product_number = $num["product_number"];
                $productModel->buying_number = $num["buying_number"];
                $productModel->total_amount = $productModel->sale_price * $productModel->buying_number;
                $productModel->supplier_id = $productItem->supplier_id;
                $productModel->supplier_product_id = $productItem->supplier_product_id;
                $productModel->num = $stockItem->type == WarehousePlanning::TYPE_EXCEPTION ? $productItem->num : $productItem->barcode;
                $productModel->spec = $productItem->spec;
                $productModel->unit = $productItem->unit;
                $productModel->material_type = $stockItem->type == WarehousePlanning::TYPE_EXCEPTION ? $productItem->material_type : $productItem->product_category_id;
                $productModel->warehouse_id = $warehouseId;
                $productModel->type = $stockItem->type;
                $productModel->pstock_id = $pstock_id;
                $productModel->batches = $stockItem->batches;
                if(!$productModel->save()) {
                    return array("state" => 0, "message" => $productModel->getFirstErrors());
                }
                $meterialType[] = $productModel->material_type;
                $supplier[] = $productModel->supplier_id;
                $totalAmount += $productModel->total_amount;
            }
            $date = date("m", strtotime($model->create_time));
            $areaId = 0;
            $result = Flow::confirmFollowAdminId(Flow::TYPE_SALE, $model, $totalAmount, $date, $areaId, $supplier, $meterialType);
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
        }
        return array("state" => 1);
    }
    
    /**
     * 完成
     */
    public function Finish()
    {
        $saleCheck = BusinessAll::findOne(["business_id" => $this->sale_check_id, "business_type" => Flow::TYPE_SALE_CHECK]);
        if($saleCheck) {
            $saleCheck->is_complete = 1;
            $saleCheck->save();
        }
        $saleItem = BusinessAll::findOne(["business_id" => $this->id, "business_type" => Flow::TYPE_SALE]);
        if($saleItem) {
            $saleItem->is_complete = 1;
            $saleItem->save();
        }
        $isCheck = CheckFlow::amountIsCheckFlow($this->department_id);
        if($isCheck) {
            return array("state" => 0, "message" => "部门处于资金盘点中，无法执行销存入账");
        }
        $model = new DepartmentBalanceLog();
        $result = $model->addDepartmentBalanceLog($this->department_id, $this->id, DepartmentBalanceLog::BUSINESS_TYPE_SALE, DepartmentBalanceLog::MOD_IN, $this->total_amount, '库存销售');
        if(!$result["state"]) {
            return $result;
        }
        return ["state" => 1];
    }
    
    /**
     * 驳回方法
     */
    public function Reject() {
        $saleCheckItem = SaleCheck::findOne($this->sale_check_id);
        $saleCheckItem->status = Flow::STATUS_UNION_REJECT;
        $saleCheckItem->failCause = "销存入账联合驳回销存盘点";
        if(!$saleCheckItem->save()) {
            $message = $saleCheckItem->getFirstErrors();
            return ["state" => 0, "message" => reset($message)];
        }
        $saleCheck = BusinessAll::findOne(["business_id" => $saleCheckItem->id, "business_type" => Flow::TYPE_SALE_CHECK]);
        if($saleCheck) {
            $saleCheck->status = Flow::STATUS_UNION_REJECT;
            $saleCheck->is_complete = 1;
            $saleCheck->save();
        }
        return ["state" => 1];
    }
}
