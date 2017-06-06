<?php
namespace libs;

use Yii;
use yii\helpers\ArrayHelper;
use Exception;
/**
 * 功能描述:数据备份+还原操作
 */
class BackMysql {

    private $link;
    private $config;
    private $content;
    private $dbname = array();

    const DIR_SEP = DIRECTORY_SEPARATOR; //操作系统的目录分隔符

    public function __construct() {//初始化相关属性
        header("Content-type: text/html;charset=utf-8");
        $this->config = Yii::$app->params["dbBack"];
        $result = $this->connect();
        if(!$result["state"]) {
            return $result;
        }
    }

    /*
     * 连接数据库
     */
    private function connect() {
        $this->link = mysqli_connect($this->config['host'], $this->config['username'], $this->config['userPassword']);
        if ($this->link) {
            mysqli_query($this->link, "SET NAMES '{$this->config['charset']}'");
            mysqli_query($this->link, "set interactive_timeout=24*3600");
        } else {
            return ["state" => 0, "message" => "无法连接到数据库"];
        }
        return ["state" => 1];
    }

    /*
     * 设置欲备份的数据库
     * @param string $dbname 数据库名(支持多个参数.默认为全部的数据库)
     */
    public function setdbname($dbname = '*') {
        if ($dbname == '*') {
            $rs = mysql_list_dbs();
            $rows = mysql_num_rows($rs);
            if ($rows) {
                for ($i = 0; $i < $rows; $i++) {
                    $dbname = mysql_tablename($rs, $i);
                    //这些数据库不需要备份
                    $block = array('information_schema', 'mysql');
                    if (!in_array($dbname, $block)) {
                        $this->dbname[] = $dbname;
                    }
                }
            } else {
                return ["state" => 0, "message" => "没有任何数据库"];
            }
        } else {
            $this->dbname = func_get_args();
        }
        return ["state" => 1];
    }

    /*
     * 获取备份文件
     * @param string $fileName 文件名
     */
    private function getfile($fileName) {
        $this->content = '';
        $fileName = $this->trimPath(Yii::getAlias($this->config['path']) . self::DIR_SEP . $fileName);
        if (is_file($fileName)) {
            $ext = strrchr($fileName, '.');
            if ($ext == '.sql') {
                $this->content = file_get_contents($fileName);
            } elseif ($ext == '.gz') {
                $this->content = implode('', gzfile($fileName));
            } else {
                return ["state" => 0, "message" => "无法识别的文件格式"];
            }
        } else {
            return ["state" => 0, "message" => "文件不存在"];
        }
        return ["state" => 1];
    }

    /*
     * 备份文件
     */
    private function setFile() {
        $recognize = '';
        $recognize = implode('_', $this->dbname);
        $fileName = $this->trimPath(Yii::getAlias($this->config['path']) . self::DIR_SEP . $recognize . '_' . date('Ymd')."_".  rand(10, 99) . '.sql');
        $path = $this->setPath($fileName);
        if ($path !== true) {
            return ["state" => 0, "message" => "无法创建备份目录目录 '$path'"];
        }
        if ($this->config['isCompress'] == 0) {
            if (!file_put_contents($fileName, $this->content, LOCK_EX)) {
                return ["state" => 0, "message" => "写入文件失败,请检查磁盘空间或者权限!"];
            }
        } else {
            if (function_exists('gzwrite')) {
                $fileName .= '.gz';
                if ($gz = gzopen($fileName, 'wb')) {
                    gzwrite($gz, $this->content);
                    gzclose($gz);
                } else {
                    return ["state" => 0, "message" => "写入文件失败,请检查磁盘空间或者权限!"];
                }
            } else {
                return ["state" => 0, "message" => "没有开启gzip扩展"];
            }
        }
        if ($this->config['isDownload']) {
            $this->downloadFile($fileName);
        }
        return ["state" => 1];
    }

    /*
     * 将路径修正为适合操作系统的形式
     * @param  string $path 路径名称
     */
    private function trimPath($path) {
        return str_replace(array('/', '\\', '//', '\\\\'), self::DIR_SEP, $path);
    }

    /*
     * 设置并创建目录
     * @param $fileName 路径
     */
    private function setPath($fileName) {
        $dirs = explode(self::DIR_SEP, dirname($fileName));
        $tmp = '';
        foreach ($dirs as $dir) {
            $tmp .= $dir . self::DIR_SEP;
            if (!file_exists($tmp) && !@mkdir($tmp, 0777))
                return $tmp;
        }
        return true;
    }

    /*
     * 下载文件
     * @param string $fileName 路径
     */
    private function downloadFile($fileName) {
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Length: ' . filesize($fileName));
        header('Content-Disposition: attachment; filename=' . basename($fileName));
        readfile($fileName);
    }

    /*
     * 给表名或者数据库名加上``
     * @param string $str
     */
    private function backquote($str) {
        return "`{$str}`";
    }

    /*
     * 获取数据库的所有表
     * @param string $dbname 数据库名
     */
    private function getTables($dbname) {
        $Db = \Yii::$app->db;
        $list = $Db->createCommand('SHOW TABLE STATUS')->queryAll();
        $list = array_map('array_change_key_case', $list);
        $list = ArrayHelper::map($list, 'name', 'name');
        return $list;
    }

    /**
     * 将数组按照字节数分割成小数组
     * @param array $array  数组
     * @param int $byte     字节数
     */
    private function chunkArrayByByte($array, $byte = 5120) {
        $i = 0;
        $sum = 0;
        foreach ($array as $v) {
            $sum += strlen($v);
            if ($sum < $byte) {
                $return[$i][] = $v;
            } elseif ($sum == $byte) {
                $return[++$i][] = $v;
                $sum = 0;
            } else {
                $return[++$i][] = $v;
                $i++;
                $sum = 0;
            }
        }
        return $return;
    }

    /**
     * 备份
     */
    public function backup() {
        $this->content = '/* This file is created by MySQLReback ' . date('Y-m-d H:i:s') . ' */';
        foreach ($this->dbname as $dbname) {
            $qdbname = $this->backquote($dbname);
            $rs = mysqli_query($this->link, "SHOW CREATE DATABASE {$qdbname}");
            if ($row = mysqli_fetch_row($rs)) {
                //建立数据库
                $this->content .= "\r\n /* 创建数据库 {$qdbname} */";
                //必须设置一个分割符..单用分号是不够安全的.
                $this->content .= "\r\n DROP DATABASE IF EXISTS {$qdbname};/* MySQLReback Separation */ {$row[1]};/* MySQLReback Separation */";
                mysqli_select_db($this->link, $dbname);
                //取得表
                $tables = $this->getTables($dbname);
                foreach ($tables as $table) {
                    $table = $this->backquote($table);
                    $tableRs = mysqli_query($this->link, "SHOW CREATE TABLE {$table}");
                    if ($tableRow = mysqli_fetch_row($tableRs)) {
                        //建表
                        $this->content .= "\r\n /* 创建表结构 {$table}  */";
                        $this->content .= "\r\n DROP TABLE IF EXISTS {$table};/* MySQLReback Separation */ {$tableRow[1]};/* MySQLReback Separation */";
                        $rescolumns = mysqli_query($this->link, "SHOW FULL COLUMNS FROM {$table}") ;
                        $fieldType = [];
                        while($row = mysqli_fetch_array($rescolumns)){
                            $fieldType[] = $row["Type"];
                        }
                        //获取数据
                        $tableDateRs = mysqli_query($this->link, "SELECT * FROM {$table}");
                        $valuesArr = array();
                        $values = '';
                        while ($tableDateRow = mysqli_fetch_row($tableDateRs)) {
                            //组合INSERT的VALUE
                            foreach ($tableDateRow as $key => &$v) {
                                if(in_array($fieldType[$key], ["date", "datetime"])) {
                                    $v = $v ?  "'" . addslashes($v) . "'" : addslashes("null");
                                }else if(strstr($fieldType[$key], "int") || strstr($fieldType[$key], "float")) {
                                    $v = is_numeric($v) && ($v >= 0 || $v < 0) ?  "'" . addslashes($v) . "'" : addslashes("0");
                                } else {
                                    $v = "'" . addslashes($v) . "'"; 
                                }
                            }
                            $valuesArr[] = '(' . implode(',', $tableDateRow) . ')';
                        }
                        $temp = $this->chunkArrayByByte($valuesArr);
                        if (is_array($temp)) {
                            foreach ($temp as $v) {
                                $values = implode(',', $v) . ';/* MySQLReback Separation */';
                                //空的数据表就不组合SQL语句了..因为没得组合
                                if ($values != ';/* MySQLReback Separation */') {
                                    $this->content .= "\r\n /* 插入数据 {$table} */";
                                    $this->content .= "\r\n INSERT INTO {$table} VALUES {$values}";
                                }
                            }
                        }
                    }
                }
            } else {
                return ["state" => 0, "message" => "未能找到数据库"];
            }
        }
        if (!empty($this->content)) {
            $result = $this->setFile();
            if(!$result["state"]) {
                return $result;
            }
        }
        return ["state" => 1];
    }
    
    /**
     * 增量备份
     */
    public function incrementBackup() {
        foreach ($this->dbname as $dbname) {
            $qdbname = $this->backquote($dbname);
            $rs = mysql_query("SHOW CREATE DATABASE {$qdbname}");
            if ($row = mysql_fetch_row($rs)) {
                dump($row);
            }
        }
    }

    /**
     * 恢复数据库
     * @param string $fileName 文件名
     */
    public function recover($fileName) {
        $result = $this->getfile($fileName);
        if(!$result["state"]) {
            return $result;
        }
        if (!empty($this->content)) {
            $content = explode(';/* MySQLReback Separation */', $this->content);
            foreach ($content as $sql) {
                $sql = trim($sql);
                if (!empty($sql)) {
                    $rs = mysqli_query($this->link, $sql);
                    if ($rs) {
                        if (strstr($sql, 'CREATE DATABASE')) {
                            $dbnameArr = sscanf($sql, 'CREATE DATABASE %s');
                            $dbname = trim($dbnameArr[0], '`');
                            mysqli_select_db($this->link, $dbname);
                        }
                    } else {
                        return ["state" => 0, "message" => '备份文件被损坏!' . mysqli_error($this->link)];
                    }
                }
            }
        } else {
            return ["state" => 0, "message" => "无法读取备份文件"];
        }
        return ["state" => 1];
    }

    /**
     * @抛出异常信息
     */
    private function throwException($error) {
        throw new Exception($error);
    }
}
    