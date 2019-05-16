<?php
//处理使用http还是https
$httpProtocol = ((isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) ||
        isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') ? 'https' : 'http') . '://';

return [
    'user.passwordResetTokenExpire' => 3600,

    //全局域，如 http://www.example.com  注意：域名后面不要带斜杠(/)  因为要与 Yii::$app->request->hostInfo 同步
    'baseUrl' => $httpProtocol . $_SERVER['HTTP_HOST'],
    'httpHostUrl' => $_SERVER['HTTP_HOST'],
    'httpProtocol' => $httpProtocol,

    //网站文件名后缀
    'suffix' => '.html',

    //日志管理的配置
    'logMangeConfig' => [
        'logLevel' => 2,//对应LogManage类中的 $_logLevel 日志等级（0：不写日志 1：写所有日志内容 2：写日志但不写retData的内容）
        'mysqlLogLevel' => 0,// 0：关闭写sql日志 1：开启写sql日志
    ],

    //cookie、redis缓存的通用缓存时间
    'cacheExpire.zero' => 0,//0小时
    'cacheExpire.halfAnHour' => 1800,//0.5小时
    'cacheExpire.oneHour' => 3600,//1小时
    'cacheExpire.twoHours' => 7200,//2小时
    'cacheExpire.twelveHours' => 43200,//12小时
    'cacheExpire.oneDay' => 86400,//1天
    'cacheExpire.twoDays' => 172800,//2天
    'cacheExpire.sevenDays' => 604800,//7天
    'cacheExpire.fifteenDays' => 1296000,//15天
    'cacheExpire.oneMon' => 2592000,//30天
    'cacheExpire.twoMons' => 5184000,//60天
    'cacheExpire.threeMons' => 7776000,//90天
    'cacheExpire.oneYear' => 31536000,//365天

    //项目的图片相关路径
    'imgUrl' => [
        'imgDefault' => '',
    ],

];
