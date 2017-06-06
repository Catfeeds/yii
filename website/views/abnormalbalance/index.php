<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;
use common\models\Department;
use libs\common\Flow;
use common\models\Admin;
$this->title = '业务操作-业务收支流水日志';
?>
<?= $this->context->renderPartial('/public/menu') ?>
<div class="main-container">
    <div class="filter">
        <form method="get">
            <input type="hidden" name="r" value="abnormalbalance/index" />
            <?php //if(Admin::checkSupperFlowAdmin()){ ?>
                <span>涉及部门
                    <?= Html::dropDownList('department_id', Yii::$app->request->get('department_id'), Department::getAllDatas(), ['prompt' => '请选择', 'class' => 'form-select']) ?>
                </span>
            <?php //} ?>
            <span>日志名称
                <input class="form-text verifySpecial" type="text" placeholder="关键字..." name="keyword" value="<?= Yii::$app->request->get('keyword') ?>" />
            </span>
            <span>变动类型
                <?= Html::dropDownList('mod', Yii::$app->request->get('mod'), $model::getModSelectData(), ['prompt' => '请选择', 'class' => 'form-select']) ?>
            </span>
            <span>状态
                <?= Html::dropDownList('status', Yii::$app->request->get('status'), Flow::getStatusSelectData(), ['prompt' => '请选择', 'class' => 'form-select']) ?>
            </span><br>
            <span>开始时间
                <input class="form-text selDate" type="text"  name="beginDate" value="<?= Yii::$app->request->get('beginDate') ?>"  style="width: 100px;" readonly="readonly" i="1"/>
            </span>
            <span>结束时间
                <input class="form-text selDate" type="text"  name="endDate" value="<?= Yii::$app->request->get('endDate') ?>"  readonly="readonly" style="width: 100px;"  i="1"/>
            </span>
            <input class="form-button" type="submit" value="搜索" />
        </form>
    </div>
    <?php $form = ActiveForm::begin(); ?>
    <table id="table-list" class="table-list">
        <caption>业务收支流水日志列表</caption>
        <tr>
            <th width="3%">序号</th>
            <th width="8%">创建时间</th>
            <th width="13%">流水名称</th>
            <th width="8%">支出部门名称</th>
            <th width="8%">收入部门名称</th>
            <th width="5%">变动<br>类型</th>
            <th width="5%">变动金额</th>
            <th width="5%">流程名称</th>
            <th width="5%">制表人</th>
            <th width="5%">进展状态</th>
            <th width="5%">下一步<br>操作</th>
            <th width="5%">下一步<br>操作人</th>
            <th width="8%">操作</th>
        </tr>
        <?php if($listDatas){ foreach($listDatas as $key => $data){ ?>
        <?= $this->context->renderPartial('_list', compact(['data', 'key'])) ?>
        <?php } } else { ?>
        <tr><td colspan="13">暂无符合条件的业务收支流水日志记录</td></tr>
        <?php } ?>
    </table>
    <?= LinkPager::widget([
        'pagination' => $listPages,
    ]); ?>
    <div class="buttons">
        <a class="button blue-button" href="<?= Url::to(['abnormalbalance/addorupdate']) ?>">新增申请</a>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<?= $this->context->renderPartial('/jquery/js') ?>
<?= $this->context->renderPartial('/jquery/dateInput', compact("message")) ?>
