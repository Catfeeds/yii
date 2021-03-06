<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "CombinationProduct".
 *
 * @property integer $id
 * @property integer $product_id
 * @property integer $order_template_id
 * @property string $name
 * @property double $price
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
 * @property integer $pstock_id
 * @property string $batches
 */
class CombinationProduct extends namespace\base\CombinationProduct
{
    
}

