<?php
namespace app_web\controllers;

use common\models\FlowConfigStep;
use common\models\AdminLog;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\Json;
use app_web\components\CController;
use libs\common\Flow;
use yii\helpers\Url;

/**
 * 业务基础数据 -- 流程步骤设置
 */
class FlowconfigstepController extends CController
{
    /**
     * 业务流程步骤列表页
     */
    public function actionIndex()
    {
        $keyword = Yii::$app->request->get('keyword');
        $type = Yii::$app->request->get('type');
        $model = new FlowConfigStep();
        $query = FlowConfigStep::find();
        if(is_numeric($type)){
            $query->andWhere(['config_sn' => $type]);
        }
        if($keyword || is_numeric($keyword)){
            $query->andWhere(['like', 'name', $keyword]);
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
     * 加载新增或修改业务流程步骤页面
     * @param type $id 业务流程步骤ID
     * @return type
     */
    public function actionForm($id = 0)
    {
        if($id){
            $model = FlowConfigStep::findOne($id);
        }else{
            $model = new FlowConfigStep;
        }
        return $this->renderPartial('_form', compact(['model']));
    }

    /**
     * 新增业务流程步骤
     */
    public function actionCreate()
    {
        $model = new FlowConfigStep();
        if($model->load(Yii::$app->request->post())){
            if(in_array($model->config_sn, Flow::getOperationType()) && !$model->operation_step) {
                $return['message'] = "该类型必须有执行步骤";
                return Json::encode($return);
            }
            if($model->save()){
                //记录日志
                $code = 'add_flowconfigstep';
                $content = '新增业务流程步骤' .$model->id;
                AdminLog::addLog($code, $content);
                $return["message"] = "新增成功";
                $return["type"] = "url";
                $return["url"] = Url::to(["flowconfigstep/index"]);
                return Json::encode($return);
            }else{
                $errors = $model->getFirstErrors();
                $return['message'] = join("\n", $errors);
            }
        }
        return Json::encode($return);
    }

    /**
     * 修改业务流程步骤
     * @param type $id 业务流程步骤ID
     * @return type
     */
    public function actionUpdate($id)
    {
        $model = FlowConfigStep::findOne($id);
        if($model->load(Yii::$app->request->post())){
            if(in_array($model->config_sn, Flow::getOperationType()) && !$model->operation_step) {
                $return['message'] = "该类型必须有执行步骤";
                return Json::encode($return);
            }
            if($model->save()){
                //记录日志
                $code = 'update_fcstep';
                $content = '修改业务流程步骤' .$model->id;
                AdminLog::addLog($code, $content);
                $return["message"] = "修改成功";
                $return["type"] = "url";
                $return["url"] = Url::to(["flowconfigstep/index"]);
                return Json::encode($return);
            }else{
                $errors = $model->getFirstErrors();
                $return['message'] = join("\n", $errors);
            }
        }
        return Json::encode($return);
    }
    
    /**
     * 删除业务流程步骤
     */
    public function actionDelete($id) {
        $model = FlowConfigStep::findOne($id);
        if ($model->delete()) {
            //记录日志
            $code = 'delete_flowconfigstep';
            $content = '删除业务流程步骤' . $model->id;
            AdminLog::addLog($code, $content);
            $return['error'] = 0;
            $return['message'] = '操作成功！';
        } else {
            $return['error'] = 1;
            $return['message'] = '操作不成功！';
        }
        return Json::encode($return);
    }
}
