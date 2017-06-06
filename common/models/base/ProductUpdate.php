<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "ProductUpdate".
 *
 * @property integer $id
 * @property integer $product_id
 * @property string $name
 * @property integer $product_category_id
 * @property string $barcode
 * @property double $purchase_price
 * @property double $sale_price
 * @property integer $supplier_id
 * @property integer $supplier_product_id
 * @property string $num
 * @property string $spec
 * @property string $unit
 * @property integer $material_type
 * @property integer $inventory_warning
 * @property integer $status
 * @property integer $create_admin_id
 * @property string $create_time
 * @property integer $verify_admin_id
 * @property string $verify_time
 * @property integer $approval_admin_id
 * @property string $approval_time
 * @property integer $operation_admin_id
 * @property string $operation_time
 * @property integer $config_id
 * @property string $failCause
 * @property integer $is_batches
 * @property integer $timing_type
 */
class ProductUpdate extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'productupdate';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_id', 'product_category_id', 'barcode', 'supplier_id', 'supplier_product_id', 'num', 'create_admin_id', 'config_id'], 'required'],
            [['product_id', 'product_category_id', 'supplier_id', 'supplier_product_id', 'material_type', 'inventory_warning', 'status', 'create_admin_id', 'verify_admin_id', 'approval_admin_id', 'operation_admin_id', 'config_id', 'is_batches', 'timing_type'], 'integer'],
            [['purchase_price', 'sale_price'], 'number'],
            [['create_time', 'verify_time', 'approval_time', 'operation_time'], 'safe'],
            [['name', 'barcode', 'failCause'], 'string', 'max' => 255],
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
            'product_id' => 'Product ID',
            'name' => 'Name',
            'product_category_id' => '物料分类',
            'barcode' => '条形码',
            'purchase_price' => '进货参考价格',
            'sale_price' => '销售价格',
            'supplier_id' => 'Supplier ID',
            'supplier_product_id' => 'Supplier Product ID',
            'num' => 'Num',
            'spec' => 'Spec',
            'unit' => 'Unit',
            'material_type' => 'Material Type',
            'inventory_warning' => 'Inventory Warning',
            'status' => 'Status',
            'create_admin_id' => 'Create Admin ID',
            'create_time' => 'Create Time',
            'verify_admin_id' => 'Verify Admin ID',
            'verify_time' => 'Verify Time',
            'approval_admin_id' => 'Approval Admin ID',
            'approval_time' => 'Approval Time',
            'operation_admin_id' => 'Operation Admin ID',
            'operation_time' => 'Operation Time',
            'config_id' => 'Config ID',
            'failCause' => 'Fail Cause',
            'is_batches' => 'Is Batches',
            'timing_type' => '定时操作类型',
        ];
    }
}
