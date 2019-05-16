<?php
/**
 * User: Ron
 * Date: 2017/11/30 上午11:53
 * 队列相关的配置。目前使用的是RabbitMq。
 * 配置中字段说明：type:队列类型   exchange:默认接受消息的exchange   queue:默认的队列名称   routingKey:队列名称的routingKey
 */

return [
    //服务的配置
    'hostConfig' => [
        'host' => '1',//服务器地址
        'port' => '8082',//端口
        'user' => 'guest',//用户名
        'password' => 'guest',//密码
    ],

];