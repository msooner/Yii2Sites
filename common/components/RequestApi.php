<?php
/**
 * User: Ron
 * Date: 2017/09/22 上午11:01
 * 对外发出api请求类
 */

namespace common\components;

class RequestApi {
    /**
     * 以curl形式发送API接口请口
     *
     * @param string $url 接口地址
     * @param array $params 接口参数
     * @param string $type 接口请求方式get、post等
     * @param array $headers 请求header
     * @param int $timeout 请求接口的过期时间
     * @param string $reqType 日志类型
     * @param bool $getHttpCode 是否获取http code
     * @param array $replaceLogParams 写日志时，$params中要替换掉的内容
     * @return array|mixed
     *
     * 注意事项:
     * java很多接口使用raw方式获取数据,这种的时候改用以下方式发送请求
     * RequestApi::sendApiRequest('http://192.168.3.38:84/goods/getShareUrl.do',array(json_encode(array('id' => 34323,'type' => 0))),'POSTSTRING',array('Content-Type: text/raw'));
     * 如果请求参数里面加上了isApp2Api 并且值不为空，则可以使用下面的形式
     * RequestApi::sendApiRequest('http://192.168.3.38:84/goods/getShareUrl.do',array('id' => 34323,'type' => 0,'isApp2Api'=>1),'POSTSTRING',array('Content-Type: text/raw'));
     */
    public static function sendApiRequest($url, $params = array(), $type = 'GET', $headers = array(), $timeout = 20, $reqType = 'curlLog', $getHttpCode = false, $replaceLogParams = array())
    {
        //添加跟踪接口日志
        $paramsLogs = $params;
        /** @var mixed $params */
        if (!empty($params) && !PubFun::is_not_json($params)) {
            $paramsLogs = json_decode($params, true);
        }
        $paramsLogs = self::_replaceArrToArr($paramsLogs, $replaceLogParams);
        if ($type == 'GET') {
            if ($params) {
                foreach ($params as $paramKey => $paramVal) {
                    $paramArr[] = $paramKey . '=' . $paramVal;
                }
                /** @var array $paramArr */
                $url = $url . '?' . implode('&', $paramArr);
            }
            $url = str_replace(' ', '%20', $url);
        } elseif ($type == 'POSTSTRING') {
            //如果提交方式是POSTSTRING,则参数需要转换为json
            $params = array(json_encode($params, true));
        }

        //记录请求日志
        $logObj = new LogManage();
        $logObj->_processStartTime = PubFun::getMillisecondInt();
        $logObj->writeLog(array('method' => $type, 'requestType' => 1, 'requestData' => $paramsLogs, 'requestUrl' => $url, 'retMsg' => $reqType, 'userAgent' => ''));
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        $headers[] = 'HTTP_PCM_USER_IP:'.PubFun::getIP();
        $requestApp2TokenConfig = \Yii::$app->params['requestApp2TokenConfig'];
        if($requestApp2TokenConfig) {
            $headers[] = $requestApp2TokenConfig['key'].':'.$requestApp2TokenConfig['value'];
        }
        if ($headers) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_NOSIGNAL, true);               //注意，毫秒超时一定要设置这个
        curl_setopt($ch, CURLOPT_TIMEOUT_MS, $timeout * 1000);   //超时时间200毫秒
        switch ($type) {
            case "GET" :
                curl_setopt($ch, CURLOPT_HTTPGET, true);
                break;
            case "POST":
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
                break;
            case "POSTBUILD":
                curl_setopt($ch, CURLOPT_POST,true);
                curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($params));
                break;
            case "POSTSTRING":
                curl_setopt($ch, CURLOPT_POST, true);
                $postDataStr = implode('&', $params);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postDataStr);
                break;
            case "PUT" :
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
                break;
            case "DELETE":
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
                curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
                break;
        }
        $result = curl_exec($ch);
        if ($getHttpCode) {
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        }
        curl_close($ch);

        //记录响应日志
        $logObj->writeLog(array('method' => $type, 'requestType' => 2, 'requestData' => $paramsLogs, 'requestUrl' => $url, 'retMsg' => $reqType, 'userAgent' => '', 'retData' => $result));

        if ($getHttpCode) {
            /** @var int $httpCode */
            return array(
                'status' => $httpCode,
                'data' => $result
            );
        } else {
            return $result;
        }
    }

    /**
     * 传递一个数组以替换目标数组相同键名的值
     *
     * @author Ron 2017-03-29
     * @param array $arr 需要被替换的数组
     * @param array $arrReplace 替换的数组
     * @return array 替换后的新数组
     */
    private static function _replaceArrToArr($arr, $arrReplace)
    {
        if (!is_array($arrReplace) || empty($arrReplace)) {
            return $arr;
        }
        foreach ($arrReplace as $k => $v) {
            if (isset($arr[$k])) {
                $arr[$k] = $v;
            }
        }
        return $arr;
    }

}