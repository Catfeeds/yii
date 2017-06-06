<?php
namespace app_web\behavior;

use Yii;
use yii\base\Behavior;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;

class PermissionBehavior extends Behavior
{

    public $actions = [];

    public function events()
    {
        return [
            Controller::EVENT_BEFORE_ACTION => 'beforeAction',
        ];
    }

    public function beforeAction($event)
    {
        $controller = $event->action->controller->id;
        $action = $event->action->id;

        $access = $controller . "_" . $action;

        if(Yii::$app->user->isGuest){
            return Yii::$app->user->loginRequired();
        }

        $auth = Yii::$app->authManager;

        if(!$data = $auth->getPermission($access)){
            $data = $auth->createPermission($access);
            $data->description = '创建了 ' . $access . ' 许可';
            $auth->add($data);
        }

        // 这里添加不需要验证的用户
        if (Yii::$app->user->id==1) return true;

        if(!Yii::$app->user->can($access)){
           // throw new ForbiddenHttpException('你无权访问这个页面！');
        }

        return true;
    }
}