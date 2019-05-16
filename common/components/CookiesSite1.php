<?php
/**
 * User: Ron
 * Date: 2017/10/26 下午3:07
 * 项目中的cookies处理
 * 注意：设置cookie后，刷新页面全局$_COOKIE中才有相应的值。
 */

namespace common\components;

use Yii;

class CookiesSite1 extends CookiesBase {

    /**
     * 获取唯一ID的cookie
     *
     * @return mixed
     */
    public static function getUsers()
    {
        return static::getYiiCookie('Users');
    }

    /**
     * 设置唯一ID的cookie
     *
     * @param int $uuid
     * @param int $timeExpire 时间
     * @return mixed
     */
    public static function setUsers($uuid, $timeExpire = -1)
    {
        if ($timeExpire == -1) $timeExpire = Yii::$app->params['cacheExpire.twoHours'];
        static::setYiiCookie('Users', $uuid, $timeExpire);
        return static::setCookieNowEffective('Users', $uuid, 1);
    }

}
