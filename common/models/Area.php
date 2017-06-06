<?php
namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;
/**
 * This is the model class for table "Area".
 *
 * @property integer $id
 * @property string $name
 * @property integer $parentId
 * @property integer $status
 * @property integer $sort
 */
class Area extends namespace\base\Area 
{
    const STATUS_NO = 0;
    const STATUS_OK = 1;
    
    private static $_statusList = [
        self::STATUS_OK => '有效',
        self::STATUS_NO => '无效',
    ];
    
    /**
     * 展示状态名称
     */
    public function showStatus()
    {
        return self::$_statusList[$this->status];
    }

    /**
     * 获取状态列表
     */
    public static function getStatusSelectData()
    {
        return self::$_statusList;
    }
    
    /**
     * 获取有效的下级地区列表
     * @param type $parentId 父类地区ID
     * @param type $status　状态
     * @return type
     */
    public static function getAreaSelectData($parentId = 0, $status = "")
    {
        if(is_numeric($status)) {
            $info = self::findByCondition(["parentId" => $parentId, "status" => $status])->all();
        } else {
            $info = self::findByCondition(["parentId" => $parentId])->all();
        }
        return ArrayHelper::map($info, "id", "name");
    }
    
    /**
     * 获取父类地区名称
     */
    public function showParentName()
    {
        if(!$this->parentId) {
            return "无";
        }
        $item = self::findOne($this->parentId);
        return $item ? $item->name : "未知".$this->parentId;
    }
    
    /**
     * 获取地区的全名从最父级开始
     * @param type $id 地区ID
     * @return type
     */
    public static function getNameById($id)
    {
        $info = self::findOne($id);
        if(!$info) {
            return "未知".$id;
        }
        $result = "";
        if($info->parentId) {
            $result = self::getNameById($info->parentId);
        }
        return $result . $info->name;
    }
    
    /**
     * 根据地区ID获取地区相连的父子级地区列表
     * @param type $id
     * @return int
     */
    public static function getParentIdsById($id)
    {
        $item = self::findOne($id);
        $result = ['provinceId' => 0, 'cityId' => '', 'areaId' => ''];
        if(!$item) {
            return $result;
        }
        if($item->parentId == 0) {
            $result['provinceId'] = $id;
            return $result;
        }
        $childItem = self::findOne(["parentId" => $item->id]);
        if($childItem) {
            $result['provinceId'] = $item->parentId;
            $result['cityId'] = $id;
            return $result;
        }
        $result['areaId'] = $id;
        $result['cityId'] = $item->parentId;
        $parentItem = self::findOne($item->parentId);
        $result['provinceId'] = $parentItem->parentId;
        return $result;
    }
}

