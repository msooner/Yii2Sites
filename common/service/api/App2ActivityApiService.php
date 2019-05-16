<?php
/**
 * 活动相关的API对接：主要对接活动相关的接口
 *
 * User: Ron
 * Date: 18/7/23
 * Time: 下午4:53
 */

namespace common\service\api;

use common\components\PubFun;
use common\service\core\BaseService;
use common\javaapi\App2ActivityApi;

class App2ActivityApiService extends BaseService {


    /**
     * 动物饲养活动-主页
     *
     * @author Ron 2018-07-23
     * @return array
     */
    public function getMainInfo()
    {
        $mainInfo = (new App2ActivityApi())->getMainInfo();
        if ($mainInfo['result'] === 0) {
            return [ 'res' => true, 'data' => $mainInfo, 'msg' => 'Success!'];
        } else {
            return [ 'res' => false, 'data' => [], 'msg' => $mainInfo['message']];
        }
    }
}
