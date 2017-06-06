<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "ProductLog".
 *
 * @property integer $id
 * @property integer $product_id
 * @property string $content
 * @property double $purchase_price
 * @property double $sale_price
 * @property string $create_time
 * @property integer $admin_id
 */
class ProductLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ProductLog';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_id', 'content', 'purchase_price', 'sale_price', 'create_time'], 'required'],
            [['product_id', 'admin_id'], 'integer'],
            [['purchase_price', 'sale_price'], 'number'],
            [['create_time'], 'safe'],
            [['content'], 'string', 'max' => 1000],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'product_id' => '物料id',
            'content' => '操作内容',
            'purchase_price' => '进货价格',
            'sale_price' => '销售价格',
            'create_time' => '日期',
            'admin_id' => '操作者ID',
        ];
    }
}
