<?php
use yii\helpers\Url;
use common\models\Role;
use common\models\Department;
use common\models\Product;
use common\models\ProductStock;
$this->title = '销存管理-订单详情';
?>
<?= $this->context->renderPartial('/public/menu') ?>
<div class="main-container">
    <table id="table-list" class="table-list taleft">
        <caption>订单模版详情</caption>
        <tr id="quick-form">
            <td style="width: 30%;">下订员工：<?=$model->create_admin_id ? Role::getNameByRoleId($model->create_admin_id) : "无"  ?></td>
            <td style="width: 20%;">下订时间：<?= $model->create_time ?></td>
            <td style="width: 30%;">执行员工：<?=$model->operation_admin_id?Role::getNameByRoleId($model->operation_admin_id) : "无" ?></td>
            <td style="width: 20%;">执行时间：<?=$model->operation_time  ?></td>
        </tr>
        <tr>
            <td>特别优惠减免金额：<?= $model->benefit_money ?></td>
            <td>收支员工：<?=$model->operation_admin_id?Role::getNameByRoleId($model->operation_admin_id) : "无"?></td>
            <td >顾客单位：<?= $model->customer_company ?></td>
            <td >订单处理人：<?=$model->operation_admin_id?Role::getNameByRoleId($model->operation_admin_id) : "无" ?></td>
        </tr>
        <tr>
            <td  >默认申领部门：<?= $model->department_id ? Department::getNameById($model->department_id) : "无" ?></td>
            <td >销售总额：<?= $model->total_amount ?></td>
            <td colspan="2">用途说明：<?= $model->remark ? $model->remark : "无" ?></td>
        </tr>
        <tr class="showSelProduct">
            <td colspan="4">
                <table id="table-list" class="table-list">
                    <tr>
                      <th width="10%">序号</th>
                        <th width="10%">物料名称</th>
                        <th width="10%">物料ID</th>
                         <th width="10%">供应商物料ID</th>
                        <th width="10%">物料类别</th>
                        <th width="10%">规格</th>
                        <th width="10%">条形码</th>
                        <th width="5%">批次号</th>
                        <th width="5%">单位</th>
                        <th width="8%">库存数量</th>
                        <th width="8%">下订数量</th>
                        <th width="10%">销售定价</th>
                        <th width="10%">销售价格</th>
                        <th width="10%">销售总价</th>
                    </tr>
                    <?php foreach($info as $key=> $data){ ?>
                        <tr>
                           <td><?= isset($key) ? $key+1 : 0; ?></td>
                            <td><?= $data->name ?></td>
                            <td><?= $data->product_id ?></td>
                            <td><?= $data->supplier_product_id ?></td>
                             <td><?= Product::showTypeName($data->material_type) ?></td>
                              <td><?= $data->spec ?></td>
                               <td><?= $data->num?></td>
                                <td><?= $data->barcode?></td>
                                 <td><?= $data->unit ?></td>
                                   <td><?= ProductStock::getStockNumber($data->product_id,$data->warehouse_id) ?></td>  
                                    <td><?= $data->product_number ?></td>
                                     <td><?=  number_format($data->price,2) ?></td>
                                  <td><?= number_format($data->sale_price,2) ?></td>
                                    <td><?= number_format($data->total_amount, 2) ?></td>
                        </tr>
                    <?php } ?>
                </table>
            </td>
        </tr>
    </table>
    <div class="buttons">
    <?php if(!$model->status&&$model->operation_admin_id==Yii::$app->user->getId()) {?>
     <a class="button blue-button" href="<?= Url::to(['order/rejectsaleorder', 'id' => $model->id]) ?>">驳回</a>
        <a class="button blue-button" href="<?= Url::to(['order/operationsaleorder', 'id' => $model->id]) ?>">执行</a> 
        <?php } ?>
        <a class="button blue-button" href="javascript:history.back(-1)">返回</a>
    </div>
</div>
<?= $this->context->renderPartial('/jquery/js') ?>