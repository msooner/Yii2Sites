<?php
/**
 * User: Ron
 * Date: 2017/09/20 上午11:41
 * 有关语言的配置
 */

return [
    //默认支持的语言
    'languageDefault' => 'en',

    //默认支持，不需要翻译的语言，不能改动
    'language_no_lang' => 'en',

    //配置不需要走自动翻译的语言
    'notAutoTranLanguage' => ['ar'],

    //语言id和语言code对应关系，数据表存储的是语言id。若要添加新语言，在此数据添加对应关系即可。
    //注意：在某些特殊情况下针对性地处理，需要把7转化为0（0也为英语）
    'language_code' => [
        1 => 'AR',
        2 => 'ES',
        3 => 'FR',
        4 => 'IT',
        5 => 'JA',
        6 => 'PL',
        7 => 'EN',
        8 => 'TW',
        9 => 'TR',
        12 => 'ID',
        15 => 'VI'
    ],

    //域名对应语言，可以多对一，不在对应列表的域名则对应英语。注意：域名对应的语言必须是language_code中配置有的语言。
    'domain_language' => [

    ],
];