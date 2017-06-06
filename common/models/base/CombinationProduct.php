<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "CombinationProduct".
 *
 * @property integer $id
 * @property integer $product_id
 * @property integer $order_template_id
 * @property string $name
 * @property double $price
 * @property double $purchase_price
 * @property double $sale_price
 * @property integer $product_number
 * @property double $total_amount
 * @property integer $supplier_id
 * @property integer $supplier_product_id
 * @property string $num
 * @property string $spec
 * @property string $unit
 * @property string $material_type
 * @property integer $status
 * @property integer $pstock_id
 * @property string $batches
 */
class CombinationProduct extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'combinationproduct';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_id', 'order_template_id', 'name', 'product_number', 'supplier_id', 'supplier_product_id', 'num', 'material_type', 'pstock_id'], 'required'],
            [['product_id', 'order_template_id', 'product_number', 'supplier_id', 'supplier_product_id', 'status', 'material_type', 'pstock_id'], 'integer'],
            [['price', 'purchase_price', 'sale_price', 'total_amount'], 'number'],
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
            'product_id' => '物料id ',
            'order_template_id' => '订单ID',
            'name' => '名称',
            'price' => '货品价格',
            'purchase_price' => '采购价格',
            'sale_price' => '销售价格',
            'product_number' => '数量 ',
            'total_amount' => '总价',
            'supplier_id' => '供应商ID',
            'supplier_product_id' => '供应商货品ID',
            'num' => '供应商出品编码',
            'spec' => '规格',
            'unit' => '单位',
            'material_type' => '物料类别',
            'status' => '0 无效 1有效 99删除',
            'pstock_id' => '库存ID',
            'batches' => '库存批次号',
        ];
    }
}
