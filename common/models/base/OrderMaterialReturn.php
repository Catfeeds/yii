<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "OrderMaterialReturn".
 *
 * @property integer $id
 * @property string $name
 * @property integer $procurement_id
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
 * @property string $remark
 * @property string $operation_time
 * @property string $verify_time
 * @property string $approval_time
 * @property integer $department_id
 * @property integer $pay_state
 * @property string $pay_deposit_time
 * @property string $pay_all_time
 * @prpperty integer $timing_type
 */
class OrderMaterialReturn extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ordermaterialreturn';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'procurement_id', 'sn', 'order_sn', 'planning_date', 'operation_admin_id', 'create_time', 'config_id'], 'required'],
            [['procurement_id', 'warehouse_id', 'supplier_id', 'payment', 'create_admin_id', 'verify_admin_id', 'approval_admin_id', 'operation_admin_id', 'status', 'config_id', 'department_id', 'pay_state', 'timing_type'], 'integer'],
            [['planning_date', 'payment_term', 'create_time', 'operation_time', 'verify_time', 'approval_time', 'pay_deposit_time', 'pay_all_time'], 'safe'],
            [['deposit', 'total_amount'], 'number'],
            [['name', 'failCause', 'remark'], 'string', 'max' => 255],
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
            'procurement_id' => '退货id',
            'sn' => '表单号',
            'order_sn' => '订单编号',
            'warehouse_id' => '仓库ID',
            'supplier_id' => '供应商ID',
            'planning_date' => '计划退货时间',
            'payment' => '支付方式',
            'deposit' => '定金',
            'total_amount' => '总金额',
            'payment_term' => '计划退款时间',
            'create_admin_id' => 'Create Admin ID',
            'verify_admin_id' => 'Verify Admin ID',
            'approval_admin_id' => 'Approval Admin ID',
            'operation_admin_id' => 'Operation Admin ID',
            'status' => 'Status',
            'create_time' => 'Create Time',
            'config_id' => 'Config ID',
            'failCause' => 'Fail Cause',
            'remark' => 'Remark',
            'operation_time' => 'Operation Time',
            'verify_time' => 'Verify Time',
            'approval_time' => 'Approval Time',
            'department_id' => '部门ID',
            'pay_state' => '支付状态',
            'pay_deposit_time' => '部分支付时间',
            'pay_all_time' => '全部支付时间',
            'timing_type' => '定时操作类型',
        ];
    }
}
