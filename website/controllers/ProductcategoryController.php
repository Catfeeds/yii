<?php

namespace app_web\controllers;

use common\models\ProductCategory;
use common\models\AdminLog;
use common\models\Product;
use libs\Utils;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\Json;
use app_web\components\CController;
use yii\web\UploadedFile;
use moonland\phpexcel\Excel;
use Exception;

/**
 * 业务基础数据 -- 物料分类管理
 */
class ProductcategoryController extends CController {

    /**
     * 物料分类列表页
     */
    public function actionIndex() {
        $level = Yii::$app->request->get('level');
        $status = Yii::$app->request->get('status');
        $keyword = Yii::$app->request->get('keyword');
        $model = new ProductCategory();
        $query = ProductCategory::find();
        if ($level) {
            $query->andWhere(['level' => $level]);
        }
        if (is_numeric($status)) {
            $query->andWhere(['status' => $status]);
        } else {
            $query->andWhere(['status' => [0, 1]]);
        }
        if ($keyword) {
            $query->andWhere(['like', 'name', $keyword]);
        }
        $query->orderBy('status desc, id desc');
        $isDownload = Yii::$app->request->get("isDownload");
        if ($isDownload) {
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
        return $this->render('index', compact(['model', 'listDatas', 'listPages']));
    }

    /**
     * 加载新增或修改物料分类页面
     * @param type $id 物料分类ID
     * @return type
     */
    public function actionForm($id = 0) {
        if ($id) {
            $model = ProductCategory::findOne($id);
        } else {
            $model = new ProductCategory;
        }
        return $this->renderPartial('_form', compact(['model']));
    }

    /**
     * 新增物料分类
     */
    public function actionCreate() {
        $model = new ProductCategory();
        if ($model->load(Yii::$app->request->post())) {
            if($model->save()){
                $return['html'] = $this->renderPartial('_list', ['data' => $model, 'key' => -1]);
                //记录日志
                $code = 'add_ProductCate';
                $content = '新增物料分类' . $model->id;
            } else {
                $errors = $model->getFirstErrors();
                $return['message'] = join("\n", $errors);
            }
            AdminLog::addLog($code, $content);
            return Json::encode($return);
        }
    }

    /**
     * 修改物料分类
     * @param type $id 物料分类ID
     * @return type
     */
    public function actionUpdate($id) {
        $model = ProductCategory::findOne($id);
        if ($model->load(Yii::$app->request->post())) {
            if($model->save()){ 
                //记录日志
                $code = 'update_ProductCate';
                $content = '编辑物料分类' . $model->id;
                AdminLog::addLog($code, $content);

                $return['html'] = $this->renderPartial('_list', ['data' => $model, "key" => -1]);
            } else {
                $return['message'] = '操作不成功！';
            }
            return Json::encode($return);
        }
    }

    /**
     * 删除物料分类
     * @param type $id 物料分类ID
     * @return type
     */
    public function actionDelete($id) {
        $model = ProductCategory::findOne($id);
        if(!$model || $model->status == 1) {
            $return['error'] = 1;
            $return['message'] = '无法删除有效的物料分类，请刷新再试！';
            return Json::encode($return);
        }
        $product = Product::findOne(["product_category_id" => $id]);
        if($product) {
            $return['error'] = 1;
            $return['message'] = '该物料分类还有所属物料，无法删除！';
            return Json::encode($return);
        }
        if($model->delete()) {
            $code = 'delete_cate';
            $content = '删除物料分类' . $model->id;
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
     * 获取物料分类下供应商出品记录
     * @param type $id 物料分类ID
     * @return type
     */
    public function actionProductList($id) {
        $model = new ProductCategoryProduct();
        $query = ProductCategoryProduct::find();
        $query->andWhere(['ProductCategory_id', $id]);
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
        return $this->render('productList', compact(['model', 'listDatas', 'listPages']));
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
                'name' => $val->showName(),
                'factor' => $val->factor,
                'is_batches' => ProductCategory::showBatchesName($val->is_batches),
                'status' => $val->showStatus(),
            ];
        }
        $columns = [
            [ 'attribute' => 'id','header' => '序号'],
            [ 'attribute' => 'name','header' => '名称'],
            [ 'attribute' => 'factor','header' => '定价系数'],
            [ 'attribute' => 'is_batches','header' => '是否需要批次号'],
            [ 'attribute' => 'status','header' => '状态'],
        ];
        return Utils::downloadExcel($datas, $columns, "物料分类列表");
    }
    
    /**
     * 下载导入模板
     */
    public function actionDowntemplate() {
        $datas[] = [
            'name' => "",
            'factor' => "",
            'is_batches' => "",
            'status' => "",
        ];
        $columns = [
            [ 'attribute' => 'name','header' => '名称 [必填]'],
            [ 'attribute' => 'factor','header' => '定价系数 [必填]'],
            [ 'attribute' => 'is_batches','header' => '是否需要批次号 1[需要] 0[不需要]'],
            [ 'attribute' => 'status','header' => '状态 1[有效] 0[无效]'],
        ];
        return Utils::downloadExcel($datas, $columns, "物料分类模板");
    }
    
    /**
     * 导入物料分类
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
                    $model = new ProductCategory();
                    $model->parent_id = 0;
                    $model->slug = 'slug';
                    $model->sort = "1";
                    foreach ($data as $key => $val) {
                        if($key == "名称 [必填]") {
                            $model->name = "$val";
                        }
                        if($key == "定价系数 [必填]") {
                            if(!is_numeric($val)) {
                                throw new Exception("定价系数必须为数字");
                            }
                            $model->factor = $val;
                        }
                        if($key == "是否需要批次号 1[需要] 0[不需要]") {
                            if(!in_array($val, [0,1])) {
                                throw new Exception("是否需要批次号设置错误");
                            }
                            $model->is_batches = $val;
                        }
                        if($key == "状态 1[有效] 0[无效]") {
                            $status = 0;
                            if(in_array($val, [0,1])) {
                                $status = $val;
                            }
                            $model->status = $status;
                        }
                    }
                    if(!$model->save()){
                        $errors = $model->getFirstErrors();
                        throw new Exception(reset($errors));
                    }
                }
                Utils::delFile($file);
                $transaction->commit();
            } catch (Exception $exc) {
                Utils::delFile($file);
                $transaction->rollBack();
                return Json::encode(["result" => "Error", "message" => $exc->getMessage()]);
            }
            return Json::encode(["result" => "Success"]);
        }
        return Json::encode(["result" => "Error", "message" => "网络异常"]);
    }
    
    /**
     * 异步获取物料分类的定价系数和是否需要批次号
     */
    public function actionAjaxinfo(){
        $cateId = \Yii::$app->request->get("cateId");
        $cateItem = ProductCategory::findOne($cateId);
        $resule = [
                "factor" => $cateItem->factor,
                "is_batches" => $cateItem->is_batches,
        ];
        return Json::encode($resule);
    }
}
