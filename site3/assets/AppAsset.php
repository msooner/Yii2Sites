<?php

namespace site3\assets;

use yii\web\AssetBundle;

/**
 * Main application asset bundle.
 */
class AppAsset extends AssetBundle {
    public $basePath = '@webroot';
    public $baseUrl = '@web';//@web 表示的是当前运行应用的根 URL。
    //全局CSS
    public $css = [
        'css/site.css',
    ];
    //全局JS
    public $js = [

    ];
    //依赖关系
    public $depends = [
        'yii\web\YiiAsset',//主要包含yii.js 文件，该文件完成模块JavaScript代码组织功能，也为 data-method 和 data-confirm属性提供特别支持和其他有用的功能。
        'yii\bootstrap\BootstrapAsset',//包含从Twitter Bootstrap 框架的CSS文件
        //'yii\bootstrap\BootstrapPluginAsset',//包含从Twitter Bootstrap 框架的JavaScript 文件 来支持Bootstrap JavaScript插件
        //'yii\web\JqueryAsset',//包含从jQuery bower 包的jquery.js文件
        //'yii\jui\JuiAsset',//包含从jQuery UI库的CSS 和 JavaScript 文件
    ];

    //定义按需加载JS方法，注意加载顺序在body标签结束前。例：AppAsset::addScript($this,'@web/js/jquery-ui.custom.min.js');
    public static function addScript($view, $jsFile)
    {
        $view->registerJsFile($jsFile, [AppAsset::className(), 'depends' => 'web\assets\AppAsset']);
    }

    //定义按需加载css方法，注意加载顺序在body标签结束前。例：AppAsset::addCss($this,'@web/css/font-awesome.min.css');
    public static function addCss($view, $cssFile)
    {
        $view->registerCssFile($cssFile, [AppAsset::className(), 'depends' => 'web\assets\AppAsset']);
    }

    //在页面直接引用js和css的方式。depends指定依赖，position指定在head区域内加载。
    //css定义
    //AppAsset::register($this);//注册
    //$this->registerCssFile('@web/css/font-awesome.min.css', ['depends'=>['web\assets\AppAsset']]);
    //定义js
    //$this->registerJsFile('@web/js/jquery-ui.custom.min.js', ['depends'=>['web\assets\AppAsset']]);
    //$this->registerJsFile('@web/js/jquery-ui.custom.min.js', ['depends'=>['web\assets\AppAsset'], 'position'=>\yii\web\View::POS_HEAD]);

}
