<?php
namespace common\models\base;

use Yii;

/**
 * This is the model class for table "Product".
 *
 * @property integer $id
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
 * @property integer $modify_status
 * @property integer $operation_admin_id
 * @property string $operation_time
 * @property integer $config_id
 * @property string $failCause
 * @prpperty integer $is_batches
 * @prpperty integer $is_update
 * @prpperty integer $timing_type
 */
class Product extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'product';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_category_id', 'supplier_id', 'supplier_product_id', 'num', 'material_type', 'create_admin_id', 'modify_status', 'config_id', 'is_batches'], 'required'],
            [['product_category_id', 'supplier_id', 'supplier_product_id', 'inventory_warning', 'status', 'create_admin_id', 'verify_admin_id', 'approval_admin_id', 'modify_status', 'operation_admin_id', 'config_id', 'material_type', 'is_batches', 'is_update', 'timing_type'], 'integer'],
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
            'name' => '名称',
            'product_category_id' => '物料分类ID',
            'barcode' => '条形码',
            'purchase_price' => '进货参考价格',
            'sale_price' => '预计销售价格',
            'supplier_id' => '供应商ID',
            'supplier_product_id' => '供应商物料ID',
            'num' => '物料编码',
            'spec' => '规格',
            'unit' => '单位',
            'material_type' => '物料类别',
            'inventory_warning' => '库存警告',
            'status' => '录入操作',
            'create_admin_id' => 'Create Admin ID',
            'create_time' => 'Create Time',
            'verify_admin_id' => 'Verfiy Admin ID',
            'verify_time' => 'Verfiy Time',
            'approval_admin_id' => 'Approval Admin ID',
            'approval_time' => 'Approval Time',
            'modify_status' => 'Modify Status',
            'operation_admin_id' => 'Operation Admin ID',
            'operation_time' => 'Operation Time',
            'config_id' => 'Config ID',
            'failCause' => 'Fail Cause',
            'is_batches' => '是否需要批次号 【0：不需要 1：需要】',
            'is_update' => '是否需要批次号 【0：不需要 1：需要】',
            'timing_type' => '定时操作类型',
        ];
    }
}
