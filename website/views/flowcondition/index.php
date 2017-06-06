<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;
use common\models\FlowConfig;
$this->title = '业务基础数据-业务流程';
?>
<?= $this->context->renderPartial('/public/menu') ?>

<div class="main-container">
    <div class="filter">
        <form method="get">
            <input type="hidden" name="r" value="flowcondition/index" />
            <span>流程名称</span>
            <?= Html::dropDownList('config_id', Yii::$app->request->get('config_id'), FlowConfig::getAllSelectData(FlowConfig::STATUS_YES), ['prompt' => '请选择', 'class' => 'form-select']) ?>
            <span>范围类型</span>
            <?= Html::dropDownList('type', Yii::$app->request->get('type'), $model::getTypeSelectData(), ['prompt' => '请选择', 'class' => 'form-select']) ?>
            <span>状态</span>
            <?= Html::dropDownList('status', Yii::$app->request->get('status'), $model::getStatusSelectData(), ['prompt' => '请选择', 'class' => 'form-select']) ?>
            <input class="form-button" type="submit" value="搜索" />
        </form>
    </div>

    <?php $form = ActiveForm::begin(); ?>
    <table id="table-list" class="table-list">
        <caption>业务流程条件列表</caption>
        <tr>
            <th width="3%">序号</th>
            <th width="10%">流程名称</th>
            <th width="5%">条件名称</th>
            <th width="5%">范围类型</th>
            <th width="5%">下限</th>
            <th width="5%">上限</th>
            <th width="3%">状态</th>
            <!--<th width="10%">操作</th>-->
        </tr>
        <?php if($listDatas){ foreach($listDatas as $key => $data){ ?>
        <?= $this->context->renderPartial('_list', compact(['data', 'key'])) ?>
        <?php } } else { ?>
        <tr><td colspan="7">暂无符合条件的业务流程条件记录</td></tr>
        <?php } ?>
    </table>

    <?= LinkPager::widget([
        'pagination' => $listPages,
    ]); ?>

    <?php ActiveForm::end(); ?>

    <div class="buttons">
        <!--<a class="button blue-button" get-create-form="<?= Url::to(['flowcondition/form']) ?>" href="javascript:void(0)">新增</a>-->
        <div style="display:none"><input type="file" id="uploadExcel" name="excel" /></div>
    </div>
</div>


<?= $this->context->renderPartial('/jquery/js') ?>
<?= $this->context->renderPartial('/jquery/excel') ?>
