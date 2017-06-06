<?php
namespace app_web\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use app_web\components\CController;
use yii\helpers\Json;
use common\models\ProductInvoicingSale;
use common\models\ProductInvoicingSaleInfo;
use common\models\Admin;
use libs\common\Flow;

/**
 * 销存管理 -- 实时销存管理
 */
class InvoicingsaleController extends CController {
    
    /**
     * 实时销存列表页
     */
    public function actionIndex()
    {
        $status = Yii::$app->request->get('status');
        $keyword = Yii::$app->request->get('keyword');
        $sn = Yii::$app->request->get('sn');
        $warehouse_id = Yii::$app->request->get('warehouse_id');
        $beginDate = Yii::$app->request->get('beginDate');
        $endDate = Yii::$app->request->get('endDate');
        $model = new ProductInvoicingSale();
        $query = ProductInvoicingSale::find();
//        if(!Admin::checkSupperFlowAdmin()){
//      	  $query->andWhere(["department_id" => Admin::getDepId()]);
//        }
        if(is_numeric($status)){
            $query->andWhere(['status' => $status]);
        }
        if($keyword || is_numeric($keyword)){
            $query->andWhere(['like', 'name', $keyword]);
        }
        if($sn || is_numeric($sn)){
            $query->andWhere(['sn'=> $sn]);
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
        if(is_numeric($warehouse_id)) {
            $query->andWhere(['warehouse_id' => $warehouse_id]);
        }
        $query->orderBy('id desc');
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10,
                'validatePage' => false,
            ],
        ]);
        $listDatas = $dataProvider->getModels();
        $listPages = $dataProvider->getPagination();
        return $this->render('index', compact(['model', 'listDatas', 'listPages', 'message']));
    }
    
    /**
     * 实时销存详情页
     * @param type $id 实时销存ID
     */
    public function actionInfo($id)
    {
        $model = ProductInvoicingSale::findOne($id);
        $info = ProductInvoicingSaleInfo::findAll(["invoicing_sale_id" => $id]);
        return $this->render('info', compact('model', 'info'));
    }
    
    /**
     * 实时销存取消
     * @param type $id 实时销存ID
     * @return type
     */
    public function actionCancel($id) {
        $model = ProductInvoicingSale::findOne($id);
        if(!$model) {
            return Json::encode(["message" => "网络异常，请刷新再试", "error" => 1]);
        }
        $result = $model->cancel();
        $result["error"] = $result["state"] ? 0 : 1;
        return Json::encode($result);
    }
}
