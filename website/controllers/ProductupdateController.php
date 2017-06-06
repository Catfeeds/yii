<?php
namespace app_web\controllers;

use libs\Utils;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\Json;
use app_web\components\CController;

use common\models\ProductUpdate;
use common\models\ProductUpdateLog;
use libs\common\Flow;

/**
 * 业务基础数据 -- 物料管理 -- 物料修改
 */
class ProductupdateController extends CController
{
    /**
     * 物料修改记录列表页
     */
    public function actionIndex()
    {
        $status = Yii::$app->request->get('status');
        $keyword = Yii::$app->request->get('keyword');
        $supplier_id = Yii::$app->request->get('supplier_id');
        $beginDate = Yii::$app->request->get('beginDate');
        $endDate = Yii::$app->request->get('endDate');
        $model = new ProductUpdate();
        $query = ProductUpdate::find();
        $query->andWhere(['not in', 'status', [Flow::STATUS_VERIFY_REJECT, Flow::STATUS_APPROVAL_REJECT, Flow::STATUS_FINISH_REJECT, Flow::STATUS_UNION_REJECT,  Flow::STATUS_FINISH, Flow::STATUS_HANG_UP]]);
        if(is_numeric($status)){
            $query->andWhere(['status' => $status]);
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
        if(is_numeric($supplier_id)) {
            $query->andWhere(['supplier_id' => $supplier_id]);
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
     * 物料修改详情页
     * @param type $id 物料修改ID
     */
    public function actionInfo($id)
    {
        $model = ProductUpdate::findOne($id);
        return $this->render('info', compact('model'));
    }
    
    /**
     * 审核操作
     * @param type $id 物料修改ID
     */
    public function actionVerify($id) 
    {
        $model = ProductUpdate::findOne($id);
        $remark = Yii::$app->request->get('remark');
        $result = Flow::Verify(Flow::TYPE_PRODUCT_UPDATE, $model, $remark);
        return Json::encode($result);
    }
    
    /**
     * 批准操作
     * @param type $id 物料修改ID
     */
    public function actionApproval($id)
    {
        $model = ProductUpdate::findOne($id);
        $remark = Yii::$app->request->get('remark');
        $result = Flow::Approval(Flow::TYPE_PRODUCT_UPDATE, $model, $remark);
        return Json::encode($result);
    }
    
    /**
     * 完成操作
     * @param type $id 物料修改ID
     */
    public function actionFinish($id)
    {
        $model = ProductUpdate::findOne($id);
        $remark = Yii::$app->request->get('remark');
        $result = Flow::Finish(Flow::TYPE_PRODUCT_UPDATE, $model, $remark);
        return Json::encode($result);
    }

    /**
     * 驳回操作
     * @param type $id 物料修改ID
     */
    public function actionReject()
    {
        $id = Yii::$app->request->get('id');
        $failCause = Yii::$app->request->get('failCause');
        $model = ProductUpdate::findOne($id);
        $result = Flow::Reject(Flow::TYPE_PRODUCT_UPDATE, $model, $failCause);
        return Json::encode($result);
    }
    
    /**
     * 物料修改日志列表页
     */
    public function actionLog() {
        $keyword = Yii::$app->request->get('keyword');
        $supplier_id = Yii::$app->request->get('supplier_id');
        $beginDate = Yii::$app->request->get('beginDate');
        $endDate = Yii::$app->request->get('endDate');
        $query = ProductUpdateLog::find();
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
        if(is_numeric($supplier_id)) {
            $query->andWhere(['supplier_id' => $supplier_id]);
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
        return $this->render('log', compact(['model', 'listDatas', 'listPages', 'message']));
    }
    
    /**
     * 物料修改日志详情页
     */
    public function actionLoginfo($id) {
        $model = ProductUpdateLog::findOne($id);
        return $this->render('loginfo', compact('model'));
    }
}
