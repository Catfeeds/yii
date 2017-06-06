<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\BaseActiveRecord;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

use common\models\WarehouseBuying;
use common\models\WarehouseCheck;
use common\models\WarehouseCheckout;
use common\models\WarehouseMaterialReturn;
use common\models\WarehouseTransfer;
use common\models\WarehouseTransferDep;
use common\models\WarehouseWastage;
use common\models\ProductInvoicingSale;
use common\models\WarehouseBack;
use common\models\SaleCheck;

/**
 * This is the model class for table "WarehouseGateway".
 *
 * @property integer $id
 * @property integer $warehouse_id
 * @property integer $product_id
 * @property integer $type
 * @property integer $stock
 * @property integer $num
 * @property integer $gateway_no
 * @property integer $gateway_type
 * @property string $create_time
 * @property string $comment
 * @property integer $product_type
 * @property string $batches
 */
class WarehouseGateway extends namespace\base\WarehouseGateway
{
    /**
     * 入库
     */
    const TYPE_IN = 1;
    /**
     * 出库 
     */
    const TYPE_OUT = 2;
    /**
     * 采购入库
     */
    const GATEWAY_TYPE_BUYING = 1;
    /**
     * 盘点
     */
    const GATEWAY_TYPE_CHECK = 2;
    /**
     * 出库
     */
    const GATEWAY_TYPE_CHECKOUT = 3;
    /**
     * 退货
     */
    const GATEWAY_TYPE_MATERIALRETURN = 4;
    /**
     * 调仓
     */
    const GATEWAY_TYPE_TRANSFER = 5;
    /**
     * 转货
     */
    const GATEWAY_TYPE_TRANSFERDEP = 6;
    /**
     * 耗损
     */
    const GATEWAY_TYPE_WASTAGE = 7;
    /**
     * 销售
     */
    const GATEWAY_TYPE_SALE = 8 ;
    /**
     * 退仓
     */
    const GATEWAY_TYPE_BACK = 9 ;
    /**
     * 销存盘点
     */
    const GATEWAY_TYPE_SALECHECK = 10;
       
    private static $_typeAll = [
        self::TYPE_IN => "入库",
        self::TYPE_OUT => "出库",
    ];
    
    private static $_gatewayTypeAll = [
        self::GATEWAY_TYPE_BUYING => "采购入库",
        self::GATEWAY_TYPE_CHECK => "盘点",
        self::GATEWAY_TYPE_CHECKOUT => "出库",
        self::GATEWAY_TYPE_MATERIALRETURN => "退货",
        self::GATEWAY_TYPE_TRANSFER => "调仓",
        self::GATEWAY_TYPE_TRANSFERDEP => "转货",
        self::GATEWAY_TYPE_WASTAGE => "耗损",
        self::GATEWAY_TYPE_SALE => "销售",
        self::GATEWAY_TYPE_BACK => "退仓",
        self::GATEWAY_TYPE_SALECHECK => "销存盘点",
    ];
    
    /**
     * 获取记录的类型
     */
    public function showType()
    {
        return isset(self::$_typeAll[$this->type]) ? self::$_typeAll[$this->type] : '未知'.$this->type ;
    }
   
    /**
     * 获取所有的类型
     */
    public static function getTypeSelectData()
    {
        return self::$_typeAll;
    }
    
    /**
     * 获取记录的操作类型
     */
    public function showGatewayType()
    {
        return isset(self::$_gatewayTypeAll[$this->gateway_type]) ? self::$_gatewayTypeAll[$this->gateway_type] : '未知'.$this->gateway_type ;
    }
   
    /**
     * 获取所有的操作类型
     */
    public static function getGatewayTypeSelectData()
    {
        return self::$_gatewayTypeAll;
    }
    
    /**
     * 添加仓库商品出入库记录
     * @param int $warehouse_id 仓库ID
     * @param int $product_id 物料ID
     * @param int $type 出入库类型
     * @param int $stock 当时库存
     * @param int $num  出入数量
     * @param string $gateway_no 操作单号
     * @param int $gateway_type 操作类型
     * @param int $product_type 物料类型
     * @param string $comment 备注
     */
    public static function addWarehouseGateway($warehouse_id, $product_id, $type, $stock, $num, $gateway_no, $gateway_type, $product_type, $batches, $comment = "")
    {
        $model = new WarehouseGateway();
        $model->warehouse_id = $warehouse_id;
        $model->product_id = $product_id;
        $model->type = $type;
        $model->stock = $stock;
        $model->num = $num;
        $model->gateway_no = $gateway_no;
        $model->gateway_type = $gateway_type;
        $model->create_time = date("Y-m-d H:i:s");
        $model->product_type = $product_type;
        $model->batches = $batches;
        $model->comment = $comment;
        if(!$model->validate()) {
            return array("state" => 0, "message" => $model->getFirstErrors());
        }
        $model->save();
        return array("state" => 1);
    }
    
    /**
     * 获取类型相对的模型
     */
    public function getModelByGatewayType() {
        switch ($this->gateway_type) {
            case self::GATEWAY_TYPE_BUYING:
                return WarehouseBuying::findOne($this->gateway_no);
            case self::GATEWAY_TYPE_CHECK:
                return CheckFlow::findOne($this->gateway_no);
            case self::GATEWAY_TYPE_CHECKOUT:
                return WarehouseCheckout::findOne($this->gateway_no);
            case self::GATEWAY_TYPE_MATERIALRETURN:
                return WarehouseMaterialReturn::findOne($this->gateway_no);
            case self::GATEWAY_TYPE_TRANSFER:
                return WarehouseTransfer::findOne($this->gateway_no);
            case self::GATEWAY_TYPE_TRANSFERDEP:
                return WarehouseTransferDep::findOne($this->gateway_no);
            case self::GATEWAY_TYPE_WASTAGE:
                return WarehouseWastage::findOne($this->gateway_no);
            case self::GATEWAY_TYPE_SALE:
                return ProductInvoicingSale::findOne($this->gateway_no);
            case self::GATEWAY_TYPE_BACK:
                return WarehouseBack::findOne($this->gateway_no);
            case self::GATEWAY_TYPE_SALECHECK:
                return SaleCheck::findOne($this->gateway_no);
        }
    }
}

