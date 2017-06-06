<?php
namespace app_web\controllers;

use libs\Utils;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\Json;
use app_web\components\CController;
use yii\helpers\Url;
use yii\web\UploadedFile;
use common\models\AbnormalBalance;
use libs\common\Flow;
use common\models\Admin;

/**
 * 业务基础数据管理
 * 业务收支资金流水管理
 */
class AbnormalbalanceController extends CController
{
    /**
     * 业务收支资金列表页
     */
    public function actionIndex()
    {
        $department_id = Yii::$app->request->get('department_id');
        $mod = Yii::$app->request->get('mod');
        $status = Yii::$app->request->get('status');
        $keyword = Yii::$app->request->get('keyword');
        $beginDate = Yii::$app->request->get('beginDate');
        $endDate = Yii::$app->request->get('endDate');
        $model = new AbnormalBalance();
        $query = AbnormalBalance::find();
//        if(!Admin::checkSupperFlowAdmin()){
//            $query->andWhere(["department_id" => Admin::getDepId()])->orWhere(["income_department_id" => Admin::getDepId()]);
//        }  
        if($department_id){
            $query->andWhere(["department_id" => $department_id])->orWhere(["income_department_id" => $department_id]);
        }
        if($mod){
            $query->andWhere(['mod' => $mod]);
        }
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
     * 添加新的业务收支资金流水
     */
    public function actionAddorupdate() 
    {
        $id = Yii::$app->request->get("id");
        if(is_numeric($id)) {
            $model = AbnormalBalance::findOne($id);
        } else {
            $model = new AbnormalBalance();
        }
        if(Yii::$app->request->post()) {
            $result =$model->addAbnormalBalance(Yii::$app->request->post());
            if($result["state"]) {
                $return["message"] = "新增成功";
                $return["type"] = "url";
                $return["url"] = Url::to(["abnormalbalance/index"]);
                return Json::encode($return);
            }
            $return['message'] = is_array($result["message"]) ? reset($result["message"]) : $result["message"];
            return Json::encode($return);
        }
        return $this->render('addorupdate', compact('model'));
    }
    
    
    /**
     * 详情页
     */
    public function actionInfo($id)
    {
        $model = AbnormalBalance::findOne($id);
        return $this->render('info', compact('model'));
    }
    
    /**
     * 审核
     */
    public function actionVerify($id) 
    {
        $model = AbnormalBalance::findOne($id);
        $remark = Yii::$app->request->get("remark");
        $result = Flow::Verify(Flow::TYPE_ABNORMAL_FUND, $model, $remark);
        return Json::encode($result);
    }
    
    /**
     * 批准
     */
    public function actionApproval($id)
    {
        $model = AbnormalBalance::findOne($id);
        $remark = Yii::$app->request->get("remark");
        $result = Flow::Approval(Flow::TYPE_ABNORMAL_FUND, $model, $remark);
        return Json::encode($result);
    }
    
    /**
     * 执行完成 
     */
    public function actionFinish()
    {
        $id = Yii::$app->request->get('id');
        $remark = Yii::$app->request->get("remark");
        $model = AbnormalBalance::findOne($id);
        $result = Flow::Finish(Flow::TYPE_ABNORMAL_FUND, $model, $remark);
        return Json::encode($result);
    }
    
    /**
     * 驳回
     */
    public function actionReject()
    {
        $id = Yii::$app->request->get('id');
        $failCause = Yii::$app->request->get('failCause');
        $model = AbnormalBalance::findOne($id);
        $result = Flow::Reject(Flow::TYPE_ABNORMAL_FUND, $model, $failCause);
        return Json::encode($result);
    }
}
