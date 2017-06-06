<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "WarehouseSale".
 *
 * @property integer $id
 * @property string $name
 * @property string $sn
 * @property double $total_amount
 * @property integer $department_id
 * @property integer $warehouse_id
 * @property integer $create_admin_id
 * @property integer $verify_admin_id
 * @property string $verify_time
 * @property integer $approval_admin_id
 * @property string $approval_time
 * @property integer $operation_admin_id
 * @property string $operation_time
 * @property integer $status
 * @property string $create_time
 * @property integer $config_id
 * @property string $failCause
 * @property integer $timing_type
 */
class WarehouseSale extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'WarehouseSale';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'sn', 'warehouse_id', 'operation_admin_id', 'operation_time', 'create_time', 'config_id'], 'required'],
            [['total_amount'], 'number'],
            [['department_id', 'warehouse_id', 'create_admin_id', 'verify_admin_id', 'approval_admin_id', 'operation_admin_id', 'status', 'config_id', 'timing_type'], 'integer'],
            [['verify_time', 'approval_time', 'operation_time', 'create_time'], 'safe'],
            [['name', 'failCause'], 'string', 'max' => 255],
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
            'name' => 'Name',
            'sn' => 'Sn',
            'total_amount' => 'Total Amount',
            'department_id' => 'Department ID',
            'warehouse_id' => 'Warehouse ID',
            'create_admin_id' => 'Create Admin ID',
            'verify_admin_id' => 'Verify Admin ID',
            'verify_time' => 'Verify Time',
            'approval_admin_id' => 'Approval Admin ID',
            'approval_time' => 'Approval Time',
            'operation_admin_id' => 'Operation Admin ID',
            'operation_time' => 'Operation Time',
            'status' => 'Status',
            'create_time' => 'Create Time',
            'config_id' => 'Config ID',
            'failCause' => 'Fail Cause',
            'timing_type' => '定时操作类型',
        ];
    }
}
