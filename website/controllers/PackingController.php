<?php
namespace app_web\controllers;
use Yii;
use yii\web\Controller;
use yii\helpers\Json;
use common\models\Computer;

class PackingController extends Controller {
    public function actionCheck() {
        $mac_addr = Yii::$app->request->get("mac_addr");
        if(!$mac_addr) {
            return Json::encode(["ret_val" => 2]);
        }
        $model = Computer::findOne(["mac" => $mac_addr, "status" => Computer::STATUS_OK]);
        if(!$model) {
            return Json::encode(["ret_val" => 2]);
        }
        Yii::$app->session->set("checkMac", true);
        Yii::$app->session->set("selRole", $model->role_id);
        return Json::encode(["ret_val" => 1]);
    }
}