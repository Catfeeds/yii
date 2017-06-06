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
//        self::STATUS_DEL => '删除',
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
        $rules = parent::rules();
        $childRules = [
            [['area_id'], 'required', "message" => $this->name."所属地区无效"],
            ['name', 'unique', 'targetClass' => '\common\models\Warehouse', 'message' => '仓库已存在！'],
            [['name', 'num'] , 'checkname' , 'skipOnEmpty' => false],
        ];
        return array_merge($rules, $childRules);
    }
    
    public function checkname($attribute , $params)
    {
        if(preg_match('/[^0-9a-zA-Z一-龥]/u',$this->$attribute)){
            $this->addError($attribute , ($attribute == "name" ? "仓库名称" : "分区编号").'不能有空格和特殊字符');
        }
    }
    
    public function showStatus()
    {
        return self::$_status[$this->status];
    }


    public static function getStatusSelectData()
    {
        return self::$_status;
    }
   
    public function showType()
    {
        return self::$_type[$this->type];
    }


    public static function getTypeSelectData()
    {
        return self::$_type;
    }
    
    public function showSale()
    {
        return self::$_saleList[$this->is_sale];
    }


    public static function getSaleSelectData()
    {
        return self::$_saleList;
    }
    
    /**
     * 根据仓库ID获取仓库名称
     * @param type $id 仓库ID
     * @return type
     * @author dean feng851028@163.com
     */
    public static function getNameById($id)
    {
         $model = self::findOne($id);
        return isset($model->name) ?  $model->name: '未知'.$id;
    }
    
    /**
     * 获取仓库列表
     * @param type $status 状态
     * @param type $isSale 是否销售
     * @param type $department_id 部门ID
     * @return type
     * @author dean feng851028@163.com
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
//        if(!Admin::checkSupperAdmin()){ 
//            $where = ["department_id" => Admin::getDepId()];
//        }
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
