<?php
namespace app_web\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use common\models\BusinessRemind;
use common\models\Admin;
use common\models\Config;
use common\models\Department;
use app_web\behavior\PermissionBehavior;
use yii\data\ActiveDataProvider;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * 设置权限
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['login', 'relogin'],
                        'allow' => true,
                    ],
                ],
            ],
        ];
    }

    /**
     * 设置错误页面
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * 首页
     */
    public function actionIndex()
    {
        $this->redirect("index.php?r=businessremind/index");
        Yii::$app->end();
    }
    /**
     * 登录页面
     */
    public function actionLogin()
    {  
        $model = new LoginForm();
        if($model->load(Yii::$app->request->post()) && $model->login()){
            $logoModel = Config::findOne(['set_name' => 'logo']);
            \Yii::$app->cache->set("logo", $logoModel ? $logoModel->set_value : "", 86400);
            $companyModel = Config::findOne(['set_name' => 'company']);
            $department = Department::getNameById(Admin::getDepId());
            \Yii::$app->cache->set("company_".Admin::getDepId(), ($companyModel ? $companyModel->set_value . " -- " . $department : ""), 86400);
            $this->redirect("index.php?r=businessremind/index");
            Yii::$app->end();
        }
        return $this->renderPartial('login', ['model' => $model,]);
    }
    
    /**
     * 强制登录页面
     */
    public function actionRelogin()
    {
        $this->layout = false;
        $model = new LoginForm();
        $model->username = Yii::$app->session->get("username");
        $model->isSafety = true;
        if($model->load(Yii::$app->request->post()) && $model->login()){
            Yii::$app->session->remove("isSafety");
            Yii::$app->session->remove("username");
            $logoModel = Config::findOne(['set_name' => 'logo']);
            \Yii::$app->cache->set("logo", $logoModel ? $logoModel->set_value : "", 86400);
            $companyModel = Config::findOne(['set_name' => 'company']);
            $department = Department::getNameById(Admin::getDepId());
            \Yii::$app->cache->set("company_".Admin::getDepId(), ($companyModel ? $companyModel->set_value . " -- " . $department : ""), 86400);
            $this->redirect("index.php?r=businessremind/index");
            Yii::$app->end();
        }
        return $this->render('relogin', [
            'model' => $model,
        ]);
    }

    /**
     * 退出
     */
    public function actionLogout()
    {
        Admin::updateAll(["is_safety" => 1], ["id" => \Yii::$app->user->getId()]);
        $checkMac = Yii::$app->session->get("checkMac") ? Yii::$app->session->get("checkMac") : false;
        $setRole = Yii::$app->session->get("setRole") ? Yii::$app->session->get("setRole") : -1;
        Yii::$app->user->logout();
        Yii::$app->session->set("checkMac", $checkMac);
        Yii::$app->session->set("setRole", $setRole);
        return $this->goHome();
    }
    
    /**
     * 没有权限
     */
    public function actionNojure() {
        echo $this->render("nojure");
    }
    
    /**
     * 报错页面
     */
    public function actionError() {
        $this->layout = false;
        echo $this->render("error");
    }
}
