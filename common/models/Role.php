<?php
namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "Role".
 *
 * @property integer $id
 * @property string $name
 * @property integer $department_id
 * @property integer $status
 * @property integer $is_sole
 */
class Role extends namespace\base\Role {

    const STATUS_NO = 0;
    const STATUS_OK = 1;
    const STATUS_DEL = 99;

    private static $_status = [
            self::STATUS_OK => '有效',
            self::STATUS_NO => '无效',
    ];

    /**
     * 字段规则
     */
    public function rules() {
        return [
                [['name', 'department_id', 'status'], 'required'],
                [['department_id', 'status', 'is_sole'], 'integer'],
                [['name'], 'string', 'max' => 12, 'tooLong'=>'{attribute}的长度不能超过12个字符'],
                ['name', 'checkname', 'skipOnEmpty' => false],
                ['name', 'unique', 'targetClass' => '\common\models\Role', 'message' => '角色已存在！'],
        ];
    }

    /**
     * 验证参数不能有空格和特殊字符 
     */
    public function checkname($attribute, $params) {
        if (preg_match('/[^0-9a-zA-Z一-龥]/u', $this->$attribute)) {
            $this->addError($attribute, '角色不能有空格和特殊字符');
        }
    }

    /**
     * 字段设置别名
     */
    public function attributeLabels() {
        return [
                'id' => 'ID',
                'name' => '角色',
                'department_id' => '所属部门',
                'status' => '状态',
                'is_sole' => '是否唯一',
        ];
    }

    /**
     * 展示所属部门名称
     */
    public function showDeparmentName() {
        $model = Department::findOne($this->department_id);
        return isset($model->name) ? $model->name : '无';
    }

    /**
     * 展示角色名称
     */
    public function showName() {
        return $this->name;
    }

    /**
     * 展示状态
     */
    public function showStatus() {
        return self::$_status[$this->status];
    }

    /**
     * 根据状态获取所有角色列表
     * @param type $status 角色状态
     * @return type
     */
    public static function getAllRoleByStatus($status = "") {
        if (is_numeric($status)) {
            $info = self::findByCondition(["status" => $status])->all();
        } else {
            $info = self::find()->all();
        }
        $result = ArrayHelper::map($info, "id", "name");
        unset($result[1]);
        return $result;
    }

    /**
     * 根据角色ID获取角色名称
     * @param type $roleId 角色ID
     * @return type
     */
    public static function getNameByRoleId($roleId) {
        $model = self::findOne($roleId);
        return isset($model->name) ? $model->name : '无';
    }
    
    /**
     * 根据部门ID或角色类型获取角色列表
     * @param type $departmentId 部门ID
     * @param type $type 角色类型
     * @return type
     */
    public static function getListByDepartmentId($departmentId, $type) {
        $query = Role::find();
        $query->andWhere(["status" => 1]);
        if(is_numeric($departmentId)) {
            $query->andWhere(["department_id" => $departmentId]);
        }
        if($type != "create") {
            $query->andWhere(["is_sole" => 1]);
        }
        $data = $query->all();
        $return = ArrayHelper::map($data, "id", "name");
        return $return;
    }

    /**
     * 获取状态列表
     */
    public static function getStatusSelectData() {
        return self::$_status;
    }

    /**
     * 获取是否唯一列表
     */
    public static function getSoleSelectData() {
        return [0 => "否", 1 => "是"];
    }
    
    /**
     * 展示是否唯一
     */
    public function showSole() {
        return $this->is_sole ? "是" : "否";
    }
}
    