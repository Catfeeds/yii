<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "WarehouseCheckout".
 *
 * @property integer $id
 * @property string $name
 * @property string $sn
 * @property integer $warehouse_id
 * @property integer $department_id
 * @property integer $receive_warehouse_id
 * @property double $total_amount
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
 * @property double $total_cost
 * @property integer $is_buckle
 * @property integer $timing_type
 */
class WarehouseCheckout extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'warehousecheckout';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'sn', 'department_id', 'receive_warehouse_id', 'operation_admin_id', 'operation_time', 'create_time', 'config_id'], 'required', 'message' => '{attribute}不能为空'],
            [['warehouse_id', 'department_id', 'receive_warehouse_id', 'create_admin_id', 'verify_admin_id', 'approval_admin_id', 'operation_admin_id', 'status', 'config_id', 'is_buckle', 'timing_type'], 'integer'],
            [['total_amount', 'total_cost'], 'number'],
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
            'name' => '表单名',
            'sn' => '出库单号',
            'warehouse_id' => '出库仓库',
            'department_id' => 'Department ID',
            'receive_warehouse_id' => '入库仓库',
            'total_amount' => 'Total Amount',
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
            'total_cost' => '总成本',
            'is_buckle' => '是否扣仓【0：否 1：是】',
            'timing_type' => '定时操作类型',
        ];
    }
}
