<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "RoleAuth".
 *
 * @property integer $role_id
 * @property string $auth
 */
class RoleAuth extends namespace\base\RoleAuth
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'RoleAuth';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['role_id', 'auth'], 'required'],
            [['role_id'], 'integer'],
            [['auth'], 'string', 'max' => 40],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'role_id' => 'roleID',
            'auth' => '权限',
        ];
    }
}
