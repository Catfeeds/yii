<?php
namespace app_web\components;

use Yii;
use yii\web\Controller;
use yii\helpers\Url;
use yii\helpers\Json;
use libs\common\Flow;
use common\models\Admin;
use common\models\RoleJurisdiction;
use common\models\Config;
use common\models\Department;
use common\models\CheckPlanningFlow;
use common\models\CheckFlow;

class CController extends Controller {
    
    public function init() {
//        Yii::$app->session->set("checkMac", true);
        // if(!Yii::$app->session->get("checkMac")) {
        //     $this->locationUrl('该电脑未授权，请先授权再操作！', 'index.php?r=site/logout');
        //     Yii::$app->end();
        // }
        if(!Yii::$app->session->get("loginTime") || time() - Yii::$app->session->get("loginTime") > 1800) {
            $this->locationUrl('长时间未操作，请重新登录！', 'index.php?r=site/logout');
            Yii::$app->end();
        }
        Yii::$app->session->set("loginTime", time());
        $actionId = Yii::$app->requestedRoute;
        if(!in_array($actionId, ["admin/editpwd", "system/setcompanyinfo"])) {
            $this->checkFirstLogin();
        } 
        $authResult = $this->checkAuth();
        if(is_array($authResult)) {
            echo Json::encode($authResult);
            Yii::$app->end();
        }
        if(!Admin::checkSupperAdmin() && !Admin::checkBusinAdmin() && !RoleJurisdiction::checkRoleJue(Yii::$app->requestedRoute)){
            $this->redirect("index.php?r=site/nojure");
            Yii::$app->end();
        }
        if(Yii::$app->requestedRoute == "system/auth" && !Admin::checkSupperAdmin()) {
            $this->redirect("index.php?r=site/nojure");
            Yii::$app->end();
        }
        $cache = Yii::$app->cache;
        if(!isset($cache['logo'])) {
            $logoModel = Config::findOne(['set_name' => 'logo']);
            Yii::$app->cache->set("logo", $logoModel ? $logoModel->set_value : "", 86400);
        }
        if(!isset($cache['company_'.Admin::getDepId()])) {
            $companyModel = Config::findOne(['set_name' => 'company']);
            $department = Department::getNameById(Admin::getDepId());
            Yii::$app->cache->set("company_".Admin::getDepId(), ($companyModel ? $companyModel->set_value . " -- " . $department : ""), 86400);
        }
        $this->checkSafetyLogout();
        parent::init();
    }
    
    /**
     * 验证是否首次登录
     */
    private function checkFirstLogin() {
        $is_first = Yii::$app->user->getIdentity()->is_first;
        if(Admin::checkSupperAdmin() && $is_first == 0) {
            $this->locationUrl('开业清库后请设置公司名称及公司LOGO', 'index.php?r=system/setcompanyinfo');
            Yii::$app->end();
        }
        if($is_first == 0 || $is_first == 1){
            
            $this->locationUrl('首次登录，请您先修改密码', 'index.php?r=admin/editpwd');
            Yii::$app->end();
        }
        return true;
    }
    
    /**
     * 验证单点登录
     */
    private function checkSafetyLogout() {   
        $identity = Yii::$app->user->getIdentity();
        if(Yii::$app->session->get("safetyIp") == Yii::$app->request->getUserIP() && Yii::$app->session->get("safetyTime") == $identity->last_login) {
            return true;
        }
        Yii::$app->session->set("username", $identity->username);
        $this->locationUrl('当前帐号已在其他浏览器登录，是否强制登录！', 'index.php?r=site/relogin', true);
        Yii::$app->end();
    }
    
    /**
     * 验证盘点是否过期
     */
    public function checkCheckOverdue() {
        $checkFlowQuery = CheckFlow::find();
        $checkFlowQuery->andWhere(['not in', 'status', [Flow::STATUS_FINISH, Flow::STATUS_VERIFY_REJECT, Flow::STATUS_APPROVAL_REJECT, Flow::STATUS_FINISH_REJECT, Flow::STATUS_UNION_REJECT, Flow::STATUS_HANG_UP]]);
        $checkFlowAll = $checkFlowQuery->all();
        foreach ($checkFlowAll as $val) {
            if(strtotime($val->end_time." 23:59:59") < time()) {
                $ftype = $val->type == CheckPlanningFlow::TYPE_PLANNING ? Flow::TYPE_CHECK_PLANNING_PROOF : ($val->type == CheckPlanningFlow::TYPE_DEPARTMENT ? Flow::TYPE_CHECK_DEPARTMENT_PROOF : Flow::TYPE_CHECK_WAREHOUSE_PROOF);
                Flow::Overdue($ftype, $val, "过期挂起");
            }
        }
        $query = CheckPlanningFlow::find();
        $query->andWhere(['not in', 'status', [Flow::STATUS_VERIFY_REJECT, Flow::STATUS_APPROVAL_REJECT, Flow::STATUS_FINISH_REJECT, Flow::STATUS_UNION_REJECT, Flow::STATUS_HANG_UP]]);
        $query->andWhere(["is_proof" => 0]);
        $checkPlanningAll = $query->all();
        foreach ($checkPlanningAll as $val) {
            if(strtotime($val->end_time." 23:59:59") < time()) {
                $ftype = $val->type == CheckPlanningFlow::TYPE_PLANNING ? Flow::TYPE_CHECK_PLANNING : ($val->type == CheckPlanningFlow::TYPE_DEPARTMENT ? Flow::TYPE_CHECK_DEPARTMENT : Flow::TYPE_CHECK_WAREHOUSE);
                Flow::Overdue($ftype, $val, "过期挂起");
            }
        }
    }
    
    /**
     * 跳转页面 
     * @param type $message 信息
     * @param type $url 跳转地址
     */
    public function locationUrl($message, $url, $isConfirm = false) {
        header("Content-Type:text/html;charset=UTF-8");
        if($isConfirm) {
            echo "<script type='text/javascript'>if(confirm('{$message}')){window.location.href='{$url}';}else{window.location.href='index.php?r=site/logout'}</script>";
        } else {
            echo "<script type='text/javascript'>alert('{$message}');window.location.href='{$url}';</script>";
        }
    }
    
    /**
     * 验证是否授权
     */
    public function checkAuth() {
        $authKeyRoute = Yii::$app->session->get("authKeyRoute");
        $authKeyParams = Yii::$app->session->get("authKeyParams");
        if(!$authKeyRoute && !$authKeyParams) {
            return true;
        }
        $route = Yii::$app->requestedRoute;
        $routeList = explode("/", $route);
        if(in_array($routeList[1], ["verify", "approval", "finish", "reject"])) {
            if(!Yii::$app->session->get("authTime") || time() - Yii::$app->session->get("authTime") > 300) {
                Yii::$app->session->set("authTime", "");
                Yii::$app->session->set("authKeyRoute", "");
                Yii::$app->session->set("authKeyParams", "");
                return ["error" => 0, "message" => "长时间未操作，请重新授权"];
            }
            return true;
        }
        $params = Yii::$app->request->get("id") ? Yii::$app->request->get("id") : 0;
        if($authKeyRoute != $route  || $authKeyParams != $params || !Yii::$app->session->get("authTime") || time() - Yii::$app->session->get("authTime") > 300) {
            Yii::$app->session->set("authTime", "");
            Yii::$app->session->set("authKeyRoute", "");
            Yii::$app->session->set("authKeyParams", "");
        }
        Yii::$app->session->set("authTime", time());
        return true;
    }
}
