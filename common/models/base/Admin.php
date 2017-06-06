<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "Admin".
 *
 * @property string $id
 * @property string $username
 * @property string $password
 * @property string $auth_key
 * @property string $mobile
 * @property integer $department_id
 * @property integer $role_id
 * @property string $role
 * @property integer $last_login
 * @property string $last_ip
 * @property integer $status
 * @property string $name
 * @property string $id_card
 * @property string $job_number
 * @property string $entry_date
 * @property string $leave_date
 * @property string $create_time
 * @property string $update_time
 * @property integert $is_first
 * @property integert $is_safety
 */
class Admin extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'admin';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'password', 'auth_key', 'mobile'], 'required'],
            [['department_id', 'role_id', 'last_login', 'status', 'is_first', 'is_safety'], 'integer'],
            [['entry_date', 'leave_date', 'create_time', 'update_time'], 'safe'],
            [['username'], 'string', 'max' => 45],
            [['password'], 'string', 'max' => 64],
            [['auth_key'], 'string', 'max' => 32],
            [['mobile', 'last_ip'], 'string', 'max' => 15],
            [['name', 'id_card', 'job_number'], 'string', 'max' => 100],
           
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '管理员 ID',
            'username' => '姓名',
            'password' => '密码',
            'auth_key' => '校验码',
            'mobile' => '联系电话',
            'department_id' => '部门ID',
            'role_id' => '管理员角色ID',
            'role' => '角色',
            'last_login' => '最后登录时间',
            'last_ip' => '最后登录 IP',
            'status' => '账号状态 0: 禁用 1: 启用 99：删除',
            'name' => '姓名',
            'id_card' => '证件号',
            'job_number' => '工号',
            'entry_date' => '入职日期',
            'leave_date' => '离职日期',
            'create_time' => '创建日期',
            'update_time' => '更新日期',
            'is_first' => '是否首次登录 1：是 0：否',
            'is_safety' => '是否安全登出 1：是 0：否',
        ];
    }
}
