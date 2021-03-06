<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "AdminLog".
 *
 * @property integer $id
 * @property integer $admin_id
 * @property string  $content
 * @property integer $status
 * @property string  $type
 * @property string  $ip
 * @property string  $os
 * @property string  $browser
 * @property string  $create_time
 */
class AdminLog extends namespace\base\AdminLog
{
    const STATUS_NO = 0;
    const STATUS_OK = 1;
    const STATUS_DEL = 99;
    
    private static $_status = [
        self::STATUS_OK => '有效',
        self::STATUS_NO => '无效',
        self::STATUS_DEL => '删除',
    ];
    
    /**
     * 展示状态
     */
    public function showStatus()
    {
        return self::$_status[$this->status];
    }

    /**
     * 获取状态列表
     */
    public static function getStatusSelectData()
    {
        return self::$_status;
    }
    
    /**
     * 获取操作人姓名
     */
    public function showAdminName(){
     	$model = Admin::findOne($this->admin_id);
        return isset($model->username) ?  $model->username: '无';
    }
    
    /**
     * 添加新的日志
     * @param type $code 操作Code
     * @param type $content 操作内容
     * @param type $status 状态
     * @return type
     */
    public static function addLog($code, $content = '', $status = 1)
    {
    	$log = new AdminLog;
        $log->admin_id = Yii::$app->user->id;
        $log->type = $code;
        $log->content = $content ? $content : $code;
        $log->create_time = date("Y-m-d H:i:s");
        $log->ip = Yii::$app->request->userIP;
        $log->status = $status;
        return $log->save();
    }
}
