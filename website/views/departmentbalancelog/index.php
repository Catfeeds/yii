<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;
use common\models\Department;
use libs\common\Flow;
use common\models\Admin;
$this->title = '业务操作-资金流水日志';
?>
<?= $this->context->renderPartial('/public/menu') ?>
<div class="main-container">
    <div class="filter">
        <form method="get">
            <input type="hidden" name="r" value="departmentbalancelog/index" />
            <?php //if(Admin::checkSupperFlowAdmin()){ ?>
                <span>部门
                    <?= Html::dropDownList('department_id', Yii::$app->request->get('department_id'), Department::getAllDatas(), ['prompt' => '请选择', 'class' => 'form-select']) ?>
                </span>
            <?php //} ?>
            <span>业务类型
                <?= Html::dropDownList('business_type', Yii::$app->request->get('business_type'), $model::getBusinessTypeSelectData(), ['prompt' => '请选择', 'class' => 'form-select']) ?>
            </span>
            <span>变动类型
                <?= Html::dropDownList('mod', Yii::$app->request->get('mod'), $model::getModSelectData(), ['prompt' => '请选择', 'class' => 'form-select']) ?>
            </span><br>
            <span>开始时间
                <input class="form-text selDate" type="text"  name="beginDate" value="<?= Yii::$app->request->get('beginDate') ?>"  style="width: 100px;" readonly="readonly" i="1"/>
            </span>
            <span>结束时间
                <input class="form-text selDate" type="text"  name="endDate" value="<?= Yii::$app->request->get('endDate') ?>"  readonly="readonly" style="width: 100px;" i="1"/>
            </span>
            <input type="hidden" name="isDownload" value="0" id="isDownload" />
            <input class="form-button subSearch" type="submit" value="搜索" />
        </form>
    </div>
    <?php $form = ActiveForm::begin(); ?>
    <table id="table-list" class="table-list">
        <caption>资金流水日志列表</caption>
        <tr>
            <th width="3%">序号</th>
            <th width="10%">时间</th>
            <th width="10%">表单</th>
            <th width="10%">部门</th>
            <th width="10%">收入金额</th>
            <th width="10%">支付金额</th>
            <th width="10%">收入支出<br>说明</th>
            <th width="10%">结存余额</th>
            <th width="10%">操作备注</th>
            <th width="10%">表单<br>执行人</th>
            <!--<th width="5%">操作</th>-->
        </tr>
        <?php if($listDatas){ foreach($listDatas as $key => $data){ ?>
        <?= $this->context->renderPartial('_list', compact(['data', 'key'])) ?>
        <?php } } else { ?>
        <tr><td colspan="11">暂无符合条件的资金流水日志记录</td></tr>
        <?php } ?>
    </table>
    <?= LinkPager::widget([
        'pagination' => $listPages,
    ]); ?>
    <?php ActiveForm::end(); ?>
    <div class="buttons">
        <a class="button blue-button" href="<?= Url::to(['departmentbalancelog/index']) ?>">刷新资金流水数据</a>
        <a class="button blue-button" download-excel='subSearch'>导出</a>
    </div>
</div>
<?= $this->context->renderPartial('/jquery/js') ?>
<?= $this->context->renderPartial('/jquery/dateInput', compact("message")) ?>
