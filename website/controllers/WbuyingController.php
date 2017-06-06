<?php
namespace app_web\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\Json;
use app_web\components\CController;
use yii\helpers\Url;

use common\models\WarehouseBuying;
use common\models\WarehouseBuyingProduct;
use common\models\ProductStock;
use libs\common\Flow;
use common\models\Admin;
/**
 * 业务操作 -- 库存管理 -- 入库记录管理
 */
class WbuyingController extends CController
{
    /**
     * 采购入库列表页
     */
    public function actionIndex()
    {
        $sn = Yii::$app->request->get('sn');
        $status = Yii::$app->request->get('status');
        $keyword = Yii::$app->request->get('keyword');
        $beginDate = Yii::$app->request->get('beginDate');
        $endDate = Yii::$app->request->get('endDate');
        $model = new WarehouseBuying();
        $query = WarehouseBuying::find();
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
        if($keyword || is_numeric($keyword)){
            $query->andWhere(['like', 'name', $keyword]);
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
     * 采购入库详情页
     * @param type $id 采购入库ID
     */
    public function actionInfo($id)
    {
        $model = WarehouseBuying::findOne($id);
        $info = WarehouseBuyingProduct::findAll(["buying_id" => $id]);
        return $this->render('info', compact(['model', 'info']));
    }
    
    /**
     * 审核操作
     * @param type $id 采购入库ID
     */
    public function actionVerify($id) 
    {
        $model = WarehouseBuying::findOne($id);
        $remark = Yii::$app->request->get('remark');
        $result = Flow::Verify(Flow::TYPE_BUYING, $model, $remark);
        return Json::encode($result);
    }
    
    /**
     * 批准操作
     * @param type $id 采购入库ID
     */
    public function actionApproval($id)
    {
        $model = WarehouseBuying::findOne($id);
        $remark = Yii::$app->request->get('remark');
        $result = Flow::Approval(Flow::TYPE_BUYING, $model, $remark);
        return Json::encode($result);
    }
    
    /**
     * 完成操作
     * @param type $id 采购入库ID
     */
    public function actionFinish($id)
    {
        $model = WarehouseBuying::findOne($id);
        $remark = Yii::$app->request->get('remark');
        $result = Flow::Finish(Flow::TYPE_BUYING, $model, $remark);
        return Json::encode($result);
//        $item = WarehouseBuying::findOne($id);
//        $info = WarehouseBuyingProduct::findAll(["buying_id" => $id]);
//        $model = new ProductStock();
//        if(Yii::$app->request->post()) {
//            $post = Yii::$app->request->post();
//            $result = $model->addStock($item, $info, $post);
//            if($result["state"]) {
//                Yii::$app->getSession()->setFlash("msg", "执行成功");
//                $return["type"] = "url";
//                $return["url"] = Url::to(["pstock/index"]);
//                return Json::encode($return);
//            }
//            $return['message'] = is_array($result["message"]) ? reset($result["message"]) : $result["message"];
//            return Json::encode($return);
//        }
//        return $this->render('addStock', compact('model','item', 'info'));
    }

    /**
     * 驳回
     */
    public function actionReject()
    {
        $id = Yii::$app->request->get('id');
        $failCause = Yii::$app->request->get('failCause');
        $model = WarehouseBuying::findOne($id);
        $result = Flow::Reject(Flow::TYPE_BUYING, $model, $failCause);
        return Json::encode($result);
    }
}
