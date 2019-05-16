<?php
/**
 * User: Ron
 * Date: 2017/11/24 下午3:29
 * To change this template use File | Settings | File Templates.
 */

namespace common\extend\oauth\sdk;

use common\components\PubFun;
use common\extend\oauth\ThinkOauth;
use common\components\LogManage;
use yii\db\Exception;
use yii\queue\PushEvent;

class GoogleSDK extends ThinkOauth {

    /**
     * 获取requestCode的api接口
     *
     * @var string
     */
    protected $GetRequestCodeURL = 'https://accounts.google.com/o/oauth2/v2/auth';

    /**
     * 获取access_token的api接口
     *
     * @var string
     */
    protected $GetAccessTokenURL = 'https://www.googleapis.com/oauth2/v4/token';

    /**
     * 获取request_code的额外参数 URL查询字符串格式
     *
     * @var string
     */
    protected $Authorize = 'scope=https://www.googleapis.com/auth/userinfo.profile https://www.googleapis.com/auth/userinfo.email';

    /**
     * API根路径
     *
     * @var string
     */
    protected $ApiBase = 'https://www.googleapis.com/oauth2/v1/';

    /**
     * 组装接口调用参数 并调用接口
     *
     * @param  string $api 微博API
     * @param  string $param 调用API的额外参数
     * @param  string $method HTTP请求方法 默认为GET
     * @param  bool $multi
     * @return array
     * @throws \Exception
     */
    public function call($api, $param = '', $method = 'GET', $multi = false)
    {
        /* Google 调用公共参数 */
        $params = array();
        $header = array("Authorization: Bearer {$this->Token['access_token']}");
        $data = $this->http($this->url($api), $this->param($params, $param), $method, $header);

        return json_decode($data, true);
    }

    /**
     * 解析access_token方法请求后的返回值
     *
     * @param string $result 获取access_token的方法的返回值
     * @param mixed $extend
     * @return mixed
     * @throws \Exception
     */
    protected function parseToken($result, $extend)
    {
        $LogManage = new LogManage();
        $LogManage->writeLogMsg('google token返回:' . var_export($result, true), $this->_pathSign);
        $data = json_decode($result, true);
        if (isset($data['access_token']) && $data['access_token'] && $data['token_type'] && $data['expires_in']) {
            $this->Token = $data;
            $data['openid'] = $this->openid();
            return $data;
        } else {
            (new LogManage())->writeLogMsg("获取 Google ACCESS_TOKEN出错：未知错误", $this->_pathSign);
            //throw new \Exception("获取 Google ACCESS_TOKEN出错：未知错误");
        }
    }

    /**
     * 获取当前授权应用的openid
     *
     * @return mixed
     * @throws \Exception
     */
    public function openid()
    {
        if (isset($this->Token['openid']))
            return $this->Token['openid'];
        $data = $this->call('userinfo');
        $LogManage = new LogManage();
        $LogManage->writeLogMsg('google用户信息：' . var_export($data, true), $this->_pathSign);
        if (!empty($data['id'])) {
            return $data['id'];
        } else {
            $LogManage->writeLogMsg('没有获取到 Google 用户ID！', $this->_pathSign);
            throw new \Exception('没有获取到 Google 用户ID！');
        }
    }

    /**
     * 请求code
     * @return string
     * @throws \Exception
     */
    public function getRequestCodeURL()
    {
        $this->config();
        //Oauth 标准参数
        $params = array(
            'client_id' => $this->AppKey,
            'redirect_uri' => $this->Callback,
            'response_type' => $this->ResponseType,
            'state' => 'site',
            'scope' => $this->Authorize
        );
        /*
        //获取额外参数
        if ($this->Authorize) {

            parse_str($this->Authorize, $_param);
            if (is_array($_param)) {
                $params = array_merge($params, $_param);
            } else {
                (new LogManage())->writeLogMsg('AUTHORIZE配置不正确！', $this->_pathSign);
                throw new \Exception('AUTHORIZE配置不正确！');
            }
        }*/
        //var_dump($this->GetRequestCodeURL . '?' . http_build_query($params));exit;
        return $this->GetRequestCodeURL . '?' . http_build_query($params);
    }

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
            'grant_type' => $this->GrantType,
            'code' => $code,
            'redirect_uri' => $this->Callback,
        );
        $data = $this->http($this->GetAccessTokenURL, $params, 'POST');
        try {
            $this->Token = $this->parseToken($data, $extend);
        }catch (Exception $e) {

        }
        return $this->Token;
    }

    public function setToken($token) {
        $this->Token = $token;
        return true;
    }

}