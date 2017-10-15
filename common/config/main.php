<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        // 数据库配置
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=longlong666666',
            'username' => 'longlong666666',
            'password' => '4kb4h2pc',
            'charset' => 'utf8',
        ],
    ],
    'language' => 'zh-CN',
];
