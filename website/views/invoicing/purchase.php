<?php
use common\models\Menu;

$this->title = '业务表单统计查询';
?>


 <?= $this->context->renderPartial('/public/menu') ?>

<div class="main-container">
    <div class="filter">
        <span>业务表单统计查询</span>
        <input class="form-text" type="text" placeholder="业务表单统计查询"/>
        <input class="form-button" type="button" value="添加物料表商品"/>
    </div>

    <table class="table-list">
        <caption>业务表单统计查询</caption>
        <tr>
            <th>序号</th>
            <th>物料名称</th>
            <th>物料 ID</th>
            <th>供应商</th>
            <th>类别</th>
        </tr>
        <tr>
            <td>1</td>
            <td>毛巾</td>
            <td>0035</td>
            <td>XX</td>
            <td>外销</td>
        </tr>
        <tr>
            <td>1</td>
            <td>毛巾</td>
            <td>0035</td>
            <td>XX</td>
            <td>外销</td>
        </tr>
        <tr>
            <td>1</td>
            <td>毛巾</td>
            <td>0035</td>
            <td>XX</td>
            <td>外销</td>
        </tr>
        <tr>
            <td>1</td>
            <td>毛巾</td>
            <td>0035</td>
            <td>XX</td>
            <td>外销</td>
        </tr>
    </table>

    <div class="buttons">
        <a class="button blue-button" href="">确定</a>

        <input class="button blue-button" type="button" value="返回"/>
    </div>
</div>
