<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "FlowConfigStep".
 *
 * @property integer $id
 * @property string $name
 * @property integer $create_step
 * @property integer $verify_step
 * @property integer $approval_step
 * @property integer $operation_step
 * @property string $business_begin_table
 * @property string $business_end_table
 * @property integer $config_sn
 */
class FlowConfigStep extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'FlowConfigStep';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['create_step', 'verify_step', 'approval_step', 'operation_step',  'config_sn'], 'required'],
            [['create_step', 'verify_step', 'approval_step', 'operation_step', 'config_sn', 'business_end_table'], 'integer'],
            [['name'], 'string', 'max' => 200],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '流程类名称',
            'create_step' => '创建步骤',
            'verify_step' => '审核步骤',
            'approval_step' => '批准步骤',
            'operation_step' => '执行步骤',
            'business_begin_table' => '业务操作表单',
            'business_end_table' => '业务终止表单',
            'config_sn' => '流程类标识',
        ];
    }
}
