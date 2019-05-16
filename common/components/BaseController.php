<?php
/**
 * User: Ron
 * Date: 2017/09/21 下午1:49
 * Controller 公共基础父类。此类放置基础内容，但不做任何初始化操作。
 *
 * 对于render指定目录说明：
 *     目前是在对应项目下分多个站点（如PC与M站分离），独立入口，对应controller、view也需要对应的分目录管理，因此而产生的问题是对view下目录的管理有一定的要求。
 *     例子：如view下有web/down/index.php
 *     如果用$this->render('index')方式来指定渲染路径，则controller命名必须与view下的目录要对应。web/DownController/index对应view/web/down/index.php
 *     如果用$this->render('//web/down/index')方式来指定任意的绝对渲染路径，则controller可以自定义view下目录。web/DownController/index可以对应view/自定义目录/……/index.php
 */

namespace common\components;

use yii\base\InvalidConfigException;
use yii\web\Controller;
use yii\web\Response;
use Yii;

class BaseController extends Controller {

    protected $_data = [];//全局需要的view数据集
    protected $_baseUrl = '';//应用的顶级域
    protected $_siteUrlWebUrl = '';//PC英文站访问地址
    protected $_siteUrlWapUrl = '';//M英文站访问地址
    protected $_suffix = '';//域名后缀
    protected $_siteId = 0;//站点ID，site1:0  site2:1  site3:2
    protected $_terminalType = 11;//终端类型(1: android手机，2:iphone手机，3:iPad，10:PC站，11:M站)
    public $yiiPathInfo = '';//这个是入口脚本之后，问号之前（查询字符串）的部分
    public $yiiControllerName = '';//控制器名称

    public function init()
    {
        $this->initRequestParam();
        $this->_baseUrl = Yii::$app->params['baseUrl'];
        $this->_siteUrlWebUrl = Yii::$app->params['siteUrl']['webUrl'];
        $this->_siteUrlWapUrl = Yii::$app->params['siteUrl']['wapUrl'];
        $this->_suffix = Yii::$app->params['suffix'];
        $this->_siteId = Yii::$app->params['siteId'];
        $this->_terminalType = YII_TERMINAL_TYPE;
        $this->yiiPathInfo = Yii::$app->request->pathInfo;
        $this->yiiControllerName = $this->id;

        define('YII_CONTROLLER_NAME', $this->id);
    }

    /**
     * 格式化输出json数据
     *
     * @param int $code 状态
     * @param array $data 数据
     * @param string $msg 信息
     */
    protected function _ajaxReturn($code = 0, $data = array(), $msg = '')
    {
        $return = array(
            'code' => $code,
            'data' => $data,
            'message' => $msg
        );
        Yii::$app->response->format = Response::FORMAT_JSON;
        Yii::$app->response->data = $return;
        Yii::$app->response->send();
        exit;
    }

    /**
     * 获取请求的数据
     *
     * @param string $name 键名
     * @param string $default 默认值
     * @return array|mixed|string
     */
    public function getParam($name, $default = '')
    {
        if (Yii::$app->request->isPost) {
            $ret = Yii::$app->request->post($name, $default);
        } elseif (Yii::$app->request->isGet) {
            $ret = Yii::$app->request->get($name, $default);
        } else {
            $ret = $default;
        }
        return is_string($ret) && !empty($ret) ? trim($ret) : $ret;
    }

    /**
     * 该请求是一个 AJAX 请求
     *
     * @return bool
     */
    public function isAjax()
    {
        if (Yii::$app->request->isAjax) {
            return true;
        }
        return false;
    }

    /**
     * 该请求是一个 AJAX 请求
     *
     * @return bool
     */
    public function isPost()
    {
        if (Yii::$app->request->isPost) {
            return true;
        }
        return false;
    }

    /**
     * 设置header，使不使用页面缓存
     */
    protected function setHeaderNoCache()
    {
        /**设置header信息--不缓存页面*/
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pramga: no-cache");
    }

}