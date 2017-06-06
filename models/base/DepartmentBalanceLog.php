<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "DepartmentBalanceLog".
 *
 * @property integer $id
 * @property string $name 
 * @property integer $department_id
 * @property integer $business_id
 * @property integer $business_type
 * @property double $balance
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
 */
class DepartmentBalanceLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'DepartmentBalanceLog';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'department_id', 'business_id', 'business_type', 'content', 'status', 'create_time', 'create_admin_id', 'config_id'], 'required'],
            [['department_id', 'business_id', 'business_type', 'mod', 'status', 'create_admin_id', 'verify_admin_id', 'approval_admin_id', 'operation_admin_id', 'config_id'], 'integer'],
            [['balance', 'current_balance'], 'number'],
            [['create_time', 'verify_time', 'approval_time', 'operation_time'], 'safe'],
            [['content'], 'string', 'max' => 1000],
            [['name', 'failCause'], 'string', 'max' => 255],
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
            'department_id' => '部门',
            'business_id' => '业务ID',
            'business_type' => '业务类型',
            'balance' => '变动金额',
            'current_balance' => '当前金额',
            'mod' => '进出：1 进 2出',
            'content' => '操作内容',
            'status' => 'Status',
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
        ];
    }
}
