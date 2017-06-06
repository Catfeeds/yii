<?php
namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\BaseActiveRecord;
use yii\db\Expression;
use yii\base\Exception;
use common\models\Product;

use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "WarehousePlanningProduct".
 *
 * @property integer $id
 * @property integer $product_id
 * @property integer $planning_id
 * @property string $name
 * @property double $price
 * @property double $purchase_price
 * @property integer $product_number
 * @property double $total_amount
 * @property integer $supplier_id
 * @property integer $supplier_product_id
 * @property string $num
 * @property string $spec
 * @property string $unit
 * @property string $material_type
 * @property integer $status
 * @property integer $product_cate_id
 */
class WarehousePlanningProduct extends namespace\base\WarehousePlanningProduct
{
}
