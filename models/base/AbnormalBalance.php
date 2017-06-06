<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "AbnormalBalance".
 *
 * @property integer $id
 * @property string $name
 * @property integer $department_id
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
 * @prpperty integer $timing_type
 */
class AbnormalBalance extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'AbnormalBalance';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['department_id', 'content', 'status', 'create_time', 'create_admin_id', 'config_id'], 'required'],
            [['department_id', 'mod', 'status', 'create_admin_id', 'verify_admin_id', 'approval_admin_id', 'operation_admin_id', 'config_id', 'timing_type'], 'integer'],
            [['balance', 'current_balance'], 'number'],
            [['create_time', 'verify_time', 'approval_time', 'operation_time'], 'safe'],
            [['name', 'failCause'], 'string', 'max' => 255],
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
            'name' => '名称',
            'department_id' => '部门',
            'balance' => '变动金额',
            'current_balance' => '当前金额',
            'mod' => 'Mod',
            'content' => '变动内容',
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
            'timing_type' => '定时操作类型',
        ];
    }
}
