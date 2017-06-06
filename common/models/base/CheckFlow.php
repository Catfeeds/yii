<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "CheckFlow".
 *
 * @property integer $id
 * @property integer $check_planning_id
 * @property integer $type
 * @property string $name
 * @property string $sn
 * @property string $total_buying_amount
 * @property string $check_buying_amount
 * @property integer $status
 * @property integer $config_id
 * @property integer $create_admin_id
 * @property string $create_time
 * @property integer $verify_admin_id
 * @property string $verify_time
 * @property integer $approval_admin_id
 * @property string $approval_time
 * @property integer $operation_admin_id
 * @property string $operation_time
 * @property string $failCause
 * @property integer $department_id
 * @property integer $warehouse_id
 * @property string $end_time
 * @prpperty integer $timing_type
 */
class CheckFlow extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'checkflow';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['check_planning_id', 'type', 'name', 'sn', 'total_buying_amount', 'check_buying_amount', 'status', 'config_id', 'create_admin_id', 'create_time'], 'required'],
            [['check_planning_id', 'type', 'status', 'config_id', 'create_admin_id', 'verify_admin_id', 'approval_admin_id', 'operation_admin_id', 'department_id', 'warehouse_id', 'timing_type'], 'integer'],
            [['total_buying_amount', 'check_buying_amount'], 'number'],
            [['create_time', 'verify_time', 'approval_time', 'operation_time', 'end_time'], 'safe'],
            [['name', 'sn'], 'string', 'max' => 40],
            [['failCause'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'check_planning_id' => 'Check Planning ID',
            'type' => 'Type',
            'name' => '表单名',
            'sn' => '表单号',
            'total_buying_amount' => 'Total Buying Amount',
            'check_buying_amount' => 'Check Buying Amount',
            'status' => 'Status',
            'config_id' => 'Config ID',
            'create_admin_id' => 'Create Admin ID',
            'create_time' => 'Create Time',
            'verify_admin_id' => 'Verify Admin ID',
            'verify_time' => 'Verify Time',
            'approval_admin_id' => 'Approval Admin ID',
            'approval_time' => 'Approval Time',
            'operation_admin_id' => 'Operation Admin ID',
            'operation_time' => 'Operation Time',
            'failCause' => 'Fail Cause',
            'department_id' => 'Department ID',
            'warehouse_id' => 'Warehouse ID',
            'end_time' => 'End Time',
            'timing_type' => '定时操作类型',
        ];
    }
}
