<?php

namespace common\models\base;

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
class ProductInvoicingSaleInfo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ProductInvoicingSaleInfo';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['invoicing_sale_id', 'product_id', 'name', 'product_number', 'buying_number', 'supplier_id', 'supplier_product_id', 'num', 'pstock_id'], 'required'],
            [['invoicing_sale_id', 'product_id', 'product_number', 'buying_number', 'supplier_id', 'supplier_product_id', 'material_type', 'status', 'pstock_id'], 'integer'],
            [['purchase_price', 'sale_price', 'total_amount'], 'number'],
            [['name'], 'string', 'max' => 255],
            [['num', 'spec', 'unit', 'batches'], 'string', 'max' => 40],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'invoicing_sale_id' => 'Invoicing Sale ID',
            'product_id' => 'Product ID',
            'name' => 'Name',
            'purchase_price' => 'Purchase Price',
            'sale_price' => 'Sale Price',
            'product_number' => 'Product Number',
            'buying_number' => '收银订单数量',
            'total_amount' => 'Total Amount',
            'supplier_id' => 'Supplier ID',
            'supplier_product_id' => 'Supplier Product ID',
            'num' => 'Num',
            'spec' => 'Spec',
            'unit' => 'Unit',
            'material_type' => 'Material Type',
            'status' => 'Status',
            'pstock_id' => '库存ID',
            'batches' => '库存批次号',
        ];
    }
}
