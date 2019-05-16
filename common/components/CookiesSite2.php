<?php
/**
 * User: Ron
 * Date: 2017/10/26 下午3:23
 * markavip 项目中的cookies处理
 */

namespace common\components;

use Yii;

class CookiesSite2 extends CookiesBase {

    /**
     * 获取语言的cookie
     *
     * @return mixed
     */
    public static function getLanguage()
    {
        return static::getYiiCookie('language');
    }

    /**
     * 设置语言的cookie
     *
     * @param string $language
     * @return mixed
     */
    public static function setLanguage($language)
    {
        return static::setYiiCookie('language', strtoupper($language), Yii::$app->params['cacheExpire.oneHour']);
    }

}