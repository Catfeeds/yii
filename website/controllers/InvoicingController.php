<?php

namespace app_web\controllers;

use common\models\Product;
use common\models\ProductStock;
use common\models\BusinessAll;
use common\models\Admin;
use common\models\Department;
use common\models\DepartmentBalance;
use common\models\DepartmentCheck;
use common\models\DepartmentCheckProduct;
use common\models\DepartmentCheckAmount;
use common\models\WarehouseCheck;
use common\models\WarehouseAmountCheck;
use common\models\Warehouse;
use common\models\ProductInvoicingSale;
use common\models\ProductInvoicingSaleInfo;

use libs\Utils;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\Json;
use app_web\components\CController;
use yii\helpers\ArrayHelper;
use libs\common\Flow;

/**
 * 销存管理 -- 实时销存管理
 */
class InvoicingController extends CController {

    /**
     * 实时库存管理
     */
    public function actionRealtime() {
        $keyword = Yii::$app->request->get('keyword');
        $departmentId = Yii::$app->request->get("department_id");
        $model = new ProductInvoicingSale();
        $query = ProductInvoicingSale::find();
        $query->andWhere(["status" => ProductInvoicingSale::STATUS_NO_SALE]);
//        if(!Admin::checkSupperFlowAdmin()){
//            $query->andWhere(['department_id' => Admin::getDepId()]);
//        }
        if (is_numeric($departmentId)) {
            $query->andWhere(['department_id' => $departmentId]);
        }
        $saleAll = $query->all();
        $saleIds = ArrayHelper::getColumn($saleAll, "id");
        $saleAll = ArrayHelper::index($saleAll, "id");
        $infoQuery = ProductInvoicingSaleInfo::find();
        $infoQuery->andWhere(["invoicing_sale_id" => $saleIds]);
        if (is_numeric($keyword)) {
            $infoQuery->andWhere(['product_id' => $keyword]);
        } else if ($keyword != "") {
            $infoQuery->andWhere(['like', 'name', $keyword]);
        }
        $infoQuery->orderBy('id desc');
        $dataProvider = new ActiveDataProvider([
            'query' => $infoQuery,
            'pagination' => [
                'pageSize' => 20,
                'validatePage' => false,
            ],
        ]);
        $listDatas = $dataProvider->getModels();
        $listPages = $dataProvider->getPagination();
        return $this->render('realtime', compact(['model', 'saleAll', 'listDatas', 'listPages']));
    }

    /**
     * 销存商品管理
     */
    public function actionProduct() {
        //包含 订单入库 出库申请 退仓申请 转货申请 掉仓库申请 物料耗损 库存盘点申请
        $department_id = Yii::$app->request->get('department_id');
        $warehouse_id = Yii::$app->request->get('warehouse_id');
        $sn = Yii::$app->request->get('sn');
        $business_type = Yii::$app->request->get('business_type');
        $model = new BusinessAll();
        $query = BusinessAll::find();
//        if(!Admin::checkSupperFlowAdmin()){
//            $query->andWhere(['warehouse_id' => Admin::getWarehouseIdsById()]);
//        }
        $businessTypeAll = [
            Flow::TYPE_BUYING => "订单入库",
            Flow::TYPE_BACK => "退仓申请",
            Flow::TYPE_CHECKOUT => "出库申请",
            Flow::TYPE_TRANSFEFDEP => "转货申请",
            Flow::TYPE_TRANSFEF => "调仓申请",
            Flow::TYPE_MATERIALRETURN => "物料退货申请",
            Flow::TYPE_WASTAGE => "耗损申请",
        ];
        $query->andWhere(["business_type" => array_keys($businessTypeAll)]);
        $query->andWhere(["is_complete" => 1]);
        if (is_numeric($department_id)) {
            $query->andWhere(["department_id" => $department_id]);
        }
        if (is_numeric($warehouse_id)) {
            $query->andWhere(["warehouse_id" => $warehouse_id]);
        }
        if ($sn || is_numeric($sn)) {
            $query->andWhere(["like", "sn", $sn]);
        }
        if (is_numeric($business_type)) {
            $query->andWhere(["business_type" => $business_type]);
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
        return $this->render('product', compact(['model', 'listDatas', 'listPages', 'businessTypeAll']));
    }

    /**
     * 销存库存管理
     */
    public function actionStock() {
        //该部门的商品历史销售累计。
        $keyword = Yii::$app->request->get('keyword');
        $departmentId = Yii::$app->request->get("departmentId");
        $supplierName = Yii::$app->request->get("supplierName");
        $model = new ProductStock();
        $query = ProductStock::find();
//        if(!Admin::checkSupperFlowAdmin()){
//            $warehouseAll = Warehouse::findAll(["department_id" => Admin::getDepId()]);
//            $warehouseIds = ArrayHelper::getColumn($warehouseAll, "id");
//            $query->andWhere(['warehouse_id' => $warehouseIds]);
//        }
        if (is_numeric($keyword)) {
            $query->andWhere(['product_id' => $keyword]);
        } else if ($keyword != "") {
            $productAll = Product::find()->where(['like', 'name', $keyword])->all();
            if ($productAll) {
                $productIds = ArrayHelper::getColumn($productAll, 'id');
                $query->andWhere(['product_id' => $productIds]);
            } else {
                $query->andWhere(['product_id' => 0]);
            }
        }
        if (is_numeric($departmentId)) {
            $warehouseAll = Warehouse::findAll(["department_id" => $departmentId]);
            $warehouseIds = ArrayHelper::getColumn($warehouseAll, "id");
            $query->andWhere(['warehouse_id' => $warehouseIds]);
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
        return $this->render('stock', compact(['model', 'listDatas', 'listPages']));
    }

    /**
     * 销存盘点页面
     * @return type
     */
    public function actionChecksale() {
        $model = new ProductInvoicingSale();
        if (Yii::$app->request->post()) {
            $result = $model->checkSale(Yii::$app->request->post());
            if ($result["state"]) {
                $return["message"] = "销存盘点申请成功";
                $return["type"] = "url";
                $return["url"] = Yii::$app->request->getReferrer();
                return Json::encode($return);
            }
            $return['message'] = is_array($result["message"]) ? reset($result["message"]) : $result["message"];
        }
        return Json::encode($return);
    }

  

}
