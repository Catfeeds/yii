<?php

namespace common\models;


use Yii;
use libs\Utils;
use libs\common\Flow;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "salesorder".
 *
 * @property integer $id
 * @property string $name
 * @property string $sn
 * @property integer $sale_order_id
 * @property string $total_amount
 * @property integer $department_id
 * @property integer $warehouse_id
 * @property integer $create_admin_id
 * @property integer $operation_admin_id
 * @property string $operation_time
 * @property integer $status
 * @property string $create_time
 * @property integer $config_id
 * @property string $remark
 * @property integer $timing_type
 */
class Salesorder extends namespace \base\Salesorder
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'salesorder';
    }
 /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'sn', 'create_admin_id','operation_admin_id','sale_order_id','custom_pay_service_id', 'create_time','customer_company'], 'required','message'=>'{attribute}不能为空'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '表单名称',
            'sn' => '表单号',
            'sale_order_id' => '订单号',
            'total_amount' => '订单金额',
            'department_id' => '部门id',
            'customer_company' => '顾客名字',
            'warehouse_id' => '仓库id',
            'create_admin_id' => '表单制作人',
            'operation_admin_id' => '表单处理人',
            'operation_time' => '表单操作时间',
            'custom_pay_service_id' => '收支人员',
            'down_payment_pay_ways' => '定金支付方式',
            'down_payment' => '定金',
            'benefit_money' => '特别减免金额',
            'status' => '状态',
            'create_time' => '表单生成时间',
            'remark' => 'Remark'
        ];
    }

    const STATUS_NO = 0;
    const STATUS_OK = 1;
    const STATUS_ABN = 2;
   private static $_status = [
        self::STATUS_OK => '正常结束',
        self::STATUS_NO => '待付款',
        self::STATUS_ABN => '异常结束',
    ];
    /**
     * 展示供应商状态
     */
    public function showStatus()
    {
        return self::$_status[$this->status];
    }
   /**
   *增加对应的订单数据
   *传值：对应post过来的数据
   *返值：返回添加成功或者失败的信息
   *2017年2月22日 11:53:22
   *肖波
   */
    public function addSalesOrder($post){     
     //增加对应的订单主表和对应的订单附表，并且新生成一条流水表记录】
        if(!isset($post["goodsId"]) || count($post["goodsId"]) == 0) {
            return array("state" => 0, "message" => "请选择销售商品");
        }
        //事务开始
        $transaction = Yii::$app->db->beginTransaction();
        try{
            //接受参数
            $this->attributes = $post["Salesorder"];           
            $this->sn = Utils::generateSn(Flow::STATUS_CREATE_ORDER);
            $this->sale_order_id=time().rand(10,100);
            $this->status =0;
            $this->total_amount = 0;
          
            //保存主表 
            if(!($this->save())) {
                $transaction->rollBack();
                return array("state" => 0, "message" => $this->getFirstErrors());
            }
            $productInfo = Product::findAll(["id" => $post["goodsId"]]);
            $productInfo = ArrayHelper::index($productInfo, "id");
            $num = $totalMoney = 0;
            $meterialType = $supplier = array();
            
            //循环保存  对应的二维表 
            foreach ($post["goodsId"] as $key => $productId) {
                if(!isset($productInfo[$productId])) {
                    continue;
                }
                if(!isset($post["goodsNum"][$key])) {
                    continue;
                }
                if($post["goodsPrice"][$key] == 0) {
                    $transaction->rollBack();
                    return array("state" => 0, "message" => "真实销售价格必须大于0");
                }
                if($post["goodsNum"][$key] == 0) {
                    $transaction->rollBack();
                    return array("state" => 0, "message" => "销售数量必须大于0");
                }
                $productItem = $productInfo[$productId];
                $planningProduct = new Salesorderproduct();
                $planningProduct->product_id = $productId;
                $planningProduct->sale_order_id = $this->sale_order_id;
                $planningProduct->name = $productItem->name;
                $planningProduct->price = $productItem->sale_price;
                $planningProduct->sale_price = $post["goodsPrice"][$key];
                $planningProduct->product_number = $post["goodsNum"][$key];
                $planningProduct->total_amount = $post["goodsPrice"][$key] * $planningProduct->product_number;
                $planningProduct->supplier_id = $productItem->supplier_id;
                $planningProduct->supplier_product_id = $productItem->supplier_product_id;
                $planningProduct->num = $productItem->barcode;
                $planningProduct->warehouse_id=$post["warehouseId"][$key];  //存储对应的库存id
                $planningProduct->barcode = $productItem->barcode;
                $planningProduct->spec = $productItem->spec;
                $planningProduct->unit = $productItem->unit;
                $planningProduct->material_type = $productItem->material_type;
                $planningProduct->product_cate_id = $productItem->product_category_id;
                if(!$planningProduct->save()) {
                    $transaction->rollBack();
                    return array("state" => 0, "message" => $planningProduct->getFirstErrors());
                }
                $num++;
                $totalMoney += $planningProduct->total_amount;
                $meterialType[] = $planningProduct->product_cate_id;
                $supplier[] = $planningProduct->supplier_id;
            }
            if($totalMoney > 99999999) {
                $transaction->rollBack();
                return array("state" => 0, "message" => "采购总金额不能超过限制金额一亿");
            }
            if($num == 0) {
                $transaction->rollBack();
                return array("state" => 0, "message" => "请选择销售商品");
            }
            if($this->benefit_money > $totalMoney) {
                $transaction->rollBack();
                return array("state" => 0, "message" => "特别减免不能大于销售总价");
            }
            //把金额的信息再次保存
            $this->total_amount = $totalMoney;//销售总价
            $this->real_amount=$totalMoney-$benefit_money;//减免之后的总价格
            if(!$this->save()){
                $transaction->rollBack();
                return array("state" => 0, "message" => $this->getFirstErrors());
            }
            BusinessRemind::addRemind($this->id, Flow::showTypeUrl(Flow::STATUS_CREATE_ORDER), $this->status, $this->operation_admin_id, $this->name.'需要您的执行');
            $businessModel = new BusinessAll();
            $business = $businessModel->addBusiness($this,Flow::STATUS_CREATE_ORDER);
            if(!$business["state"]) {
                $transaction->rollBack();
                return ["error" => 0, "message" => $business["message"]];
            }
            //记录管理员日志
            AdminLog::addLog("order", "制作订单成功：".$this->id);
            $transaction->commit();
            return array("state" => 1);
        } catch (Exception $ex) {
            $transaction->rollBack();
            //如果失败返回失败的一些信息
            return array("state" => 0, "message" => $ex->getTraceAsString());
        } 
}
        /**
        *执行对应的订单
        */

        public function operationsaleorder($model)
        {
            $transaction=Yii::$app->db->beginTransaction();  
            try{
            $model->operation_admin_id=Yii::$app->user->getId();
            $model->operation_time=date('Y-m-d H-i-s');
            $model->status=1;
            if(!$model->save())
            {
              $transaction->rollBack();
               return 0;
            }
            $business=BusinessAll::findOne(['business_id'=>$model->id,
                  'business_type'=>Flow::STATUS_CREATE_ORDER]);
            $business->delete();
            $remindBusiness=BusinessRemind::findOne(['business_id'=>$model->id,
                  'business_type'=>Flow::showTypeUrl(Flow::STATUS_CREATE_ORDER)]);
            //echo '<pre>';print_r($remindBusiness);die;
            $remindBusiness->business_state=1;
               $remindBusiness->status=1;
           if(!$remindBusiness->save()) 
            {
                $transaction->rollBack();
               return 0;  
            }
            $transaction->commit();
            return 1;
        }catch (Exception $EX)
        {
            $transaction->rollBack();
            return 0;
        }
        }
        /*
        *驳回对应的订单
        */
        public function rejectsaleorder($model){

       $transaction=Yii::$app->db->beginTransaction();  
            try{
            $model->operation_admin_id=Yii::$app->user->getId();
            $model->operation_time=date('Y-m-d H-i-s');
            $model->status=2;
            if(!$model->save())
            {
              $transaction->rollBack();
               return 0;
            }
            $business=BusinessAll::findOne(['business_id'=>$model->id,
                  'business_type'=>Flow::STATUS_CREATE_ORDER]);
            $business->delete();
            $remindBusiness=BusinessRemind::findOne(['business_id'=>$model->id,
                  'business_type'=>Flow::showTypeUrl(Flow::STATUS_CREATE_ORDER)]);
            //echo '<pre>';print_r($remindBusiness);die;
            $remindBusiness->business_state=2;
                   $remindBusiness->status=1;
           if(!$remindBusiness->save()) 
            {
                $transaction->rollBack();
               return 0;  
            }
            $transaction->commit();
            return 1;
        }catch (Exception $EX)
        {
            $transaction->rollBack();
            return 0;
        }

        }

      



    
    }



