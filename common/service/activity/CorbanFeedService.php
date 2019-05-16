<?php
/**
 * User: Ron
 * Date: 18/7/23
 * Time: 上午10:30
 *
 * 通用活动页面
 */

namespace common\service\activity;

use Yii;
use common\service\core\BaseService;
use common\service\api\App2ActivityApiService;
use common\service\api\App2CouponApiService;

class CorbanFeedService  extends BaseService {

    /**
     * 获取用户信息
     *
     * @param int $userId 用户ID
     * @return array
    */
    public function getUsersInfo($userId){
        if (empty($userId)) {
            return [ 'res' => false, 'data' => [], 'msg' => 'The user is not logged in!'];
        }
        //请求主页接口数据
        $mainInfo = (new App2ActivityApiService())->getMainInfo();
        if ($mainInfo['res']) {
            //封装用户信息
            $result = $mainInfo['data'];
            $result['mainVenue'] = $this->actionMainVenue();
            $result['userToken'] = base64_encode($userId);
            return [ 'res' => true, 'data' => $result, 'msg' => $mainInfo['msg']];
        } else {
            return [ 'res' => false, 'data' => [], 'msg' => $mainInfo['msg']];
        }
    }
}