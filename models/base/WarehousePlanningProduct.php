<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "WarehousePlanningProduct".
 *
 * @property integer $id
 * @property integer $product_id
 * @property integer $planning_id
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
 * @property string $material_type
 * @property integer $status
 * @property integer $product_cate_id
 */
class WarehousePlanningProduct extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'WarehousePlanningProduct';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_id', 'planning_id', 'name', 'product_number', 'supplier_id', 'supplier_product_id', 'num', 'material_type', 'product_cate_id'], 'required', 'message' => '{attribute}不能为空'],
            [['product_id', 'planning_id', 'product_number', 'supplier_id', 'supplier_product_id', 'status', 'material_type', 'product_cate_id'], 'integer'],
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
            'planning_id' => 'Planning ID',
            'name' => 'Name',
            'price' => 'Price',
            'purchase_price' => 'Purchase Price',
            'product_number' => '采购数量',
            'total_amount' => 'Total Amount',
            'supplier_id' => 'Supplier ID',
            'supplier_product_id' => 'Supplier Product ID',
            'num' => 'Num',
            'spec' => 'Spec',
            'unit' => 'Unit',
            'material_type' => 'Material Type',
            'status' => 'Status',
            'product_cate_id' => '物料分类ID',
        ];
    }
}
