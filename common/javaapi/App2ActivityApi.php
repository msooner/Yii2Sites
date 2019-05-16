<?php
/**
 * 对接跟活动相关的所有接口
 *
 * User: Ron
 * Date: 18/7/23
 * Time: 下午4:58
 */

namespace common\javaapi;

use common\components\PubFun;
use common\components\Utils;
use common\javaapi\core\App2Base;

class App2ActivityApi extends App2Base{

    /**
     * xxx活动-获取用户信息
     *
     * @author Ron 2018-07-23
     * @return array|mixed
     */
    public function getMainInfo()
    {
        $this->_getParams();
        ksort($this->_params);
        $resContentsJson = $this->_requestForGet('app.users');
        if (empty($resContentsJson)) {
            return $this->_returnData();
        }
        return $resContentsJson;
    }
}
