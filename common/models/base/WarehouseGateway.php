<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "WarehouseGateway".
 *
 * @property integer $id
 * @property integer $warehouse_id
 * @property integer $product_id
 * @property integer $type
 * @property integer $stock
 * @property integer $num
 * @property integer $gateway_no
 * @property integer $gateway_type
 * @property string $create_time
 * @property string $comment
 * @property integer $product_type
 * @property string $batches
 */
class WarehouseGateway extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'warehousegateway';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['warehouse_id', 'product_id', 'type', 'num', 'gateway_no', 'gateway_type', 'create_time', 'product_type'], 'required'],
            [['warehouse_id', 'product_id', 'type', 'stock', 'num', 'gateway_no', 'gateway_type', 'product_type'], 'integer'],
            [['create_time'], 'safe'],
            [['comment', 'batches'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'warehouse_id' => 'Warehouse ID',
            'product_id' => 'Product ID',
            'type' => 'Type',
            'stock' => 'Stock',
            'num' => 'Num',
            'gateway_no' => 'Gateway No',
            'gateway_type' => 'Gateway Type',
            'create_time' => 'Create Time',
            'comment' => 'Comment',
            'product_type' => 'Product Type',
            'batches' => 'Batches',
        ];
    }
}
