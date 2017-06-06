<?php
namespace common\models;

use Yii;

/**
 * This is the model class for table "ProductInvoicingSaleInfo".
 *
 * @property integer $id
 * @property integer $invoicing_sale_id
 * @property integer $product_id
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
 * @property integer $material_type
 * @property integer $status
 * @property integer $pstock_id
 * @property string $batches
 */
class ProductInvoicingSaleInfo extends namespace\base\ProductInvoicingSaleInfo
{
    
}