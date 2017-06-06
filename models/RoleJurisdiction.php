<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\db\Exception;
use common\models\Menu;
/**
 * This is the model class for table "RoleJurisdiction".
 *
 * @property integer $id
 * @property integer $roleId
 * @property integer $menu_id
 */
class RoleJurisdiction extends namespace\base\RoleJurisdiction
{
    public function setRoleJur($post) {
        $transaction = Yii::$app->db->beginTransaction();
        try{
            foreach ($post["roleList"] as $roleId) {
                RoleJurisdiction::deleteAll(["roleId" => $roleId]);
            }
            if(isset($post["selRoleJur"])){
                foreach ($post["selRoleJur"] as $roleId => $val) {
                    foreach ($val as $menuId => $v) {
                        $model = new RoleJurisdiction();
                        $model->menu_id = $menuId;
                        $model->roleId = $roleId;
                        if(!$model->save()) {
                            $transaction->rollBack();
                            return ["state" => 0, "message" => $model->getFirstErrors()];
                        }
                    }
                }
            }
            $transaction->commit();
            return ["state" => 1, "message" => "设置成功"];
        } catch (Exception $ex) {
            $transaction->rollBack();
            return ["state" => 0, "message" => $ex->getTraceAsString()];
        }
    }
    
    /**
     * 验证是否有权限访问
     * @param type $url 当前地址
     * @return boolean false 没有权限 true 有权限
     */
    public static function checkRoleJue($url) {
        $menuItem = Menu::findAll(["is_show" => 1, "depth" => [2,3], "url" => $url]);
        if(!$menuItem) {
            return true;
        }
        $menuIds = ArrayHelper::getColumn($menuItem, "id");
        $roleId = Yii::$app->user->getIdentity()->role_id;
        $roleJueItem = RoleJurisdiction::findOne(["menu_id" => $menuIds, "roleId" => $roleId]);
        return $roleJueItem ? true : false;
    }
}
