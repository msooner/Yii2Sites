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
            'APP_KEY' => '1029012810259-pd60bo2s3p9fq35lmebno1hda8crevdh.apps.googleusercontent.com',//应用注册成功后分配的 Client Id
            'APP_SECRET' => 'QZ8UoiPtExe_vgsaNopfiu3Z',//应用注册成功后分配的Client Secret
            'CALLBACK' => $httpProtocol . $_SERVER['HTTP_HOST']. '/oauth/callback/google',//回调地址
            'AUTHORIZE' => 'https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile',
        ],
        //facebook配置
        'OAUTH_SDK_FACEBOOK' => [
            'APP_KEY' => '539332112801050',//应用注册成功后分配的 Client Id
            'APP_SECRET' => '135deaaae73bc1e291d6d310eda25b86',//应用注册成功后分配的Client Secret
            'CALLBACK' => $httpProtocol .  $_SERVER['HTTP_HOST']. '/oauth/callback/facebook',//回调地址
        ],
        //获取gmail 联系人API
        'GMAIL_SDK_GOOGLE' => [
            'APP_KEY' => '8072374380-daj3qj4atto9g4nljv5butp1fpm6b20v.apps.googleusercontent.com',//应用注册成功后分配的 Client Id
            'APP_SECRET' => '-vuctvCIVeasqG_l0CqS-NQG',//应用注册成功后分配的Client Secret
            'CALLBACK' => $httpProtocol . $_SERVER['HTTP_HOST'] . '/UserInviteAction/gmailContact',//回调地址
        ],
    ],

    'markavip' => [

    ],

    'nimini' => [

    ]
];