<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "Role".
 *
 * @property integer $id
 * @property string $name
 * @property integer $department_id
 * @property integer $status
 * @property integer $is_sole
 */
class Role extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'role';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'department_id'], 'required'],
            [['department_id', 'status', 'is_sole'], 'integer'],
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
            'name' => '角色名称',
            'department_id' => '部门ID',
            'status' => '0 无效 1有效 99删除',
            'is_sole' => '是否唯一',
        ];
    }
}
