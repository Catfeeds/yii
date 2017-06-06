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
    public static function showBatchesName($is_batches)
    {
        return isset(self::$_batchesAll[$is_batches]) ? self::$_batchesAll[$is_batches] : "未知".$is_batches;
    }
    
    public static function getBatchesSelectData()
    {
        return self::$_batchesAll;
    }

    public static function getCatrgorySelectData() {
        $info = self::findByCondition(["status" => self::STATUS_YES, "parent_id" => 0])->all();
        return ArrayHelper::map($info, "id", "name");
    }
    
    /**
     * 根据分类ID获取分类名称
     * @param type $cateId 分类ID
     * @return type
     * @aurhor dean feng851028@163.com
     */
    public static function getNameById($cateId) {
        $item = self::findOne($cateId);
        return $item ? $item->name : "无";
    }
     public function showName()
    {
        return $this->name;
    }
    
    public function showStatus()
    {
        return self::$_status[$this->status];
    }



    public static function getStatusSelectData()
    {
        return self::$_status;
    }
}
