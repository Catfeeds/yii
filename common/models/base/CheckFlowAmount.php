<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "CheckFlowAmount".
 *
 * @property integer $id
 * @property integer $check_flow_id
 * @property integer $check_department_id
 * @property string $amount
 * @property string $check_amount
 */
class CheckFlowAmount extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'checkflowamount';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['check_flow_id', 'check_department_id', 'amount', 'check_amount'], 'required'],
            [['check_flow_id', 'check_department_id'], 'integer'],
            [['amount', 'check_amount'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'check_flow_id' => 'Check Flow ID',
            'check_department_id' => 'Check Department ID',
            'amount' => 'Amount',
            'check_amount' => 'Check Amount',
        ];
    }
}
