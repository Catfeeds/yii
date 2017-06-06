<?php
namespace app_web\controllers;
use common\models\Supplier;
use common\models\SupplierProduct;
use common\models\AdminLog;
use common\models\Product;
use common\models\ProductStock;

use libs\Utils;
use Yii;
use Exception;
use yii\data\ActiveDataProvider;
use yii\helpers\Json;
use app_web\components\CController;
use yii\web\UploadedFile;
use yii\helpers\Url;
use moonland\phpexcel\Excel;

/**
 * 业务基础数据管理 -- 供应商出品管理
 */
class SupplierproductController extends CController
{
    /**
     * 供应商出品列表
     */
    public function actionIndex()
    {
        $supplierId = Yii::$app->request->get('supplier_id');
        $status = Yii::$app->request->get('status');
        $name = Yii::$app->request->get('name');
        $model = new SupplierProduct();
        $query = SupplierProduct::find();
        if(is_numeric($status)){
            $query->andWhere(['status' => $status]);
        }
        if($supplierId){
            $query->andWhere(['supplier_id' => $supplierId]);
        }
        if($name || is_numeric($name)){
            $query->andWhere(['like','name' , $name]);
        }
        $query->orderBy('status desc, id desc');
        $isDownload = Yii::$app->request->get("isDownload");
        if ($isDownload) {
            $this->downloadIndex($query);
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
        return $this->render('index', compact(['model', 'listDatas', 'listPages']));
    }

    /**
     * 加载供应商出品创建或修改页
     * @param type $id 记录ID
     * @return type
     */
    public function actionForm($id = 0)
    {
        if($id){
            $model = SupplierProduct::findOne($id);
        }else{
            $model = new SupplierProduct;
        }
        return $this->renderPartial('_form', compact(['model']));
    }

    /**
     * 创建新的供应商出品
     */
    public function actionCreate()
    {
        $model = new SupplierProduct();
        if($model->load(Yii::$app->request->post())){
            if(!$model->save()) {
                $errors = $model->getFirstErrors();
                $return['message'] = join("\n", $errors);
                return Json::encode($return);
            }
            $return['html'] = $this->renderPartial('_list', ['data' => $model, "key" => -1]);
        }else{
            $errors = $model->getFirstErrors();
            $return['message'] = join("\n", $errors);
        }
        //记录日志
        $code = 'add_supplier';
        $content = '新增供应商出品' .$model->id;
        AdminLog::addLog($code, $content);
        return Json::encode($return);
    }

    /**
     * 修改供应商出品记录
     * @param type $id 记录ID
     * @return type
     */
    public function actionUpdate($id)
    {
        $model = SupplierProduct::findOne($id);
        if($model->load(Yii::$app->request->post())){
            if(!$model->save()) {
                $errors = $model->getFirstErrors();
                $return['message'] = join("\n", $errors);
                return Json::encode($return);
            }
            //记录日志
            $code = 'update_supplier';
            $content = '编辑供应商出品' .$model->id;
            AdminLog::addLog($code, $content);
            $return['html'] = $this->renderPartial('_list', ['data' => $model, "key" => -1]);
        }else{
            $return['message'] = '操作不成功！';
        }
        return Json::encode($return);
    }

    /**
     * 删除供应商出品记录
     * @param type $id 记录ID
     * @return type
     */
    public function actionDelete($id)
    {
        $model = SupplierProduct::findOne($id);
        if(!$model || $model->status == 1) {
            $return['error'] = 1;
            $return['message'] = '无法删除有效的供应商出品，请刷新再试！';
            return Json::encode($return);
        }
        $product = Product::findOne(["supplier_product_id" => $id]);
        if($product) {
            $return['error'] = 1;
            $return['message'] = '该供应商出品已生成物料，无法删除！';
            return Json::encode($return);
        }
        if($model->delete()) {
            $code = 'delete_supplier_product';
            $content = '删除供应商出品' . $model->id;
            AdminLog::addLog($code, $content);
            $return['error'] = 0;
            $return['message'] = '删除成功！';
        } else {
            $return['error'] = 1;
            $return['message'] = '操作不成功！';
        }
        return Json::encode($return);
    }

    /**
     * 设置供应商出品记录有效
     * @param type $id 记录ID
     * @return type
     */
    public function actionAddproduct($id)
    {
        $item = SupplierProduct::findOne($id);
        if(!$item || $item->status != SupplierProduct::STATUS_NO) {
            $return['error'] = 1;
            $return['message'] = '此出品状态错误，无法加入物料！';
            return Json::encode($return);
        }
        $model = new Product();
        $result = $model->addProduct($item);
        if($result["state"]) {
            $return['html'] = '操作成功';
        }else{
            $return['message'] = is_array($result["message"]) ? reset($result["message"]) : $result["message"];
        }
        return Json::encode($return);
    }
    
    /**
     * 设置供应商出品记录无效
     * @param type $id 记录ID
     * @return type
     */
    public function actionInvalidproduct($id)
    {
        $item = SupplierProduct::findOne($id);
        if(!$item || $item->status != SupplierProduct::STATUS_YES) {
            $return['error'] = 1;
            $return['message'] = '此出品状态错误，无法设置无效！';
            return Json::encode($return);
        }
        $product = Product::findOne(["supplier_product_id" => $id]);
        if(!$product) {
            $item->status = SupplierProduct::STATUS_NO;
            $item->save();
            $return["message"] = "设置成功";
            $return["type"] = "url";
            $return["url"] = Url::to(["supplierproduct/index"]);
            return Json::encode($return);
        }
        $stock = ProductStock::findOne(["product_id" => $product->id]);
        if($stock) {
            $return['error'] = 1;
            $return['message'] = '该供应商出品生成物料还存在于仓库，无法设置无效！';
            return Json::encode($return);
        }
        $result = $product->invalidProduct($item);
        if($result["state"]) {
            $return["message"] = "设置成功";
            $return["type"] = "url";
            $return["url"] = Url::to(["product/index"]);
            return Json::encode($return);
        }
        $return['message'] = is_array($result["message"]) ? reset($result["message"]) : $result["message"];
        return Json::encode($return);
    }
    
    /**
     * 异步获取供应商的出品列表
     * @param int $id 供应商ID
     */
    public function actionAjaxproductlist($id)
    {
        $num = Yii::$app->request->get('num');
        $keyword = Yii::$app->request->get('keyword');
        $barcode = Yii::$app->request->get("barcode");
        $model = new Product();
        $query = Product::find();
        $query->andWhere(['status' => 1]);
        $query->andWhere(['modify_status' => Product::MODIFY_STATUS_FINISH]);
        $query->andWhere(['supplier_id' => $id]);
        if($keyword || is_numeric($keyword)){
            $query->andWhere(['like','name',$keyword]);
        }
        if($barcode || is_numeric($barcode)){
            $query->andWhere(['like','barcode', $barcode]);
        }
        if($num) {
            $query->andWhere(['num' => $num]);
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
     * 下载记录
     * @param type $query 下载记录条件
     * @author dean
     */
    public function downloadIndex($query) {
        $all = $query->all();
        $datas = [];    
        foreach ($all as $key => $val) {
            $datas[] = [
                'id' => $key+1,
                'name' => $val->name,
                'supplier_id' => Supplier::getNameById($val->supplier_id),
                'num' => $val->num,
                'purchase_price' => $val->purchase_price,
                'spec' => $val->spec,
                'unit' => $val->unit,
                'type' => $val->showType(),
                'status' => $val->showStatus(),
            ];
        }
        $columns = [
            [ 'attribute' => 'id','header' => '序号'],
            [ 'attribute' => 'name','header' => '名称'],
            [ 'attribute' => 'supplier_id','header' => '供应商'],
            [ 'attribute' => 'num','header' => '供应商出品编码'],
            [ 'attribute' => 'purchase_price','header' => '进货参考价格'],
            [ 'attribute' => 'spec','header' => '规格'],
            [ 'attribute' => 'unit','header' => '单位'],
            [ 'attribute' => 'type','header' => '物料类别'],
            [ 'attribute' => 'status','header' => '状态'],
        ];
        return Utils::downloadExcel($datas, $columns, "供应商物料出品列表");
    }
    
    /**
     * 下载导入模板
     */
    public function actionDowntemplate() {
        $datas[] = [
            'name' => "",
            'supplier_id' => "",
            'num' => "",
            'purchase_price' => "",
            'spec' => "",
            'unit' => "",
            'type' => "",
        ];
        
        $columns = [
            [ 'attribute' => 'name','header' => '名称[必填]'],
            [ 'attribute' => 'supplier_id','header' => '供应商[必填]'],
            [ 'attribute' => 'num','header' => '供应商出品编码[必填]'],
            [ 'attribute' => 'purchase_price','header' => '进货参考价格[必填]'],
            [ 'attribute' => 'spec','header' => '规格'],
            [ 'attribute' => 'unit','header' => '单位'],
            [ 'attribute' => 'type','header' => '物料类别 1[商品] 2[资产]'],
        ];
        return Utils::downloadExcel($datas, $columns, "供应商物料出品导入模板");
    }
    
    /**
     * 导入供应商出品记录
     * @return type
     * @throws Exception
     */
    public function actionImport() {
        set_time_limit(0);
        if(Yii::$app->request->getIsPost()){
            $data = UploadedFile::getInstanceByName('excel');
            if(!$data){
                return Json::encode(["result" => "Error", "message" => "上传失败"]);
            }
            if(!in_array($data->type, ["application/vnd.ms-excel", "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet", "application/octet-stream"])) {
                return Json::encode(["result" => "Error", "message" => "上传文件格式错误"]);
            }
            $file = Utils::getFile(Utils::newFileName($data->getExtension()));
            if(!$data->saveAs($file)){
                return Json::encode(["result" => "Error", "message" => "上传失败"]);
            }
            $datas = Excel::import($file);
            $transaction = \Yii::$app->db->beginTransaction();
            try {
                foreach($datas as $data){
                    $model = new SupplierProduct();
                    $model->status = SupplierProduct::STATUS_NO;
                    foreach ($data as $key => $val) {
                        if($key == "名称[必填]") {
                            $model->name = "$val";
                        }
                        if($key == "供应商[必填]") {
                            $item = Supplier::findOne(["name" => $val]);
                            if(!$item) {
                                throw new Exception("请填写正确的供应商名称");
                            }
                            $model->supplier_id = $item->id;
                        }
                        if($key == "供应商出品编码[必填]") {
                            $model->num = "$val";
                        }
                        if($key == "进货参考价格[必填]") {
                            $model->purchase_price = $val;
                        }
                        if($key == "规格") {
                            $model->spec = "$val";
                        }
                        if($key == "单位") {
                            $model->unit = "$val";
                        }
                        if($key == "物料类别 1[商品] 2[资产]") {
                            $material_type = Product::TYPE_PRODUCT;
                            if(in_array($val, Product::getTypeSelectData())){
                                $material_type = array_search($val, Product::getTypeSelectData());
                            }
                            $model->material_type = $material_type;
                        }
                    }
                    if(!$model->save()){
                        $errors = $model->getFirstErrors();
                        Utils::delFile($file);
                        $transaction->rollBack();
                        return Json::encode(["result" => "Error", "message" => join("\n", $errors)]);
                    }
                }
                Utils::delFile($file);
                $transaction->commit();
            } catch (Exception $exc) {
                Utils::delFile($file);
                $transaction->rollBack();
                return Json::encode(["result" => "Error", "message" => $exc->getMessage() ? $exc->getMessage() : $exc->getTraceAsString()]);
            }
            return Json::encode(["result" => "Success"]);
        }
        return Json::encode(["result" => "Error", "message" => "网络异常"]);
    }
}
