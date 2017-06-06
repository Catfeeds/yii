<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "Combination".
 *
 * @property integer $id
 * @property string $name
 * @property string $sn
 * @property double $total_amount
 * @property integer $payment
 * @property double $deposit
 * @property integer $warehouse_id
 * @property integer $create_admin_id
 * @property string $create_time
 * @property string $approval_time
 * @property string $operation_time
 * @property string $operation_cause
 * @property integer $supplier_id
 * @property string $common
 */
class Combination extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'Combination';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'sn', 'payment', 'deposit', 'create_time', 'approval_time', 'operation_time', 'common'], 'required'],
            [['total_amount', 'deposit'], 'number'],
            [['payment', 'warehouse_id', 'create_admin_id', 'supplier_id'], 'integer'],
            [['create_time', 'approval_time', 'operation_time'], 'safe'],
            [['name', 'operation_cause', 'common'], 'string', 'max' => 255],
            [['sn'], 'string', 'max' => 40],
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
            'sn' => '表单号',
            'total_amount' => '总价',
            'payment' => '支付方式',
            'deposit' => '定金',
            'warehouse_id' => '仓库',
            'create_admin_id' => '制表人',
            'create_time' => '制表时间',
            'approval_time' => '批准时间',
            'operation_time' => '验收时间',
            'operation_cause' => '验收说明',
            'supplier_id' => '供应商',
            'common' => '用途说明',
        ];
    }
}
