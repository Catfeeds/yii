<?php
    $num = empty($num) ? 6 : $num;
$js = <<<JS
    var i = {$num};
    $(document).on("click", ".addGoods", function(){
        $(this).addClass("delGoods").removeClass("addGoods");
        $(this).text("删除");
        var html = '<tr class="showGoodsTr tr_'+i+'" i="0"><td><span class="supplierName"></span></td><td><input type="text" class="selGoodsName" name="goodsName[]" value="" readonly="readonly" i="'+i+'">';
        html += '</td><td><span class="goodsBarcode"></span></td><td><span class="goodsCate"></span></td><td><span class="goodsPrice"></span></td>';
        html += '<td><input type="text" name="goodsNum[]" value="0" onblur="javascript:ckprto('+i+')" class="selGoodsNum" onkeyup="value=value.replace(/\\\D/g,\'\')"></td>';
        html += '<td><span class="goodsTotalMoney">0</span></td><td><span class="goodsSpec"></span></td><td><span class="goodsUnit"></span></td><td>';
        html += '<input type="hidden" name="goodsId[]" value="0" class="selGoodsId"><a href="javascript:void(0)" class="addGoods">添加</a></td></tr>';
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
    }).on("click", ".selGoodsName", function(){
        var selKey = $(this).attr("i");
        $(".tanchuang #selKey").val(selKey);
        $(".houtai_overlay").show();
        $(".tanchuang").show();
        var html = '<iframe name="mainFrame" src="?r=supplierproduct/ajaxproductlist"  frameborder="0" scrolling="auto" style="width: 100%;height: 480px;"></iframe>';
        $(".tanchuang .showContent").html(html)
    });
JS;

Yii::$app->getView()->registerJs($js, \yii\web\View::POS_READY, 'wplanningAdd');