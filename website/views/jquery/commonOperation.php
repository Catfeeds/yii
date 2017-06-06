<?php
$js = <<<JS
    $(document).on("click", ".operation", function(){
        if($(this).attr("href")) {
            return true;
        }
        $(".tanchuang").attr("style","width: 400px;height: 300px;right: 40%;");
        var operatorName = $(this).text();
        var html = '<div style="margin-left:60px;"><div>'+operatorName+'说明：</div>';
        html += '<div><textarea class="remark" name="remark" id="remark" ></textarea></div>';
        html += '<div><a class="button blue-button submitRemark" href="javascript:void(0);">'+operatorName+'</a>';
        html += '<input type="hidden" class="operatorUrl" value="" />';
        html += '<a class="button blue-button closeRemark" href="javascript:void(0);" style="margin-left: 20px;">取消</a></div></div>';
        $(".tanchuang .showContent").html(html);
        $("#remark").attr("style","line-height: 18px;display: inline-block; height: 120px;cursor: text;overflow: auto;width: 270px;resize: none;max-width: 270px;max-height: 120px;");
        var operatorUrl = $(this).attr("operator_url");
        $("#remark").val("");
        $(".operatorUrl").val(operatorUrl);
        $(".houtai_overlay").show();
        $(".tanchuang").show();
    }).on("click", ".closeRemark", function(){ 
        $(".houtai_overlay").hide();
        $(".tanchuang").hide();
    }).on("click", ".submitRemark", function(){ 
        var remark = $("#remark").val();
        $(this).addClass("submitRemarkBack").removeClass("submitRemark");
        var operatorUrl = $(".operatorUrl").val();
        $.get(operatorUrl,{"remark":remark}, function(result){
            alert(result.message);
            if(!result.error) {
                location.reload();
            }
            $(".submitRemarkBack").addClass("submitRemark").removeClass("submitRemarkBack");
        }, "json");
    });
JS;

Yii::$app->getView()->registerJs($js, \yii\web\View::POS_READY, 'commonOperation');