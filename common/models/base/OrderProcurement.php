<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "OrderProcurement".
 *
 * @property integer $id
 * @property string $name
 * @property string $procurement_id
 * @property string $sn
 * @property string $order_sn
 * @property integer $warehouse_id
 * @property integer $supplier_id
 * @property string $planning_date
 * @property integer $payment
 * @property double $deposit
 * @property double $total_amount
 * @property string $payment_term
 * @property integer $create_admin_id
 * @property integer $verify_admin_id
 * @property integer $approval_admin_id
 * @property integer $operation_admin_id
 * @property integer $status
 * @property string $create_time
 * @property integer $config_id
 * @property string $failCause
 * @property integer $department_id
 * @property string $verify_time
 * @property string $approval_time
 * @property string $operation_time
 * @property integer  $pay_state
 * @property string $pay_deposit_time
 * @property string $pay_all_time
 * @property integer $buckle_amount
 * @property integer $timing_type
 */
class OrderProcurement extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'orderprocurement';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'procurement_id', 'sn', 'order_sn', 'planning_date', 'operation_admin_id', 'create_time', 'config_id', 'pay_state', 'buckle_amount'], 'required'],
            [['procurement_id', 'planning_date', 'payment_term', 'create_time', 'verify_time', 'approval_time', 'operation_time', 'pay_deposit_time', 'pay_all_time'], 'safe'],
            [['warehouse_id', 'supplier_id', 'payment', 'create_admin_id', 'verify_admin_id', 'approval_admin_id', 'operation_admin_id', 'status', 'config_id', 'department_id', 'pay_state', 'timing_type'], 'integer'],
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
            'procurement_id' => '采购计划id',
            'sn' => '表单号',
            'order_sn' => '订单编号',
            'warehouse_id' => '仓库ID',
            'department_id' => '部门ID',
            'supplier_id' => '供应商ID',
            'planning_date' => '计划下单时间',
            'payment' => '支付方式',
            'deposit' => '定金',
            'total_amount' => '总金额',
            'payment_term' => '付款期',
            'create_admin_id' => 'Create Admin ID',
            'verify_admin_id' => 'Verify Admin ID',
            'approval_admin_id' => 'Approval Admin ID',
            'operation_admin_id' => 'Operation Admin ID',
            'status' => 'Status',
            'create_time' => 'Create Time',
            'config_id' => 'Config ID',
            'failCause' => 'Fail Cause',
            'pay_state' => '支付状态',
            'pay_deposit_time' => '部分支付时间',
            'pay_all_time' => '全部支付时间',
            'buckle_amount' => '扣项',
            'timing_type' => '定时操作类型',
        ];
    }
}
