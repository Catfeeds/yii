<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "WarehouseTransferDep".
 *
 * @property integer $id
 * @property string $name
 * @property string $sn
 * @property integer $warehouse_id
 * @property integer $receive_warehouse_id
 * @property integer $supplier_id
 * @property double $total_amount
 * @property integer $department_id
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
class WarehouseTransferDep extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'warehousetransferdep';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'sn', 'receive_warehouse_id', 'operation_admin_id', 'operation_time', 'create_time', 'config_id', 'supplier_id'], 'required', 'message' => '{attribute}不能为空'],
            [['warehouse_id', 'receive_warehouse_id', 'department_id', 'create_admin_id', 'verify_admin_id', 'approval_admin_id', 'operation_admin_id', 'status', 'config_id', 'is_buckle', 'timing_type', 'supplier_id'], 'integer'],
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
            'sn' => '转货单号',
            'warehouse_id' => '转出仓库',
            'receive_warehouse_id' => '转入仓库',
            'supplier_id' => '供应商ID',
            'total_amount' => 'Total Amount',
            'department_id' => 'Department ID',
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
            'total_cost' => 'Total Cost',
            'is_buckle' => '是否扣仓【0：否 1：是】',
            'timing_type' => '定时操作类型',
        ];
    }
}
