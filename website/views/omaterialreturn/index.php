<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;
use common\models\Warehouse;
use common\models\Supplier;
use libs\common\Flow;
use common\models\Admin;
//$departmentId = Admin::checkSupperFlowAdmin() ? 0 : Admin::getDepId();
$departmentId = 0;
$this->title = '业务操作-退货收款列表';
?>
<?= $this->context->renderPartial('/public/menu') ?>
<div class="main-container">
    <div class="filter">
        <form method="get">
            <input type="hidden" name="r" value="omaterialreturn/index" />
            <span>名称
                <input class="form-text verifySpecial" type="text" placeholder="关键字..." name="keyword" value="<?= Yii::$app->request->get('keyword') ?>" onkeyup="javascript:validateValue(this)"/>
            </span>
            <span>编号
                <input class="form-text verifySpecial" type="text" placeholder="关键字..." name="sn" value="<?= Yii::$app->request->get('sn') ?>" onkeyup="javascript:validateValue(this)"/>
            </span>
            <span>仓库
                <?= Html::dropDownList('warehouse_id', Yii::$app->request->get('warehouse_id'), Warehouse::getAllByStatus(Warehouse::STATUS_OK, "", $departmentId), ['prompt' => '请选择', 'class' => 'form-select']) ?>
            </span>
            <span>供应商
                <?= Html::dropDownList('supplier_id', Yii::$app->request->get('supplier_id'), Supplier::getSupplierSelectData(Supplier::STATUS_OK), ['prompt' => '请选择', 'class' => 'form-select']) ?>
            </span><br>
            <span>状态
                <?= Html::dropDownList('status', Yii::$app->request->get('status'), Flow::getStatusSelectData(), ['prompt' => '请选择', 'class' => 'form-select']) ?>
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
        <caption>退货收款</caption>
        <tr>
            <th width="3%">序号</th>
            <th width="10%">创建时间</th>
            <th width="10%">表单名称</th>
            <th width="5%">退货编号</th>
            <th width="8%">退货仓库</th>
            <th width="8%">供应商</th>
            <th width="5%">总价</th>
            <th width="8%">流程名称</th>
            <th width="5%">制表人</th>
            <th width="5%">进展状态</th>
            <th width="5%">下一步<br>操作</th>
            <th width="5%">下一步<br>操作人</th>
            <th width="10%">操作</th>
        </tr>
        <?php if($listDatas){ foreach($listDatas as $key => $data){ ?>
        <?= $this->context->renderPartial('_list', compact(['data', 'key'])) ?>
        <?php } } else { ?>
        <tr><td colspan="13">暂无符合条件的退货收款记录</td></tr>
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
