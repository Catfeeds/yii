<?php
namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\BaseActiveRecord;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "FlowCondition".
 *
 * @property integer $id
 * @property integer $config_id
 * @property integer $type
 * @property string $name
 * @property string $upper_limit
 * @property string $lower_limit
 * @property integer $status
 */
class FlowCondition extends namespace\base\FlowCondition
{
    /**
     * 无效
     */
    const STATUS_NO = 0;
    /**
     * 有效
     */
    const STATUS_YES = 1;
    /**
     * 删除
     */
    const STATUS_DEL = 99;
    /**
     * 金额
     */
    const TYPE_PRICE = 1;
    /**
     * 时间
     */
    const TYPE_TIME = 2;
    /**
     * 部门
     */
    const TYPE_AREA = 3;
    /**
     * 供应商
     */
    const TYPE_SUPPLIER = 4;
    /**
     * 商品分类
     */
    const TYPE_CATEGORY = 5;
    
    public static $_status = [
    	self::STATUS_YES => '有效',
        self::STATUS_NO => '无效',
        self::STATUS_DEL => '删除',
    ];
    
    public static $_type = [
        self::TYPE_PRICE => "价格范围",
        self::TYPE_TIME => "时间范围",
        self::TYPE_AREA => "部门范围",
        self::TYPE_SUPPLIER => "供应商范围",
        self::TYPE_CATEGORY => "商品类别",
    ];

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    BaseActiveRecord::EVENT_BEFORE_INSERT => 'status',
                ],
                'value' => self::STATUS_YES,
            ]
        ];
    }
    
    public function showStatus()
    {
        return isset(self::$_status[$this->status]) ? self::$_status[$this->status] : "未知".$this->status;
    }
    
    public static function getStatusSelectData()
    {
        return self::$_status;
    }
    
    
    public function showType()
    {
        return isset(self::$_type[$this->type]) ? self::$_type[$this->type] : "未知".$this->type;
    }
    
    public static function getTypeSelectData()
    {
        return self::$_type;
    }
    
    public static function getConditionListByConfigId($configId)
    {
         $info = self::findByCondition(["config_id" => $configId])->all();
         if(!$info) {
             return "无";
         }
         $result = "";
         foreach ($info as $key => $val) {
             $result .= ($key + 1) . "、" . $val->name . "(状态：".$val->showStatus().")" . "<br>";
         }
         return $result;
    }
    
}
