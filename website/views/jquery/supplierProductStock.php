<?php
    $num = empty($num) ? 6 : $num;
    $type = empty($type) ? "default" : $type;
$js = <<<JS
    var i = {$num};
    $(".selSupplier").change(function(){
        $(".showGoodsTr").each(function(index){
            if($(this).attr("i") == "1") {
                var showTr = $(this);
                showTr.find(".selGoodsNum").val("");
                var i = showTr.find(".selGoodsName").attr("i");
                ckprto(i);
                showTr.find(".selGoodsId").val("0");
                showTr.find(".selStockId").val("0");
                showTr.find(".selGoodsName").val("");
                showTr.find(".reGoodsPrice").val("");
                showTr.find("span").text("");
                showTr.attr("i", "0");
            }
        });
    });
    $(document).on("click", ".addGoods", function(){
        $(this).addClass("delGoods").removeClass("addGoods");
        $(this).text("删除");
        if('{$type}' == "ordertemplate"){
            var html = '<tr class="showGoodsTr tr_'+i+'" i="0"><td><input type="text" class="selGoodsName" name="goodsName[]" value="" readonly="readonly" i="'+i+'" placeholder="点击选择商品"></td>';
            html += '<td><span class="goodsBarcode"></span></td><td><span class="goodsType"></span></td><td><span class="goodsCate"></span></td><td><span class="goodsPrice"></span></td>';
            html += '<td><span class="goodsSpec"></span></td><td><span class="goodsUnit"></span></td>';
            html += '<td><input type="text" class="selGoodsNum" name="goodsNum[]" value="" onblur="javascript:ckprto('+i+')" onkeyup="value=value.replace(/\\\D/g,\'\')"></td><td><span class="goodsTotalMoney"></span></td>';
            html += '<td><input type="hidden" class="selGoodsId" name="goodsId[]" value="0"><a href="javascript:void(0)" class="addGoods">添加</a></td></tr>';
        } else {
            var html = '<tr class="showGoodsTr tr_'+i+'" i="0"><td><input type="text" class="selGoodsName" name="goodsName[]" value="" readonly="readonly" i="'+i+'" placeholder="点击选择商品"></td>';
            html += '<td><span class="goodsBarcode"></span></td><td><span class="goodsType"></span></td><td><span class="goodsCate"></span></td><td><span class="goodsPrice"></span></td>';
            html += '<td><input type="text" class="reGoodsPrice verifyFloat" name="goodsPrice[]" value="" style="width: 80%;" i="'+i+'" maxlength="20" onkeyup="javascript:CheckInputIntFloat(this)"></td>';
            html += '<td><input type="text" class="selGoodsNum" name="goodsNum[]" value="" onblur="javascript:ckprto('+i+')" onkeyup="value=value.replace(/\\\D/g,\'\')"></td><td><span class="goodsTotalMoney"></span></td>';
            html += '<td><span class="goodsSpec"></span></td><td><span class="goodsUnit"></span></td>';
            html += '<td><input type="hidden" class="selGoodsId" name="goodsId[]" value="0"><a href="javascript:void(0)" class="addGoods">添加</a></td></tr>';
        }
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
        var supplierId = $(".selSupplier").val();
        if(!supplierId) {
            alert("请选择供应商");
            return false;
        }
        var selKey = $(this).attr("i");
        $(".tanchuang #selKey").val(selKey);
        $(".houtai_overlay").show();
        $(".tanchuang").show();
        var html = '<iframe name="mainFrame" src="?r=supplierproduct/ajaxproductlist&id='+supplierId+'" frameborder="0" scrolling="auto" style="width: 100%;height: 480px;"></iframe>';
        $(".tanchuang .showContent").html(html)
    });
JS;

Yii::$app->getView()->registerJs($js, \yii\web\View::POS_READY, 'supplierProductStock');