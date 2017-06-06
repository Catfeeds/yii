<?php

use yii\helpers\Html;
use common\models\Menu;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Menu */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="menu-form">

    <?php $form = ActiveForm::begin([
        'fieldClass' => \common\widgets\ActiveField::className(),
    ]); ?>

    <?= $form->field($model, 'parent_id')->dropDownList(Menu::getAllSelectData()) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'url')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'icon')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'auth')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'is_open')->radioList(Menu::getIsOpenSelect()) ?>

    <?= $form->field($model, 'is_show')->radioList(Menu::getIsShowSelect()) ?>

    <?= $form->field($model, 'sort')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
