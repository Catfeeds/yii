<?php
return [
    'adminEmail' => 'admin@example.com',
    'supportEmail' => 'support@example.com',
    'user.passwordResetTokenExpire' => 3600,
    'dbBack' => [
    //    'host' => '192.168.1.201',
        'host' => '127.0.0.1',
        'port' => '3306',
        'username' => 'root',
        'userPassword' => 'root',
        'charset' => 'utf8',
        'path' => '@db_back_path',
        'isCompress' => 1,
        'isDownload' => 0,
    ],
];
