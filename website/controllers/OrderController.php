<?php
namespace app_web\controllers;

use Yii;
use app_web\components\CController;
use common\models\Salesorder;
use yii\helpers\Json;
use common\models\Salesorderproduct;
use common\models\Admin;
use yii\data\ActiveDataProvider;
use common\models\Warehouse;
use common\models\ProductStock;
use common\models\Product;
use yii\helpers\ArrayHelper;
use common\models\Supplier;
use yii\helpers\Url;


class OrderController extends CController {

  /**
     * 肖波
     *2017年2月21日 11:20:03
     * 制作订单-销售订单管理
     */
    public function actionIndex() {
        $departmentId = Yii::$app->request->get("departmentId");
        $customer_company = Yii::$app->request->get("customer_company");
        $model = new Salesorder;
        $query = Salesorder::find();
        if (is_numeric($departmentId)) {
            $query->andWhere(['department_id' => $departmentId]);
        }
        if ($customer_company != "") {
            $query->andWhere(['like', 'customer_company', $customer_company]);
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
        return $this->render('index', compact(['model', 'listDatas', 'listPages']));
    }

/**
*功能：订单详情页面
*传值：对应的订单数据表中的id
*返值：返回对应的详情页面中的数据
*2017年2月21日 11:28:55
*肖波
*/
 
    /**
     * 详情页
     */
    public function actionInfo($id)
    {
        $model = Salesorder::findOne($id);
        $info = Salesorderproduct::findAll(["sale_order_id" => $model->sale_order_id]);
        return $this->render('info', compact('model', 'info'));
    }
     /**
     * 新增页
     */
    public function actionAdd()
    {
      //得到对应的部门
     $user= Yii::$app->user->getId();
     $department=Admin::findOne($user);
     $department_id=$department->department_id;
    $result= Admin::getDepAdmin($department_id);
        $model = new Salesorder;
        return $this->render('add', compact('model','department_id'));
    }
  /**
  *功能：新建销售订单
  *传值：对应的填写的数据
  *返值：返回对应的数据建立的状态
  *2017年2月21日 15:09:29
  */
  /**
     * 新增处理方法
     */
    public function actionCreate()
    {
        $model = new Salesorder();
        if(Yii::$app->request->post()){
            $result = $model->addSalesOrder(Yii::$app->request->post());
            if($result["state"]) {
                $return["message"] = "新增成功";
                $return["type"] = "url";
                $return["url"] = Url::to(["order/index"]);
                return Json::encode($return);
            }
            $return['message'] = is_array($result["message"]) ? reset($result["message"]) : $result["message"];
        }
        return Json::encode($return);
    }
      /**
      *执行对应的销售订单
      *传值：对应的订单id
      *返值：返回对应的操作成功或者失败
      *2017年2月22日 17:45:15
      */
      public function actionOperationsaleorder($id){   
           $model=Salesorder::findOne($id);
          $result=$model->operationsaleorder($model);
          if($result)
          {
            Yii::$app->getSession()->setFlash("msg",'订单执行成功');
            return $this->redirect(['index']);
          }else{
             Yii::$app->getSession()->setFlash("msg",'订单执行失败');
              return $this->redirect(['index']);
          } 
      }
     /**
     *驳回对应的订单
     *传值：对应的订单id
     *返值：返回对应的成功或者失败的值
     *2017年2月22日 19:19:47
     *肖波
     */
     public function actionRejectsaleorder($id){
     
          $model=Salesorder::findOne($id);
          $result=$model->rejectsaleorder($model);
          if($result)
          {
            Yii::$app->getSession()->setFlash("msg",'订单驳回成功');
            return $this->redirect(['index']);
          }else{
             Yii::$app->getSession()->setFlash("msg",'订单驳回失败');
              return $this->redirect(['index']);
          } 
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
        $supplierName=Yii::$app->request->get("supplier_name");
        $materialType = Yii::$app->request->get('material_type');
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
        if($materialType){
            $productAll = Product::findAll(["material_type" => $materialType]);
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
        return $this->renderPartial('_productlist', compact('model', 'listDatas', 'listPages','id'));
    }


}


















?>