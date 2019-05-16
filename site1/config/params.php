<?php

return [
    'siteId' => 0,//站点ID，site1:0  site2:1  site3:2
    //项目的图片尺寸、质量
    'imgSize' => [
        'qImg' => 'x80',//图片质量
        'size_300_400' => '300x400',//图片尺寸
        'size_600_800' => '600x800',//图片尺寸
        'size_210_280' => '210x280',
        'size_450_600' => '450x600',
        'THUMB_300x400' => '_300x400x80.jpg',//WAP站图片缩略图使用
        'THUMB_210x280' => '_210x280x80.jpg',
        'THUMB_450x600' => 'THUMB_450x600.jpg',
    ],

    //redis缓存前缀，若需要分类，可在项目中定义来覆盖此配置
    'redis_namespace' => [
        'redis_namespace_web' => 'site1web_',
        'redis_namespace_wap' => 'site1wap_',
        'useType' => ['site1' => []],//配置站点的redis是使用单链接还是集群，配置了就是使用单点
        'redis_nav' => 'redis_nav_',//分类栏目的redis缓存前缀
    ],

];
