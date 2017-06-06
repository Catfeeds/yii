<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "BusinessAll".
 *
 * @property integer $id
 * @property integer $business_id
 * @property string $business_type
 * @property integer $department_id
 * @property integer $warehouse_id
 * @property string $name
 * @property string $sn
 * @property integer $create_admin_id
 * @property integer $verify_admin_id
 * @property string $verify_time
 * @property integer $approval_admin_id
 * @property string $approval_time
 * @property integer $operation_admin_id
 * @property string $operation_time
 * @property integer $status
 * @property string $create_time
 */
class BusinessAll extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'BusinessAll';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['business_id', 'business_type', 'name', 'sn', 'operation_admin_id', 'create_time', 'department_id', 'warehouse_id'], 'required'],
            [['business_id', 'create_admin_id', 'verify_admin_id', 'approval_admin_id', 'operation_admin_id', 'status', 'department_id', 'warehouse_id'], 'integer'],
            [['verify_time', 'approval_time', 'operation_time', 'create_time'], 'safe'],
            [['name'], 'string', 'max' => 255],
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
            'business_id' => '业务ID',
            'business_type' => '业务类型',
            'department_id' => '所属部门',
            'warehouse_id' => '所属仓库',
            'name' => '表单名',
            'sn' => '表单号',
            'create_admin_id' => '制表人',
            'create_time' => '制表时间',
            'verify_admin_id' => '审核人',
            'verify_time' => '审核时间',
            'approval_admin_id' => '批准人',
            'approval_time' => '批准时间',
            'operation_admin_id' => '完成人',
            'operation_time' => '完成时间',
            'status' => '状态',
        ];
    }
}
