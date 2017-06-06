<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "Verification".
 *
 * @property integer $id
 * @property string $name
 * @property string $sn
 * @property integer $warehouse_id
 * @property double $total_amount
 * @property integer $create_admin_id
 * @property integer $verify_admin_id
 * @property string $verify_time
 * @property integer $approval_admin_id
 * @property string $approval_time
 * @property integer $status
 * @property string $create_time
 * @property integer $payment
 */
class Verification extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'Verification';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'sn', 'verify_time', 'approval_time', 'create_time'], 'required'],
            [['warehouse_id', 'create_admin_id', 'verify_admin_id', 'approval_admin_id', 'status', 'payment'], 'integer'],
            [['total_amount'], 'number'],
            [['verify_time', 'approval_time', 'create_time'], 'safe'],
            [['name'], 'string', 'max' => 255],
            [['sn'], 'string', 'max' => 40],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '名称',
            'sn' => '表单号',
            'warehouse_id' => '仓库ID',
            'total_amount' => '总价',
            'create_admin_id' => '制表人',
            'verify_admin_id' => '审核人',
            'verify_time' => '审核时间',
            'approval_admin_id' => '批准人',
            'approval_time' => '批准时间',
            'status' => '0 待审核 1 待批准 2待下定 9批准驳回 10挂起',
            'create_time' => '制表时间',
            'payment' => '付款方式 1 现金 2 预付 3后付 4定金',
        ];
    }
}
