<?php

namespace app_dev\controllers;

use app_dev\models\RoleForm;
use common\models\Admin;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Controller;

class RoleController extends Controller
{

    public function actionIndex($user_id = NULL)
    {
        return $this->render('index', [
            'roles' => Yii::$app->authManager->getRoles(),
            'user_id' => $user_id
        ]);
    }

    public function actionCreate($user_id = NULL)
    {
        if(!$user_id){
            $user_id = '';
        }

        //角色表单
        $model = new RoleForm();

        if($model->load(Yii::$app->request->post()) && $model->save()){
            Yii::$app->session->addFlash('success', '角色“' . $model->name . '”创建成功');

            if($user_id){
                return $this->redirect(['item', 'user_id' => $user_id]);
            }else{
                return $this->redirect(['index']);
            }
        }else{
            return $this->render('create', [
                'model' => $model,
                'user_id' => $user_id,
            ]);
        }
    }

    public function actionUpdate($name, $user_id = NULL)
    {
        $authManager = \Yii::$app->authManager;

        $child = $authManager->getChildren($name);

        if($child){
            Yii::$app->session->addFlash('warning', '角色“' . $name . '”有用户，不能修改');

            if($user_id){
                return $this->redirect(['item', 'user_id' => $user_id]);
            }else{
                return $this->redirect(['index']);
            }
        }

        $role = $authManager->getRole($name);
        if(!$role){
            return false;
        }

        $model = new RoleForm();
        $model->name = $role->name;
        $model->description = $role->description;

        if($model->load(\Yii::$app->request->post()) && $model->update($name)){
            Yii::$app->session->addFlash('success', '角色“' . $name . '”更新成功');

            if($user_id){
                return $this->redirect(['item', 'user_id' => $user_id]);
            }else{
                return $this->redirect(['index']);
            }
        }else{
            return $this->render('update', [
                'model' => $model,
                'user_id' => $user_id
            ]);
        }
    }

    public function actionDelete($name, $user_id = NULL)
    {
        $authManager = \Yii::$app->authManager;

        $child = $authManager->getChildren($name);

        if($child){
            Yii::$app->session->addFlash('warning', '节点有子角色，不能删除');

            if($user_id){
                return $this->redirect(['item', 'user_id' => $user_id]);
            }else{
                return $this->redirect(['index']);
            }
        }

        $role = $authManager->getRole($name);
        if(!$role){
            return false;
        }

        if($authManager->remove($role)){
            Yii::$app->session->addFlash('success', '成功删除角色“' . $name . '”');

            if($user_id){
                return $this->redirect(['item', 'user_id' => $user_id]);
            }else{
                return $this->redirect(['index']);
            }
        }else{
            Yii::$app->session->addFlash('error', '角色“' . $name . '”删除失败');

            if($user_id){
                return $this->redirect(['item', 'user_id' => $user_id]);
            }else{
                return $this->redirect(['index']);
            }
        }
    }

    public function actionAuth($name, $user_id = NULL)
    {
        $authManager = \Yii::$app->authManager;

        $role = $authManager->getRole($name);
        if(!$role){
            Yii::$app->session->addFlash('warning', '节点未找到');

            return $this->goBack();
        }

        if(Yii::$app->request->getIsPost()){
            $auths = Yii::$app->request->post('auth');

            $authManager->removeChildren($role);

            foreach($auths as $auth){
                $auth = $authManager->getPermission($auth);
                if(!$auth){
                    continue;
                }
                $authManager->addChild($role, $auth);
            }

            if($user_id){
                return $this->redirect(['admin/index']);
            }else{
                return $this->redirect(['index']);
            }
        }

        $roleNodes = $authManager->getPermissionsByRole($name);
        $roleNodes = array_keys($roleNodes);
        $auths = $authManager->getPermissions();

        return $this->render('auth', [
            'auths' => $auths,
            'roleNodes' => $roleNodes,
            'name' => $name,
            'user_id' => $user_id
        ]);
    }

    public function actionItem($user_id = NULL)
    {
        $admin = Admin::findOne($user_id);
        if(!$admin){
            Yii::$app->session->addFlash('warning', '找不到账户');

            return $this->goBack();
        }

        $authManager = Yii::$app->authManager;

        if(Yii::$app->request->getIsPost()){
            $roles = Yii::$app->request->post('roles');

            $authManager->revokeAll($user_id);

            if(!empty($roles) && is_array($roles)){
                foreach($roles as $role){
                    $role = $authManager->getRole($role);
                    if(!$role){
                        continue;
                    }
                    $authManager->assign($role, $user_id);
                }
            }

            $admin->role = is_array($roles) ? join(",", $roles) : $roles;

            if($admin->save()){
                Yii::$app->session->addFlash('success', '更新成功');

                return $this->redirect(['admin/index']);
            }else{
                Yii::$app->session->addFlash('error', '更新失败');

                return $this->goBack();
            }
        }

        $userRoles = $authManager->getRolesByUser($user_id);
        $userRoles = ArrayHelper::getColumn(ArrayHelper::toArray($userRoles), 'name');
        $roles = $authManager->getRoles();

        return $this->render('item', ['roles' => $roles, 'userRoles' => $userRoles, 'user_id' => $user_id]);
    }
}
