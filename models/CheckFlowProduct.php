<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "CheckFlowProduct".
 *
 * @property integer $id
 * @property integer $check_flow_id
 * @property integer $pstock_id
 * @property string $batches
 * @property integer $product_id
 * @property string $name
 * @property double $purchase_price
 * @property double $sale_price
 * @property integer $product_number
 * @property integer $buying_number
 * @property double $total_amount
 * @property integer $supplier_id
 * @property integer $supplier_product_id
 * @property string $barcode
 * @property string $spec
 * @property string $unit
 * @property integer $material_type
 * @property integer $warehouse_id
 * @property integer $department_id
 * @property integer $status
 * @property integer $type
 */
class CheckFlowProduct extends namespace\base\CheckFlowProduct 
{
    
}
