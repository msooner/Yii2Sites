<?php
/**
 * User: Ron
 * Date: 2017/09/21 下午1:53
 * markavip 的公共基础父类。初始化应用的需要的基础内容。通过 init() 来初始化，不要通过构造函数来初始化。
 */

namespace site2\components;

use Yii;
use common\components\BaseController;
use common\components\CookiesSite2;
use common\components\PubFun;

class SiteController extends BaseController {

    public $layout = false;//设置不加载layout布局，也就是不使用布局
    protected $_siteLanguage = 'EN';//当前访问域的语言
    protected $_serverName = '';
    protected $_userIp = '';

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

        //获取用户当前IP地址
        $this->getUserIp();
        $this->_serverName = Yii::$app->request->url;//在使用虚拟后缀情况下，和 $_SERVER['SCRIPT_NAME'] 有差别
    }

    public function initWeb()
    {

    }

    public function initWap()
    {

    }

    /**
     * 获取用户当前的IP地址
     */
    public function getUserIp()
    {
        $userIp = PubFun::getIP();
        if ($this->getParam('ip')) {
            $userIp = $this->getParam('ip');
        }
        $this->_userIp = $userIp;
    }

}
