<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "AbnormalBalance".
 *
 * @property integer $id
 * @property string $name
 * @property integer $department_id
 * @property integer $income_department_id
 * @property double $current_balance
 * @property integer $mod
 * @property string $content
 * @property integer $status
 * @property string $create_time
 * @property integer $create_admin_id
 * @property integer $verify_admin_id
 * @property string $verify_time
 * @property integer $approval_admin_id
 * @property string $approval_time
 * @property integer $operation_admin_id
 * @property string $operation_time
 * @property integer $config_id
 * @property string $failCause
 * @property integer $timing_type
 * @property string $sn
 */
class AbnormalBalance extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'abnormalbalance';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['department_id', 'income_department_id', 'mod', 'status', 'create_admin_id', 'verify_admin_id', 'approval_admin_id', 'operation_admin_id', 'config_id', 'timing_type'], 'integer'],
            [['current_balance'], 'number'],
            [['content', 'status', 'create_time', 'create_admin_id', 'config_id'], 'required'],
            [['create_time', 'verify_time', 'approval_time', 'operation_time'], 'safe'],
            [['name', 'failCause', 'sn'], 'string', 'max' => 255],
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
            'name' => '表单名',
            'department_id' => '支出部门',
            'income_department_id' => '收入部门',
            'current_balance' => '变动金额',
            'mod' => '变动类型',
            'content' => '变动内容',
            'status' => '状态',
            'create_time' => 'Create Time',
            'create_admin_id' => 'Create Admin ID',
            'verify_admin_id' => 'Verify Admin ID',
            'verify_time' => 'Verify Time',
            'approval_admin_id' => 'Approval Admin ID',
            'approval_time' => 'Approval Time',
            'operation_admin_id' => 'Operation Admin ID',
            'operation_time' => 'Operation Time',
            'config_id' => 'Config ID',
            'failCause' => 'Fail Cause',
            'timing_type' => 'Timing Type',
            'sn' => '表单号',
        ];
    }
}
