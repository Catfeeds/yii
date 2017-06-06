<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\BaseActiveRecord;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use common\models\FlowCondition;
use libs\common\Flow;

/**
 * This is the model class for table "FlowConfig".
 *
 * @property integer $id
 * @property string $name
 * @property string $type
 * @property integer $operation_role_id
 * @property string $operation_name
 * @property integer $operation_department_id
 * @property integer $verify_role_id
 * @property string $verify_name
 * @property integer $verify_department_id
 * @property integer $approval_role_id
 * @property string $approval_name
 * @property integer $approval_department_id
 * @property integer $create_role_id
 * @property string $create_name
 * @property integer $create_department_id
 * @property integer $status
 * @property integer $create_admin_id
 */
class FlowConfig extends namespace\base\FlowConfig {

    //无效
    const STATUS_NO = 0;
    //有效
    const STATUS_YES = 1;
    //删除
    const STATUS_DEL = 99;
    
    public static $_status = [
    	self::STATUS_YES => '有效',
        self::STATUS_NO => '无效',
        self::STATUS_DEL => '删除',
    ];
    
    /**
     * 字段规则
     */
    public function rules()
    {
        $rules = parent::rules();
        $childRules = [
            [['name', 'operation_name', 'verify_name', 'approval_name', 'create_name'] , 'checkname' , 'skipOnEmpty' => false],
            [['name', 'operation_name', 'verify_name', 'approval_name', 'create_name'], 'string', 'max' => 12, 'tooLong'=>'{attribute}的长度不能超过12个字符'],
        ];
        return array_merge($rules, $childRules);
    }
    
    /**
     * 验证参数不能有空格和特殊字符 
     */
    public function checkname($attribute , $params)
    {
        if(preg_match('/[^0-9a-zA-Z一-龥]/u',$this->$attribute)){
            $this->addError($attribute , ($attribute == "name" ? "流程名称" : ($attribute == "operation_name" ? "执行名称" : ($attribute == "verify_name" ? "审核名称" : ($attribute == "approval_name" ? "批准名称" : "创建名称")))).'不能有空格和特殊字符');
        }
    }
    
    /**
     * 展示状态
     */
    public function showStatus() {
        return isset(self::$_status[$this->status]) ? self::$_status[$this->status] : "未知" . $this->status;
    }
    
    /**
     * 获取状态列表
     */
    public static function getStatusSelectData() {
        return self::$_status;
    }

    /**
     * 通过流程ID获取流程名称
     * @param type $id 流程ID
     * @return type
     */
    public static function getNameById($id) {
        $model = FlowConfig::findOne($id);
        return isset($model->name) ? $model->name : '无';
    }

    /**
     * 获取符合状态的所有流程列表
     * @param type $status 流程状态
     * @return type
     */
    public static function getAllSelectData($status = "") {
        if (is_numeric($status)) {
            $info = self::findByCondition(["status" => $status])->all();
        } else {
            $info = self::find()->all();
        }
        return ArrayHelper::map($info, "id", "name");
    }
    
    /**
     * 添加或修改业务流程
     * @param type $id 业务流程ID
     * @param type $post 表单提交数据
     * @return type
     */
    public function addEdit($id, $post) {
        $transaction = Yii::$app->db->beginTransaction();
        try{
            $model = new FlowConfig;
            if($id) {
                $model = FlowConfig::findOne($id);
            } 
            $model->load($post);
            if(is_numeric($model->name)) {
                $transaction->rollBack();
                return ["state" => 0, "message" => "流程名称必须为字符串"];
            }
            if(!(($model->create_role_id && $model->create_name && $model->create_department_id) || (!$model->create_role_id && !$model->create_name && !$model->create_department_id))){
                $transaction->rollBack();
                return ["state" => 0, "message" => "创建组合错误，要不都有值，要不都为空"];
            }
            if(!(($model->verify_role_id && $model->verify_name && $model->verify_department_id) || (!$model->verify_role_id && !$model->verify_name && !$model->verify_department_id))) {
                $transaction->rollBack();
                return ["state" => 0, "message" => "审核组合错误，要不都有值，要不都为空"];
            }
            if(!(($model->approval_role_id && $model->approval_name && $model->approval_department_id) || (!$model->approval_role_id && !$model->approval_name && !$model->approval_department_id))) {
                $transaction->rollBack();
                return ["state" => 0, "message" => "批准组合错误，要不都有值，要不都为空"];
            }
            if(!(($model->operation_role_id && $model->operation_name && $model->operation_department_id) || (!$model->operation_role_id && !$model->operation_name && !$model->operation_department_id))) {
                $transaction->rollBack();
                return ["state" => 0, "message" => "执行组合错误，要不都有值，要不都为空"];
            }
            if(!in_array($model->type, Flow::getNoCreateType()) && !($model->create_role_id && $model->create_name && $model->create_department_id)) {
                $transaction->rollBack();
                return ["state" => 0, "message" => "该流程类型必须有创建操作"];
            }
            if(($model->approval_role_id && $model->approval_name && $model->approval_department_id) && (!$model->verify_role_id || !$model->verify_name || !$model->verify_department_id)) {
                $transaction->rollBack();
                return ["state" => 0, "message" => "流程操作规则有批准就必须有审核"];
            }
            if(in_array($model->type, Flow::getOperationType()) && !($model->operation_role_id && $model->operation_name && $model->operation_department_id)) {
                $transaction->rollBack();
                return ["state" => 0, "message" => "该流程类型必须有执行操作"];
            }
            $model->create_admin_id = \Yii::$app->user->getId();
            if(!$model->save()){
                $transaction->rollBack();
                return ["state" => 0, "message" => $model->getFirstErrors()];
            }
            FlowCondition::deleteAll(["config_id" => $model->id]);
            foreach ($post["info"] as $type => $infoVal) {
                if(in_array($type, array(FlowCondition::TYPE_PRICE, FlowCondition::TYPE_TIME))) {
                    if(!$infoVal["lower_limit"] || !$infoVal["upper_limit"]) {
                        $transaction->rollBack();
                        return ["state" => 0, "message" => ($type == FlowCondition::TYPE_PRICE ? "价格" : "时间")."范围的上下限值不能为空或零"];
                    }
                    if($infoVal["lower_limit"] > $infoVal["upper_limit"]) {
                        $transaction->rollBack();
                        return ["state" => 0, "message" => ($type == FlowCondition::TYPE_PRICE ? "价格" : "时间")."范围的下限值不能大于上限"];
                    }
                }
//                if($type == FlowCondition::TYPE_TIME) {
//                    if(strtotime($infoVal["lower_limit"]) == strtotime($infoVal["upper_limit"])) {
//                        $transaction->rollBack();
//                        return ["state" => 0, "message" => "时间范围的下限值不能等于上限"];
//                    }
//                }
                $conditionModel = new FlowCondition();
                $conditionModel->config_id = $model->id;
                $conditionModel->type = $type;
                 $conditionModel->name = '流程条件';
                   $conditionModel->status = 1;
                $conditionModel->attributes = $infoVal;
                if(!$conditionModel->save()) {
                    $transaction->rollBack();
                    return ["state" => 0, "message" => $conditionModel->getFirstErrors()];
                }
            }
            $transaction->commit();
            return ["state" => 1];
        } catch (Exception $ex) {
            $transaction->rollBack();
            return ["state" => 0, "message" => $ex->getTraceAsString()];
        }
    }

    /**
     * 验证符合流程的条件
     * @param int $type  流程类型
     * @param float $totalMoney 总金额
     * @param int $date 当前时间戳
     * @param int $department 部门
     * @param array $supplier  供应商
     * @param array $productType 物品类型
     * @return array
     */
    public static function checkConfigCondition($type, $totalMoney, $date, $department, $supplier, $productType) {
        $configAll = self::findAll(["type" => $type, "status" => FlowConfig::STATUS_YES]);
        $result = [];
        $department = array_flip(array_flip(is_array($department) ? $department : [$department]));
        $supplier = array_flip(array_flip(is_array($supplier) ? $supplier : [$supplier]));
        $productType = array_flip(array_flip(is_array($productType) ? $productType : [$productType]));
        foreach ($configAll as $val) {
            $conditionAll = FlowCondition::findAll(["config_id" => $val["id"], "status" => FlowCondition::STATUS_YES]);
            if (!$conditionAll) {
                continue;
            }
            $isCheck = true;
            foreach ($conditionAll as $conditionVal) {
                if($totalMoney && $conditionVal["type"] == FlowCondition::TYPE_PRICE && !($totalMoney >= $conditionVal["lower_limit"] && $totalMoney <= $conditionVal["upper_limit"])) {
                    $isCheck = false;
                    break;
                }
                if($conditionVal["type"] == FlowCondition::TYPE_TIME && !($date >= strtotime($conditionVal["lower_limit"]) && $date <= strtotime($conditionVal["upper_limit"] . " 23:59:59"))) {
                    $isCheck = false;
                    break;
                }
                if($department && $conditionVal["type"] == FlowCondition::TYPE_AREA && !self::checkIsConform($conditionVal["lower_limit"], $department)) {
                    $isCheck = false;
                    break;
                }
                if($supplier && $conditionVal["type"] == FlowCondition::TYPE_SUPPLIER && !self::checkIsConform($conditionVal["lower_limit"], $supplier)) {
                    $isCheck = false;
                    break;
                }
                if($productType && $conditionVal["type"] == FlowCondition::TYPE_CATEGORY && !self::checkIsConform($conditionVal["lower_limit"], $productType)) {
                    $isCheck = false;
                    break;
                }
            }
            if($isCheck) {
                $result[] = $val;
            }
        }
        return $result;
    }
    
    /**
     * 验证是否符合
     * @param array $checkVal 条件值
     * @param array $checkArr 订单值
     * @return boolean
     */
    private static function checkIsConform($checkVal, $checkArr) {
        if($checkVal == 0) {
            return true;
        }
        if(count($checkArr) > 1) {
            return false;
        }
        if(end($checkArr) == $checkVal) {
            return true;
        }
        return false;
    }
}
