<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "WarehouseBuying".
 *
 * @property integer $id
 * @property string $name
 * @property string $sn
 * @property string $order_sn
 * @property integer $warehouse_id
 * @property integer $department_id
 * @property integer $supplier_id
 * @property string $planning_date
 * @property integer $payment
 * @property double $deposit
 * @property double $total_amount
 * @property string $payment_term
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
 * @property integer $type
 * @property integer $timing_type
 */
class WarehouseBuying extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'warehousebuying';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'sn', 'order_sn', 'department_id', 'planning_date', 'operation_admin_id', 'create_time', 'config_id', 'type'], 'required'],
            [['warehouse_id', 'department_id', 'supplier_id', 'payment', 'create_admin_id', 'verify_admin_id', 'approval_admin_id', 'operation_admin_id', 'status', 'config_id', 'type', 'timing_type'], 'integer'],
            [['planning_date', 'payment_term', 'verify_time', 'approval_time', 'operation_time', 'create_time'], 'safe'],
            [['deposit', 'total_amount'], 'number'],
            [['name', 'failCause'], 'string', 'max' => 255],
            [['sn', 'order_sn'], 'string', 'max' => 40],
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
            'sn' => '表单号',
            'order_sn' => '订单编号',
            'warehouse_id' => '仓库ID',
            'department_id' => '部门ID',
            'supplier_id' => '供应商ID',
            'planning_date' => '计划下单时间',
            'payment' => '支付方式',
            'deposit' => '定金',
            'total_amount' => '总金额',
            'payment_term' => '付款时间',
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
            'type' => '类型 1：正常 2：例行 3：例外',
            'timing_type' => '定时操作类型',
        ];
    }
}
