<?php
namespace app_web\controllers;

use Yii;
use app_web\components\CController;
use yii\data\ActiveDataProvider;
use common\models\Area;
use common\models\AdminLog;
use common\models\Warehouse;
use yii\helpers\Json;

class AreaController extends CController
{
    /**
     * 地区列表
     */
    public function actionIndex()
    {
        $parentId = Yii::$app->request->get('parentId');
        $status = Yii::$app->request->get('status');
        $keyword = Yii::$app->request->get('keyword');
        $model = new Area();
        $query = Area::find();
        if(!is_numeric($parentId)) {
            $parentId = 0;
        }
        $query->andWhere(['parentId' => $parentId]);
        if(is_numeric($status)){
            $query->andWhere(['status' => $status]);
        }
        if($keyword){
            $query->andWhere(['like', 'name', $keyword]);
        }
        $query->orderBy('status desc, id desc');
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10,
                'validatePage' => false,
            ],
        ]);
        $listDatas = $dataProvider->getModels();
        $listPages = $dataProvider->getPagination();
        return $this->render('index', compact(['model', 'listDatas', 'listPages', 'parentId']));
    }

     /**
     * 加载创建或修改页面
     * @param type $id 地区ID
     * @return type
     */
    public function actionForm($id = 0)
    {
        $parentId = Yii::$app->request->get('parentId', 0);
        if($id){
            $model = Area::findOne($id);
        }else{
            $model = new Area;
            $model->parentId = $parentId;
        }
        return $this->renderPartial('_form', compact(['model']));
    }

    /**
     * 创建新地区记录
     */
    public function actionCreate()
    {
        $model = new Area();
        if($model->load(Yii::$app->request->post()) && $model->save()){//if直接存储
            AdminLog::addLog("area_add", "添加新地区".$model->id);//添加日志
            $return['html'] = $this->renderPartial('_list', ['data' => $model, "key" => -1]);
        }else{
            $errors = $model->getFirstErrors();//自带的方法
            $return['message'] = join("\n", $errors);
        }
        return Json::encode($return);
    }

    /**
     * 修改地区记录
     * @param type $id 地区ID
     */
    public function actionUpdate($id)
    {
        $model = Area::findOne($id);
        if($model->load(Yii::$app->request->post()) && $model->save()){
            AdminLog::addLog("update_add", "修改新地区".$model->id);
            $return['html'] = $this->renderPartial('_list', ['data' => $model, "key" => -1]);
        }else{
            $return['message'] = '操作不成功！';
        }
        return Json::encode($return);
    }

    /**
     * 删除地区
     * @param type $id 地区ID
     */
    public function actionDelete($id)
    {
        $model = Area::findOne($id);
        if(!$model  || $model->status == 1) {
            $return['error'] = 1;
            $return['message'] = '无法删除有效的地区，请刷新再试！';
            return Json::encode($return);
        }
        $child = Area::findOne(["parentId" => $id]);
        if($child) {
            $return['error'] = 1;
            $return['message'] = '该地区还有下属地区，无法删除！';
            return Json::encode($return);
        }
        $warehosue = Warehouse::findOne(["area_id" => $id]);
        if($warehosue) {
            $return['error'] = 1;
            $return['message'] = '该地区还有绑定的仓库，无法删除！';
            return Json::encode($return);
        }
        if($model->delete()) {
            $code = 'delete_area';
            $content = '删除地区' . $model->id;
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
     * 异步获取地区下级地区列表
     */
    public function actionAjaxgetlist()
    {
        $pId = Yii::$app->request->get('pid');
        $nextName = Yii::$app->request->get('nextName');
        $childName = Yii::$app->request->get('childName');
        if ($pId < 0 || !is_numeric($pId)) {
            app()->end();//不懂
        }
        $return = [];
        $data = Area::getAreaSelectData($pId, Area::STATUS_OK);
        if(!$data) {
            echo Json::encode($return);
        }
        $return["htmlOptions"] = [];
        if($nextName) {
            $return["htmlOptions"]["class"] = $nextName;
            $return["htmlOptions"]["name"] = $nextName;
        }
        if($childName) {
            $return["htmlOptions"]["nextName"] = $childName;
        }
        $return["data"][] = ["key" => "-1", "value" => '请选择'];
        foreach ($data as $key => $val) {
            $result["key"] = $key;
            $result["value"] = $val;
            $return["data"][] = $result;
        }
        echo Json::encode($return);
    }
}
