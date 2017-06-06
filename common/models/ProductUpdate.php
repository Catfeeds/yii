<?php

namespace common\models;

use Yii;
use Exception;
use libs\common\Flow;
use common\models\ProductUpdateLog;
use common\models\BusinessAll;
use common\models\AdminLog;
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
class ProductUpdate extends namespace\base\ProductUpdate
{
    /**
     * 字段规则
     */
    public function rules() {
        $rules = parent::rules();
        $childRules = [
            [['product_id', 'product_category_id', 'barcode', 'supplier_id', 'supplier_product_id', 'sale_price', 'purchase_price'], 'required', 'message' => "{attribute}不能为空"],
            [['barcode', 'inventory_warning'] , 'checkname' , 'skipOnEmpty' => false],
        ];
        return array_merge($rules, $childRules);
    }
    
    /**
     * 验证参数不能有空格和特殊字符 
     */
    public function checkname($attribute , $params) {
        if(preg_match('/[^0-9a-zA-Z一-龥]/u',$this->$attribute)){
            $this->addError($attribute , ($attribute == "barcode" ? "条形码" : "库存警告").'不能有空格和特殊字符');
        }
    }
    
    /**
     * 添加新的物料修改记录
     * @param type $model 物料对象
     * @param type $post 表单提交数据
     * @return type
     * @throws Exception
     */
    public function addUpdate($model, $post) {
        if($post["Product"]["product_category_id"] == $model->product_category_id && $post["Product"]["barcode"] == $model->barcode && $post["Product"]["purchase_price"] == $model->purchase_price &&
                $post["Product"]["sale_price"] == $model->sale_price && $post["Product"]["is_batches"] == $model->is_batches && 
                $post["Product"]["inventory_warning"] == $model->inventory_warning) {
            return ["state" => 0, "message" => "对不起，您未做任何修改，无法创建物料修改流程！"];
        }
        $transaction = Yii::$app->db->beginTransaction();
        try{
            $this->attributes = $post["Product"];
            $this->product_id = $model->id;
            $this->name = $model->name;
            $this->supplier_id = $model->supplier_id;
            $this->supplier_product_id = $model->supplier_product_id;
            $this->num = $model->num;
            $this->spec = $model->spec;
            $this->unit = $model->unit;
            $this->material_type = $model->material_type;
            $this->create_admin_id = \Yii::$app->user->getId();
            $this->create_time = date("Y-m-d H:i:s");
            $this->config_id = 0;
            $this->status = Flow::STATUS_APPLY_VERIFY;
            if(!$this->validate()) {
                $message = $this->getFirstErrors();
                throw new Exception(reset($message));
            }
            $this->save();
            $model->is_update = 1;
            $model->save();
            $result = Flow::confirmFollowAdminId(Flow::TYPE_PRODUCT_UPDATE, $this, $this->sale_price, time(), [], [$this->supplier_id], [$this->product_category_id]);
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
            if($this->status == Flow::STATUS_FINISH){
                $result = $this->Finish();
                if(!$result["state"]) {
                    $transaction->rollBack();
                    return $result;
                }
            }
            AdminLog::addLog("product_edit", "物料信息修改申请成功：".$this->id);
            $transaction->commit();
            return array("state" => 1);
        } catch (Exception $ex) {
            $transaction->rollBack();
            return ["state" => 0, "message" => $ex->getMessage()];
        }
    }
    
    /**
     * 物料修改完成方法
     */
    public function Finish() {
        $product = Product::findOne($this->product_id);
        if(!$product) {
            return ["state" => 0, "message" => "未知错误"];
        }
        $log = new ProductUpdateLog();
        $log->product_id = $this->product_id;
        $log->name = $product->name;
        $log->product_category_id = $product->product_category_id;
        $log->barcode = $product->barcode;
        $log->purchase_price = $product->purchase_price;
        $log->sale_price = $product->sale_price;
        $log->supplier_id = $product->supplier_id;
        $log->supplier_product_id = $product->supplier_product_id;
        $log->num = $product->num;
        $log->spec = $product->spec;
        $log->unit = $product->unit;
        $log->material_type = $product->material_type;
        $log->inventory_warning = $product->inventory_warning;
        $log->is_batches = $product->is_batches;
        $log->create_time = date("Y-m-d H:i:s");
        $log->update_product_id = $this->id;
        if(!$log->validate()) {
            $message = $log->getFirstErrors();
            return ["state" => 0, "message" => reset($message)];
        }
        $log->save();        
        $product->product_category_id = $this->product_category_id;
        $product->purchase_price = $this->purchase_price;
        $product->sale_price = $this->sale_price;
        $product->barcode = $this->barcode;
        $product->inventory_warning = $this->inventory_warning;
        $product->is_batches = $this->is_batches;
        $product->is_update = 0;
        if(!$product->validate()) {
            $message = $product->getFirstErrors();
            return ["state" => 0, "message" => reset($message)];
        }
        $product->save();
        return ["state" => 1]; 
    }
    
    /**
     * 物料修改驳回方法
     */
    public function Reject() {
        $model = Product::findOne(["id" => $this->product_id]);
        if(!$model) {
            return ["state" => 0, "message" => "网络异常，请刷新再试"];
        }
        $model->is_update = 0;
        if(!$model->save()) {
            $message = $model->getFirstErrors();
            return ["state" => 0, "message" => reset($message)];
        }
        return ["state" => 1];
    }
}
