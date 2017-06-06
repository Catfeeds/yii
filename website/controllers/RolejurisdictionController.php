<?php
namespace app_web\controllers;

use Yii;
use app_web\components\CController;
use yii\data\ActiveDataProvider;
use common\models\RoleJurisdiction;
use common\models\Role;
use common\models\Menu;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
/**
 * 部门基础数据 -- 权限管理
 */
class RolejurisdictionController extends CController {
    
    /**
     * 权限管理列表页
     */
    public function actionSetrole() {
        $department_id = Yii::$app->request->get("department_id");
        $keyword = Yii::$app->request->get("keyword");
        $query = Role::find();
        $query->andWhere(['status' => Role::STATUS_OK]);
        $query->andWhere(['<>', 'id', "1"]);
        if ($keyword || is_numeric($keyword)) {
            $query->andWhere(['like', 'name', $keyword]);
        }
        if($department_id) {
             $query->andWhere(['department_id'=>$department_id]);
        }
        $query->orderBy('id desc');
//        dump($query->createCommand()->getRawSql());
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 6,
                'validatePage' => false,
            ],
        ]);
        $listDatas = $dataProvider->getModels();
        $listPages = $dataProvider->getPagination();
        $menuAll = Menu::findAll(["is_show" => 1, "depth" => 3]);
        $twoMenuIds = ArrayHelper::getColumn($menuAll, "parent_id");
        $menuAll = ArrayHelper::map($menuAll, "id", "name");
        $twoMenuAll = Menu::find()->andWhere(["is_show" => 1, "depth" => 2])->andWhere(["not in", "id", $twoMenuIds])->all();
        $twoMenuAll = ArrayHelper::map($twoMenuAll, "id", "name");
        $model = new RoleJurisdiction();
        $jurisdictionAll = $model::find()->all();
        $roleJur = [];
        foreach ($jurisdictionAll as $jurisdictionVal) {
            $roleJur[$jurisdictionVal->roleId][$jurisdictionVal->menu_id] = 1;
        }
        echo $this->render("setrole", compact("listDatas", "listPages","menuAll", "roleJur", "twoMenuAll"));
    }
    
    /**
     * 设置权限
     */
    public function actionSaverole(){
        $return['message'] = "页面不存在";
        if(Yii::$app->request->getIsPost()) {
            $model = new RoleJurisdiction();
            $result = $model->setRoleJur(Yii::$app->request->post());
            if($result["state"]) {
                $return['html'] = 1;
            }
            $return['message'] = is_array($result["message"]) ? reset($result["message"]) : $result["message"];
        }
        return Json::encode($return);
    }
}

