<?php
namespace app_web\controllers;

use libs\Utils;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\Json;
use app_web\components\CController;
use yii\web\UploadedFile;
use common\models\WarehouseTransferDep;
use common\models\WarehouseTransferDepProduct;
use libs\common\Flow;
use common\models\Admin;

/**
 * 业务操作 -- 库存管理 -- 转货管理
 */
class WtransferdepController extends CController
{
    /**
     * 仓库转货列表页
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
        $model = new WarehouseTransferDep();
        $query = WarehouseTransferDep::find();
//        if(!Admin::checkSupperFlowAdmin()){
//            $query->andWhere(["department_id" => Admin::getDepId()]);
//        }
        $query->andWhere(['not in', 'status', [Flow::STATUS_VERIFY_REJECT, Flow::STATUS_APPROVAL_REJECT, Flow::STATUS_FINISH_REJECT, Flow::STATUS_UNION_REJECT,  Flow::STATUS_FINISH, Flow::STATUS_HANG_UP]]);
        if(is_numeric($status)){
            $query->andWhere(['status' => $status]);
        }
        $is_buckle = Yii::$app->request->get('is_buckle');
        if(is_numeric($is_buckle)){
            $query->andWhere(['is_buckle' => $is_buckle]);
        }
        if($keyword || is_numeric($keyword)){
            $query->andWhere(['like', 'name', $keyword]);
        }
        if($sn || is_numeric($sn)){
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
     * 转货详情页
     * @param type $id 转货ID
     */
    public function actionInfo($id)
    {
        $model = WarehouseTransferDep::findOne($id);
        $info = WarehouseTransferDepProduct::findAll(["transfer_dep_id" => $id]);
        return $this->render('info', compact('model', 'info'));
    }
    
    /**
     * 审核操作
     * @param type $id 转货ID
     */
    public function actionVerify($id) 
    {
        $model = WarehouseTransferDep::findOne($id);
        $remark = Yii::$app->request->get('remark');
        $result = Flow::Verify(Flow::TYPE_TRANSFEFDEP, $model, $remark);
        return Json::encode($result);
    }
    
    /**
     * 批准操作
     * @param type $id 转货ID
     */
    public function actionApproval($id)
    {
        $model = WarehouseTransferDep::findOne($id);
        $remark = Yii::$app->request->get('remark');
        $result = Flow::Approval(Flow::TYPE_TRANSFEFDEP, $model, $remark);
        return Json::encode($result);
    }
    
    /**
     * 完成操作
     * @param type $id 转货ID
     */
    public function actionFinish($id)
    {
        $model = WarehouseTransferDep::findOne($id);
        $remark = Yii::$app->request->get('remark');
        $result = Flow::Finish(Flow::TYPE_TRANSFEFDEP, $model, $remark);
        return Json::encode($result);
    }

    /**
     * 驳回操作
     * @param type $id 转货ID
     */
    public function actionReject()
    {
        $id = Yii::$app->request->get('id');
        $failCause = Yii::$app->request->get('failCause');
        $model = WarehouseTransferDep::findOne($id);
        $result = Flow::Reject(Flow::TYPE_TRANSFEFDEP, $model, $failCause);
        return Json::encode($result);
    }
}
