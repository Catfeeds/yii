<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\BaseActiveRecord;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "FlowConfigStep".
 *
 * @property integer $id
 * @property string $name
 * @property integer $create_step
 * @property integer $verify_step
 * @property integer $approval_step
 * @property integer $operation_step
 * @property string $business_begin_table
 * @property string $business_end_table
 * @property integer $config_sn
 */
class FlowConfigStep extends namespace\base\FlowConfigStep {

    //无
    const STEP_NO = 0;
    //有
    const STEP_YES = 1;
    
    public static $_step = [
    	self::STEP_YES => '有',
        self::STEP_NO => '无',
    ];
   
    /**
     * 展示步骤
     */
    public static function showStep($isStep) {
        return isset(self::$_step[$isStep]) ? self::$_step[$isStep] : "未知" . $isStep;
    }
    
    /**
     * 获取状态列表
     */
    public static function getStepSelectData() {
        return self::$_step;
    }
}
