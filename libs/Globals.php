<?php
namespace libs;

function app()
{
	return Yii::app();
}

function cs()
{
	return Yii::app()->getClientScript();
}

function user()
{
	return Yii::app()->user;
}

function cache()
{
	return Yii::app()->cache;
}

function ctl()
{
	return Yii::app()->controller;
}

function act()
{
	return Yii::app()->controller->action;
}

function url($route, $params = array(), $ampersand = '&')
{
	return Yii::app()->createUrl($route, $params, $ampersand);
}

function encode($text)
{
	return htmlspecialchars($text, ENT_QUOTES, Yii::app()->charset);
}

function l($text, $url = '#', $htmlOptions = array())
{
	return CHtml::link($text, $url, $htmlOptions);
}

function baseUrl($url = null)
{
	static $baseUrl;
	if($baseUrl === null)
		$baseUrl = Yii::app()->getRequest()->getBaseUrl();
	return $url === null ? $baseUrl : $baseUrl . '/' . ltrim($url, '/');
}

function param($name, $value = null)
{
	if($value !== null){
		Yii::app()->params[$name] = $value;
	}
	return Yii::app()->params[$name];
}

function debug($data, $end = true)
{
	if(isset($_GET['debug']) && $_GET['debug']){
		return dump($data, $end);
	}
}

function dump($data, $end = true)
{
	echo '<pre>';
	print_r($data);
	echo '</pre>';
	if($end){
		die();
	}
}

function dumps($var, $end = true) {
	CVarDumper::dump($var, 10, true);
	if ( $end ) {
		app()->end();
	}
}

function getDump($var, $depth = 10, $highlight = false) {
	return CVarDumper::dumpAsString($var, $depth, $highlight);
}

