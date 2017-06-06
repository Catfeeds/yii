<?php

namespace common\models;

use Yii;
use Exception;
use yii\helpers\ArrayHelper;
use libs\Utils;
use common\models\BusinessAll;
use common\models\AdminLog;
use common\models\CheckPlanningFlowData;

use libs\common\Flow;

/**
 * This is the model class for table "CheckPlanningFlow".
 *
 * @property integer $id
 * @property string $name
 * @property string $sn
 * @property integer $type
 * @property integer $supplier_id
 * @property integer $is_check_amount
 * @property string $check_time
 * @property string $end_time
 * @property string $product_name
 * @property integer $product_cate_id
 * @property integer $status
 * @property integer $config_id
 * @property integer $create_admin_id
 * @property string $create_time
 * @property integer $verify_admin_id
 * @property string $verify_time
 * @property integer $approval_admin_id
 * @property string $approval_time
 * @property integer $operation_admin_id
 * @property string $operation_time
 * @property string $failCause
 * @property string $remark
 * @property integer $department_id
 * @property integer $warehouse_id
 * @property integer $is_proof
 * @prpperty integer $timing_type
 */
class CheckPlanningFlow extends namespace\base\CheckPlanningFlow 
{
    /**
     * 盘点类型 -- 总盘点计划
     */
    CONST TYPE_PLANNING = 1;
    /**
     * 盘点类型 -- 部门盘点计划
     */
    CONST TYPE_DEPARTMENT = 2;
    /**
     * 盘点类型 -- 仓库盘点计划
     */
    CONST TYPE_WAREHOUSE = 3;
    
    public function rules()
    {
        $rules = parent::rules();
        $childRules =  [
            [['name', 'sn', 'product_name', 'remark'] , 'checkname' , 'skipOnEmpty' => false],
        ];
        return ArrayHelper::merge($childRules, $rules);
    }
    
    public function checkname($attribute , $params)
    {
        if(preg_match('/[^0-9a-zA-Z一-龥]/u',$this->$attribute)){
            $this->addError($attribute , ($attribute == "name" ? "表单名" : ($attribute == "sn" ? "表单号" : ($attribute == "product_name" ? "盘点商品名称" : "计划原因"))).'不能有空格和特殊字符');
        }
    }
    
    public function addOrUpdateCheckPlanning($post, $type = self::TYPE_PLANNING) {
        $transaction = \Yii::$app->db->beginTransaction();
        try{
            if(!isset($post["dataId"]) || !count($post["dataId"])) {
                throw new Exception("请选择要盘点的".($type == self::TYPE_PLANNING ? "部门" : "仓库"));
            }
            $flowType = 0;
            $message = "";
            $this->attributes = $post["CheckPlanningFlow"];
            switch ($type) {
                case self::TYPE_PLANNING:
                    $flowType = Flow::TYPE_CHECK_PLANNING;
                    $message = "总盘点计划申请成功";
                    break;
                case self::TYPE_DEPARTMENT:
                    $flowType = Flow::TYPE_CHECK_DEPARTMENT;
                    $message = "部门盘点计划申请成功";
                    break;
                case self::TYPE_WAREHOUSE:
                    $flowType = Flow::TYPE_CHECK_WAREHOUSE;
                    $message = "仓库盘点计划申请成功";
                    $warehouse = Warehouse::findOne($this->warehouse_id);
                    $this->department_id = $warehouse->department_id;
                    break;
                default :
                    throw new Exception("网络异常");
            }
            $this->sn = Utils::generateSn($flowType);
            if($this->check_time >= $this->end_time) {
                throw new Exception("盘点结束时间不能小于或等于盘点时间");
            }
            $this->is_check_amount = isset($post["isCheckAmount"]) ? 1 : 0;
            $this->type = $type;
            $this->status = Flow::STATUS_APPLY_VERIFY;
            $this->config_id = 0;
            $this->create_admin_id = \Yii::$app->user->getId();
            $this->create_time = date("Y-m-d H:i:s");
            $this->is_proof = 0;
            if(!$this->validate()) {
                $message = $this->getFirstErrors();
                throw new Exception(reset($message));
            }
            $this->save();
            foreach ($post["dataId"] as $dataId => $dataName) {
                $childModel = new CheckPlanningFlowData();
                $childModel->check_planning_flow_id = $this->id;
                $childModel->data_id = $dataId;
                $childModel->data_name = $dataName;
                if(!$childModel->validate()) {
                    $message = $childModel->getFirstErrors();
                    throw new Exception(reset($message));
                }
                $childModel->save();
            }
            $result = Flow::confirmFollowAdminId($flowType, $this, 0, time(), 0, [$this->supplier_id], [$this->product_cate_id]);
            if(!$result["state"]) {
                throw new Exception($result["message"]);
            }
            $businessModel = new BusinessAll();
            $business = $businessModel->addBusiness($this, $flowType);
            if(!$business["state"]) {
                $message = reset($business["message"]);
                throw new Exception($message);
            }
            AdminLog::addLog("wcheck_add", $message." ：".$this->id);
            $transaction->commit();
            return array("state" => 1, "checkId" => $this->id);
        } catch (Exception $ex) {
            $transaction->rollBack();
            return ["state" => 0, "message" => $ex->getMessage()];
        }
    }
}
