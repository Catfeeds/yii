<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "WarehouseBuyingProduct".
 *
 * @property integer $id
 * @property integer $product_id
 * @property integer $buying_id
 * @property string $name
 * @property double $price
 * @property double $purchase_price
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
 */
class WarehouseBuyingProduct extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'warehousebuyingproduct';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_id', 'buying_id', 'name', 'product_number', 'buying_number', 'supplier_id', 'supplier_product_id', 'num', 'material_type', 'warehouse_id'], 'required'],
            [['product_id', 'buying_id', 'product_number', 'buying_number', 'supplier_id', 'supplier_product_id', 'warehouse_id', 'status', 'material_type'], 'integer'],
            [['price', 'purchase_price', 'total_amount'], 'number'],
            [['name'], 'string', 'max' => 255],
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
            'buying_id' => 'Buying ID',
            'name' => 'Name',
            'price' => 'Price',
            'purchase_price' => 'Purchase Price',
            'product_number' => '采购数量',
            'buying_number' => '入库数量',
            'total_amount' => 'Total Amount',
            'supplier_id' => 'Supplier ID',
            'supplier_product_id' => 'Supplier Product ID',
            'num' => 'Num',
            'spec' => 'Spec',
            'unit' => 'Unit',
            'material_type' => 'Material Type',
            'warehouse_id' => 'Warehouse ID',
            'status' => 'Status',
        ];
    }
}
