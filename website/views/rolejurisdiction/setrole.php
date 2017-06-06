<?php
    use yii\widgets\ActiveForm;
    use yii\widgets\LinkPager;
    use common\models\Admin;
    use common\models\Department;
    use yii\helpers\Url;
    use yii\helpers\Html;
    use yii\helpers\ArrayHelper;

    $this->title = '系统基础数据-设置访问权限';
//    $departmentAll = Admin::checkSupperFlowAdmin() ? Department::getSelectData(-1) : ['' => '请选择', Admin::getDepId() => Department::getNameById(Admin::getDepId())];
    $departmentAll =  Department::getSelectData(-1);
?>
<?= $this->context->renderPartial('/public/menu') ?>
<style>
    .table-list td input[type='checkbox']{margin-top:8px;width: 20px;height: 20px;}
</style>
<div class="main-container">
    <div class="filter">
        <form method="get">
            <input type="hidden" name="r" value="rolejurisdiction/setrole" />
            <span>角色名称
                <input class="form-text verifySpecial" type="text" placeholder="关键字..." name="keyword" value="<?= Yii::$app->request->get('keyword') ?>" onkeyup="javascript:validateValue(this)"/>
            </span>
            <span>部门
                <?= Html::dropDownList('department_id', Yii::$app->request->get('department_id'), $departmentAll, ['class' => 'form-select']) ?>
            </span>
            <input class="form-button subSearch" type="submit" value="搜索" />
        </form>
    </div>
    <?php $form = ActiveForm::begin(); ?>
    <?php if ($listDatas) { $roleAll = ArrayHelper::map($listDatas, "id", "name");?>
        <table class="table-list">
            <tbody>
                <tr>
                    <td style="width: 8%;">&nbsp;</td>
                        <?php foreach ($roleAll as $roleId => $roleName) { ?>
                            <td style="text-align: center;">
                                <?php echo Html::checkbox("setAll", false, ["class" => "setAll", "value" => $roleId]); ?><br>
                                <?php echo $roleName; ?>
                                <?php echo Html::hiddenInput("roleList[]", $roleId);?>
                            </td>
                        <?php } ?>
                </tr>
                <?php foreach ($twoMenuAll as $menuId => $menuName) { ?>
                    <tr>
                        <td><?php echo $menuName; ?></td>
                            <?php foreach ($roleAll as $roleId => $roleName) { ?>
                                <td style="text-align: center;">
                                    <?php $isSel = isset($roleJur[$roleId]) && isset($roleJur[$roleId][$menuId]) ? true : false; ?>
                                    <?php echo Html::checkbox("selRoleJur[{$roleId}][{$menuId}][]", $isSel, ["class" => "selRoleJur_{$roleId}"]); ?>
                                </td>
                            <?php } ?>
                    </tr>
                <?php } ?>
                <?php foreach ($menuAll as $menuId => $menuName) { ?>
                    <tr>
                        <td><?php echo $menuName; ?></td>
                        <?php foreach ($roleAll as $roleId => $roleName) { ?>
                            <td style="text-align: center;">
                                <?php $isSel = isset($roleJur[$roleId]) && isset($roleJur[$roleId][$menuId]) ? true : false; ?>
                                <?php echo Html::checkbox("selRoleJur[{$roleId}][{$menuId}][]", $isSel, ["class" => "selRoleJur_{$roleId}"]); ?>
                            </td>
                        <?php } ?>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } else {
        echo "暂无符合条件的角色";
    } ?>
    <div class="buttons">
        <a class="button blue-button" href="javascript:void(0)" save-data="<?= Url::to(['rolejurisdiction/saverole']) ?>">保存</a> 
    </div>
<?php ActiveForm::end(); ?>
    <?= LinkPager::widget([
        'pagination' => $listPages,
    ]); ?>
</div>
<?= $this->context->renderPartial('/jquery/js') ?>
<?php
    $js = <<<JS
    $(".setAll").click(function(){
        var roleId = $(this).val();
        if($(this).attr("checked")) {
            $(".selRoleJur_"+roleId).attr("checked", "checked");
        } else {
            $(".selRoleJur_"+roleId).removeAttr("checked");
        }
    });
JS;
Yii::$app->getView()->registerJs($js, \yii\web\View::POS_READY, 'setRole');
?>