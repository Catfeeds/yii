<?php 
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;
use common\models\Product;
use common\models\Warehouse;
$warehouseAll = Warehouse::getAllByStatus(Warehouse::STATUS_OK);
$warehouseId = Yii::$app->request->get('warehouseId');
?>
<script src="/script/jquery.js"></script>
<script src="/script/jquery.jqprint-0.3.js"></script>
<script src="/script/jquery-migrate-1.2.1.js"></script>
<link href="/style/reset.css" rel="stylesheet">
<link href="/style/form.css" rel="stylesheet">
<link href="/style/global.css" rel="stylesheet"> 
<div class="main-container">
    <div class="filter">
        <form method="get">
            <input type="hidden" name="r" value="pstock/print" />
            <span>仓库名称</span>
            <?= Html::dropDownList('warehouseId', $warehouseId, $warehouseAll, ['prompt' => '请选择', 'class' => 'form-select']) ?>
            <input class="form-button" type="submit" value="搜索" />
        </form>
    </div>
    <div class="productList">
        <div style="font-size:24px;text-align: center;margin-bottom: 10px;">
            <?php echo isset($warehouseAll[$warehouseId]) ? $warehouseAll[$warehouseId] . "的" : ""?>库存物料列表</div>
    <?php $form = ActiveForm::begin(); ?>
    <table id="table-list" class="table-list">
        <tr>
            <th width="10%">物料名称</th>
            <th width="10%">物料条形码</th>
            <th width="10%">物料类型</th>
            <th width="10%">采购单价</th>
            <th width="10%">规格</th>
            <th width="10%">单位</th>
            <th width="10%">库存</th>
        </tr>
        <?php foreach($listDatas as $data){ 
             $productItem = Product::findOne($data->product_id);?>
            <tr>
                <td width="10%" style="text-align:center;"><?= $productItem->name ?></td>
                <td width="10%" style="text-align:center;"><?= $productItem->barcode ?></td>
                <td width="10%" style="text-align:center;"><?= Product::showTypeName($productItem->material_type) ?></td>
                <td width="10%" style="text-align:center;"><?= $productItem->purchase_price ?></td>
                <td width="10%" style="text-align:center;"><?= $productItem->spec ?></td>
                <td width="10%" style="text-align:center;"><?= $productItem->unit ?></td>
                <td width="10%" style="text-align:center;"><?= $data->number ?></td>
            </tr>
        <?php } ?>
    </table>
    <?= LinkPager::widget([
        'pagination' => $listPages,
    ]); ?>
    <?php ActiveForm::end(); ?>
    </div>
    <div class="buttons">
        <a class="button blue-button jqprint" href="javascript:void(0)">打印</a>
    </div>
</div>
<script>
    $(function () {
        $(".jqprint").click(function(){
            <?php if($warehouseId){?>
                $(".productList").jqprint();
            <?php } else { ?>
                alert("请选择仓库");
                return false;
            <?php }?>
        });
    });
</script>
