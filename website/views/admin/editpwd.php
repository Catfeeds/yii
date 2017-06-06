<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
?>
<?= $this->context->renderPartial('/public/menu') ?>
<div class="main-container">
   
    <?php $form = ActiveForm::begin(); ?>
    <table id="table-list" class="table-list taleft">
        <caption>修改密码</caption>
        <tr id="quick-form">
            <td><div style="width: 100px;float: left;">旧密码：</div><div style="width: 70%;float: left;">
                <?= Html::passwordInput('oldpwd', '',['style' => 'width:60%', 'maxLength' => 20]) ?></div></td>
        </tr>
        <tr>
            <td><div style="width: 100px;float: left;">新密码：</div><div style="width: 70%;float: left;">
                <?= Html::passwordInput('newpwd', '',['style' => 'width:60%', 'maxLength' => 20]) ?></div></td>
        </tr>
        <tr id="quick-form">
            <td><div style="width: 100px;float: left;">验证新密码：</div><div style="width: 70%;float: left;">
                <?= Html::passwordInput('yzpwd', '',['style' => 'width:60%', 'maxLength' => 20]) ?></div></td>
        </tr>
    </table>
   <div class="buttons">
      <a class="button blue-button" href="javascript:void(0)" save-data="<?= Url::to(['admin/editpwd']) ?>">保存</a> 
      <a class="button blue-button" href="javascript:history.back(-1)">返回</a>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<?= $this->context->renderPartial('/jquery/js') ?>