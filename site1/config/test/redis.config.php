<?php
/**
 * User: Ron
 * Date: 2017/09/18 下午1:36
 * redis连接配置
 */
return [
    //词条使用的redis
    'redisLanguage' => [
        'host' => '主机地址',
        'port' => '6380',
        'connTimeOut' => 5,
        'redisPassword' => ''//此密码目前没启用
    ],
    //站点使用的redis,集群redis
    'redisApplication' => [
        'host' => [
            //主机地址列表
        ],
        'connTimeOut' => 5,//链接redis超时时间
        'readTimeOut' => 5,//读取redis超时时间
        'redisPassword' => ''//redis密码
    ],
    'redisApplicationOne' => [
        'host' => '主机地址',
        'port' => 6381,
        'redisPassword' => ''//redis密码
    ]
];