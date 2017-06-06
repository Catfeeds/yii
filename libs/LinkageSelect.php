<?php
    namespace libs;
    use Yii;
    use yii\helpers\Html;
    use yii\base\InvalidConfigException;
    use yii\base\Widget;
    use yii\base\ErrorException;
/**
 * demo:	 联动下拉列表, 以下配置为树结构取最后一级 
	$this->widget('ext.webwidget.linkageselect.LinkageSelect', array(
		'model' => $model,
		'attribute' => 'pId',
		'data' => $types,
		'empty' => true,
		'ajaxUrl' => Url::to('area/ajaxGetList'),
		'emptyValue' => $emptyValue,
		'emptyText' => '请选择'
	))
	
	服务端返回 json demo: 
	{
	    "data": [
	        {
	            "key": -1,
	            "value": "请选择"
	        },
	        {
	            "key": "13",
	            "value": "测试子类"
	        }
	    ],
	    "htmlOptions": []
	}
		
	demo: 以下配置适用于地区联动配置.  多级联动并且每一级数据都需要获取
	$this->widget('ext.webwidget.linkageselect.LinkageSelect', array(
		'data' => array(
			array(
				'name' => Html::activeName($model, 'provinceId'),		
				'value' => $model->provinceId,
				'data' => Areas::getSelectByPid(0, Areas::DEEP_PROVINCE),
				'htmlOptions' => array('deep' => Areas::DEEP_PROVINCE, 'className' => 'BuyerForm')
			),
			array(
				'name' => Html::activeName($model, 'cityId'),
				'value' => $model->cityId,
				'validateValue' => true,
				'data' => Areas::getSelectByPid($model->provinceId, Areas::DEEP_CITY),
				'htmlOptions' => array('deep' => Areas::DEEP_CITY, 'className' => 'BuyerForm')
			),
			array(
				'name' => Html::activeName($model, 'areaId'),		
				'value' => $model->areaId,
				'validateValue' => true,
				'data' => Areas::getSelectByPid($model->cityId, Areas::DEEP_AREA),
				'htmlOptions' => array('deep' => Areas::DEEP_AREA)
			),
		), 
		'empty' => true,
		'ajaxUrl' => url('ajax/getCity'),
		'emptyValue' => '0',
		'emptyText' => '请选择',
		'ajaxParams' => array(
			'deep' => 'jqscript:$(this).attr("deep")',
			'className' => 'jqscript:$(this).attr("className")'
		)
	))
	
	服务端返回json demo:
	{
	    "data": [
	        {
	            "key": -1,
	            "value": "请选择"
	        },
	        {
	            "key": "1426",
	            "value": "东河区"
	        {
	            "key": "1434",
	            "value": "青山区"
	        }
	    ],
	    "htmlOptions": {
	        "name": "BuyerForm[areaId]",
	        "deep": 3,
	        "className": "BuyerForm"
	    }
	}
 */
class LinkageSelect  extends Widget {

	/**
	 * 对应model
	 */
	public $model = null;
	/**
	 * 对应attribute
	 */
	public $attribute = null;
	/**
	 * name属性, 如果model为null. 则使用name
	 */
	public $name = '';
	/**
	 * value值, 如果model为null, 则使用value
	 */
	public $value = '';
	/**
	 * 异步获取数据url
	 */
	public $ajaxUrl;
	/**
	 * 异步请求参数
	 * 如果异步参数中有带有script语法, 使用语法参考demo
	 * demo: 
	 * 	'ajaxParams' => array('opt1' => 'jqscript:$(this).attr("attr1")', 'opt2' => 'jqscript:$(this).attr("attr2")')
	 */
	public $ajaxParams = array();
	/**
	 * 异步请求script语法头标识
	 */
	public $ajaxScriptParamsPrex = 'jqscript:';
	/**
	 * 下拉列表初始值, 可以是一维也可以是二维数组.
	 *  如果是一维数组, 则对应生成一个select
	 *  如果是二维数组, 则对应生成多个select, 二维数组的name,value等属性在data里面设置. 多维数组data参数的数据格式为
	 *  data = array(
	 *  	array(
	 *  		'name' => '',
	 *  		'value' => '',
	 *  		'data' => array(),
	 *  		'validateValue' => false, 	//是否验证下拉列表值, 如果下拉列表值非法, 则不显示该下拉列表
	 * 		'htmlOptions' => array()
	 *  	)
	 *  )
	 */
	public $data = array();
	/**
	 * 是否添加空值
	 */
	public $empty = false;
	/**
	 * 默认空value, empty为true时有效
	 */
	public $emptyValue = '-1';
	/**
	 * 默认空text, empty为true时有效
	 */
	public $emptyText = '';
	/**
	 * 额外html属性
	 */
	public $htmlOptions = array();
	/**
	 * 默认样式名
	 */
	public $defaultClass = 'LinkageSelectClass';
	
	private $randClass;
	
	public function init() {
		$this->randClass = 'randClass'.rand(10,99).rand(10,99).rand(10,99);
		$this->htmlOptions['class'] = isset($this->htmlOptions['class']) ? $this->mergeClass($this->htmlOptions['class']) : $this->mergeClass('');
	}
	
	public function run() {
		//二维数组
		if (!(count ($this->data) == count ($this->data, 1))) {
			foreach ($this->data as $k => $data) {
				$data['name'] = isset($data['name']) ? $data['name'] : '';
				$data['value'] = isset($data['value']) ? $data['value'] : '';
				$data['data'] = isset($data['data']) ? $data['data'] : array();
				$data['htmlOptions'] = isset($data['htmlOptions']) ? $data['htmlOptions'] : $this->htmlOptions;
				$data['htmlOptions']['class'] = isset($data['htmlOptions']['class']) ? $this->mergeClass($data['htmlOptions']['class']) : $this->mergeClass('');
				
				$data['validateValue'] = isset($data['validateValue']) ? $data['validateValue'] : false;
				if ($data['validateValue']) {
					if (empty($data['data']) || !isset($data['data'][$data['value']])) {
						continue;
					}
				}
				if ($this->empty) {
					$data['data'] = [$this->emptyValue => $this->emptyText] + $data['data'];
				}
				echo Html::dropDownList($data['name'], $data['value'], $data['data'], $data['htmlOptions']);
			}
		} else {
			if ($this->empty) {
				$this->data = array($this->emptyValue => $this->emptyText) + $this->data;
			}
			if (is_null($this->model)) {
				echo Html::dropDownList($this->name, $this->select, $this->data, $this->htmlOptions);
			} else {
				echo Html::activeDropDownList($this->model, $this->attribute, $this->data, $this->htmlOptions);
			}
		}
		$params = array('"pid" : $(this).val()');
		foreach ($this->ajaxParams as $key => $value) {
			if (is_string($value) && !preg_match('/^'.$this->ajaxScriptParamsPrex.'/', $value)) {
				$params[] = '"'.$key.'" : "'.$value.'"'; 
			} else {
				$value = str_replace($this->ajaxScriptParamsPrex, '', $value);
				$params[] = '"'.$key.'" : '.$value;
			}
		}
		$params = implode(',', $params);
		
		/**
		 * callbackData 返回数据格式:
		 * 	array(
		 * 		'data' => array(
		 * 			array('key' => 'itemKey1', 'value' => 'itmeValue1'),
		 * 			array('key' => 'itemKey2', 'value' => 'itmeValue2'),
		 * 			array('key' => 'itemKey3', 'value' => 'itmeValue3'),
		 *		),
		 *		'htmlOptions' => array('class' => 'class1', 'id' => 'selectId')
		 * 	)
		 */
		if ($this->ajaxUrl) {
			$script = '$(document).on("change", ".'.$this->randClass.'", function(){
				var _this = $(this);
				$.get("'.$this->ajaxUrl.'", {'.$params.'}, function(callbackData){
					while($(".'.$this->randClass.'").length - 1 != parseInt($(".'.$this->randClass.'").index(_this))) {
						$(".'.$this->randClass.'").last().remove();
					}
					if (callbackData == undefined) {
						return false;
					}
					var attrs = "";
					if (callbackData.htmlOptions != undefined && callbackData.data.length != undefined && callbackData.data.length > 0) {
						if (! ("class" in callbackData.htmlOptions)) {
							attrs = "class = \"'.$this->defaultClass.' '.$this->randClass.'\" ";	
						}
						$.each(callbackData.htmlOptions, function(k,v){
							if (k == "class") {
								var defaultClass = randClassClass = "";
								if (v.indexOf("'.$this->defaultClass.'") == -1) {
									defaultClass = "'.$this->defaultClass.'";
								}
								if (v.indexOf("'.$this->randClass.'") == -1) {
									randClassClass = "'.$this->randClass.'";
								}
								attrs += k + " = \""+v+" "+defaultClass+" "+randClassClass+"\" ";
							} else {
								attrs += k + " = \""+v+"\" ";
							}
						})
						var html = "<select "+attrs+">";
						$.each(callbackData.data, function(key, val){
							html += "<option value=\'"+val.key+"\'>"+val.value+"</option>";
						});
						html += "<select>";
						$(_this).after(html);
					}
				}, "json");
			})';
                        Yii::$app->getView()->beginPage();
                        Yii::$app->getView()->beginBody();
                        Yii::$app->getView()->registerJs($script, \yii\web\View::POS_READY, $this->randClass.'Script');
                        Yii::$app->getView()->endBody();
                        Yii::$app->getView()->endPage();
		}
	}

	/**
	 * 合并样式
	 */
	private function mergeClass($class) {
		return $class.' '.$this->defaultClass.' '.$this->randClass;
	}
}