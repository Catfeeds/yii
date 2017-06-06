<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "RoleJurisdiction".
 *
 * @property integer $id
 * @property integer $roleId
 * @property integer $menu_id
 */
class RoleJurisdiction extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'rolejurisdiction';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['roleId', 'menu_id'], 'required'],
            [['roleId', 'menu_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'roleId' => 'Role ID',
            'menu_id' => 'Menu ID',
        ];
    }
}
