<?php
$js = "";
if(!empty($message)) {
    $js .= "alert('{$message}');";
}
$js .= <<<JS
    $(".selDate").attr("readonly", "readonly").date_input();
JS;

Yii::$app->getView()->registerJsFile('script/jquery.date_input.js',['depends'=>['app_web\assets\AppAsset']]);
Yii::$app->getView()->registerJs($js, \yii\web\View::POS_READY, 'dateInput');