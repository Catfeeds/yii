<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "SaleCheckProduct".
 *
 * @property integer $id
 * @property integer $product_id
 * @property integer $sale_check_id
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
 * @property integer $warehouse_id
 * @property integer $status
 * @property integer $type
 * @property integer $pstock_id
 * @property string $batches
 */
class SaleCheckProduct extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'salecheckproduct';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_id', 'sale_check_id', 'name', 'product_number', 'buying_number', 'supplier_id', 'supplier_product_id', 'num', 'warehouse_id'], 'required'],
            [['product_id', 'sale_check_id', 'product_number', 'buying_number', 'supplier_id', 'supplier_product_id', 'material_type', 'warehouse_id', 'status', 'type', 'pstock_id'], 'integer'],
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
            'product_id' => 'Product ID',
            'sale_check_id' => 'Sale Check ID',
            'name' => 'Name',
            'purchase_price' => 'Purchase Price',
            'sale_price' => 'Sale Price',
            'product_number' => 'Product Number',
            'buying_number' => 'Buying Number',
            'total_amount' => 'Total Amount',
            'supplier_id' => 'Supplier ID',
            'supplier_product_id' => 'Supplier Product ID',
            'num' => 'Num',
            'spec' => 'Spec',
            'unit' => 'Unit',
            'material_type' => 'Material Type',
            'warehouse_id' => 'Warehouse ID',
            'status' => 'Status',
            'type' => 'Type',
            'pstock_id' => 'Pstock ID',
            'batches' => 'Batches',
        ];
    }
}
