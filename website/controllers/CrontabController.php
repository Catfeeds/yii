<?php
namespace app_web\controllers;

use Yii;
use yii\web\Controller;
use yii\helpers\ArrayHelper;
use libs\BackMysql;
use Exception;

use common\models\BusinessAll;
use common\models\Config;
use common\models\Product;
use libs\common\Flow;
use common\models\BusinessRemind;

class CrontabController extends Controller {
    /**
     * 定时任务 -- 流程配置
     */
    public function actionBusinessbegin() {
        set_time_limit(0);
        $query = BusinessAll::find();
        $query->andWhere(['status' => [Flow::STATUS_APPLY_VERIFY, Flow::STATUS_APPLY_APPROVAL, Flow::STATUS_APPLY_FINISH]]);
        $businessAll = $query->all();
        $commonSetModel = Config::findOne(["set_name" => "commonSet"]);
        if(!$commonSetModel) {
            echo "没有配置设置数据";
            exit;
        }
        $flowTypeSet = Config::find()->andWhere(['like', 'set_name', "flowType"])->all();
        $flowTypeSet = ArrayHelper::map($flowTypeSet, "set_name", "set_value");
        foreach ($businessAll as $businessVal) {
            if(!isset($flowTypeSet["flowType_".$businessVal->business_type])) {
                continue;
            }
            $model = BusinessAll::findModelByBusinessIdAndType($businessVal->business_type, $businessVal->business_id);
            if(!$model) {
                continue;
            }
            $result = $this->checkIsExpire($model, $flowTypeSet["flowType_".$businessVal->business_type], $businessVal->business_type);
            if(!$result) {
                continue;
            }
            $file = Yii::getAlias('@log/' . "BusinessCrontab.txt");
            if($commonSetModel->set_value) {
                $passResult = $this->businessPass($model, $businessVal->business_type);
                if(!$passResult["state"]) {
                    file_put_contents($file, Flow::showType($businessVal->business_type)."(ID:{$model->id}) --> Pass -->".date("Y-m-d")." :  ".  reset($passResult["message"]) ."\n\r", FILE_APPEND);
                }
                continue;
            }
            $rejectResult = $this->timingReject($model, $businessVal->business_type);
            if(!$rejectResult["state"]) {
                file_put_contents($file, Flow::showType($businessVal->business_type)."(ID:{$model->id}) --> Reject -->".date("Y-m-d")." :  ".  reset($rejectResult["message"]) ."\n\r", FILE_APPEND);
            }
        }
        echo date("Y-m-s")."-->成功";
    }
    
    /**
     * 验证流程是否过期
     * @param type $model 流程模块
     * @param type $dayNum 过期时间
     * @return boolean true 过期 false 没过期
     */
    private function checkIsExpire($model, $dayNum, $ftype) {
        $status = $ftype == Flow::TYPE_ADDPRODUCT ? $model->modify_status : $model->status;
        if($status == Flow::STATUS_APPLY_VERIFY) {
            if(time() - strtotime($model->create_time) > 86400 * $dayNum) {
                return true;
            }
            return false;
        }
        if($status == Flow::STATUS_APPLY_APPROVAL) {
            $date = $model->verify_admin_id ? $model->verify_time : $model->create_time;
            if(time() - strtotime($date) > 86400 * $dayNum) {
                return true;
            }
            return false;
        }
        if($status == Flow::STATUS_APPLY_FINISH) {
            $date = $model->approval_admin_id ? $model->approval_time : ($model->verify_admin_id ? $model->verify_time : $model->create_time);
            if(time() - strtotime($date) > 86400 * $dayNum) {
                return true;
            }
            return false;
        }
        return false;
    }
    
    /**
     * 业务流程定时通过
     * @param type $model 流程模块
     * @param type $ftype 流程类型
     * @return type
     */
    private function businessPass($model, $ftype) {
        $status = $ftype == Flow::TYPE_ADDPRODUCT ? $model->modify_status : $model->status;
        BusinessRemind::disposeRemind($model->id, Flow::showTypeUrl($ftype), $status);
        if($status == Flow::STATUS_APPLY_VERIFY) {
            $model->timing_type = Flow::TIMING_TYPE_VERIFY;
            BusinessRemind::addRemind($model->id, Flow::showTypeUrl($ftype), $status, $model->verify_admin_id, $model->name.'定时审核通过需要您的确定');
        }
        if($status == Flow::STATUS_APPLY_APPROVAL) {
            $model->timing_type = Flow::TIMING_TYPE_APPROVAL;
            BusinessRemind::addRemind($model->id, Flow::showTypeUrl($ftype), $status, $model->approval_admin_id, $model->name.'定时批准通过需要您的确定');
        }
        if($status == Flow::STATUS_APPLY_FINISH) {
            $model->timing_type = Flow::TIMING_TYPE_OPERATION;
            BusinessRemind::addRemind($model->id, Flow::showTypeUrl($ftype), $status, $model->operation_admin_id, $model->name.'定时执行通过需要您的确定');
        }
        if(!$model->save()) {
            return ["state" => 0, "message" => $model->getFirstErrors()];
        }
        return ["state" => 1];
    }
    
    /**
     * 业务流程过期驳回
     * @param type $model 流程模块
     * @param type $ftype 流程类型
     * @return type
     */
    private function timingReject($model, $ftype) {
        $status = $ftype == Flow::TYPE_ADDPRODUCT ? $model->modify_status : $model->status;
        BusinessRemind::disposeRemind($model->id, Flow::showTypeUrl($ftype), $status);
        if($status == Flow::STATUS_APPLY_VERIFY) {
            $model->timing_type = Flow::TIMING_TYPE_VERIFY_REJECT;
            BusinessRemind::addRemind($model->id, Flow::showTypeUrl($ftype), $status, $model->verify_admin_id, $model->name.'过期审核驳回需要您的确定');
        }
        if($status == Flow::STATUS_APPLY_APPROVAL) {
            $model->timing_type = Flow::TIMING_TYPE_APPROVAL_REJECT;
            BusinessRemind::addRemind($model->id, Flow::showTypeUrl($ftype), $status, $model->approval_admin_id, $model->name.'过期批准驳回需要您的确定');
        }
        if($status == Flow::STATUS_APPLY_FINISH) {
            $model->timing_type = Flow::TIMING_TYPE_OPERATION_REJECT;
            BusinessRemind::addRemind($model->id, Flow::showTypeUrl($ftype), $status, $model->operation_admin_id, $model->name.'过期执行驳回需要您的确定');
        }
        if(!$model->save()) {
            return ["state" => 0, "message" => $model->getFirstErrors()];
        }
        return ["state" => 1];
    }
    
    /**
     * 定时任务 -- 备份数据库
     */
    public function actionExportdatabasebegin() {
        $dbBack = new BackMysql();
        $result = $dbBack->setDBName('wms');  
        if(!$result["state"]) {
            echo $result["message"];exit;
        }
//        $dbBack->incrementBackup();
        $backupResult = $dbBack->backup();
        if($backupResult["state"]){  
            echo  '数据库备份成功！!';   
        }else{
             echo $result["message"];
        }     
        exit;
    }
}