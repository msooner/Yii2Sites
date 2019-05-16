<?php
/**
 * User: Ron
 * Date: 2017/09/19 上午11:05
 * 路由规则
 */
return [
    //首页路由
    '' => 'web/index/index',
    '/index' => 'web/index/index',//index.html

    [
        'pattern' => '<controller:[\w\\-\d]+>/<action:[\w\\-\d]+>/<paramOne:[\w\\-\d]+>/<paramTwo:[\w\\-\d]+>/<paramThree:[\w\\-\d]+>/<paramFour:[\w\\-\d]+>',
        'route' => 'web/<controller>/<action>',
        'defaults' => ['paramOne' => '', 'paramTwo' => '', 'paramThree' => '', 'paramFour' => '']
    ],

];