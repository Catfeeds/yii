<?php
namespace app_web\controllers;

use Yii;
use common\models\BusinessRemind;
use yii\data\ActiveDataProvider;
use app_web\components\CController;

/**
 * 系统 基础数据管理
 * 业务提醒
 */
class BusinessremindController extends CController
{

    /**
     * 业务提醒列表
     */
    public function actionIndex()
    {
        $status = Yii::$app->request->get('status');
        $model = new BusinessRemind();
        $query = BusinessRemind::find();
        //获取当前登录用户的 提醒列表
        $query->andWhere(['admin_id' => Yii::$app->user->id]);//重写方法,书写方便
        if(is_numeric($status)) {
            $query->andWhere([ 'status' => $status]);
        } else {
            $query->andWhere([ 'status' => 0]);
        }
        $query->orderBy('id desc');

        $dataProvider = new ActiveDataProvider([//可以封装
            'query' => $query,
            'pagination' => [
                'pageSize' => 10,
                'validatePage' => false,
            ],
        ]);
        $listDatas = $dataProvider->getModels();
        $listPages = $dataProvider->getPagination();
        return $this->render('index', compact(['model', 'listDatas', 'listPages']));
    }
    
    /**
     * 设置提醒已处理
     * @param type $id 提醒ID
     * @return type
     */
    public function actionConfirm($id){//id不存在需要判断
        $model = BusinessRemind::findOne($id);
        $model->status = 1;
        $model->save();
        return $this->redirect(['index']);
    }


}
