<?php
namespace frontend\models;

use common\models\Brand;
use common\models\Category;
use common\models\Company;
use common\models\UserCompany;
use libs\Utils;
use yii\base\ErrorException;
use yii\base\Model;
use Yii;

/**
 * Brand form
 */
class UploadForm extends Model
{
    public $id;
    public $name = '';
    public $categoryId = 0;
    public $areaId = 0;
    public $address = '';
    public $logo = '';
    public $intro = '';
    public $bidding = '';
    public $autoWithdraw = 0;
    public $tag = '';

    private $brand;

    private static $_bidding = ['建筑招标', '室内设计', '采购招标', '家具招标', '配饰招标', '灯具招标', '卫浴招标', '厨具招标', '面料招标', '涂料招标', '门窗招标', '环境招标', '建筑施工', '建材招标'];

    public function rules()
    {
        return [
            ['name', 'filter', 'filter' => 'trim'],
            ['name', 'required', 'message' => '请填写品牌名称'],
            ['name', 'string', 'min' => 2, 'message' => '品牌名称不得少于2个字'],

            ['categoryId', 'required', 'message' => '请选择产品类别'],

            [['id', 'areaId', 'address', 'name', 'categoryId', 'logo', 'intro', 'bidding', 'autoWithdraw', 'tag'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => '品牌名称',
            'areaId' => '所在地区',
            'address' => '详细地址',
            'categoryId' => '品牌类别',
            'logo' => '品牌标志',
            'intro' => '品牌说明',
            'bidding' => '可参与招标方向',
            'autoWithdraw' => '自动充值',
            'tag' => '关键词',
        ];
    }

    public function setDatas($brand)
    {
        $this->setAttributes($brand->getAttributes());

        $this->brand = $brand;
    }

    public function create()
    {
        $this->logo = Utils::coverBufferImage($this->logo);

        $brand = new Company;
        $brand->userId = Yii::$app->user->id;
        $brand->name = $this->name;
        $brand->areaId = $this->areaId;
        $brand->address = $this->address;
        $brand->categoryId = $this->categoryId;
        $brand->logo = $this->logo;
        $brand->intro = $this->intro;
        $brand->bidding = is_array($this->bidding) ? join(",", $this->bidding) : $this->bidding;
        $brand->autoWithdraw = $this->autoWithdraw;
        $brand->tag = is_array($this->tag) ? join(",", $this->tag) : $this->tag;
        $brand->status = 1;
        if(!$brand->save()){
            Yii::error($brand->getErrors());
            die();
        }

        $userCompany = new UserCompany();
        $userCompany->userId = Yii::$app->user->id;
        $userCompany->companyId = $brand->id;
        $userCompany->ownerId = $brand->userId;

        if(Yii::$app->user->identity->companyId == 0){
            Yii::$app->user->identity->companyId = $brand->id;
            Yii::$app->user->identity->position = '负责人';

            $userCompany->owner = UserCompany::OWNER_MANAGE;
        }else{
            $userCompany->owner = UserCompany::OWNER_ESCROW;
        }

        $userCompany->save();

        Utils::clearBuffer();
    }

    public function modify()
    {
        $this->logo = Utils::coverBufferImage($this->logo);

        $brand = $this->brand;
        $brand->name = $this->name;
        $brand->areaId = $this->areaId;
        $brand->address = $this->address;
        $brand->categoryId = $this->categoryId;
        $brand->logo = $this->logo;
        $brand->intro = $this->intro;
        $brand->bidding = is_array($this->bidding) ? join(",", $this->bidding) : $this->bidding;
        $brand->autoWithdraw = $this->autoWithdraw;
        $brand->tag = is_array($this->tag) ? join(",", $this->tag) : $this->tag;
        $brand->save();

        Utils::clearBuffer();
    }

    public static function getBidding()
    {
        return self::$_bidding;
    }

    public static function getCategory($parentId = 0)
    {
        return Category::getSelectData('company', $parentId);
    }

    public static function getAutoWithdraw()
    {
        return [
            Company::AUTO_WITHDRAW_NO => '禁用自动充值',
            Company::AUTO_WITHDRAW_OK => '启用自动充值',
        ];
    }
}