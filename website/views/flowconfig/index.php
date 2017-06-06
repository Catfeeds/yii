<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;
use libs\common\Flow;

$this->title = '业务基础数据-业务流程';
?>
<?= $this->context->renderPartial('/public/menu') ?>

<div class="main-container">
    <div class="filter">
        <form method="get">
            <input type="hidden" name="r" value="flowconfig/index" />
            <span>流程名称
                <input class="form-text verifySpecial" type="text" placeholder="关键字..." name="keyword" value="<?= Yii::$app->request->get('keyword') ?>" onkeyup="javascript:validateValue(this)"/> 
            </span>
            <span>流程类型
                <?= Html::dropDownList('type', Yii::$app->request->get('type'), Flow::getTypeSelectData(), ['prompt' => '请选择', 'class' => 'form-select']) ?>
            </span>
            <span>状态
                <?= Html::dropDownList('status', Yii::$app->request->get('status'), array(0=>"无效", 1=> "有效"), ['prompt' => '请选择', 'class' => 'form-select']) ?>
            </span>
            <input type="hidden" name="isDownload" value="0" id="isDownload" />
            <input class="form-button subSearch" type="submit" value="搜索" />
        </form>
    </div>

    <?php $form = ActiveForm::begin(); ?>
    <table id="table-list" class="table-list">
        <caption>业务流程列表</caption>
        <tr>
            <th width="3%">序号</th>
            <th width="10%">流程名称</th>
            <th width="5%">流程类型</th>
            <th width="5%">创建角色</th>
            <th width="5%">创建名称</th>
            <th width="5%">创建部门</th>
            <th width="5%">审核角色</th>
            <th width="5%">审核名称</th>
            <th width="5%">审核部门</th>
            <th width="5%">批准角色</th>
            <th width="5%">批准名称</th>
            <th width="5%">批准部门</th>
            <th width="5%">执行角色</th>
            <th width="5%">执行名称</th>
            <th width="5%">执行部门</th>
            <th width="3%">状态</th>
            <th width="10%">操作</th>
        </tr>
        <?php if($listDatas){ foreach($listDatas as $key => $data){ ?>
        <?= $this->context->renderPartial('_list', compact(['data', 'key'])) ?>
        <?php } } else { ?>
        <tr><td colspan="17">暂无符合条件的业务流程记录</td></tr>
        <?php } ?>
    </table>

    <?= LinkPager::widget([
        'pagination' => $listPages,
    ]); ?>

    <?php ActiveForm::end(); ?>

    <div class="buttons" style="padding: 30px 0px">
        <a class="button blue-button" href="<?= Url::to(['flowconfig/addedit']) ?>" style="margin: 0 10px;">新增</a>
        <a class="button blue-button" href="<?= Url::to(['flowconfig/downflowtype']) ?>" style="margin: 0 10px;">下载流程类型</a>
        <a class="button blue-button" href="<?= Url::to(['flowconfig/downtemplate']) ?>" style="margin: 0 10px;">下载导入模板</a>
        <a class="button blue-button" import-excel="<?= Url::to(['flowconfig/import']) ?>"  href="javascript:void(0)" style="margin: 0 10px;">导入</a>
        <a class="button blue-button" download-excel='subSearch' style="margin: 0 10px;">导出</a>
        <div style="display:none"><input type="file" id="uploadExcel" name="excel" /></div>
    </div>
</div>


<?= $this->context->renderPartial('/jquery/js') ?>
<?= $this->context->renderPartial('/jquery/excel') ?>
