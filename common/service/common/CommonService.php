<?php
/**
 * User: Ron
 * Date: 2018/11/01 下午12:49
 * 公共有关的业务
 */

namespace common\service\common;

use common\components\{VarCache, CookiesSite1, Language, PubFun};
use common\service\core\BaseService;
use Yii;

class CommonService extends BaseService {

    /**
     * 处理 QUERY_STRING 数据
     *
     * @return array
     */
    public function getQueryString()
    {
        $queryStringParam = array();
        $urlQueryString = isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'] ? $_SERVER['QUERY_STRING'] : '';
        if ($urlQueryString) {
            parse_str($urlQueryString, $queryStringParam);
        }
        return $queryStringParam;
    }

    /**
     * 设置全局变量：当前URL，主要用于登录成功后返回
     *
     * @author Ron 2019-05-14
     */
    public function getThisUrl()
    {
        $thisUrl = '';
        //过滤get请求参数
        foreach ($_GET as $gKey => $gVal) {
            if (empty($gVal)) {
                unset($_GET[$gKey]);
            }
        }
        //不需要返回的情况
        $ignoreCase = [];
        if (! empty($this->yiiPathInfo) && ! in_array(Yii::$app->controller->action->id, $ignoreCase)) {
            $thisUrl = base64_encode('/' . $this->yiiPathInfo . (!empty($_GET) ? http_build_query($_GET) : ''));
        }
        return $thisUrl;
    }

    public function checkUserIsRobot($googleToken)
    {
        $otherJavaApi = new OtherJavaApi();
        $googleResult = $otherJavaApi->getGooogleRECAPTCHAResponse(['response' => $googleToken]);
        $googleScoreConfig =  Yii::$app->params['googleScore'];
        if($googleResult['score'] >= $googleScoreConfig) {
            return false;
        } else {
            return true;
        }
    }

}

