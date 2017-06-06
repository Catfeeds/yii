<?php
namespace app_dev\controllers;

use Yii;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\Controller;

/**
 * Ajax controller
 */
class AjaxController extends Controller
{
    private $return = [
        'error' => 1,
        'msg' => '',
        'html' => '',
        'json' => '',
        'link' => ''
    ];

    public function actionSelect()
    {
        $parentId = Yii::$app->request->post('parentId');
        $model = Yii::$app->request->post('model');
        $inputName = Yii::$app->request->post('inputName');

        $className = '\common\models\\' . ucfirst($model);

        $datas = $className::getSelectData($parentId);

        if($datas){
            $this->output([
                'error' => 0,
                'html' => Html::dropDownList($inputName, '', $datas, [
                    'class' => 'form-control',
                    'parent-id' => $parentId,
                    'ajax' => $model,
                    'ajax-url' => Url::to(['ajax/select']),
                ])
            ]);
        }else{
            $this->output(['error' => 0]);
        }
    }

    private function output($params)
    {
        $output = array_merge($this->return, $params);
        echo Json::encode($output);
        Yii::$app->end();
    }
}
