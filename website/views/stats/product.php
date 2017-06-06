<?php

    use yii\helpers\Html;
    use yii\helpers\Url;
    use yii\widgets\ActiveForm;
    use yii\widgets\LinkPager;
    use common\models\Supplier;
    use common\models\Warehouse;
    use common\models\Product;
    use common\models\ProductCategory;
    use common\models\Department;
    use common\models\Admin;
    use common\models\WarehousePlanning;
    use common\models\WarehouseBuyingProduct;

$this->title = '查询统计-物料出入库统计';
?>
<?= $this->context->renderPartial('/public/menu') ?>
<div class="main-container">
    <div class="filter">
        <form method="get">
            <input type="hidden" name="r" value="stats/product" />
            <?php //if (Admin::checkSupperFlowAdmin()) { ?>
                <span>所属部门
                    <?= Html::dropDownList('department_id', Yii::$app->request->get('department_id'), Department::getSelectData(-1), ['class' => 'form-select selDepartmentId']) ?>
                </span>
            <?php //} ?>
            <?php // $department_id = !Admin::checkSupperFlowAdmin() ? Admin::getDepId() : (Yii::$app->request->get('department_id') ? Yii::$app->request->get('department_id') : "-1"); ?>
            <?php $department_id = (Yii::$app->request->get('department_id') ? Yii::$app->request->get('department_id') : "-1"); ?>
            <span>所属仓库
                <?= Html::dropDownList('warehouse_id', Yii::$app->request->get('warehouse_id'), Warehouse::getAllByStatus("", "", $department_id), ['prompt' => '请选择', 'class' => 'form-select selWarehouseId']) ?>
            </span>
            <span>物料ID
                <input class="form-text verifySpecial" type="text" placeholder="物料ID" name="product_id" value="<?= Yii::$app->request->get('product_id') ?>" onkeyup="javascript:validateValue(this)"/>
            </span>
            <span>物料名称
                <input class="form-text verifySpecial" type="text" placeholder="物料名称" name="product_name" value="<?= Yii::$app->request->get('product_name') ?>" onkeyup="javascript:validateValue(this)"/>
            </span><br>
            <span>出入库类型
                <?= Html::dropDownList('type', Yii::$app->request->get('type'), $model::getTypeSelectData(), ['prompt' => '请选择', 'class' => 'form-select']) ?>
            </span>
            <span>操作类型
                <?= Html::dropDownList('gateway_type', Yii::$app->request->get('gateway_type'), $model::getGatewayTypeSelectData(), ['prompt' => '请选择', 'class' => 'form-select']) ?>
            </span>
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
        <caption>物料出入库统计</caption>
        <tr>
            <th width="3%">序号</th>
            <th width="3%">物料ID</th>
            <th width="8%">物料名称</th>
            <th width="8%">供应商</th>
            <th width="3%">供应商<br>物料ID</th>
            <th width="3%">批次号</th>
            <th width="3%">规格</th>
            <th width="3%">单位</th>
            <th width="5%">部门名称</th>
            <th width="5%">仓库名称</th>
            <th width="3%">出入库<br>类型</th>
            <th width="4%">当时库存</th>
            <th width="4%">操作数量</th>
            <th width="4%">物品类型</th>
            <th width="3%">采购单价</th>
            <th width="3%">销售定价</th>
            <th width="5%">表单名</th>
            <th width="5%">表单号</th>
            <th width="5%">操作人</th>
            <th width="8%">创建时间</th>
        </tr>
        <?php if($listDatas){ 
            foreach ($listDatas as $key => $data) {
                if ($data->product_type == WarehousePlanning::TYPE_EXCEPTION) {
                    $product = WarehouseBuyingProduct::findOne($data->product_id);
                } else {
                    $product = Product::findOne($data->product_id);
                }
                $gatewayModel = $data->getModelByGatewayType();
                ?>
                <tr>
                    <td><?= $key + 1 ?></td>
                    <td><?= $data->product_type == WarehousePlanning::TYPE_EXCEPTION ? 0 : $data->product_id ?></td>
                    <td><?= $product->name ?></td>
                    <td><?= Supplier::getNameById($product->supplier_id) ?></td>
                    <td><?= $data->product_type == WarehousePlanning::TYPE_EXCEPTION ? 0 : $product->supplier_product_id ?></td>
                    <td><?= $data->batches ?></td>
                    <td><?= $product->spec ?></td>
                    <td><?= $product->unit ?></td>
                    <td><?= Warehouse::getDepartmentNameByWarehouseId($data->warehouse_id) ?></td>
                    <td><?= Warehouse::getNameById($data->warehouse_id) ?></td>
                    <td><?= $data->showType() ?></td>
                    <td><?= $data->stock ?></td>
                    <td><?= $data->num ?></td>
                    <td><?= ProductCategory::getNameById($data->product_type == WarehousePlanning::TYPE_EXCEPTION ? $product->material_type : $product->product_category_id) ?></td>
                    <td><?= $data->product_type == WarehousePlanning::TYPE_EXCEPTION ? $product->price : $product->purchase_price ?></td>
                    <td><?= $data->product_type == WarehousePlanning::TYPE_EXCEPTION ? $product->purchase_price : $product->sale_price ?></td>
                    <td><?= $data->showGatewayType() ?></td>
                    <td><?= $gatewayModel->sn ?></td>
                    <td><?= Admin::getNameById($data->gateway_type == $data::GATEWAY_TYPE_SALE ? $gatewayModel->create_admin_id : $gatewayModel->operation_admin_id) ?></td>
                    <td><?= date("Y-m-d", strtotime($gatewayModel->create_time)) . "<br>" . date("H:i:s", strtotime($gatewayModel->create_time)) ?></td>
                </tr>
        <?php } } else { ?>
        <tr><td colspan="20">暂无符合条件的物料出入库统计记录</td></tr>
        <?php } ?>
    </table>
    <?=
        LinkPager::widget([
                'pagination' => $listPages,
        ]);
    ?>
<?php ActiveForm::end(); ?>
    <div class="buttons">
        <a class="button blue-button" download-excel='subSearch'>导出</a>
    </div>
</div>
<?= $this->context->renderPartial('/jquery/js') ?>
<?= $this->context->renderPartial('/jquery/dateInput', compact("message")) ?>