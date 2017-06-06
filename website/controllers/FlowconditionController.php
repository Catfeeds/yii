<?php
namespace app_web\controllers;

use common\models\FlowCondition;
use common\models\AdminLog;
use common\models\FlowConditionProduct;
use libs\Utils;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\Json;
use app_web\components\CController;
use yii\web\UploadedFile;

/**
 * 业务基础数据管理
 * 供应商管理
 */
class FlowconditionController extends CController
{
    public function actionIndex()
    {
        $status = Yii::$app->request->get('status');
        $type = Yii::$app->request->get('type');
        $config_id = Yii::$app->request->get('config_id');
        $model = new FlowCondition();
        
        $query = FlowCondition::find();

        if(is_numeric($status)){
            $query->andWhere(['status' => $status]);
        }
        if(is_numeric($type)){
            $query->andWhere(['type' => $type]);
        }
        if(is_numeric($config_id)){
            $query->andWhere(['config_id' => $config_id]);
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

    public function actionForm($id = 0)
    {
        if($id){
            $model = FlowCondition::findOne($id);
        }else{
            $model = new FlowCondition;
        }

        return $this->renderPartial('_form', compact(['model']));
    }

    public function actionCreate()
    {
        $model = new FlowCondition();

        if($model->load(Yii::$app->request->post()) && $model->save()){
            $return['html'] = $this->renderPartial('_list', ['data' => $model, "key" => -1]);
        }else{
            $errors = $model->getFirstErrors();
            dump($model->getErrors());
            $return['message'] = join("\n", $errors);
        }
		//记录日志
		$code = 'add_supplier';
		$content = '新增供应商' .$model->id;
		AdminLog::addLog($code, $content);
		
        return Json::encode($return);
    }

    public function actionUpdate($id)
    {
        $model = FlowCondition::findOne($id);

        if($model->load(Yii::$app->request->post()) && $model->save()){
        	//记录日志
			$code = 'update_supplier';
			$content = '编辑供应商' .$model->id;
			AdminLog::addLog($code, $content);
			
            $return['html'] = $this->renderPartial('_list', ['data' => $model, "key" => -1]);
        }else{
            $return['message'] = '操作不成功！';
        }

        return Json::encode($return);
    }

    public function actionDelete($id)
    {
        $model = FlowCondition::findOne($id);
        if($model->status == FlowCondition::STATUS_YES) {
            $return['error'] = 1;
            $return['message'] = '状态错误，无法删除！';
            return Json::encode($return);
        }
        if($model->delete()){
        	//记录日志
			$code = 'delete_supplier';
			$content = '删除供应商' .$model->id;
			AdminLog::addLog($code, $content);
		
            $return['error'] = 0;
        }else{
            $return['error'] = 1;
            $return['message'] = '操作不成功！';
        }

        return Json::encode($return);
    }

    public function actionImport()
    {
        $params = Yii::$app->request->post();
        
        if(Yii::$app->request->getIsPost()){
            $data = UploadedFile::getInstanceByName('excel');
            if($data){
                $file = Utils::newFileName($data->getExtension());

                if($data->saveAs($file)){
                    Utils::exportImport('FlowCondition', $file);
                    
                    return Json::encode(["result" => "Success"]);
                }else{
                    return Json::encode(["result" => "Fail"]);
                }
            }
            return Json::encode(["result" => "ValidFail"]);
        }
        
        return Json::encode(["result" => "PostFail"]);
    }
    
    public function actionExport()
    {
        return Utils::exportExcel('FlowCondition');
    }
}
