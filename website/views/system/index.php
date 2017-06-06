<?php
use common\models\Menu;

$this->title = '系统基础数据-logo';
?>

<div class="tabs">
    <?php $menus = Menu::getMenusByDepth(1); ?>
    <?php foreach($menus as $menu){ ?>
        <a class="<?= $menu->checkActive() ?>" href="<?= $menu->showUrl() ?>"><?= $menu->showName() ?></a>
    <?php } ?>
</div>

<div class="sub-tabs">
    <?php $menus = Menu::getMenusByDepth(2); ?>
    <?php foreach($menus as $menu){ ?>
        <a class="<?= $menu->checkActive() ?>" href="<?= $menu->showUrl() ?>"><?= $menu->showName() ?></a>
    <?php } ?>
</div>

<div class="main-container">
    <div class="filter">
        <span>系统基础数据-供logo设置商</span>
        <input class="form-text" type="text" placeholder="计划下单最晚时间"/>
        <input class="form-button" type="button" value="添加物料表商品"/>
    </div>

    <table class="table-list">
        <caption>供应商</caption>
        <thead>
        <tr>
            <th>序号</th>
            <th>名称</th>
            <th>物料 ID</th>
            <th>供应商</th>
            <th>类别</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>1</td>
            <td>上海市难道</td>
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
        </tbody>
    </table>

    <div class="buttons">
        <a class="button blue-button" href="">确定</a>

        <input class="button blue-button" type="button" value="返回"/>
    </div>
</div>
