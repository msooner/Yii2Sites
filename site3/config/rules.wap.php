<?php
/**
 * User: Ron
 * Date: 2017/09/19 上午11:05
 * 路由规则
 */
return [
    //首页
    '' => 'wap/index/index',
    '/index' => 'wap/index/index',

    [
        'pattern' => '<controller:[\w\\-\d]+>/<action:[\w\\-\d]+>/<paramOne:[\w\\-\d]+>/<paramTwo:[\w\\-\d]+>/<paramThree:[\w\\-\d]+>/<paramFour:[\w\\-\d]+>',
        'route' => 'wap/<controller>/<action>',
        'defaults' => ['paramOne' => '', 'paramTwo' => '', 'paramThree' => '', 'paramFour' => '']
    ],

];