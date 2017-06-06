<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "WarehousePlanning".
 *
 * @property integer $id
 * @property string $name
 * @property string $sn
 * @property integer $warehouse_id
 * @property integer $department_id
 * @property string $order_sn
 * @property string $planning_date
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
 * @property string $total_money
 * @property string $failCause
 * @property integer $type
 * @property integer $supplier_id
 * @property integer $payment
 * @property double $deposit
 * @property string $payment_term
 * @property integer $buckle_amount
 * @property integer $timing_type
 */
class WarehousePlanning extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'WarehousePlanning';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'sn', 'department_id', 'order_sn', 'planning_date',  'create_time', 'config_id', 'total_money', 'supplier_id', 'payment'], 'required', 'message' => '{attribute}不能为空'],
            [['warehouse_id', 'department_id', 'create_admin_id', 'verify_admin_id', 'approval_admin_id', 'operation_admin_id', 'status', 'config_id', 'type', 'supplier_id', 'payment', 'timing_type'], 'integer'],
            [['planning_date', 'verify_time', 'approval_time', 'operation_time', 'create_time', 'payment_term'], 'safe'],
            [['total_money', 'deposit', 'buckle_amount'], 'number'],
            [['name'], 'string', 'max' => 255],
            [['sn'], 'string', 'max' => 40],
            [['order_sn'], 'string', 'max' => 100],
            [['failCause'], 'string', 'max' => 250],
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
            'sn' => '采购单号',
            'warehouse_id' => '仓库ID',
            'department_id' => 'Department ID',
            'order_sn' => 'Order Sn',
            'planning_date' => '采购计划日期',
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
            'total_money' => 'Total Money',
            'failCause' => 'Fail Cause',
            'type' => 'Type',
            'supplier_id' => '供应商',
            'payment' => '付款方式',
            'deposit' => '定金',
            'payment_term' => '付款日期',
            'buckle_amount' => '扣项',
            'buckle_amount' => '定时操作类型',
        ];
    }
}
