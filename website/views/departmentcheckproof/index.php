<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;

use common\models\Admin;
use common\models\FlowConfig;
use common\models\Department;
use libs\common\Flow;
$this->title = '业务基础数据-部门盘点计划校队列表';
?>
<?= $this->context->renderPartial('/public/menu') ?>

<div class="main-container">
    <div class="filter">
        <form method="get">
            <input type="hidden" name="r" value="departmentcheckproof/index" />
            <span>校队名称
                <input class="form-text verifySpecial" type="text" placeholder="关键字..." name="keyword" value="<?= Yii::$app->request->get('keyword') ?>" onkeyup="javascript:validateValue(this)"/> 
            </span>
            <span>校队单号
                <input class="form-text verifySpecial" type="text" placeholder="关键字..." name="sn" value="<?= Yii::$app->request->get('sn') ?>" onkeyup="javascript:validateValue(this)"/> 
            </span>
            <?php //if (Admin::checkSupperFlowAdmin()) { ?>
                <span>盘点部门
                    <?= Html::dropDownList('department_id', Yii::$app->request->get('department_id'), Department::getSelectData(-1), ['class' => 'form-select selDepartmentId']) ?>
                </span>
            <?php //} ?>
            <span>状态
                <?= Html::dropDownList('status', Yii::$app->request->get('status'), Flow::getStatusSelectData(), ['prompt' => '请选择', 'class' => 'form-select']) ?>
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
        <caption>部门盘点计划校队列表</caption>
        <tr>
            <th width="3%">序号</th>
            <th width="10%">校队名称</th>
            <th width="10%">校队单号</th>
            <th width="5%">盘点时间</th>
            <th width="5%">盘点人</th>
            <th width="8%">盘点仓库</th>
            <th width="10%">盘点前<br>商品金额</th>
            <th width="10%">盘点后<br>商品金额</th>
            <th width="10%">状态</th>
            <th width="10%">流程名</th>
            <th width="10%">下一步<br>操作</th>
            <th width="10%">下一步<br>操作人</th>
            <th width="10%">操作</th>
        </tr>
        <?php if($listDatas){ 
            foreach($listDatas as $key => $data){
                $nextStep = Flow::showNextStepByInfo(Flow::TYPE_CHECK_DEPARTMENT_PROOF, $data);
                $allStep = Flow::showAllStep(Flow::TYPE_CHECK_DEPARTMENT_PROOF, $data);
        ?>
            <tr>
                <td><?= isset($key) ? $key+1 : 0; ?></td>
                <td><?= $data->name ?></td>
                <td><?= $data->sn ?></td>
                <td><?= date("Y-m-d", strtotime($data->create_time)) . "<br>" . date("H:i:s", strtotime($data->create_time)) ?></td>
                <td><?= Admin::getNameById($data->create_admin_id) ?></td>
                <td><?= Department::getNameById($data->department_id) ?></td>
                <td><?= $data->total_buying_amount ?></td>
                <td><?= $data->check_buying_amount ?></td>
                <td><?= Flow::showStatusAll($data->status) ?></td>
                <td><?= FlowConfig::getNameById($data->config_id) ?></td>
                <td><?= (isset($nextStep["nextStep"]) ? $nextStep["nextStep"] : "无") ?></td>
                <td><?= isset($nextStep["nextStepAdmin"]) ? $nextStep["nextStepAdmin"] : "无" ?></td>
                <td>
                    <a href="<?= Url::to(['departmentcheckproof/info', 'id' => $data->id]) ?>">详情</a>
                    <?php foreach ($allStep as $stepVal) { ?>
                        <?php if($stepVal["state"]) { ?>
                            <a class="quick-form-button" href="<?= Url::to(['departmentcheckproof/info',"id" => $data->id]) ?>" style="margin-left: 10px;"><?php echo $stepVal["stepName"];?></a>
                        <?php } else {  ?>
                            <span style="margin-left: 10px;"><?php echo $stepVal["stepName"];?></span>
                        <?php } ?>
                    <?php } ?>
                </td>
            </tr>
        <?php } } else { ?>
            <tr><td colspan="13">暂无仓库盘点计划校队数据</td></tr>
        <?php } ?>
    </table>

    <?= LinkPager::widget([
        'pagination' => $listPages,
    ]); ?>

    <?php ActiveForm::end(); ?>
    <div class="buttons">
        <a class="button blue-button" download-excel='subSearch'>导出</a>
    </div>
</div>


<?= $this->context->renderPartial('/jquery/js') ?>
<?= $this->context->renderPartial('/jquery/excel') ?>
<?= $this->context->renderPartial('/jquery/dateInput', compact("message")) ?>
