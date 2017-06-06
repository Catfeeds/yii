<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\BaseActiveRecord;
use yii\db\Expression;
use Exception;
use yii\web\UploadedFile;
use libs\common\Flow;
use libs\Utils;
use common\models\AdminLog;

/**
 * This is the model class for table "Supplier".
 *
 * @property integer $id
 * @property string $set_name
 * @property string $set_value
 * @property string $set_desc
 * @property integer $group_id
 */
class Config extends namespace\base\Config
{
	public $admin_ver_password;
	public $admin_ver_password1;
	public $admin_ver_password2;
	public $admin_business_password;
	public $admin_business_password1;
	public $admin_business_password2;
	public $business_business_password;
	public $business_business_password1;
	public $business_business_password2;
	public $flow_business_password;
	public $flow_business_password1;
	public $flow_business_password2;
    public function rules()
    {
        return [
            [['set_name', 'set_value'], 'required'],
            [['group_id'], 'integer'],
            [['set_desc', 'set_value'], 'string', 'max' => 255],
            [['group_id'], 'default', 'value' => 0],
            [['admin_ver_password', 'admin_ver_password1','admin_ver_password2'], 'string', 'max' => 255],
            [['admin_business_password', 'admin_business_password1','admin_business_password2'], 'string', 'max' => 255],
            [['business_business_password', 'business_business_password1','business_business_password2'], 'string', 'max' => 255],
            [['flow_business_password', 'flow_business_password1','flow_business_password2'], 'string', 'max' => 255],
            ['set_name', 'unique', 'targetClass' => '\common\models\Config', 'message' => '名称已经已存在！'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'set_name' => '设置名称',
            'set_value' => '设置值',
            'set_desc' => '描述',
            'group_id' => '分组 1 logo 2名称 3. 系统密码 ',
        ];
    }
    
   public function beforeSave($insert) {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
               
            }
            $this->set_name = strip_tags($this->set_name);
            $this->set_desc = strip_tags($this->set_desc);
            $this->set_value = strip_tags($this->set_value);
            return true;
        }
        return false;
    }

    public function showName()
    {
        return $this->name;
    }

    public function setCompanyInfo($post) {
        $transaction = Yii::$app->db->beginTransaction();
        try{
            if(!$post["company"]) {
                $message = "公司名称不能为空";
                throw new Exception($message);
            }
            $companyModel = new Config();
            $companyModel->set_name = 'company';
            $companyModel->set_desc = '公司名称';
            $companyModel->group_id = 2;
            $companyModel->set_value = $post["company"];
            if(!$companyModel->save()) {
                $message = $companyModel->getFirstErrors();
                throw new Exception(reset($message));
            }
            $logoModel = new Config();
            $logoModel->set_name = 'logo';
            $logoModel->set_desc = 'logo';
            $logoModel->group_id = 1;
            $logoModel->set_value = $post["Config"]["logo"];
            $logo = UploadedFile::getInstance($logoModel, 'set_value');
            if(!$logo) {
                $message = '请选择图片！';
                throw new Exception($message);
            }
            if(!in_array($logo->type, array("image/png", "image/jpg", "image/jpeg"))) {
                $message = '文件格式错误，请上传“.jpg .jpge .png”格式图片！';
                throw new Exception($message);
            }
            $logoModel->set_value = Utils::newFileName($logo->getExtension());
            if(!$logoModel->set_value){
                $message = "网络异常，Logo上传失败";
                throw new Exception($message);
            }
            if(!$logoModel->save()) {
                $message = $logoModel->getFirstErrors();
                throw new Exception(reset($message));
            }
            $logo->saveAs(Utils::getFile($logoModel->set_value));
            Yii::$app->cache->set('logo', $logoModel->set_value);
            Yii::$app->cache->set('company', $companyModel->set_value);
            Yii::$app->session->set("setCompanyInfo", true);
            AdminLog::addLog("set_companyinfo", "设置公司名称和Logo");
            $transaction->commit();
            return ["state" => 1];
        } catch (Exception $ex) {
            $transaction->rollBack();
            return ["state" => 0, "message" => $ex->getMessage()];
        }
    }
    
    public function setAllConfig($post) {
        $transaction = \Yii::$app->db->beginTransaction();
        try{
            $commonModel = Config::findOne(["set_name" => "commonSet"]);
            if(!$commonModel) {
                $commonModel = new Config();
            }
            $commonModel->set_name = "commonSet";
            $commonModel->set_value = $post["commonSet"];
            $commonModel->set_desc = "11";
            if(!$commonModel->validate()) {
                $message = $commonModel->getFirstErrors();
                throw new Exception(reset($message));
            }
            $commonModel->save();
            foreach(Flow::getTypeSelectData() as $type => $name){
                if(!is_numeric($post["flowType"][$type]) || $post["flowType"][$type] <= 0) {
                    throw new Exception($name."的天数必须大于零");
                }                
                $model = Config::findOne(["set_name" => "flowType_".$type]);
                if(!$model) {
                    $model = new Config();
                }
                $model->set_name = "flowType_".$type;
                $model->set_value = $post["flowType"][$type];
                $model->set_desc = "11";
                if(!$model->validate()) {
                    $message = $model->getFirstErrors();
                    throw new Exception(reset($message));
                }
                $model->save();
            }
            AdminLog::addLog("set_configAll", "设置配置数据");
            $transaction->commit();
            return ["state" => 1];
        } catch (Exception $ex) {
            $transaction->rollBack();
            return ["state" => 0, "message" => $ex->getMessage()];
        }
    }

}
