<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "ProductStock".
 *
 * @property integer $id
 * @property string $batches
 * @property integer $product_id
 * @property integer $number
 * @property integer $warehouse_id
 * @property integer $type
 */
class ProductStock extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ProductStock';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['batches', 'product_id', 'number', 'warehouse_id', 'supplier_id', 'type'], 'required'],
            [['product_id', 'number', 'warehouse_id', 'type'], 'integer'],
            [['batches'], 'string', 'max' => 40],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'batches' => '批次号',
            'product_id' => '物料编号',
            'number' => '数量',
            'warehouse_id' => '仓库ID',
            'supplier_id' => '供应商ID',
            'type' => '类型 1：正常 2：例行 3：例外',
        ];
    }
}
