<?php
// +----------------------------------------------------------------------
// | TOPThink [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2010 http://topthink.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi.cn@gmail.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------
// | ThinkOauth.class.php 2013-02-25
// +----------------------------------------------------------------------

namespace common\extend\oauth;

use common\components\LogManage;
use common\components\PubFun;
use common\extend\oauth\sdk\FacebookSDK;
use common\extend\oauth\sdk\GoogleSDK;

abstract class ThinkOauth {
    /**
     * oauth版本
     *
     * @var string
     */
    protected $Version = '2.12';

    /**
     * 申请应用时分配的app_key
     *
     * @var string
     */
    protected $AppKey = '';

    /**
     * 申请应用时分配的 app_secret
     *
     * @var string
     */
    protected $AppSecret = '';

    /**
     * 授权类型 response_type 目前只能为code
     *
     * @var string
     */
    protected $ResponseType = 'code';

    /**
     * grant_type 目前只能为 authorization_code
     *
     * @var string
     */
    protected $GrantType = 'authorization_code';

    /**
     * 回调页面URL  可以通过配置文件配置
     *
     * @var string
     */
    protected $Callback = '';

    /**
     * 获取request_code的额外参数 URL查询字符串格式
     *
     * @var string
     */
    protected $Authorize = '';

    /**
     * 获取request_code请求的URL
     *
     * @var string
     */
    protected $GetRequestCodeURL = '';

    /**
     * 获取access_token请求的URL
     *
     * @var string
     */
    protected $GetAccessTokenURL = '';

    /**
     * API根路径
     *
     * @var string
     */
    protected $ApiBase = '';

    /**
     * 授权后获取到的TOKEN信息
     *
     * @var array
     */
    protected $Token = null;

    /**
     * 调用接口类型
     *
     * @var string
     */
    private $Type = '';

    /**
     * 第三方授权的配置
     *
     * @var array
     */
    protected $configOath = [];

    protected $_pathSign = 1;//日志目录标识

    /**
     * 构造方法，配置应用信息
     *
     * @param null|array $token
     * @throws \Exception
     */
    public function __construct($token = null)
    {
        //设置SDK类型
        $class = get_class($this);
        $this->Type = strtoupper(substr($class, 24, (strlen($class) - 27)));
        //获取应用配置
        $configOath = require(__DIR__ . '/../../../common/config/' . YII_ENV . '/oauth.php');
        $this->configOath = $configOath[YII_APP_NAME];
        $config = [];
        if (array_key_exists("OAUTH_SDK_{$this->Type}", $this->configOath))
            $config = $this->configOath["OAUTH_SDK_{$this->Type}"];
        if (empty($config['APP_KEY']) || empty($config['APP_SECRET'])) {
            throw new \Exception('请配置您申请的APP_KEY和APP_SECRET');
        } else {
            $this->AppKey = $config['APP_KEY'];
            $this->AppSecret = $config['APP_SECRET'];
            $this->Token = $token;//设置获取到的TOKEN
            $this->Callback = $config['CALLBACK'];
        }
    }

    /**
     * 取得Oauth实例
     *
     * @param string $type
     * @param string|null $token
     * @return mixed 返回Oauth
     * @throws \Exception
     */
    public static function getInstance($type, $token = null)
    {
        if (strtolower($type) == 'facebook') {
            return new FacebookSDK($token);
        } elseif (strtolower($type) == 'google') {
            return new GoogleSDK($token);
        } else {
            $name = ucfirst(strtolower($type)) . 'SDK';
            (new LogManage())->writeLogMsg($name . ' 类不存在', 1);
            throw new \Exception($name . ' 类不存在');
        }

        /*$name = ucfirst(strtolower($type)) . 'SDK';
        if (class_exists($name)) {
            return new $name($token);
        } else {
            (new LogManage())->writeLogMsg($name . ' 类不存在', 1);
            throw new \Exception($name . ' 类不存在');
        }*/
    }

    /**
     * 初始化配置
     * @throws \Exception
     */
    protected function config()
    {
        $config = [];
        if (array_key_exists("OAUTH_SDK_{$this->Type}", $this->configOath)) {
            $config = $this->configOath["OAUTH_SDK_{$this->Type}"];
        }
        if (!empty($config['AUTHORIZE']))
            $this->Authorize = $config['AUTHORIZE'];
        if (!empty($config['CALLBACK']))
            $this->Callback = $config['CALLBACK'];
        else {
            (new LogManage())->writeLogMsg('请配置回调页面地址', $this->_pathSign);
            throw new \Exception('请配置回调页面地址');
        }
    }


    abstract protected function getRequestCodeURL();

        /**
     * 获取access_token
     *
     * @param string $code 上一步请求到的code
     * @param null $extend
     * @return array|mixed|null
     * @throws \Exception
     */
    public function getAccessToken($code, $extend = null)
    {
        $this->config();
        $params = array(
            'client_id' => $this->AppKey,
            'client_secret' => $this->AppSecret,
            'code' => $code,
            'redirect_uri' => $this->Callback,
        );
        $data = $this->http($this->GetAccessTokenURL, $params, 'GET');
        $this->Token = $this->parseToken($data, $extend);
        return $this->Token;
    }

    /**
     * 合并默认参数和额外参数
     *
     * @param array $params 默认参数
     * @param array /string $param 额外参数
     * @return array
     */
    protected function param($params, $param)
    {
        if (is_string($param))
            parse_str($param, $param);
        return array_merge($params, $param);
    }

    /**
     * 获取指定API请求的URL
     *
     * @param string $api API名称
     * @param string $fix api后缀
     * @return string 请求的完整URL
     */
    protected function url($api, $fix = '')
    {
        return $this->ApiBase . $api . $fix;
    }

    /**
     * 发送HTTP请求方法，目前只支持CURL发送请求
     *
     * @param string $url 请求URL
     * @param array $params 请求参数
     * @param string $method 请求方法GET/POST
     * @param array $header http头
     * @param bool $multi 判断是否传输文件
     * @return array|mixed 响应数据
     * @throws \Exception
     */
    protected function http($url, $params, $method = 'GET', $header = array(), $multi = false)
    {
        $opts = array(
            CURLOPT_TIMEOUT => 30,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTPHEADER => $header
        );

        /* 根据请求类型设置特定参数 */
        switch (strtoupper($method)) {
            case 'GET':
                $opts[CURLOPT_URL] = $url . '?' . http_build_query($params);
                break;
            case 'POST':
                //判断是否传输文件
                $params = $multi ? $params : http_build_query($params);
                $opts[CURLOPT_URL] = $url;
                $opts[CURLOPT_POST] = 1;
                $opts[CURLOPT_POSTFIELDS] = $params;
                break;
            default:
                throw new \Exception('不支持的请求方式！');
        }

        /* 初始化并执行curl请求 */
        $ch = curl_init();
        curl_setopt_array($ch, $opts);
        $data = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);
        if ($error) {
            (new LogManage())->writeLogMsg('请求发生错误：' . $error, $this->_pathSign);
            throw new \Exception('请求发生错误：' . $error);
        }
        return $data;
    }

    /**
     * 抽象方法，在SNSSDK中实现
     * 组装接口调用参数 并调用接口
     *
     * @param string $api
     * @param string $param
     * @param string $method
     * @param bool $multi
     * @return mixed
     */
    abstract protected function call($api, $param = '', $method = 'GET', $multi = false);

    /**
     * 抽象方法，在SNSSDK中实现
     * 解析access_token方法请求后的返回值
     *
     * @param string $result
     * @param mixed $extend
     * @return mixed
     */
    abstract protected function parseToken($result, $extend);

    /**
     * 抽象方法，在SNSSDK中实现
     * 获取当前授权用户的SNS标识
     *
     * @return mixed
     */
    abstract public function openid();


}