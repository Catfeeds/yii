<?php
namespace app_web\controllers;
use libs\Utils;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\Json;
use app_web\components\CController;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

use common\models\WarehouseProcurement;
use common\models\WarehouseProcurementProduct;
use common\models\WarehouseBuying;
use common\models\Warehouse;
use libs\common\Flow;
use common\models\FlowConfig;
use common\models\Admin;
use common\models\Supplier;
/**
 * 业务操作 -- 采购下定
 */
class WprocurementController extends CController
{
    /**
     * 采购下定列表页
     */
    public function actionIndex()
    {
        $sn = Yii::$app->request->get('sn');
        $status = Yii::$app->request->get('status');
        $keyword = Yii::$app->request->get('keyword');
        $beginDate = Yii::$app->request->get('beginDate');
        $endDate = Yii::$app->request->get('endDate');
        $payment = Yii::$app->request->get('payment'); 
        $supplierName = Yii::$app->request->get("supplierName");       
        $model = new WarehouseProcurement();        
        $query = WarehouseProcurement::find();
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
        //供应商查询
        if($supplierName != ""){
           $supplierAll = Supplier::find()->where(['like','name',$supplierName])->all();
           if($supplierAll) {
               $supplierIds = ArrayHelper::getColumn($supplierAll, 'id');
               $query->andWhere(['supplier_id'=> $supplierIds]);
           } else {
               $query->andWhere(['supplier_id'=> 0]);
           }
        }
        if($payment){
            $query->andWhere(['payment' => $payment]);
        }
        if($keyword){
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
        $isDownload = Yii::$app->request->get('isDownload');
        if($isDownload) {
            $this->ExportIndex($query);
        }
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
     * 导出处理 - 历史表单统计
     */
    public function ExportIndex($query)
    {
        $all = $query->all();
        $datas = [];
        foreach ($all as $key => $val) {
            $nextStep = Flow::showNextStepByInfo(Flow::TYPE_ORDER, $val);
            $datas[] = [
                'key' => $key+1,
                'create_time' => $val->create_time,                
                'name' => $val->name,                
                'warehouse_id' => Warehouse::getNameById($val->warehouse_id),                
                'sn' => $val->sn,                
                'total_amount' => number_format($val->total_amount, 2),                
                'payment' => $val->showPayment(),                
                'deposit' => number_format($val->deposit, 2),                
                'planning_date' => $val->planning_date,                
                'payment_term' => $val->payment_term,                
                'config_id' => FlowConfig::getNameById($val->config_id),                
                'create_admin_id' => Admin::getNameById($val->create_admin_id),                
                'verify_admin_id' => Admin::getNameById($val->verify_admin_id),                
                'approval_admin_id' => Admin::getNameById($val->approval_admin_id),                
                'operation_admin_id' => Admin::getNameById($val->operation_admin_id),                
                'status' => Flow::showStatus($val->status),                
                'nextStep' => isset($nextStep["nextStep"]) ? $nextStep["nextStep"] : "无" ,                
            ];
        }
        $columns = [
            [ 'attribute' => 'key','header' => '序号'],
            [ 'attribute' => 'create_time','header' => '创建时间'],
            [ 'attribute' => 'name','header' => '采购名称'],
            [ 'attribute' => 'warehouse_id','header' => '仓库名称'],
            [ 'attribute' => 'sn','header' => '编号'],
            [ 'attribute' => 'total_amount','header' => '采购总价'],
            [ 'attribute' => 'payment','header' => '付款方式'],
            [ 'attribute' => 'deposit','header' => '定金'],
            [ 'attribute' => 'planning_date','header' => '计划下单时间'],
            [ 'attribute' => 'payment_term','header' => '付款时间'],
            [ 'attribute' => 'config_id','header' => '流程名称'],
            [ 'attribute' => 'create_admin_id','header' => '制表人'],
            [ 'attribute' => 'verify_admin_id','header' => '审核人'],
            [ 'attribute' => 'approval_admin_id','header' => '批准人'],
            [ 'attribute' => 'operation_admin_id','header' => '执行人'],
            [ 'attribute' => 'status','header' => '进展状态'],
            [ 'attribute' => 'nextStep','header' => '下一步操作'],
        ];
        return Utils::downloadExcel($datas, $columns, "采购下定列表");
    }
    
    /**
     * 详情页面
     */
    public function actionInfo($id)
    {
        $model = WarehouseProcurement::findOne($id);
        $info = WarehouseProcurementProduct::findAll(["procurement_id" => $id]);
        return $this->render('info', compact('model', 'info'));
    }
    
    /**
     * 审核操作
     */
    public function actionVerify($id) 
    {
        $model = WarehouseProcurement::findOne($id);
        $remark = Yii::$app->request->get('remark');
        $result = Flow::Verify(Flow::TYPE_ORDER, $model, $remark);
        return Json::encode($result);
    }
    
    /**
     * 批准操作
     */
    public function actionApproval($id)
    {
        $model = WarehouseProcurement::findOne($id);
        $remark = Yii::$app->request->get('remark');
        $result = Flow::Approval(Flow::TYPE_ORDER, $model, $remark);
        return Json::encode($result);
    }
    
    /**
     * 完成操作
     */
    public function actionFinish($id)
    {
        $model = WarehouseProcurement::findOne($id);
        $remark = Yii::$app->request->get('remark');
        $result = Flow::Finish(Flow::TYPE_ORDER, $model, $remark);
        return Json::encode($result);
//        $item = WarehouseProcurement::findOne($id);
//        $info = WarehouseProcurementProduct::findAll(["procurement_id" => $id]);
//        $model = new WarehouseBuying();
//        if(Yii::$app->request->post()) {
//            $post = Yii::$app->request->post();
//            $result = $model->addBuying($item, $info, $post);
//            if($result["state"]) {
//                Yii::$app->getSession()->setFlash("msg", "操作成功");
//                $return["type"] = "url";
//                $return["url"] = Url::to(["wbuying/index"]);
//                return Json::encode($return);
//            }
//            $return['message'] = is_array($result["message"]) ? reset($result["message"]) : $result["message"];
//            return Json::encode($return);
//        }
//        return $this->render('addBuying', compact('model','item', 'info'));
    }

    /**
     * 驳回操作
     */
    public function actionReject()
    {
        $id = Yii::$app->request->get('id');
        $failCause = Yii::$app->request->get('failCause');
        $model = WarehouseProcurement::findOne($id);
        $result = Flow::Reject(Flow::TYPE_ORDER, $model, $failCause);
        return Json::encode($result);
    }
}
