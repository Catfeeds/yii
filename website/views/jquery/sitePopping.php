<?php
$js = <<<JS
    $(".nui-msgbox-close").click(function(){
        $(".houtai_overlay").hide();
        $(".tanchuang").hide();
    });
JS;

Yii::$app->getView()->registerJs($js, \yii\web\View::POS_READY, 'sitePopping');