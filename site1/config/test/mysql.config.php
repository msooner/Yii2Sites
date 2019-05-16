<?php
/**
 * User: Ron
 * Date: 2017/09/15 下午3:34
 * mysql数据库相关的配置
 */
return [
    //业务库
    'mysqlBrand' => [
        'class' => 'yii\db\Connection',
        'tablePrefix' => '表前缀',

        // 主库的通用配置
        'masterConfig' => [
            'username' => '用户名',
            'password' => '密码',
            'tablePrefix' => '表前缀',
            'attributes' => [
                // 使用一个更小的连接超时。每个从库都共同地指定了 10 秒的连接超时时间，这意味着，如果一个从库在 10 秒内不能被连接上，它将被视为“挂掉的”。
                PDO::ATTR_TIMEOUT => 10,
            ],
        ],
        // 主库的配置列表
        'masters' => [
            ['dsn' => 'mysql:host=地址;port=3306;dbname=数据库', 'charset' => 'utf8'],//dsn for master server 1
        ],

        // 从库的通用配置
        'slaveConfig' => [
            'username' => '用户名',
            'password' => '密码',
            'tablePrefix' => '表前缀',
            'attributes' => [
                // 使用一个更小的连接超时。每个从库都共同地指定了 10 秒的连接超时时间，这意味着，如果一个从库在 10 秒内不能被连接上，它将被视为“挂掉的”。
                PDO::ATTR_TIMEOUT => 10,
            ],
        ],
        // 从库的配置列表
        'slaves' => [
            ['dsn' => 'mysql:host=地址;port=3306;dbname=数据库', 'charset' => 'utf8'],//dsn for slave server 1
            ['dsn' => 'mysql:host=地址;port=3306;dbname=数据库', 'charset' => 'utf8'],//dsn for slave server 2
            ['dsn' => 'mysql:host=地址;port=3306;dbname=数据库', 'charset' => 'utf8'],//dsn for slave server 3
        ],

    ],

    //业务库2
    //如果不是全局，则可以在局部使用以下配置创建数据库连接对象 $dbLog = Yii::createObject($mysqlConfig['mysqlLog']);
    'mysqlTwo' => [
        'class' => 'yii\db\Connection',
        'tablePrefix' => '表前缀',

        // 主库的通用配置
        'masterConfig' => [
            'username' => '用户名',
            'password' => '密码',
            'tablePrefix' => '表前缀',
            'attributes' => [
                // 使用一个更小的连接超时。每个从库都共同地指定了 10 秒的连接超时时间，这意味着，如果一个从库在 10 秒内不能被连接上，它将被视为“挂掉的”。
                PDO::ATTR_TIMEOUT => 10,
            ],
        ],
        // 主库的配置列表
        'masters' => [
            ['dsn' => 'mysql:host=地址;port=3306;dbname=数据库', 'charset' => 'utf8'],//dsn for master server 1
        ],

        // 从库的通用配置
        'slaveConfig' => [
            'username' => '用户名',
            'password' => '密码',
            'tablePrefix' => '表前缀',
            'attributes' => [
                // 使用一个更小的连接超时。每个从库都共同地指定了 10 秒的连接超时时间，这意味着，如果一个从库在 10 秒内不能被连接上，它将被视为“挂掉的”。
                PDO::ATTR_TIMEOUT => 10,
            ],
        ],
        // 从库的配置列表
        'slaves' => [
            ['dsn' => 'mysql:host=地址;port=3306;dbname=数据库', 'charset' => 'utf8'],//dsn for slave server 1
        ],
    ],

];
