<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;
use common\models\Warehouse;
use common\models\Department;
use common\models\Admin;
use common\models\Product;

$this->title = '销存管理-订单主页';
?>
<?= $this->context->renderPartial('/public/menu') ?>

<div class="main-container">
    <div class="filter">
        <form method="get">
            <input type="hidden" name="r" value="invoicing/salesorder" />
            <span>顾客公司
                <input class="form-text verifySpecial" type="text" placeholder="请输入公司名称" name="customer_company" value="<?= Yii::$app->request->get('customer_company') ?>" onkeyup="javascript:validateValue(this)"/>
            </span>
            <span>所属部门
                <?= Html::dropDownList('departmentId', Yii::$app->request->get('departmentId'), Department::getSelectData(-1), ['class' => 'form-select selDepartmentId']) ?>
            </span>
            <input class="form-button" type="submit" value="搜索" />
            <input class="form-button" type="submit" value="全部" />
        </form>
    </div>
    <?php $form = ActiveForm::begin(); ?>
    <table id="table-list" class="table-list">
        <caption><?= "选择";?>-销售订单管理</caption>
        <tr>
            <th width="3%">序号</th>
            <th width="15%">表单名</th>
            <th width="10%">表单号</th>
            <th width="8%">制表人</th>
            <th width="10%">制表日期</th>
            <th width="8%">部门</th>
            <th width="8%">执行人</th>
            <th width="10%">执行日期</th>
            <th width="8%">顾客公司</th>
            <th width="8%">当前状态</th>
            <th width="10%">操作</th>
        </tr>
          <?php if($listDatas){ foreach($listDatas as $key => $data){ ?>
        <?= $this->context->renderPartial('_list', compact(['data', 'key'])) ?>
        <?php } } else { ?>
        <tr><td colspan="16">暂无订单</td></tr>
        <?php } ?>
    <?= LinkPager::widget([
        'pagination' => $listPages,
    ]); ?>
    <?php ActiveForm::end(); ?>
        
    </table>

</div>
   <div class="buttons">
        <a class="button blue-button"  href="<?= Url::to(['order/add']) ?>">新建销售订单</a>
        </div>