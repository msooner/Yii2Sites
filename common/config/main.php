<?php
/**
 * 公共配置规则以及注意事项
 *
 * 配置加载规则（分应用、分环境）：
 * 1、与应用无关，且与环境无关的配置项，写到 common\config\main.php 或 common\config\params.php 中去。
 * 2、与应用有关，且与环境无关的配置项，写到 应用目录\config\main.php 或 应用目录\config\params.php 中去。
 * 3、与应用无关，且与环境有关的配置项，写到 common\config\YII_ENV\*.php 或 common\config\YII_ENV\*.php 中去。
 * 4、与应用有关，且与环境有关的配置项，写到 应用目录\config\YII_ENV\*.php 或 应用目录\config\YII_ENV\*.php 中去。
 *
 * 注意：
 * 1、优先使用应用配置，如果应用中没有配置，会使用common中的配置。应用中与common中有相同的配置，common的公共配置将被应用的配置覆盖。
 * 2、局部使用的配置，独立配置，不需要在入口加载。
 *    比如在控制器中 $cartConfig = require(__DIR__ . '/../../common/config/' . YII_ENV . '/cart.config.php');
 */

//加载 与应用无关，且与环境有关的配置项
$params = array_merge(
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/' . YII_ENV . '/params.php'),
    require(__DIR__ . '/language.config.php')
);

//加载 与应用有关，且与环境有关的配置项
//$dbMysqlBrand = require(__DIR__ . '/' . YII_ENV . '/mysql.config.php');

//加载 与应用无关，且与环境无关的配置项
return [
    'timeZone' => 'Asia/Chongqing',//默认时区
    'charset' => 'UTF-8',//配置网站字符编码
    //'language' => 'en',//全局默认语言配置 'zh-CN'
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
    ],
    'params' => $params,
];

