<?php
namespace app_web\controllers;

use libs\Utils;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\Json;
use app_web\components\CController;
use yii\helpers\Url;

use common\models\Combination;
use common\models\AdminLog;
use common\models\CombinationProduct;
use common\models\Admin;
use common\models\Warehouse;
/**
 * 业务设置 -- 订单模版管理
 */
class CombinationController extends CController
{
    /**
     * 订单模版列表页
     */
    public function actionIndex()
    {
        $keyword = Yii::$app->request->get('keyword');
        $warehouse_id = Yii::$app->request->get('warehouse_id');
        $beginDate = Yii::$app->request->get('beginDate');
        $endDate = Yii::$app->request->get('endDate');
        $model = new Combination();
        $query = Combination::find();
        if(is_numeric($warehouse_id)){
            $query->andWhere(['warehouse_id' => $warehouse_id]);
        }
        if($keyword != ""){
            $query->andWhere(['like', 'name', $keyword]);
        }
        $message = "";
        if($beginDate && $endDate && strtotime($beginDate) > strtotime($endDate)) {
            $message = "开始时间不能大于结束时间";
        }
        if($beginDate){
            $query->andFilterCompare('create_time', $beginDate." 00:00:00", '>=');
        }
        if($endDate){
            $query->andFilterCompare('create_time', $endDate." 23:59:59",  '<=');
        }
        $query->orderBy('id desc');
        $isDownload = Yii::$app->request->get("isDownload");
        if($isDownload) {
            $this->downloadIndex($query);
        } 
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 5,
                'validatePage' => false,
            ],
        ]);
        $listDatas = $dataProvider->getModels();
        $listPages = $dataProvider->getPagination();
        return $this->render('index', compact(['model', 'listDatas', 'listPages', 'message']));
    }

    /**
     * 新增订单模版
     */
    public function actionAdd()
    {
        $model = new Combination();
        $model->warehouse_id = Warehouse::WAREHOUSE_HQ;
        if(Yii::$app->request->post()) {
            $result = $model->addCombination(Yii::$app->request->post());
            if($result["state"]) {
                $return["message"] = "申请成功";
                $return["type"] = "url";
                $return["url"] = Url::to(["combination/index"]);
                return Json::encode($return);
            }
            $return['message'] = is_array($result["message"]) ? reset($result["message"]) : $result["message"];
            return Json::encode($return);
        }
        return $this->render("add", compact("model"));
    }

    /**
     * 组合物料模版删除
     * @param int $id 模版ID
     */
    public function actionDelete($id)
    {
        $model = Combination::findOne($id);
        if($model->delete()){
            //记录日志
            $code = 'delete_combination';
            $content = '删除组合物料模板' .$model->id;
            AdminLog::addLog($code, $content);
            $return['error'] = 0;
        }else{
            $return['error'] = 1;
            $return['message'] = '操作不成功！';
        }
        return Json::encode($return);
    }
    
    /**
     * 组合物料模版详情页
     * @param type $id 模版ID
     */
    public function actionInfo($id)
    {
        $model = Combination::findOne($id);
        $info = CombinationProduct::findAll(["order_template_id" => $id]);
        return $this->render('info', compact(['model', 'info']));
    }
    
    /**
     * 订单模版修改页
     * @param type $id 模版ID
     */
    public function actionUpdate($id)
    {
        $model = Combination::findOne($id);
        $info = CombinationProduct::findAll(["order_template_id" => $id]);
        if(Yii::$app->request->post()){
            $result = $model->updateTemplate(Yii::$app->request->post());
            if($result["state"]) {
                $return["message"] = "申请成功";
                $return["type"] = "url";
                $return["url"] = Url::to(["combination/index"]);
                return Json::encode($return);
            }
            $return['message'] = $result["message"];
            return Json::encode($return);
        }
        return $this->render('update', compact('model', 'info'));
    }
    
    /**
     * 导出订单模版列表
     * @param type $query 查询对象
     * @return type
     */
    public function downloadIndex($query) {
        $all = $query->all();
        $datas = [];    
        foreach ($all as $key => $val) {
            $datas[] = [
                'key' => $key+1,
                "name" => $val->name,
                "warehouse_id" => Warehouse::getNameById($val->warehouse_id),
                "create_time" => $val->create_time,
                "payment" => $val->showPayment(),
                "deposit" => $val->deposit,
                "common" => $val->common ? $val->common : "无",
                "create_admin_id" => Admin::getNameById($val->create_admin_id),
            ];
        }
        $columns = [
            [ 'attribute' => 'key','header' => '序号'],
            [ 'attribute' => 'name','header' => '模版名称'],
            [ 'attribute' => 'warehouse_id','header' => '仓库'],
            [ 'attribute' => 'create_time','header' => '模版制定时间'],
            [ 'attribute' => 'payment','header' => '订单付款方式'],
            [ 'attribute' => 'deposit','header' => '定金'],
            [ 'attribute' => 'common','header' => '用途说明'],
            [ 'attribute' => 'create_admin_id','header' => '制定人'],
        ];
        return Utils::downloadExcel($datas, $columns, "组合出库模板记录");
    }
}
