<?php
/**
 * User: Ron
 * Date: 2017/09/19 上午11:05
 * 路由规则
 */
return [
    //首页路由
    '' => 'web/index/index',
    '/index' => 'web/index/index',

    //通用路由（此配置必须放在最后）。链接中不需要加 wap。默认参数已给默认值，可不传参数。
    //'<controller:[\w\\-\d]+>/<action:[\w\\-\d]+>/<paramOne:[\w\\-\d]+>/<paramTwo:[\w\\-\d]+>/<paramThree:[\w\\-\d]+>' => 'wap/<controller>/<action>',
    [
        'pattern' => '<controller:[\w\\-\d]+>/<action:[\w\\-\d]+>/<paramOne:[\w\\-\d]+>/<paramTwo:[\w\\-\d]+>/<paramThree:[\w\\-\d]+>/<paramFour:[\w\\-\d]+>',
        'route' => 'web/<controller>/<action>',
        'defaults' => ['paramOne' => '', 'paramTwo' => '', 'paramThree' => '', 'paramFour' => '']
    ],

];