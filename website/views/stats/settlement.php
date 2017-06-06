<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;
use common\models\Admin;
use libs\common\Flow;
use common\models\Department;
use common\models\Supplier;
use common\models\BusinessAll;
use common\models\Warehouse;
$this->title = '供应商结算列表';
?>
 <?= $this->context->renderPartial('/public/menu') ?>
<div class="main-container">
    <div class="filter">
        <form method="get">
            <input type="hidden" name="r" value="stats/settlement" />
            <?php //if(Admin::checkSupperFlowAdmin()){ ?>
                <span>所属部门
                    <?= Html::dropDownList('department_id', Yii::$app->request->get('department_id'), Department::getSelectData(-1), ['class' => 'form-select selDepartmentId']) ?>
                </span>
            <?php //} ?>
            <?php // $department_id = !Admin::checkSupperFlowAdmin() ? Admin::getDepId() : (Yii::$app->request->get('department_id') ? Yii::$app->request->get('department_id') : "-1");?>
            <?php $department_id = (Yii::$app->request->get('department_id') ? Yii::$app->request->get('department_id') : "-1");?>
            <span>供应商
                <?= Html::dropDownList('supplier_id', Yii::$app->request->get('supplier_id'), Supplier::getSupplierSelectData(), ['prompt' => '请选择', 'class' => 'form-select']) ?>
            </span>
            <span>开始时间
                <input class="form-text selDate" type="text"  name="beginDate" value="<?= $beginDate ?>"  style="width: 100px;" readonly="readonly" i="1"/>
            </span>
            <span>结束时间
                <input class="form-text selDate" type="text"  name="endDate" value="<?= $endDate ?>"  readonly="readonly" style="width: 100px;" i="1"/>
            </span>
            <?= Html::hiddenInput("isDownload", 0, ["id" => "isDownload"]); ?>
            <input class="form-button subSearch" type="submit" value="搜索" />
        </form>
    </div>
    <?php $form = ActiveForm::begin(); ?>
    <table id="table-list" class="table-list">
        <caption><?= "全部";?>-供应商结算列表</caption>
        <tr>
            <th width="3%">序号</th>
            <th width="8%">部门</th>
            <th width="8%">仓库</th>
            <th width="8%">供应商</th>
            <th width="5%">供应商ID</th>
            <th width="8%">已付</th>
            <th width="8%">已收</th>
            <th width="8%">待付</th>
            <th width="8%">待收</th>
        </tr>
        <?php $key = $payTotal = $putTotal = $noPutTotal = $noPayTotal = 0;
            if($listDatas) {
            foreach($listDatas as $listVal){ 
                foreach ($listVal as $data) { ?>
            <tr>
                <td><?= $key+1; ?></td>
                <td><?= Department::getNameById($data["department_id"]) ?></td>
                <td><?= Warehouse::getNameById($data["warehouse_id"]) ?></td>
                <td><?= Supplier::getNameById($data["supplier_id"]) ?></td>
                <td><?= $data["supplier_id"] ?></td>
                <td><?php $payTotal += $data["paySum"] ;echo number_format($data["paySum"], 2);?></td>
                <td><?php $putTotal += $data["receipt"];echo number_format($data["receipt"], 2);?></td>
                <td><?php $noPayTotal += $data["noPaySum"]; echo number_format($data["noPaySum"], 2);?></td>
                <td><?php $noPutTotal += $data["noReceipt"];echo number_format($data["noReceipt"], 2);?></td>
            </tr>
            <?php $key++;} } } else { ?>
            <tr><td colspan="9">暂无符合条件的供应商结算列表记录</td></tr>
            <?php } ?>
            <tr>
                <td colspan="2">已付总计：<?php echo $payTotal;?></td>
                <td colspan="2">已收总计：<?php echo $putTotal;?></td>
                <td colspan="2">待付总计：<?php echo $noPayTotal;?></td>
                <td colspan="3">待收总计：<?php echo $noPutTotal;?></td>
            </tr>
    </table>
    <?php ActiveForm::end(); ?>
    <div class="buttons">
        <a class="button blue-button"  download-excel='subSearch'>导出</a>
        <div style="display:none"><input type="file" id="uploadExcel" name="excel" /></div>
    </div>
</div>
<?= $this->context->renderPartial('/jquery/js') ?>
<?= $this->context->renderPartial('/jquery/excel') ?>
<?= $this->context->renderPartial('/jquery/dateInput', compact("message")) ?>
