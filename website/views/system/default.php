<?php
use common\models\Menu;
use yii\widgets\ActiveForm;

$this->title = '系统基础数据-开业清库';
?>

<?= $this->context->renderPartial('/public/menu') ?>
<?php
use yii\helpers\Url;
use yii\helpers\Html;
?>
<div class="main-container">
    <?php $form = ActiveForm::begin(); ?>
        <table class="table-list taleft">
            <tbody>
                <tr>
                    <td>超级管理员开业清库密码：</td>
                    <td><?= Html::activePasswordInput($model, 'set_value', ["maxlength" => 20, 'style'=>'width:60%;']) ?></td>
                </tr>
            </tbody>
        </table>
        <div class="buttons">
            <input type="text" name="isCheck" value="0" style="display: none;"/>
            <a class="button blue-button" href="javascript:void(0)" clean-data="<?= Url::to(['system/default']) ?>">确认开业清库</a> 
        </div>
    <?php ActiveForm::end(); ?>
</div>
<?= $this->context->renderPartial('/site/popping') ?>
<?= $this->context->renderPartial('/jquery/js') ?>