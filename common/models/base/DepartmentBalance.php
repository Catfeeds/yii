<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "DepartmentBalance".
 *
 * @property integer $id
 * @property integer $department_id
 * @property double $balance
 * @property double $income_amount
 * @property double $expenses_amount
 */
class DepartmentBalance extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'departmentbalance';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['department_id'], 'required'],
            [['department_id'], 'integer'],
            [['balance', 'income_amount', 'expenses_amount'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'department_id' => '部门',
            'balance' => '余额',
            'income_amount' => '进项总额',
            'expenses_amount' => '出项总额',
        ];
    }
}
