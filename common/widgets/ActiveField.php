<?php

namespace common\widgets;

use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;

class ActiveField extends \yii\widgets\ActiveField
{

    public function __construct($config = [])
    {
        parent::__construct($config);

        $this->registerClientScript();
    }

    public function mutliDropDownList($model)
    {
        $inputName = Html::getInputName($this->model, $this->attribute);
        $inputValue = Html::getAttributeValue($this->model, $this->attribute);

        $className = '\common\models\\' . ucfirst($model);

        $datas = $className::getSelectData(0);

        $this->parts['{input}'] = Html::dropDownList($inputName, $inputValue, $datas, [
            'class' => 'form-control',
            'parent-id' => 0,
            'ajax' => ucfirst($model),
            'ajax-url' => Url::to(['ajax/select']),
        ]);

        return $this;
    }

    protected function registerClientScript()
    {
        $view = \Yii::$app->getView();
        $js = <<<JS
$(document).on('change', 'select[ajax]', function(){
    var item = $(this);
    var parentId = item.val();
    
    if(parseInt(parentId) == parseInt(item.attr('parent-id'))){
        return;
    }
    
    var model = item.attr('ajax');
    var inputName = item.attr('name');
    var url  = item.attr('ajax-url');

    if(url){
        $.post(url, {model : model, parentId : parentId, inputName : inputName}, function(datas){
            item.nextAll('select').remove();
            if(datas.html){
                item.after(datas.html);
            }
        }, 'json');
    }
});
JS;
        $view->registerJs($js, View::POS_READY, 'custom_active_field');
    }
}