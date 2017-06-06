<?php

namespace libs;

use common\models\Supplier;
use Yii;
use moonland\phpexcel\Excel;
use common\models\BusinessAll;
use libs\common\Flow;

/**
 * 通用工具类
 */
class Utils
{

    public static function debug($data, $end = true)
    {
        if(Yii::$app->request->get('debug')){
            return self::dump($data, $end);
        }
    }

    public static function dump($data, $end = true)
    {
        echo '<pre>';
        print_r($data);
        echo '</pre>';

        if($end){
            die();
        }
    }

    public static function exportImport($model, $file)
    {
        $datas = Excel::import($file);
        $model = "\\common\\models\\" . ucfirst($model);
        $primaryKey = Supplier::primaryKey();

        foreach($datas as $data){
            $isNew = false;

            $primaryWhere = [];
            foreach($primaryKey as $key){
                if(!isset($data[$key])){
                    $isNew = true;
                    break;
                }else{
                    $primaryWhere[$key] = $data[$key];
                }
            }

            if($isNew){
                $item = new $model;
            }else{
                $item = $model::find()->where($primaryWhere)->one();
                if(!$item){
                    $item = new $model;
                }
            }

            $item->setAttributes($data);
            if(!$item->save()){
                Yii::warning(print_r($item, true));
            }
        }

        self::delFile($file);
    }

    /**
     * 验证上传excel的格式
     * @param type $type
     * @return type
     */
    public static function checkExcelType($type) {
        if(in_array($type, ["application/vnd.ms-excel", "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet", "application/octet-stream"])) {
            return ["state" => 1];
        }
        return ["state" => 0, "message" => "上传文件格式错误"];
    }

    /**
     * 验证时间类型
     * @param type $time
     * @return type
     */
    public static function checkTimeType($time) {
        $entry_date = strtotime($time);
        if(strstr("-", $time)){
            $date = explode('-',$time);
            if(count($date) == 3) {
                $entry_date = "20".$date[2]."-".$date[0]."-".$date[1];
            }
        } else {
            $entry_date = date("Y-m-d", $entry_date);
        }
        return $entry_date;
    }

    public static function exportExcel($modelName, $fileName = '')
    {
        if(!$fileName) $fileName = $modelName;
        $model = "\\common\\models\\" . ucfirst($modelName);
        $datas = $model::find()->all();
        $columns = [];
        $labels = (new $model())->attributeLabels();
        foreach($labels as $field => $label){
            $columns[] = [
                'attribute' => $field,
                'header' => $field,
            ];
        }

        Excel::export([
            'models' => $datas,
            'columns' => $columns,
            'fileName' => $fileName . ".xlsx",
        ]);
    }
    
    /**
     * 导出Excel 
     * @param array $datas 数据
     * @param array $columns 头部
     * @param string $fileName 文件名
     */
    public static function downloadExcel($datas, $columns, $fileName)
    {
        Excel::export([
            'models' => $datas,
            'columns' => $columns,
            'fileName' => iconv("UTF-8","GBK",$fileName) . ".xlsx",
        ]);
    }

    public static function getImage($image)
    {
        $file = self::getFile($image);
        if(!$image || !file_exists($file)){
            $image = 'image/default.jpg';
        }

        return Yii::getAlias('@web/' . $image);
    }

    public static function getFile($file)
    {
        return Yii::getAlias('@webroot/' . $file);
    }

    /**
     * 生成文件名称
     *
     * @param string $ext 后缀
     * @param bool   $rand
     *
     * @return string
     */
    public static function newFileName($ext, $rand = false)
    {
        if($rand){
            $fix = rand(1000, 9999);
        }else{
            list($usec, $sec) = explode(" ", microtime());
            $fix = substr($usec, 2, 4);
        }

        return 'upload/' . date('YmdHis') . $fix . "." . ltrim($ext, ".");
    }

    /**
     * 遍历生成目录
     *
     * @param string $dirpath 要生成的目录路径
     *
     * @return string
     */
    public static function mkdir($dirpath)
    {
        $root = Yii::getAlias('@webroot') . DS;
        $root = preg_replace('/[\\\\\/]/', DS, $root);
        $dirpath = preg_replace('/[\\\\\/]/', DS, $dirpath);
        if($dirpath != $root && !file_exists($dirpath)){
            $path = explode(DS, str_replace($root, '', $dirpath));
            $dirpath = $root . array_shift($path);
            if(!file_exists($dirpath)){
                @mkdir($dirpath);
                @chmod($dirpath, 0777);
            }

            foreach($path as $dir){
                $dirpath .= DS . $dir;

                if($dir != '.' && $dir != '..'){
                    if(!file_exists($dirpath)){
                        @mkdir($dirpath);
                        @chmod($dirpath, 0777);
                    }
                }
            }
        }

        return $dirpath;
    }

    /**
     * 遍历删除目录以及其所有子目录和文件
     *
     * @param string $folder 要删除的目录路径
     *
     * @return bool
     */
    public static function rmdir($folder)
    {
        set_time_limit(0);
        if(!file_exists($folder)){
            return false;
        }
        $files = array_diff(scandir($folder), array('.','..'));
        foreach ($files as $file) {
            $file = $folder . DS . $file;
            (is_dir($file) && !is_link($folder)) ? self::rmdir($file) : unlink($file);
        }
        return rmdir($folder);
    }

    /**
     * 将文件保存
     *
     * @param string $file 目标文件
     * @param string $source 源文件
     *
     * @return boolean
     */
    public static function saveFile($file, $source)
    {
        if(@copy($source, $file)){
            return true;
        }else{
            if(function_exists('move_uploaded_file') && @move_uploaded_file($source, $file)){
                return true;
            }else{
                if(@is_readable($source) && (@$fp_s = fopen($source, 'rb')) && (@$fp_t = fopen($file, 'wb'))){

                    while(!feof($fp_s)){
                        $s = @fread($fp_s, 1024 * 512);
                        @fwrite($fp_t, $s);
                    }

                    fclose($fp_s);
                    fclose($fp_t);

                    return true;
                }else{
                    return false;
                }
            }
        }
    }

    /**
     * 删除文件
     *
     * @param string  $file 要删除的文件路径
     *
     * @return boolean
     */
    public static function delFile($file)
    {
        return @unlink($file);
    }

    /**
     * 生成随机字符串
     *
     * @param integer $len 要获得的随机字符串长度
     * @param bool    $onlyNum
     *
     * @return string
     */
    public static function getRand($len = 12, $onlyNum = false)
    {
        $chars = $onlyNum ? '0123456789' : 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        mt_srand((double)microtime() * 1000000 * getmypid());
        $return = '';
        while(strlen($return) < $len){
            $return .= substr($chars, (mt_rand() % strlen($chars)), 1);
        }

        return $return;
    }

    /**
     * UTF8 字符串截取
     *
     * @param string  $str 要截取的字符串
     * @param integer $start 截取起始位置
     * @param integer $len 截取长度
     *
     * @return string
     */
    public static function substr($str, $start = 0, $len = 50)
    {
        return mb_strlen($str) > $len ? mb_substr($str, $start, $len, 'UTF-8') . "…" : $str;
    }
    
    /**
     * 生成随机单号
     * @param type $type 业务类型
     * @return type
     */
    public static function generateSn($type)
    {
        $num = BusinessAll::findNum($type);
        switch ($type){
            case Flow::TYPE_PLANNING:
            case Flow::TYPE_PLANNING_ROUTINE:
            case Flow::TYPE_PLANNING_EXCEPTION:
                $title = "Caig";
                break;
            case Flow::TYPE_ORDER:
                $title = "Dingdxd";
                break;
            case Flow::TYPE_BUYING:
                $title = "Ruk";
                break;
            case Flow::TYPE_BACK:
                $title = "Tuic";
                break;
            case Flow::TYPE_CHECKOUT:
                $title = "Chuk";
                break;
            case Flow::TYPE_TRANSFEFDEP:
                $title = "Zhuanh";
                break;
            case Flow::TYPE_TRANSFEF:
                $title = "Diaoc";
                break;
            case Flow::TYPE_MATERIALRETURN:
                $title = "Tuih";
                break;
            case Flow::TYPE_WASTAGE:
                $title = "Haos";
                break;
            case Flow::TYPE_ADDPRODUCT:
                $title = "Wulsjlr";
                break;
            case Flow::TYPE_ORDER_FINANCE:
                $title = "Zhif";
                break;
            case Flow::TYPE_SALE:
                $title = "Xiaocrz";
                break;
//            case Flow::TYPE_FUND:
//                $title = "Xiaocrz";
//                break;
            case Flow::TYPE_ABNORMAL_FUND:
                $title = "Feictzj";
                break;
            case Flow::TYPE_ORDER_MATERIAL:
                $title = "Shoukjl";
                break;
            case Flow::TYPE_CHECK_PLANNING:
            case Flow::TYPE_CHECK_DEPARTMENT:
            case Flow::TYPE_CHECK_WAREHOUSE:
                $title = "Pandjh";
                break;
            case Flow::TYPE_CHECK_PLANNING_PROOF:
            case Flow::TYPE_CHECK_DEPARTMENT_PROOF:
            case Flow::TYPE_CHECK_WAREHOUSE_PROOF:
                $title = "Pandkc";
                break;
            case Flow::TYPE_PRODUCT_UPDATE:
                $title = "Wulsjxg";
                break;
            case Flow::TYPE_SALE_CHECK:
                $title = "Xiaochs";
                break;
            default :
                $title = "Weiz";
                break;
        }
        return $title.date("Ymd").sprintf("%03d", ($num+1));
    }
}


?>