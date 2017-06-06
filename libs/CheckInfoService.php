<?php
namespace libs;
use Yii;
use common\models\CheckPlanningFlow;
use common\models\DepartmentBalance;
use common\models\Product;
use common\models\ProductStock;
use common\models\Warehouse;
use common\models\WarehousePlanning;
use common\models\WarehouseBuyingProduct;
use common\models\ProductCategory;

use yii\helpers\ArrayHelper;

class CheckInfoService {
    /**
     * 获取盘点计划的数据详情
     * @param type $checkItem 盘点计划对象
     * @param type $checkData 盘点计划详情对象
     * @return type
     */
    public function getInfoList($checkItem, $checkData) {
        $return = ["amountList" => [], "productList" => []];
        if($checkItem->type == CheckPlanningFlow::TYPE_PLANNING && $checkItem->is_check_amount) {
            $departmentIds = ArrayHelper::getColumn($checkData, "data_id");
            $return["amountList"] = $this->getAmountList($departmentIds);
        }else if($checkItem->type == CheckPlanningFlow::TYPE_DEPARTMENT && $checkItem->is_check_amount) {
            $return["amountList"] = $this->getAmountList($checkItem->department_id);
        }
        $return["productList"] = $this->getProductList($checkItem, $checkData);
        return $return;
    }
    
    /**
     * 获取盘点计划的部门余额
     * @param type $departmentIds 部门ID
     * @return type
     */
    private function getAmountList($departmentIds) {
        $departmentBalanceAll = DepartmentBalance::findAll(["department_id" => $departmentIds]);
        return ArrayHelper::map($departmentBalanceAll, "department_id", "balance");
    }
    
    /**
     * 获取满足盘点计划的物料列表
     * @param type $checkItem 盘点计划对象
     * @param type $checkData 盘点计划详情对象
     * @return type
     */
    private function getProductList($checkItem, $checkData) {
        $query = Product::find();
        $buyProductQuery = WarehouseBuyingProduct::find();
        if($checkItem->product_name) {
            $query->andWhere(['like', "name", $checkItem->product_name]);
            $buyProductQuery->andWhere(['like', "name", $checkItem->product_name]);
        }
        if($checkItem->product_cate_id) {
            $query->andWhere(["product_category_id" => $checkItem->product_cate_id]);
            $buyProductQuery->andWhere(["material_type" => $checkItem->product_cate_id]);
        }
        $productItem = $query->all();
        $buyProductItem = $buyProductQuery->all();
        $productIds = ArrayHelper::getColumn($productItem, "id");
        $buyProductIds = ArrayHelper::getColumn($buyProductItem, "id");
        $productAll = ArrayHelper::index($productItem, "id");
        $return = [];
        
        foreach ($checkData as $val) {
            if($checkItem->type == CheckPlanningFlow::TYPE_PLANNING){
                $warehouseAll = Warehouse::findAll(["department_id" => $val->data_id]);
                $warehouseIds = ArrayHelper::getColumn($warehouseAll, "id");
            } else {
                $warehouseIds = $val->data_id;
            }
            $pStockQuery = ProductStock::find();
            if($productIds) {
                $pStockQuery->orWhere(["product_id" => $productIds, "type" => [WarehousePlanning::TYPE_NORMAL, WarehousePlanning::TYPE_ROUTINE]]);
            } else {
                $pStockQuery->orWhere(["product_id" => 0, "type" => [WarehousePlanning::TYPE_NORMAL, WarehousePlanning::TYPE_ROUTINE]]);
            }
            if($buyProductIds) {
                $pStockQuery->orWhere(["product_id" => $buyProductIds, "type" => WarehousePlanning::TYPE_EXCEPTION]);
            } else {
                $pStockQuery->orWhere(["product_id" => 0, "type" => WarehousePlanning::TYPE_EXCEPTION]);
            }
            $pStockQuery->andWhere(["warehouse_id" => $warehouseIds]);
            if($checkItem->supplier_id) {
                $pStockQuery->andWhere(["supplier_id" => $checkItem->supplier_id]);
            }
            $pStockQuery->orderBy("warehouse_id");
            $stockAll = $pStockQuery->all();
            $return[$val->data_id] = $this->getProductInfo($checkItem->id, $stockAll, $productAll);
        }
        return $return;
    }
    
    /**
     * 获取物料详情
     * @param type $checkPlanningId 盘点计划ID
     * @param type $stockAll 满足条件的物料库存ID
     * @param type $productAll 物料列表对象
     * @return type
     */
    private function getProductInfo($checkPlanningId, $stockAll, $productAll) {
        $return = [];
        $importList = Yii::$app->cache->get("importList_".$checkPlanningId);
//        dump($importList);
        foreach ($stockAll as $stockVal) {
            if($stockVal->type == WarehousePlanning::TYPE_EXCEPTION) {
                $productItem = WarehouseBuyingProduct::findOne($stockVal->product_id);
            } else {
                $productItem = $productAll[$stockVal->product_id];
            }
            $warehouseItem = Warehouse::findOne($stockVal->warehouse_id);
            $return[$stockVal->id] = [
                'batches' => $stockVal->batches,
                'department_id' => $warehouseItem->department_id,
                'warehouse_id' => $stockVal->warehouse_id,
                'warehouse_name' => $warehouseItem->name,
                'product_id' => $stockVal->product_id,
                'number' => $stockVal->number,
                'name' => $productItem->name,
                'purchase_price' => $stockVal->type == WarehousePlanning::TYPE_EXCEPTION ? $productItem->price : $productItem->purchase_price,
                'sale_price' => $stockVal->type == WarehousePlanning::TYPE_EXCEPTION ? $productItem->purchase_price : $productItem->sale_price,
                'supplier_id' => $productItem->supplier_id,
                'supplier_product_id' => $productItem->supplier_product_id,
                'barcode' => $stockVal->type == WarehousePlanning::TYPE_EXCEPTION ? $productItem->num : $productItem->barcode,
                'spec' => $productItem->spec,
                'unit' => $productItem->unit,
                'material_type' => $stockVal->type == WarehousePlanning::TYPE_EXCEPTION ? $productItem->material_type : $productItem->product_category_id,
                'cate_name' => ProductCategory::getNameById($stockVal->type == WarehousePlanning::TYPE_EXCEPTION ? $productItem->material_type : $productItem->product_category_id),
                'type' => $stockVal->type,
                'check_num' => isset($importList[$stockVal->id]) ? $importList[$stockVal->id] : "",
            ];
        }
        return $return;
    }
}
