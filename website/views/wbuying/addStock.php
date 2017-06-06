<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;
use libs\Utils;
use common\models\Warehouse;
use common\models\FlowConfig;
use common\models\Admin;
use common\models\Supplier;
use common\models\WarehouseProcurement;
use common\models\Product;
use common\models\ProductCategory;
use libs\common\Flow;
$this->title = '业务基础数据-下定入库';
?>
<?= $this->context->renderPartial('/public/menu') ?>
<div class="main-container">
   
    <?php $form = ActiveForm::begin(); ?>
    <table id="table-list" class="table-list taleft">
        <tr>
            <td style="width: 10%;">下定序号：<?= $item->id ?></td>
            <td style="width: 30%;">下定名称：<?= $item->name ?></td>
            <td style="width: 20%;">编号：<?= $item->sn ?></td>
            <td style="width: 20%;">仓库名称：<?= Warehouse::getNameById($item->warehouse_id) ?></td>
            <td style="width: 20%;">采购总价：<?= number_format($item->total_amount, 2) ?></td>
        </tr>
        <tr>
            <td colspan="2">计划下单时间：<?= $item->planning_date ?></td>
            <td>付款方式：<?= $item->showPayment(); ?></td>
            <td>定金：<?= number_format($item->deposit, 2) ?></td>
            <td>付款日期：<?= $item->payment_term ?></td>
        </tr>
        <tr>
            <td colspan="2">流程名称：<?= FlowConfig::getNameById($item->config_id) ?></td>
            <td>制表人：<?= Admin::getNameById($item->verify_admin_id) ?></td>
            <td>审核人：<?= Admin::getNameById($item->approval_admin_id) ?></td>
            <td>批准人：<?= Admin::getNameById($item->operation_admin_id) ?></td>
        </tr>
        <tr>
            <td colspan="2">批次号：<?= Html::activeTextInput($model, 'batches', ['style' => 'width:70%', 'value' => Utils::generateSn(Flow::TYPE_ORDER)]) ?></td>
            <td colspan="3">入库说明：<?= Html::textInput('remark', '',['style' => 'width:80%', 'class' => 'verifySpecial']) ?></td>
        </tr>
    </table>
    <table id="table-list" class="table-list">
        <tr>
            <th width="10%">物料名称</th>
            <th width="10%">供应商</th>
            <th width="5%">供应商<br>物料ID</th>
            <th width="10%">物料分类</th>
            <th width="8%">条形码</th>
            <th width="5%">规格</th>
            <th width="5%">单位</th>
            <th width="8%">实际<br>采购价格</th>
            <th width="8%">实际<br>采购数量</th>
            <th width="8%">设置<br>销售价格</th>
            <th width="8%">实际<br>入库数量</th>
            <th width="10%">物料总价</th>
        </tr>
        <?php foreach($info as $data){ ?>
            <tr class="tr_<?= $data->id ?>">
                <td><?= $data->name ?></td>
                <td><?= Supplier::getNameById($data->supplier_id) ?></td>
                <td><?= $data->supplier_product_id ?></td>
                <td><?= ProductCategory::getNameById($data->material_type) ?></td>
                <td><?= $data->num ?></td>
                <td><?= $data->spec ?></td>
                <td><?= $data->unit ?></td>
                <td><?= number_format($data->purchase_price, 2) ?></td>
                <td><?= $data->buying_number ?></td>
                <td><?= Html::textInput("goodsPrice[".$data->id."]", $data->purchase_price,["class" => "goodsPrice verifyFloat",'style' => 'width: 80%;', 'i' => $data->id]);?></td>
                <td><?= Html::textInput('buyingNum['.$data->id.']', $data->buying_number, ["class" => "selGoodsNum", "onblur"=>"javascript:ckprto(".$data->id.")", "onkeyup"=>"value=value.replace(/\D/g,'')",'style' => 'width:80%']) ?></td>
                <td><span class="productTotalAmount"><?= number_format($data->total_amount, 2) ?></span></td>
            </tr>
        <?php } ?>
    </table>
   <div class="buttons">
      <a class="button blue-button" href="javascript:void(0)" save-data="<?= Url::to(['wbuying/finish', "id"=>$item->id]) ?>">入库</a> 
      <a class="button blue-button" href="javascript:history.back(-1)">返回</a>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<?= $this->context->renderPartial('/jquery/js') ?>
<?= $this->context->renderPartial('/jquery/dateInput') ?>
<?= $this->context->renderPartial('/jquery/excel') ?>
<?php
$js = <<<JS
    $(".goodsPrice").blur(function(){
        var id = $(this).attr("i");
        ckprto(id);
    });
JS;
Yii::$app->getView()->registerJs($js, \yii\web\View::POS_READY, 'addStock');
?>
<script>
    function ckprto(id){
        var sum = $(".tr_"+id+" .selGoodsNum").val();
        $(".tr_"+id+" .productTotalAmount").text("");
        if(!sum) {
            return false;
        }
        var price = $(".tr_"+id+" .goodsPrice").val();
        if(!price) {
            return false;
        }
        var total = accMul(sum , price);
        $(".tr_"+id+" .productTotalAmount").text(parseFloat(total).toFixed(2));
        var ztotal = 0;
        $(".productTotalAmount").each(function(){
            ztotal = ztotal * 1 + $(this).text() * 1;
        });
        $(".totalAmount").text(parseFloat(ztotal).toFixed(2));
    }
</script>
