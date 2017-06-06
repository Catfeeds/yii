<?php
namespace app_web\controllers;

use common\models\Product;
use common\models\ProductStock;
use common\models\BusinessAll;

use Yii;
use yii\data\ActiveDataProvider;
use app_web\components\CController;
use yii\helpers\ArrayHelper;

/**
 * 盘点 controller
 */
class InventoryController extends CController
{

    public function actionIndex()
    {
        return $this->render('index');
    }
	/*实时库存管理*/
    public function actionRealtime()
    {
          $keyword = Yii::$app->request->get('keyword');
        $warehouseId = Yii::$app->request->get("warehouseId");
        $supplierName = Yii::$app->request->get("supplierName");
        $model = new ProductStock();
        $query = ProductStock::find();
        if($warehouseId){
            $query->andWhere(['warehouse_id' => $warehouseId]);
        }
        
        //供应商查询
         if($supplierName != ""){
            $supplierAll = Supplier::find()->where(['like','name',$supplierName])->all();
            if($supplierAll) {
                $supplierIds = ArrayHelper::getColumn($supplierAll, 'id');
                $productAll = Product::findAll(['supplier_id' => $supplierIds]);
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
        if(is_numeric($keyword)){
            $query->andWhere(['product_id', $keyword]);
        } else if($keyword != ""){
            $productAll = Product::find()->where(['like','name',$keyword])->all();
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
        
        return $this->render('realtime', compact(['model', 'listDatas', 'listPages']));
    }


    
    /*销存商品管理*/
    public function actionProduct()
    {
    	//包含 订单入库 出库申请 退仓申请 转货申请 掉仓库申请 物料耗损 库存盘点申请
    	//所有类的集合，执行最后一步。
        return $this->render('product');
    }
    
    /*销存库存管理*/
    public function actionStock()
    {
    	//该部门的商品历史销售累计。
        return $this->render('stock');
    }
    
     /*部门盘点管理*/
    public function actionCheck()
    {
        return $this->render('check');
    }
    
    
  
}
