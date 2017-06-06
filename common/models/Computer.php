<?php
namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\BaseActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "Computer".
 *
 * @property integer $id
 * @property string $name
 * @property string $num
 * @property string $level
 * @property integer $status
 * @property string $create_time
 * @property integer $role_id
 */
class Computer extends namespace\base\Computer {

    const STATUS_NO = 0;
    const STATUS_OK = 1;
    const STATUS_DEL = 99;

    private static $_position = [
            0 => '办公区',
            1 => '库房',
            2 => '柜台',
    ];
    private static $_type = [
            0 => '服务器',
            1 => '库管',
            2 => '财务',
    ];
    private static $_status = [
            self::STATUS_NO => '无效',
            self::STATUS_OK => '有效',
    ];

    /**
     * 默认保存数据
     * @return type
     */
    public function behaviors() {
        return [
                [
                        'class' => TimestampBehavior::className(),
                        'attributes' => [
                                BaseActiveRecord::EVENT_BEFORE_INSERT => 'create_time',
                        ],
                        'value' => new Expression('NOW()'),
                ]
        ];
    }

    /**
     * 字段规则
     */
    public function rules() {
        return [
                [['name', 'mac', 'role_id'], 'required'],
                [['status', 'type', 'position', 'role_id'], 'integer'],
                [['create_time'], 'safe'],
                [['name'], 'string', 'max' => 20, 'tooLong' => '业务计算机名称长度为20个字符'],
                [['mac'], 'checkmac', 'skipOnEmpty' => false],
                [['mac'], 'unique', 'targetClass' => '\common\models\Computer', 'message' => 'mac地址已存在！'],
                ['name', 'checkname', 'skipOnEmpty' => false],
        ];
    }

    /**
     * 验证参数不能有空格和特殊字符 
     */
    public function checkname($attribute, $params) {
        if (preg_match('/[^0-9a-zA-Z一-龥]/u', $this->$attribute)) {
            $this->addError($attribute, '计算机名称不能有空格和特殊字符');
        }
    }

    /**
     * 验证MAC是否符合
     * @param type $attribute
     */
    public function checkmac($attribute, $params) {
        $macArr = explode("-", $this->$attribute);
        if (count($macArr) != 6) {
            $this->addError($attribute, 'mac地址格式错误');
            return false;
        }
        foreach ($macArr as $val) {
            if (strlen($val) != 2) {
                $this->addError($attribute, 'mac地址格式错误');
                return false;
            }
        }
        return true;
    }

    /**
     * 展示状态
     */
    public function showStatus() {
        return isset(self::$_status[$this->status]) ? self::$_status[$this->status] : '';
    }

    /**
     * 展示类型
     */
    public function showType() {
        return isset(self::$_type[$this->type]) ? self::$_type[$this->type] : '';
    }

    /**
     * 展示位置
     */
    public function showPosition() {
        return isset(self::$_position[$this->position]) ? self::$_position[$this->position] : '';
    }

    /**
     * 获取类型列表
     */
    public static function getTypeSelectData() {
        return self::$_type;
    }

    /**
     * 获取位置列表
     */
    public static function getPositionSelectData() {
        return self::$_position;
    }

    /**
     * 获取状态列表
     */
    public static function getStatusSelectData() {
        return self::$_status;
    }

}
    