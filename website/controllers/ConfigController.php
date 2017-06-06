<?php
namespace app_web\controllers;

use common\models\Config;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\Json;
use app_web\components\CController;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/**
 * 系统 基础数据管理
 * 参数设置
 */
class ConfigController extends CController
{
    /**
     * 配置数据列表页
     */
    public function actionIndex()
    {
        $query = Config::find();
        $query->andWhere(['like', 'set_name', "flowType"]);
        $query->orWhere(["set_name" => "commonSet"]);
        $query->orderBy('id desc');
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 100,
                'validatePage' => false,
            ],
        ]);
        $listDatas = $dataProvider->getModels();
        $configInfo = ArrayHelper::map($listDatas, "set_name", "set_value");
        return $this->render('index', compact(['configInfo']));
    }
    
    /**
     * 设置配置数据
     */
    public function actionSetinfo() {
        if(\Yii::$app->request->post()) {
            $model = new Config;
            $result = $model->setAllConfig(Yii::$app->request->post());
            if($result["state"]) {
                $return["message"] = "设置成功";
                $return["type"] = "url";
                $return["url"] = Url::to(["config/index"]);
                return Json::encode($return);
            }
            $return['message'] = is_array($result["message"]) ? reset($result["message"]) : $result["message"];
            return Json::encode($return);
        }
        $this->locationUrl('页面错误！', 'index.php?r=businessremind/index');
    }

}
