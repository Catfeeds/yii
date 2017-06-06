<?php
    //index.php?r=wcheckout%2Faddroutine&combId=3
$js = <<<JS
    if($(window).width() > 1200) {
        $(".tanchuang").attr("style","width:400px;height:150px;right:40%;display: none");
    } else {
        $(".tanchuang").attr("style","display: none;width: 40%;height: 150px;min-width: 40%;right: 30%;");
    }
    $(".sendCheckout").click(function(){
        var combId = $(this).attr("i");
        var html = '<div style="margin:0 0 20px 60px;">设置套数：';
        html += '<input type="text" id="combNum" value="1" name="combNum" onkeyup="value=value.replace(/\\\D/g,\'\')" style="height:24px;padding:3px;" maxLength="8"></div>';
        html += '<div style="margin-left:60px;"><a class="button blue-button submitAdd">提交</a>';
        html += '<a class="button blue-button closeAdd" href="javascript:void(0);" style="margin-left: 20px;">取消</a></div>';
        $(".tanchuang .showContent").html(html);
        $(".tanchuang #selKey").val(combId);
        $("#failCause").val("");
        $(".houtai_overlay").show();
        $(".tanchuang").show();
    });
    $(".setCommonAll").click(function(){
        var html = '<div style="margin:0 0 20px 60px;">设置通用设置天数：';
        html += '<input type="text" id="dayNum" value="1" name="dayNum" onkeyup="value=value.replace(/\\\D/g,\'\')" style="height:24px;padding:3px;" maxLength="3"></div>';
        html += '<div style="margin-left:60px;"><a class="button blue-button submitCommonDay">确定</a>';
        html += '<a class="button blue-button closeCommonDay" href="javascript:void(0);" style="margin-left: 20px;">取消</a></div>';
        $(".tanchuang .showContent").html(html);
        $(".houtai_overlay").show();
        $(".tanchuang").show();
    });
    $(document).on("click", ".closeAdd, .closeCommonDay", function(){ 
        $(".tanchuang #selKey").val("");
        $(".houtai_overlay").hide();
        $(".tanchuang").hide();
    }).on("click", ".submitAdd", function(){ 
        var combNum = $("#combNum").val();
        if(combNum <= 0){
            alert("模版套数必须要大于零");
            return false;
        }
        if(combNum > 99999999){
            alert("模版套数必须小于100000000");
            return false;
        }
        var combId = $(".tanchuang #selKey").val();
        window.location.href="/index.php?r=wcheckout/addroutine&combId="+combId+"&combNum="+combNum;
    }).on("click", ".submitCommonDay", function(){
        var dayNum = $("#dayNum").val();
        if(dayNum <= 0){
            alert("通用天数必须要大于零");
            return false;
        }
        if(dayNum > 365) {
            alert("通用天数最大为365天");
            return false;
        }
        $(".setDayNum").val(dayNum);
        $(".closeCommonDay").click();
    });
JS;
Yii::$app->getView()->registerJs($js, \yii\web\View::POS_READY, 'sendCheck');
