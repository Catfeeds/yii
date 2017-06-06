<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "Warehouse".
 *
 * @property integer $id
 * @property string $name
 * @property integer $type
 * @property integer $status
 * @property integer $area_id
 * @property integer $is_sale
 * @property string $num
 * @property datetime $create_time
 * @property integer $department_id
 */
class Warehouse extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'Warehouse';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name','num'], 'required'],
            [['create_time'], 'safe'],
            [['type', 'status', 'area_id', 'is_sale', 'department_id'], 'integer'],
            [['name','num'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '仓库名',
            'type' => '仓库类型',
            'status' => '仓库状态',
            'area_id' => '所属地区',
            'is_sale' => '是否可销售库区',
            'num' => '编号',
            'create_time' => '创建时间',
            'department_id' => '部门ID',
        ];
    }
}
