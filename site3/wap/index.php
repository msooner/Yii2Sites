<?php
defined('YII_DEBUG') or define('YII_DEBUG', true);//生产环境必须设置为false
defined('YII_ENV') or define('YII_ENV', 'dev');//环境切换  dev:开发环境  prod:生产环境  test:测试环境
defined('YII_SITE_TYPE') or define('YII_SITE_TYPE', 'wap');//定义是web，还是wap
defined('YII_APP_NAME') or define('YII_APP_NAME', 'site3');//定义应用的全局名称常量
defined('YII_TERMINAL_TYPE') or define('YII_TERMINAL_TYPE', 11);//终端类型(1: android手机，2:iphone手机，3:iPad，10:PC站，11:M站)

require(__DIR__ . '/../../vendor/autoload.php');
require(__DIR__ . '/../../vendor/yiisoft/yii2/Yii.php');

//主要用于执行一些Yii应用引导的代码，比如定义一系列的路径别名。
require(__DIR__ . '/../../common/config/bootstrap.php');
require(__DIR__ . '/../config/bootstrap.php');

//合并main配置项
$config = yii\helpers\ArrayHelper::merge(
    require(__DIR__ . '/../../common/config/main.php'),
    require(__DIR__ . '/../config/main.php')
);

(new yii\web\Application($config))->run();
