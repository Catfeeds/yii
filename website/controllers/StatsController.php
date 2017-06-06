<?php
namespace app_web\controllers;

use libs\Utils;
use Yii;
use yii\data\ActiveDataProvider;
use app_web\components\CController;
use yii\helpers\ArrayHelper;
use libs\common\Flow;
use common\models\Admin;
use common\models\Product;
use common\models\ProductCategory;
use common\models\ProductStock;
use common\models\BusinessAll;
use common\models\Warehouse;
use common\models\WarehousePlanning;
use common\models\WarehouseBuying;
use common\models\WarehouseBuyingProduct;
use common\models\Supplier;
use common\models\WarehouseGateway;
use common\models\Department;
use common\models\WarehouseSale;
use common\models\AbnormalBalance;
use common\models\OrderProcurement;
use common\models\OrderMaterialReturn;
use common\models\DepartmentBalanceLog;
use common\models\WarehouseWastage;

/**
 * 查询统计
 */
class StatsController extends CController
{
    /**
     * 历史表单统计
     */
    public function actionIndex()
    {
        $department_id = Yii::$app->request->get('department_id');
        $warehouse_id = Yii::$app->request->get('warehouse_id');
        $sn = Yii::$app->request->get('sn');
        $business_type = Yii::$app->request->get('business_type');
        $isDownload = Yii::$app->request->get('isDownload');
        $beginDate = Yii::$app->request->get('beginDate');
        $endDate = Yii::$app->request->get('endDate');
        $model = new BusinessAll();
        $query = BusinessAll::find();
        $query->andWhere(["status"=> [Flow::STATUS_FINISH, Flow::STATUS_VERIFY_REJECT, Flow::STATUS_APPROVAL_REJECT, Flow::STATUS_FINISH_REJECT, Flow::STATUS_UNION_REJECT, Flow::STATUS_HANG_UP]]);
        $query->andWhere(["is_complete"=> 1]);
//        if(!Admin::checkSupperFlowAdmin()){
//            $query->andWhere(['department_id' => Admin::getDepId()]);
//        }
//        $query->andWhere(["business_type" => [Flow::TYPE_BUYING, Flow::TYPE_BACK, Flow::TYPE_CHECKOUT, Flow::TYPE_TRANSFEFDEP, Flow::TYPE_TRANSFEF, Flow::TYPE_MATERIALRETURN, Flow::TYPE_WASTAGE]]);
        if(is_numeric($department_id)) {
            $query->andWhere(["department_id"=> $department_id]);
        }
        if(is_numeric($warehouse_id)) {
            $query->andWhere(["warehouse_id" => $warehouse_id]);
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
        if($sn || is_numeric($sn)) {
            $query->andWhere(["like", "sn", $sn]);
        }
        if(is_numeric($business_type)) {
            $query->andWhere(["business_type" => $business_type]);
        }
        //历史表单数据
        $query->orderBy('id desc');
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
     * 导出处理 - 历史表单统计
     */
    public function ExportIndex($query)
    {
        $all = $query->all();
        $datas = [];
        foreach ($all as $key => $val) {
            $datas[] = [
                'key' => $key+1,
                'name' => $val->name,
                'sn' => $val->sn,
                'business_id' => $val->business_id,
                'business_type' => Flow::showType($val->business_type) ,
                'department_id' => Department::getNameById($val->department_id),
                'warehouse_id' => Warehouse::getNameById($val->warehouse_id),                
                'create_admin_id' => Admin::getNameById($val->create_admin_id),                
                'verify_admin_id' => Admin::getNameById($val->verify_admin_id),                
                'approval_admin_id' => Admin::getNameById($val->approval_admin_id),                
                'operation_admin_id' => Admin::getNameById($val->operation_admin_id),                
                'status' => Flow::showStatusAll($val->status),                
            ];
        }
        $columns = [
            [ 'attribute' => 'key','header' => '序号'],
            [ 'attribute' => 'name','header' => '表单名称'],
            [ 'attribute' => 'sn','header' => '表单号'],
            [ 'attribute' => 'business_id','header' => '管理表单ID'],
            [ 'attribute' => 'business_type','header' => '表单类型'],
            [ 'attribute' => 'department_id','header' => '所属部门'],
            [ 'attribute' => 'warehouse_id','header' => '所属仓库'],
            [ 'attribute' => 'create_admin_id','header' => '创建人'],
            [ 'attribute' => 'verify_admin_id','header' => '审核人'],
            [ 'attribute' => 'approval_admin_id','header' => '批准人'],
            [ 'attribute' => 'operation_admin_id','header' => '执行人'],
            [ 'attribute' => 'status','header' => '状态'],
        ];
        return Utils::downloadExcel($datas, $columns, "历史表单统计");
    }
	
    /**
     * 实时库存统计查询
     */
    public function actionRealtime()
    {
        $keyword = Yii::$app->request->get('keyword');
        $warehouseId = Yii::$app->request->get("warehouseId");
        $supplierName = Yii::$app->request->get("supplierName");
        $product_category_id = Yii::$app->request->get("product_category_id");
        $isDownload = Yii::$app->request->get('isDownload');
        $model = new ProductStock();
        $query = ProductStock::find();
//        if(!Admin::checkSupperFlowAdmin()){
//            $query->andWhere(['warehouse_id' => Admin::getWarehouseIdsById()]);
//        }
        if($warehouseId){
            $query->andWhere(['warehouse_id' => $warehouseId]);
        }
        //供应商查询
         if($supplierName != ""){
            $supplierAll = Supplier::find()->where(['like','name',$supplierName])->all();
            if($supplierAll) {
                $supplierIds = ArrayHelper::getColumn($supplierAll, 'id');
                $productAll = Product::findAll(['supplier_id'=> $supplierIds]);
                if($productAll) {
                    $productIds = ArrayHelper::getColumn($productAll, 'id');
                    $query->andWhere(['product_id'=> $productIds]);
                } else {
                    $query->andWhere(['product_id'=> 0]);
                }
            } else {
                $query->andWhere(['product_id'=> 0]);
            }
        }
        
        //分类查询
         if($product_category_id != ""){
         
            $productAll = Product::findAll(['product_category_id'=> $product_category_id]);
            if($productAll) {
                $productIds = ArrayHelper::getColumn($productAll, 'id');
                $query->andWhere(['product_id'=> $productIds]);
            } else {
                $query->andWhere(['product_id'=> 0]);
            }
          
        }
        
        if(is_numeric($keyword)){
            $query->andWhere(['product_id'=> $keyword]);
        } else if($keyword != ""){
            $productAll = Product::find()->where(['like','name', $keyword])->all();
            if($productAll) {
                $productIds = ArrayHelper::getColumn($productAll, 'id');
                $query->andWhere(['product_id'=> $productIds]);
            } else {
                $query->andWhere(['product_id'=> 0]);
            }
        }
        $query->orderBy('id desc');
        if($isDownload) {
            $this->ExportRealtime($query);
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
        
        return $this->render('realtime', compact(['model', 'listDatas', 'listPages']));
    }
    
    /**
     * 导出处理 - 实时库存统计
     */
    public function ExportRealtime($query)
    {
        $all = $query->all();
        $datas = [];
        foreach ($all as $key => $val) {
            if($val->type == WarehousePlanning::TYPE_EXCEPTION) {
                $productItem = WarehouseBuyingProduct::findOne($val->product_id);
            }else {
                $productItem = Product::findOne($val->product_id);
            }
            $datas[] = [
                'id' => $key + 1,
                'product_id' => $val->type == WarehousePlanning::TYPE_EXCEPTION ? 0 : $val->product_id,
                'name' => $productItem->name,
                'batches' => $val->batches,
                'warehouse_id' => Warehouse::getNameById($val->warehouse_id),
                'supplier_id' => $productItem->supplier_id." / ".Supplier::getNameById($productItem->supplier_id),
                'supplier_product_id' => $val->type == WarehousePlanning::TYPE_EXCEPTION ? 0 : $productItem->supplier_product_id,
                'material_type' => ProductCategory::getNameById($val->type == WarehousePlanning::TYPE_EXCEPTION ? $productItem->material_type : $productItem->product_category_id),
                'barcode' => $val->type == WarehousePlanning::TYPE_EXCEPTION ? $productItem->num : $productItem->barcode,
                'spec' => $productItem->spec,
                'unit' => $productItem->unit,
                'purchase_price' => $val->type == WarehousePlanning::TYPE_EXCEPTION ? $productItem->price : $productItem->purchase_price,
                'sale_price' => $val->type == WarehousePlanning::TYPE_EXCEPTION ? $productItem->purchase_price : $productItem->sale_price,
                'warning' => $val->type == WarehousePlanning::TYPE_EXCEPTION ? "不需要" : $productItem->showInventoryWarning(),
                'stock' => $val->number,
            ];
        }
        $columns = [
            [ 'attribute' => 'id','header' => '序号'],
            [ 'attribute' => 'product_id','header' => '物料ID'],
            [ 'attribute' => 'name','header' => '物料名称'],
            [ 'attribute' => 'batches','header' => '批次号'],
            [ 'attribute' => 'warehouse_id','header' => '所属仓库'],
            [ 'attribute' => 'supplier_id','header' => '供应商ID/名称'],
            [ 'attribute' => 'supplier_product_id','header' => '供应商物料ID'],
            [ 'attribute' => 'material_type','header' => '物料类型'],
            [ 'attribute' => 'barcode','header' => '物料条形码'],
            [ 'attribute' => 'spec','header' => '规格'],
            [ 'attribute' => 'unit','header' => '单位'],
            [ 'attribute' => 'purchase_price','header' => '采购价格'],
            [ 'attribute' => 'sale_price','header' => '销售定价'],
            [ 'attribute' => 'warning','header' => '库存预警'],
            [ 'attribute' => 'stock','header' => '库存'],
        ];
        return Utils::downloadExcel($datas, $columns, "实时库存统计");
    }
    
    /**
     * 采购入库报表
     */
    public function actionPurchase()
    {
        $warehouse_id = Yii::$app->request->get("warehouse_id");
        $material_type = Yii::$app->request->get("material_type");
        $supplier_id = Yii::$app->request->get("supplier_id");
        $beginDate = Yii::$app->request->get("beginDate");
        $endDate = Yii::$app->request->get("endDate");
        $department_id = Yii::$app->request->get("department_id");
        $isDownload = Yii::$app->request->get("isDownload");
        $buyingQuery = WarehouseBuying::find();
        $query = WarehouseBuyingProduct::find();
        if(is_numeric($warehouse_id)){
            $buyingQuery->andWhere(["warehouse_id" => $warehouse_id]);
        }
        if(is_numeric($supplier_id)){
            $buyingQuery->andWhere(["supplier_id" => $supplier_id]);
            $query->andWhere(["supplier_id" => $supplier_id]);
        }
//        if(!Admin::checkSupperFlowAdmin()){
//            $query->andWhere(["warehouse_id" => Admin::getWarehouseIdsById()]);
//        }
        if(is_numeric($department_id)) {
            $warehouseAll = Warehouse::findAll(["department_id" => $department_id]);
            if($warehouseAll) {
                $warehouseIds = ArrayHelper::getColumn($warehouseAll, "id");
                $buyingQuery->andWhere(["warehouse_id" => $warehouseIds]);
            } else {
                $buyingQuery->andFilterCompare(["=","warehouse_id" , 0]);
            }
        }
        $message = "";
        if($beginDate && $endDate && strtotime($beginDate) > strtotime($endDate)) {
            $message = "开始时间不能大于结束时间";
        }
        if($beginDate){
            $buyingQuery->andFilterCompare('create_time', $beginDate." 00:00:00", '>=');
        }
        if($endDate){
            $buyingQuery->andFilterCompare('create_time', $endDate." 23:59:59",  '<=');
        }
        $buyingQuery->andWhere(["status" => Flow::STATUS_FINISH]);
        $buyingAll = $buyingQuery->all();
        if(is_numeric($material_type)){
            $query->andWhere(["material_type" => $material_type]);
        }
        if($buyingAll) {
            $buyingId = ArrayHelper::getColumn($buyingAll, 'id');
            $query->andWhere(['buying_id' => $buyingId]);
        } else {
            $query->andWhere(["buying_id" => 0]);
        }
        $query->orderBy('id desc');
        if($isDownload) {
            $this->downloadPurchase($query);
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
        return $this->render('purchase', compact(['listDatas', 'listPages', 'message']));
    }
    
    /**
     * 采购入库报表
     */
    private function downloadPurchase($query) {
        $all = $query->all();
        $datas = [];    
        foreach ($all as $key => $val) {
            $buyingItem = WarehouseBuying::findOne($val->buying_id);
            $datas[] = [
                'id' => $key+1,
                'name' => $val->name,
                'product_id' => $val->product_id,
                'supplier_id' => Supplier::getNameById($val->supplier_id),
                'supplier_product_id' => $val->supplier_product_id,
                'spec' => $val->spec,
                'unit' => $val->unit,
                'product_number' => $val->product_number,
                'buying_number' => $val->buying_number,
                'material_type' => ProductCategory::getNameById($val->material_type),
                'price' => number_format($val->price, 2),
                'purchase_price' => number_format($val->purchase_price, 2),
                'warehouse_id' => Warehouse::getNameById($val->warehouse_id),
                'order_sn' => $buyingItem->order_sn,
                'create_time' => $buyingItem->create_time,
            ];
        }
        $columns = [
            [ 'attribute' => 'id','header' => '序号'],
            [ 'attribute' => 'name','header' => '物料名'],
            [ 'attribute' => 'product_id','header' => '物料ID'],
            [ 'attribute' => 'supplier_id','header' => '供应商'],
            [ 'attribute' => 'supplier_product_id','header' => '供应商物料ID'],
            [ 'attribute' => 'spec','header' => '规格'],
            [ 'attribute' => 'unit','header' => '单位'],
            [ 'attribute' => 'product_number','header' => '采购数量'],
            [ 'attribute' => 'buying_number','header' => '入库数量'],
            [ 'attribute' => 'material_type','header' => '物料类型'],
            [ 'attribute' => 'price','header' => '预定采购单价'],
            [ 'attribute' => 'purchase_price','header' => '实际采购单价'],
            [ 'attribute' => 'warehouse_id','header' => '存放库区'],
            [ 'attribute' => 'order_sn','header' => '订单号'],
            [ 'attribute' => 'create_time','header' => '创建时间'],
        ];
        return Utils::downloadExcel($datas, $columns, "采购入库报表");
    }
     
    /**
     * 物料出入报表
     */
    public function actionProduct(){
    	$department_id = Yii::$app->request->get('department_id');
    	$warehouse_id = Yii::$app->request->get('warehouse_id');
        $product_id = Yii::$app->request->get('product_id');
        $product_name = Yii::$app->request->get('product_name');
        $type = Yii::$app->request->get('type');
        $gateway_type = Yii::$app->request->get('gateway_type');
        $beginDate = Yii::$app->request->get('beginDate');
        $endDate = Yii::$app->request->get('endDate');
        $isDownload = Yii::$app->request->get('isDownload');
        $model = new WarehouseGateway();
        $query = WarehouseGateway::find();
        
//        if(!Admin::checkSupperFlowAdmin()){
//            $query->andWhere(["warehouse_id" => Admin::getWarehouseIdsById()]);
//        }
        if($department_id){
            $warehouse = Warehouse::getAllByStatus("", "", $department_id);
            $query->andWhere(['warehouse_id' => array_keys($warehouse)]);
        }
        if($warehouse_id){
            $query->andWhere(['warehouse_id' => $warehouse_id]);
        }
        if($product_id || $product_id==='0'){
            $query->andWhere(['product_id' => $product_id]);
        }
        if($type){
            $query->andWhere(['type' => $type]);
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
        if($gateway_type){
            $query->andWhere(['gateway_type' => $gateway_type]);
        }
        if($product_name  || $product_name==='0'){
            $productAll = Product::find()->where(['like','name',$product_name])->all();
            if($productAll) {
                $productIds = ArrayHelper::getColumn($productAll, 'id');
                $query->andWhere(['product_id'=> $productIds]);
            } else {
                $query->andWhere(['product_id'=> 0]);
            }
        }
        $query->orderBy('id desc');
        if($isDownload) {
            $all = $query->all();
            $this->downloadProduct($all);
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
        return $this->render('product', compact(['model', 'listDatas', 'listPages', 'message']));
    }
    
    /**
     * 导出物料出入库统计
     */
    private function downloadProduct($all) {
        $datas = [];    
        foreach ($all as $key => $val) {
            if ($val->product_type == WarehousePlanning::TYPE_EXCEPTION) {
                $product = WarehouseBuyingProduct::findOne($val->product_id);
            } else {
                $product = Product::findOne($val->product_id);
            }
            $gatewayModel = $val->getModelByGatewayType();
            $datas[] = [
                'id' => $key+1,
                'product_id' => $val->product_type == WarehousePlanning::TYPE_EXCEPTION ? 0 : $val->product_id,
                'name' => $product->name,
                'supplier_id' => Supplier::getNameById($product->supplier_id),
                'supplier_product_id' => $val->product_type == WarehousePlanning::TYPE_EXCEPTION ? 0 : $product->supplier_product_id,
                'batches' => $val->batches,
                'spec' => $product->spec,
                'unit' => $product->unit,
                'deparment_id' => Warehouse::getDepartmentNameByWarehouseId($val->warehouse_id),
                'warehouse_id' => Warehouse::getNameById($val->warehouse_id),
                'type' => $val->showType(),
                'stock' => $val->stock,
                'num' => $val->num,
                'material_type' => ProductCategory::getNameById($val->product_type == WarehousePlanning::TYPE_EXCEPTION ? $product->material_type : $product->product_category_id),
                'purchase_price' => $val->product_type == WarehousePlanning::TYPE_EXCEPTION ? $product->price : $product->purchase_price,
                'sale_price' => $val->product_type == WarehousePlanning::TYPE_EXCEPTION ? $product->purchase_price : $product->sale_price,
                'gateway_type' => $val->showGatewayType(),
                'sn' => $gatewayModel->sn,
                'operation_admin_id' => Admin::getNameById($val->gateway_type == $val::GATEWAY_TYPE_SALE ? $gatewayModel->create_admin_id : $gatewayModel->operation_admin_id),
                'create_time' => $gatewayModel->create_time,
            ];
        }
        $columns = [
            [ 'attribute' => 'id','header' => '序号'],
            [ 'attribute' => 'product_id','header' => '物料ID'],
            [ 'attribute' => 'name','header' => '物料名'],
            [ 'attribute' => 'supplier_id','header' => '供应商'],
            [ 'attribute' => 'supplier_product_id','header' => '供应商物料ID'],
            [ 'attribute' => 'batches','header' => '批次号'],
            [ 'attribute' => 'spec','header' => '规格'],
            [ 'attribute' => 'unit','header' => '单位'],
            [ 'attribute' => 'deparment_id','header' => '部门名称'],
            [ 'attribute' => 'warehouse_id','header' => '仓库名称'],
            [ 'attribute' => 'type','header' => '出入库类型'],
            [ 'attribute' => 'stock','header' => '当时库存'],
            [ 'attribute' => 'num','header' => '操作数量'],
            [ 'attribute' => 'material_type','header' => '物料类型'],
            [ 'attribute' => 'purchase_price','header' => '采购单价'],
            [ 'attribute' => 'sale_price','header' => '销售定价'],
            [ 'attribute' => 'gateway_type','header' => '表单名'],
            [ 'attribute' => 'sn','header' => '表单号'],
            [ 'attribute' => 'operation_admin_id','header' => '操作人'],
            [ 'attribute' => 'create_time','header' => '创建时间'],
        ];
        return Utils::downloadExcel($datas, $columns, "物料出入库统计");
    }

    /**
     * 供应商供货
     */
    public function actionSupply(){
    	$warehouse_id = Yii::$app->request->get("warehouse_id");
        $supplier_id = Yii::$app->request->get("supplier_id");
        $beginDate = Yii::$app->request->get("beginDate");
        $endDate = Yii::$app->request->get("endDate");
        $department_id = Yii::$app->request->get("department_id");
        $isDownload = Yii::$app->request->get("isDownload");
        $buyingQuery = WarehouseBuying::find();
        $query = WarehouseBuyingProduct::find();
//        if(!Admin::checkSupperFlowAdmin()){
//            $query->andWhere(["warehouse_id" => Admin::getWarehouseIdsById()]);
//        }
        if(is_numeric($warehouse_id)){
            $buyingQuery->andWhere(["warehouse_id" => $warehouse_id]);
        }
        if(is_numeric($supplier_id)){
            $buyingQuery->andWhere(["supplier_id" => $supplier_id]);
            $query->andWhere(["supplier_id" => $supplier_id]);
        }
        if(is_numeric($department_id)) {
            $warehouseAll = Warehouse::findAll(["department_id" => $department_id]);
            if($warehouseAll) {
                $warehouseIds = ArrayHelper::getColumn($warehouseAll, "id");
                $buyingQuery->andWhere(["warehouse_id" => $warehouseIds]);
            } else {
                $buyingQuery->andFilterCompare(["=","warehouse_id" , 0]);
            }
        }
        $message = "";
        if($beginDate && $endDate && strtotime($beginDate) > strtotime($endDate)) {
            $message = "开始时间不能大于结束时间";
        }
        if($beginDate){
            $buyingQuery->andFilterCompare('create_time', $beginDate." 00:00:00", '>=');
        }
        if($endDate){
            $buyingQuery->andFilterCompare('create_time', $endDate." 23:59:59",  '<=');
        }
        $buyingQuery->andWhere(["status" => Flow::STATUS_FINISH]);
        $buyingAll = $buyingQuery->all();
        if($buyingAll) {
            $buyingId = ArrayHelper::getColumn($buyingAll, 'id');
            $query->andWhere(['buying_id' => $buyingId]);
        } else {
            $query->andWhere(["buying_id" => 0]);
        }
        $query->orderBy('id desc');
        if($isDownload) {
            $this->downloadSupply($query);
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
        return $this->render('supply', compact(['listDatas', 'listPages', 'message']));
    }
    
    /**
     * 导出供应商结算报表
     */
    private function downloadSupply($query) {
        $all = $query->all();
        $datas = [];    
        foreach ($all as $key => $val) {
            $buyingItem = WarehouseBuying::findOne($val->buying_id);
            $datas[] = [
                'id' => $key+1,
                'name' => $val->name,
                'product_id' => $val->product_id,
                'supplier_id' => Supplier::getNameById($val->supplier_id),
                'supplier_product_id' => $val->supplier_product_id,
                'operation_time' => $buyingItem->sn,
                'spec' => $val->spec,
                'unit' => $val->unit,
                'product_number' => $val->product_number,
                'buying_number' => $val->buying_number,
                'material_type' => ProductCategory::getNameById($data->material_type),
                'price' => number_format($val->price, 2),
                'warehouse_id' => Warehouse::getNameById($val->warehouse_id),
                'order_sn' => $buyingItem->order_sn,
                'create_time' => $buyingItem->create_time,
            ];
        }
        $columns = [
            [ 'attribute' => 'id','header' => '序号'],
            [ 'attribute' => 'name','header' => '物料名'],
            [ 'attribute' => 'product_id','header' => '物料ID'],
            [ 'attribute' => 'supplier_id','header' => '供应商'],
            [ 'attribute' => 'supplier_product_id','header' => '供应商物料ID'],
            [ 'attribute' => 'operation_time','header' => '采购批次号'],
            [ 'attribute' => 'spec','header' => '规格'],
            [ 'attribute' => 'unit','header' => '单位'],
            [ 'attribute' => 'product_number','header' => '采购数量'],
            [ 'attribute' => 'buying_number','header' => '入库数量'],
            [ 'attribute' => 'material_type','header' => '物料类型'],
            [ 'attribute' => 'price','header' => '采购单价'],
            [ 'attribute' => 'warehouse_id','header' => '存放库区'],
            [ 'attribute' => 'order_sn','header' => '订单号'],
            [ 'attribute' => 'create_time','header' => '创建时间'],
        ];
        return Utils::downloadExcel($datas, $columns, "供应商供货报表");
    }

    /**
     * 供应商结算
     */
    public function actionSettlement() {
        /***
         * SELECT supplier_id,
SUM(IF(pay_state = 0, total_amount, 0)) as noPaySum,
SUM(IF(pay_state = 1, deposit, 0)) as payDeposit,
SUM(
	IF(pay_state = 2, 
		IF(pay_all_time > "2016-09-12" AND pay_all_time <= "2016-10-12 23:59:59",
			IF(payment in (1,2), 
				IF(pay_deposit_time > "2016-09-12" AND pay_deposit_time <= "2016-10-12 23:59:59", total_amount, total_amount-deposit), 
				total_amount), 
			0), 
		0)
) as payAll
FROM `OrderProcurement` GROUP BY supplier_id;
         */
        
        /*
        $department_id = Yii::$app->request->get('department_id');
        $supplier_id = Yii::$app->request->get('supplier_id');
        $beginDate = Yii::$app->request->get('beginDate');
        $endDate = Yii::$app->request->get('endDate');
        $model = new BusinessAll();
        $query = BusinessAll::find();
        $query->andWhere(["status"=> Flow::STATUS_FINISH]);
        if(!Admin::checkSupperAdmin()){
            $query->andWhere(['department_id' => Admin::getDepId()]);
        }
        $query->andWhere(["business_type" => [Flow::TYPE_BUYING, Flow::TYPE_MATERIALRETURN]]);
        if(is_numeric($department_id)) {
            $query->andWhere(["department_id"=> $department_id]);
        }
        if(is_numeric($supplier_id)) {
            $buyingAll = WarehouseBuying::findAll(["supplier_id" => $supplier_id]);
            if($buyingAll) {
                $buyingIds = ArrayHelper::getColumn($buyingAll, "id");
                $query->andWhere(["business_type" => Flow::TYPE_BUYING, "business_id" => $buyingIds]);
            } else {
                $query->andWhere(["business_type" => Flow::TYPE_BUYING, "business_id" => 0]);
            }
            $materialReturnAll = WarehouseMaterialReturn::findAll(["supplier_id" => $supplier_id]);
            if($materialReturnAll) {
                $materialReturnIds = ArrayHelper::getColumn($materialReturnAll, "id");
                $query->orWhere(["business_type" => Flow::TYPE_MATERIALRETURN, "business_id" => $materialReturnIds]);
            } else {
                $query->orWhere(["business_type" => Flow::TYPE_MATERIALRETURN, "business_id" => 0]);
            }
//            dump($query->createCommand()->getRawSql());
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
        //历史表单数据
        $query->orderBy('id desc');
        $isDownload = Yii::$app->request->get('isDownload');
        if($isDownload) {
            $this->downloadSettlement($query);
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
        */
        $department_id = Yii::$app->request->get('department_id');
        $supplier_id = Yii::$app->request->get('supplier_id');
        $beginDate = Yii::$app->request->get('beginDate');
        if(!$beginDate) {
            $beginDate = date("Y-m-d",strtotime("-1 month"));
        }
        $endDate = Yii::$app->request->get('endDate');
        if(!$endDate) {
            $endDate = date("Y-m-d");
        }
        $message = "";
        if($beginDate && $endDate && strtotime($beginDate) > strtotime($endDate)) {
            $message = "开始时间不能大于结束时间";
        }
        $expenSql = "SELECT supplier_id, warehouse_id, department_id,SUM(IF(pay_state = 0, total_amount, 0)) as noPaySum, SUM(IF(pay_state = 1, deposit, 0)) as payDeposit,";
        $expenSql .= " SUM(IF(pay_state = 2, IF(pay_all_time > '{$beginDate}' AND pay_all_time <= '{$endDate} 23:59:59', IF(payment in (1,2),";
        $expenSql .= " IF(pay_deposit_time > '{$beginDate}' AND pay_deposit_time <= '{$endDate} 23:59:59', total_amount, total_amount-deposit),total_amount),0), 0)) as payAll";
        $expenSql .= " FROM `OrderProcurement` WHERE  pay_all_time > '{$beginDate}' AND pay_all_time <= '{$endDate} 23:59:59'";
//        if(!Admin::checkSupperFlowAdmin()){
//            $departmentId = Admin::getDepId();
//            $expenSql .= " AND department_id = {$departmentId}";
//        }
        if($department_id) {
            $expenSql .= " AND department_id = {$department_id}";
        }
        if($supplier_id) {
            $expenSql .= " AND supplier_id = {$supplier_id}";
        }
        $expenSql .= " GROUP BY supplier_id, warehouse_id, department_id;";
        $expenAll = OrderProcurement::findBySql($expenSql)->all();
        $listDatas = [];
        if($expenAll) {
            foreach ($expenAll as $expenVal) {
                $listDatas[$expenVal->warehouse_id][$expenVal->supplier_id] = [
                        "department_id" => $expenVal->department_id,
                        "warehouse_id" => $expenVal->warehouse_id,
                        "supplier_id" => $expenVal->supplier_id,
                        "paySum" => $expenVal->payDeposit + $expenVal->payAll,
                        "noPaySum" => $expenVal->noPaySum,
                        "noReceipt" => 0,
                        "receipt" => 0,
                ];
            }
        }
        $receiptSql = "SELECT supplier_id, warehouse_id, department_id,SUM(IF(pay_state = 0, total_amount, 0)) as noReceipt,SUM(IF(pay_state = 2, total_amount, 0)) as receipt";
        $receiptSql .= " FROM `OrderMaterialReturn` WHERE pay_all_time > '{$beginDate}' AND pay_all_time <= '{$endDate} 23:59:59'";
//        if(!Admin::checkSupperFlowAdmin()){
//            $departmentId = Admin::getDepId();
//            $receiptSql .= " AND department_id = {$departmentId}";
//        }
        if($department_id) {
            $receiptSql .= " AND department_id = {$department_id}";
        }
        if($supplier_id) {
            $receiptSql .= " AND supplier_id = {$supplier_id}";
        }
        $receiptSql.= " GROUP BY supplier_id, warehouse_id, department_id;";
        $receiptAll = OrderMaterialReturn::findBySql($receiptSql)->all();
        if($receiptAll) {
            foreach ($receiptAll as $receiptVal) {
                if(isset($listDatas[$receiptVal->warehouse_id]) && isset($listDatas[$receiptVal->warehouse_id][$receiptVal->supplier_id])) {
                    $listDatas[$receiptVal->warehouse_id][$receiptVal->supplier_id]["noReceipt"] = $receiptVal->noReceipt;
                    $listDatas[$receiptVal->warehouse_id][$receiptVal->supplier_id]["receipt"] = $receiptVal->receipt;
                } else {
                    $listDatas[$receiptVal->warehouse_id][$receiptVal->supplier_id] = [
                        "department_id" => $expenVal->department_id,
                        "warehouse_id" => $expenVal->warehouse_id,
                        "supplier_id" => $expenVal->supplier_id,
                        "paySum" => 0,
                        "noPaySum" => 0,
                        "noReceipt" => $receiptVal->noReceipt,
                        "receipt" => $receiptVal->receipt,
                    ];
                }
            }
        }
        $isDownload = Yii::$app->request->get('isDownload');
        if($isDownload) {
            $this->downloadSettlement($listDatas);
        }
        return $this->render('settlement', compact(['listDatas','message', 'beginDate', 'endDate']));
    }
    
    /**
     * 导出供应商结算报表
     */
    private function downloadSettlement($listDatas) {
        $datas = [];    
        $i = 1;
        foreach ($listDatas as $listVal) {
            foreach ($listVal as $val) {
                $datas[] = [
                    'id' => $i,
                    'department_id' => Department::getNameById($val["department_id"]),
                    'warehouse_id' => Warehouse::getNameById($val["warehouse_id"]),
                    'supplierName' => Supplier::getNameById($val["supplier_id"]),
                    'supplier_id' => $val["supplier_id"],
                    "paySum" => $val["paySum"],
                    "receipt" => $val["receipt"],
                    "noPaySum" => $val["noPaySum"],
                    "noReceipt" => $val["noReceipt"],
                ];
                $i++;
            }
        }
        $columns = [
            [ 'attribute' => 'id','header' => '序号'],
            [ 'attribute' => 'department_id','header' => '部门名称'],
            [ 'attribute' => 'warehouse_id','header' => '仓库名称'],
            [ 'attribute' => 'supplierName','header' => '供应商'],
            [ 'attribute' => 'supplier_id','header' => '供应商ID'],
            [ 'attribute' => 'paySum','header' => '已付金额'],
            [ 'attribute' => 'receipt','header' => '已收金额'],
            [ 'attribute' => 'noPaySum','header' => '待付金额'],
            [ 'attribute' => 'noReceipt','header' => '待收金额'],
        ];
        return Utils::downloadExcel($datas, $columns, "供应商结算报表");
    }
    
    /**
     * 毛利
     */
    public function actionGrossprofit() {
        /*
        $department_id = Yii::$app->request->get('department_id');
        $beginDate = Yii::$app->request->get('beginDate');
        $endDate = Yii::$app->request->get('endDate');
        $query = WarehouseSale::find();
        $query->andWhere(["status" => Flow::STATUS_FINISH]);
        if(!Admin::checkSupperAdmin()){
            $query->andWhere(['department_id' => Admin::getDepId()]);
        }
        if(is_numeric($department_id)) {
            $query->andWhere(['department_id' => $department_id]);
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
        $all = $query->all();
        $listDatas = [];
        if($all) {
            $list = ArrayHelper::map($all, "id", "id", "department_id");
            $i = 1;
            foreach ($list as $departmentId => $val) {
                $listDatas[$i]["key"] = $i;
                $listDatas[$i]["departmentName"] = Department::getNameById($departmentId);
                $productAll = WarehouseSaleProduct::find()->where(["sale_id" => $val])->all();
                $listDatas[$i]["income"] = $listDatas[$i]["expend"] = 0;
                foreach ($productAll as $productVal) {
                    $listDatas[$i]["income"] += $productVal->sale_price * $productVal->buying_number;
                    $listDatas[$i]["expend"] += $productVal->purchase_price * $productVal->buying_number;
                }
                $listDatas[$i]["profit"] = $listDatas[$i]["income"] - $listDatas[$i]["expend"];
                $i++;
            }
        } */
        $department_id = Yii::$app->request->get('department_id');
        $beginDate = Yii::$app->request->get('beginDate');
        $endDate = Yii::$app->request->get('endDate');
        $timeType = Yii::$app->request->get('timeType');
        if(!is_numeric($timeType)) {
            $timeType = 1;
        }
        if($timeType == 1) {
            $timeWhere = 'DATE_FORMAT( create_time, "%Y-%m-%d" ) as time, DATE_FORMAT( create_time, "%Y" ) as `year`';
        } else if($timeType == 2) {
            $timeWhere = 'DATE_FORMAT( create_time, "%Y-%m" ) as time, DATE_FORMAT( create_time, "%Y" ) as `year`';
        } else if($timeType == 3) {
            $timeWhere = 'quarter(create_time) as time, DATE_FORMAT( create_time, "%Y" ) as `year`';
        }
        $saleSql = 'SELECT '.$timeWhere.',s.department_id,SUM(sp.buying_number * sp.purchase_price) as `expen` ,SUM(sp.buying_number * sp.sale_price) as `income`';
        $saleSql .= ' FROM WarehouseSale as s, WarehouseSaleProduct as sp WHERE s.id = sp.sale_id AND s.status = ' . Flow::STATUS_FINISH;
        $wastageSql = 'SELECT '.$timeWhere.',w.department_id,SUM(wp.buying_number * wp.purchase_price) as `wastage`';
        $wastageSql .= ' FROM WarehouseWastage as w, WarehouseWastageProduct as wp WHERE w.id = wp.wastage_id AND w.status = ' . Flow::STATUS_FINISH;
        $otherIncomeSql = 'SELECT '.$timeWhere.',income_department_id as department_id,SUM(if(`mod` = 1 , current_balance, 0)) as `income`,SUM(if(`mod` = 2 , current_balance, 0)) as `other`';
        $otherIncomeSql .= ' FROM AbnormalBalance WHERE `status` = ' . Flow::STATUS_FINISH . ' AND `mod` IN ('. implode(",", AbnormalBalance::findOtherIncomeMod()).')';
        $otherExpenSql = 'SELECT '.$timeWhere.',department_id,SUM(if(`mod` = 1 , current_balance, 0)) as `expen`,SUM(if(`mod` = 3 , current_balance, 0)) as `other`';
        $otherExpenSql .= ' FROM AbnormalBalance WHERE `status` = ' . Flow::STATUS_FINISH . ' AND `mod` IN ('. implode(",", AbnormalBalance::findOtherExpenMod()).')';
//        if(!Admin::checkSupperFlowAdmin()){
//            $saleSql .= " AND s.department_id = " . Admin::getDepId();
//        }
//        if(!Admin::checkSupperFlowAdmin()){
//            $wastageSql .= " AND w.department_id = " . Admin::getDepId();
//        }
//        if(!Admin::checkSupperFlowAdmin()){
//            $otherIncomeSql .= " AND income_department_id = " . Admin::getDepId();
//        }
//        if(!Admin::checkSupperFlowAdmin()){
//            $otherExpenSql .= " AND department_id = " . Admin::getDepId();
//        }
        if(is_numeric($department_id)) {
            $saleSql .= " AND s.department_id = " . $department_id;
            $wastageSql .= " AND w.department_id = " . $department_id;
            $otherIncomeSql .= " AND income_department_id = " . $department_id;
            $otherExpenSql .= " AND department_id = " . $department_id;
        }
        if($beginDate){
            $saleSql .= " AND s.create_time >= '" . $beginDate." 00:00:00'";
            $wastageSql .= " AND w.create_time >= '" . $beginDate." 00:00:00'";
            $otherIncomeSql .= " AND create_time >= '" . $beginDate." 00:00:00'";
            $otherExpenSql .= " AND create_time >= '" . $beginDate." 00:00:00'";
        }
        if($endDate){
            $saleSql .= " AND s.create_time <= '" . $endDate." 23:59:59'";
            $wastageSql .= " AND w.create_time <= '" . $endDate." 23:59:59'";
            $otherIncomeSql .= " AND create_time <= '" . $endDate." 23:59:59'";
            $otherExpenSql .= " AND create_time <= '" . $endDate." 23:59:59'";
        }
        $saleSql .= " GROUP BY department_id,time,`year` ORDER BY time,`year` DESC";
        $wastageSql .= " GROUP BY department_id,time,`year` ORDER BY time,`year` DESC";
        $otherIncomeSql .= " GROUP BY income_department_id,time,`year` ORDER BY time,`year` DESC";
        $otherExpenSql .= " GROUP BY department_id,time,`year` ORDER BY time,`year` DESC";
        $message = "";
        if($beginDate && $endDate && strtotime($beginDate) > strtotime($endDate)) {
            $message = "开始时间不能大于结束时间";
        }
        $saleAll = WarehouseSale::findBySql($saleSql)->all();
        $wastageAll = WarehouseWastage::findBySql($wastageSql)->all();
        $otherIncomeAll = AbnormalBalance::findBySql($otherIncomeSql)->all();
        $otherExpenAll = AbnormalBalance::findBySql($otherExpenSql)->all();
        $integrateDatas = [];
        foreach ($saleAll as $saleVal) {
            $integrateDatas[$saleVal->year][$saleVal->time][$saleVal->department_id] = [
                'income' => $saleVal->income,
                'expen' => $saleVal->expen, 
            ];
        }
        foreach ($wastageAll as $wastageVal) {
            $integrateDatas[$wastageVal->year][$wastageVal->time][$wastageVal->department_id]['wastage'] = $wastageVal->wastage;
        }
        foreach ($otherIncomeAll as $otherIncomeVal) {
            $integrateDatas[$otherIncomeVal->year][$otherIncomeVal->time][$otherIncomeVal->department_id]['otherIncome'] = $otherIncomeVal->income - $otherIncomeVal->other;
        }
        foreach ($otherExpenAll as $otherExpenVal) {
            $integrateDatas[$otherExpenVal->year][$otherExpenVal->time][$otherExpenVal->department_id]['otherExpen'] = $otherExpenVal->expen - $otherExpenVal->other;
        }
        
        $listDatas = [];
        $quarter = ["一", "二", "三", "四"];
        $i = 1;
        foreach ($integrateDatas as $year => $vals) {
            foreach ($vals as $time => $val) {
                foreach ($val as $departmentId => $list) {
                    $income = isset($list["income"]) ? $list["income"] : 0;
                    $expen = isset($list["expen"]) ? $list["expen"] : 0;
                    $wastage = isset($list["wastage"]) ? $list["wastage"] : 0;
                    $otherIncome = isset($list["otherIncome"]) ? $list["otherIncome"] : 0;
                    $otherExpen = isset($list["otherExpen"]) ? $list["otherExpen"] : 0;
                    $listDatas[$i] = [
                        'key' => $i,
                        'time' => $timeType == 3 ? $year."-第".$quarter[$time - 1]."季度" : $time,
                        'departmentName' => Department::getNameById($departmentId),
                        'income' => $income,
                        'expen' => $expen,
                        'wastage' => $wastage,
                        'otherIncome' => $otherIncome,
                        'otherExpen' => $otherExpen,
                        'profit' => ($income + $otherIncome - $expen - $wastage - $otherExpen) / (($income - $otherExpen) != 0 ? ($income - $otherExpen) : 1),
                    ];
                    $i++;
                }
            }
        }
        $isDownload = Yii::$app->request->get('isDownload');
        if($isDownload) {
            $this->downloadGrossprofit($listDatas);
        }
        return $this->render('grossprofit', compact(['listDatas','message']));
    }
    
    /**
     * 导出部门毛利记录
     * @param type $listDatas
     * @return type
     */
    private function downloadGrossprofit($listDatas) {
        $columns = [
            [ 'attribute' => 'key','header' => '序号'],
            [ 'attribute' => 'time','header' => '时间'],
            [ 'attribute' => 'departmentName','header' => '部门名称'],
            [ 'attribute' => 'income','header' => '销售总金额'],
            [ 'attribute' => 'expen','header' => '对应销售总成本'],
            [ 'attribute' => 'wastage','header' => '损耗成本'],
            [ 'attribute' => 'otherIncome','header' => '其他收入'],
            [ 'attribute' => 'otherExpen','header' => '其他支出'],
            [ 'attribute' => 'profit','header' => '毛利率'],
        ];
        return Utils::downloadExcel($listDatas, $columns, "部门毛利记录");
    }
    
    /**
     * 利润
     */
    public function actionProfit()
    {
        /*
        $department_id = Yii::$app->request->get('department_id');
        $beginDate = Yii::$app->request->get('beginDate');
        $endDate = Yii::$app->request->get('endDate');
        $query = WarehouseSale::find();
        $query->andWhere(["status" => Flow::STATUS_FINISH]);
        $aBalanceQuery = AbnormalBalance::find();
        $aBalanceQuery->andWhere(["status" => Flow::STATUS_FINISH]);
        if(!Admin::checkSupperAdmin()){
            $query->andWhere(['department_id' => Admin::getDepId()]);
            $aBalanceQuery->andWhere(["department_id" => Admin::getDepId()]);
        }
        if(is_numeric($department_id)) {
            $query->andWhere(['department_id' => $department_id]);
            $aBalanceQuery->andWhere(['department_id' => $department_id]);
        }
        if($beginDate){
            $query->andFilterCompare('create_time', $beginDate." 00:00:00", '>=');
            $aBalanceQuery->andFilterCompare('create_time', $beginDate." 00:00:00", '>=');
        }
        if($endDate){
            $query->andFilterCompare('create_time', $endDate." 23:59:59",  '<=');
            $aBalanceQuery->andFilterCompare('create_time', $endDate." 23:59:59",  '<=');
        }
        $message = "";
        if($beginDate && $endDate && strtotime($beginDate) > strtotime($endDate)) {
            $message = "开始时间不能大于结束时间";
        }
        $all = $query->all();
        $aBalanceAll = $aBalanceQuery->all();
        $aBalanceList = [];
        if($aBalanceAll) {
            foreach ($aBalanceAll as $aBalanceVal) {
                $aBalanceList[$aBalanceVal->department_id] = isset($aBalanceList[$aBalanceVal->department_id]) ? $aBalanceList[$aBalanceVal->department_id] : ["income" => 0, "expend" => 0];
                if($aBalanceVal->mod == AbnormalBalance::MOD_IN) {
                    $aBalanceList[$aBalanceVal->department_id]["income"] += $aBalanceVal->balance;
                } else {
                    $aBalanceList[$aBalanceVal->department_id]["expend"] += $aBalanceVal->balance;
                }
            }
        }
        $listDatas = [];
        if($all) {
            $list = ArrayHelper::map($all, "id", "id", "department_id");
            $i = 1;
            foreach ($list as $departmentId => $val) {
                $listDatas[$i]["key"] = $i;
                $listDatas[$i]["departmentName"] = Department::getNameById($departmentId);
                $productAll = WarehouseSaleProduct::find()->where(["sale_id" => $val])->all();
                $listDatas[$i]["income"] = $listDatas[$i]["expend"] = 0;
                foreach ($productAll as $productVal) {
                    $listDatas[$i]["income"] += $productVal->sale_price * $productVal->buying_number;
                    $listDatas[$i]["expend"] += $productVal->purchase_price * $productVal->buying_number;
                }
                $listDatas[$i]["abnormalIncome"] = isset($aBalanceList[$departmentId]) ? $aBalanceList[$departmentId]["income"] : 0;
                $listDatas[$i]["abnormalExpend"] = isset($aBalanceList[$departmentId]) ? $aBalanceList[$departmentId]["expend"] : 0;
                $listDatas[$i]["profit"] = $listDatas[$i]["income"] + $listDatas[$i]["abnormalIncome"] - $listDatas[$i]["expend"] - $listDatas[$i]["abnormalExpend"];
                $i++;
            }
            foreach ($aBalanceList as $departmentId => $val) {
                if(isset($list[$departmentId])){
                    continue;
                }
                $listDatas[$i]["key"] = $i;
                $listDatas[$i]["departmentName"] = Department::getNameById($departmentId);
                $listDatas[$i]["income"] = $listDatas[$i]["expend"] = 0;
                $listDatas[$i]["abnormalIncome"] = $val["income"];
                $listDatas[$i]["abnormalExpend"] = $val["expend"];
                $listDatas[$i]["profit"] = $listDatas[$i]["income"] + $listDatas[$i]["abnormalIncome"] - $listDatas[$i]["expend"] - $listDatas[$i]["abnormalExpend"];
                $i++;
            }
        } else {
            $i = 1;
            foreach ($aBalanceList as $departmentId => $val) {
                $listDatas[$i]["key"] = $i;
                $listDatas[$i]["departmentName"] = Department::getNameById($departmentId);
                $listDatas[$i]["income"] = $listDatas[$i]["expend"] = 0;
                $listDatas[$i]["abnormalIncome"] = $val["income"];
                $listDatas[$i]["abnormalExpend"] = $val["expend"];
                $listDatas[$i]["profit"] = $listDatas[$i]["income"] + $listDatas[$i]["abnormalIncome"] - $listDatas[$i]["expend"] - $listDatas[$i]["abnormalExpend"];
                $i++;
            }
        } */
        $department_id = Yii::$app->request->get('department_id');
        $beginDate = Yii::$app->request->get('beginDate');
        $endDate = Yii::$app->request->get('endDate');
        $timeType = Yii::$app->request->get('timeType');
        if(!is_numeric($timeType)) {
            $timeType = 1;
        }
        if($timeType == 1) {
            $timeWhere = 'DATE_FORMAT( create_time, "%Y-%m-%d" ) as time, DATE_FORMAT( create_time, "%Y" ) as `year`';
        } else if($timeType == 2) {
            $timeWhere = 'DATE_FORMAT( create_time, "%Y-%m" ) as time, DATE_FORMAT( create_time, "%Y" ) as `year`';
        } else if($timeType == 3) {
            $timeWhere = 'quarter(create_time) as time, DATE_FORMAT( create_time, "%Y" ) as `year`';
        }
        $abnoSql = 'SELECT '.$timeWhere.',department_id,SUM(IF(`mod` = 1,balance,0)) as income,SUM(IF(`mod` = 2,balance,0)) as expen FROM DepartmentBalanceLog';
        $abnoSql .= ' WHERE status =  ' . Flow::STATUS_FINISH . " AND business_type in (". DepartmentBalanceLog::BUSINESS_TYPE_MATERIAL_RETURN.",".DepartmentBalanceLog::BUSINESS_TYPE_WASTAGE.")";
        $saleSql = 'SELECT '.$timeWhere.',s.department_id,SUM(sp.buying_number * sp.purchase_price) as `expen` ,SUM(sp.buying_number * sp.sale_price) as `income`';
        $saleSql .= 'FROM WarehouseSale as s, WarehouseSaleProduct as sp WHERE s.id = sp.sale_id ';
//        if(!Admin::checkSupperFlowAdmin()){
//            $abnoSql .= " AND department_id = " . Admin::getDepId();
//            $saleSql .= " AND s.department_id = " . Admin::getDepId();
//        }
        if(is_numeric($department_id)) {
            $abnoSql .= " AND department_id = " . $department_id;
            $saleSql .= " AND s.department_id = " . $department_id;
        }
        if($beginDate){
            $abnoSql .= " AND create_time >= '" . $beginDate." 00:00:00'";
            $saleSql .= " AND s.create_time >= '" . $beginDate." 00:00:00'";
        }
        if($endDate){
            $abnoSql .= " AND create_time <= '" . $endDate." 23:59:59'";
            $saleSql .= " AND s.create_time <= '" . $endDate." 23:59:59'";
        }
        $abnoSql .= " GROUP BY department_id,time,`year` ORDER BY time,`year` DESC";
        $saleSql .= " GROUP BY department_id,time,`year` ORDER BY time,`year` DESC";
        $message = "";
        if($beginDate && $endDate && strtotime($beginDate) > strtotime($endDate)) {
            $message = "开始时间不能大于结束时间";
        }
        $ableAll = AbnormalBalance::findBySql($abnoSql)->all();
        $ableList = [];
        if($ableAll) {
            foreach ($ableAll as $ableVal) {
                $ableList[$ableVal->department_id][$ableVal->year][$ableVal->time]["abnormalIncome"] = $ableVal->income;
                $ableList[$ableVal->department_id][$ableVal->year][$ableVal->time]["abnormalExpend"] = $ableVal->expen;
            }
        }
        $saleAll = WarehouseSale::findBySql($saleSql)->all();
        $quarter = ["一", "二", "三", "四"];
        $listDatas = [];
        if($saleAll) {
            $i = 1;
            $saleList = [];
            foreach ($saleAll as $saleVal) {
                $abnormalIncome = $abnormalExpend = 0;
                if(isset($ableList[$saleVal->department_id]) && isset($ableList[$saleVal->department_id][$saleVal->year][$saleVal->time]["abnormalIncome"])) {
                    $abnormalIncome = $ableList[$saleVal->department_id][$saleVal->year][$saleVal->time]["income"];
                }
                if(isset($ableList[$saleVal->department_id]) && isset($ableList[$saleVal->department_id][$saleVal->year][$saleVal->time]["abnormalExpend"])) {
                    $abnormalExpend = $ableList[$saleVal->department_id][$saleVal->year][$saleVal->time]["expen"];
                }
                $saleList[$saleVal->department_id][$saleVal->year][$saleVal->time] = true;
                $listDatas[$i] = array(
                    'key' => $i,
                    'year' => $saleVal->year,
                    'search' => $saleVal->time,
                    'timeType' => $timeType,
                    'time' => $timeType == 3 ? $saleVal->year."-第".$quarter[$saleVal->time - 1]."季度" : $saleVal->time,
                    'departmentName' => Department::getNameById($saleVal->department_id),
                    'income' => $saleVal->income,
                    'expen' => $saleVal->expen,
                    'profit' => $saleVal->income + $abnormalIncome - $saleVal->expen - $abnormalExpend,
                );
                $i++;
            }
            foreach ($ableList as $departmentId => $ableVal) {
                foreach ($ableVal as $year => $value) {
                    foreach ($value as $time => $val) {
                        if(isset($saleList[$departmentId]) && isset($saleList[$departmentId][$year]) && isset($saleList[$departmentId][$time])){
                            continue;
                        }
                        $listDatas[$i]["key"] = $i;
                        $listDatas[$i]["year"] = $year;
                        $listDatas[$i]["search"] = $time;
                        $listDatas[$i]["timeType"] = $timeType;
                        $listDatas[$i]["time"] = $timeType == 3 ? $year."-第".$quarter[$time - 1]."季度" : $time;
                        $listDatas[$i]["departmentName"] = Department::getNameById($departmentId);
                        $listDatas[$i]["income"] = $val["abnormalIncome"];
                        $listDatas[$i]["expen"] =  $val["abnormalExpend"];
                        $listDatas[$i]["profit"] = $listDatas[$i]["income"] - $listDatas[$i]["expen"];
                        $i++;
                    }
                }
            }
        } else {
            $i = 1;
            foreach ($ableList as $departmentId => $ableVal) {
                foreach ($ableVal as $year => $value) {
                    foreach ($value as $time => $val) {
                        $listDatas[$i]["key"] = $i;
                        $listDatas[$i]["year"] = $year;
                        $listDatas[$i]["search"] = $time;
                        $listDatas[$i]["timeType"] = $timeType;
                        $listDatas[$i]["time"] = $timeType == 3 ? $year."-第".$quarter[$time - 1]."季度" : $time;
                        $listDatas[$i]["departmentName"] = Department::getNameById($departmentId);
                        $listDatas[$i]["income"] = $val["abnormalIncome"];
                        $listDatas[$i]["expen"] =  $val["abnormalExpend"];
                        $listDatas[$i]["profit"] = $listDatas[$i]["income"] - $listDatas[$i]["expen"];
                        $i++;
                    }
                }
            }
        }
        usort( $listDatas, function( $a, $b ) {
            $search = ($a['timeType'] == 3 ? $b['search'] - $a['search'] : strtotime($b['search']) - strtotime($a['search']));
            return $a['year'] == $b['year'] ? $search : $b['year'] - $a['year'] && $search;
        });
        
        $isDownload = Yii::$app->request->get('isDownload');
        if($isDownload) {
            $this->downloadProfit($listDatas);
        }
        return $this->render('profit', compact(['listDatas','message','timeType']));
    }
    
    /**
     * 导出部门利润记录
     * @param type $listDatas
     * @return type
     */
    private function downloadProfit($listDatas) {
        $i = 1;
        foreach ($listDatas as &$val) {
            $val['key'] = $i;
            $i++;
        }
        $columns = [
            [ 'attribute' => 'key','header' => '序号'],
            [ 'attribute' => 'time','header' => '时间'],
            [ 'attribute' => 'departmentName','header' => '部门名称'],
            [ 'attribute' => 'income','header' => '总收入'],
            [ 'attribute' => 'expen','header' => '总支付'],
            [ 'attribute' => 'profit','header' => '利润'],
        ];
        return Utils::downloadExcel($listDatas, $columns, "部门利润记录");
    }
}
