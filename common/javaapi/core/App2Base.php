<?php
/**
 * User: Ron
 * Date: 2017/09/20 上午10:59
 * app2 的父类
 */

namespace common\javaapi\core;

use common\components\LogManage;
use Yii;
use common\components\CookiesSite1;
use common\components\Language;
use common\components\PubFun;
use common\components\RequestApi;
use common\components\Utils;

class App2Base extends BaseApiCore {
    protected $_theConfig = array();
    protected $_timeout = 10;//调java接口的超时时间（秒）
    protected $_retLogType = 'app2Log';//日志标识
    protected $_terminalType = 11;//终端类型(1: android手机，2:iphone手机，3:iPad，10:PC站，11:M站)
    protected $_appVersion = 10;//版本
    protected $_appTypeId = 0;//站点（0:site1, 1:site2, 2:site3）
    protected $_rowsPerpage = 12; //每页请求商品数量
    protected $_pcLocalVersion = 1;//0 表示老版本，返回美金（默认），1 表示新版本，返回本地币种

    public function __construct($parameter = array())
    {
        $this->_theConfig = require(__DIR__ . '/../../../common/config/' . YII_ENV . '/app2.config.php');

        if(isset($this->_theConfig['paramCommonData']) && isset($this->_theConfig['paramCommonData'][YII_APP_NAME])) {
            $paramCommonData = $this->_theConfig['paramCommonData'];
            if(isset($paramCommonData[YII_APP_NAME][YII_SITE_TYPE]['siteId'])) {
                $this->_terminalType = $paramCommonData[YII_APP_NAME][YII_SITE_TYPE]['siteId'];
            }
            if(isset($paramCommonData[YII_APP_NAME][YII_SITE_TYPE]['appTypeId'])) {
                $this->_appTypeId = $paramCommonData[YII_APP_NAME][YII_SITE_TYPE]['appTypeId'];
            }
        }
        if (array_key_exists('terminalType', $parameter)) $this->_terminalType = $parameter['terminalType'];
    }

    /**
     * 初始化参数
     */
    protected function _getParams()
    {
        $this->_params = array();
        //pageNum 第几页，从1开始     pageSize 每页条数
        $this->_params['terminalType'] = $this->_terminalType;
        $this->_params['appVersion'] = $this->_appVersion;
        $this->_params['lang'] = Language::getChangeLanguageCode();
        $this->_params['appTimestamp'] = time() * 1000;//时间戳，毫秒
        $this->_params['appTypeId'] = $this->_appTypeId;
        $this->_params['rowsPerpage'] = $this->_rowsPerpage;

    }

    /**
     * 统一请求处理
     *
     * @param string $configUrl 请求的接口配置
     * @param array $returnWithData 需要合并到返回中的格式化内容
     * @return array|mixed
     */
    protected function _requestForPostString($configUrl, $returnWithData = [])
    {
        $resContentsJson = RequestApi::sendApiRequest($this->_theConfig[$configUrl], $this->_params, $this->_poststring, $this->_headerRwa, $this->_timeout, $this->_retLogType);
        $formatResultData = $this->formatResult($resContentsJson);
        if($formatResultData['isSuccessData']) {
            if(!isset($formatResultData['resContentsArr']['result']) || (int)$formatResultData['resContentsArr']['result'] !== 0) {
                $this->errorApp2Log(['requestUrl' => $this->_theConfig[$configUrl],'param' => $this->_params,'postString','timeout' => $this->_timeout,'resultData' => $resContentsJson]);
            }
            return $formatResultData['resContentsArr'];
        } else {
            $this->errorApp2Log(['requestUrl' => $this->_theConfig[$configUrl],'param' => $this->_params,'postString','timeout' => $this->_timeout,'resultData' => $resContentsJson]);
            return $this->_returnData($returnWithData);
        }
    }

    /**
     * 统一GET API请求处理
     *
     * @author Ron 2018-04-18
     * @param string $configUrl 请求的接口配置
     * @param array $returnWithData 需要合并到返回中的格式化内容
     * @return array|mixed
     */
    protected function _requestForGet($configUrl, $returnWithData = [])
    {
        $resContentsJson = RequestApi::sendApiRequest($this->_theConfig[$configUrl], $this->_params, $this->_theGet, $this->_headerRwa, $this->_timeout, $this->_retLogType);
        $formatResultData = $this->formatResult($resContentsJson);
        if($formatResultData['isSuccessData']) {
            if(!isset($formatResultData['resContentsArr']['result']) || (int)$formatResultData['resContentsArr']['result'] !== 0) {
                $this->errorApp2Log(['requestUrl' => $this->_theConfig[$configUrl],'param' => $this->_params,'get','timeout' => $this->_timeout,'resultData' => $resContentsJson]);
            }
            return $formatResultData['resContentsArr'];
        } else {
            $this->errorApp2Log(['requestUrl' => $this->_theConfig[$configUrl],'param' => $this->_params,'postString','timeout' => $this->_timeout,'resultData' => $resContentsJson]);
            return $this->_returnData($returnWithData);
        }
    }


    protected function formatResult($resContentsJson) {
        $resContentsArr = [];
        if($resContentsJson) {
            $resContentsArr = json_decode($resContentsJson,true);
            if(is_array($resContentsArr)) {
                $isSuccessData = true;
            } else {
                $isSuccessData = false;
            }
        } else {
            $isSuccessData = false;
        }
        return [
            'resContentsArr' => $resContentsArr,
            'isSuccessData' => $isSuccessData
        ];
    }


    public function errorApp2Log($msgData) {
        $logManage = new LogManage();
        $logManage->writeShortLog($msgData,'errorApp2Log');
    }

}