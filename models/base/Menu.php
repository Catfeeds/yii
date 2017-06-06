<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "Menu".
 *
 * @property string $id
 * @property string $parent_id
 * @property integer $depth
 * @property string $arr_parant
 * @property string $name
 * @property string $url
 * @property string $params
 * @property string $icon
 * @property string $auth
 * @property integer $is_open
 * @property integer $is_show
 * @property string $sort
 */
class Menu extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'Menu';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['parent_id', 'depth', 'is_open', 'is_show', 'sort'], 'integer'],
            [['name'], 'required'],
            [['arr_parant', 'url', 'params'], 'string', 'max' => 255],
            [['name'], 'string', 'max' => 45],
            [['icon'], 'string', 'max' => 120],
            [['auth'], 'string', 'max' => 64],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '菜单 ID',
            'parent_id' => '父级菜单

最多3级',
            'depth' => '层级',
            'arr_parant' => '父级链',
            'name' => '菜单名',
            'url' => '菜单指向链接

不为空表示为最终菜单
为空时表示为菜单节点',
            'params' => '地址参数',
            'icon' => '菜单 Icon 样式名',
            'auth' => '所需权限

填最上一级权限',
            'is_open' => '是否默认开启
0: 不开启
1: 开启',
            'is_show' => '是否显示
0: 不显示
1: 显示',
            'sort' => '排序',
        ];
    }
}
