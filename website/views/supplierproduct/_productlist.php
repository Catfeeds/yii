<?php 
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;
use common\models\Product;
use common\models\ProductCategory;
?>
<script src="/script/jquery.js"></script>
<link href="/style/reset.css" rel="stylesheet">
<link href="/style/form.css" rel="stylesheet">
<link href="/style/global.css" rel="stylesheet"> 
<div class="main-container">
    <div class="filter">
        <form method="get">
            <input type="hidden" name="r" value="supplierproduct/ajaxproductlist" />
            <span>物料名称</span>
            <input class="form-text verifySpecial" type="text" placeholder="关键字..." name="keyword" value="<?= Yii::$app->request->get('keyword') ?>" onkeyup="javascript:validateValue(this)"/>
            <span>条形码</span>
            <input class="form-text verifySpecial" type="text" placeholder="条形码..." name="barcode" value="<?= Yii::$app->request->get('barcode') ?>" onkeyup="javascript:validateValue(this)"/>
            <span>出品编码</span>
            <input class="form-text verifySpecial" type="text" placeholder="出品编码..." name="num" value="<?= Yii::$app->request->get('num') ?>" onkeyup="javascript:validateValue(this)"/>
            <input type="hidden" value="<?= Yii::$app->request->get('id') ?>" name="id" />
            <input class="form-button" type="submit" value="搜索" />
        </form>
    </div>

    <?php $form = ActiveForm::begin(); ?>
    <table id="table-list" class="table-list">
        <tr>
            <th width="5%"><input type="checkbox" class="selGoodsAll" style="width: 16px;height: 16px;margin: 5px;"/>序号</th>
            <th width="10%">物料名称</th>
            <th width="10%">物料条形码</th>
            <th width="10%">出品编码</th>
            <th width="5%">物料类别</th>
            <th width="10%">物料分类</th>
            <th width="10%">进货单价</th>
            <th width="5%">规格</th>
            <th width="5%">单位</th>
            <th width="5%">操作数量</th>
        </tr>
        <?php foreach($listDatas as $key => $data){  ?>
            <tr>
                <td><input type="checkbox" class="selGoods" value="<?= $data->id ?>" style="width: 16px;height: 16px;margin: 5px;"/><?= $key+1;?></td>
                <td><a href="javascript:void(0)" class="selectGoods" i="<?= $data->id ?>"><?= $data->name ?></a></td>
                <td><span class="productBarcode_<?= $data->id ?>"><?= $data->barcode ?></span></td>
                <td><span class="productNum_<?= $data->id ?>"><?= $data->num ?></span></td>
                <td><span class="productType_<?= $data->id ?>"><?= Product::showTypeName($data->material_type) ?></span></td>
                <td><span class="productCate_<?= $data->id ?>"><?= ProductCategory::getNameById($data->product_category_id) ?></span></td>
                <td><span class="productPrice_<?= $data->id ?>"><?= $data->purchase_price ?></span></td>
                <td><span class="productSpec_<?= $data->id ?>"><?= $data->spec ?></span></td>
                <td><span class="productUnit_<?= $data->id ?>"><?= $data->unit ?></span></td>
                <td><input type="type" class="selGoodsNum_<?= $data->id ?>" value="" onkeyup="value=value.replace(/\D/g,'')"/></td>
            </tr>
        <?php } ?>
    </table>
    <a class="button blue-button selColse" href="javascript:void(0)" style="margin-top: 10px;font-size: 14px;line-height: 30px;padding: 0px 8px;">选中关闭</a>
    <?= LinkPager::widget([
        'pagination' => $listPages,
    ]); ?>

    <?php ActiveForm::end(); ?>
</div>
<script>
    function validateValue(textbox) {  
        var val = textbox.value.replace(/[^\a-\z\A-\Z0-9\u4E00-\u9FA5]/g,'');
        textbox.value = val;
    }  
    $(function () {
        if($(window).width() < 1200) {
            var width = $(".main-container").find(".table-list").css("width");
            width = width.replace("px", "");
            var newWidth = width*1 + 200*1;
            $(".wapper").attr("style", "width:"+newWidth+"px");
        }
        $("input[type='text']").attr("maxLength", 20);
        $(".verifySpecial").attr("onkeyup","javascript:validateValue(this)");
        $(".verifySpecial").blur(function(){
            validateValue(this);
        });
        $(".selGoodsAll").change(function(){
            if($(this).attr("checked") == "checked") {
                $("input[type='checkbox']").attr("checked","checked");
            } else {
                $("input[type='checkbox']").removeAttr("checked");
            }
        });
        $(".selColse").click(function(){
            var selData = new Array();
            var isSub = false;
            var n = 0;
            $(".selGoods").each(function(index){
                if($(this).attr("checked") == "checked") {
                    var i = $(this).val();
                    selData[n] = new Array();
                    selData[n]['selStockId'] = i;
                    selData[n]["selGoodsId"] = $(".productId_"+i).val();
                    selData[n]["selectProductName"] = $(".selectGoods[i='"+i+"']").text();
                    selData[n]["productType"] = $(".productType_"+i).text();
                    selData[n]["productCate"] = $(".productCate_"+i).text();
                    selData[n]["productBarcode"] = $(".productBarcode_"+i).text();
                    selData[n]["productSpec"] = $(".productSpec_"+i).text();
                    selData[n]["productUnit"] = $(".productUnit_"+i).text();
                    selData[n]["productPrice"] = $(".productPrice_"+i).text();
                    selData[n]["selGoodsNum"] = $(".selGoodsNum_"+i).val();
                    isSub = true;
                    n++;
                }
            });
            if(isSub) {
                window.parent.func(selData); 
            }
        });
        $(".selectGoods").click(function(){
            var selData = new Array();
            var i = $(this).attr("i");
            var n = 0;
            selData[n] = new Array();
            selData[n]['selStockId'] = i;
            selData[n]["selGoodsId"] = $(".productId_"+i).val();
            selData[n]["selectProductName"] = $(".selectGoods[i='"+i+"']").text();
            selData[n]["productType"] = $(".productType_"+i).text();
            selData[n]["productCate"] = $(".productCate_"+i).text();
            selData[n]["productBarcode"] = $(".productBarcode_"+i).text();
            selData[n]["productSpec"] = $(".productSpec_"+i).text();
            selData[n]["productUnit"] = $(".productUnit_"+i).text();
            selData[n]["productPrice"] = $(".productPrice_"+i).text();
            selData[n]["selGoodsNum"] = $(".selGoodsNum_"+i).val();
            window.parent.func(selData);  
        });
    });
</script>
