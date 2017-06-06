<?php

namespace common\models;

use Yii;
use yii\helpers\Url;

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
class Menu extends namespace\base\Menu
{
    const IS_OPEN_NO= 0; // 收缩
    const IS_OPEN_YES = 1; // 展开

    const IS_SHOW_NO= 0; // 禁用
    const IS_SHOW_YES = 1; // 启用

    /**
     * 字段规则
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
            [['parent_id', 'sort'], 'default', 'value' => 0],
            ['is_open', 'default', 'value' => self::IS_OPEN_NO],
            ['is_open', 'in', 'range' => [self::IS_OPEN_NO, self::IS_OPEN_YES]],
            ['is_show', 'default', 'value' => self::IS_SHOW_YES],
            ['is_show', 'in', 'range' => [self::IS_SHOW_NO, self::IS_SHOW_YES]],
        ];
    }

    /**
     * 设置字段别名
     */
    public function attributeLabels()
    {
        return [
            'id' => '菜单 ID',
            'parent_id' => '父级菜单',
            'depth' => '层级',
            'arr_parant' => '父级链',
            'name' => '菜单名',
            'url' => '菜单指向链接',
            'params' => '地址参数',
            'icon' => 'Icon 样式名',
            'auth' => '所需权限',
            'is_open' => '是否默认开启',
            'is_show' => '是否显示',
            'sort' => '排序',
        ];
    }

    /**
     * 保存默认值
     */
    public function beforeSave($insert)
    {
        if($this->parent_id){
            $this->arr_parant = join(",", self::findOne($this->parent_id)->getArrParent(true));
        }
        $this->depth = count($this->getArrParent());
        return parent::beforeSave($insert);
    }

    /**
     * 验证是否选中并返回样式
     * @param type $activeClass
     * @return type
     */
    public function checkActive($activeClass = 'active')
    {
        $url = self::getParentUrl();
        $menu = Menu::find()->where(['url' => $url])->orderby(['depth' => SORT_DESC])->one();
        if($menu && in_array($this->id, $menu->getArrParent(true))){
            return $activeClass;
        }
    }

    /**
     * 获取父类连接
     * @param type $self
     * @return type
     */
    public function getArrParent($self = false)
    {
        $arr_parent = explode(",", $this->arr_parant);
        if($self){
            array_push($arr_parent, $this->id);
        }
        return $arr_parent;
    }

    /**
     * 展示名称
     */
    public function showName()
    {
        return $this->name;
    }

    /**
     * 展示图标
     */
    public function showIcon()
    {
        return $this->icon;
    }

    /**
     * 展示连接
     */
    public function showUrl()
    {
        if($this->url){
            if(substr($this->url, 0, 4) != 'http'){
                $url[] = $this->url;
                if($this->params){
                    parse_str($this->params, $params);
                    $url = array_merge($url, $params);
                }
            }else{
                $url = $this->url;
            }
        }else{
            $url = 'javascript:void(0)';
        }
        return Url::to($url);
    }

    /**
     * 获取父类获取子类记录
     * @param type $parent_id 父类ID
     * @return type
     */
    public static function getMenus($parent_id = 0)
    {
        return Menu::find()->where(['parent_id' => $parent_id,"is_show" => 1])->orderby(['sort' => SORT_ASC])->all();
    }

    /**
     * 获取整层所有菜单
     * @param type $depth 楼层
     * @return type
     */
    public static function getMenusByDepth($depth = 0)
    {
        $url = self::getParentUrl();//得到对应父类的地址
        $menu = Menu::find()->where(['url' => $url])->orderby(['depth' => SORT_DESC])->one();
        if($menu){
            $arr_parent = $menu->getArrParent();//获取父类链接  0,2
            array_push($arr_parent,$menu->id);
            if(isset($arr_parent[$depth])){
                return self::getMenus($arr_parent[$depth]);  //获取当前行的所有相应的标签
            }
        }
        return [];
    }
    
    /**
     * 获取当前访问地址的父类地址
     */
    private static function getParentUrl() {
        $controller = Yii::$app->controller->id;
        $action = Yii::$app->controller->action->id;
        if(in_array($action, ['info', 'create', 'add', 'addorupdate', 'update', 'addexception', 'setnum', 'addroutine', 'addedit', 'editpwd', 'edit', 'finish'])){
            $action = 'index' ;
        }
        $url = $controller . "/" . $action;
        if($url =='pstock/check'){
            $url = "wcheck/index";
        }else if($url =='pstock/checkout'|| $url =='wcheckout/addroutine'){
            $url = "wcheckout/index";
        }else if($url =='pstock/wastage'){
            $url = "wwastage/index";
        }else if($url =='pstock/transferdep'){
            $url = "wtransferdep/index";
        }else if($url =='pstock/transfer'){
            $url = "wtransfer/index";
        }else if($url =='pstock/back'){
            $url = "wback/index";
        }else if($url =='wbuying/finish'){
            $url = "wbuying/index";
        }else if($url =='invoicing/checkinfo'){
            $url = "invoicing/check";
        }else if($url =='pstock/invoicingsale'){
            $url = "invoicingsale/index";
        }else if($url =='checkplanning/proof'){
            $url = "checkproof/index";
        }else if($url =='departmentcheckplanning/proof'){
            $url = "departmentcheckproof/index";
        }else if($url =='warehousecheckplanning/proof'){
            $url = "warehousecheckplanning/index";
        }else if($url =='productupdate/loginfo'){
            $url = "productupdate/log";
        }else if($url =='system/setcompanyinfo'){
            $url = "system/default";
        }
        return $url;
    }
    
    /**
     * 获取父类下所有子类的列表
     * @param type $parent_id 父类ID
     * @return type
     */
    public static function getAllSelectData($parent_id = 0)
    {
        $return = [];
        $datas =  Menu::find()->all();
        if($datas){
            $return[$parent_id] = '请选择';
            foreach($datas as $data){
                $return[$data->id] = $data->showName();
            }
        }
        return $return;
    }

    /**
     * 获取样式列表
     */
    public static function getIsOpenSelect()
    {
        return [
            self::IS_OPEN_NO => '收缩',
            self::IS_OPEN_YES => '展开',
        ];
    }

    /**
     * 获取状态列表
     */
    public static function getIsShowSelect()
    {
        return [
            self::IS_SHOW_NO => '禁用',
            self::IS_SHOW_YES => '启用',
        ];
    }
}
