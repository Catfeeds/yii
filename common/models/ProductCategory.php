<?php
namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\BaseActiveRecord;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
/**
 * This is the model class for table "ProductCategory".
 *
 * @property string $id
 * @property string $parent_id
 * @property string $name
 * @property string $slug
 * @property integer $factor
 * @property integer $status
 * @property string $sort
 * @property integer $is_batches
 */
class ProductCategory extends namespace\base\ProductCategory
{
    const STATUS_YES = 1;
    const STATUS_NO = 0;
    const STATUS_OK = 1;
    const STATUS_DEL = 99;
    private static $_status = [
        self::STATUS_OK => '有效',
        self::STATUS_NO => '无效',
    ];
    /**
     * 是否需要批次号 -- 需要
     */
    const IS_BATCHES_YES = 1;
    /**
     * 是否需要批次号 -- 不需要
     */
    const IS_BATCHES_NO = 0;    
    private static $_batchesAll = [
        self::IS_BATCHES_NO => '不需要',
        self::IS_BATCHES_YES => '需要',
    ];
    
    /**
     * 字段类型
     */
    public function rules()
    {
        $rules = parent::rules();
        $childRules = [
            [['name'], 'string', 'max' => 12, 'tooLong'=>'{attribute}的长度不能超过12个字符'],
            [['name'], 'unique', 'targetClass' => '\common\models\ProductCategory', 'message' => '{attribute}已存在！'],
            [['name'] , 'checkname' , 'skipOnEmpty' => false],
        ];
        return array_merge($rules, $childRules);
    }
    
    /**
     * 验证参数不能有空格和特殊字符 
     */
    public function checkname($attribute , $params)
    {
        if(preg_match('/[^0-9a-zA-Z一-龥]/u',$this->$attribute)){
            $this->addError($attribute , '物料名称不能有空格和特殊字符');
        }
    }
    
    /**
     * 展示是否需要批次号
     * @param type $is_batches 是否需要批次号
     * @return type
     */
    public static function showBatchesName($is_batches)
    {
        return isset(self::$_batchesAll[$is_batches]) ? self::$_batchesAll[$is_batches] : "未知".$is_batches;
    }
    
    /**
     * 展示是否需要批次号列表
     */
    public static function getBatchesSelectData()
    {
        return self::$_batchesAll;
    }

    /**
     * 获取所有有效的顶级分类
     */
    public static function getCatrgorySelectData() {
        $info = self::findByCondition(["status" => self::STATUS_YES, "parent_id" => 0])->all();
        return ArrayHelper::map($info, "id", "name");
    }
    
    /**
     * 根据分类ID获取分类名称
     * @param type $cateId 分类ID
     * @return type
     */
    public static function getNameById($cateId) {
        $item = self::findOne($cateId);
        return $item ? $item->name : "无";
    }
    
    /**
     * 展示名称
     */
    public function showName()
    {
        return $this->name;
    }
    
    /**
     * 展示状态
     */
    public function showStatus()
    {
        return self::$_status[$this->status];
    }
    
    /**
     * 获取所有状态列表
     */
    public static function getStatusSelectData()
    {
        return self::$_status;
    }
}
