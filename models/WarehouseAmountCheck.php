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
use common\models\DepartmentBalanceLog;
use common\models\AdminLog;
use libs\common\Flow;
/**
 * This is the model class for table "WarehouseAmountCheck".
 *
 * @property integer $id
 * @property integer $check_planning_id
 * @property integer $check_department_id
 * @property string $name
 * @property string $sn
 * @property double $total_amount
 * @property double $total_purchase_amount
 * @property integer $department_id
 * @property integer $warehouse_id
 * @property integer $create_admin_id
 * @property integer $verify_admin_id
 * @property string $verify_time
 * @property integer $approval_admin_id
 * @property string $approval_time
 * @property integer $operation_admin_id
 * @property string $operation_time
 * @property integer $status
 * @property string $create_time
 * @property integer $config_id
 * @property string $failCause
 * @property string $remark
 */
class WarehouseAmountCheck extends namespace\base\WarehouseAmountCheck
{
  
    /**
     * 完成操作
     */
    public function Finish()
    {
        $deblanModel = DepartmentBalance::findOne($this->department_id);
        if(!$deblanModel) {
            $deblanModel = new DepartmentBalance();
            $deblanModel->department_id = $this->department_id;
            $deblanModel->balance = 0;
            $deblanModel->income_amount = 0;
            $deblanModel->expenses_amount = 0;
            if(!$deblanModel->save()) {
                return ["state" => 0, "message" => $deblanModel->getFirstErrors()];
            }
        }
        $deblanModel->balance = $deblanModel->balance + ($this->total_purchase_amount - $this->total_amount);
        if(!$deblanModel->save()) {
            return ["state" => 0, "message" => $deblanModel->getFirstErrors()];
        }
        $balanceLog = new DepartmentBalanceLog();
        $result = $balanceLog->addDepartmentBalanceLog($this->department_id, $this->id, DepartmentBalanceLog::BUSINESS_TYPE_AMOUNT_CHECK, DepartmentBalanceLog::MOD_CHECK, $deblanModel->balance, '资金盘点');
        if(!$result["state"]) {
            return $result;
        }
        AdminLog::addLog("wacheck_finish", "资金盘点成功完成：".$this->id);
        return ["state" => 1];  
    }
}
