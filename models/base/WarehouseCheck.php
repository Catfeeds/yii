<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "WarehouseCheck".
 *
 * @property integer $id
 * @property integer $check_planning_id
 * @property string $name
 * @property string $sn
 * @property double $total_amount
 * @property double $total_purchase_amount
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
 * @property string $remark
 * @property integer $timing_type
 */
class WarehouseCheck extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'WarehouseCheck';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'sn', 'total_purchase_amount', 'operation_admin_id', 'operation_time', 'create_time', 'config_id'], 'required', 'message' => '{attribute}不能为空'],
            [['total_amount', 'total_purchase_amount'], 'number'],
            [['department_id', 'warehouse_id', 'create_admin_id', 'verify_admin_id', 'approval_admin_id', 'operation_admin_id', 'status', 'config_id', 'check_planning_id', 'timing_type'], 'integer'],
            [['verify_time', 'approval_time', 'operation_time', 'create_time'], 'safe'],
            [['name', 'failCause', 'remark'], 'string', 'max' => 255],
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
            'check_planning_id' => '盘点计划ID',
            'name' => '表单名',
            'sn' => '盘点单号',
            'total_amount' => 'Total Amount',
            'total_purchase_amount' => 'Total Purchase Amount',
            'department_id' => '部门ID',
            'warehouse_id' => '仓库ID',
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
            'remark' => 'Remark',
            'timing_type' => '定时操作类型',
        ];
    }
}
