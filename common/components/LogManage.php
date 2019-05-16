<?php
/**
 * User: Ron
 * Date: 2017/09/22 上午11:13
 * 自定义日志管理类
 */

namespace common\components;

use Yii;

class LogManage {

    /**
     * 日志版本号
     *
     * @var string
     */
    protected $_logVer = '1.0';

    /**
     * 应用的类型，如nimini的pc的日志：app-nimini-pc  Yii::$app->id . '-' . YII_SITE_TYPE
     *
     * @var string
     */
    protected $_logApp = 'web_log';

    /**
     * 日志类型（0：能用类型  1：错误日志  2：性能日志）
     *
     * @var int
     */
    public $_logType = 0;

    /**
     * 日志等级（0：不写日志 1：写所有日志内容 2：写日志但不写retData的内容）
     *
     * @var int
     */
    public $_logLevel = 0;

    /**
     * 日志模块，目前都为0
     *
     * @var int
     */
    public $_logModule = 0;

    /**
     * 记录开始计时时间，单位为毫秒
     *
     * @var int
     */
    public $_processStartTime = 0;

    /**
     * 写日志所在的方法
     *
     * @var string
     */
    protected $_method = '';


    public function __construct()
    {
        $this->_logApp = Yii::$app->id;
        if (isset(Yii::$app->params['logMangeConfig']['logLevel']))
            $this->_logLevel = Yii::$app->params['logMangeConfig']['logLevel'];
    }

    public function __set($name, $value)
    {
        if (isset($this->$name)) {
            $this->$name = $value;
        } else {
            throw new \ErrorException("属性{$name}不存在");
        }
    }

    public function __get($name)
    {
        if (isset($this->$name)) {
            return $this->$name;
        } else {
            throw new \ErrorException("属性{$name}不存在");
        }
    }

    /**
     * 格式化日志，统一日志格式，规范内容
     *
     * @param array $msgData 需要记录日志的数据
     * @return bool
     */
    public function LogFormat($msgData)
    {
        if (!array_key_exists('method', $msgData))
            $msgData['method'] = Yii::$app->request->getMethod();//一般使用中取 __METHOD__ 传进来
        if ($this->_method) $msgData['method'] = $this->_method;
        $logArr = array(
            'ver' => $this->_logVer,
            'serTime' => date('Y-m-d H:i:s'),
            'LogApp' => $this->_logApp,
            'LogType' => $this->_logType,
            'LogLevel' => $this->_logLevel,
            'logModule' => $this->_logModule,
            'userId' => array_key_exists('userId', $msgData) ? $msgData['userId'] : 0,
            'content' => array(
                'processTime' => array_key_exists('processTime', $msgData) ? $msgData['processTime'] : 0,
                'serverAddr' => $_SERVER['SERVER_ADDR'],
                'method' => $msgData['method'],
                'userAgent' => array_key_exists('userAgent', $msgData) ? $msgData['userAgent'] : Yii::$app->request->getUserAgent(),
                'userHost' => array_key_exists('userHost', $msgData) ? $msgData['userHost'] : Yii::$app->request->getUserHost(),
                'userIp' => array_key_exists('userIp', $msgData) ? $msgData['userIp'] : Yii::$app->request->getUserIp(),
                'requestType' => array_key_exists('requestType', $msgData) ? $msgData['requestType'] : 0,//默认为0，若有请求和响应则：1是请求，2是响应
                'requestData' => array_key_exists('requestData', $msgData) ? $msgData['requestData'] : '',//$_REQUEST
                'requestUrl' => array_key_exists('requestUrl', $msgData) ? $msgData['requestUrl'] : Yii::$app->request->absoluteUrl,
                'retCode' => array_key_exists('retCode', $msgData) ? $msgData['retCode'] : 0,
                'retMsg' => array_key_exists('retMsg', $msgData) ? $msgData['retMsg'] : '',//一般的日志内容，或者请求返回的一般内容
                'retData' => array_key_exists('retData', $msgData) ? $msgData['retData'] : '',//mb_substr(json_encode($this->retData), 0, 1500),
            ),
        );
        return json_encode($logArr, true);
    }

    /**
     * @param string $namePath 路径
     * @param string $msg 需要记录的内容
     * @return bool
     */
    public function logRecord($namePath, $msg)
    {
        try {
            $filePath = Yii::getAlias('@YiiSiteApp') . '/runtime/logs/' . YII_SITE_TYPE . '/' . $namePath;

            if (!file_exists($filePath)) {
                mkdir($filePath, 0777, true);
            }
            $fileName = $filePath . '/' . date('YmdH') . '_' . $namePath . '.log';
            if ($fileName != '') {
                $file = fopen($fileName, 'a+');
                //flock($file, LOCK_EX);
                fwrite($file, $msg . "\r\n");
                //flock($file, LOCK_UN);
                fclose($file);
            }
        } catch (\ErrorException $e) {
            //异常
        }
        return true;
    }

    /**
     * 记录日志
     *
     * @param array $theMsg 消息内容列表
     * @param string $path 目录
     * @return bool
     */
    public function writeLog($theMsg = array(), $path = 'logJavaApi')
    {
        if($this->_logLevel <= 0) return false;//不写日志

        $msgData = array();
        if (array_key_exists('userId', $theMsg)) $msgData['userId'] = $theMsg['userId'];
        if ($this->_processStartTime) $msgData['processTime'] = PubFun::getMillisecondInt() - $this->_processStartTime;
        if (array_key_exists('method', $theMsg)) $msgData['method'] = $theMsg['method'];
        if (array_key_exists('userAgent', $theMsg)) $msgData['userAgent'] = $theMsg['userAgent'];
        if (array_key_exists('userHost', $theMsg)) $msgData['userHost'] = $theMsg['userHost'];
        if (array_key_exists('userIp', $theMsg)) $msgData['userIp'] = $theMsg['userIp'];
        if (array_key_exists('requestType', $theMsg)) $msgData['requestType'] = $theMsg['requestType'];
        if (array_key_exists('requestData', $theMsg)) $msgData['requestData'] = $theMsg['requestData'];
        if (array_key_exists('requestUrl', $theMsg)) $msgData['requestUrl'] = $theMsg['requestUrl'];
        if (array_key_exists('retCode', $theMsg)) $msgData['retCode'] = $theMsg['retCode'];
        if (array_key_exists('retMsg', $theMsg)) $msgData['retMsg'] = $theMsg['retMsg'];
        if (array_key_exists('retData', $theMsg) && $this->_logLevel == 1) $msgData['retData'] = $theMsg['retData'];

        $formatData = $this->LogFormat($msgData);
        $this->logRecord($path, $formatData);

        return true;
    }

    /**
     * 记录redis日志
     *
     * @author Ron 2018-11-26
     * @param array $logMsg 日志信息
     * @param string $logPath 日志路径
     * @return boolean
     */
    public function writeShortLog($logMsg, $logPath)
    {
        if($this->_logLevel <= 0) return false; //不写日志
        if (empty($logMsg) || empty($logPath)) return false;
        //短日志类型为：写入时间、描述字符串字符串
        if (!isset($logMsg['processTime']) || empty($logMsg['processTime'])) $logMsg['processTime'] = date('Y-m-d H:i:s');
        $formatData = json_encode($logMsg, true);
        $this->logRecord($logPath, $formatData);

        return true;
    }

    /**
     * 简便地记录相关日志
     *
     * @param string $msg 消息内容
     * @param int $pathSign 目录标识
     * @return bool
     */
    public function writeLogMsg($msg = '', $pathSign = 0)
    {
        $shortLogSign = [3, 4];
        $thePath = 'logMsg';
        $pathList = [1 => 'logOauth', 2 => 'logQueue', 3 => 'logRedis', 4 => 'logRedisRead'];
        if (array_key_exists($pathSign, $pathList)) $thePath = $pathList[$pathSign];
        //判断记录日志的类型
        if (in_array($pathSign, $shortLogSign))
            return $this->writeShortLog(['retMsg' => $msg], $thePath);
        else
            return $this->writeLog(['retMsg' => $msg, 'userAgent' => '', 'requestUrl' => ''], $thePath);
    }

}