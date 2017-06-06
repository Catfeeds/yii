<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "BusinessRemind".
 *
 * @property integer $id
 * @property integer $business_id
 * @property string $business_type
 * @property integer $business_state
 * @property integer $status
 * @property string $create_time
 * @property integer $admin_id
 */
class BusinessRemind extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'BusinessRemind';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['business_id', 'business_type', 'business_state','status', 'create_time', 'admin_id'], 'required'],
            [['business_id', 'business_state','status', 'admin_id'], 'integer'],
            [['create_time'], 'safe'],
            [['business_type'], 'string', 'max' => 40],
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
            'business_state' => '业务状态',
            'status' => '0 无效 1有效 99删除',
            'create_time' => '制表时间',
            'admin_id' => '接受人用户ID',
        ];
    }
}
