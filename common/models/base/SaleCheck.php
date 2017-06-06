<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "SaleCheck".
 *
 * @property integer $id
 * @property string $name
 * @property string $sn
 * @property double $total_amount
 * @property double $sale_total_amount
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
 * @property string $remark
 * @property double $paid_amount
 * @property double $compensation_amount
 */
class SaleCheck extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'salecheck';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'sn', 'sale_total_amount', 'warehouse_id', 'create_time', 'config_id', 'remark', 'paid_amount', 'compensation_amount'], 'required'],
            [['total_amount', 'sale_total_amount', 'paid_amount', 'compensation_amount'], 'number'],
            [['department_id', 'warehouse_id', 'create_admin_id', 'verify_admin_id', 'approval_admin_id', 'operation_admin_id', 'status', 'config_id', 'timing_type'], 'integer'],
            [['verify_time', 'approval_time', 'operation_time', 'create_time'], 'safe'],
            [['name', 'failCause'], 'string', 'max' => 255],
            [['sn'], 'string', 'max' => 40],
            [['remark'], 'string', 'max' => 100],
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
            'sn' => '表单号',
            'total_amount' => '应销总价',
            'sale_total_amount' => '实销总价',
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
            'timing_type' => 'Timing Type',
            'remark' => '损益原因',
            'paid_amount' => '上缴金额',
            'compensation_amount' => '补偿金额',
        ];
    }
}
