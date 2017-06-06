<?php
/**
 * Created by PhpStorm.
 * User: yidashi
 * Date: 16/3/2
 * Time: 下午8:05
 */

namespace app_web\controllers;

use Yii;
use Exception;
use yii\data\ArrayDataProvider;
use app_web\components\CController;
use libs\BackMysql;
use yii\web\Response;
use yii\helpers\Json;

/**
 * 系统基础数据 -- 数据库备份及恢复
 */
class ImportController extends CController
{
    public $dbBackConfig;
    public function init() {
        $this->dbBackConfig = Yii::$app->params["dbBack"];
        parent::init();
    }

    /**
     * 数据库备份及恢复列表页
     */
    public function actionIndex()
    {
        $path = Yii::getAlias($this->dbBackConfig['path']);
        if(!is_dir($path)){
            mkdir($path, 0755, true);
        }
        $flag = \FilesystemIterator::KEY_AS_FILENAME;
        $glob = new \FilesystemIterator($path, $flag);
        $list = array();
        foreach ($glob as $name => $file) {
            $info["time"] = date("Y-m-d H:i:s", $file->getATime());
            $info["name"] = $file->getFilename();
            $info["size"] = $file->getSize();
            $info["compress"] = $file->getExtension();
            $list[] = $info;
        }
        rsort($list);
        $dataProvider = new ArrayDataProvider([
            'allModels' => $list,
            'pagination' => [
                'pageSize' => 10,
                'validatePage' => false,
            ],
        ]);
        $listDatas = $dataProvider->getModels();
        $listPages = $dataProvider->getPagination();
        return $this->render('index', compact(['listDatas', 'listPages']));
    }
    
    /**
     * 手动备份数据库
     */
    public function actionBackup() {
        $dbBack = new BackMysql();
        $result = $dbBack->setDBName('wms');  
        if(!$result["state"]) {
            $return["message"] = $result["message"];
            return Json::encode($return);
        }
        $backupResult = $dbBack->backup();
        if($backupResult["state"]){  
            $return["error"] = 0;
            $return["message"] = '数据库备份成功！!';
            return Json::encode($return);
        }else{
            $return["message"] = $backupResult["message"];
            return Json::encode($return);
        }     
    }
    
    /**
     * 还原数据库
     */
    public function actionRecover() {
        set_time_limit(0);
        $name = \Yii::$app->request->get("name");
        $dbBack = new BackMysql();
        $result = $dbBack->recover($name);  
        if(!$result["state"]) {
            $return["message"] = $result["message"];
            return Json::encode($return);
        }
        $return["error"] = 0;
        $return["message"] = "还原成功";
        return Json::encode($return);
    }
    
    /**
     * 删除数据库备份文件
     */
    public function actionDel() {
        $name = \Yii::$app->request->get("name");
        $path = Yii::getAlias($this->dbBackConfig['path']);
        if(!file_exists($path."/".$name)) {
            $return["message"] = "错误的文件目录";
            return Json::encode($return);
        }
        $result = @unlink($path."/".$name);
        if(!$result) {
            $return["message"] = "删除失败";
            return Json::encode($return);
        }
        $return["error"] = 0;
        $return["message"] = "删除成功";
        return Json::encode($return);
    }
}