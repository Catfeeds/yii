<?php
$js = <<<JS
    $(document).on("click", ".reject", function(){
        if($(window).width() > 1200) {
            $(".tanchuang").attr("style","width:400px;height:300px;right:40%;display: none");
        } else {
            $(".tanchuang").attr("style","display: none;width: 40%;height: 300px;min-width: 40%;right: 30%;");
        }
        var html = '<div style="margin-left:60px;"><div>驳回理由：</div>';
        html += '<div><textarea class="failCause" name="failCause" id="failCause" ></textarea></div>';
        html += '<div><a class="button blue-button submitReject" href="javascript:void(0);">提交</a>';
        html += '<input type="hidden" class="rejectUrl" value="" />';
        html += '<a class="button blue-button closeReject" href="javascript:void(0);" style="margin-left: 20px;">取消</a></div></div>';
        $(".tanchuang .showContent").html(html);
        var selKey = $(this).attr("i");
        $(".tanchuang #selKey").val(selKey);
        var rejectUrl = $(this).attr("reject_href");
        $("#failCause").val("");
        $(".rejectUrl").val(rejectUrl);
        $(".houtai_overlay").show();
        $(".tanchuang").show();
    }).on("click", ".closeReject", function(){ 
        $(".houtai_overlay").hide();
        $(".tanchuang").hide();
    }).on("click", ".submitReject", function(){ 
        var failCause = $("#failCause").val();
        if(!failCause){
            alert("请填写驳回理由");
            return false;
        }
        $(this).addClass("submitRejectBack").removeClass("submitReject");
        var rejectUrl = $(".rejectUrl").val();
        $.get(rejectUrl,{"failCause":failCause}, function(result){
            alert(result.message);
            if(!result.error) {
                location.reload();
            }
            $(".submitRejectBack").addClass("submitReject").removeClass("submitRejectBack");
        }, "json");
    });
JS;

Yii::$app->getView()->registerJs($js, \yii\web\View::POS_READY, 'wplanningReject');