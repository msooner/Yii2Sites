<?php
/**
 * User: Ron
 * Date: 2017/09/18 下午1:36
 * mongodb连接的配置
 */
return [
    //mongodb相关连接配置
    'mongodb' => [
        'class' => '\yii\mongodb\Connection',
        'dsn' => 'mongodb://用户名:密码@地址/库名',
        'newDsn' => 'mongodb://用户名:密码@地址',
        'db' => '库名',
    ],
    //日志mongodb库连接配置
    'mongodbLog' => [
        'class' => '\yii\mongodb\Connection',
        'dsn' => 'mongodb://用户名:密码@地址/库名',
        'db' => '库名',
    ],
];