<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "WarehouseMaterialReturn".
 *
 * @property integer $id
 * @property string $name
 * @property string $sn
 * @property integer $warehouse_id
 * @property integer $supplier_id
 * @property integer $department_id
 * @property double $total_amount
 * @property integer $create_admin_id
 * @property integer $verify_admin_id
 * @property string $verify_time
 * @property integer $approval_admin_id
 * @property string $approval_time
 * @property integer $status
 * @property string $create_time
 * @property integer $operation_admin_id
 * @property string $operation_time
 * @property integer $config_id
 * @property string $failCause
 * @property string $common
 * @property integer $buying_id
 * @property string $planning_date
 * @property string $payment_term
 * @property integer $is_buckle
 * @property integer $timing_type
 */
class WarehouseMaterialReturn extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'WarehouseMaterialReturn';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'sn', 'supplier_id', 'create_time', 'planning_date', 'payment_term', 'common'], 'required', 'message' => '{attribute}不能为空'],
            [['warehouse_id', 'supplier_id', 'create_admin_id', 'verify_admin_id', 'approval_admin_id', 'status', 'operation_admin_id', 'config_id', 'buying_id', 'department_id', 'is_buckle', 'timing_type'], 'integer'],
            [['total_amount'], 'number'],
            [['verify_time', 'approval_time', 'create_time', 'operation_time', 'planning_date', 'payment_term'], 'safe'],
            [['name', 'failCause', 'common'], 'string', 'max' => 255],
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
            'name' => '名称',
            'sn' => '表单号',
            'warehouse_id' => '仓库ID',
            'supplier_id' => '供应商ID',
            'department_id' => '部门ID',
            'total_amount' => 'Total Amount',
            'create_admin_id' => 'Create Admin ID',
            'verify_admin_id' => 'Verify Admin ID',
            'verify_time' => 'Verify Time',
            'approval_admin_id' => 'Approval Admin ID',
            'approval_time' => 'Approval Time',
            'status' => 'Status',
            'create_time' => 'Create Time',
            'operation_admin_id' => 'Operation Admin ID',
            'operation_time' => 'Operation Time',
            'config_id' => 'Config ID',
            'failCause' => 'Fail Cause',
            'common' => '退货理由',
            'buying_id' => '入库ID',
            'planning_date' => '退货时间',
            'payment_term' => '退款时间',
            'is_buckle' => '是否扣仓【0：否 1：是】',
            'timing_type' => '定时操作类型',
        ];
    }
}
