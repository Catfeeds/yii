<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "Area".
 *
 * @property integer $id
 * @property string $name
 * @property integer $parentId
 * @property integer $status
 * @property integer $sort
 */
class Area extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'area';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'parentId', 'sort'], 'required'],
            [['parentId', 'status', 'sort'], 'integer'],
            [['name'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '地区名称',
            'parentId' => '父类ID',
            'status' => 'Status',
            'sort' => 'Sort',
        ];
    }
}
