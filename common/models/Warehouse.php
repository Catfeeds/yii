<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\BaseActiveRecord;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use common\models\Department;
use common\models\Admin;

/**
 * This is the model class for table "Warehouse".
 *
 * @property integer $id
 * @property string $name
 * @property integer $type
 * @property integer $status
 * @property integer $area_id
 * @property integer $is_sale
 * @property string $num
 * @property datetime $create_time
 */
class Warehouse extends namespace\base\Warehouse
{

    const STATUS_NO = 0;
    const STATUS_OK = 1;
    const STATUS_DEL = 99;
    /**
     * 仓库--总部
     */
    const WAREHOUSE_HQ = 1;

    private static $_status = [
        self::STATUS_OK => '有效',
        self::STATUS_NO => '无效',
    ];
    
    private static $_type = [
        '1' => '存贮',
        '2' => '存贮销售',
        '3' => '销售',
    ];
    
    const SALE_NO = 0;
    CONST SALE_YES = 1;
    
    private static $_saleList = [
        self::SALE_NO => '不销售',
        self::SALE_YES => '销售',
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
                    BaseActiveRecord::EVENT_BEFORE_INSERT => 'create_time',
                ],
                'value' => new Expression('NOW()'),
            ]
        ];
    }

    /**
     * 字段规则
     */
    public function rules()
    {
        $rules = parent::rules();
        $childRules = [
            [['area_id'], 'required', "message" => $this->name."所属地区无效"],
            [['num'], 'string', 'max' => 20, 'tooLong'=>'{attribute}的长度不能超过20个字符'],
            [['name'], 'string', 'max' => 12, 'tooLong'=>'{attribute}的长度不能超过12个字符'],
            [['name', 'num'], 'unique', 'targetClass' => '\common\models\Warehouse', 'message' => '{attribute}已存在！'],
            [['name', 'num'] , 'checkname' , 'skipOnEmpty' => false],
        ];
        return array_merge($rules, $childRules);
    }
    
    /**
     * 验证参数不能有空格和特殊字符 
     */
    public function checkname($attribute , $params)
    {
        if(preg_match('/[^0-9a-zA-Z一-龥]/u',$this->$attribute)){
            $this->addError($attribute , ($attribute == "name" ? "仓库名称" : "分区编号").'不能有空格和特殊字符');
        }
    }
    
    /**
     * 展示仓库状态
     */
    public function showStatus()
    {
        return self::$_status[$this->status];
    }

    /**
     * 获取仓库状态列表
     */
    public static function getStatusSelectData()
    {
        return self::$_status;
    }
   
    /**
     * 展示仓库类型
     */
    public function showType()
    {
        return self::$_type[$this->type];
    }

    /**
     * 获取仓库类型列表
     */
    public static function getTypeSelectData()
    {
        return self::$_type;
    }
    
    /**
     * 展示仓库是否销售
     */
    public function showSale()
    {
        return self::$_saleList[$this->is_sale];
    }

    /**
     * 获取仓库是否销售列表
     */
    public static function getSaleSelectData()
    {
        return self::$_saleList;
    }
    
    /**
     * 根据仓库ID获取仓库名称
     * @param type $id 仓库ID
     * @return type
     */
    public static function getNameById($id)
    {
         $model = self::findOne($id);
        return isset($model->name) ?  $model->name: '无';
    }
    
    /**
     * 获取仓库列表
     * @param type $status 状态
     * @param type $isSale 是否销售
     * @param type $department_id 部门ID
     * @return type
     */
    public static function getAllByStatus($status = "", $isSale = "", $department_id = "")
    {
        $where = [];
        if(is_numeric($status)) {
            $where["status"] = $status;
        }
        if(is_numeric($isSale)) {
            $where["is_sale"] = $isSale;
        }
        if(is_numeric($department_id) && $department_id > 0) {
            $where["department_id"] = $department_id;
        }
        if($where) {
            $info = self::findByCondition($where)->all();
        } else {
            $info = self::find()->all();
        }
        return ArrayHelper::map($info, "id", "name");
    }
    
    /**
     * 通过仓库获取部门名称
     * @param type $warehouseId 仓库ID
     * @return string
     */
    public static function getDepartmentNameByWarehouseId($warehouseId) {
        $warehouseItem = Warehouse::findOne($warehouseId);
        if(!$warehouseItem) {
            return "未绑定";
        }
        $departmentItem = Department::findOne($warehouseItem->department_id);
        return $departmentItem ? $departmentItem->name : "未绑定";
    }
}
