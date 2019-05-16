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

class FacebookSDK extends ThinkOauth {

    /**
     * 获取requestCode的api接口
     *
     * @var string
     */
    protected $GetRequestCodeURL = 'https://www.facebook.com/v2.12/dialog/oauth';

    /**
     * 获取access_token的api接口
     *
     * @var string
     */
    protected $GetAccessTokenURL = 'https://graph.facebook.com/v2.12/oauth/access_token';

    /**
     * 获取request_code的额外参数 URL查询字符串格式
     *
     * @var string
     */
    protected $Authorize = 'scope=email,user_likes,publish_actions';

    /**
     * API根路径
     *
     * @var string
     */
    protected $ApiBase = 'https://graph.facebook.com/v2.12/';

    /**
     * @var array 不同接口获取数据对应的字段。
     */
    protected $allApiFieldData = [
        'me' => 'id,cover,name,first_name,last_name,age_range,link,gender,locale,picture,timezone,updated_time,verified,email'
    ];

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
        /* facebook 调用公共参数 */
        $params = array('access_token' => $this->Token['access_token']);
        if(isset($this->allApiFieldData[$api])) {
            $params['fields'] = $this->allApiFieldData[$api];
        }
        $header = array();
        $data = $this->http($this->url($api), $this->param($params, $param), $method, $header);
        return json_decode($data, true);
    }

    /**
     * 解析access_token方法请求后的返回值
     *
     * @param string $result 获取access_token的方法的返回值
     * @param mixed $extend
     * @return array|mixed
     * @throws \Exception
     */
    protected function parseToken($result, $extend)
    {
        $LogManage = new LogManage();
        $LogManage->writeLogMsg('facebook token返回:' . var_export($result, true), $this->_pathSign);
        parse_str($result, $data);
        if (is_array($data) && isset($data['access_token']) && isset($data['expires'])) {
            $this->Token = $data;
            $data['openid'] = $this->openid();
            return $data;
        } else {
            $jsonData = json_decode($result,true);
            if ($jsonData && isset($jsonData['access_token']) && !empty($jsonData['access_token'])) {
                $this->Token = $jsonData;
                $jsonData['openid'] = $this->openid();
                return $jsonData;
            } else {
                $LogManage->writeLogMsg("获取 facebook ACCESS_TOKEN出错：{$result}", $this->_pathSign);
            }
        }
        return [];
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
        $data = $this->call('me');
        $LogManage = new LogManage();
        $LogManage->writeLogMsg('facebook用户信息：' . var_export($data, true), $this->_pathSign);
        if (!empty($data['id'])) {
            return $data['id'];
        } else {
            $LogManage->writeLogMsg('没有获取到 facebook 用户ID！', $this->_pathSign);
            throw new \Exception('没有获取到 facebook 用户ID！');
        }
    }

    public function setToken($token) {
        $this->Token = $token;
        return true;
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
            'state' => 'snslogin'
        );
        //获取额外参数
        if ($this->Authorize) {
            parse_str($this->Authorize, $_param);
            if (is_array($_param)) {
                $params = array_merge($params, $_param);
            } else {
                (new LogManage())->writeLogMsg('AUTHORIZE配置不正确！', $this->_pathSign);
                throw new \Exception('AUTHORIZE配置不正确！');
            }
        }
        return $this->GetRequestCodeURL . '?' . http_build_query($params);
    }


}