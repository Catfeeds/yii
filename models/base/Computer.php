<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "Computer".
 *
 * @property integer $id
 * @property string $name
 * @property string $mac
 */
class Computer extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'Computer';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'mac'], 'required'],
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
        ];
    }
}
