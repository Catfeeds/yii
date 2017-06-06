<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "OrderMaterialReturnProduct".
 *
 * @property integer $id
 * @property integer $product_id
 * @property integer $order_procurement_id
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
 * @property integer $material_type
 * @property integer $status
 * @property integer $type
 */
class OrderMaterialReturnProduct extends namespace\base\OrderMaterialReturnProduct
{
}
