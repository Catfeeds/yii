<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;
use common\models\Supplier;
use libs\common\Flow;
use common\models\FlowConfig;
use common\models\Product;
use common\models\Admin;
use common\models\ProductCategory;
$this->title = '业务基础数据-供应商出品列表';
$supplierAll = Supplier::getSupplierSelectData();
?>
<?= $this->context->renderPartial('/public/menu') ?>
<div class="main-container">
    <div class="filter">
        <form method="get">
            <input type="hidden" name="r" value="productupdate/index" />
            <span>物料名称
                <input class="form-text verifySpecial" type="text" placeholder="关键字..." name="keyword" value="<?= Yii::$app->request->get('keyword') ?>" onkeyup="javascript:validateValue(this)"/>
            </span>
            <span>供应商
                <?= Html::dropDownList('supplier_id', Yii::$app->request->get('supplier_id'), $supplierAll, ['prompt' => '请选择', 'class' => 'form-select']) ?>
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
            <input class="form-button subSearch" type="submit" value="搜索" />
        </form>
    </div>

    <?php $form = ActiveForm::begin(); ?>
    <table id="table-list" class="table-list">
        <caption>物料出品修改申请列表</caption>
        <tr>
            <th width="5%">序号</th>
            <th width="10%">名称</th>
            <th width="8%">供应商</th>
            <th width="8%">条形码</th>
            <th width="5%">销售<br>价格</th>
            <th width="5%">物料<br>分类</th>
            <th width="8%">需要<br>批次号</th>
            <th width="8%">库存警告</th>
            <th width="5%">状态</th>
            <th width="8%">流程名称</th>
            <th width="8%">制表人</th>
            <th width="8%">制表时间</th>
            <th width="8%">下一步<br>操作</th>
            <th width="8%">下一步<br>操作人</th>
            <th width="10%">操作</th>
        </tr>
        <?php if($listDatas){ foreach($listDatas as $key => $data){  
                $nextStep = Flow::showNextStepByInfo(Flow::TYPE_PRODUCT_UPDATE, $data);
                $allStep = Flow::showAllStep(Flow::TYPE_PRODUCT_UPDATE, $data);?>
            <tr>
                <td><?= isset($key) ? $key+1 : 0; ?></td>
                <td><?= $data->name ?></td>
                <td><?= Supplier::getNameById($data->supplier_id); ?></td>
                <td><?= $data->barcode ?></td>
                <td><?= $data->sale_price ?></td>
                <td><?= ProductCategory::getNameById($data->product_category_id) ?></td>
                <td><?= $data->is_batches ? "是" : "否" ?></td>
                <td><?= $data->inventory_warning >0 ? $data->inventory_warning : "不需要" ?></td>
                <td><?= Flow::showStatusAll($data->status) ?></td>
                <td><?= FlowConfig::getNameById($data->config_id) ?></td>
                <td><?= Admin::getNameById($data->create_admin_id) ?></td>
                <td><?= date("Y-m-d", strtotime($data->create_time)) . "<br>" . date("H:i:s", strtotime($data->create_time)) ?></td>
                <td><?= isset($nextStep["nextStep"]) ? $nextStep["nextStep"] : "无" ?></td>
                <td><?= isset($nextStep["nextStepAdmin"]) ? $nextStep["nextStepAdmin"] : "无" ?></td>
                <td>
                    <a class="quick-form-button" href="<?= Url::to(['productupdate/info',"id" => $data->id]) ?>">详情</a>
                    <?php foreach ($allStep as $stepVal) { ?>
                        <?php if($stepVal["state"]) { ?>
                            <a class="quick-form-button" href="<?= Url::to(['productupdate/info',"id" => $data->id]) ?>" style="margin-left: 10px;"><?php echo $stepVal["stepName"];?></a>
                        <?php } else {  ?>
                            <span style="margin-left: 10px;"><?php echo $stepVal["stepName"];?></span>
                        <?php } ?>
                    <?php } ?>
                </td>
            </tr>
        <?php }}else { ?>
            <tr><td colspan="15">暂无符合条件的物流修改申请记录</td></tr>
        <?php } ?>
    </table>

    <?= LinkPager::widget([
        'pagination' => $listPages,
    ]); ?>

    <?php ActiveForm::end(); ?>
</div>
<?= $this->context->renderPartial('/jquery/js') ?>
<?= $this->context->renderPartial('/jquery/excel') ?>
<?= $this->context->renderPartial('/jquery/dateInput', compact("message")) ?>