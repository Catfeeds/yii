<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "salesorder".
 *
 * @property integer $id
 * @property string $name
 * @property string $sn
 * @property integer $sale_order_id
 * @property string $total_amount
 * @property integer $department_id
 * @property string $customer_company
 * @property integer $warehouse_id
 * @property integer $create_admin_id
 * @property integer $operation_admin_id
 * @property string $operation_time
 * @property integer $custom_pay_service_id
 * @property integer $down_payment_pay_ways
 * @property string $down_payment
 * @property string $benefit_money
 * @property integer $status
 * @property string $create_time
 * @property integer $config_id
 * @property string $remark
 * @property integer $timing_type
 */
class Salesorder extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'salesorder';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'sn', 'sale_order_id', 'warehouse_id', 'create_time', 'config_id'], 'required'],
            [['sale_order_id', 'department_id', 'warehouse_id', 'create_admin_id', 'operation_admin_id', 'custom_pay_service_id', 'down_payment_pay_ways', 'status', 'config_id', 'timing_type'], 'integer'],
            [['total_amount', 'down_payment', 'benefit_money'], 'number'],
            [['operation_time', 'create_time'], 'safe'],
            [['name', 'remark'], 'string', 'max' => 255],
            [['sn', 'customer_company'], 'string', 'max' => 40],
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
            'sale_order_id' => 'Sale Order ID',
            'total_amount' => 'Total Amount',
            'department_id' => 'Department ID',
            'customer_company' => 'Customer Company',
            'warehouse_id' => 'Warehouse ID',
            'create_admin_id' => 'Create Admin ID',
            'operation_admin_id' => 'Operation Admin ID',
            'operation_time' => 'Operation Time',
            'custom_pay_service_id' => 'Custom Pay Service ID',
            'down_payment_pay_ways' => 'Down Payment Pay Ways',
            'down_payment' => 'Down Payment',
            'benefit_money' => 'Benefit Money',
            'status' => 'Status',
            'create_time' => 'Create Time',
            'config_id' => 'Config ID',
            'remark' => 'Remark',
            'timing_type' => 'Timing Type',
        ];
    }
}
