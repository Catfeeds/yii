<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "OrderTemplateProduct".
 *
 * @property integer $id
 * @property integer $product_id
 * @property integer $order_template_id
 * @property string $name
 * @property double $purchase_price
 * @property integer $buying_number
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
class OrderTemplateProduct extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ordertemplateproduct';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_id', 'order_template_id', 'name', 'buying_number', 'supplier_id', 'supplier_product_id', 'num', 'material_type', 'product_cate_id'], 'required'],
            [['product_id', 'order_template_id', 'buying_number', 'supplier_id', 'supplier_product_id', 'status', 'material_type', 'product_cate_id'], 'integer'],
            [['purchase_price',  'total_amount'], 'number'],
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
            'product_id' => '物料id ',
            'order_template_id' => '订单ID',
            'name' => '名称',
            'purchase_price' => '采购价格',
            'buying_number' => '转货数量 ',
            'total_amount' => '总价',
            'supplier_id' => '供应商ID',
            'supplier_product_id' => '供应商货品ID',
            'num' => '供应商出品编码',
            'spec' => '规格',
            'unit' => '单位',
            'material_type' => '物料类别',
            'warehouse_id' => '存放库区ID',
            'status' => '0 无效 1有效 99删除',
            'product_cate_id' => '物料分类ID',
        ];
    }
}
