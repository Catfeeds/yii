<?php
namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\BaseActiveRecord;
use yii\db\Exception;

use common\models\WarehouseBuying;
use common\models\WarehouseSale;
use common\models\WarehousePlanning;
use libs\common\Flow;
use common\models\Product;
/**
 * This is the model class for table "ProductStock".
 *
 * @property integer $id
 * @property string $batches
 * @property integer $product_id
 * @property integer $number
 * @property integer $warehouse_id
 * @property integer $type
 */
class ProductStock extends namespace\base\ProductStock
{
    /**
     * 采购入库
     * @param type $buyingItem 采购下定记录
     * @param type $buyingProduct 采购下定物料记录
     * @param type $post 表单提交数据
     * @return type
     */
    public function addStock($buyingItem, $buyingProduct, $post) {
        if(!$post["ProductStock"]["batches"]) {
            return array("state" => 0, "message" => "入库批次号不能为空");
        }
        $transaction = Yii::$app->db->beginTransaction();
        try {
            foreach ($buyingProduct as $productItem) {
                if($buyingItem->type == WarehousePlanning::TYPE_EXCEPTION) {
                    $stockItem = false;
                } else {
                    $productModel = Product::findOne($productItem->product_id);
                    if($productModel->is_batches) {
                        $stockItem = false;
                    } else{
                        $stockItem = self::findOne(["product_id" => $productItem->product_id, 'warehouse_id' => $productItem->warehouse_id, 'type' => $buyingItem->type]);
                    }
                }
                if($stockItem) {
                    $result = WarehouseGateway::addWarehouseGateway($productItem->warehouse_id, $productItem->product_id, WarehouseGateway::TYPE_IN, $stockItem->number, $productItem->buying_number, $buyingItem->id, WarehouseGateway::GATEWAY_TYPE_BUYING, $buyingItem->type, $stockItem->batches);
                    if(!$result["state"]) {
                        $transaction->rollBack();
                        return $result;
                    }
                    $stockItem->number = $stockItem->number + $productItem->buying_number;
                    $stockItem->update();
                    continue;
                }
                $model = new ProductStock();
                $model->batches = $post["ProductStock"]["batches"];
                $model->product_id = $buyingItem->type == WarehousePlanning::TYPE_EXCEPTION ? $productItem->id : $productItem->product_id;
                $model->number = $productItem->buying_number;
                $model->warehouse_id = $productItem->warehouse_id;
                $model->supplier_id = $productItem->supplier_id;
                $model->type = $buyingItem->type;
                if(!$model->save()) {
                    $transaction->rollBack();
                    return array("state" => 0, "message" => $model->getFirstErrors());
                }
                $result = WarehouseGateway::addWarehouseGateway($productItem->warehouse_id, $productItem->product_id, WarehouseGateway::TYPE_IN, 0, $productItem->buying_number, $buyingItem->id, WarehouseGateway::GATEWAY_TYPE_BUYING, $buyingItem->type, $model->batches);
                if(!$result["state"]) {
                    $transaction->rollBack();
                    return $result;
                }
            }
            $buyingItem->status = Flow::STATUS_FINISH;
            $buyingItem->update();
            $remarkResult = CommonRemark::addCommonRemark($buyingItem->id, Flow::TYPE_BUYING, $post["remark"], CommonRemark::TYPE_OPERATOR);
            if(!$remarkResult["state"]) {
                $transaction->rollBack();
                return $remarkResult;
            }
            $transaction->commit();
            return array("state" => 1);
        } catch (Exception $exc) {
            $transaction->rollBack();
            return array("state" => 0, "message" => $exc->getTraceAsString());
        }
    }
    
    /**
     * 销售核存
     * @param type $post 表单提交数据
     */
    public function checkSale($post)
    {
        $real = $post["real"];
        if(count($real) == 0) {
            return array("state" => 1);
        }
        $stockIds = array_keys($real);
        $storeItem = self::findAll($stockIds);
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $productList = array();
            foreach ($storeItem as $val) {
                $productItem = Product::findOne($val->product_id);
                if($val->number < $real[$val->id]) {
                    $transaction->rollBack();
                    return ["state" => 0, "message" => "商品：".$productItem->name."库存小于销售数量"];
                }
                $productList[$val->warehouse_id][$val->product_id]["product_number"] = $val->number;
                $productList[$val->warehouse_id][$val->product_id]["buying_number"] = $real[$val->id];
            }
            $model = new WarehouseSale();
            $result = $model->addSale($productList);
            if(!$result["state"]) {
                $transaction->rollBack();
                return $result;
            }
            $transaction->commit();
            return ["state" => 1];
        } catch (Exception $exc) {
            $transaction->rollBack();
            return ["state" => 0, "message" => $exc->getTraceAsString()];
        }

        
    }
}
