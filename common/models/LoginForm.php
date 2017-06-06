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
    public $isSafety = false;
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
//            Yii::$app->session->set("checkMac", true);
            // if(!Yii::$app->session->get("checkMac")) {
            //     $this->addError($attribute, '该电脑未授权，无法登录');
            // }
            if(!$user || !$user->validatePassword($this->password)){
                $this->addError($attribute, '错误的密码或者用户名.');
            }
            $setRole = Yii::$app->session->get("selRole");
            if($setRole != 0 && $setRole != $user->role_id) {
                $this->addError($attribute, '该角色无法在此电脑上登陆');
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
            if(!$this->isSafety && !$admin->is_safety) {
                Yii::$app->session->set("isSafety", 1);
                Yii::$app->session->set("username", $this->username);
                return false;
            }
            Yii::$app->session->set("loginTime", time());
            $result = Yii::$app->user->login($admin, $this->rememberMe ? 600 : 0);
            if($result){
                $lastTime = time();
                Yii::$app->session->set("safetyTime", $lastTime);
                Yii::$app->session->set("safetyIp", Yii::$app->request->getUserIP());
                $admin->last_ip = Yii::$app->request->getUserIP();
                $admin->last_login = $lastTime;
                $admin->is_safety = 0;
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
            if($this->_user && $this->_user->username !== $this->username) {
                $this->_user->addError("username", '错误的密码或者用户名.');
                return false;
            }
        }
        if(!$this->_user) {
            $this->_user = Admin::findByJobNumber($this->username);
            if($this->_user && $this->_user->job_number !== $this->username) {
                $this->_user->addError("username", '错误的密码或者用户名.');
                return false;
            }
        }
        return $this->_user;
    }
}
