<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "CheckPlanningFlowData".
 *
 * @property integer $id
 * @property integer $check_planning_flow_id
 * @property integer $data_id
 * @property string $data_name
 */
class CheckPlanningFlowData extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'CheckPlanningFlowData';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['check_planning_flow_id', 'data_id'], 'required'],
            [['check_planning_flow_id', 'data_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'check_planning_flow_id' => 'Check Planning Flow ID',
            'data_id' => 'Data ID',
            'data_name' => 'Data Name',
        ];
    }
}
