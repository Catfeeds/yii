<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "FlowCondition".
 *
 * @property integer $id
 * @property integer $config_id
 * @property integer $type
 * @property string $name
 * @property string $upper_limit
 * @property string $lower_limit
 * @property integer $status
 */
class FlowCondition extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'flowcondition';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'status', 'config_id'], 'integer'],
            [['name', 'config_id'], 'required'],
            [['name'], 'string', 'max' => 255],
            [['upper_limit', 'lower_limit'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'config_id' => '流程ID',
            'type' => '1 价格范围 2时间范围 3空间位置4 供应商范围 5商品类别',
            'name' => 'Name',
            'upper_limit' => '上限',
            'lower_limit' => '下线',
            'status' => '0无效 1有效 99删除',
        ];
    }
}
