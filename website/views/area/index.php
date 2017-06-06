<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;


$this->title = '业务基础数据-地区列表';
?>


 <?= $this->context->renderPartial('/public/menu') ?>

<div class="main-container">
    <div class="filter">
        <form method="get">
            <input type="hidden" name="r" value="area/index" />
            <span>名称</span>
            <input class="form-text" type="text" placeholder="关键字..." name="keyword" value="<?= Yii::$app->request->get('keyword') ?>" />
            <span>状态</span>
            <?= Html::dropDownList('status', Yii::$app->request->get('status'), $model::getStatusSelectData(), ['prompt' => '请选择', 'class' => 'form-select']) ?>
            <?= Html::hiddenInput("parentId", $parentId)?>
            <input class="form-button" type="submit" value="搜索" />
        </form>
    </div>

    <?php $form = ActiveForm::begin(); ?>
    <table id="table-list" class="table-list">
        <caption>地区列表</caption>
        <tr>
            <th width="5%">序号</th>
            <th width="40%">地区名称</th>
            <th width="20%">父类名称</th>
            <th width="10%">状态</th>
            <th width="10%">排序</th>
            <th width="15%">操作</th>
        </tr>
        <?php if($listDatas){ foreach($listDatas as $key => $data){ ?>
        <?= $this->context->renderPartial('_list', compact(['data', 'key'])) ?>
        <?php } } else { ?>
        <tr><td colspan="6">暂无符合条件的地区记录</td></tr>
        <?php } ?>
    </table>

    <?= LinkPager::widget([
        'pagination' => $listPages,
    ]); ?>

    <?php ActiveForm::end(); ?>

    <div class="buttons">
        <a class="button blue-button" get-create-form="<?= Url::to(['area/form', 'parentId' => $parentId]) ?>" href="javascript:void(0)">新增</a>
        <div style="display:none"><input type="file" id="uploadExcel" name="excel" /></div>
    </div>
</div>


<?= $this->context->renderPartial('/jquery/js') ?>
<?= $this->context->renderPartial('/jquery/excel') ?>
