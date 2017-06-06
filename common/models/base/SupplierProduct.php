<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "SupplierProduct".
 *
 * @property integer $id
 * @property string $name
 * @property double $purchase_price
 * @property integer $supplier_id
 * @property string $num
 * @property string $spec
 * @property string $unit
 * @property string $material_type
 * @property integer $status
 * @property integer $is_update
 */
class SupplierProduct extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'supplierproduct';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'supplier_id', 'num', 'material_type', 'spec', 'unit'], 'required'],
            [['purchase_price'], 'number'],
            [['supplier_id', 'status', 'material_type', 'is_update'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['num', 'spec', 'unit'], 'string', 'max' => 12],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '名称',
            'purchase_price' => '进货参考价格',
            'supplier_id' => '供应商ID',
            'num' => '供应商出品编码',
            'spec' => '规格',
            'unit' => '单位',
            'material_type' => '物料类别',
            'status' => '0 无效 1有效 99删除',
            'is_update' => '是否修改 0 无 1是',
        ];
    }
}
