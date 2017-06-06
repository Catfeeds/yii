<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "AdminLog".
 *
 * @property integer $id
 * @property integer $admin_id
 * @property string  $content
 * @property integer $status
 * @property string  $type
 * @property string  $ip
 * @property string  $os
 * @property string  $browser
 * @property string  $create_time
 */
class AdminLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'adminlog';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['admin_id', 'content', 'status', 'ip', 'create_time'], 'required'],
            [['admin_id', 'status'], 'integer'],
            [['create_time'], 'safe'],
            [['content'], 'string', 'max' => 50],
            [['type', 'ip'], 'string', 'max' => 20],
            [['os', 'browser'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'admin_id' => '操作员ID',
            'content' => '内容',
            'status' => '状态：0 操作失败；1 操作成功',
            'type' => '操作类型',
            'ip' => '操作 IP',
            'os' => '操作系统',
            'browser' => '浏览器',
            'create_time' => 'Create Time',
        ];
    }
}
