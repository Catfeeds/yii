<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-web',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'app_web\controllers',
    'components' => [
        'user' => [
            'identityClass' => 'common\models\Admin',
            'enableAutoLogin' => true,
        ],
        'cache' => [  
            'class' => 'yii\caching\FileCache',  
        ],  
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
            'itemTable' => 'AuthItem',
            'itemChildTable' => 'AuthItemChild',
            'assignmentTable' => 'AuthAssignment',
            'ruleTable' => 'AuthRule',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        
        // 'urlManager' => [
        //     'enablePrettyUrl' => true,
        //     'showScriptName' => false,
        //     'rules' => [
        //        '/' => 'site/login',
        //     ],
  
        
    ],
    'params' => $params,
];
