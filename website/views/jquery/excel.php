<?php
$js = <<<JS
    var importUrl;
    
    $("input#uploadExcel").change(function(){
        $.ajaxFileUpload({
            url: importUrl,
            secureuri: true,
            data: {_csrf: $('meta[name="csrf-token"]').attr('content')},
            fileElementId: 'uploadExcel',
            dataType: 'json',
            success: function(data, status){
                if(data.result == 'Success'){
                    alert("上传成功");
                }else{
                    alert(data.message);
                }
                location.reload();
            },  
            error: function(data, status, e){  
                return;  
            }  
        });  
    });
    
    $("a[import-excel]").click(function(){
        importUrl = $(this).attr('import-excel');
        $("input#uploadExcel").click();
    });
    
JS;

Yii::$app->getView()->registerJs($js, \yii\web\View::POS_READY, 'excel');
Yii::$app->getView()->registerJsFile('/script/ajaxfileupload.js', ['depends' => 'yii\web\YiiAsset']);