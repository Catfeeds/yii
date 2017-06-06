<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\BaseActiveRecord;
use yii\db\Expression;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use libs\Utils;

use common\models\DepartmentBalance;
use common\models\DepartmentBalanceLog;
use libs\common\Flow;
use common\models\BusinessAll;
/**
 * This is the model class for table "AbnormalBalance".
 *
 * @property integer $id
 * @property string $name
 * @property integer $department_id
 * @property integer $income_department_id
 * @property double $current_balance
 * @property integer $mod
 * @property string $content
 * @property integer $status
 * @property string $create_time
 * @property integer $create_admin_id
 * @property integer $verify_admin_id
 * @property string $verify_time
 * @property integer $approval_admin_id
 * @property string $approval_time
 * @property integer $operation_admin_id
 * @property string $operation_time
 * @property integer $config_id
 * @property string $failCause
 * @property integer $timing_type
 * @property string $sn
 */
class AbnormalBalance extends namespace\base\AbnormalBalance
{    
    public $sum;
    public $time;
    public $year;
    public $income;
    public $expen;
    public $other;
    
    /**
     * 变动类型 -- 业务转账
     */
    const MOD_BUSINESS = 1;
    /**
     * 变动类型 -- 收取扣项
     */
    const MOD_BUCKLE = 2;
    /**
     * 变动类型 -- 上缴收入
     */
    const MOD_PAID = 3;
    
    private static $_modAll = [
        self::MOD_BUSINESS => '业务转账',
        self::MOD_BUCKLE => '收取扣项',
        self::MOD_PAID => '上缴收入',
    ];
    
     /**
     * 参数规则
     */
    public function rules()
    {
        $rules = parent::rules();
        $childRules =  [
            [['name', 'mod', 'current_balance', 'config_id', 'content'], 'required', 'message' => '{attribute}不能为空'],
            [['name'] , 'checkname' , 'skipOnEmpty' => false],
        ];
        return ArrayHelper::merge($childRules, $rules);
    }
    
    /**
     * 验证参数不能有空格和特殊字符 
     */
    public function checkname($attribute , $params)
    {
        if(preg_match('/[^0-9a-zA-Z一-龥]/u',$this->$attribute)){
            $this->addError($attribute , '表单名不能有空格和特殊字符');
        }
    }

    /**
     * 获取变动类型名称
     */
    public function showMod() {
        return isset(self::$_modAll[$this->mod]) ? self::$_modAll[$this->mod] : "未知" . $this->mod;
    }
    
    /**
     * 获取其他收入的变动类型
     */
    public static function findOtherIncomeMod() {
        return [self::MOD_BUSINESS, self::MOD_BUCKLE];
    }
    /**
     * 获取其他支出的变动类型
     */
    public static function findOtherExpenMod() {
        return [self::MOD_BUSINESS, self::MOD_PAID];
    }
    
    /**
     * 获取变动类型列表
     */
    public static function getModSelectData() {
        return self::$_modAll;
    }
    
    /**
     * 获取业务收支变动类型所需的部门
     * @param type $mod 务收支变动类型
     * @return array ["state" => 0/1 无效/有效, "departmentList" => ["expen" => 0/1/2 无/所属部门/全部部门,"income" => 0/1/2 无/所属部门/全部部门]]
     */
    public static function checkModDepartment($mod) {
        if(!in_array($mod, array_keys(self::$_modAll))) {
            return ["state" => 0, "message" => "业务收支变动类型错误"];
        }
        if(in_array($mod, [self::MOD_BUSINESS])) {
            return ["state" => 1, "departmentList" => ["expen" => 2, "income" => 1]];
        }
        if(in_array($mod, [self::MOD_PAID])) {
            return ["state" => 1, "departmentList" => ["expen" => 1, "income" => 2]];
        }
        if(in_array($mod, [self::MOD_BUCKLE])) {
            return ["state" => 1, "departmentList" => ["expen" => 0, "income" => 1]];
        }
        return ["state" => 1, "departmentList" => ["expen" => 0, "income" => 0]];
    }

    /**
     * 添加新的业务收支资金流水
     * @param type $post 表单提交数据
     * @return type
     */
    public function addAbnormalBalance($post) {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $this->attributes = $post["AbnormalBalance"];
            $checkMod = self::checkModDepartment($this->mod);
            if(!$checkMod["state"]) {
                $transaction->rollBack();
                return $checkMod;
            }
            if($checkMod["departmentList"]["expen"] > 0 && !$this->department_id) {
                $transaction->rollBack();
                return array("state" => 0, "message" => "请选择支出部门");
            }
            if($checkMod["departmentList"]["income"] > 0 && !$this->income_department_id) {
                $transaction->rollBack();
                return array("state" => 0, "message" => "请选择收入部门");
            }
            if($this->department_id && $this->income_department_id && $this->department_id == $this->income_department_id){
                $transaction->rollBack();
                return array("state" => 0, "message" => "业务收支的收入部门不能等于支出部门");
            }
            if($this->current_balance <= 0) {
                $transaction->rollBack();
                return array("state" => 0, "message" => "业务收支流水总金额不能小于等于零");
            }
            if($this->current_balance > 99999999) {
                $transaction->rollBack();
                return array("state" => 0, "message" => "业务收支流水总金额不能超过限制金额一亿");
            }
            $this->status = Flow::STATUS_APPLY_VERIFY;
            $this->sn = Utils::generateSn(Flow::TYPE_ABNORMAL_FUND);
            $this->create_admin_id = Yii::$app->user->getId();
            $this->create_time = date("Y-m-d H:i:s");
            $this->verify_admin_id = 0;
            $this->approval_admin_id = 0;
            $this->operation_admin_id = 0;
            $this->config_id = 0;
            if(!$this->save()) {
                $transaction->rollBack();
                return ["state" => 0, "message" => $this->getFirstErrors()];
            }
            $date = date("m", strtotime($this->create_time));
            $areaId = 0;
            $result = Flow::confirmFollowAdminId(Flow::TYPE_ABNORMAL_FUND, $this, $this->current_balance, $date, $areaId, [], []);
            if(!$result["state"]) {
                $transaction->rollBack();
                return $result;
            }
            $businessModel = new BusinessAll();
            $business = $businessModel->addBusiness($this, Flow::TYPE_ABNORMAL_FUND);
            if(!$business["state"]) {
                $transaction->rollBack();
                return ["error" => 0, "message" => $business["message"]];
            }
            if($this->status == Flow::STATUS_FINISH){
                $result = $this->Finish();
                if(!$result["state"]) {
                    $transaction->rollBack();
                    return $result;
                }
            }
            AdminLog::addLog("departblan_add", "业务收支流水申请成功：".$this->id);
            $transaction->commit();
            return ["state" => 1, "message" => "操作成功"];
        } catch (Exception $exc) {
            $transaction->rollBack();
            return ["state" => 0, "message" => $exc->getTraceAsString()];
        }
    }
    
    /**
     * 完成方法
     */
    public function Finish() {
        $checkMod = self::checkModDepartment($this->mod);
        if(!$checkMod["state"]) {
            return $checkMod;
        }
        if($checkMod["departmentList"]["expen"] > 0) {
            $isCheck = CheckFlow::amountIsCheckFlow($this->department_id);
            if($isCheck) {
                return array("state" => 0, "message" => "部门处于资金盘点中，无法执行业务收支流水");
            }
            $balanceLog = new DepartmentBalanceLog();
            $result = $balanceLog->addDepartmentBalanceLog($this->department_id, $this->id, DepartmentBalanceLog::BUSINESS_TYPE_ABNORMAL_FUND, DepartmentBalanceLog::MOD_OUT, $this->current_balance, '业务收支流水');
            if(!$result["state"]) {
                return $result;
            }
        }
        if($checkMod["departmentList"]["income"] > 0 && $this->income_department_id) {
            $isCheck = CheckFlow::amountIsCheckFlow($this->income_department_id);
            if($isCheck) {
                return array("state" => 0, "message" => "部门处于资金盘点中，无法执行业务收支流水");
            }
            $balanceLog = new DepartmentBalanceLog();
            $result = $balanceLog->addDepartmentBalanceLog($this->income_department_id, $this->id, DepartmentBalanceLog::BUSINESS_TYPE_ABNORMAL_FUND, DepartmentBalanceLog::MOD_IN, $this->current_balance, '业务收支流水');
            if(!$result["state"]) {
                return $result;
            }
        }
        return ['state' => 1];
    }
}
