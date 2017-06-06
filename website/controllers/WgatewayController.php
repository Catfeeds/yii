<?php
namespace app_web\controllers;

use common\models\WarehouseGateway;
use common\models\Product;
use common\models\Warehouse;
use common\models\WarehousePlanning;
use common\models\WarehouseBuyingProduct;
use libs\Utils;
use Yii;
use yii\data\ActiveDataProvider;
use app_web\components\CController;
use yii\helpers\ArrayHelper;
use common\models\Admin;
use libs\common\Flow;
/**
 * 业务操作 -- 库存管理 -- 库存出入库管理
 */
class WgatewayController extends CController
{
    /**
     * 库存出入库列表页
     */
    public function actionIndex()
    {
        $warehouse_id = Yii::$app->request->get('warehouse_id');
        $product_id = Yii::$app->request->get('product_id');
        $product_name = Yii::$app->request->get('product_name');
        $type = Yii::$app->request->get('type');
        $gateway_type = Yii::$app->request->get('gateway_type');
        $beginDate = Yii::$app->request->get('beginDate');
        $endDate = Yii::$app->request->get('endDate');
        $model = new WarehouseGateway();
        $query = WarehouseGateway::find();
//        if(!Admin::checkSupperFlowAdmin()){
//            $query->andWhere(["warehouse_id" => Admin::getWarehouseIdsById()]);
//        }
        if($warehouse_id){
            $query->andWhere(['warehouse_id' => $warehouse_id]);
        }
        if($product_id || $product_id ==='0'){
            $query->andWhere(['product_id' => $product_id]);
        }
        if($type){
            $query->andWhere(['type' => $type]);
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
        if($gateway_type){
            $query->andWhere(['gateway_type' => $gateway_type]);
        }
        if($product_name){
            $productAll = Product::find()->where(['like','name',$product_name])->all();
            if($productAll) {
                $productIds = ArrayHelper::getColumn($productAll, 'id');
                $query->andWhere(['product_id'=> $productIds]);
            } else {
                $query->andWhere(['product_id'=> 0]);
            }
        }
        $query->orderBy('id desc');
        $isDownload = Yii::$app->request->get('isDownload');
        if($isDownload) {
            $all = $query->all();
            $this->downloadIndex($all);
        } 
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 15,
                'validatePage' => false,
            ],
        ]);
        $listDatas = $dataProvider->getModels();
        $listPages = $dataProvider->getPagination();
        
        return $this->render('index', compact(['model', 'listDatas', 'listPages', 'message']));
    }
    
    /**
     * 库存出入库日志记录导出
     */
    private function downloadIndex($all) {
        $datas = [];    
        foreach ($all as $key => $val) {
            $product = "";
            if($val->product_type == WarehousePlanning::TYPE_EXCEPTION) {
                $product = WarehouseBuyingProduct::findOne($val->product_id);
            }
            $datas[] = [
                'id' => $key+1,
                'product_id' => $val->product_type == WarehousePlanning::TYPE_EXCEPTION ? 0 : $val->product_id,
                'batches' => $val->batches,
                'name' => $val->product_type == WarehousePlanning::TYPE_EXCEPTION ? ($product ? $product->name : "未知") : Product::getNameById($val->product_id),
                'warehouse_id' => Warehouse::getNameById($val->warehouse_id),
                'showType' => $val->showType(),
                'stock' => $val->stock,
                'num' => $val->num,
                'showGatewayType' => $val->showGatewayType(),
                'create_time' => $val->create_time,
                'comment' => $val->comment ? $val->comment : "无",
            ];
        }
        $columns = [
            [ 'attribute' => 'id','header' => '序号'],
            [ 'attribute' => 'product_id','header' => '物料ID'],
            [ 'attribute' => 'batches','header' => '批次号'],
            [ 'attribute' => 'name','header' => '物料名称'],
            [ 'attribute' => 'warehouse_id','header' => '仓库名称'],
            [ 'attribute' => 'showType','header' => '出入库类型'],
            [ 'attribute' => 'stock','header' => '当时库存'],
            [ 'attribute' => 'num','header' => '操作数量'],
            [ 'attribute' => 'showGatewayType','header' => '操作类型'],
            [ 'attribute' => 'create_time','header' => '操作时间'],
            [ 'attribute' => 'comment','header' => '备注'],
        ];
        return Utils::downloadExcel($datas, $columns, "物料库存出入库日志");
    }
    
    
}
