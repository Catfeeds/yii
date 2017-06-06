<?php
namespace app_web\controllers;

use libs\Utils;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\Json;
use app_web\components\CController;
use yii\helpers\Url;

use common\models\OrderTemplate;
use common\models\AdminLog;
use common\models\OrderTemplateProduct;
/**
 * 业务设置 -- 订单模版管理
 */
class OrdertemplateController extends CController
{
    /**
     * 订单模版列表页
     */
    public function actionIndex()
    {
        $keyword = Yii::$app->request->get('keyword');
        $supplier_id = Yii::$app->request->get('supplier_id');
        $beginDate = Yii::$app->request->get('beginDate');
        $endDate = Yii::$app->request->get('endDate');
        $model = new OrderTemplate();
        $query = OrderTemplate::find();
        if(is_numeric($supplier_id)){
            $query->andWhere(['supplier_id' => $supplier_id]);
        }
        if($keyword || is_numeric($keyword)){
            $query->andWhere(['like', 'name', $keyword]);
        }
        if($beginDate){
            $query->andFilterCompare('create_time', $beginDate." 00:00:00", '>=');
        }
        if($endDate){
            $query->andFilterCompare('create_time', $endDate." 23:59:59",  '<=');
        }
        $message = "";
        if($beginDate && $endDate && strtotime($beginDate) > strtotime($endDate)) {
            $message = "开始时间不能大于结束时间";
        }
        $query->orderBy('id desc');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 5,
                'validatePage' => false,
            ],
        ]);

        $listDatas = $dataProvider->getModels();
        $listPages = $dataProvider->getPagination();
        
        return $this->render('index', compact(['model', 'listDatas', 'listPages', 'message']));
    }

    /**
     * 新增订单模版
     */
    public function actionAdd()
    {
        $model = new OrderTemplate();
        if(Yii::$app->request->post()) {
            $result = $model->addOrderTemplate(Yii::$app->request->post());
            if($result["state"]) {
                $return["message"] = "新增成功";
                $return["type"] = "url";
                $return["url"] = Url::to(["ordertemplate/index"]);
                return Json::encode($return);
            }
            $return['message'] = is_array($result["message"]) ? reset($result["message"]) : $result["message"];
            return Json::encode($return);
        }
        return $this->render("add", compact("model"));
    }

    /**
     * 订单模版删除
     * @param int $id 模版ID
     */
    public function actionDelete($id)
    {
        $model = OrderTemplate::findOne($id);
        if($model->delete()){
            //记录日志
            $code = 'delete_supplier';
            $content = '删除供应商' .$model->id;
            AdminLog::addLog($code, $content);
            $return['error'] = 0;
        }else{
            $return['error'] = 1;
            $return['message'] = '操作不成功！';
        }
        return Json::encode($return);
    }
    
    /**
     * 订单模版详情页
     * @param type $id 模版ID
     */
    public function actionInfo($id)
    {
        $model = OrderTemplate::findOne($id);
        $info = OrderTemplateProduct::findAll(["order_template_id" => $id]);
        return $this->render('info', compact(['model', 'info']));
    }
    
    /**
     * 订单模版修改页
     * @param type $id 模版ID
     */
    public function actionUpdate($id)
    {
        $model = OrderTemplate::findOne($id);
        $info = OrderTemplateProduct::findAll(["order_template_id" => $id]);
        if(Yii::$app->request->post()){
            $result = $model->updateTemplate(Yii::$app->request->post());
            if($result["state"]) {
                $return["message"] = "修改成功";
                $return["type"] = "url";
                $return["url"] = Url::to(["ordertemplate/index"]);
                return Json::encode($return);
            }
            $return['message'] = is_array($result["message"]) ? reset($result["message"]) : $result["message"];
            return Json::encode($return);
        }
        return $this->render('update', compact('model', 'info'));
    }
}
