<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "Department".
 *
 * @property integer $id
 * @property string $name
 * @property integer $parent_id
 * @property integer $status
 * @property string $create_time
 * @property string $number
 * @property string $acronym
 */
class Department extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'department';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name','number', 'parent_id', 'acronym'], 'required'],
            [['parent_id', 'status'], 'integer'],
            [['create_time'], 'safe'],
            [['name'], 'string', 'max' => 10],
            [['number'], 'string', 'max' => 10],
            [['acronym'], 'string', 'max' => 10],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '部门名称',
            'parent_id' => '上级部门',
            'status' => 'Status',
            'create_time' => 'Create Time',
            'number' => '部门编号',
            'acronym' => '部门缩写',
        ];
    }
}
