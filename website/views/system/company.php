<?php
use common\models\Menu;
use yii\widgets\ActiveForm;
$this->title = '系统基础数据-公司名称';
?>
<?= $this->context->renderPartial('/public/menu') ?>
<?php
use yii\helpers\Url;
use yii\helpers\Html;
?>
<div class="main-container">
    <?php
        $form = ActiveForm::begin();
    ?>
    <table class="table-list">
        <tbody>
            <tr>
                <td>公司名称：</td>
                <td style="text-align: left;margin-left: 10px;">
                    <?= $form->field($model, 'set_value')->label(false)->textInput(['maxlength' => 20, "onkeyup" => "javascript:validateValue(this)", 'class' => "verifySpecial"]) ?></td>
            </tr>
        </tbody>
    </table>
    <div class="buttons">
        <?= Html::submitButton('保存', ['class' => 'button blue-button']) ?>
           <a class="button blue-button"  href="<?= Url::to(['site/index']) ?>" >取消</a>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<?= $this->context->renderPartial('/jquery/js') ?>
<?= $this->context->renderPartial('/jquery/dateInput', compact("message")) ?>