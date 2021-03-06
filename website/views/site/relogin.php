<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app_web\assets\LoginAsset;

LoginAsset::register($this);
?>

<?php $this->beginPage() ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?>进销存管理系统</title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<h1>进销存管理系统<sup>2016</sup></h1>

<div class="login" style="margin-top:50px;">

    <div class="header">
        <div class="switch" id="switch">
            <a class="switch_btn_focus" id="switch_qlogin" href="javascript:void(0);" tabindex="7">强制登录</a>
            <div class="switch_bottom" id="switch_bottom" style="position: absolute; width: 64px; left: 0px;"></div>
        </div>
    </div>


    <div class="web_qr_login" id="web_qr_login" style="display: block; height: 235px;">

        <!--登录-->
        <div class="web_login" id="web_login">


            <div class="login-box">


                <div class="login_form">
                    <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>
                        <input type="hidden" name="did" value="0"/>
                        <input type="hidden" name="to" value="log"/>
                        <div class="uinArea" id="uinArea">
                            <label class="input-tips" for="u">帐号：</label>
                            <div class="inputOuter" id="uArea">
                                <?= Html::activeTextInput($model, 'username', ['class' => 'inputstyle', 'disabled' => true]) ?>
                            </div>
                        </div>
                        <div class="pwdArea" id="pwdArea">
                            <label class="input-tips" for="p">密码：</label>
                            <div class="inputOuter" id="pArea">
                                <?= Html::activePasswordInput($model, 'password', ['class' => 'inputstyle']) ?>
                            </div>
                        </div>
                        <?= Html::activeHiddenInput($model, 'isSafety');?>
                        <?php if($model->getFirstErrors()){?>
                           <div class="loginFooter">
                                        <?php echo current($model->getFirstErrors()); ?>
                                </div>
                        <?php }?>
                        <div style="padding-left:50px;margin-top:20px;">
                            <input type="submit" value="登 录" style="width:150px;" class="button_blue"/>
                        </div>
                    <?php ActiveForm::end(); ?>
                </div>

            </div>

        </div>
        <!--登录end-->
    </div>
</div>
<div class="jianyi">*推荐使用ie8或以上版本ie浏览器或Chrome内核浏览器访问本站</div>

<?php $this->endBody() ?>

<script type="text/javascript">
    <?php $msg = Yii::$app->getSession()->getFlash("msg");?>
    <?php if($msg){echo "alert('{$msg}');";}?>
</script>
</body>
</html>
<?php $this->endPage() ?>