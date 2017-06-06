<?php
namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\BaseActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "BusinessRemind".
 *
 * @property integer $id
 * @property integer $business_id
 * @property string $business_type
 * @property integer $business_state
 * @property integer $status
 * @property string $create_time
 * @property integer $admin_id
 */
class BusinessRemind extends namespace\base\BusinessRemind {
    /**
     * 新增业务提醒
     * @param type $business_id 业务ID
     * @param type $business_type 业务类型
     * @param type $business_state 业务状态
     * @param type $admin_id 处理人
     * @param type $content 提醒内容
     * @return type
     */
    public static function addRemind($business_id, $business_type, $business_state, $admin_id, $content = '') {
        $adminIds = is_array($admin_id) ? $admin_id : [$admin_id];
        foreach ($adminIds as $adminId) {
            $businessRemind = new BusinessRemind;
            $businessRemind->business_id = $business_id;
            $businessRemind->business_type = $business_type;
            $businessRemind->business_state = $business_state;
            $businessRemind->status = 0;
            $businessRemind->create_time = date("Y-m-d H:i:s");
            $businessRemind->admin_id = $adminId;
            $businessRemind->content = $content;
            if (!$businessRemind->validate()) {
                return array("state" => 0, "message" => $businessRemind->getFirstErrors());
            }
            $businessRemind->save();
        }
        return array("state" => 1);
    }
    
    /**
     * 处理业务提醒
     * @param type $business_id 业务ID
     * @param type $business_type 业务类型
     * @param type $business_state 业务状态
     * @return type
     */
    public static function disposeRemind($business_id, $business_type, $business_state) {
        $modelAll = BusinessRemind::findAll(["business_id" => $business_id, "business_type" => $business_type, "business_state" => $business_state, 'status' => 0]);
        if(!$modelAll) {
            return array("state" => 1);
        }
        foreach ($modelAll as $model) {
            $model->status = 1;
            if(!$model->save()) {
                return array("state" => 0, "message" => $model->getFirstErrors());
            }
        }
        return array("state" => 1);
    }
}
    