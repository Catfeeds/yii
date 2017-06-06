<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "salesorderproduct".
 *
 * @property integer $id
 * @property integer $product_id
 * @property string $sale_order_id
 * @property string $name
 * @property double $price
 * @property double $sale_price
 * @property integer $product_number
 * @property double $total_amount
 * @property integer $supplier_id
 * @property integer $supplier_product_id
 * @property integer $warehouse_id
 * @property string $num
 * @property string $spec
 * @property string $unit
 * @property string $barcode
 * @property integer $material_type
 * @property integer $product_cate_id
 */
class Salesorderproduct extends  namespace  \base\Salesorderproduct
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'salesorderproduct';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_id', 'sale_order_id', 'name', 'product_number', 'supplier_id', 'supplier_product_id', 'num'], 'required'],
            [['product_id', 'product_number', 'supplier_id', 'supplier_product_id', 'warehouse_id', 'material_type', 'product_cate_id'], 'integer'],
            [['price', 'sale_price', 'total_amount'], 'number'],
            [['sale_order_id', 'num', 'spec', 'unit', 'barcode'], 'string', 'max' => 40],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'product_id' => 'Product ID',
            'sale_order_id' => 'Sale Order ID',
            'name' => 'Name',
            'price' => 'Price',
            'sale_price' => 'Sale Price',
            'product_number' => 'Product Number',
            'total_amount' => 'Total Amount',
            'supplier_id' => 'Supplier ID',
            'supplier_product_id' => 'Supplier Product ID',
            'warehouse_id' => 'Warehouse ID',
            'num' => 'Num',
            'spec' => 'Spec',
            'unit' => 'Unit',
            'barcode' => 'Barcode',
            'material_type' => 'Material Type',
            'product_cate_id' => 'Product Cate ID',
        ];
    }
}
