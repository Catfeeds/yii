<?php
$js = <<<JS
//    if($(window).width() < 1200) {
        var width = $(".main-container").find(".table-list").css("width");
        var wapperWidth = $(".wapper").css("width");
        width = width.replace("px", "");
        wapperWidth = wapperWidth.replace("px", "");
        if(width * 1 + 200 * 1 > wapperWidth) {
            var newWidth = width*1 + 200*1;
            $(".wapper").attr("style", "width:"+newWidth+"px");
        }
//    }
    $(window).resize(function(){
        $(".wapper").removeAttr("style");
        var width = $(".main-container").find(".table-list").css("width");
        var wapperWidth = $(".wapper").css("width");
        width = width.replace("px", "");
        wapperWidth = wapperWidth.replace("px", "");
        if(width * 1 + 200 * 1 > wapperWidth) {
            var newWidth = width*1 + 200*1;
            $(".wapper").attr("style", "width:"+newWidth+"px");
        }
    });
    $("input[type='text']").attr("maxLength", 20);
    $(".verifySpecial").attr("onkeyup","javascript:validateValue(this)");
    $(".verifySpecial").blur(function(){
        validateValue(this);
    });
    $(".verifyFloat").attr("onkeyup", "javascript:CheckInputIntFloat(this)");
    $(".verifyFloat").blur(function(){
        CheckInputIntFloat(this);
    });
    $(".verifyMinus").attr("onkeyup", "javascript:CheckInputIntMinus(this)");
    $(".verifyMinus").blur(function(){
        CheckInputIntMinus(this);
    });
    $(document).on("click", ".jumpPage",function(){
        var page = $(this).parent("ul").find("input").val();
        if(!page) {
            return false;
        }
        var nowPage = $(this).attr("nowPage");
        var pageCount = $(this).attr("pageCount");
        if(page * 1 > pageCount * 1) {
            alert("当前数据最大页数为："+pageCount);
            return false;
        }
        var search = window.location.href;
        if(search.indexOf("&page="+nowPage+"&") > 0 ){
            var newUrl = search.replace("&page="+nowPage+"&", "&page="+page+"&");
        } else {
            var newUrl = search+"&page="+page;
        }
        window.location.href = newUrl;
    });
    $(document).on('click', 'a[get-create-form]', function(){
        var table = $('#table-list');
        var url = $(this).attr('get-create-form');
        
        $.get(url, function(html){
            table.append(html);
        });
    });
    $(document).on('click', 'form[method="get"] input[type="submit"]', function(){
        var beginDate = $("form[method='get'] input[name='beginDate']").val();
        var endDate = $("form[method='get'] input[name='endDate']").val();
        if(beginDate && endDate){
            var start = new Date(beginDate.replace("-", "/").replace("-", "/"));
            var end = new Date(endDate.replace("-", "/").replace("-", "/"));
            if(start > end){ 
                alert("起始时间不能大于结束时间！");
                return false;
            }
        }
        return true;
    });
    $(document).on('click', 'a[get-update-form]', function(){
        var tr = $(this).closest('tr');
        var url = $(this).attr('get-update-form');
        
        if($('#quick-form').length > 0){
            $('#quick-form').prev('tr').show();
            $('#quick-form').remove();
        }
        
        $.get(url, function(html){
            tr.after(html);
            tr.hide();
        });
    });

    $(document).on('click', 'a[save-data]', function(){
        var saveClick = $(this);
        var form = saveClick.closest('form');
        var tr = saveClick.closest('tr');
        var url = saveClick.attr('save-data');
        saveClick.attr('save-data_backup', url);
        saveClick.removeAttr("save-data");
        $.post(url, form.serialize(), function(datas){
            if(datas.html){
                alert(datas.message ? datas.message : "保存成功");
                location.reload();
                return true;
            }
            if(datas.message){
                alert(datas.message);
                saveClick.attr('save-data', url);
                saveClick.removeAttr("save-data_backup");
            }
            if(datas.type == "url") {
                window.location.href = datas.url;
            }
        }, 'json');
    });
    
    $(document).on('click', 'a[delete-data]', function(){
        var form = $(this).closest('form');
        var tr = $(this).closest('tr');
        var url = $(this).attr('delete-data');
        
        if(confirm('确定要删除吗？')){
            $.post(url, form.serialize(), function(datas){
                if(datas.message){
                    alert(datas.message);
                }
                if(!datas.error){
                    location.reload();
                }
            }, 'json');
        }
    });
        
    $(document).on('click', 'a[cancel-data]', function(){     
        var url = $(this).attr("cancel-data");   
        if(confirm('您确定要取消该条记录吗？')){
            $.post(url, {}, function(datas){
                if(datas.message){
                    alert(datas.message);
                }
                if(!datas.error){
                    location.reload();
                }
            }, 'json');
        }
    });
    
    $(document).on('click', 'a[recover-data]', function(){
        var url = $(this).attr('recover-data');        
        if(confirm('确定要还原到此版本吗？')){
            if($(window).width() > 1200) {
                $(".tanchuang").attr("style","width:400px;height:100px;right:40%;display: none");
            } else {
                $(".tanchuang").attr("style","display: none;width: 40%;height: 100px;min-width: 40%;right: 30%;");
            }
            var html = '<div style="    margin: 0 50px">还原中···， 请稍候！</div>';
            $(".tanchuang .nui-msgbox-close").remove();
            $(".tanchuang .showContent").html(html);
            $(".houtai_overlay").show();
            $(".tanchuang").show();
            $.post(url, {}, function(datas){
                $(".houtai_overlay").hide();
                $(".tanchuang").hide();
                if(datas.message){
                    alert(datas.message);
                }
                if(!datas.error){
                    location.reload();
                }
            }, 'json');
        }
    });
    $(document).on('click', 'a[backup-data]', function(){
        var url = $(this).attr('backup-data');  
        if($(window).width() > 1200) {
            $(".tanchuang").attr("style","width:400px;height:100px;right:40%;display: none");
        } else {
            $(".tanchuang").attr("style","display: none;width: 40%;height: 100px;min-width: 40%;right: 30%;");
        }
        var html = '<div style="    margin: 0 50px">备份中···， 请稍候！</div>';
        $(".tanchuang .nui-msgbox-close").remove();
        $(".tanchuang .showContent").html(html);
        $(".houtai_overlay").show();
        $(".tanchuang").show();
        $.post(url, {}, function(datas){
            $(".houtai_overlay").hide();
            $(".tanchuang").hide();
            if(datas.message){
                alert(datas.message);
            }
            if(!datas.error){
                location.reload();
            }
        }, 'json');
    });
    
    $(document).on('click', 'a[invalid-data]', function(){
        var form = $(this).closest('form');
        var tr = $(this).closest('tr');
        var url = $(this).attr('invalid-data');
        
        if(confirm('确定要设置为无效吗？')){
            $.post(url, form.serialize(), function(datas){
                if(datas.message){
                    alert(datas.message);
                    location.reload();
                }
                if(!datas.error){
                    location.reload();
                }
            }, 'json');
        }
    });
        
    $(document).on('click', 'a[valid-data]', function(){
        var form = $(this).closest('form');
        var tr = $(this).closest('tr');
        var url = $(this).attr('valid-data');
        
        if(confirm('确定要设置为有效吗？')){
            $.post(url, form.serialize(), function(datas){
                if(datas.message){
                    alert(datas.message);
                    location.reload();
                }
                if(!datas.error){
                    location.reload();
                }
            }, 'json');
        }
    });
        
    $(document).on('click', 'a[resetpwd-data]', function(){
        var url = $(this).attr('resetpwd-data');
        
        if(confirm('确定要重置该角色密码吗？')){
            $.post(url, {}, function(datas){
                if(datas.message){
                    alert(datas.message);
                }
                if(!datas.error){
                    location.reload();
                }
            }, 'json');
        }
    });
    
    $(document).on('click', 'a[ajax-data]', function(){
        var form = $(this).closest('form');
        var tr = $(this).closest('tr');
        var url = $(this).attr('ajax-data');
        $.post(url, form.serialize(), function(datas){
            if(datas.message){
                alert(datas.message);
            }
        }, 'json');
    });
    
    $(document).on('click', 'a[get-update-reload]', function(){
        var tr = $(this).closest('tr');
        var url = $(this).attr('get-update-reload');        
        $.get(url, function(html){
            alert(html.message);
            if(!html.error){
                location.reload();
            }
        }, "json");
    });
    
    $(document).on('click', '.confirmPass', function(){
        $(this).addClass("confirmPassBack").removeClass("confirmPass");
        var remark = "流程定时通过";
        var confitmUrl = $(this).attr('confirm-url');
        $.get(confitmUrl,{"remark":remark}, function(result){
            alert(result.message);
            if(!result.error) {
                location.reload();
            }
            $(".confirmPassBack").addClass("confirmPass").removeClass("confirmPassBack");
        }, "json");
    });
    
    $(document).on('click', '.confirmReject', function(){
        $(this).addClass("confirmRejectBack").removeClass("confirmReject");
        var remark = "流程超时驳回";
        var confitmUrl = $(this).attr('confirm-url');
        $.get(confitmUrl,{"failCause":remark}, function(result){
            alert(result.message);
            if(!result.error) {
                location.reload();
            }
            $(".confirmRejectBack").addClass("confirmReject").removeClass("confirmRejectBack");
        }, "json");
    });
    
    $(document).on('click', '.unionReject', function(){
        $(this).addClass("unionRejectBack").removeClass("unionReject");
        var remark = "来源订单驳回联动此流程驳回";
        var confitmUrl = $(this).attr('confirm-url');
        $.get(confitmUrl,{"failCause":remark}, function(result){
            alert(result.message);
            if(!result.error) {
                location.reload();
            }
            $(".unionRejectBack").addClass("unionReject").removeClass("unionRejectBack");
        }, "json");
    });
        
    $(document).on('click', 'a[download-excel]', function(){
//        var search = window.location.href;
//        if(search.indexOf("&isDownload=0") > 0 ){
//            var newUrl = search.replace("&isDownload=0", "&isDownload=1");
//        } else {
//            var newUrl = search+"&isDownload=1";
//        }
//        window.open(newUrl);
//        window.close();
        var className = $(this).attr('download-excel'); 
        if($("#isDownload").length > 0 && $("."+className).length > 0) {
            $("#isDownload").val("1");   
            $("."+className).parent("form").submit();
            $("#isDownload").val("0"); 
        }
    });
        
    $(document).on('click', 'a[clean-data]', function(){
        var pwd = $("#config-set_value").val();
        if(!pwd){
            alert("请填写超级管理员密码准入密码");
            return false;
        }
        if(confirm("确定要开业清库吗")){
            var url = $(this).attr('clean-data'); 
            var form = $(this).closest('form');
            $.post(url, form.serialize(), function(datas){
                if(datas.message){
                    alert(datas.message);
                }
                if(datas.type == "url") {
                    if($(window).width() > 1200) {
                        $(".tanchuang").attr("style","width:400px;height:100px;right:40%;display: none");
                    } else {
                        $(".tanchuang").attr("style","display: none;width: 40%;height: 100px;min-width: 40%;right: 30%;");
                    }
                    var html = '<div style="margin: 0 50px">开业清库中···， 请稍候！</div>';
                    $(".tanchuang .nui-msgbox-close").remove();
                    $(".tanchuang .showContent").html(html);
                    $(".houtai_overlay").show();
                    $(".tanchuang").show();
                    $.post(datas.url, {}, function(result) {
                        $(".houtai_overlay").hide();
                        $(".tanchuang").hide();
                        if(result.message){
                            alert(result.message);
                        }
                        if(result.type == "url") {
                            window.location.href = result.url;
                        }
                    }, 'json');
                }
            }, 'json');
        }
    });
JS;

Yii::$app->getView()->registerJs($js, \yii\web\View::POS_READY, 'public');
?>
<script type="text/javascript">
    //不可以输入特殊字符串
    function validateValue(textbox) {  
        var val = textbox.value.replace(/[^\a-\z\A-\Z0-9\u4E00-\u9FA5]/g,'');
        textbox.value = val;
    }     
    //只可以输入整型或浮点型
    function CheckInputIntFloat(oInput)  { 
        if('' != oInput.value.replace(/\d{1,}\.{0,1}\d{0,}/,''))  { 
            oInput.value = oInput.value.match(/\d{1,}\.{0,1}\d{0,}/) == null ? '' : oInput.value.match(/\d{1,}\.{0,1}\d{0,}/); 
        } 
    }
    //只可以输入整型或浮点型，可以为负
    function CheckInputIntMinus(oInput)  { 
        var inputVal = oInput.value;
        var f = "";
        var e = inputVal;
        if(inputVal.indexOf("-") == 0) {
            f = inputVal.substr(0, 1);
            e = inputVal.substr(1);
        }      
        if('' != e.replace(/\d{1,}\.{0,1}\d{0,}/,''))  { 
            oInput.value = f + (e.match(/\d{1,}\.{0,1}\d{0,}/) == null ? '' : e.match(/\d{1,}\.{0,1}\d{0,}/)); 
        } 
    }
    //乘法
    function accMul(arg1,arg2) {
      var m=0,s1=arg1.toString(),s2=arg2.toString();
      try{ m += s1.split(".")[1].length } catch(e) {}
      try{ m += s2.split(".")[1].length } catch(e) {}
      return Number(s1.replace(".","")) * Number(s2.replace(".","")) / Math.pow(10,m);
    }
    //减法
    function accSub(arg1, arg2) {
        var r1, r2, m, n;
        try { r1 = arg1.toString().split(".")[1].length } catch(e) { r1 = 0}
        try { r2 = arg2.toString().split(".")[1].length } catch(e) { r2 = 0}
        m = Math.pow(10, Math.max(r1, r2));
        n = (r1 >= r2) ? r1 : r2;
        return ((arg1 * m - arg2 * m) / m).toFixed(n);
    }
    //除法
    function accDiv(arg1,arg2){
        var t1=0,t2=0,r1,r2;
        try{ t1 = arg1.toString().split(".")[1].length } catch(e) {}
        try{ t2 = arg2.toString().split(".")[1].length } catch(e) {}
        with(Math){
            r1 = Number(arg1.toString().replace(".",""));
            r2 = Number(arg2.toString().replace(".",""));
            return accMul(r1/r2, pow(10, t2-t1))
        }
    }
</script>