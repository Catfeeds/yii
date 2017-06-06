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
            [['parent_id', 'sort'], 'default', 'value' => 0],
            ['is_open', 'default', 'value' => self::IS_OPEN_NO],
            ['is_open', 'in', 'range' => [self::IS_OPEN_NO, self::IS_OPEN_YES]],
            ['is_show', 'default', 'value' => self::IS_SHOW_YES],
            ['is_show', 'in', 'range' => [self::IS_SHOW_NO, self::IS_SHOW_YES]],
        ];
    }

    /**
     * @inheritdoc
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

    public function beforeSave($insert)
    {
        if($this->parent_id){
            $this->arr_parant = join(",", self::findOne($this->parent_id)->getArrParent(true));
        }

        $this->depth = count($this->getArrParent());

        return parent::beforeSave($insert);
    }

    public function checkActive($activeClass = 'active')
    {
        $url = self::getParentUrl();

        $menu = Menu::find()->where(['url' => $url])->orderby(['depth' => SORT_DESC])->one();

        if($menu && in_array($this->id, $menu->getArrParent(true))){
            return $activeClass;
        }
    }

    public function getArrParent($self = false)
    {
        $arr_parent = explode(",", $this->arr_parant);
        if($self){
            array_push($arr_parent, $this->id);
        }
        return $arr_parent;
    }

    public function showName()
    {
        return $this->name;
    }

    public function showIcon()
    {
        return $this->icon;
    }

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

    public static function getMenus($parent_id = 0)
    {
        return Menu::find()->where(['parent_id' => $parent_id,"is_show" => 1])->orderby(['sort' => SORT_ASC])->all();
    }

    public static function getMenusByDepth($depth = 0)
    {
        $url = self::getParentUrl();
        $menu = Menu::find()->where(['url' => $url])->orderby(['depth' => SORT_DESC])->one();
        if($menu){
            $arr_parent = $menu->getArrParent();
            array_push($arr_parent, $menu->id);
            if(isset($arr_parent[$depth])){
                return self::getMenus($arr_parent[$depth]);
            }
        }

        return [];
    }
    
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

    public static function getSelectData($parent_id = 0)
    {
        $return = [];
        if(is_numeric($parent_id)){
            $datas = self::getMenus($parent_id);
            if($datas){
                $return[$parent_id] = '请选择';
                foreach($datas as $data){
                    $return[$data->id] = $data->showName();
                }
            }
        }
        return $return;
    }
    
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

    public static function getIsOpenSelect()
    {
        return [
            self::IS_OPEN_NO => '收缩',
            self::IS_OPEN_YES => '展开',
        ];
    }

    public static function getIsShowSelect()
    {
        return [
            self::IS_SHOW_NO => '禁用',
            self::IS_SHOW_YES => '启用',
        ];
    }
}
