<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "FlowConfig".
 *
 * @property integer $id
 * @property string $name
 * @property string $type
 * @property integer $operation_role_id
 * @property string $operation_name
 * @property integer $operation_department_id
 * @property integer $verify_role_id
 * @property string $verify_name
 * @property integer $verify_department_id
 * @property integer $approval_role_id
 * @property string $approval_name
 * @property integer $approval_department_id
 * @property integer $create_role_id
 * @property string $create_name
 * @property integer $create_department_id
 * @property integer $status
 */
class FlowConfig extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'FlowConfig';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'type'], 'required'],
            [['operation_role_id', 'operation_department_id', 'verify_role_id', 'verify_department_id', 'approval_role_id', 'approval_department_id', 'create_role_id', 'create_department_id', 'status'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['operation_name', 'verify_name', 'approval_name', 'create_name'], 'string', 'max' => 40],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '流程名称',
            'type' => '流程类型',
            'operation_role_id' => '完成角色',
            'operation_name' => '完成名称',
            'operation_department_id' => '完成部门',
            'verify_role_id' => '审核角色',
            'verify_name' => '审核名称',
            'verify_department_id' => '审核部门',
            'approval_role_id' => '批准角色',
            'approval_name' => '批准名称',
            'approval_department_id' => '批准部门',
            'create_role_id' => '创建角色',
            'create_name' => '创建名称',
            'create_department_id' => '创建部门',
            'status' => '0无效 1有效 99删除',
        ];
    }
}
