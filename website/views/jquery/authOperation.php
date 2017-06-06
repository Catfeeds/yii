<?php
    use yii\helpers\Url;
    $authUrl = Url::to(["ajax/checkauth"]);
    $route = Yii::$app->requestedRoute;
    $params = implode(",", Yii::$app->requestedParams);
    $authKeyRoute = Yii::$app->session->get("authKeyRoute");
    $authKeyParams = Yii::$app->session->get("authKeyParams");
$js = <<<JS
    if("{$authKeyRoute}" && "{$authKeyParams}") {
        $(".authOperation").each(function(index){
            var operationName = $(this).attr("i");
            $(this).addClass(operationName).removeClass("authOperation");
        });
    }
    function showAuth(){
        if($(window).width() > 1200) {
            $(".tanchuang").attr("style","width:400px;height:150px;right:40%;display: none");
        } else {
            $(".tanchuang").attr("style","display: none;width: 40%;height: 150px;min-width: 40%;right: 30%;");
        }
        var html = '<div style="margin:0 0 20px 60px;">授权密码：';
        html += '<input type="password" id="authPassword" value="" name="authPassword" style="height:24px;padding:3px;" maxLength="20"></div>';
        html += '<div style="margin-left:60px;"><a class="button blue-button submitAuth" href="javascript:void(0);">授权</a>';
        html += '<a class="button blue-button closeAuth" href="javascript:void(0);" style="margin-left: 20px;">取消</a></div>';
        $(".tanchuang .showContent").html(html);
        $("#failCause").val("");
        $(".houtai_overlay").show();
        $(".tanchuang").show();
    }
    $(document).on('click', '.authOperation', function(){
        showAuth();
    }).on('click', '.closeAuth', function(){
        $(".tanchuang .showContent").html("");
        $(".houtai_overlay").hide();
        $(".tanchuang").removeAttr("style").hide();
    }).on('click', '.submitAuth', function(){
        var authPassword = $("#authPassword").val();
        if(!authPassword) {
            alert("请填写流程授权密码");
            return false;
        }
        $.get("{$authUrl}",{"authPassword":authPassword, "authKeyRoute":"$route", "authKeyParams":$params}, function(result){
            alert(result.message);
            if(result.error) {
                return false;
            }
            $(".authOperation").each(function(index){
                var operationName = $(this).attr("i");
                $(this).addClass(operationName).removeClass("authOperation");
            });
            $(".tanchuang .showContent").html("");
            $(".houtai_overlay").hide();
            $(".tanchuang").removeAttr("style").hide();
        }, "json");
    });
JS;
    
Yii::$app->getView()->registerJs($js, \yii\web\View::POS_READY, 'authOperation');