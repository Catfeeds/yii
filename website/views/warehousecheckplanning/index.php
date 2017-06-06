<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;

use common\models\Supplier;
use common\models\ProductCategory;
use common\models\Admin;
use common\models\Warehouse;
use libs\common\Flow;
$this->title = '业务基础数据-仓库盘点计划列表';
//$department_id = Admin::checkSupperFlowAdmin() ? "" : Admin::getDepId();
$department_id = "";
?>
<?= $this->context->renderPartial('/public/menu') ?>

<div class="main-container">
    <div class="filter">
        <form method="get">
            <input type="hidden" name="r" value="warehousecheckplanning/index" />
            <span>计划名称
                <input class="form-text verifySpecial" type="text" placeholder="关键字..." name="keyword" value="<?= Yii::$app->request->get('keyword') ?>" /> 
            </span>
            <span>计划单号
                <input class="form-text verifySpecial" type="text" placeholder="关键字..." name="sn" value="<?= Yii::$app->request->get('sn') ?>" /> 
            </span>
            <span>盘点仓库
                <?= Html::dropDownList('warehouse_id', Yii::$app->request->get('warehouse_id'), Warehouse::getAllByStatus(Warehouse::STATUS_OK, "", $department_id), ['prompt' => '请选择','class' => 'form-select selDepartmentId']) ?>
            </span>
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
        <caption>总盘点计划列表</caption>
        <tr>
            <th width="3%">序号</th>
            <th width="10%">计划名称</th>
            <th width="10%">计划单号</th>
            <th width="5%">盘点仓库</th>
            <th width="5%">预计<br>盘点时间</th>
            <th width="10%">盘点<br>物料名称</th>
            <th width="5%">盘点<br>物料分类</th>
            <th width="10%">盘点<br>供应商</th>
            <th width="3%">是否<br>盘点资金</th>
            <th width="10%">状态</th>
            <th width="10%">下一步<br>操作</th>
            <th width="10%">下一步<br>操作人</th>
            <th width="10%">操作</th>
        </tr>
        <?php if($listDatas){ 
            foreach($listDatas as $key => $data){
                $nextStep = Flow::showNextStepByInfo(Flow::TYPE_CHECK_WAREHOUSE, $data);
                $allStep = Flow::showAllStep(Flow::TYPE_CHECK_WAREHOUSE, $data);
        ?>
            <tr>
                <td><?= isset($key) ? $key+1 : 0; ?></td>
                <td><?= $data->name ?></td>
                <td><?= $data->sn ?></td>
                <td><?= Warehouse::getNameById($data->warehouse_id) ?></td>
                <td><?= $data->check_time ?></td>
                <td><?= $data->product_name ? $data->product_name : "全部" ?></td>
                <td><?= $data->product_cate_id ? ProductCategory::getNameById($data->product_cate_id): "全部" ?></td>
                <td><?= $data->supplier_id ? Supplier::getNameById($data->supplier_id) : "全部" ?></td>
                <td><?= $data->is_check_amount ? "是" : "否" ?></td>
                <td><?= $data->status == Flow::STATUS_FINISH && !$data->is_proof ? "待盘点" : Flow::showStatusAll($data->status) ?></td>
                <td><?= $data->status == Flow::STATUS_FINISH && !$data->is_proof ? "盘点" : (isset($nextStep["nextStep"]) ? $nextStep["nextStep"] : "无") ?></td>
                <td><?= isset($nextStep["nextStepAdmin"]) ? $nextStep["nextStepAdmin"] : "无" ?></td>
                <td>
                    <a href="<?= Url::to(['warehousecheckplanning/info', 'id' => $data->id]) ?>">详情</a>
                    <?php foreach ($allStep as $stepVal) { ?>
                        <?php if($stepVal["state"]) { ?>
                            <a class="quick-form-button" href="<?= Url::to(['warehousecheckplanning/info',"id" => $data->id]) ?>" style="margin-left: 10px;"><?php echo $stepVal["stepName"];?></a>
                        <?php } else {  ?>
                            <span style="margin-left: 10px;"><?php echo $stepVal["stepName"];?></span>
                        <?php } ?>
                    <?php } ?>
                    <?php if($data->status == Flow::STATUS_FINISH && !$data->is_proof){ ?>
                           <a class="quick-form-button" href="<?= Url::to(['warehousecheckplanning/proof',"id" => $data->id]) ?>" style="margin-left: 10px;">盘点</a> 
                    <?php } else { ?>
                        <span style="margin-left: 10px;">盘点</span>
                    <?php } ?>
                </td>
            </tr>
        <?php } } else { ?>
            <tr><td colspan="13">暂无仓库盘点计划</td></tr>
        <?php } ?>
    </table>

    <?= LinkPager::widget([
        'pagination' => $listPages,
    ]); ?>

    <?php ActiveForm::end(); ?>
    <div class="buttons">
        <a class="button blue-button" href="<?= Url::to(['warehousecheckplanning/addorupdate']) ?>">新增</a>
        <a class="button blue-button" download-excel='subSearch'>导出</a>
    </div>
</div>


<?= $this->context->renderPartial('/jquery/js') ?>
<?= $this->context->renderPartial('/jquery/excel') ?>
<?= $this->context->renderPartial('/jquery/dateInput', compact("message")) ?>
