<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "Supplier".
 *
 * @property integer $id
 * @property string $name
 * @property string $num
 * @property string $level
 * @property integer $status
 * @property string $create_time
 * @property integer $pay_period
 */
class Supplier extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'Supplier';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'num', 'create_time', 'pay_period'], 'required'],
            [['status', 'pay_period'], 'integer'],
            [['create_time'], 'safe'],
            [['name', 'num'], 'string', 'max' => 40],
            [['level'], 'string', 'max' => 2],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '供应商名称',
            'num' => '供应商编号',
            'level' => '分级',
            'status' => '0 无效 1有效 99删除',
            'create_time' => '创建时间',
            'pay_period' => '付款账期 【1：日结 2：周结 3：月结 4：季度结】',
        ];
    }
}
