<?php
/**
 * User: Ron
 * Date: 2017/10/31 下午2:41
 * 时间处理有关的业务
 */

namespace common\service\common;

use common\service\core\BaseService;

class TimeService extends BaseService {

    /**
     * 返回两个时区的时差，单位为秒
     *
     * @author Ron 2017-6-21
     *
     * @param int $remote_tz 时间
     * @param string $origin_tz 时区
     * @return bool|int
     */
    public function getTimeZoneOffset($remote_tz, $origin_tz = 'Asia/Chongqing')
    {
        if (empty($remote_tz)) {
            $remote_tz = 'Asia/Chongqing';
        }
        if ($origin_tz === null) {
            //如果时区没有配置则返回false
            if (!is_string($origin_tz = date_default_timezone_get())) {
                return false;
            }
        }
        $origin_dtz = new \DateTimeZone($origin_tz);
        $remote_dtz = new \DateTimeZone($remote_tz);
        $origin_dt = new \DateTime("now", $origin_dtz);
        $remote_dt = new \DateTime("now", $remote_dtz);
        $offset = $origin_dtz->getOffset($origin_dt) - $remote_dtz->getOffset($remote_dt);

        return $offset;
    }

}