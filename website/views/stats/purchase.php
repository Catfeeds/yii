<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;
use common\models\Admin;
use common\models\Supplier;
use common\models\Warehouse;
use common\models\Product;
use common\models\ProductCategory;
use common\models\Department;
use common\models\WarehouseBuying;

$this->title = '查询统计-采购入库报表';
?>
<?= $this->context->renderPartial('/public/menu') ?>

<div class="main-container">
    <div class="filter">
        <form method="get">
            <input type="hidden" name="r" value="stats/purchase" />
           <?php //if(Admin::checkSupperFlowAdmin()){ ?>
            <span>所属部门
                <?= Html::dropDownList('department_id', Yii::$app->request->get('department_id'), Department::getSelectData(-1), ['class' => 'form-select selDepartmentId']) ?>
            </span>
            <?php //} ?>
            <?php // $department_id = !Admin::checkSupperFlowAdmin() ? Admin::getDepId() : (Yii::$app->request->get('department_id') ? Yii::$app->request->get('department_id') : "-1");?>
            <?php $department_id = (Yii::$app->request->get('department_id') ? Yii::$app->request->get('department_id') : "-1");?>
            <span>所属仓库
                <?= Html::dropDownList('warehouse_id', Yii::$app->request->get('warehouse_id'), Warehouse::getAllByStatus("", "",$department_id), ['prompt' => '请选择', 'class' => 'form-select selWarehouseId']) ?>
            </span>
            <span>物料分类
                <?= Html::dropDownList('material_type', Yii::$app->request->get('material_type'), ProductCategory::getCatrgorySelectData(), ['prompt' => '请选择', 'class' => 'form-select']) ?> 
            </span>
            <span>供应商
                <?= Html::dropDownList('supplier_id', Yii::$app->request->get('supplier_id'), Supplier::getSupplierSelectData(), ['prompt' => '请选择', 'class' => 'form-select']) ?> 
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

    <table id="table-list" class="table-list">
        <caption>采购入库报表</caption>
        <tr>
            <th width="2%">序号</th>
            <th width="8%">物料名</th>
            <th width="2%">物料ID</th>
            <th width="8%">供应商</th>
            <th width="3%">供应商<br>物料ID</th>
            <th width="3%">规格</th>
            <th width="3%">单位</th>
            <th width="3%">采购数量</th>
            <th width="3%">入库数量</th>
            <th width="4%">物料类型</th>
            <th width="3%">预定<br>采购单价</th>
            <th width="3%">实际<br>采购定价</th>
            <th width="5%">存放库区</th>
            <th width="5%">订单号</th>
            <th width="8%">创建时间</th>
        </tr>
        <?php if($listDatas){ foreach($listDatas as $key => $data){ ?>
        <?php $buyingItem = WarehouseBuying::findOne($data->buying_id);?>
            <tr>
                <td><?= $key+1; ?></td>
                <td><?= $data->name ?></td>
                <td><?= $data->product_id ?></td>
                <td><?= Supplier::getNameById($data->supplier_id) ?></td>
                <td><?= $data->supplier_product_id ?></td>
                <td><?= $data->spec ?></td>
                <td><?= $data->unit ?></td>
                <td><?= $data->product_number ?></td>
                <td><?= $data->buying_number ?></td>
                <td><?= ProductCategory::getNameById($data->material_type) ?></td>
                <td><?= number_format($data->price, 2) ?></td>
                <td><?= number_format($data->purchase_price, 2) ?></td>
                <td><?= Warehouse::getNameById($data->warehouse_id); ?></td>
                <td><?= $buyingItem->order_sn ?></td>
                <td><?= date("Y-m-d", strtotime($buyingItem->create_time))."<br>".date("H:i:s", strtotime($buyingItem->create_time)) ?></td>
            </tr>
        <?php } } else { ?>
        <tr><td colspan="16">暂无符合条件的采购入库报表记录</td></tr>
        <?php } ?>
    </table>

    <?= LinkPager::widget([
        'pagination' => $listPages,
    ]); ?>

    <div class="buttons">
        <a class="button blue-button" download-excel='subSearch'>导出</a>
    </div>
</div>
<?= $this->context->renderPartial('/jquery/js') ?>
<?= $this->context->renderPartial('/jquery/excel') ?>
<?= $this->context->renderPartial('/jquery/dateInput', compact("message")) ?>