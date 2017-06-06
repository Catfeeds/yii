<?php

namespace common\models;

use Yii;
use Exception;

/**
 * This is the model class for table "ProductUpdateLog".
 *
 * @property integer $id
 * @property integer $product_id
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
 * @property integer $is_batches
 * @property string $create_time
 * @property integer $update_product_id
 */
class ProductUpdateLog extends namespace\base\ProductUpdateLog
{
    
}
