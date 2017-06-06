<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;
use libs\common\Flow;

$this->title = '业务基础数据-业务流程步骤';
?>
<?= $this->context->renderPartial('/public/menu') ?>
<div class="main-container">
    <div class="filter">
        <form method="get">
            <input type="hidden" name="r" value="flowconfigstep/index" />
            <span>流程名称</span>
            <input class="form-text verifySpecial" type="text" placeholder="关键字..." name="keyword" value="<?= Yii::$app->request->get('keyword') ?>" /> 
            <span>流程类型</span>
            <?= Html::dropDownList('type', Yii::$app->request->get('type'), Flow::getTypeSelectData(), ['prompt' => '请选择', 'class' => 'form-select']) ?>
            <input class="form-button" type="submit" value="搜索" />
        </form>
    </div>

    <?php $form = ActiveForm::begin(); ?>
    <table id="table-list" class="table-list">
        <caption>业务流程步骤列表</caption>
        <tr>
            <th width="3%">序号</th>
            <th width="10%">流程流程类标识类名称</th>
            <th width="5%">操作步骤</th>
            <th width="5%">审核步骤</th>
            <th width="5%">批准步骤</th>
            <th width="5%">执行步骤</th>
            <th width="8%">业务终止表单</th>
            <th width="10%">操作</th>
        </tr>
        <?php if($listDatas){ foreach($listDatas as $key => $data){ ?>
        <?= $this->context->renderPartial('_list', compact(['data', 'key'])) ?>
        <?php } } else { ?>
        <tr><td colspan="8">暂无符合条件的业务流程步骤记录</td></tr>
        <?php } ?>
    </table>

    <?= LinkPager::widget([
        'pagination' => $listPages,
    ]); ?>

    <?php ActiveForm::end(); ?>

    <div class="buttons">
        <a class="button blue-button" get-create-form="<?= Url::to(['flowconfigstep/form']) ?>" href="javascript:void(0)">新增</a>
    </div>
</div>


<?= $this->context->renderPartial('/jquery/js') ?>
<?= $this->context->renderPartial('/jquery/excel') ?>
