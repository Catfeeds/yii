<?php
namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\BaseActiveRecord;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
/**
 * This is the model class for table "WarehouseSaleProduct".
 *
 * @property integer $id
 * @property integer $product_id
 * @property integer $sale_id
 * @property string $name
 * @property double $purchase_price
 * @property double $sale_price
 * @property integer $product_number
 * @property integer $buying_number
 * @property double $total_amount
 * @property integer $supplier_id
 * @property integer $supplier_product_id
 * @property string $num
 * @property string $spec
 * @property string $unit
 * @property string $material_type
 * @property integer $warehouse_id
 * @property integer $status
 * @property integer $type
 * @property integer $pstock_id
 * @property string $batches
 */
class WarehouseSaleProduct extends namespace\base\WarehouseSaleProduct
{
    const STATUS_OK = 1;
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    BaseActiveRecord::EVENT_BEFORE_INSERT => 'status',
                ],
                'value' => self::STATUS_OK,
            ]
        ];
    }
}
