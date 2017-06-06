<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\BaseActiveRecord;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "SupplierProduct".
 *
 * @property integer $id
 * @property string $name
 * @property double $purchase_price
 * @property integer $supplier_id
 * @property string $num
 * @property string $spec
 * @property string $unit
 * @property string $material_type
 * @property integer $status
 */
class SupplierProduct extends namespace\base\SupplierProduct
{
    const STATUS_NO = 0;
    const STATUS_YES = 1;
    const STATUS_DEL = 99;
    
    public static $_status = [
        self::STATUS_NO => "无效",
        self::STATUS_YES => "有效",
    ];
    
    const TYPE_PRODUCT = 1; //商品
    const MODIFY_ASSETS = 2;
    
    private static $_type = [
        self::TYPE_PRODUCT => '商品',
        self::MODIFY_ASSETS => '资产',
    ];
    
    /**
     * 保存默认值
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    BaseActiveRecord::EVENT_BEFORE_INSERT => 'status',
                ],
                'value' => self::STATUS_NO,
            ]
        ];
    }
    
    /**
     * 展示供应商出品状态
     */
    public function showStatus()
    {
        return $this->is_update ? "修改中" : (isset(self::$_status[$this->status]) ? self::$_status[$this->status] : "未知".$this->status);
    }
    
    /**
     * 获取供应商出品状态列表
     */
    public static function getStatusSelectData() 
    {
        return self::$_status;
    }
    
    /**
     * 获取供应商出品类型列表
     */
    public static function getTypeSelectData()
    {
        return self::$_type;
    }
    
    /**
     * 展示供应商出品类型
     */
    public function showType()
    {
        return isset(self::$_type[$this->material_type]) ? self::$_type[$this->material_type] : "未知".$this->material_type;
    }
    
    /**
     * 字段规则
     */
    public function rules()
    {
        $rules = parent::rules();
        $childRules = [
            [['name', 'num', 'spec', 'unit'] , 'checkname' , 'skipOnEmpty' => false],
            [['name'], 'string', 'max' => 12, 'tooLong'=>'{attribute}的长度不能超过12个字符'],
            [['num'], 'string', 'max' => 20, 'tooLong'=>'{attribute}的长度不能超过20个字符'],
            [['spec', 'unit'], 'string', 'max' => 10, 'tooLong'=>'{attribute}的长度不能超过10个字符'],
            [['purchase_price'], 'number', 'max' => 99999999, 'tooBig'=>'{attribute}的长度不能超过8个字符'],
            [['num'], 'unique', 'targetClass' => '\common\models\SupplierProduct', 'message' => '{attribute}已存在！'],
        ];
        return array_merge($rules, $childRules);
    }
    
    /**
     * 验证参数不能有空格和特殊字符 
     */
    public function checkname($attribute , $params)
    {
        if(preg_match('/[^0-9a-zA-Z一-龥]/u',$this->$attribute)){
            $this->addError($attribute , ($attribute == "name" ? "名称" : ($attribute == "num" ? "供应商出品编码" : ($attribute == "spec" ? "规格" : "单位"))).'不能有空格和特殊字符');
        }
    }
}
