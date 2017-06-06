<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;
use common\models\Admin;

$this->title = '业务基础数据-业务计算机设置';
?>
<?= $this->context->renderPartial('/public/menu') ?>
<div class="main-container">
    <div class="filter">
        <form method="get">
            <input type="hidden" name="r" value="computer/index" />
            <span>名称：
            <input class="form-text verifySpecial" type="text" placeholder="关键字..." name="keyword" value="<?= Yii::$app->request->get('keyword') ?>" onkeyup="javascript:validateValue(this)"/>
            </span>
            <span>类型：
            <?= Html::dropDownList('type', Yii::$app->request->get('type'), $model::getTypeSelectData(), ['prompt' => '请选择', 'class' => 'form-select']) ?>
            </span>
            <span>位置：
            <?= Html::dropDownList('position', Yii::$app->request->get('position'), $model::getPositionSelectData(), ['prompt' => '请选择', 'class' => 'form-select']) ?>
            </span>
            <span>状态：
            <?= Html::dropDownList('status', Yii::$app->request->get('status'), $model::getStatusSelectData(), ['prompt' => '请选择', 'class' => 'form-select']) ?>
            </span>
            <input type="hidden" name="isDownload" value="0" id="isDownload" />
            <input class="form-button subSearch" type="submit" value="搜索" />
        </form>
    </div>

    <?php
        $form = ActiveForm::begin();
    ?>
    <table id="table-list" class="table-list">
        <caption>业务计算机列表</caption>
        <tr>
            <td colspan="8" style="text-align: left;">
                &nbsp;&nbsp;<span style="color: red">授权台数：<?php echo $computerNum ? $computerNum->set_value : 20;?>&nbsp;&nbsp;台</span>
                <?php //if(Admin::checkSupperFlowAdmin()){ ?>
                    &nbsp;&nbsp;&nbsp;&nbsp;<a href="<?php echo Url::to(["computer/setnum"]);?>">设置</a>
                <?php //} ?>
            </td>
        </tr>
        <tr>
            <th>序号</th>
            <th>名称</th>
            <th>mac地址</th>
            <th>类别</th>
            <th>所属角色</th>
            <th>位置</th>
            <th>状态</th>
            <th>操作</th>
        </tr>
        <?php if($listDatas){ foreach($listDatas as $key => $data){ ?>
        <?= $this->context->renderPartial('_list', compact(['data', 'key'])) ?>
        <?php } } else { ?>
        <tr><td colspan="8">暂无符合条件的业务计算机记录</td></tr>
        <?php } ?>
    </table>

    <?= LinkPager::widget([
        'pagination' => $listPages,
    ]); ?>

    <?php ActiveForm::end(); ?>

    <div class="buttons">
        <a class="button blue-button" get-create-form="<?= Url::to(['form']) ?>" href="javascript:void(0)">新增</a>
        <a class="button blue-button" href="<?= Url::to(['computer/downtemplate']) ?>" >下载导入模板</a>
        <a class="button blue-button" import-excel="<?= Url::to(['computer/import']) ?>"  href="javascript:void(0)">导入</a>
        <a class="button blue-button" download-excel='subSearch'>导出</a>
        <div style="display:none"><input type="file" id="uploadExcel" name="excel" /></div>
            <?= $this->context->renderPartial('/jquery/excel') ?>
    </div>
</div>
<?= $this->context->renderPartial('/jquery/js') ?>
