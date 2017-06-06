<?php
namespace app_web\controllers;

use common\models\Product;
use common\models\ProductStock;
use common\models\Warehouse;
use common\models\WarehouseTransfer;
use common\models\WarehouseBack;
use common\models\WarehouseWastage;
use common\models\WarehouseCheck;
use common\models\WarehouseCheckout;
use common\models\WarehouseTransferDep;
use common\models\CheckPlanning;
use common\models\CheckPlanningCondition;
use common\models\BusinessAll;
use common\models\Supplier;
use libs\common\Flow;
use libs\Utils;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\Json;
use app_web\components\CController;
use yii\helpers\ArrayHelper;
use common\models\ProductInvoicingSale;
use common\models\Admin;
use yii\helpers\Url;
/**
 * 业务操作 -- 库存管理
 */
class PstockController extends CController
{
    /**
     * 查看库存管理表单  
     */
    public function actionIndex()
    {
        $keyword = Yii::$app->request->get('keyword');
        $warehouseId = Yii::$app->request->get("warehouseId");
        $supplier_id = Yii::$app->request->get("supplier_id");
        $model = new ProductStock();
        $query = ProductStock::find();
//        if(!Admin::checkSupperFlowAdmin()){
//      	  $query->andWhere(["warehouse_id" => Admin::getWarehouseIdsById()]);
//        }
        if($warehouseId){
            $query->andWhere(['warehouse_id' => $warehouseId]);
        }
        if($supplier_id){
            $query->andWhere(['supplier_id' => $supplier_id]);
        }
        if(is_numeric($keyword)){
            $query->andWhere(['product_id'=> $keyword]);
        } else if($keyword != ""){
            $productAll = Product::find()->andWhere(['like','name',$keyword])->all();
            if($productAll) {
                $productIds = ArrayHelper::getColumn($productAll, 'id');
                $query->andWhere(['product_id'=> $productIds]);
            } else {
                $query->andWhere(['product_id'=> 0]);
            }
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
        return $this->render('index', compact(['model', 'listDatas', 'listPages']));
    }
    
    /**
     * 库存管理表单页
     */
    public function actionStockmanage() {
        $department_id = Yii::$app->request->get('department_id');
        $warehouse_id = Yii::$app->request->get('warehouse_id');
    	$model = new BusinessAll();
        $query = BusinessAll::find();
//        if(!Admin::checkSupperFlowAdmin()){
//            $query->andWhere(['warehouse_id' => Admin::getWarehouseIdsById()]);
//        }
        $query->andWhere(["business_type" => [Flow::TYPE_BUYING, Flow::TYPE_BACK, Flow::TYPE_CHECKOUT, Flow::TYPE_TRANSFEFDEP, Flow::TYPE_TRANSFEF, Flow::TYPE_MATERIALRETURN, Flow::TYPE_WASTAGE]]);
        if(is_numeric($department_id)) {
            $query->andWhere(["department_id"=> $department_id]);
        }
        if(is_numeric($warehouse_id)) {
            $query->andWhere(["warehouse_id" => $warehouse_id]);
        }
        $query->orderBy('id desc');
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
                'validatePage' => false,
            ],
        ]);
        $listDatas = $dataProvider->getModels();
        $listPages = $dataProvider->getPagination();
        return $this->render('stockManage', compact(['model', 'listDatas', 'listPages']));
    }
    
    /**
     * 库存调仓申请
     */
    public function actionTransfer() 
    {
        $model = new WarehouseTransfer();
        if(Yii::$app->request->post()) {
            $result = $model->addTransfer(Yii::$app->request->post());
            if($result["state"]) {
                $return["message"] = "申请成功";
                $return["type"] = "url";
                $return["url"] = Url::to(["wtransfer/index"]);
                return Json::encode($return);
            }
            $return['message'] = is_array($result["message"]) ? reset($result["message"]) : $result["message"];
            return Json::encode($return);
        }
        return $this->render("transfer", compact("model"));
    }
    
    /**
     * 库存耗损申请
     */
    public function actionWastage()
    {
        $model = new WarehouseWastage();
        if(Yii::$app->request->post()) {
            $result = $model->addWastage(Yii::$app->request->post());
            if($result["state"]) {
                $return["message"] = "申请成功";
                $return["type"] = "url";
                $return["url"] = Url::to(["wwastage/index"]);
                return Json::encode($return);
            }
            $return['message'] = is_array($result["message"]) ? reset($result["message"]) : $result["message"];
            return Json::encode($return);
        }
        return $this->render("wastage", compact("model"));
    }
    
    /**
     * 库存盘点申请
     */
    public function actionCheck()
    {
        $model = new WarehouseCheck();
        if(Yii::$app->request->post()) {
            $result = $model->addCheck(Yii::$app->request->post());
            if($result["state"]) {
                $return["message"] = "申请成功";
                $return["type"] = "url";
                $return["url"] = Url::to(["wcheck/index"]);
                return Json::encode($return);
            }
            $return['message'] = is_array($result["message"]) ? reset($result["message"]) : $result["message"];
            return Json::encode($return);
        }
        return $this->render("check", compact("model"));
    }
    
    /**
     * 库存盘点计划盘点
     */
    public function actionCheckplanning()
    {
        $planningId = Yii::$app->request->get("planningId");
        $planningItem = CheckPlanningCondition::findOne($planningId);
        $model = new WarehouseCheck();
        $model->warehouse_id = $planningItem->warehouse_id;
        $model->check_planning_id = $planningItem->check_planning_id;
        $model->name = CheckPlanning::getNameById($planningItem->check_planning_id) ."-".Warehouse::getNameById($planningItem->warehouse_id)."盘点";
        $productQuery = Product::find();
        if($planningItem->supplier_id > 0) {
            $productQuery->andWhere(['supplier_id' => 1]);
        }
        if($planningItem->material_type > 0) {
            $productQuery->andWhere(['material_type' => 1]);
        }
        $productAll = $productQuery->all();
        $productIds = ArrayHelper::getColumn($productAll, "id");
        $productAll = ArrayHelper::index($productAll, "id");
        $pStockQuery = ProductStock::find();
        $pStockQuery->andWhere(["warehouse_id" => $planningItem->warehouse_id]);
        if($productIds) {
            $pStockQuery->andWhere(["product_id" => $productIds]);
        }
        $pStockAll = $pStockQuery->all();
        if(Yii::$app->request->post()) {
            if($planningItem->status != CheckPlanningCondition::STATUS_CHECKING || $planningItem->check_admin_id != Yii::$app->user->getId()) {
                $return['message'] = "盘点计划错误";
                return Json::encode($return);
            }
            $result = $model->addCheck(Yii::$app->request->post());
            if($result["state"]) {
                $return["message"] = "申请成功";
                $return["type"] = "url";
                $return["url"] = Url::to(["wcheck/index"]);
                return Json::encode($return);
            }
            $return['message'] = is_array($result["message"]) ? reset($result["message"]) : $result["message"];
            return Json::encode($return);
        }
        return $this->render("checkplanning", compact("model", "productAll", "pStockAll"));
    }
    
    /**
     * 库存出库申请
     */
    public function actionCheckout()
    {
        $model = new WarehouseCheckout();
        $model->warehouse_id = Warehouse::WAREHOUSE_HQ;
        if(Yii::$app->request->post()) {
            $result = $model->addCheckout(Yii::$app->request->post());
            if($result["state"]) {
                $return["message"] = "申请成功";
                $return["type"] = "url";
                $return["url"] = Url::to(["wcheckout/index"]);
                return Json::encode($return);
            }
            $return['message'] = is_array($result["message"]) ? reset($result["message"]) : $result["message"];
            return Json::encode($return);
        }
        return $this->render("checkout", compact("model"));
    }
    
    /**
     * 物料销存申请
     */
    public function actionInvoicingsale() {
        $model = new ProductInvoicingSale();
        if(Yii::$app->request->post()) {
            $result = $model->addInvoicingSale(Yii::$app->request->post());
            if($result["state"]) {
                $return["message"] = "申请成功";
                $return["type"] = "url";
                $return["url"] = Url::to(["invoicingsale/index"]);
                return Json::encode($return);
            }
            $return['message'] = is_array($result["message"]) ? reset($result["message"]) : $result["message"];
            return Json::encode($return);
        }
        return $this->render("invoicingSale", compact("model"));
    }
    
    /**
     * 库存转货申请
     */
    public function actionTransferdep() 
    {
        $model = new WarehouseTransferDep();
        if(Yii::$app->request->post()) {
            $result = $model->addTransferDep(Yii::$app->request->post());
            if($result["state"]) {
                $return["message"] = "申请成功";
                $return["type"] = "url";
                $return["url"] = Url::to(["wtransferdep/index"]);
                return Json::encode($return);
            }
            $return['message'] = is_array($result["message"]) ? reset($result["message"]) : $result["message"];
            return Json::encode($return);
        }
        return $this->render("transferdep", compact("model"));
    }
    
    /**
     * 库存退仓申请
     */
    public function actionBack() 
    {
        $model = new WarehouseBack();
        $model->receive_warehouse_id = Warehouse::WAREHOUSE_HQ;
        if(Yii::$app->request->post()) {
            $result = $model->addBack(Yii::$app->request->post());
            if($result["state"]) {
                $return["message"] = "申请成功";
                $return["type"] = "url";
                $return["url"] = Url::to(["wback/index"]);
                return Json::encode($return);
            }
            $return['message'] = is_array($result["message"]) ? reset($result["message"]) : $result["message"];
            return Json::encode($return);
        }
        return $this->render("back", compact("model"));
    }
    
    /**
     * 异步获取仓库物流列表
     */
    public function actionAjaxproductlist()
    {
        $barcode = Yii::$app->request->get('barcode');
        $keyword = Yii::$app->request->get('keyword');
        $warehouseId = Yii::$app->request->get('warehouseId');
        $supplierId = Yii::$app->request->get('supplierId');
        $supplierName = Yii::$app->request->get('supplierName');
        $materialType = Yii::$app->request->get('material_type');
        $model = new ProductStock();
        $query = ProductStock::find();
        if($warehouseId){
            $query->andWhere(['warehouse_id' => $warehouseId]);
        }
        if($supplierId){
            $query->andWhere(['supplier_id' => $supplierId]);
        }
        if($keyword || is_numeric($keyword)){
            $productAll = Product::find()->where(['like','name',$keyword])->all();
            if($productAll) {
                $productIds = ArrayHelper::getColumn($productAll, 'id');
                $query->andWhere(['product_id'=> $productIds]);
            } else {
                $query->andWhere(['product_id'=> 0]);
            }
        }
        if($materialType){
            $productAll = Product::findAll(["material_type" => $materialType]);
            if($productAll) {
                $productIds = ArrayHelper::getColumn($productAll, 'id');
                $query->andWhere(['product_id'=> $productIds]);
            } else {
                $query->andWhere(['product_id'=> 0]);
            }
        }
        if($barcode || is_numeric($barcode)) {
            $productAll = Product::findAll(['barcode'=>$barcode]);
            if($productAll) {
                $productIds = ArrayHelper::getColumn($productAll, 'id');
                $query->andWhere(['product_id'=> $productIds]);
            } else {
                $query->andWhere(['product_id'=> 0]);
            }
        }
        if($supplierName || is_numeric($supplierName)) {
            $supplierAll = Supplier::find()->where(['like','name',$supplierName])->all();
            if($supplierAll) {
                $supplierIds = ArrayHelper::getColumn($supplierAll, 'id');
                $query->andWhere(['supplier_id'=> $supplierIds]);
            } else {
                $query->andWhere(['supplier_id'=> 0]);
            }
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
        return $this->renderPartial('_productlist', compact('model', 'listDatas', 'listPages'));
    }

      /**
     * 异步获取部门的库存
     *2017年2月21日 17:28:26
     *肖波
     * @param int $id 对应的部门ID
     */
    public function actionAjaxdepartproductlist($id)
    {  
        $keyword = Yii::$app->request->get('keyword');
        $barcode = Yii::$app->request->get("barcode");
        $model = new ProductStock();
        $query = ProductStock::find();
        //得到对应的部门中的仓库 2017年2月21日 17:46:23肖波
       $warehouse=Warehouse::findAll(['department_id'=>$id]);
       $warehouse_id = ArrayHelper::getColumn( $warehouse, 'id');
        //得到所有部门下的仓库中所有的商品 2017年2月21日 17:47:39 肖波
        $query->andWhere(['warehouse_id'=> $warehouse_id]);
        if($keyword || is_numeric($keyword)){
            $productAll = Product::find()->where(['like','name',$keyword])->all();
            if($productAll) {
                $productIds = ArrayHelper::getColumn($productAll, 'id');
                $query->andWhere(['product_id'=> $productIds]);
            } else {
                $query->andWhere(['product_id'=> 0]);
            }
        }
        if($barcode || is_numeric($barcode)) {
            $productAll = Product::findAll(['barcode'=>$barcode]);
            if($productAll) {
                $productIds = ArrayHelper::getColumn($productAll, 'id');
                $query->andWhere(['product_id'=> $productIds]);
            } else {
                $query->andWhere(['product_id'=> 0]);
            }
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
        return $this->renderPartial('_productlist', compact('model', 'listDatas', 'listPages'));
    }
}
