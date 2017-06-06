<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;
use common\models\Admin;
use common\models\Department;

$this->title = '业务基础数据-仓库分区';
?>


 <?= $this->context->renderPartial('/public/menu') ?>

<div class="main-container">
    <div class="filter">
        <form method="get">
            <input type="hidden" name="r" value="warehouse/index" />
             <?php //if(Admin::checkSupperFlowAdmin()){ ?>
            <span>所属部门
                <?= Html::dropDownList('department_id', Yii::$app->request->get('department_id'), Department::getSelectData(-1), ['class' => 'form-select selDepartmentId']) ?>
            </span>
            <?php //} ?>
            <span>仓库名</span>
            <input class="form-text verifySpecial" type="text" placeholder="关键字..." name="keyword" value="<?= Yii::$app->request->get('keyword') ?>" onkeyup="javascript:validateValue(this)"/>
            <span>状态</span>
            <?= Html::dropDownList('status', Yii::$app->request->get('status'), $model::getStatusSelectData(), ['prompt' => '请选择', 'class' => 'form-select']) ?>
            <input type="hidden" name="isDownload" value="0" id="isDownload" />
            <input class="form-button subSearch" type="submit" value="搜索" />
        </form>
    </div>

    <?php $form = ActiveForm::begin(); ?>
    <table id="table-list" class="table-list">
        <caption>仓库列表</caption>
        <tr>
            <th width="5%">序号</th>
            <th width="20%">仓库名</th>
            <th width="10%">性质类别</th>
            <th width="5%">分区编号</th><br>
            <th width="10%">所属地区</th>
            <th width="10%">是否销售</th>
            <th width="10%">所属部门</th>
            <th width="10%">状态</th>
            <th width="15%">操作</th>
        </tr>
        <?php if($listDatas){ foreach($listDatas as $key => $data){ ?>
        <?= $this->context->renderPartial('_list', compact(['data', 'key'])) ?>
        <?php } } else { ?>
        <tr><td colspan="9">暂无符合条件的仓库记录</td></tr>
        <?php } ?>
    </table>

    <?= LinkPager::widget([
        'pagination' => $listPages,
    ]); ?>

    <?php ActiveForm::end(); ?>

    <div class="buttons">
        <a class="button blue-button" get-create-form="<?= Url::to(['form']) ?>" href="javascript:void(0)">新增</a>
        <a class="button blue-button" href="<?= Url::to(['warehouse/downtemplate']) ?>" >下载导入模板</a>
        <a class="button blue-button" import-excel="<?= Url::to(['warehouse/import']) ?>"  href="javascript:void(0)">导入</a>
        <a class="button blue-button" download-excel='subSearch'>导出</a>
        <div style="display:none"><input type="file" id="uploadExcel" name="excel" /></div>
    </div>
</div>


<?= $this->context->renderPartial('/jquery/js') ?>
<?= $this->context->renderPartial('/jquery/excel') ?>