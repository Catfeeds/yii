<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "ProductInvoicingSale".
 *
 * @property integer $id
 * @property string $name
 * @property string $sn
 * @property integer $department_id
 * @property double $total_amount
 * @property integer $warehouse_id
 * @property integer $status
 * @property integer $create_admin_id
 * @property string $create_time
 * @property double $sale_amount
 * @property double $check_sale_amount
 * @property string $profit_loss_cause
 * @property double $last_invoic_amount
 * @property double $predict_invoic_amount
 * @property double $paid_amount
 * @property double $compensation_amount
 */
class ProductInvoicingSale extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'productinvoicingsale';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name','sn'], 'required', 'message' => '{attribute}不能为空'],
            [['department_id', 'warehouse_id', 'status', 'create_admin_id'], 'integer'],
            [['total_amount', 'sale_amount', 'check_sale_amount', 'last_invoic_amount', 'predict_invoic_amount', 'paid_amount', 'compensation_amount'], 'number'],
            [['create_time'], 'safe'],
            [['name'], 'string', 'max' => 100],
            [['sn'], 'string', 'max' => 50],
            [['profit_loss_cause'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '销存表单名',
            'sn' => '销存单号',
            'department_id' => '部门ID',
            'total_amount' => '销存总价',
            'warehouse_id' => '仓库ID',
            'status' => '状态',
            'create_admin_id' => '创建人',
            'create_time' => '创建时间',
            'sale_amount' => '应销金额统计',
            'check_sale_amount' => '实际销售金额统计',
            'profit_loss_cause' => '损益原因',
            'last_invoic_amount' => '上次结存余额',
            'predict_invoic_amount' => '预计结存余额',
            'paid_amount' => '上缴金额',
            'compensation_amount' => '补偿金额',
        ];
    }
}
