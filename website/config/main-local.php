<?php

$config = [
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'aTGyzOL__jO3W4tS03lBwDrcUt3X4sxU',
        ],
    ],
];

if(!YII_ENV_TEST){
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug']['class'] = 'yii\debug\Module';
    $config['modules']['debug']['allowedIPs'] = [
        '127.0.0.1',
        '::1',
        '10.0.2.2'
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii']['class'] = 'yii\gii\Module';
    $config['modules']['gii']['allowedIPs'] = [
        '127.0.0.1',
        '::1',
        '10.0.2.2'
    ];
}

return $config;
