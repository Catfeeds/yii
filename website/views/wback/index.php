<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;
use common\models\Admin;
use common\models\Warehouse;
use libs\common\Flow;
$this->title = '业务操作-物料退仓列表';
?>
<?= $this->context->renderPartial('/public/menu') ?>
<div class="main-container">
    <div class="filter">
        <form method="get">
            <input type="hidden" name="r" value="wback/index" />
            <span>退仓名称
            <input class="form-text verifySpecial" type="text" placeholder="关键字..." name="keyword" value="<?= Yii::$app->request->get('keyword') ?>" onkeyup="javascript:validateValue(this)"/>
            </span>
            <span>退仓编号
            <input class="form-text verifySpecial" type="text" placeholder="关键字..." name="sn" value="<?= Yii::$app->request->get('sn') ?>" onkeyup="javascript:validateValue(this)"/>
            </span>
            <?php // $department_id = !Admin::checkSupperFlowAdmin() ? Admin::getDepId() : (Yii::$app->request->get('department_id') ? Yii::$app->request->get('department_id') : "-1");?>
            <?php $department_id = (Yii::$app->request->get('department_id') ? Yii::$app->request->get('department_id') : "-1");?>
            <span>调出仓库
            <?= Html::dropDownList('warehouse_id', Yii::$app->request->get('warehouse_id'), Warehouse::getAllByStatus(Warehouse::STATUS_OK, "", $department_id), ['prompt' => '请选择', 'class' => 'form-select']) ?>
            </span>
            <span>状态
            <?= Html::dropDownList('status', Yii::$app->request->get('status'), Flow::getStatusSelectData(), ['prompt' => '请选择', 'class' => 'form-select']) ?>
            </span><br>
            <span>是否扣仓
                <?= Html::dropDownList('is_buckle', Yii::$app->request->get('is_buckle'), Flow::getBuckleSelectData(), ['prompt' => '请选择', 'class' => 'form-select']) ?>
            </span>
            <span>开始时间
            <input class="form-text selDate" type="text"  name="beginDate" value="<?= Yii::$app->request->get('beginDate') ?>"  style="width: 100px;" readonly="readonly"  i="1"/>
            </span>
            <span>结束时间
            <input class="form-text selDate" type="text"  name="endDate" value="<?= Yii::$app->request->get('endDate') ?>"  readonly="readonly" style="width: 100px;"  i="1"/>
            </span>
            <input class="form-button" type="submit" value="搜索" />
        </form>
    </div>
    <?php $form = ActiveForm::begin(); ?>
    <table id="table-list" class="table-list">
        <caption>物料退仓列表</caption>
        <tr>
            <th width="3%">序号</th>
            <th width="5%">制表时间</th>
            <th width="10%">退仓名称</th>
            <th width="5%">退仓编号</th>
            <th width="8%">退出仓库</th>
            <th width="5%">退仓总价</th>
            <th width="8%">流程名称</th>
            <th width="5%">制表人</th>
            <th width="5%">是否扣仓</th>
            <th width="5%">进展状态</th>
            <th width="5%">下一步<br>操作</th>
            <th width="5%">下一步<br>操作人</th>
            <th width="10%">操作</th>
        </tr>
        <?php if($listDatas){ foreach($listDatas as $key => $data){ ?>
        <?= $this->context->renderPartial('_list', compact(['data', 'key'])) ?>
        <?php } } else { ?>
        <tr><td colspan="13">暂无退仓记录</td></tr>
        <?php } ?>
    </table>
    <?= LinkPager::widget([
        'pagination' => $listPages,
    ]); ?>
    <?php ActiveForm::end(); ?>
    <div class="buttons">
        <a class="button blue-button" href="<?= Url::to(['pstock/back']) ?>">退仓申请</a>
    </div>
</div>
<?= $this->context->renderPartial('/jquery/js') ?>
<?= $this->context->renderPartial('/jquery/excel') ?>
<?= $this->context->renderPartial('/jquery/dateInput', compact("message")) ?>
