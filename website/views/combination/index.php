<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;
use common\models\Admin;
use common\models\Warehouse;

$this->title = '业务设置-组合物料模版管理';
//$departmentId = Admin::checkSupperFlowAdmin() ? "" : Admin::getDepId();
$departmentId = "";
?>
<?= $this->context->renderPartial('/public/menu') ?>
<div class="main-container">
    <div class="filter">
        <form method="get">
            <input type="hidden" name="r" value="combination/index" />
            <span>模版名称
                <input class="form-text verifySpecial" type="text" placeholder="关键字..." name="keyword" value="<?= Yii::$app->request->get('keyword') ?>" />
            </span>
            <span>仓库
                <?= Html::dropDownList('warehouse_id', Yii::$app->request->get('warehouse_id'), Warehouse::getAllByStatus(Warehouse::STATUS_OK, "", $departmentId), ['prompt' => '请选择', 'class' => 'form-select']) ?>
            </span>
            <span>开始时间
                <input class="form-text selDate" type="text"  name="beginDate" value="<?= Yii::$app->request->get('beginDate') ?>"  style="width: 100px;" readonly="readonly"  i="1"/>
            </span>
            <span>结束时间
                <input class="form-text selDate" type="text"  name="endDate" value="<?= Yii::$app->request->get('endDate') ?>"  readonly="readonly" style="width: 100px;"  i="1"/>
            </span>
            <input type="hidden" name="isDownload" value="0" id="isDownload" />
            <input class="form-button subSearch" type="submit" value="搜索" />
        </form>
    </div>

    <?php $form = ActiveForm::begin(); ?>
    <table id="table-list" class="table-list">
        <caption>组合物料模版列表</caption>
        <tr>
            <th width="3%">序号</th>
            <th width="20%">模版名称</th>
            <th width="8%">仓库</th>
            <th width="10%">模版<br>制定时间</th>
            <th width="5%">订单<br>付款方式</th>
            <th width="5%">定金</th>
            <th width="10%">用途说明</th>
            <th width="5%">制定人</th>
            <th width="8%">操作</th>
        </tr>
        <?php if($listDatas){ foreach($listDatas as $key => $data){ ?>
        <?= $this->context->renderPartial('_list', compact(['data', 'key'])) ?>
        <?php } } else { ?>
        <tr><td colspan="9">暂无符合条件的组合物料模版记录</td></tr>
        <?php } ?>
    </table>

    <?= LinkPager::widget([
        'pagination' => $listPages,
    ]); ?>

    <?php ActiveForm::end(); ?>

    <div class="buttons">
        <a class="button blue-button" href="<?= Url::to(['combination/add']) ?>">新增</a>
        <a class="button blue-button" download-excel='subSearch'>导出</a>
        <div style="display:none"><input type="file" id="uploadExcel" name="excel" /></div>
    </div>
</div>
<?= $this->context->renderPartial('/site/popping') ?>
<?= $this->context->renderPartial('/jquery/js') ?>
<?= $this->context->renderPartial('/jquery/excel') ?>
<?= $this->context->renderPartial('/jquery/dateInput', compact("message")) ?>
<?= $this->context->renderPartial('/jquery/setCombinationNum') ?>

