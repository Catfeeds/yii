<?php

namespace common\models\base;

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
class CheckFlowProduct extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'checkflowproduct';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['check_flow_id', 'product_id', 'name', 'product_number', 'buying_number', 'supplier_id', 'supplier_product_id', 'barcode', 'warehouse_id', 'department_id'], 'required'],
            [['check_flow_id', 'pstock_id', 'product_id', 'product_number', 'buying_number', 'supplier_id', 'supplier_product_id', 'material_type', 'warehouse_id', 'department_id', 'status', 'type'], 'integer'],
            [['purchase_price', 'sale_price', 'total_amount'], 'number'],
            [['batches', 'barcode', 'spec', 'unit'], 'string', 'max' => 40],
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
            'check_flow_id' => 'Check Flow ID',
            'pstock_id' => 'Pstock ID',
            'batches' => 'Batches',
            'product_id' => 'Product ID',
            'name' => 'Name',
            'purchase_price' => 'Purchase Price',
            'sale_price' => 'Sale Price',
            'product_number' => 'Product Number',
            'buying_number' => 'Buying Number',
            'total_amount' => 'Total Amount',
            'supplier_id' => 'Supplier ID',
            'supplier_product_id' => 'Supplier Product ID',
            'barcode' => 'Barcode',
            'spec' => 'Spec',
            'unit' => 'Unit',
            'material_type' => 'Material Type',
            'warehouse_id' => 'Warehouse ID',
            'department_id' => 'Department ID',
            'status' => 'Status',
            'type' => 'Type',
        ];
    }
}
