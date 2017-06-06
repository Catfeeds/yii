<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Url;
use yii\helpers\Html;
use common\models\Menu;
use common\models\Admin;
use app_web\assets\AppAsset;
use libs\Utils;
AppAsset::register($this);
?>
<?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
    </head>
    <body>
    <?php $this->beginBody() ?>

    <div class="wapper">
        <div class="header">
            <div class="pull-left">
                <a class="logo" href="<?= Yii::$app->getHomeUrl() ?>" style="background: url(<?= Utils::getImage(Yii::$app->cache->get("logo"))?>) no-repeat center;background-size: 180px 60px;"></a>
            </div>
            <div class="pull-left" style="padding:15px; font-size: 24px;">
                <?= Yii::$app->cache->get("company_".Admin::getDepId()) ?>
            </div>
            <div class="pull-right">
                欢迎您， <?php echo Yii::$app->user->identity->username;?>
                <a class="notice" href="<?= Url::to(['admin/editpwd']) ?>" style="background: none;">修改密码</a>
                <a class="notice" href="<?= Url::to(['businessremind/index']) ?>">业务提醒</a>
                <a class="logout" href="<?= Url::to(['site/logout']) ?>">安全退出</a>
            </div>
        </div>

        <div class="tbody">
            <div class="inner">
                <?= $content ?>
            </div>
        </div>

        <div class="sidebar">
            <?php $menus = Menu::getMenus(); ?>
            <?php foreach($menus as $menu){ ?>
            <a class="item <?= $menu->checkActive() ?>" href="<?= $menu->showUrl() ?>"><span class="<?= $menu->showIcon() ?>"></span><?= $menu->showName() ?></a>
            <?php } ?>
        </div>
    </div>
    <?php $this->endBody() ?>
    <script type="text/javascript">
        <?php $msg = Yii::$app->getSession()->getFlash("msg");?>
        <?php if($msg){echo "alert('{$msg}');" ; unset(Yii::$app->session['msg']); ;}?>

    </script>
    </body>
    </html>
<?php $this->endPage() ?>
