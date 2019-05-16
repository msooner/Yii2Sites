<?php
/**
 * User: Ron
 * Date: 2017/09/20 下午2:54
 * 逻辑层父类。
 * 注意：
 *     1、service层主要处理从controller中抽取出来的逻辑。
 *     2、service层处理数据时，通过model类的方法进行处理数据。不允许调用model层父类和yii2框架内的方法处理数据，此约束为统一处理数据入口。
 */

namespace common\service\core;

use Yii;

class BaseService {
    protected $_baseUrl = '';//应用的域
    protected $_suffix = '';//文件名后缀
    protected $_siteId = 0;//站点ID，site1:0  site2:1  site3:2
    protected $_terminalType = 11;//终端类型(1: android手机，2:iphone手机，3:iPad，10:PC站，11:M站)
    protected $_countryType = 1;//终端类型(1: android手机，2:iphone手机，3:iPad，10:PC站，11:M站)
    public $yiiPathInfo = '';//这个是入口脚本之后，问号之前（查询字符串）的部分

    public function __construct()
    {
        $this->_baseUrl = Yii::$app->params['baseUrl'];
        $this->_suffix = Yii::$app->params['suffix'];
        $this->_siteId = Yii::$app->params['siteId'];
        $this->_terminalType = YII_TERMINAL_TYPE;
        $this->yiiPathInfo = Yii::$app->request->pathInfo;
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
        return trim($ret);
    }

    /**
     * 添加默认参数
     *
     * @param array $data
     * @return array
     */
    public function getBaseParamSiteId($data = [])
    {
        if (!isset($data['siteId'])) $data = array_merge($data, ['siteId' => $this->_siteId]);
        if (!isset($data['siteType'])) $data = array_merge($data, ['siteType' => $this->_terminalType]);
        return $data;
    }

    /**
     * 添加默认参数
     *
     * @param array $data
     * @return array
     */
    public function getBaseParamAppTypeId($data = [])
    {
        if (!isset($data['appTypeId'])) $data = array_merge($data, ['appTypeId' => $this->_siteId]);
        if (!isset($data['terminalType'])) $data = array_merge($data, ['terminalType' => $this->_terminalType]);

        return $data;
    }
    public function getBaseCountryCodeType($data = [])
    {
        if (!isset($data['appTypeId'])) $data = array_merge($data, ['appTypeId' => $this->_siteId]);
        if (!isset($data['terminalType'])) $data = array_merge($data, ['terminalType' => $this->_terminalType]);
        if (!isset($data['type'])) $data = array_merge($data, ['type' => $this->_countryType]);
        return $data;
    }
    /**
     * 添加默认参数
     *
     * @param array $data
     * @return  array
     *
     */
    public function getBaseParamAppCountryType($data =[])
    {
        if (!isset($data['appTypeId'])) $data = array_merge($data, ['appTypeId' => $this->_siteId]);
        if (!isset($data['terminalType'])) $data = array_merge($data, ['terminalType' => $this->_terminalType]);
        return $data;

    }

    /**
     * 添加默认参数
     *
     * @param array $data
     * @return array
     */
    public function getBaseParamSiteIdForSolr($data = [])
    {
        if (!isset($data['site'])) $data = array_merge($data, ['site' => $this->_siteId]);
        if (!isset($data['terminalType'])) $data = array_merge($data, ['terminalType' => $this->_terminalType]);
        if (!isset($data['invoker'])) $data = array_merge($data, ['invoker' => YII_SITE_TYPE]);
        return $data;
    }

}