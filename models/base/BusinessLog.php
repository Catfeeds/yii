<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "BusinessLog".
 *
 * @property integer $id
 * @property integer $business_id
 * @property string $business_type
 * @property string $content
 * @property integer $status
 * @property string $create_time
 * @property integer $admin_id
 */
class BusinessLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'BusinessLog';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'business_id', 'business_type', 'content', 'status', 'create_time', 'admin_id'], 'required'],
            [['id', 'business_id', 'status', 'admin_id'], 'integer'],
            [['create_time'], 'safe'],
            [['business_type'], 'string', 'max' => 40],
            [['content'], 'string', 'max' => 1000],
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
            'business_type' => '业务类型 对应表名',
            'content' => '操作内容',
            'status' => '0 无效 1有效 99删除',
            'create_time' => '创建时间',
            'admin_id' => '操作人用户ID',
        ];
    }
}
