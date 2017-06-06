<?php 
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;
use common\models\Product;
?>
<script src="/script/jquery.js"></script>
<link href="/style/reset.css" rel="stylesheet">
<link href="/style/form.css" rel="stylesheet">
<link href="/style/global.css" rel="stylesheet"> 
<div class="main-container">
    <div class="filter">
        <form method="get">
            <input type="hidden" name="r" value="product/ajaxproductlist" />
            <span>物料名称</span>
            <input class="form-text" type="text" placeholder="关键字..." name="keyword" value="<?= Yii::$app->request->get('keyword') ?>" />
            <span>条形码</span>
            <input class="form-text" type="text" placeholder="关键字..." name="barcode" value="<?= Yii::$app->request->get('barcode') ?>" />
            <input class="form-button" type="submit" value="搜索" />
        </form>
    </div>

    <?php $form = ActiveForm::begin(); ?>
    <table id="table-list" class="table-list">
        <tr>
            <th width="10%">物料名称</th>
            <th width="10%">物料ID</th>
            <th width="10%">供应商</th>
            <th width="10%">供应商物料ID</th>
            <th width="10%">物料类型</th>
            <th width="10%">条形码ID</th>
            <th width="10%">规格</th>
            <th width="10%">单位</th>
            <th width="10%">采购价格</th>
            <th width="10%">销售定价</th>
        </tr>
        <?php foreach($listDatas as $data){ ?>
            <tr>
                <td><a href="javascript:void(0)" class="selectGoods" i="<?= $data->id ?>"><?= $data->name ?></a></td>
                <td><?= $data->id ?></td>
                <td><span class="supplierName_<?= $data->id ?>"><?= $data->showSupplierName() ?></span></td>
                <td><?= $data->supplier_product_id ?></td>
                <td><span class="productCate_<?= $data->id ?>"><?= Product::showTypeName($data->material_type) ?></span></td>
                <td><span class="productBarcode_<?= $data->id ?>"><?= $data->barcode ?></span></td>
                <td><span class="productSpec_<?= $data->id ?>"><?= $data->spec ?></span></td>
                <td><span class="productUnit_<?= $data->id ?>"><?= $data->unit ?></span></td>
                <td><span class="productPrice_<?= $data->id ?>"><?= $data->purchase_price ?></span></td>
                <td><?= number_format($data->sale_price, 2) ?></td>
            </tr>
        <?php } ?>
    </table>

    <?= LinkPager::widget([
        'pagination' => $listPages,
    ]); ?>

    <?php ActiveForm::end(); ?>
</div>
<script>
    $(function () {
        $(".selectGoods").click(function(){
            var productId = $(this).attr("i");
            var selectProductName = $(this).text();
            var supplierName = $(".supplierName_"+productId).text();
            var productCate = $(".productCate_"+productId).text();
            var productBarcode = $(".productBarcode_"+productId).text();
            var productSpec = $(".productSpec_"+productId).text();
            var productUnit = $(".productUnit_"+productId).text();
            var productPrice = $(".productPrice_"+productId).text();
            window.parent.func(productId, selectProductName, supplierName, productCate, productBarcode, productSpec, productUnit, productPrice);  
        });
    });
</script>
