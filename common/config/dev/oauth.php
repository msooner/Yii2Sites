<?php
/**
 * User: Ron
 * Date: 2017/11/24 下午4:08
 * 第三方授权的配置
 */

//处理使用http还是https
$httpProtocol = ((isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) ||
        isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') ? 'https' : 'http') . '://';

return [
    //注意：此数组第一维的键为 YII_APP_NAME 的值
    'site1' => [
        //Google配置
        'OAUTH_SDK_GOOGLE' => [
            'APP_KEY' => '',//应用注册成功后分配的 Client Id
            'APP_SECRET' => 'ca8tNeHFbQBaNEYaFess5JOY',//应用注册成功后分配的Client Secret
            'CALLBACK' => '',//回调地址
            'AUTHORIZE' => 'https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile',
        ],
        //facebook配置
        'OAUTH_SDK_FACEBOOK' => [
            'APP_KEY' => '',//应用注册成功后分配的 Client Id
            'APP_SECRET' => '',//应用注册成功后分配的Client Secret
            'CALLBACK' => '',//回调地址
        ],
        //获取gmail 联系人API
        'GMAIL_SDK_GOOGLE' => [
            'APP_KEY' => '',//应用注册成功后分配的 Client Id
            'APP_SECRET' => '',//应用注册成功后分配的Client Secret
            'CALLBACK' => '',//回调地址
        ],
    ],

    'site2' => [

    ],

    'site3' => [

    ]
];