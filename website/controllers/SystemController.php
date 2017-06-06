<?php

namespace app_web\controllers;

use libs\Utils;
use Yii;
use yii\helpers\Json;
use common\models\Config;
use app_web\components\CController;
use yii\web\UploadedFile;
use yii\helpers\ArrayHelper;
use common\models\Department;
use common\models\Admin;
use yii\helpers\Url;
use Exception;

/**
 * 系统基础数据管理
 */
class SystemController extends CController {
    /**
     * 设置公司Logo
     */
    public function actionLogo() {
        $key = 'logo';
        $model = Config::findOne(['set_name' => $key]);
        if (!$model) {
            $model = new Config();
            $model->set_name = $key;
            $model->set_desc = 'logo';
            $model->group_id = 1;
        }
        if ($model->load(Yii::$app->request->post())) {
            $logo = UploadedFile::getInstance($model, 'set_value');
            if(!$logo) {
                $message = '请选择图片！';
                $model = Config::findOne(['set_name' => $key]);
                return $this->render('logo', ['model' => $model, 'message' => $message]);
            }
            if(!in_array($logo->type, array("image/png", "image/jpg", "image/jpeg"))) {
                $message = '文件格式错误，请上传“.jpg .jpge .png”格式图片！';
                $model = Config::findOne(['set_name' => $key]);
                if (!$model) {
                    $model = new Config();
                    $model->set_name = $key;
                    $model->set_desc = 'logo';
                    $model->group_id = 1;
                }
                return $this->render('logo', ['model' => $model, 'message' => $message]);
            }
            $model->set_value = Utils::newFileName($logo->getExtension());
            if(!$model->set_value){
                $message = '网络异常，请重新上传！';
                $model = Config::findOne(['set_name' => $key]);
                if (!$model) {
                    $model = new Config();
                    $model->set_name = $key;
                    $model->set_desc = 'logo';
                    $model->group_id = 1;
                }
                return $this->render('logo', ['model' => $model, 'message' => $message]);
            }
            if ($model->set_value  && $model->save()) {
                if ($logo) {
                    $logo->saveAs(Utils::getFile($model->set_value));
                }
                Yii::$app->cache->set('logo', $model->set_value, 86400);
                $message = '操作成功！';
                return $this->render('logo', ['model' => $model, 'message' => $message]);
            }
        }
        return $this->render('logo', ['model' => $model]);
    }

    /**
     * 设置公司名称
     */
    public function actionCompany() {
        $key = 'company';
        $model = Config::findOne(['set_name' => $key]);
        if (!$model) {
            $model = new Config();
            $model->set_name = $key;
            $model->set_desc = '公司名称';
            $model->group_id = 2;
        }
        if ($model->load(Yii::$app->request->post())) {
            $model->set_value = strip_tags($model->set_value);
            if($model->save()) {
                $department = Department::getNameById(Admin::getDepId());
                Yii::$app->cache->set('company_'.Admin::getDepId(), ($model->set_value . " -- " . $department), 86400);
                $message = "操作成功";
            } else {
                $message = "操作不成功";
            }
            return $this->render('company', compact(["model", "message"]));
        } else {
            return $this->render('company', ['model' => $model]);
        }
    }

    /**
     * 验证超级管理员准入密码
     */
    public function actionDefault() {
        $key = '';
        $model = Config::findOne(['set_name' => $key]);
        if (!$model) {
            $model = new Config;
        }

        if ($model->load(Yii::$app->request->post())) {
            //判断密码
            $key = 'admin_ver_password';
            $model_ver = Config::findOne(['set_name' => $key]);
            if (!$model_ver) {
                $return['message'] = '超级管理员准入密码错误';
                return Json::encode($return);
            }
            if (!$model->set_value) {
                $return['message'] = '超级管理员准入密码不能为空';
                return Json::encode($return);
            }
            if (md5($model->set_value) != $model_ver->set_value) {
                $return['message'] = '超级管理员准入密码错误!';
                return Json::encode($return);
            }
            $return['type'] = "url";
            $return['url'] = Url::to(["system/cancel"]);
            return Json::encode($return);
        } else {
            return $this->render('default', [
                'model' => $model,
            ]);
        }
    }
    
    /**
     * 开业清库
     */
    public function actionCancel() {
        $ext_array = array('Area', 'Menu', 'AuthItem', 'AuthRule','area', 'menu', 'authItem', 'authrule');
        $Db = \Yii::$app->db;
        $list = $Db->createCommand('SHOW TABLE STATUS')->queryAll();

        $list = array_map('array_change_key_case', $list);
        $list = ArrayHelper::map($list, 'name', 'name');
        foreach ($list as $key => $val) {
            if (!in_array($val, $ext_array)) {
                $sql = "TRUNCATE TABLE $val";
                $Db->createCommand($sql)->execute();
            }
        }
        //新增一个角色
        $role = "INSERT INTO `Role` (`id`, `name`, `department_id`, `status`, `is_sole`) VALUES
(1, '超级管理员', 1, 1, 1),(2, '流程管理员', 1, 1, 1),(3, '业务管理员', 1, 1, 1);";
        $Db->createCommand($role)->execute();

          //新增一个部门
        $dep = "INSERT INTO `Department` (`id`, `name`, `parent_id`, `status`, `create_time`, `number`, `acronym`) VALUES
(1, '总部', 0, 1, '2016-08-23 16:52:27', '001', 'js');";
        $Db->createCommand($dep)->execute();

        //新增一个用户
        $adminSql = "INSERT INTO `Admin` (`id`, `username`, `password`, `auth_key`, `mobile`, `department_id`, `role_id`, `last_login`, `last_ip`, `status`, `name`, `id_card`, `job_number`, `entry_date`, `leave_date`, `create_time`, `update_time`, `is_safety`) VALUES
(1, 'admin', '$2y$13$2R.ZZHBniqn8HiZbPJoHFuVcGcZRkBGsWHtqKpwDQbuWE1eMH.uoe', 'pHJZ1S-zcGweAq9X-BtvoVdUYIa_zskj', '', 1, 1, 1472874166, '27.38.60.154', 1, NULL, 'zr001', 'zr001', '2016-10-01', NULL, '2016-08-23 16:51:52', '2016-09-03 11:42:46', 1),
(2, 'flowAdmin', '$2y$13$2R.ZZHBniqn8HiZbPJoHFuVcGcZRkBGsWHtqKpwDQbuWE1eMH.uoe', 'pHJZ1S-zcGweAq9X-BtvoVdUYIa_zskj', '', 1, 2, 1472874166, '27.38.60.154', 1, NULL, 'zr002', 'zr002', '2016-10-01', NULL, '2016-08-23 16:51:52', '2016-09-03 11:42:46', 1),
(3, 'businAdmin', '$2y$13$2R.ZZHBniqn8HiZbPJoHFuVcGcZRkBGsWHtqKpwDQbuWE1eMH.uoe', 'pHJZ1S-zcGweAq9X-BtvoVdUYIa_zskj', '', 1, 3, 1472874166, '27.38.60.154', 1, NULL, 'zr003', 'zr003', '2016-10-01', NULL, '2016-08-23 16:51:52', '2016-09-03 11:42:46', 1);";
                    $Db->createCommand($adminSql)->execute();

        $authsql = "INSERT INTO `Config` (`set_name`, `set_value`, `set_desc`, `group_id`) VALUES
('admin_ver_password', 'e10adc3949ba59abbe56e057f20f883e', '', 3),
('admin_business_password', 'e10adc3949ba59abbe56e057f20f883e', '超级管理员业务密码', 3),
('business_business_password', 'e10adc3949ba59abbe56e057f20f883e', '业务管理员业务密码', 3),
('flow_business_password', 'e10adc3949ba59abbe56e057f20f883e', '流程管理员业务密码', 3);";
        $Db->createCommand($authsql)->execute();

        //新增一个仓库
        $warehousesql = "INSERT INTO `Warehouse` (`id`, `name`, `type`, `status`,`area_id`,`is_sale`,`num`,`create_time`,`department_id`) VALUES
(1, '总仓库', 1, 1, 0, 0, 'zck', '".date("Y-m-d H:i:s")."', 1);";
        $Db->createCommand($warehousesql)->execute();
        Yii::$app->user->logout();
        $return['type'] = "url";
        $return['url'] = Url::to(["site/logout"]);
        $return['message'] = "清库成功，请重新登陆！";
        return Json::encode($return);
    }
    
    /**
     * 开业清库后设置公司名称及Logo
     */
    public function actionSetcompanyinfo() {
        $model = new Config();
        $message = "";
        $company = "";
        if (Yii::$app->request->post()) {
            $post = Yii::$app->request->post();
            $company = $post["company"];
            $result = $model->setCompanyInfo($post);
            if($result["state"]) {
                Yii::$app->getSession()->setFlash("msg", "设置成功");
                Yii::$app->getSession()->setFlash("url", Url::to(["businessremind/index"]));
                return $this->render('setcompanyinfo', compact("model", "message"));
            }
            $message = $result["message"];
        }
        return $this->render('setcompanyinfo', compact("model", "message", "company"));
    }

    /**
     * 设置管理员权限
     * @return type
     * @throws Exception
     */
    public function actionAuth() {
        $key = 'admin_ver_password';
        $model = Config::findOne(['set_name' => $key]);
        if (!$model) {
            $model = new Config();
        }
        if ($model->load(Yii::$app->request->post())) {
            $array = ['admin_ver_password' => '超级管理员开业清库密码', 'flow_business_password' => '流程管理员授权业务密码'];
            $transaction = Yii::$app->db->beginTransaction();
            try{
                $message = "请填写您需要修改的密码";
                foreach ($array as $key => $val) {
                    $key = $key;
                    $key1 = $key . '1';
                    $key2 = $key . '2';
                    if(!$model->$key && !$model->$key1 && !$model->$key2) {
                        continue;
                    }
                    $model_admin_ver = Config::findOne(['set_name' => $key]);
                    if (!$model_admin_ver) {
                        throw new Exception($val . '不存在');
                    }
                    if (!$model->$key || !$model->$key1 || !$model->$key2) {
                        throw new Exception($val . '的原密码和两次新密码必填');
                    } 
                    if ($model->$key1 != $model->$key2) {
                        throw new Exception($val . '的两次新密码不一样');
                    } 
                    if ($model->$key == $model->$key1) {
                        throw new Exception($val . '的新密码不能等于旧密码');
                    } 
                    if (md5($model->$key) != $model->set_value) {
                        throw new Exception($val . '的原密码错误');
                    } 
                    $model_admin_ver->set_value = md5($model->$key1);
                    $model_admin_ver->save();
                    $message = "保存成功";
                }
                $return['message'] = $message;
                $return['html'] = 1;
                $transaction->commit();
            } catch (Exception $ex) {
                $return['message'] = $ex->getMessage();
                $transaction->rollBack();
            }
            return Json::encode($return);
        } 
        return $this->render('auth', ['model' => $model]);
    }

}
