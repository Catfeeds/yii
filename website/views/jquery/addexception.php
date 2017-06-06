<?php
    use common\models\Product;
    use common\models\ProductCategory;
    $num = empty($num) ? 6 : $num;
    $typeHtml = '<select name="goodsType[]" size="1">';
    foreach (Product::getTypeSelectData() as $cateId => $cateName) {
        $typeHtml .= '<option value="'.$cateId.'">'.$cateName.'</option>';
    }
    $typeHtml .= "</select>";
    $cateHtml = '<select name="goodsCate[]" size="1">';
    foreach (ProductCategory::getCatrgorySelectData() as $cateId => $cateName) {
        $cateHtml .= '<option value="'.$cateId.'">'.$cateName.'</option>';
    }
    $cateHtml .= "</select>";
$js = <<<JS
    var i = {$num};
    $(document).on("click", ".addGoods", function(){
        $(this).addClass("delGoods").removeClass("addGoods");
        $(this).text("删除");
        var html = '<tr class="tr_'+i+'"><td><input type="text" name="goodsName[]" value="" class="verifySpecial"></td><td><input type="text" name="goodsBarcode[]" value="" class="verifySpecial"></td>';
        html += '<td>{$typeHtml}</td><td>{$cateHtml}</td><td><input type="text" class="reGoodsPrice verifyFloat" name="goodsPrice[]" value="" style="width: 80%;" i="'+i+'" maxlength="20" onkeyup="javascript:CheckInputIntFloat(this)"></td>';
        html += '<td><input type="text" class="selGoodsNum" name="goodsNum[]" value="" onblur="javascript:ckprto('+i+')" onkeyup="value=value.replace(/\\\D/g,\'\')"></td><td><span class="goodsTotalMoney">0.00</span></td>';
        html += '<td><input type="text" name="goodsSpec[]" value="" style="width:80%"  class="verifySpecial"></td><td><input type="text" name="goodsUnit[]" value="" style="width:80%"  class="verifySpecial"></td><td>';
        html += '<input type="hidden" class="selGoodsId" name="goodsId[]" value="0"><a href="javascript:void(0)" class="addGoods">添加</a></td></tr>';
        i++;
        $(".showGoodsList").append(html);
    }).on("click", ".delGoods", function(){
        var goodsId = $(this).parent("td").find("input").val();
        if(goodsId > 0) {
            if(!confirm("您确定要删除该商品吗", "温馨提示")){
                return false;
            }
            totalMoney();
        }
        $(this).parent("td").parent("tr").remove();
    });
JS;

Yii::$app->getView()->registerJs($js, \yii\web\View::POS_READY, 'wplanningAdd');