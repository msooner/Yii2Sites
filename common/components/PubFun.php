<?php
/**
 * User: Ron
 * Date: 2017/09/20 上午11:19
 * 常用公共方法
 * 注意：和后端[redis/mongo/service……]有数据交互有公共方法放Utils类，和后端没数据交互的公共方法请放PubFun类。
 */

namespace common\components;

use Yii;
use yii\helpers\Url;

class PubFun {

    private static $schemes = [
        'http',
        'https'
    ];

    /**
     * 获取毫秒时间,目前主要用于计算程序的执行时间
     */
    public static function getMillisecond()
    {
        list($t1, $t2) = explode(' ', microtime());
        return (float)sprintf('%.0f', (floatval($t1) + floatval($t2)) * 1000);
    }

    /**
     * 组装数据，主要用在处理ajax、api接口服务中，统一返回数据格式
     *
     * @author Ron  2017-02-23
     *
     * @param $code int
     * @param $msg string
     * @param $data array
     * @return  array
     */
    public static function packageArray($code = 200, $msg = 'success', $data = array())
    {
        return array(
            'code' => $code,
            'data' => $data,
            'message' => $msg
        );
    }

    /**
     * 判断数据是不是json格式数据
     *
     * @author Ron  2017-02-22
     *
     * @param string $str json_encode处理后返回的是字符串
     * @return bool 不是json时返回TRUE，是json时返回FALSE
     */
    public static function is_not_json($str)
    {
        if (is_numeric($str) || is_bool($str) || is_null($str) || is_array($str))
            return true;
        return is_null(json_decode($str));
    }

    /**
     * 获取毫秒时间，目前主要用于计算程序的执行时间
     *
     * @return float
     */
    public static function getMillisecondFloat()
    {
        list($t1, $t2) = explode(' ', microtime());
        return (float)sprintf('%.0f', (floatval($t1) + floatval($t2)) * 1000);
    }

    /**
     * 获取毫秒时间，目前主要用于计算程序的执行时间
     *
     * @return int
     */
    public static function getMillisecondInt()
    {
        list($t1, $t2) = explode(' ', microtime());
        return (int)((floatval($t1) + floatval($t2)) * 1000);
    }

    /**
     * 获取唯一值，依据随机数
     *
     * @return string
     */
    public static function getUniqueValue()
    {
        return md5(uniqid(mt_rand(), true));
    }

    /**
     * 获得用户的真实IP地址
     *
     * @return array|false|null|string
     */
    public static function getIP()
    {
        static $realip = NULL;
        if ($realip !== NULL) {
            return $realip;
        }

        if (isset($_SERVER)) {
            if (isset($_SERVER ['HTTP_X_FORWARDED_FOR'])) {
                $arr = explode(',', $_SERVER ['HTTP_X_FORWARDED_FOR']);
                /* 取X-Forwarded-For中第一个非unknown的有效IP字符串 */
                foreach ($arr as $ip) {
                    $ip = trim($ip);
                    if ($ip != 'unknown') {
                        $realip = $ip;
                        break;
                    }
                }
            } elseif (isset($_SERVER ['HTTP_CLIENT_IP'])) {
                $realip = $_SERVER ['HTTP_CLIENT_IP'];
            } elseif (isset($_SERVER ['HTTP_X_REAL_IP'])) {
                $realip = $_SERVER ['HTTP_X_REAL_IP'];
            } else {
                if (isset($_SERVER ['REMOTE_ADDR'])) {
                    $realip = $_SERVER ['REMOTE_ADDR'];
                } else {
                    $realip = '0.0.0.0';
                }
            }
        } else {
            if (getenv('HTTP_X_FORWARDED_FOR')) {
                $realip = getenv('HTTP_X_FORWARDED_FOR');
            } elseif (getenv('HTTP_CLIENT_IP')) {
                $realip = getenv('HTTP_CLIENT_IP');
            } else {
                $realip = getenv('REMOTE_ADDR');
            }
        }

        preg_match("/[\d\.]{7,15}/", $realip, $onlineip);
        $realip = !empty($onlineip [0]) ? $onlineip [0] : '0.0.0.0';

        return $realip;
    }

    /**
     * 根据参数名称(除sign)将所有请求参数按照字典顺序排序（加上key）后放入数组中
     *
     * @author Ron  2017-02-21
     * @param array $params
     * @return array
     */
    public static function getSortParamUrl($params)
    {
        ksort($params);
        $hash = array();
        foreach ($params as $key => $val) {
            array_push($hash, $key . '=' . $val);
        }
        return $hash;
    }

    /**
     * 组装url，并且以sha256方式加密字符串
     *
     * @author Ron  2017-02-21
     * @param array $hashParams
     * @return string
     */
    public static function getHashSign($hashParams)
    {
        $sign = hash('sha256', implode('&', $hashParams), false);
        return $sign;
    }

    /**
     * 生成url
     *
     * @param $str
     * @return mixed|string
     */
    public static function generateUrl($str)
    {
        $uri = urlencode(preg_replace('/[\.|\/|\?|&|(|)|\/|\+|\\\|\'|"|,]+/', '-', strtolower(trim($str))));
        $uri = str_replace('+', '-', $uri);
        $uri = str_replace('%', '-', $uri);
        $uri = str_replace('__', '-', $uri);
        $uri = str_replace('-.', '.', $uri);//区别
        $uri = preg_replace('/[-]+/', '-', $uri);
        return $uri;
    }

    /**
     * @param $str
     * @return mixed|string
     */
    public static function generateUrl_un($str)
    {
        $str = str_replace('\'', '', $str);
        $uri = urlencode(preg_replace('/[\.|\/|\?|&|(|)|\/|\+|\\\|\'|"|,]+/', '-', strtolower(trim($str))));
        $uri = str_replace('+', '-', $uri);
        $uri = str_replace('%', '-', $uri);
        $uri = str_replace('__', '-', $uri);
        $uri = str_replace('-.', '-', $uri);//区别
        $uri = preg_replace('/[-]+/', '-', $uri);
        return $uri;
    }

    /**
     * @param $str
     * @return mixed|string
     */
    public static function generateUrlUnline($str)
    {
        $uri = urlencode(preg_replace('/[\.|\/|\?|&|(|)|\/|\+|\\\|\'|"|,]+/', '_', strtolower(trim($str))));
        $uri = str_replace('+', '_', $uri);
        $uri = str_replace('%', '_', $uri);//区别
        $uri = str_replace('__', '_', $uri);
        $uri = str_replace('-.', '_', $uri);
        $uri = preg_replace('/[-]+/', '_', $uri);
        return $uri;
    }

    /**
     * 取到所有参数，组装url
     *
     * @param string $url
     * @param array $arrayParam
     * @return string
     */
    public static function getHttpUrlParam($url, $arrayParam = array())
    {
        $getParam = Yii::$app->request->get();
        $hasDataParam = [];
        if($getParam) {
            foreach ($getParam as $paramName => $item) {
                if($item) {
                    $hasDataParam[$paramName] = $item;
                }
            }
        }
        $urlQueryResult = parse_url($url);
        if(isset($urlQueryResult['query']) && $urlQueryResult['query']) {
            parse_str($urlQueryResult['query'],$urlQueryArr);
            if($urlQueryArr) {
                $hasDataParam = array_merge($hasDataParam,$urlQueryArr);
            }
        }
        $urlArr = explode('?',$url);
        $getParam = array_merge($hasDataParam, $arrayParam);
        if ($getParam) {
            return $urlArr[0] . '?' . http_build_query($getParam);
        } else {
            return $url;
        }
    }

    /**
     * 验证是否是合法的邮件地址
     *
     * @param $email
     * @return bool
     */
    public static function isEmail($email)
    {
        if (preg_match("/^[\w-+\.]+@([\w-]+\.)+[\w-]{2,}$/i", $email)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 是否移动端设备
     *
     * @return bool
     */
    public static function isMobile()
    {
        $isMobile = false;
        if (preg_match("/android.*mobile|windows phone|iphone/iUs", preg_replace("/\[.*\]/Us", '', $_SERVER['HTTP_USER_AGENT']))) {
            $isMobile = true;
        }
        return $isMobile;
    }

    /**
     * 检查客户端的类型，是手机还是pc。手机又分ios和andorid
     * @return array
     */
    public static function clientType() {
        //获取USER AGENT
        $agent = strtolower($_SERVER['HTTP_USER_AGENT']);
        //分析数据
        $isPc = (strpos($agent, 'windows nt')) ? true : false;
        $isIphone = (strpos($agent, 'iphone')) ? true : false;
        $isIpad = (strpos($agent, 'ipad')) ? true : false;
        $isAndroid = (strpos($agent, 'android')) ? true : false;
        $browserType = self::determineBrowser($agent);
        if($isIphone || $isIpad) {
            return [
                'systemType' => 'ios',
                'browserType' => $browserType['browserAgent']
            ];
        }
        if($isAndroid) {
            return [
                'systemType' => 'android',
                'browserType' => $browserType['browserAgent']
            ];
        }
        if($isPc) {
            return [
                'systemType' => 'windows',
                'browserType' => $browserType['browserAgent']
            ];
        }
        return [];
    }

    /**
     * 判断是否正确的电话号码
     *
     * @param int $target 号码
     * @return bool
     */
    public static function isTel($target)
    {
        if (preg_match('/^([0-9-+ ()\[\]])+$/', trim($target))) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 验证带区号的手机格式（8615912345678或86-15912345678）
     *
     * @author Ron 2018-02-06
     * @param string $phone 手机号
     * @param string $no 国家区号
     * @return bool
     */
    public static function isPhoneNo($phone, $no)
    {
        if (preg_match('/^[0-9]+\-[0-9]+$/', $phone)) { //验证格式，86-15912345678
            return true;
        } else if (is_numeric($phone) && $no && strpos($phone, $no) === 0) { //验证格式，（8615912345678
            return true;
        }
        return false;
    }

    /**
     * 验证登录手机号格式（86-15912345678或者15912345678）
     *
     * @author Ron 2018-02-07
     * @param string $phone 手机号码
     * @return bool
     */
    public static function isLoginPhone($phone)
    {
        if (preg_match('/^([0-9]+\-)?[0-9]+$/', $phone)) {
            return true;
        }
        return false;
    }

    /**
     * 将IP转换格式(用于在数据库查询得到相应国家)
     *
     * @param $ip
     * @return int
     */
    public static function numIP($ip)
    {
        $newip = 0;
        $ip = explode(".", $ip);
        for ($i = 0; $i < 4; $i++) {
            if ($i == 0) {
                $ip[$i] = $ip[$i] * 256 * 256 * 256;
            } elseif ($i == 1) {
                $ip[$i] = $ip[$i] * 256 * 256;
            } elseif ($i == 2) {
                $ip[$i] = $ip[$i] * 256;
            }
            $newip = $ip[0] + $ip[1] + $ip[2] + $ip[3];
        }
        return $newip;
    }

    /**
     * 将数字转换为IP，进行上面numIP()函数的逆向过程
     *
     * @param $long
     * @return bool|string
     */
    public static function long2ip($long)
    {
        // Valid range: 0.0.0.0 -> 255.255.255.255
        if ($long < 0 || $long > 4294967295) return false;
        $ip = "";
        for ($i = 3; $i >= 0; $i--) {
            $ip .= (int)($long / pow(256, $i));
            $long -= (int)($long / pow(256, $i)) * pow(256, $i);
            if ($i > 0) $ip .= ".";
        }
        return $ip;
    }

    /**
     * 页面协议获取。议类型：https 或者 http
     *
     * @return string
     */
    public static function getProtocol()
    {
        return (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1)
            || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') ? 'https' : 'http';
    }

    /**
     * 检测字符串是否由纯英文，纯中文，中英文混合组成
     * 1、此函数除了可以检测中文外，也可以用来检测其他非英文字符；
     * 2、对于英语系得语言，比如法语：有些时候会有误判得情况，使用得时候要特别注意
     *
     * @param string $str
     * @return int|string 1:纯英文;2:纯中文;3:中英文混合
     */
    public static function checkStrLanguage($str = '')
    {
        if (trim($str) == '') {
            return '';
        }
        $m = mb_strlen($str, 'utf-8');
        $s = strlen($str);
        if ($s == $m) {
            return 1;
        }
        if ($s % $m == 0 && $s % 3 == 0) {
            return 2;
        }
        return 3;
    }

    /**
     * 过滤地址特殊字符。
     *
     * @author Ron  2017-08-14
     * @param string $strParam
     * @return mixed
     */
    public static function replaceSpecialChar($strParam)
    {
        //$regex = "/\/|\~|\!|\@|\#|\\$|\%|\^|\&|\*|\(|\)|\_|\+|\{|\}|\:|\<|\>|\?|\[|\]|\,|\.|\/|\;|\'|\`|\-|\=|\\\|\|/";//例子
        $regex = '/\"|\\"|\'|\\\|\/|\_|\<|\>|\\?|\=|\||\\|/';//收货地址中要过滤的特殊字符
        return preg_replace($regex, "", $strParam);
    }

    /**
     * 正值表达式比对解析$_SERVER['HTTP_USER_AGENT']中的字符串 获取访问用户的浏览器的信息
     *
     * @param string $Agent
     * @return array
     */
    public static function determineBrowser($Agent)
    {
        if (preg_match('/MSIE\s*[0-9\.]+/i', $Agent, $version) || preg_match('/Trident\s*\/\s*[0-9\.]+/i', $Agent, $version)) {
            $browserAgent = "Internet Explorer";//浏览器类型
        } else if (preg_match('/OPR\s*\/\s*[0-9\.]+/i', $Agent, $version)) {
            $browserAgent = "Opera";
        } else if (preg_match('/Firefox\s*\/\s*[0-9\.]+/i', $Agent, $version)) {
            $browserAgent = "Firefox";
        } else if (preg_match('/Chrome\s*\/\s*[0-9\.]+/i', $Agent, $version)) {
            $browserAgent = "chrome";
        } else if (preg_match('/Safari\s*\/\s*[0-9\.]+/i', $Agent, $version)) {
            $browserAgent = "safari";
        } else {
            $browserAgent = "Unknown";
        }

        return ['browserAgent' => $browserAgent, 'version' => $version];
    }

    /**
     * 判断浏览器类型
     *
     * @param string $userAgent user-agent
     * @return string
     */
    public static function theIeType($userAgent = '')
    {
        if (empty($userAgent)) $userAgent = Yii::$app->request->userAgent;
        if (empty($userAgent)) return '';
        if ((false == strpos($userAgent, 'MSIE')) && (strpos($userAgent, 'Trident') !== FALSE)) {
            return 'ie11';
        }
        if (false !== strpos($userAgent, 'MSIE 10.0')) {
            return 'ie10';
        }
        if (false !== strpos($userAgent, 'MSIE 9.0')) {
            return 'ie9';
        }
        if (false !== strpos($userAgent, 'MSIE 8.0')) {
            return 'ie8';
        }
        if (false !== strpos($userAgent, 'MSIE 7.0')) {
            return 'ie7';
        }
        if (false !== strpos($userAgent, 'MSIE 6.0')) {
            return 'ie6';
        }
        if (false !== strpos($userAgent, 'Edge')) {
            return 'edge';
        }
        if (false !== strpos($userAgent, 'Firefox')) {
            return 'firefox';
        }
        if (false !== strpos($userAgent, 'Chrome')) {
            return 'chrome';
        }
        if (false !== strpos($userAgent, 'Safari')) {
            return 'safari';
        }
        if (false !== strpos($userAgent, 'Opera')) {
            return 'opera';
        }
        if (false !== strpos($userAgent, '360SE')) {
            return '360se';
        }
        //微信浏览器
        if (false !== strpos($userAgent, 'MicroMessage')) {
            return 'micromessage';
        }
        return '';
    }

    /**
     * 生成uuid
     */
    public static function genUuid()
    {
        return md5(uniqid(rand(0, 100000), true) . rand(0, 100000));
    }

    /**
     * 对数据的值进行转换，根据需要转换为对应类型类型，并且去重。
     *
     * @author Ron  2017-07-17
     * @param array $dataArr
     * @param string $type
     * @return mixed
     */
    public static function getConvertArrayType($dataArr, $type = 'int')
    {
        if (empty($dataArr)) return array();
        //去重
        $dataArr = array_unique($dataArr);
        $ret = array();
        foreach ($dataArr as $key => $val) {
            if ($type == 'int') {
                array_push($ret, (int)$val);
            } elseif ($type == 'string') {
                array_push($ret, "'" . (string)$val . "'");
            } else {
                array_push($ret, $val);
            }

        }

        return $ret;
    }

    /**
     * 从二维数组中取出指定键的所有值   注意：此方法的返回结果不要加去重处理
     *
     * @author Ron 2017-10-30
     *
     * @param array $dataArr 二维数组数据
     * @param string $column 字段
     * @return array
     */
    public static function getValFromArray($dataArr, $column)
    {
        if (empty($dataArr) || empty($column)) return array();
        if (count($dataArr) == count($dataArr, COUNT_RECURSIVE)) return array();//判断如果是一维数据直接返回
        $keyVal = array();
        if (version_compare(PHP_VERSION, '5.5.0') >= 0) {
            $keyVal = array_column($dataArr, $column);
        } else {
            foreach ($dataArr as $key => $val) {
                if (array_key_exists($column, $val)) $keyVal[] = $val[$column];
            }
        }

        return $keyVal;
    }

    /**
     * 把二维数据中第一层的键，转换为二层数据中的主键的值
     *
     * @param array $dataArr 二维数组数据
     * @param string $column 数组的主键字段
     * @return array
     */
    public static function dealPrimaryKeyForArray($dataArr, $column)
    {
        if (empty($dataArr) || empty($column)) return [];
        if (count($dataArr) == count($dataArr, COUNT_RECURSIVE)) return $dataArr;//判断如果是一维数据直接返回

        $allData = [];
        foreach ($dataArr as $key => $val) {
            if (array_key_exists($column, $val)) {
                $allData[$val[$column]] = $val;
                unset($dataArr[$key]);
            }
        }
        return $allData;
    }

    /**
     * 判断一个数组的维数
     *
     * @param array $arrayData
     * @return int
     */
    public static function arrayDepth($arrayData)
    {
        $max_depth = 1;
        foreach ($arrayData as $value) {
            if (is_array($value)) {
                $depth = static::arrayDepth($value) + 1;
                if ($depth > $max_depth) {
                    $max_depth = $depth;
                }
            }
        }
        return $max_depth;
    }

    /**
     * 将xml文件内容解析成数组
     *
     * @param string $xmlPath 文件路径
     * @return array
     */
    public static function analysisXmlToArray($xmlPath)
    {
        if (file_exists($xmlPath)) { //判断文件是否存在
            $xmlContent = file_get_contents($xmlPath); //读取文件内容
            $xmlObj = simplexml_load_string($xmlContent); //读取xml文件内容，转成对象
            $xmlArray = array();
            //递归解析xml
            self::parseXml($xmlObj, $xmlArray);

            return $xmlArray;
        }
        return array();
    }

    /**
     * 解析xml文件
     *
     * @param object $xmlObj
     * @param array $xmlArray
     * @param bool $isFirst
     */
    public static function parseXml($xmlObj, &$xmlArray, $isFirst = false)
    {
        /** @var object $value */
        foreach ($xmlObj->children() as $key => $value) {
            if ($value->children()) {
                if (isset($xmlArray[$key])) {
                    if (!$isFirst) {
                        $isFirst = true;
                        $temp = $xmlArray[$key];
                        $xmlArray[$key] = array();
                        $xmlArray[$key][] = $temp;
                        self::parseXml($value, $xmlArray[$key][], $isFirst);
                    } else {
                        self::parseXml($value, $xmlArray[$key][], $isFirst);
                    }
                } elseif ($isFirst) {
                    $xmlArray[$key] = array();
                    self::parseXml($value, $xmlArray[$key][], $isFirst);
                } else {
                    $xmlArray[$key] = array();
                    self::parseXml($value, $xmlArray[$key], $isFirst);
                }
            } else {
                if (isset($xmlArray[$key])) {
                    if (count($xmlArray[$key]) < 2) {
                        $temp = $xmlArray[$key];
                        $xmlArray[$key] = array();
                        $xmlArray[$key][] = $temp;
                    }

                    $xmlArray[$key][] = (string)$value;
                } else {
                    $xmlArray[$key] = (string)$value;
                }
            }
        }
    }

    /**
     * 设置header信息--不缓存页面
     */
    public static function showHeader()
    {
        /**设置header信息--不缓存页面*/
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pramga: no-cache");
    }

    /**
     * 处理电话号码的国际区号
     *
     * @param string $mobile
     * @param string $delCode
     * @return bool|string
     */
    public static function processPhoneNo($mobile, $delCode)
    {
        $mobile = trim($mobile);
        $delCode = trim($delCode);
        if (!$delCode) {
            return $mobile;
        } else {
            $position = strpos($mobile, $delCode);
            if ($position !== false) {
                $mobile = substr($mobile, strlen($delCode));
            }
        }
        return $mobile;
    }

    /**
     * 处理手机号码，只保留数字，根据方向，返回指定的长度
     *
     * @param string $mobile 将要处理的手机号
     * @param int $length 要取的长度
     * @param int $position 取的方向  1从左到后，-1从右到左
     * @return bool|string
     */
    public static function processMobileNumber($mobile, $length, $position = 1)
    {
        $numbers = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
        $mobile = trim($mobile);
        if (!$mobile) return false;
        if ($length < 5) {
            return false;
        }
        $strLength = strlen($mobile);
        $newMobileStr = '';
        for ($i = 0; $i < $strLength; $i++) {
            $number = $mobile[$i];
            if (in_array($number, $numbers)) {
                $newMobileStr .= $number;
            }
        }
        $newMobileLength = strlen($newMobileStr);
        if ($length >= $newMobileLength) {
            $newMobile = $newMobileStr;
        } else {
            if ($position == 1) {
                $newMobile = substr($newMobileStr, 0, $length);
            } else {
                $newMobile = substr($newMobileStr, -$length);
            }
        }

        return $newMobile;
    }

    /**
     * 验证手机号码格式
     *
     * @author Ron 2018-01-31
     * @param string $phoneNumbers 手机号
     * @return boolean
     */
    public static function validateUserPhoneNumbers($phoneNumbers)
    {
        if (empty($phoneNumbers)) {
            return false;
        }
        //用正则验证密码信息是否合法
        $pattern = '/^[_0-9]{6,20}$/i';
        if (!preg_match($pattern, $phoneNumbers)) {
            return false;
        }
        return true;
    }

    /**
     * 打印内容
     *
     * @author Ron 2018-02-08
     * @param mixed $data 需要打印的内容
     * @return null
     */
    public static function pr($data)
    {
        echo '<pre>';
        print_r($data);
        echo '</pre>';
        exit;
    }

    /**
     * 页面跳转（controller以外的跳转调用此方法）
     *
     * @author Ron 2018-05-21
     * @param string $url 跳转url
     * @param int $statusCode 跳转状态码
     */
    public static function redirect($url, $statusCode = 302)
    {
        if ($statusCode == 301)
            header('HTTP/1.1 301 Moved Permanently');
        header('Location: ' . $url);
        exit();
    }

    /**
     * 离开当前页面，跳转到其他页面
     *
     * @author Ron 2018-10-16
     * @param string $url
     * @param int $statusCode
     * @param bool $checkAjax
     */
    public static function RedirectAway($url = '/404', $statusCode = 302, $checkAjax = true)
    {
        Yii::$app->getResponse()->redirect(Url::to($url), $statusCode, $checkAjax)->send();
        exit();
    }

    /**
     * 根据键值从二维数组中获取匹配到的相关值
     *
     * @author Ron 2018-04-23
     * @param $sourceArr array 源数组数据源
     * @param $filterKey string 获取数据的键
     * @return array
     */
    public static function getArrayValueByItem($sourceArr, $filterKey) {
        if (empty($sourceArr)) {
            return array();
        }
        $returnVal = array();
        foreach ($sourceArr as $sKey => $sVal) {
            if (isset($sVal[$filterKey]))
                $returnVal[] = $sVal[$filterKey];
        }
        return $returnVal;
    }

    /**
     * 重建数据索引(主要解决数组unset后传到前端变成对象的情况)
     *
     * @author Ron 2018-12-05
     * @param array $sArr 需要重新建索引的数组
     * @return array
    */
    public static function resetArrayIndex($sArr)
    {
        if (empty($sArr) || ! is_array($sArr)) return [];
        $resArr = [];
        foreach($sArr as $sKey => $sVal) {
            $resArr[] = $sVal;
        }

        return $resArr;
    }

    /**
     * 获取并过滤当前从前端传入参数(防注入)
     *
     * Ron 2018-09-07
     * @param string $parameterName 以get或post传入的参数名
     * @param string $defaultVal   当接收参数为空时返回的默认值
     * @param string $valType      接收参数的数据类型
     * @param boolean $filer	是否要过滤接收到的值，主要针对字符串
     * @return string       当接收参数为空时返回的默认值
     * return 返回相应数据类型的接收参数
     */
    public static function filterParameter($parameterName, $defaultVal = '', $valType = '', $filer = false) {
        // 接收GET或POST形式的参数值
        if (Yii::$app->request->isPost) {
            $parameterValue = Yii::$app->request->post($parameterName, $defaultVal);
        } elseif (Yii::$app->request->isGet) {
            $parameterValue = Yii::$app->request->get($parameterName, $defaultVal);
        } else {
            $parameterValue = $defaultVal;
        }
        // 如果未初始化，则返回默认值
        if (!isset($parameterValue) || empty($parameterValue)) {
            return $defaultVal;
        }
        // 如果未设置过滤值类型，则返回接收值
        if (empty($valType)) {
            return $parameterValue;
        }
        // 根据设置的过滤类型，完成收值的处理并返回
        switch($valType) {
            case 'string' :
                if ($filer && preg_match ("/select|insert|update|delete|\'|\/\*|\*|\.\.\/|\.\/|union|into|load_file|outfile|<|>|limit|where/i", $parameterValue)) {
                    //如果存在注入字段则返回默认值
                    return $defaultVal;
                } else {
                    return strval($parameterValue);
                }
                break;
            case 'int' :
                return (int)$parameterValue;
                break;
            case 'float' :
                return (float)$parameterValue;
                break;
            case 'boolean' :
                return (bool)$parameterValue;
                break;
            default:
                return $parameterValue;
        }
    }

    /**
     * 判断是否是http或https请求
     *
     * @author Ron 2018-05-07
     * @param string $url 检测url
     * @return boolean
    */
    public static function isHttpUrl($url)
    {
        $isHttp = false;
        if (empty($url)) return $isHttp;
        $parseUrl = parse_url($url);
        if (!empty($parseUrl) && isset($parseUrl['scheme']) && in_array($parseUrl['scheme'], PubFun::$schemes)) {
            $isHttp = true;
        }
        return $isHttp;
    }

    /**
     * 简单的字符串型防注入参数过滤
     *
     * @author Ron 2018-05-08
     * @param string $str 过滤字符串
     * @return string
    */
    public static function stringInjectCheck($str)
    {
        if (empty($str)) return '';
        $checkRes = preg_match('/select|insert|update|delete|\'|\\*|\*|\.\.\/|\.\/|union|into|load_file|outfile/i',$str);
        if ($checkRes) {
            //字符串含有注入元素，返回为空
            return '';
        } else {
            return $str;
        }
    }
    /**
     * 基于base64的加密方法
     *
     * @author Ron 2018-04-18
     * @param string $str
     * @return string
     */
    public static function base64EnCode($str){
        $baseStr = base64_encode($str);
        for($i = 0; $i < strlen($baseStr);$i++){
            if($i%2 == 1){
                $strValue = $baseStr{$i-1};
                $baseStr{$i-1} = $baseStr{$i};
                $baseStr{$i} = $strValue;
            }
        }
        return $baseStr;
    }

    /**
     * 针对方法base64EnCode 的解密方法
     *
     * @author Ron 2018-04-18
     * @param $code
     * @return string
     */
    public static function base64DeCode($code){

        for($i = 0; $i < strlen($code);$i++){
            if($i%2 == 1){
                $strValue = $code{$i-1};
                $code{$i-1} = $code{$i};
                $code{$i} = $strValue;
            }
        }
        $str = base64_decode($code);
        return $str;
    }

    /**
     * 判断是否全部为数字
     *
     * @author Ron 2018-09-21
     * @param string $str
     * @return bool
     */
    public static function isAllNumber($str)
    {
        $numbers = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
        if (!is_string($str)) return false;
        if (!$str) return false;
        $flag = true;
        $length = strlen($str);
        for ($i = 0; $i < $length; $i++) {
            if (!in_array($str[$i], $numbers)) {
                $flag = false;
                break;
            }
        }
        return $flag;
    }

    /**
     * 将文本里的价格替换成标准的html形式，便于货币切换
     *
     * @author Ron 2018-09-29
     * @param string $content 包含价格的文本内容
     * @return mixed
     */
    public static function replacePriceToHtml($content){
        $newContent = preg_replace('/\$([0-9]+(\.[0-9]+)?)/','<span class="goods-price" data-price="$1"></span>',$content);
        if($newContent){
            return $newContent;
        }
        return $content;
    }

    /**
     * 将数组的值全部转成整型（针对一维数组）
     *
     * @author Ron 2017-11-13   Ron 2018-12-18
     * @param $tarArr array 需要转换的数组
     * @return array
     */
    public static function transArrValueToInt($tarArr)
    {
        if (empty($tarArr) || count($tarArr) != count($tarArr, 1)) {
            return $tarArr;
        }
        $newArr = array();
        foreach ($tarArr as $key => $val) {
            $newArr[] = (int)$val;
        }
        return $newArr;
    }

    /**
     * 对多维护数组根据某个键值进行排序
     *
     * @author Ron 2017-10-26   Ron 2018-12-19
     * @param array $sourceMultiArray 源数组
     * @param string $key 需要进行排序数组中值对应的键
     * @param int $sortType 排序类型 注：SORT_ASC-升序  SORT_DESC-降序
     * @return array 排序后的数组
     */
    public static function getArrayMultiSort($sourceMultiArray, $key, $sortType = SORT_DESC)
    {
        //如果数组为空或是一维数组直接返回源数组
        if (empty($sourceMultiArray) || (count($sourceMultiArray) == count($sourceMultiArray, 1))) {
            return $sourceMultiArray;
        }
        $newTempArr = array_column($sourceMultiArray, $key);
        array_multisort($newTempArr, $sortType, $sourceMultiArray);
        return $sourceMultiArray;
    }

    /**
     * 将http的链接转成https的链接
     *
     * @author Ron 2017-9-29   Ron 2018-12-21
     * @param string $httpUrl 需求转换的http链接
     * @return string 转换后的http链接
     */
    public static function transHttpToHttps($httpUrl)
    {
        $baseUrl = Yii::$app->params['baseUrl'];
        $baseUrlInfo = parse_url($baseUrl);
        if (strtolower($baseUrlInfo['scheme']) == 'https') {
            $urlInfo = parse_url($httpUrl);
            if (strtolower($urlInfo['scheme']) != 'http') {
                return $httpUrl;
            } else {
                $scheme = $urlInfo['scheme'];
                return str_replace($scheme . '://', 'https://', $httpUrl);
            }
        }
        return $httpUrl;
    }

    /**
     * 获取当前的domain
     *
     * @author Ron 2010-01-29
    */
    public static function getCookieDoMain()
    {
        $domain = $_SERVER['SERVER_NAME'];
        if(strpos($domain,'com') > 0) {
            $domainArr = explode('.', $domain);
            $domain =  '.' . $domainArr[count($domainArr) - 2] . '.' . $domainArr[count($domainArr) - 1];
        }
        return $domain;
    }

    /**
     * 生成用户唯一标识Ucid
     * @author Ron 2019-03-21
     * @return string
     */
    public static function getUcid()
    {
        $ucidStr = '';
        $charStr = '0123456789ABCDEF';
        for ($i = 0; $i < 36; $i++) {
            //从源字符集里面随机取出任意一个字符
            $charNum = rand(0, strlen($charStr) - 1);
            $charCoookie = $charStr{$charNum};
            //指定的位置需要替换成-
            if (in_array($i, [8, 13, 18, 23])) {
                $charCoookie = '-';
            }
            $ucidStr .= $charCoookie;
        }
        return $ucidStr;
    }

    /**
     * 字符截取（可以结合 checkStrLanguage() 来使用）
     *
     * @author Ron 2018-05-30
     * @param string $str 字符串
     * @param int $len 截取长度
     * @param string $char 后缀
     * @return string
     */
    public static function cutStr($str, $len, $char = '...')
    {
        $wordNum = 0;
        $arr = preg_split('/(?<!^)(?!$)/u', $str);
        for ($i = 0; $i < $len; $i++) {
            $ch = ord($arr[$i]);
            //如果是字符那么要补一位宽度
            if ($ch < 127) {
                $wordNum = $wordNum + 1;
            }
        }
        $len = $wordNum + $len;
        if (mb_strlen($str, 'utf-8') > $len) {
            return mb_substr($str, 0, $len, 'utf-8') . $char;
        } else {
            return $str;
        }
    }

    /**
     * 创建目录
     *
     * @param string $dir 路径
     * @param bool $isRootPath 是否是绝对路径
     * @return boolean
     */
    public static function mkdir($dir, $isRootPath = true)
    {
        if (file_exists($dir)) {
            return true;
        }
        $dirArr = explode('/', rtrim($dir, '/'));
        $tmp = $isRootPath ? '/' : '';
        foreach ($dirArr as $sub) {
            $tmp .= $sub . '/';
            if (!file_exists($tmp)) {
                @mkdir($tmp, 0777);
            }
        }
        return true;
    }

}
