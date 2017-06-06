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
 * @property double $balance
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
 * @prpperty integer $timing_type
 */
class AbnormalBalance extends namespace\base\AbnormalBalance
{    
    /**
     * 变动类型 -- 进账
     */
    const MOD_IN = 1;
    
    /**
     * 变动类型 -- 出款
     */
    const MOD_OUT = 2;
    public $sum;
    public $time;
    public $year;
    
    private static $_modAll = [
        self::MOD_IN => '进账',
        self::MOD_OUT => '出款',
    ];
    
    /**
     * 获取变动类型名称
     */
    public function showMod() {
        return isset(self::$_modAll[$this->mod]) ? self::$_modAll[$this->mod] : "未知" . $this->mod;
    }
    
    /**
     * 获取变动类型列表
     */
    public static function getModSelectData() {
        return self::$_modAll;
    }
    
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
    
    public function rules()
    {
        $rules = parent::rules();
        $childRules =  [
            [['name'] , 'checkname' , 'skipOnEmpty' => false],
        ];
        return ArrayHelper::merge($childRules, $rules);
    }
    
    public function checkname($attribute , $params)
    {
        if(preg_match('/[^0-9a-zA-Z一-龥]/u',$this->$attribute)){
            $this->addError($attribute , '表单名不能有空格和特殊字符');
        }
    }

    /**
     * 添加新的非常态资金流水
     * @param type $post 表单提交数据
     * @return type
     */
    public function addAbnormalBalance($post) {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $this->attributes = $post["AbnormalBalance"];
            $item = DepartmentBalance::findOne(["department_id" => $this->department_id]);
            $this->current_balance = $item ? $item->balance : 0;
            $this->status = Flow::STATUS_APPLY_VERIFY;
            $this->sn = Utils::generateSn(Flow::TYPE_ABNORMAL_FUND);
            $this->create_admin_id = Yii::$app->user->getId();
            $this->create_time = date("Y-m-d H:i:s");
            $this->verify_admin_id = 0;
            $this->verify_time = date("Y-m-d H:i:s");
            $this->approval_admin_id = 0;
            $this->approval_time = date("Y-m-d H:i:s");
            $this->operation_admin_id = 0;
            $this->operation_time = date("Y-m-d H:i:s");
            $this->config_id = 0;
            if(!$this->save()) {
                $transaction->rollBack();
                return ["state" => 0, "message" => $this->getFirstErrors()];
            }
            $date = date("m", strtotime($this->create_time));
            $areaId = 0;
            $result = Flow::confirmFollowAdminId(Flow::TYPE_ABNORMAL_FUND, $this, $this->balance, $date, $areaId, [], []);
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
            AdminLog::addLog("departblan_add", "非常态资金流水申请成功：".$this->id);
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
        $balanceLog = new DepartmentBalanceLog();
        $result = $balanceLog->addDepartmentBalanceLog($this->department_id, $this->id, DepartmentBalanceLog::BUSINESS_TYPE_ABNORMAL_FUND, $this->mod, $this->balance, '非常态资金变动');
        if(!$result["state"]) {
            return $result;
        }
        return ['state' => 1];
    }
}
