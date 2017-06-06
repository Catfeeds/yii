<?php
use common\models\Menu;
use yii\widgets\ActiveForm;
$this->title = '系统基础数据-logo设置';
?>
<?= $this->context->renderPartial('/public/menu') ?>
<?php
    use yii\helpers\Url;
    use yii\helpers\Html;
?>
<div class="main-container">
    <?php
        $form = ActiveForm::begin([
            'options' => [
                'enctype' => 'multipart/form-data',
            ]
        ]);
    ?>
    <table class="table-list" style="width: 97%;">
        <tbody>
            <tr>
                <td>logo：</td>
                <td class="text-left" style="white-space: normal;">
                    <?= Html::activeFileInput($model, 'set_value') ?>
                    <img class="preview-image" src="<?= \libs\Utils::getImage($model->set_value) ?>" />
                </td>
            </tr>
        </tbody>
    </table>
    <div class="buttons">
        <input type="hidden" id="isSelFile" value="0" >
        <?= Html::submitButton('保存', ['class' => 'button blue-button confirmSub']) ?>
          <a class="button blue-button"  href="javascript:;" onclick="window.location.reload()">取消</a>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<?= $this->context->renderPartial('/jquery/js') ?>
<?php
$js = <<<JS
    $('input[type="file"]').change(function(){
        var input = $(this);
        var file = this.files[0];
        var typeArr = ["image/png", "image/jpg", "image/jpeg"];
        if($.inArray(file.type, typeArr) == -1) {
            alert("图片格式错误，请重新选择");
            $("#isSelFile").val("0");
            $(this).val("");
            return false;
        }
        var reader = new FileReader();
        reader.onload = function(){
            input.next('img').attr('src', reader.result);
        };
        $("#isSelFile").val("1");
        reader.readAsDataURL(file);
    });
    $(".confirmSub").click(function(){
        var isSelFile = $("#isSelFile").val();
        if(!isSelFile) {
            alert("请重新公司Logo");
            return false;
        }
        return true;
    });
JS;
if(!empty($message)) {
    $js .= "alert('{$message}');";
}
Yii::$app->getView()->registerJs($js, \yii\web\View::POS_READY, 'upload-image');
