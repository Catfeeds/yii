<?php
namespace app_web\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\Json;
use app_web\components\CController;
use common\models\WarehouseSale;
use common\models\WarehouseSaleProduct;
use libs\common\Flow;
use common\models\Admin;
/**
 * 业务操作 - 资金管理 -- 销存核实
 */
class WsaleController extends CController
{
    /**
     * 销存核实列表
     */
    public function actionIndex()
    {
        $sn = Yii::$app->request->get('sn');
        $status = Yii::$app->request->get('status');
        $keyword = Yii::$app->request->get('keyword');
        $beginDate = Yii::$app->request->get('beginDate');
        $endDate = Yii::$app->request->get('endDate');
        $model = new WarehouseSale();
        $query = WarehouseSale::find();
//        if(!Admin::checkSupperFlowAdmin()){
//            $query->andWhere(["department_id" => Admin::getDepId()]);
//        }
        $query->andWhere(['not in', 'status', [Flow::STATUS_VERIFY_REJECT, Flow::STATUS_APPROVAL_REJECT, Flow::STATUS_FINISH_REJECT, Flow::STATUS_UNION_REJECT,  Flow::STATUS_FINISH, Flow::STATUS_HANG_UP]]);
        if($status){
            $query->andWhere(['status' => $status]);
        }
        if($sn || $sn ==='0'){
            $query->andWhere(['like', 'sn', $sn]);
        }
        if($keyword || $keyword ==='0'){
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
                'pageSize' => 10,
                'validatePage' => false,
            ],
        ]);
        $listDatas = $dataProvider->getModels();
        $listPages = $dataProvider->getPagination();
        return $this->render('index', compact(['model', 'listDatas', 'listPages', 'message']));
    }
    
    /**
     * 详情页
     */
    public function actionInfo($id)
    {
        $model = WarehouseSale::findOne($id);
        $info = WarehouseSaleProduct::findAll(["sale_id" => $id]);
        return $this->render('info', compact('model', 'info'));
    }
    
    /**
     * 审核
     */
    public function actionVerify($id) 
    {
        $model = WarehouseSale::findOne($id);
        $remark = Yii::$app->request->get('remark');
        $result = Flow::Verify(Flow::TYPE_SALE, $model, $remark);
        return Json::encode($result);
    }
    
    /**
     * 批准
     */
    public function actionApproval($id)
    {
        $model = WarehouseSale::findOne($id);
        $remark = Yii::$app->request->get('remark');
        $result = Flow::Approval(Flow::TYPE_SALE, $model, $remark);
        return Json::encode($result);
    }
    
    /**
     * 执行完成 
     */
    public function actionFinish()
    {
        $id = Yii::$app->request->get('id');
        $model = WarehouseSale::findOne($id);
        $remark = Yii::$app->request->get('remark');
        $result = Flow::Finish(Flow::TYPE_SALE, $model, $remark);
        return Json::encode($result);
    }
    
    /**
     * 驳回
     */
    public function actionReject()
    {
        $id = Yii::$app->request->get('id');
        $failCause = Yii::$app->request->get('failCause');
        $model = WarehouseSale::findOne($id);
        $result = Flow::Reject(Flow::TYPE_SALE, $model, $failCause);
        return Json::encode($result);
    }
}
