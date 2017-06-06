<?php

use common\models\Menu;

?>
<div class="tabs" style="white-space:nowrap;min-width: 850px;">
 <?php $menus = Menu::getMenusByDepth(1);  // echo'<pre>';  print_r($menus);exit;?>
    <?php foreach($menus as $menu){ ?>
        <a class="<?= $menu->checkActive() ?>" href="<?= $menu->showUrl() ?>"><?= $menu->showName() ?></a>
    <?php } ?>
</div>

<div class="sub-tabs" style="white-space:nowrap;min-width: 850px;">
    <?php $menus = Menu::getMenusByDepth(2); //echo'<pre>'; print_r($menus);exit;?>
    <?php foreach($menus as $menu){ ?>
        <a class="<?= $menu->checkActive() ?>" href="<?= $menu->showUrl() ?>"><?= $menu->showName() ?></a>
    <?php } ?>
</div>