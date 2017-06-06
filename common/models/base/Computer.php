<?php

namespace common\models\base;

use Yii;


/**
 * This is the model class for table "Computer".
 *
 * @property integer $id
 * @property string $name
 * @property string $num
 * @property string $level
 * @property integer $status
 * @property string $create_time
 * @property integer $role_id
 */
class Computer extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'computer';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'mac', 'role_id'], 'required'],
            [['type', 'role_id', 'position', 'status'], 'integer'],
            [['name'], 'string', 'max' => 40],
            [['mac'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '计算机名称',
            'mac' => 'mac地址',
            'type' => '类型',
            'position' => '位置',
            'status' => '状态',
            'role_id' => '所属角色',
        ];
    }
}
