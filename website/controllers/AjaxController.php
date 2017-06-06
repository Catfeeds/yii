<?php
namespace app_web\controllers;

use Yii;
use yii\web\Controller;
use yii\helpers\Json;
use common\models\Warehouse;
use yii\helpers\ArrayHelper;
use common\models\FlowConfigStep;
use common\models\Role;
use libs\common\Flow;
use common\models\CheckPlanningFlow;
use common\models\CheckPlanningFlowData;
use common\models\BusinessAll;
use common\models\Config;
use common\models\AbnormalBalance;

class AjaxController extends Controller {
    
    /**
     * 获取部门下所有的仓库
     */
    public function actionGetwarehouselist() {
        $id = \Yii::$app->request->get("id");
        $warehouseList = Warehouse::findAll(["department_id" => $id]);
        $return = ArrayHelper::map($warehouseList, "id", "name");
        return Json::encode($return);
    }
    
    /**
     * 获取流程的状态步骤 
     */
    public function actionAjaxflowlogic() {
        $flowType = Yii::$app->request->get("flowType");
        $item = FlowConfigStep::findOne(["config_sn" => $flowType]);
        $return["form"] = 1;
        if(!$item) {
            $return["form"] = 0;
            $return["result"] = Flow::showFlowConfig($flowType);
        } else {
            $return["result"] = [
                    "create" => $item["create_step"],
                    "verify" => $item["verify_step"],
                    "approval" => $item["approval_step"],
                    "operation" => $item["operation_step"],
            ];
        }
        $return["condition"] = Flow::getTypeCheckCondition($flowType);
        return Json::encode($return);
    }
    
    /**
     * 根据部门获取下属角色
     */
    public function actionAjaxdepartmentrole() {
        $dId = Yii::$app->request->get("dId");
        $type = Yii::$app->request->get("type");
        $query = Role::find();
        $query->andWhere(["status" => 1]);
        $query->andWhere(["department_id" => $dId]);
        if($type != "create") {
            $query->andWhere(["is_sole" => 1]);
        }
        $data = $query->all();
        $return = ArrayHelper::map($data, "id", "name");
        unset($return["1"]);
        return Json::encode($return);
    }
    
    /**
     * 确定流程是否执行完
     */
    public function actionCheckflowsuccess() {
        $dataId = Yii::$app->request->get("dataId");
        $model = CheckPlanningFlow::findOne($dataId);
        if(!$model) {
            $return = ["state" => 0, "message" => "数据异常"];
            return Json::encode($return);
        }
        $planningData = CheckPlanningFlowData::findAll(["check_planning_flow_id" => $dataId]);
        $checkDataIds = ArrayHelper::getColumn($planningData, 'data_id');
        $typeAll = [
            Flow::TYPE_BUYING, 
            Flow::TYPE_BACK, 
            Flow::TYPE_CHECKOUT, 
            Flow::TYPE_TRANSFEFDEP, 
            Flow::TYPE_TRANSFEF, 
            Flow::TYPE_MATERIALRETURN, 
            Flow::TYPE_WASTAGE, 
            Flow::TYPE_CHECK_PLANNING_PROOF,
            Flow::TYPE_CHECK_DEPARTMENT_PROOF,
            Flow::TYPE_CHECK_WAREHOUSE_PROOF,
        ];
        $businessQuery = BusinessAll::find();
        $businessQuery->andWhere(['in', 'status', [Flow::STATUS_APPLY_VERIFY, Flow::STATUS_APPLY_APPROVAL, Flow::STATUS_APPLY_FINISH]]);
        $businessQuery->andWhere(['in', 'business_type', $typeAll]);
        if($model->type == CheckPlanningFlow::TYPE_PLANNING) {
            $businessQuery->andWhere(["department_id" => $checkDataIds]);
        } else {
            $businessQuery->andWhere(["warehouse_id" => $checkDataIds]);
        }
        $businessAll = $businessQuery->all();
        if($businessAll) {
            $return = ["state" => 0, "message" => "无法刷新数据，有物料库存流程执行中"];
            return Json::encode($return);
        }
        if($model->is_check_amount){
            $amountTypeAll = [
                Flow::TYPE_ORDER_FINANCE,
                Flow::TYPE_ABNORMAL_FUND,
                Flow::TYPE_ORDER_MATERIAL,
                Flow::TYPE_SALE,
            ];
            $businessAmountQuery = BusinessAll::find();
            $businessAmountQuery->andWhere(['not in', 'status', [Flow::STATUS_FINISH, Flow::STATUS_VERIFY_REJECT, Flow::STATUS_APPROVAL_REJECT, Flow::STATUS_FINISH_REJECT, Flow::STATUS_UNION_REJECT, Flow::STATUS_HANG_UP]]);
            $businessAmountQuery->andWhere(['in', 'business_type', $amountTypeAll]);
            if($model->type == CheckPlanningFlow::TYPE_PLANNING) {
                $businessAmountQuery->andWhere(["department_id" => $checkDataIds]);
            } else {
                $businessAmountQuery->andWhere(["warehouse_id" => $checkDataIds]);
            }
            $businessAmountAll = $businessAmountQuery->all();
            if($businessAmountAll) {
                $return = ["state" => 0, "message" => "无法刷新数据，有资金流程执行中"];
                return Json::encode($return);
            }
        }
        $return = ["state" => 1, "message" => "数据已刷新"];
        return Json::encode($return);
    }
    
    /**
     * 验证授权是否成功
     */
    public function actionCheckauth() {
        $authPassword = Yii::$app->request->get("authPassword");
        $authKeyRoute = Yii::$app->request->get("authKeyRoute");
        $authKeyParams = Yii::$app->request->get("authKeyParams");
        if(!$authPassword) {
            return Json::encode(["error" => 1, "message" => "授权密码不能为空"]);
        }
        $model = Config::findOne(["set_name" => "flow_business_password"]);
        if(!$model) {
            return Json::encode(["error" => 1, "message" => "网络异常"]);
        }
        if (md5($authPassword) != $model->set_value) {
            return Json::encode(["error" => 1, "message" => "授权密码错误"]);
        }
        Yii::$app->session->set("authTime", time());
        Yii::$app->session->set("authKeyRoute", $authKeyRoute);
        Yii::$app->session->set("authKeyParams", $authKeyParams);
        return Json::encode(["error" => 0, "message" => "授权成功"]);
    }
    
    /**
     * 获取业务收支类型所需的部门
     */
    public function actionAjaxabnormalmod() {
        $mod = Yii::$app->request->get("mod");
        $return = AbnormalBalance::checkModDepartment($mod);
        return Json::encode($return);
    }
}
