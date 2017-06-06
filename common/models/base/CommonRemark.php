<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "CommonRemark".
 *
 * @property integer $id
 * @property integer $flow_id
 * @property integer $flow_type
 * @property string $remark
 * @property integer $type
 */
class CommonRemark extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'commonremark';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['flow_id', 'flow_type'], 'required'],
            [['flow_id', 'flow_type', 'type'], 'integer'],
            [['remark'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'flow_id' => '流程ID',
            'flow_type' => '流程类型',
            'remark' => '操作说明',
            'type' => '类型 1【审核】2【批准】3【执行】',
        ];
    }
}
