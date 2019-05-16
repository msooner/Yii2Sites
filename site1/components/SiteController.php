<?php
/**
 * User: Ron
 * Date: 2019/02/18 下午1:51
 * pc/m站的基础父类。初始化应用的需要的基础内容。通过 init() 来初始化，不要通过构造函数来初始化。
 */

namespace SiteController\components;

use common\components\{CodeHttp, BaseController, CookiesSite1, PubFun, Utils, Language, VarCache};
use common\service\common\{CommonService, CookieYiiService};
use Yii;

class SiteController extends BaseController {

    public $layout = false;//设置不加载layout布局，也就是不使用布局
    public $breadCrumbNav = [];

    /**
     * 初始化一些必要内容   注意：不能使用构造函数来做初始化
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

    /**
     * 初始化PC端站点的内容
     */
    public function initWeb()
    {
        //初始化一些特定cookie

    }

    /**
     * 初始化移动端站点的内容
     */
    public function initWap()
    {
        //初始化一些特定cookie
    }

    /**
     * 初始化cookie： __uid
     */
    private function _cookieUsers()
    {
        if (!CookiesSite1::getUsers()) {
            CookiesSite1::setUsers(PubFun::genUuid());
        }
        $userId = Utils::getUserId();
        if ($userId != CookiesSite1::getUid() || !isset($_COOKIE['__uid'])) {
            CookiesSite1::setUid($userId);
        }
    }

    /**
     * 错误的请求方式提醒。
     * @author  Ron 2018-09-14
     * return []
     */
    public function errorRequestTypeTip() {
        // 如果不是异步请求，则返回错误
        if (!$this->isAjax() || !$this->isPost()) {
            $this->_ajaxReturn(CodeHttp::HTTP_INTERNAL_SERVER_ERROR, [], Language::lang('Request type error!'));
        }
    }

    /**
     * 判断如果是手机浏览器，则跳转对应的手机网址。
     *
     * @author Ron 2018-12-05
     */
    public function jumpToWap()
    {
        if (preg_match("/ipad/i", $_SERVER['HTTP_USER_AGENT'])) {
            $_SERVER['HTTP_USER_AGENT'] = substr($_SERVER['HTTP_USER_AGENT'], 0, 40);
        }
        $utm_term = isset($_GET['utm_term']) ? $_GET['utm_term'] : '';
        $isMobile = false;
        if ($utm_term != 'special' && preg_match("/android.*mobile|windows phone|iphone/iUs", preg_replace("/\[.*\]/Us", '', $_SERVER['HTTP_USER_AGENT']))) {
            $isMobile = true;
        } else if ($utm_term == 'special') {
            $isMobile = false;
        }
        define('IS_MOBILE', $isMobile);

        //通过域名前缀来取所用语言
        $baseUrlArr = explode(".", Yii::$app->params['httpHostUrl']);
        $langCurrent = strtolower($baseUrlArr[0]);
        $wapUrl = $this->_siteUrlWapUrl;
        $wapUrlLang = Yii::$app->params['siteUrl']['wapUrlLang'];
        if (isset($wapUrlLang[$langCurrent]) && $wapUrlLang[$langCurrent]) $wapUrl = $wapUrlLang[$langCurrent];
        $queryStr = empty($_SERVER['QUERY_STRING']) ? '' : $_SERVER['QUERY_STRING'];
        if ($isMobile && $wapUrl) {
            //跳转到wap站
            $url = preg_replace('/^\/index\.php/', '', $_SERVER['REQUEST_URI']);
            $url .= strpos($url, '?') === false ? '?frm=web' : '&frm=web';
            $url = $wapUrl . ltrim($url, '/');
            PubFun::RedirectAway($url, 301);
        }
    }

    /**
     * 针对render进行进一步处理，加入通用数据
     *
     * @param string $view view路径
     * @param array $params 数据集
     * @param bool $return 返回的方式
     * @param bool $isGzip 是否压缩页面代码
     * @return null|string|string[]
     */
    public function renderMine($view, $params = [], $return = false, $isGzip = true)
    {
        //合并数据，render里的数据覆盖已赋值的数据
        $params = array_merge($params, $this->_data);
        $str = $this->render($view, $params);
        if (1 && $isGzip) {
            ob_start("ob_gzhandler");
            $regArray = array(
                "/<!--(.*?)-->/i",
                "/\t/i",
                "/\n/i",
                "/\r\n/i"
            );
            $str = preg_replace($regArray, "", $str);
            $str = preg_replace(array("/([\s]{2,})/i"), " ", $str);
        }
        if ($return == TRUE) {
            return $str;
        } else {
            echo $str;
        }
    }

    /**
     * 设置header信息，不缓存页面
     */
    protected function showHeader()
    {
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pramga: no-cache");
    }

    /**
     * 展示错误信息页面，主要用于PC
     *
     * @author Ron 2019-01-31
     * @param string $message 错误提示信息
     * @param string $lingUrl 回跳地址
    */
    protected function showError($message = '', $lingUrl = '')
    {
        $seoInfo = [
            'title' => Language::lang('error tip'),
        ];
        if (empty($lingUrl)) {
            $lingUrl = Yii::$app->params['siteUrl']['webUrl'];
        }
        $data = [
            'seoInfo' => $seoInfo,
            'errorUrl' => $lingUrl,
            'message' => $message,
        ];
        $this->renderMine('//web/error/ErrorMessage', $data);
        Yii::$app->response->send();
        exit();
    }

}
