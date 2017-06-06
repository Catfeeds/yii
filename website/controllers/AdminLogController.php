<?php
namespace app_web\controllers;

use common\models\AdminLog;
use Yii;
use libs\Utils;
use yii\data\ActiveDataProvider;
use yii\helpers\Json;
use app_web\components\CController;

/**
 * 业务基础数据管理
 * 事件日志
 * dengxu  2016.5.16
 */
class AdminlogController extends CController
{
    /**
     * 事件日志列表
     */
    public function actionIndex()
    {
        $status = Yii::$app->request->get('status');
        $keyword = Yii::$app->request->get('keyword');
        $beginDate = Yii::$app->request->get('beginDate');
        $endDate = Yii::$app->request->get('endDate');
        $model = new AdminLog();
        
        $query = AdminLog::find();
        if(is_numeric($status)){
            $query->andWhere(['status' => $status]);
        }
        
        if($keyword){
            $query->andWhere(['like', 'content', $keyword]);
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
        $isDownload = Yii::$app->request->get("isDownload");
        if($isDownload) {
            $this->downloadIndex($query);
        } 
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
                'validatePage' => false,
            ],
        ]);

        $listDatas = $dataProvider->getModels();
        $listPages = $dataProvider->getPagination();
        
        return $this->render('index', compact(['model', 'listDatas', 'listPages', 'message']));
    }
    
    /**
     * 导出事件记录
     * @param type $query 查询对象
     * @return type
     */
    public function downloadIndex($query) {
        $all = $query->all();
        $datas = [];    
        foreach ($all as $key => $val) {
            $datas[] = [
                'key' => $key+1,
                "content" => $val->content,
                "create_time" => $val->create_time,
                "adminName" => $val->showAdminName(),
                "admin_id" => $val->admin_id,
                "status" => $val->showStatus(),
            ];
        }
        $columns = [
            [ 'attribute' => 'key','header' => '序号'],
            [ 'attribute' => 'content','header' => '事件'],
            [ 'attribute' => 'create_time','header' => '时间'],
            [ 'attribute' => 'adminName','header' => '操作人'],
            [ 'attribute' => 'admin_id','header' => '操作人ID'],
            [ 'attribute' => 'status','header' => '状态'],
        ];
        return Utils::downloadExcel($datas, $columns, "事件日志记录");
    }
}
