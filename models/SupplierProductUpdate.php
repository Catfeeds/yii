<?php

namespace common\models;

use Yii;
use Exception;
use libs\common\Flow;

use common\models\BusinessAll;
use common\models\AdminLog;
use common\models\SupplierProduct;
use common\models\Product;

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
class SupplierProductUpdate extends namespace\base\SupplierProductUpdate
{
    public function rules() {
        $rules = parent::rules();
        $childRules = [
            [['name', 'num', 'spec', 'unit'] , 'checkname' , 'skipOnEmpty' => false],
        ];
        return array_merge($rules, $childRules);
    }
    
    public function checkname($attribute , $params) {
        if(preg_match('/[^0-9a-zA-Z一-龥]/u',$this->$attribute)){
            $this->addError($attribute , ($attribute == "name" ? "名称" : ($attribute == "num" ? "供应商出品编码" : ($attribute == "spec" ? "规格" : "单位"))).'不能有空格和特殊字符');
        }
    }
    
    public function addUpdate($model, $post) {
        $transaction = Yii::$app->db->beginTransaction();
        try{
            $this->attributes = $post["SupplierProduct"];
            $this->supplier_product_id = $model->id;
            $this->create_admin_id = \Yii::$app->user->getId();
            $this->create_time = date("Y-m-d H:i:s");
            $this->config_id = 0;
            $this->status = Flow::STATUS_APPLY_VERIFY;
            if(!$this->validate()) {
                $message = $this->getFirstErrors();
                throw new Exception(reset($message));
            }
            $this->save();
            $result = Flow::confirmFollowAdminId(Flow::TYPE_PRODUCT_UPDATE, $this, 0, time(), [], [], []);
            if(!$result["state"]) {
                $transaction->rollBack();
                return $result;
            }
            $businessModel = new BusinessAll();
            $business = $businessModel->addBusiness($this, Flow::TYPE_PRODUCT_UPDATE);
            if(!$business["state"]) {
                $transaction->rollBack();
                return ["error" => 1, "message" => $business["message"]];
            }
            $model->is_update = 1;
            $model->save();
            AdminLog::addLog("update_update", "物料信息修改申请成功：".$this->id);
            $transaction->commit();
            return array("state" => 1);
        } catch (Exception $ex) {
            $transaction->rollBack();
            return ["state" => 0, "message" => $ex->getMessage()];
        }
    }
    
    public function Finish() {
        $supplierProduct = SupplierProduct::findOne($this->supplier_product_id);
        if(!$supplierProduct) {
            return ["state" => 0, "message" => "未知错误"];
        }
        $supplierProduct->name = $this->name;
        $supplierProduct->purchase_price = $this->purchase_price;
        $supplierProduct->supplier_id = $this->supplier_id;
        $supplierProduct->num = $this->num;
        $supplierProduct->spec = $this->spec;
        $supplierProduct->unit = $this->unit;
        $supplierProduct->material_type = $this->material_type;
        $supplierProduct->is_update = 0;
        if(!$supplierProduct->validate()) {
            $message = $supplierProduct->getFirstErrors();
            return ["state" => 0, "message" => reset($message)];
        }
        $supplierProduct->save();
        $product = Product::findOne(["supplier_product_id" => $this->supplier_product_id]);
        if(!$product) {
            return ["state" => 1];
        }
        $product->modify_status = self::MODIFY_STATUS_APPLY_UPDATE;
        $product->save();
        return ["state" => 1]; 
    }
}

