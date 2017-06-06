<?php
namespace app_web\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\Json;
use app_web\components\CController;
use yii\helpers\Url;
use common\models\WarehousePlanningProduct;
use common\models\WarehousePlanning;
use common\models\OrderTemplate;
use common\models\OrderTemplateProduct;
use libs\common\Flow;
use common\models\Admin;
use common\models\WarehouseProcurement;
/**
 * 业务操作 -- 采购计划
 */
class WplanningController extends CController
{
    /**
     * 采购计划列表
     */
    public function actionIndex()
    {
        $sn = Yii::$app->request->get('sn');
        $status = Yii::$app->request->get('status');
        $keyword = Yii::$app->request->get('keyword');
        $beginDate = Yii::$app->request->get('beginDate');
        $endDate = Yii::$app->request->get('endDate');
        $payment = Yii::$app->request->get('payment');
        $model = new WarehousePlanning();
        $query = WarehousePlanning::find();
//        if(!Admin::checkSupperFlowAdmin()){
//            $query->andWhere(["department_id" => Admin::getDepId()]);
//        }
        if($sn || is_numeric($sn)){
            $query->andWhere(['like', 'sn', $sn]);
        }
        $query->andWhere(['not in', 'status', [Flow::STATUS_VERIFY_REJECT, Flow::STATUS_APPROVAL_REJECT, Flow::STATUS_FINISH_REJECT, Flow::STATUS_UNION_REJECT,  Flow::STATUS_FINISH, Flow::STATUS_HANG_UP]]);
        if(is_numeric($status)){
            $query->andWhere(['status' => $status]);
        }
        if($payment){
            $query->andWhere(['payment' => $payment]);
        }
        if($keyword || $keyword==='0'){
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
     * 新增页
     */
    public function actionAdd()
    {
        $model = new WarehousePlanning;
        $model->buckle_amount = 0;
        $model->type = WarehousePlanning::TYPE_NORMAL;
        return $this->render('add', compact('model'));
    }
    
    /**
     * 新增处理方法
     */
    public function actionCreate()
    {
        $model = new WarehousePlanning();
        if(Yii::$app->request->post()){
            $result = $model->addOrUpdatePlanning(Yii::$app->request->post());
            if($result["state"]) {
                $return["message"] = "新增成功";
                $return["type"] = "url";
                $return["url"] = Url::to(["wplanning/index"]);
                return Json::encode($return);
            }
            $return['message'] = is_array($result["message"]) ? reset($result["message"]) : $result["message"];
        }
        return Json::encode($return);
    }

    /**
     * 修改方法
     * @param type $id
     */
    public function actionUpdate($id)
    {
        $model = WarehousePlanning::findOne($id);
        $info = WarehousePlanningProduct::findAll(["planning_id" => $id]);
        if(Yii::$app->request->post()){
            if($model->type == WarehousePlanning::TYPE_EXCEPTION) {
                $result = $model->addOrUpdateExceptionPlanning(Yii::$app->request->post());
            } else {
                $result = $model->addOrUpdatePlanning(Yii::$app->request->post());
            }
            if($result["state"]) {
                $return["message"] = "修改成功";
                $return["type"] = "url";
                $return["url"] = Url::to(["wplanning/index"]);
                return Json::encode($return);
            }
            $return['message'] = is_array($result["message"]) ? reset($result["message"]) : $result["message"];
            return Json::encode($return);
        }
        if($model->type == WarehousePlanning::TYPE_EXCEPTION) {
            return $this->render('updateexception', compact('model', 'info'));
        } else {
            return $this->render('update', compact('model', 'info'));
        }
    }
    
    /**
     * 详情页
     */
    public function actionInfo($id)
    {
        $model = WarehousePlanning::findOne($id);
        $info = WarehousePlanningProduct::findAll(["planning_id" => $id]);
        return $this->render('info', compact('model', 'info'));
    }
    
    /**
     * 审核
     */
    public function actionVerify($id) 
    {
        $model = WarehousePlanning::findOne($id);
        $remark = Yii::$app->request->get('remark');
        $ftype = $model->type == WarehousePlanning::TYPE_NORMAL ? Flow::TYPE_PLANNING : ($model->type == WarehousePlanning::TYPE_ROUTINE ? Flow::TYPE_PLANNING_ROUTINE : Flow::TYPE_PLANNING_EXCEPTION);
        $result = Flow::Verify($ftype, $model, $remark);
        return Json::encode($result);
    }
    
    /**
     * 批准
     */
    public function actionApproval($id)
    {
        $model = WarehousePlanning::findOne($id);
        $remark = Yii::$app->request->get('remark');
        $ftype = $model->type == WarehousePlanning::TYPE_NORMAL ? Flow::TYPE_PLANNING : ($model->type == WarehousePlanning::TYPE_ROUTINE ? Flow::TYPE_PLANNING_ROUTINE : Flow::TYPE_PLANNING_EXCEPTION);
        $result = Flow::Approval($ftype, $model, $remark);
        return Json::encode($result);
    }
    
    /**
     * 执行完成 
     */
    public function actionFinish()
    {
        $id = Yii::$app->request->get('id');
        $model = WarehousePlanning::findOne($id);
        $remark = Yii::$app->request->get('remark');
        $ftype = $model->type == WarehousePlanning::TYPE_NORMAL ? Flow::TYPE_PLANNING : ($model->type == WarehousePlanning::TYPE_ROUTINE ? Flow::TYPE_PLANNING_ROUTINE : Flow::TYPE_PLANNING_EXCEPTION);
        $result = Flow::Finish($ftype, $model, $remark);
        return Json::encode($result);
    }
    
    /**
     * 驳回
     */
    public function actionReject()
    {
        $id = Yii::$app->request->get('id');
        $failCause = Yii::$app->request->get('failCause');
        $model = WarehousePlanning::findOne($id);
        $ftype = $model->type == WarehousePlanning::TYPE_NORMAL ? Flow::TYPE_PLANNING : ($model->type == WarehousePlanning::TYPE_ROUTINE ? Flow::TYPE_PLANNING_ROUTINE : Flow::TYPE_PLANNING_EXCEPTION);
        $result = Flow::Reject($ftype, $model, $failCause);
        return Json::encode($result);
    }
    
    /**
     * 添加例行订单
     */
    public function actionAddroutine()
    {
        $tempId = Yii::$app->request->get("tempId");
        $item = OrderTemplate::findOne($tempId);
        $info = OrderTemplateProduct::findAll(["order_template_id" => $tempId]);
        $model = new WarehousePlanning;
        $model->buckle_amount = 0;
        $model->type = WarehousePlanning::TYPE_ROUTINE;
        $model->supplier_id = $item->supplier_id;
        $model->payment = $item->payment;
        $model->deposit = $item->deposit;
        echo $this->render("addroutine", compact('model', 'item', 'info'));
    }
    
    /**
     * 添加例外订单
     */
    public function actionAddexception()
    {
        $model = new WarehousePlanning;
        $model->buckle_amount = 0;
        $model->type = WarehousePlanning::TYPE_EXCEPTION;
        if(Yii::$app->request->post()){
            $result = $model->addOrUpdateExceptionPlanning(Yii::$app->request->post());
            if($result["state"]) {
                $return["message"] = "新增成功";
                $return["type"] = "url";
                $return["url"] = Url::to(["wplanning/index"]);
                return Json::encode($return);
            }
            $return['message'] = is_array($result["message"]) ? reset($result["message"]) : $result["message"];
            return Json::encode($return);
        }
        echo $this->render("addexception", compact('model', 'item', 'info'));
    }
}
