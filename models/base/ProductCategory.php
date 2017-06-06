<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "ProductCategory".
 *
 * @property string $id
 * @property string $parent_id
 * @property string $name
 * @property string $slug
 * @property integer $factor
 * @property integer $status
 * @property string $sort
 * @property integer $is_batches
 */
class ProductCategory extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ProductCategory';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['parent_id', 'factor', 'status', 'sort', 'is_batches'], 'integer'],
            [['name', 'factor','is_batches'], 'required'],
            [['name', 'slug'], 'string', 'max' => 60],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'parent_id' => 'Parent ID',
            'name' => '分类名称',
            'slug' => 'Slug',
            'factor' => '定价系数',
            'status' => 'Status',
            'sort' => 'Sort',
            'is_batches' => '是否需要批次号 【0：不需要 1：需要】',
        ];
    }
}
