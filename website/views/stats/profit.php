<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;
use common\models\Admin;
use common\models\Department;
$this->title = '查询统计-毛利报表';
?>
<?= $this->context->renderPartial('/public/menu') ?>

<div class="main-container">
    <div class="filter">
        <form method="get">
            <input type="hidden" name="r" value="stats/profit" />
            <?php //if(Admin::checkSupperFlowAdmin()){ ?>
                <span>所属部门
                    <?= Html::dropDownList('department_id', Yii::$app->request->get('department_id'), Department::getSelectData(-1), ['class' => 'form-select selDepartmentId']) ?>
                </span>
            <?php //} ?>
            <span>时间类型
                <?= Html::dropDownList('timeType', $timeType, ["1" => "按天","2" => "按月", "3" => "按季度"], ['class' => 'form-select selDepartmentId']) ?>
            </span>
            <span>开始时间
                <input class="form-text selDate" type="text"  name="beginDate" value="<?= Yii::$app->request->get('beginDate') ?>"  style="width: 100px;" readonly="readonly" i="1"/>
            </span>
            <span>结束时间
                <input class="form-text selDate" type="text"  name="endDate" value="<?= Yii::$app->request->get('endDate') ?>"  readonly="readonly" style="width: 100px;" i="1"/>
            </span>
            <?= Html::hiddenInput("isDownload", 0, ["id" => "isDownload"]); ?>
            <input class="form-button subSearch" type="submit" value="搜索" />
        </form>
    </div>

    <?php $form = ActiveForm::begin(); ?>
    <table id="table-list" class="table-list">
        <caption><?= "全部";?> - 利润报表</caption>
        <tr>
            <th width="10%">序号</th>
            <th width="10%">时间</th>
            <th width="20%">部门</th>
            <th width="10%">总收入</th>
            <th width="10%">总支出</th>
            <th width="10%">利润</th>
        </tr>
        <?php if($listDatas){ foreach($listDatas as $key => $data){ ?>
            <tr>
                <td><?= $key+1 ?></td>
                <td><?= $data["time"] ?></td>
                <td><?= $data["departmentName"] ?></td>
                <td><?= $data["income"] ?></td>
                <td><?= $data["expen"] ?></td>
                <td><?= $data["profit"] ?></td>
            </tr>
        <?php } } else { ?>
        <tr><td colspan="6">暂无符合条件的利润报表记录</td></tr>
        <?php } ?>
    </table>
    <?php ActiveForm::end(); ?>
    <div class="buttons">
        <a class="button blue-button"  download-excel='subSearch'>导出</a>
    </div>
</div>
<?= $this->context->renderPartial('/jquery/js') ?>
<?= $this->context->renderPartial('/jquery/excel') ?>
<?= $this->context->renderPartial('/jquery/dateInput', compact("message")) ?>
