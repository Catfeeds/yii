<?php

namespace app_dev\models;

use Yii;
use yii\base\Model;
use yii\helpers\Html;

class RoleForm extends Model
{
    public $name;
    public $description;

    public function rules()
    {
        return [
            [['name'], 'string', 'max' => 20],
            [['name', 'description'], 'required'],
            [
                'description',
                'filter',
                'filter' => function ($value){
                    return Html::encode($value);
                }
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => '角色名称',
            'description' => '角色描述',
        ];
    }

    public function save()
    {
        if($this->validate()){
            $authManager = Yii::$app->authManager;
            
            $role = $authManager->createRole($this->name);
            $role->description = $this->description;
            
            $authManager->add($role);

            return true;
        }else{
            return false;
        }
    }

    public function update($name)
    {
        if($this->validate()){
            $authManager = Yii::$app->authManager;
            
            $role = $authManager->getRole($name);
            if(!$role){
                return false;
            }
            
            $authManager->remove($role);

            $role = $authManager->createRole($this->name);
            $role->description = $this->description;
            $authManager->add($role);

            return true;
        }

        return false;
    }

}