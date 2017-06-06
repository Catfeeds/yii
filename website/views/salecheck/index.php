<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;
use common\models\Warehouse;
use libs\common\Flow;
use common\models\Admin;
use common\models\FlowConfig;
//$departmentId = Admin::checkSupperFlowAdmin() ? 0 : Admin::getDepId();
$departmentId = 0;
$this->title = '业务操作-销存盘点';
?>
<?= $this->context->renderPartial('/public/menu') ?>
<div class="main-container">
    <div class="filter">
        <form method="get">
            <input type="hidden" name="r" value="salecheck/index" />
            <span>销存名称
                <input class="form-text verifySpecial" type="text" placeholder="关键字..." name="keyword" value="<?= Yii::$app->request->get('keyword') ?>" />
            </span>
            <span>销存编号
                <input class="form-text verifySpecial" type="text" placeholder="关键字..." name="sn" value="<?= Yii::$app->request->get('sn') ?>" />
            </span>
            <span>销存仓库
                <?= Html::dropDownList('warehouse_id', Yii::$app->request->get('warehouse_id'), Warehouse::getAllByStatus(Warehouse::STATUS_OK, "", $departmentId), ['prompt' => '请选择', 'class' => 'form-select']) ?>
            </span>
            <span>状态
                <?= Html::dropDownList('status', Yii::$app->request->get('status'), Flow::getStatusSelectData(), ['prompt' => '请选择', 'class' => 'form-select']) ?>
            </span><br>
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
        <caption>仓库销存盘点列表</caption>
        <tr>
            <th width="3%">序号</th>
            <th width="10%">创建时间</th>
            <th width="13%">销存名称</th>
            <th width="7%">销存编号</th>
            <th width="8%">销存仓库</th>
            <th width="5%">预计<br>销存总价</th>
            <th width="5%">实际<br>销存总价</th>
            <th width="8%">流程名称</th>
            <th width="5%">制表人</th>
            <th width="5%">进展状态</th>
            <th width="5%">下一步<br>操作</th>
            <th width="5%">下一步<br>操作人</th>
            <th width="5%">操作</th>
        </tr>
        <?php if($listDatas){ foreach($listDatas as $key => $data){ ?>
        <?php $nextStep = Flow::showNextStepByInfo(Flow::TYPE_SALE_CHECK, $data);
            $allStep = Flow::showAllStep(Flow::TYPE_SALE_CHECK, $data);
        ?>
            <tr>
                <td><?= $key + 1; ?></td>
                <td><?= date("Y-m-d", strtotime($data->create_time)) . "<br>" . date("H:i:s", strtotime($data->create_time)) ?></td>
                <td><?= $data->name ?></td>
                <td><?= $data->sn ?></td>
                <td><?= Warehouse::getNameById($data->warehouse_id) ?></td>
                <td><?= number_format($data->total_amount, 2) ?></td>
                <td><?= number_format($data->sale_total_amount, 2) ?></td>
                <td><?= FlowConfig::getNameById($data->config_id) ?></td>
                <td><?= Admin::getNameById($data->create_admin_id) ?></td>
                <td><?= Flow::showStatusAll($data->status) ?></td>
                <td><?= isset($nextStep["nextStep"]) ? $nextStep["nextStep"] : "无" ?></td>
                <td><?= isset($nextStep["nextStepAdmin"]) ? $nextStep["nextStepAdmin"] : "无" ?></td>
                <td>
                    <a class="quick-form-button" href="<?= Url::to(['salecheck/info',"id" => $data->id]) ?>">详情</a>
                    <?php foreach ($allStep as $stepVal) { ?>
                        <?php if($stepVal["state"]) { ?>
                            <a class="quick-form-button" href="<?= Url::to(['salecheck/info',"id" => $data->id]) ?>" style="margin-left: 10px;"><?php echo $stepVal["stepName"];?></a>
                        <?php } else {  ?>
                            <span style="margin-left: 10px;"><?php echo $stepVal["stepName"];?></span>
                        <?php } ?>
                    <?php } ?>
                </td>
            </tr>
        <?php } } else { ?>
        <tr><td colspan="13">暂无符合条件的仓库销存盘点记录</td></tr>
        <?php } ?>
    </table>
    <?= LinkPager::widget([
        'pagination' => $listPages,
    ]); ?>
    <?php ActiveForm::end(); ?>
</div>
<?= $this->context->renderPartial('/jquery/js') ?>
<?= $this->context->renderPartial('/jquery/dateInput', compact("message")) ?>
