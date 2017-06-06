<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "OrderMaterialReturnProduct".
 *
 * @property integer $id
 * @property integer $product_id
 * @property integer $order_procurement_id
 * @property string $name
 * @property double $price
 * @property double $purchase_price
 * @property integer $product_number
 * @property double $total_amount
 * @property integer $supplier_id
 * @property integer $supplier_product_id
 * @property string $num
 * @property string $spec
 * @property string $unit
 * @property integer $material_type
 * @property integer $status
 * @property integer $type
 */
class OrderMaterialReturnProduct extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'OrderMaterialReturnProduct';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_id', 'order_procurement_id', 'name', 'product_number', 'supplier_id', 'supplier_product_id', 'num'], 'required'],
            [['product_id', 'order_procurement_id', 'product_number', 'supplier_id', 'supplier_product_id', 'material_type', 'status', 'type'], 'integer'],
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
            'order_procurement_id' => 'Order Procurement ID',
            'name' => 'Name',
            'price' => 'Price',
            'purchase_price' => 'Purchase Price',
            'product_number' => '退货数量',
            'total_amount' => 'Total Amount',
            'supplier_id' => 'Supplier ID',
            'supplier_product_id' => 'Supplier Product ID',
            'num' => 'Num',
            'spec' => 'Spec',
            'unit' => 'Unit',
            'material_type' => 'Material Type',
            'status' => 'Status',
            'type' => 'Type',
        ];
    }
}
