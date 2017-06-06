<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\web\IdentityInterface;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "Admin".
 *
 * @property string  $id
 * @property string  $username
 * @property string  $password
 * @property string  $auth_key
 * @property string  $mobile
 * @property integer $department_id
 * @property integer $role_id
 * @property integer $last_login
 * @property string  $last_ip
 * @property integer $status
 * @property string  $name
 * @property string  $id_card
 * @property string  $job_number
 * @property string  $entry_date
 * @property string  $leave_date
 * @property string  $create_time
 * @property string  $update_time
 * @property integert $is_first
 * @property integert $is_safety
 */
class Admin extends namespace\base\Admin implements IdentityInterface
{
    const STATUS_DELETED = 0; // 禁用
    const STATUS_ACTIVE = 1; // 启用
    
    private static $_status = [
    	 self::STATUS_ACTIVE => '有效',
        self::STATUS_DELETED => '无效',
    ];

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'create_time',
                'updatedAtAttribute' => 'update_time',
                'value' => new Expression('NOW()'),
            ]
        ];
    }

    public function rules()
    {
        return [
            [['username', 'id_card', 'job_number','department_id','entry_date'], 'required'],
            [['department_id', 'role_id', 'last_login', 'status'], 'integer'],
            [['entry_date', 'leave_date', 'create_time', 'update_time'], 'safe'],
            [['username'], 'string', 'max' => 45],
            [['password'], 'string', 'max' => 64],
            [['auth_key'], 'string', 'max' => 32],
            [['mobile', 'last_ip'], 'string', 'max' => 15],
            [['name', 'id_card', 'job_number'], 'string', 'max' => 100],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['password', 'default', 'value' => '123456'],
            [['username', 'job_number', 'id_card'] , 'checkname' , 'skipOnEmpty' => false],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],
            [['username', 'job_number'], 'unique', 'targetClass' => '\common\models\Admin', 'message' => '{attribute}已存在！'],
        ];
    }
    public function checkname($attribute , $params)
    {
        if(preg_match('/[^0-9a-zA-Z一-龥]/u',$this->$attribute)){
            $this->addError($attribute , ($attribute == "username" ? "姓名" : ($attribute == "job_number" ? "工号" : "证件号")).'不能有空格和特殊字符');
        }
    }
    public function attributeLabels()
    {
        return [
            'id' => '管理员 ID',
            'username' => '姓名',
            'password' => '密码',
            'auth_key' => '校验码',
            'mobile' => '联系电话',
            'department_id' => '部门ID',
            'role_id' => '角色ID',
            'role' => '角色',
            'last_login' => '最后登录时间',
            'last_ip' => '最后登录 IP',
            'status' => '状态',
            'name' => '姓名',
            'id_card' => '证件号',
            'job_number' => '工号',
            'entry_date' => '入职日期',
            'leave_date' => '离职日期',
            'create_time' => '创建日期',
            'update_time' => '更新日期',
        ];
    }
    public function beforeSave($insert) {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                $this->auth_key = Yii::$app->security->generateRandomString(); //自动添加随机auth_key
                $this->password = Yii::$app->security->generatePasswordHash('123456'); //密码加密
            }
            return true;
        }
        return false;
    }
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * Validates Auth Key
     *
     * @param string $authKey auth key to validate
     *
     * @return boolean if auth key provided is valid for current user
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     *
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password = '')
    {
        if(!$password){
            $password = $this->password;
        }

        if($password){
            $this->password = Yii::$app->security->generatePasswordHash($password);
        }
    }

    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    public static function findIdentityByAccessToken($token, $type = NULL)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     *
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }
    
    public static function findByJobNumber($jobNumber)
    {
        return static::findOne(['job_number' => $jobNumber, 'status' => self::STATUS_ACTIVE]);
    }
    
    public static function getStatusSelectData()
    {
        return self::$_status;
    }
    
     public function showName()
    {
        return $this->username;
    }
    
    public function showDeparmentName(){
     	$model = Department::findOne($this->department_id);
        return isset($model->name) ?  $model->name: '无';
    }
    
    public function showRoleName(){
     	$model = Role::findOne($this->role_id);
        return isset($model->name) ?  $model->name: '无';
    }
    
    public function showStatus()
    {
        return self::$_status[$this->status];
    }
    
    public function checkOnly() {
        $role = Role::findOne($this->role_id);
        if(!$role->is_sole) {
            return true;
        }
        $model = Admin::findOne(["role_id" => $this->role_id]);
        if(!$model) {
            return true;
        }
        return $model->username == $this->username && $model->id == $this->id ? true : false;
    }
    
    public static function getNameById($adminId) 
    {
        $model = Admin::findOne($adminId);
        return isset($model->username) ?  $model->username: '无';
    }
    
    public static function getAdminId($dep_id, $role_id)
    {
          return intval(self::findByCondition(["department_id" => $dep_id,'role_id'=>$role_id, 'status' =>1] )->scalar()); 
    }
    
    //获取用户部门
    public static function getDepName() 
    {
    	$model = Admin::findOne(Yii::$app->user->id);
    	if($model){
        	$model = Department::findOne($model->department_id);
    	}
        return isset($model->name) ?  $model->name: '无';
    }
    //获取用户部门
    public static function getDepId() 
    {
        return Yii::$app->user->getIdentity()->department_id;
//    	$model = Admin::findOne(Yii::$app->user->id);
//    	if($model){
//            return $model->department_id;
//    	}
//        return "";
    }
    
    //获取用户部门下面的仓库
    public static function getWarehouseIdsById() 
    {
        $warehousetAll = Warehouse::findAll(['department_id' => Yii::$app->user->getIdentity()->department_id]);
        $warehouseIds = ArrayHelper::getColumn($warehousetAll, 'id');
        return $warehouseIds;
    }
    
    public static function checkSupperAdmin() {
        return Yii::$app->user->id == 1 ? true : false;
    }

    public static function checkFlowAdmin() {
    	return Yii::$app->user->getIdentity()->role_id == 2 ? true : false;
    }
    
    public static function checkBusinAdmin() {
    	return Yii::$app->user->getIdentity()->role_id == 3 ? true : false;
    }

}
