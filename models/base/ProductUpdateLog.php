<?php

namespace common\models\base;

use Yii;

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
class ProductUpdateLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ProductUpdateLog';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_id', 'product_category_id', 'barcode', 'supplier_id', 'supplier_product_id', 'num', 'create_time','update_product_id'], 'required'],
            [['product_id', 'product_category_id', 'supplier_id', 'supplier_product_id', 'material_type', 'inventory_warning', 'is_batches', 'update_product_id'], 'integer'],
            [['purchase_price', 'sale_price'], 'number'],
            [['name', 'barcode'], 'string', 'max' => 255],
            [['num', 'spec', 'unit'], 'string', 'max' => 40],
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
            'name' => 'Name',
            'product_category_id' => 'Product Category ID',
            'barcode' => 'Barcode',
            'purchase_price' => 'Purchase Price',
            'sale_price' => 'Sale Price',
            'supplier_id' => 'Supplier ID',
            'supplier_product_id' => 'Supplier Product ID',
            'num' => 'Num',
            'spec' => 'Spec',
            'unit' => 'Unit',
            'material_type' => 'Material Type',
            'inventory_warning' => 'Inventory Warning',
            'is_batches' => 'Is Batches',
            'create_time' => 'Create Time',
            'update_product_id' => 'Update Product ID',
        ];
    }
}
