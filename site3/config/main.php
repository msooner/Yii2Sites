<?php
//子项目ID
$theAppId = YII_APP_NAME . '-' . YII_SITE_TYPE;

//合并params配置项
$params = array_merge(
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/' . YII_ENV . '/params.php')
);

//加载 与应用有关，且与环境有关的配置项
//$dbMysqlBrand = require(__DIR__ . '/' . YII_ENV . '/mysql.config.php');
$rulesSite = require(__DIR__ . '/rules.' . YII_SITE_TYPE . '.php');

//默认路由配置
$defaultRoute = 'site/index';
if (YII_SITE_TYPE == 'web') {
    $defaultRoute = 'web/index/index';
} elseif (YII_SITE_TYPE == 'wap') {
    $defaultRoute = 'wap/index/index';
}

//加载 与应用有关，且与环境无关的配置项
$config = [
    'defaultRoute' => $defaultRoute,
    'id' => $theAppId,
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => YII_APP_NAME . '\controllers',
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            //此设置一经设置，生产环境最好不要更改。因为变更会导致用户本地cookies完全失效，需要重新生成。对于cookies要求高的服务可能会有异常。
            'cookieValidationKey' => '_cEdyngFHGyzJ4rC71rpLIRabWhJlTgg',
            'enableCookieValidation' => false,//true:加密校验
            'csrfParam' => '_csrf-' . $theAppId,
            'enableCsrfValidation' => false,//true:开启csrf校验
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => false,
            'identityCookie' => ['name' => '_identity-' . $theAppId, 'httpOnly' => true],
        ],
        'session' => [
            'name' => 'advanced-' . $theAppId,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => $defaultRoute,//site/error   web/index/index
        ],
        //'db' => $dbMysqlBrand['mysqlBrand'],

        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],

        'urlManager' => [
            'enablePrettyUrl' => true,//是否开启URL美化功能
            'showScriptName' => false,//是否在URL中显示入口脚本，如 index.php
            'enableStrictParsing' => false,//是否开启严格解析。该选项仅在开启美化功能后生效。在开启严格解析模式时，所有请求必须匹配 $rules[] 所声明的至少一个路由规则。
            //'suffix' => '.html',//设置一个 .html 之类的假后缀，是对美化功能的进一步补充
            'rules' => $rulesSite,
        ],

    ],
    'params' => $params,
];

if (YII_DEBUG) {
    //Gii生成代码工具，仅在 debug 状态下可以使用
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
