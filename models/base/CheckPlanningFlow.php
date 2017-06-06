<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "CheckPlanningFlow".
 *
 * @property integer $id
 * @property string $name
 * @property string $sn
 * @property integer $type
 * @property integer $supplier_id
 * @property integer $is_check_amount
 * @property string $check_time
 * @property string $end_time
 * @property string $product_name
 * @property integer $product_cate_id
 * @property integer $status
 * @property integer $config_id
 * @property integer $create_admin_id
 * @property string $create_time
 * @property integer $verify_admin_id
 * @property string $verify_time
 * @property integer $approval_admin_id
 * @property string $approval_time
 * @property integer $operation_admin_id
 * @property string $operation_time
 * @property string $failCause
 * @property string $remark
 * @property integer $department_id
 * @property integer $warehouse_id
 * @property integer $is_proof
 * @prpperty integer $timing_type
 */
class CheckPlanningFlow extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'CheckPlanningFlow';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'sn', 'type', 'config_id', 'create_admin_id', 'create_time', 'check_time', 'end_time'], 'required'],
            [['type', 'supplier_id', 'is_check_amount', 'product_cate_id', 'status', 'config_id', 'create_admin_id', 'verify_admin_id', 'approval_admin_id', 'operation_admin_id', 'department_id', 'warehouse_id', 'is_proof', 'timing_type'], 'integer'],
            [['check_time', 'end_time','create_time', 'verify_time', 'approval_time', 'operation_time'], 'safe'],
            [['name', 'sn', 'product_name'], 'string', 'max' => 40],
            [['failCause', 'remark'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '计划名称',
            'sn' => '计划单号',
            'type' => '计划类型',
            'supplier_id' => '盘点供应商',
            'is_check_amount' => '是否盘点金额',
            'check_time' => '盘点时间',
            'end_time' => '结束时间',
            'product_name' => '盘点商品名',
            'product_cate_id' => '盘点商品分类',
            'status' => '状态',
            'config_id' => '流程ID',
            'create_admin_id' => '制表人',
            'create_time' => '制表时间',
            'verify_admin_id' => 'Verify Admin ID',
            'verify_time' => 'Verify Time',
            'approval_admin_id' => 'Approval Admin ID',
            'approval_time' => 'Approval Time',
            'operation_admin_id' => 'Operation Admin ID',
            'operation_time' => 'Operation Time',
            'failCause' => 'Fail Cause',
            'remark' => 'Remark',
            'department_id' => '盘点部门',
            'warehouse_id' => '盘点仓库',
            'is_proof' => '是否校对',
            'timing_type' => '定时操作类型',
        ];
    }
}
