<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;
use common\models\Warehouse;
use common\models\Admin;
//$departmentId = Admin::checkSupperFlowAdmin() ? 0 : Admin::getDepId();
$departmentId = 0;
$this->title = '业务操作-物料库存出入库日志列表';
?>
<?= $this->context->renderPartial('/public/menu') ?>
<div class="main-container">
    <div class="filter">
        <form method="get">
            <input type="hidden" name="r" value="wgateway/index" />
            <span>物料ID
                <input class="form-text verifySpecial" type="text" placeholder="物流ID" name="product_id" value="<?= Yii::$app->request->get('product_id') ?>" onkeyup="javascript:validateValue(this)"/>
            </span>
            <span>物料名称
                <input class="form-text verifySpecial" type="text" placeholder="物料名称" name="product_name" value="<?= Yii::$app->request->get('product_name') ?>" onkeyup="javascript:validateValue(this)"/>
            </span>
            <span>仓库
                <?= Html::dropDownList('warehouse_id', Yii::$app->request->get('warehouse_id'), Warehouse::getAllByStatus("","",$departmentId), ['prompt' => '请选择', 'class' => 'form-select']) ?>
            </span>
            <span>出入库类型
                <?= Html::dropDownList('type', Yii::$app->request->get('type'), $model::getTypeSelectData(), ['prompt' => '请选择', 'class' => 'form-select']) ?>
            </span><br>
            <span>操作类型
                <?= Html::dropDownList('gateway_type', Yii::$app->request->get('gateway_type'), $model::getGatewayTypeSelectData(), ['prompt' => '请选择', 'class' => 'form-select']) ?>
            </span>
            <span>开始时间
                <input class="form-text selDate" type="text"  name="beginDate" value="<?= Yii::$app->request->get('beginDate') ?>"  style="width: 100px;" readonly="readonly"  i="1"/>
            </span>
            <span>结束时间
                <input class="form-text selDate" type="text"  name="endDate" value="<?= Yii::$app->request->get('endDate') ?>"  readonly="readonly" style="width: 100px;"  i="1"/>
            </span>
            <input type="hidden" name="isDownload" value="0" id="isDownload" />
            <input class="form-button subSearch" type="submit" value="搜索" />
        </form>
    </div>
    <?php $form = ActiveForm::begin(); ?>
    <table id="table-list" class="table-list">
        <caption>物料库存出入库日志</caption>
        <tr>
            <th width="3%">序号</th>
            <th width="3%">物料ID</th>
            <th width="12%">批次号</th>
            <th width="8%">物料名称</th>
            <th width="8%">仓库名称</th>
            <th width="3%">出入库<br>类型</th>
            <th width="5%">当时<br>库存</th>
            <th width="5%">操作<br>数量</th>
            <th width="5%">操作类型</th>
            <th width="5%">操作<br>时间</th>
            <th width="10%">备注</th>
        </tr>
        <?php if($listDatas){ foreach($listDatas as $key => $data){ ?>
        <?= $this->context->renderPartial('_list', compact(['data', 'key'])) ?>
        <?php } } else { ?>
        <tr><td colspan="11">暂无符合条件的物料库存出入库日志记录</td></tr>
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
<?= $this->context->renderPartial('/jquery/dateInput', compact("message")) ?>
<?= $this->context->renderPartial('/jquery/excel') ?>
