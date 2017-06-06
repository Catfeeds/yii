<?php
namespace app_web\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\Json;
use app_web\components\CController;

use common\models\Department;
use common\models\DepartmentBalanceLog;
use libs\common\Flow;
use common\models\Admin;
use libs\Utils;
/**
 * 业务操作 - 资金管理 -- 资金流水日志
 */
class DepartmentbalancelogController extends CController
{
    /**
     * 资金流水日志列表
     */
    public function actionIndex()
    {
        $department_id = Yii::$app->request->get('department_id');
        $business_type = Yii::$app->request->get('business_type');
        $mod = Yii::$app->request->get('mod');
        $status = Yii::$app->request->get('status');
        $keyword = Yii::$app->request->get('keyword');
        $beginDate = Yii::$app->request->get('beginDate');
        $endDate = Yii::$app->request->get('endDate');
        $model = new DepartmentBalanceLog();
        $query = DepartmentBalanceLog::find();
//        if(!Admin::checkSupperFlowAdmin()){
//            $query->andWhere(["department_id" => Admin::getDepId()]);
//        }  
        if($department_id){
            $query->andWhere(['department_id' => $department_id]);
        }
        if($business_type){
            $query->andWhere(['business_type' => $business_type]);
        }
        if($mod){
            $query->andWhere(['mod' => $mod]);
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
        return $this->render('index', compact(['model', 'listDatas', 'listPages','message']));
    }
    
    /**
     * 导出处理 - 资金流水列表
     */
    public function ExportIndex($query)
    {
        $all = $query->all();
        $datas = [];
        foreach ($all as $key => $val) {
            $datas[] = [
                'key' => $key+1,
                'create_time' => $val->create_time,
                'name' => $val->name,
                'department_id' => Department::getNameById($val->department_id),
                'balanceIn' => $val->mod == 1 ? number_format($val->balance) : 0,
                'balanceOut' => $val->mod == 2 ? number_format($val->balance) : 0 ,
                'mod' => $val->showMod(),
                'current_balance' => $val->current_balance,                
                'content' => $val->content,                
                'operation_admin_id' => Admin::getNameById($val->operation_admin_id),
            ];
        }
        $columns = [
            [ 'attribute' => 'key','header' => '序号'],
            [ 'attribute' => 'create_time','header' => '创建时间'],
            [ 'attribute' => 'name','header' => '表单'],
            [ 'attribute' => 'department_id','header' => '部门'],
            [ 'attribute' => 'balanceIn','header' => '收入金额'],
            [ 'attribute' => 'balanceOut','header' => '支付金额'],
            [ 'attribute' => 'mod','header' => '收入支出项说明'],
            [ 'attribute' => 'current_balance','header' => '结存余额'],
            [ 'attribute' => 'content','header' => '操作备注'],
            [ 'attribute' => 'operation_admin_id','header' => '表单执行人'],
        ];
        return Utils::downloadExcel($datas, $columns, "资金流水列表");
    }
    
    /**
     * 详情页
     */
    public function actionInfo($id)
    {
        $model = DepartmentBalanceLog::findOne($id);
        return $this->render('info', compact('model'));
    }
    
}
