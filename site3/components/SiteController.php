<?php
/**
 * User: Ron
 * Date: 2017/09/21 下午1:55
 * 基础父类
 */

namespace site3\components;

use Yii;
use common\components\PubFun;
use common\components\BaseController;
use common\components\CookiesSite3;
use yii\base\InvalidConfigException;

class SiteController extends BaseController {

    public $layout = false;//设置不加载layout布局，也就是不使用布局

    /**
     * 初始化一些必要内容
     */
    public function init()
    {
        parent::init();
        if (YII_SITE_TYPE == 'web') {
            $this->initWeb();
        } elseif (YII_SITE_TYPE == 'wap') {
            $this->initWap();
        }
    }

    public function initWeb()
    {

    }

    public function initWap()
    {

    }


    /**
     * 针对render进行进一步处理，加入通用数据
     *
     * @param string $view view路径
     * @param array $params 数据集
     * @return string
     */
    public function renderMine($view, $params = [])
    {
        $params = array_merge($params, $this->_data);
        return $this->render($view, $params);
    }


}