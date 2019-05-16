<?php
return [
    'siteId' => 3,//站点ID，site1:0  site2:1  site3:2

    //redis缓存前缀，若需要分类，可在项目中定义来覆盖此配置
    'redis_namespace' => [
        'redis_namespace_web' => 'site3web_',
        'redis_namespace_wap' => 'site3wap_',
        'useType' => ['site3' => ['web', 'wap']],//配置站点的redis是使用单链接还是集群
    ],

];
