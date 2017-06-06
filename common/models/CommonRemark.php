<?php
namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;
/**
 * This is the model class for table "CommonRemark".
 *
 * @property integer $id
 * @property integer $flow_id
 * @property integer $flow_type
 * @property string $remark
 * @property integer $type
 */
class CommonRemark extends namespace\base\CommonRemark
{
    /**
     * 操作类型 -- 审核
     */
    const TYPE_VERIFY = 1;
    /**
     * 操作类型 -- 批准
     */
    const TYPE_APPROVAL = 2;
    /**
     * 操作类型 -- 执行
     */
    const TYPE_OPERATOR = 3;
    
    /**
     * 添加新的流程备注
     * @param type $flowId 流程ID
     * @param type $flowType 流程类型
     * @param type $remark 备注
     * @param type $type 备注类型
     * @return type
     */
    public static function addCommonRemark($flowId, $flowType, $remark, $type) {
        $model = new CommonRemark();
        $model->flow_id = $flowId;
        $model->flow_type = $flowType;
        $model->remark = $remark;
        $model->type = $type;
        if(!$model->save()) {
            return ["state" => 0, "message" => $model->getFirstErrors()];
        }
        return ["state" => 1];
    }
    
    /**
     * 获取类型备注列表
     * @param type $flowId 流程ID
     * @param type $flowType 流程类型
     * @return type
     */
    public static function getRemarkList($flowId, $flowType) {
        $all = self::findAll(["flow_id" => $flowId, "flow_type" => $flowType]);
        return ArrayHelper::map($all, "type", "remark");
    }
}
