<?php
use common\models\Menu;
use yii\widgets\ActiveForm;
$this->title = '系统基础数据-业务计算机设置';
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
                <td>授权台数：</td>
                <td><?= Html::activeTextInput($model, 'set_value', ['style' => 'width:80%', "onkeyup"=>"value=value.replace(/\D/g,'')"]) ?></td>
            </tr>
        </tbody>
    </table>
    <div class="buttons">
        <a class="button blue-button" href="javascript:void(0)" save-data="<?= Url::to(['computer/setnum']) ?>">保存</a>
        <a class="button blue-button"  href="javascript:history.back(-1)" >返回</a>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<?= $this->context->renderPartial('/jquery/js') ?>