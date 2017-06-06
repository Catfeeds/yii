<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "SupplierProductUpdate".
 *
 * @property integer $id
 * @property integer $supplier_product_id
 * @property integer $supplier_id
 * @property string $name
 * @property double $purchase_price
 * @property string $num
 * @property string $spec
 * @property string $unit
 * @property integer $material_type
 * @property integer $create_admin_id
 * @property string $create_time
 * @property integer $verify_admin_id
 * @property string $verify_time
 * @property integer $approval_admin_id
 * @property string $approval_time
 * @property integer $operation_admin_id
 * @property string $operation_time
 * @property integer $config_id
 * @property integer $status
 * @property string $failCause
 */
class SupplierProductUpdate extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'SupplierProductUpdate';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['supplier_product_id', 'supplier_id', 'num', 'create_admin_id', 'config_id', 'status'], 'required'],
            [['supplier_product_id', 'supplier_id', 'material_type', 'create_admin_id', 'verify_admin_id', 'approval_admin_id', 'operation_admin_id', 'config_id', 'status'], 'integer'],
            [['purchase_price'], 'number'],
            [['create_time', 'verify_time', 'approval_time', 'operation_time'], 'safe'],
            [['name', 'failCause'], 'string', 'max' => 255],
            [['num', 'spec', 'unit'], 'string', 'max' => 40],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'supplier_product_id' => 'Supplier Product ID',
            'supplier_id' => 'Supplier ID',
            'name' => 'Name',
            'purchase_price' => 'Purchase Price',
            'num' => 'Num',
            'spec' => 'Spec',
            'unit' => 'Unit',
            'material_type' => 'Material Type',
            'create_admin_id' => 'Create Admin ID',
            'create_time' => 'Create Time',
            'verify_admin_id' => 'Verify Admin ID',
            'verify_time' => 'Verify Time',
            'approval_admin_id' => 'Approval Admin ID',
            'approval_time' => 'Approval Time',
            'operation_admin_id' => 'Operation Admin ID',
            'operation_time' => 'Operation Time',
            'config_id' => 'Config ID',
            'status' => 'Status',
            'failCause' => 'Fail Cause',
        ];
    }
}
