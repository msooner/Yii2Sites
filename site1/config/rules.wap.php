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
    '/index.html' => 'wap/index/index',

    //登录、注册与第三方授权登录
    ['pattern' => 'oauth/callback/<paramOne:\w+>/<siteType:\d+>/<siteId:\d+>', 'route' => 'wap/oauth/callback', 'defaults' => ['siteType' => 11, 'siteId' => 0]],

    //栏目
    //'<catName:[\\-\\ \w]+>-c<catId:\d+>' => 'wap/category/cat',

    //商品详情页
    'p/<goodsName:[\\-\\ \w]+>-g<goodsId:\d+>.html' => 'wap/goods/goods-info',

    //通用路由（此配置必须放在最后）。链接中不需要加 wap。默认参数已给默认值，可不传参数。
    //'<controller:[\w\\-\d]+>/<action:[\w\\-\d]+>/<paramOne:[\w\\-\d]+>/<paramTwo:[\w\\-\d]+>/<paramThree:[\w\\-\d]+>' => 'wap/<controller>/<action>',
    [
        'pattern' => '<controller:[\w\\-\d]+>/<action:[\w\\-\d]+>/<paramOne:[\w\\-\d]+>/<paramTwo:[\w\\-\d]+>/<paramThree:[\w\\-\d]+>/<paramFour:[\w\\-\d]+>',
        'route' => 'wap/<controller>/<action>',
        'defaults' => ['paramOne' => '', 'paramTwo' => '', 'paramThree' => '', 'paramFour' => '']
    ],


];