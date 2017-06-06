<?php
namespace common\models;

use Yii;
use yii\base\Model;

/**
 * Login form
 */
class LoginForm extends Model
{
    public $username;
    public $password;
    public $rememberMe = true;

    private $_user;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => '登陆名',
            'password' => '密码',
            'sn' => '表单号',
            'total_amount' => '总价',
            'warehouse_id' => '仓库ID',
            'create_admin_id' => '制表人',
            'create_time' => '制表时间',
            'supplier_id' => '供应商ID',
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array  $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if(!$this->hasErrors()){
            $user = $this->getUser();
            if(!$user || !$user->validatePassword($this->password)){
                $this->addError($attribute, '错误的密码或者用户名.');
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return boolean whether the user is logged in successfully
     */
    public function login()
    {
        if($this->validate()){
            $admin = $this->getUser();
            $result = Yii::$app->user->login($admin, $this->rememberMe ? 600 : 0);
            Yii::$app->session->set("loginTime", time());
            if($result){
                $admin->last_ip = Yii::$app->request->getUserIP();
                $admin->last_login = time();
                $admin->save();
            }
            return $result;
        }else{
            return false;
        }
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    protected function getUser()
    {
        if($this->_user === NULL){
            $this->_user = Admin::findByUsername($this->username);
        }
        if(!$this->_user) {
            $this->_user = Admin::findByJobNumber($this->username);
        }
        return $this->_user;
    }
}
