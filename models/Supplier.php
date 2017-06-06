<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\BaseActiveRecord;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
/**
 * This is the model class for table "Supplier".
 *
 * @property integer $id
 * @property string $name
 * @property string $num
 * @property string $level
 * @property integer $status
 * @property string $create_time
 * @property integer $pay_period
 */
class Supplier extends namespace\base\Supplier
{

    const STATUS_NO = 0;
    const STATUS_OK = 1;
    const STATUS_DEL = 99;
    
    const PAY_PERIOD_DAY = 1;
    const PAY_PERIOD_WEEK = 2;
    const PAY_PERIOD_MONTH = 3;
    const PAY_PERIOD_QUARTER = 4;
    const PAY_PERIOD_YEAR = 5;
    
    private static $_level = [
        'A' => 'A',
        'B' => 'B',
        'C' => 'C',
        'D' => 'D',
    ];

    private static $_status = [
        self::STATUS_OK => '有效',
        self::STATUS_NO => '无效',
//        self::STATUS_DEL => '删除',
    ];
    
    private static $_payPeriod = [
        self::PAY_PERIOD_DAY => '日结',
        self::PAY_PERIOD_WEEK => '周结',
        self::PAY_PERIOD_MONTH => '月结',
        self::PAY_PERIOD_QUARTER => '季度结',
        self::PAY_PERIOD_YEAR => '年结',
    ];

    public function behaviors()
    {
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

    public function rules()
    {
        return [
            [['name', 'num', 'pay_period'], 'required', 'message' => '{attribute}不能为空'],
            [['status', 'pay_period'], 'integer'],
            [['create_time'], 'safe'],
            [['name', 'num'], 'string', 'max' => 40],
            [['level'], 'string', 'max' => 2],
            ['name', 'unique', 'targetClass' => '\common\models\Supplier', 'message' => '供应商已存在！'],
            [['name', 'num'] , 'checkname' , 'skipOnEmpty' => false],
        ];
    }
    public function checkname($attribute , $params)
    {
        if(preg_match('/[^0-9a-zA-Z一-龥]/u',$this->$attribute)){
            $this->addError($attribute , ($attribute == "name" ? "供应商名称" : "供应商编号").'不能有空格和特殊字符');
        }
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '供应商名称',
            'num' => '供应商编号',
            'level' => '分级',
            'status' => '状态',
            'create_time' => '创建时间',
            'pay_period' => '付款账期',
        ];
    }

    public function showName()
    {
        return $this->name;
    }

    public function showNumber()
    {
        return $this->num;
    }

    public function showLevel()
    {
        return $this->level;
    }

    public function showStatus()
    {
        return self::$_status[$this->status];
    }

    public function showPayPeriod()
    {
        return self::$_payPeriod[$this->pay_period];
    }
    
    public static function getLevelSelectData()
    {
        return self::$_level;
    }

    public static function getStatusSelectData()
    {
        return self::$_status;
    }
    
    public static function getPayPeriodSelectData()
    {
        return self::$_payPeriod;
    }
    
    public static function getSupplierSelectData($status = 1)
    {
        if(is_numeric($status)) {
            $info = self::findByCondition(["status" => $status])->all();
        } else {
            $info = self::find()->all();
        }
        return ArrayHelper::map($info, "id", "name");
    }
    
    public static function getNameById($id) 
    {
        $model = self::findOne($id);
        return isset($model->name) ?  $model->name: '无';
    }
}
