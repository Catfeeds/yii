<?php
use common\models\Menu;
use yii\widgets\ActiveForm;

$this->title = '系统基础数据-管理员权限';
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
    <table id="table-list" class="table-list taleft">
        <tbody>
        <tr>
            <td>重置超级管理员开业清库密码：</td>
            <td> 原密码：<?= Html::activePasswordInput($model, 'admin_ver_password', [ "maxlength" => 20, 'style'=>'width:60%']) ?></td>
            <td> 新密码一次：<?= Html::activePasswordInput($model, 'admin_ver_password1', [ "maxlength" => 20, 'style'=>'width:60%']) ?></td>
            <td> 新密码两次：<?= Html::activePasswordInput($model, 'admin_ver_password2', [ "maxlength" => 20, 'style'=>'width:60%']) ?></td>
        </tr>
<!--         <tr>
            <td>重置超级管理员业务密码：</td>
            <td> 原密码：<?= Html::activePasswordInput($model, 'admin_business_password', [ "maxlength" => 20, 'style'=>'width:60%']) ?></td>
            <td> 新密码一次：<?= Html::activePasswordInput($model, 'admin_business_password1', [ "maxlength" => 20, 'style'=>'width:60%']) ?></td>
            <td> 新密码两次：<?= Html::activePasswordInput($model, 'admin_business_password2', [ "maxlength" => 20, 'style'=>'width:60%']) ?></td>
        </tr>
        
         <tr>
            <td>重置业务管理员业务密码：</td>
            <td> 原密码：<?= Html::activePasswordInput($model, 'business_business_password', [ "maxlength" => 20, 'style'=>'width:60%']) ?></td>
            <td> 新密码一次：<?= Html::activePasswordInput($model, 'business_business_password1', [ "maxlength" => 20, 'style'=>'width:60%']) ?></td>
            <td> 新密码两次：<?= Html::activePasswordInput($model, 'business_business_password2', [ "maxlength" => 20, 'style'=>'width:60%']) ?></td>     
        </tr>-->
        
        <tr>
            <td>重置流程管理员授权业务密码：</td>
            <td> 原密码：<?= Html::activePasswordInput($model, 'flow_business_password', [ "maxlength" => 20, 'style'=>'width:60%']) ?></td>
            <td> 新密码一次：<?= Html::activePasswordInput($model, 'flow_business_password1', [ "maxlength" => 20, 'style'=>'width:60%']) ?></td>
            <td> 新密码两次：<?= Html::activePasswordInput($model, 'flow_business_password2', [ "maxlength" => 20, 'style'=>'width:60%']) ?></td> 
        </tr>
        </tbody>
    </table>

    <div class="buttons">
		 <a class="button blue-button" href="javascript:void(0)" save-data="<?= Url::to(['system/auth']) ?>">保存</a> 
        <input class="button blue-button" type="reset" value="取消"/>
    </div>

    <?php ActiveForm::end(); ?>
 
</div>
<?= $this->context->renderPartial('/jquery/js') ?>