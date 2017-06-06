<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "WarehouseBack".
 *
 * @property integer $id
 * @property string $name
 * @property string $sn
 * @property integer $warehouse_id
 * @property integer $receive_warehouse_id
 * @property double $total_amount
 * @property integer $create_admin_id
 * @property string $create_time
 * @property integer $verify_admin_id
 * @property string $verify_time
 * @property integer $approval_admin_id
 * @property string $approval_time
 * @property integer $operation_admin_id
 * @property string $operation_time
 * @property integer $status
 * @property integer $config_id
 * @property string $failCause
 * @property string $remark
 * @property integer $is_buckle
 * @property integer $timing_type
 */
class WarehouseBack extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'warehouseback';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'sn', 'receive_warehouse_id', 'create_time', 'config_id'], 'required'],
            [['warehouse_id', 'receive_warehouse_id', 'create_admin_id', 'verify_admin_id', 'approval_admin_id', 'operation_admin_id', 'status', 'config_id', 'is_buckle', 'timing_type'], 'integer'],
            [['total_amount'], 'number'],
            [['create_time', 'verify_time', 'approval_time', 'operation_time'], 'safe'],
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
            'sn' => '表单号',
            'warehouse_id' => '退出仓库',
            'receive_warehouse_id' => '退入仓库',
            'total_amount' => 'Total Amount',
            'create_admin_id' => 'Create Admin ID',
            'create_time' => 'Create Time',
            'verify_admin_id' => 'Verify Admin ID',
            'verify_time' => 'Verify Time',
            'approval_admin_id' => 'Approval Admin ID',
            'approval_time' => 'Approval Time',
            'operation_admin_id' => 'Operation Admin ID',
            'operation_time' => 'Operation Time',
            'status' => 'Status',
            'config_id' => 'Config ID',
            'failCause' => 'Fail Cause',
            'remark' => 'Remark',
            'is_buckle' => '是否扣仓【0：否 1：是】',
            'timing_type' => '定时操作类型',
        ];
    }
}
