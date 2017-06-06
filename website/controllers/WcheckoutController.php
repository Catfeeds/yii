<?php
namespace app_web\controllers;

use common\models\WarehouseCheckout;
use common\models\WarehouseCheckoutProduct;
use common\models\Combination;
use common\models\CombinationProduct;
use libs\common\Flow;

use libs\Utils;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\Json;
use app_web\components\CController;
use common\models\Admin;

/**
 * 业务操作 -- 库存管理 -- 出库管理
 */
class WcheckoutController extends CController
{
    /**
     * 出库列表页
     */
    public function actionIndex()
    {
        $status = Yii::$app->request->get('status');
        $keyword = Yii::$app->request->get('keyword');
        $sn = Yii::$app->request->get('sn');
        $warehouse_id = Yii::$app->request->get('warehouse_id');
        $receive_warehouse_id = Yii::$app->request->get('receive_warehouse_id');
        $beginDate = Yii::$app->request->get('beginDate');
        $endDate = Yii::$app->request->get('endDate');
        $model = new WarehouseCheckout();
        $query = WarehouseCheckout::find();
//        if(!Admin::checkSupperFlowAdmin()){
//            $query->andWhere(["receive_warehouse_id" => Admin::getWarehouseIdsById()]);
//        }
        $query->andWhere(['not in', 'status', [Flow::STATUS_VERIFY_REJECT, Flow::STATUS_APPROVAL_REJECT, Flow::STATUS_FINISH_REJECT, Flow::STATUS_UNION_REJECT,  Flow::STATUS_FINISH, Flow::STATUS_HANG_UP]]);
        if(is_numeric($status)){
            $query->andWhere(['status' => $status]);
        }
        $is_buckle = Yii::$app->request->get('is_buckle');
        if(is_numeric($is_buckle)){
            $query->andWhere(['is_buckle' => $is_buckle]);
        }
        if($keyword || $keyword==='0'){
            $query->andWhere(['like', 'name', $keyword]);
        }
        if($sn || $sn==='0'){

             $query->andWhere(['like', 'sn', $sn]);
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
        if(is_numeric($receive_warehouse_id)) {
            $query->andWhere(['receive_warehouse_id' => $receive_warehouse_id]);
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
     * 出库详情页
     */
    public function actionInfo($id)
    {
        $model = WarehouseCheckout::findOne($id);
        $info = WarehouseCheckoutProduct::findAll(["checkout_id" => $id]);
        return $this->render('info', compact('model', 'info'));
    }
    
    /**
     * 审核出库申请
     */
    public function actionVerify($id) 
    {
        $model = WarehouseCheckout::findOne($id);
        $remark = Yii::$app->request->get('remark');
        $result = Flow::Verify(Flow::TYPE_CHECKOUT, $model, $remark);
        return Json::encode($result);
    }
    
    /**
     * 批准出库申请
     */
    public function actionApproval($id)
    {
        $model = WarehouseCheckout::findOne($id);
        $remark = Yii::$app->request->get('remark');
        $result = Flow::Approval(Flow::TYPE_CHECKOUT, $model, $remark);
        return Json::encode($result);
    }
    
    /**
     * 出库申请完成
     */
    public function actionFinish($id)
    {
        $model = WarehouseCheckout::findOne($id);
        $remark = Yii::$app->request->get('remark');
        $result = Flow::Finish(Flow::TYPE_CHECKOUT, $model, $remark);
        return Json::encode($result);
    }

    /**
     * 驳回出库申请
     */
    public function actionReject()
    {
        $id = Yii::$app->request->get('id');
        $failCause = Yii::$app->request->get('failCause');
        $model = WarehouseCheckout::findOne($id);
        $result = Flow::Reject(Flow::TYPE_CHECKOUT, $model, $failCause);
        return Json::encode($result);
    }
    
    /**
     * 添加例行出库申请
     */
    public function actionAddroutine()
    {
        $tempId = Yii::$app->request->get("combId");
        $combNum = Yii::$app->request->get("combNum");
        $item = Combination::findOne($tempId);
        $info = CombinationProduct::findAll(["order_template_id" => $tempId]);
        $model = new WarehouseCheckout();
        $model->warehouse_id = $item->warehouse_id;
        $model->total_amount = $item->total_amount * $combNum;
        echo $this->render("addroutine", compact('model', 'item', 'info', 'combNum'));
    }
}
