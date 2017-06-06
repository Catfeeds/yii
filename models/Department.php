<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\BaseActiveRecord;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "Department".
 *
 * @property integer $id
 * @property string $name
 * @property integer $parent_id
 * @property integer $status
 * @property string $create_time
 * @property string $number
 * @property string $acronym
 */
class Department extends namespace\base\Department
{
    const STATUS_NO = 0;
    const STATUS_OK = 1;
    const STATUS_DEL = 99;
    
     private static $_status = [
        self::STATUS_OK => '有效',
        self::STATUS_NO => '无效',
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
            [['name','number',  'acronym'], 'required'],
            [['status','parent_id'], 'integer'],
            [['create_time'], 'safe'],
            [['name','number'], 'string', 'max' => 20],
            [['acronym'], 'string', 'max' => 10],
            [['name', 'number', 'acronym'] , 'checkname' , 'skipOnEmpty' => false],
            ['name', 'unique', 'targetClass' => '\common\models\Department', 'message' => '部门已存在！'],
        ];
    }
	public function checkname($attribute , $params)
    {
        if(preg_match('/[^0-9a-zA-Z一-龥]/u',$this->$attribute)){
            $this->addError($attribute , ($attribute == "name" ? "部门名称" : ($attribute == "number" ? "部门编号" : "部门缩写")).'不能有空格和特殊字符');
        }
    }
  

   public function showStatus()
    {
        return isset(self::$_status[$this->status]) ? self::$_status[$this->status] : '' ;
    }
   

    public static function getStatusSelectData()
    {
        return self::$_status;
    }
    
      public function showName()
    {
        return $this->name;
    }
    
     public function showNumber()
    {
        return $this->number;
    }
    
    public function showParentName(){
     	$model = Department::findOne($this->parent_id);
        return isset($model->name) ?  $model->name: '无';
    }
    
     public static function getSelectData($parent_id = 0)
    {
        $data = [];
        if($parent_id == -1){
            $data = self::getAllDatas();
        }elseif(is_numeric($parent_id)){
            $data = self::getDatas($parent_id);
        }
        $result = ["" => "请选择"] + $data;
        return $result;
    }
    
    
    public static function getDatas($parent_id = 0)
    {
        $item = Department::find()->where(['parent_id' => $parent_id, 'status' => 1])->all();
        return ArrayHelper::map($item, "id", "name");
    }
    
     public static function getAllDatas()
    {
        $item = Department::find()->where(['status' => 1])->all();
        return ArrayHelper::map($item, "id", "name");
    }

    public static function getNameById($id) 
    {
        $model = self::findOne($id);
        return isset($model->name) ?  $model->name: '无';
    }
}
