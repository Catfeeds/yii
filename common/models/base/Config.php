<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "Config".
 *
 * @property integer $id
 * @property string $set_name
 * @property string $set_value
 * @property string $set_desc
 * @property integer $group_id
 */
class Config extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'config';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['set_name', 'set_value', 'set_desc'], 'required'],
            [['group_id'], 'integer'],
            [['set_name'], 'string', 'max' => 100],
            [['set_value', 'set_desc'], 'string', 'max' => 1000],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'set_name' => '设置名称',
            'set_value' => '设置值',
            'set_desc' => '描述',
            'group_id' => '分组 1 logo 2名称',
        ];
    }
}
