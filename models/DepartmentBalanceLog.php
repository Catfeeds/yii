<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\BaseActiveRecord;
use yii\db\Expression;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

use common\models\DepartmentBalance;

use libs\common\Flow;

/**
 * This is the model class for table "DepartmentBalanceLog".
 *
 * @property integer $id
 * @property integer $department_id
 * @property integer $business_id
 * @property string $business_type
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
 */
class DepartmentBalanceLog extends namespace\base\DepartmentBalanceLog
{
    /**
     * 业务类型 - 销售
     */
    const BUSINESS_TYPE_SALE = 1;
    /**
     * 业务类型 -- 耗损
     */
    const BUSINESS_TYPE_WASTAGE = 2;
    /**
     * 计划下单
     */
    const BUSINESS_TYPE_ORDER = 3;
    /**
     * 退货
     */
    const BUSINESS_TYPE_MATERIAL_RETURN = 4;
    /**
     * 非常态资金流水
     */
    const BUSINESS_TYPE_ABNORMAL_FUND = 5;
    /**
     * 资金盘点
     */
    const BUSINESS_TYPE_AMOUNT_CHECK = 6;
    
    /**
     * 变动类型 -- 进账
     */
    const MOD_IN = 1;
    
    /**
     * 变动类型 -- 出款
     */
    const MOD_OUT = 2;
    /**
     * 变动类型 -- 盘点
     */
    const MOD_CHECK = 3;
    
    private static $_businessType = [
        self::BUSINESS_TYPE_SALE => [
            'name' => '销售',
            'cName' => 'wsale',
        ],
        self::BUSINESS_TYPE_WASTAGE => [
            'name' => '耗损',
            'cName' => 'wwastage',
        ],
        self::BUSINESS_TYPE_ORDER => [
            'name' => '计划下单',
            'cName' => 'oprocurement',
        ],
        self::BUSINESS_TYPE_MATERIAL_RETURN => [
            'name' => '退货',
            'cName' => 'wmaterial',
        ],
        self::BUSINESS_TYPE_ABNORMAL_FUND => [
            'name' => '非常态资金流水',
            'cName' => 'abnormalbalance',
        ],
        self::BUSINESS_TYPE_AMOUNT_CHECK => [
            'name' => '资金盘点',
            'cName' => 'wacheck',
        ],
    ];
    
    private static $_modAll = [
        self::MOD_IN => '进账',
        self::MOD_OUT => '出款',
        self::MOD_CHECK => '盘点',
    ];

    /**
     * 获取业务类型名称
     */
    public function showBusinessTypeName() {
        return isset(self::$_businessType[$this->business_type]) ? self::$_businessType[$this->business_type]['name'] : "未知" . $this->business_type;
    }
    
    /**
     * 获取业务类型的控制器名称
     */
    public function showBusinessTypeCName() {
        return isset(self::$_businessType[$this->business_type]) ? self::$_businessType[$this->business_type]['cName'] : "未知" . $this->business_type;
    }
    
    /**
     * 获取业务类型列表
     */
    public static function getBusinessTypeSelectData() {
        return ArrayHelper::getColumn(self::$_businessType, 'name');
    }
    
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
    /**
     *  添加资金流水记录
     * @param type $departmentId 部门ID
     * @param type $businessId 业务ID
     * @param type $businessType 业务类型
     * @param type $mod 变动类型
     * @param type $modMoney 变动金额
     * @param type $content 内容
     * @return type
     */
    public function addDepartmentBalanceLog($departmentId, $businessId, $businessType, $mod, $modMoney, $content) {
        $item = DepartmentBalance::findOne(["department_id" => $departmentId]);
        if(!$item) {
        	$dmodel = new DepartmentBalance();
        	$dmodel->department_id = $departmentId;
        	$dmodel->balance = '0';
        	$dmodel->income_amount = '0';
        	$dmodel->expenses_amount = '0';
        	$dmodel->save();
        	
        	$item = $dmodel;
            //return ["state" => 0, "message" => "部门未知" . $departmentId];
        }
        $this->department_id = $departmentId;
        $this->business_id = $businessId;
        $this->business_type = $businessType;
        $this->mod = $mod;
        $this->name = $this->showBusinessTypeName().  $this->showMod() ."-" . date("Ymd");
        $this->balance = $modMoney;
        $this->current_balance = $item->balance;
        $this->content = $content;
        $this->status = Flow::STATUS_FINISH;
        $this->create_admin_id = Yii::$app->user->getId();
        $this->create_time = date("Y-m-d H:i:s");
        $this->verify_admin_id = 0;
        $this->verify_time = date("Y-m-d H:i:s");
        $this->approval_admin_id = 0;
        $this->approval_time = date("Y-m-d H:i:s");
        $this->operation_admin_id = Yii::$app->user->getId();
        $this->operation_time = date("Y-m-d H:i:s");
        $this->config_id = 0;
        if(!$this->save()) {
            return ["state" => 0, "message" => $this->getFirstErrors()];
        }
        $result = $this->Finish();
        if(!$result["state"]) {
            return $result;
        }
        $date = date("m", strtotime($this->create_time));
        $areaId = 0;
//        $result = Flow::confirmFollowAdminId(Flow::TYPE_ORDER_FINANCE, $this, $modMoney, $date, $areaId, [], []);
//        if(!$result["state"]) {
//            return $result;
//        }
        AdminLog::addLog("departblanlog_add", "申请成功：".$this->id);
        return ["state" => 1];
    }
    
    /**
     * 完成方法
     */
    public function Finish() {
        $item = DepartmentBalance::findOne(["department_id" => $this->department_id]);
        if(!$item) {
            return ["state" => 0, "message" => "部门未知" . $this->department_id];
        }
        if($this->mod == self::MOD_IN) {
            $item->income_amount = $item->income_amount + $this->balance;
            $item->balance = $item->balance + $this->balance;
        } else {
            $item->expenses_amount = $item->expenses_amount + $this->balance;
            $item->balance = $item->balance - $this->balance;
        }
        if(!$item->save()) {
            return ["state" => 0, "message" => $item->getFirstErrors()];
        }
        return ['state' => 1];
    }
}
