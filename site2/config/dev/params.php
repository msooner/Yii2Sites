<?php
return [
    //站点域名  注意：配置的域名后面不要带斜杠(/)  因为要与 Yii::$app->request->hostInfo 同步
    'siteUrl' => [
        'webUrl' => $httpProtocol . 'web站点域名',
        'wapUrl' => $httpProtocol . 'wap站点域名',
        'wapUrlLang' => [
            //小语种下
        ],
    ],
    //设置cookie域：默认为M站
    'setDomain' => [
        'testDomain' => '测试环境域名',
        'prodDomain' => '生产环境域名',
        'devDomain'  => '开发环境域名',
        'web' => [
            'testDomain' => '测试环境域名',
            'prodDomain' => '生产环境域名',
            'devDomain'  => '开发环境域名',
        ]
    ],

];
