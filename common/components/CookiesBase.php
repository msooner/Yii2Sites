<?php
/**
 * User: Aiden
 * Date: 2017/09/25 下午2:33
 * 自定义处理cookie的类
 * 注意：
 * 1、Cookie的key不能含“.”符号，否则会被解析成“_”。会导致具有以上符号的key获取不到Cookie的bug。
 * 2、Yii2中通过 Yii::$app->request->cookies 来取、判断cookie 和 通过 Yii::$app->response->cookies 来设置、删除cookie。
 * 3、此类的添加、查询、判断、删除cookie的方法基本覆盖了所有常用的cookie操作。若需要更复杂的处理cookie的方法，在本类后面使用这些方法扩展。
 */

namespace common\components;

use Yii;
use yii\web\Cookie;

class CookiesBase {

    /**
     * 设置cookie
     *
     * @param string $name cookie的键
     * @param mixed $value cookie的值
     * @param int $expire cookie的过期时间。如果设置为0，或省略，该Cookie将在浏览器关闭时消失。
     * @param string $domain cookie的域。为空时，cookie的域带www。
     * @param string $path 目录
     * @param bool $httpOnly 是否可通过js读取cookie。true:无法通过js读取cookie，反之可以。
     * @return mixed
     */
    public static function setYiiCookie($name, $value, $expire = 0, $domain = '', $path = '/', $httpOnly = false)
    {
        $cookie = new Cookie();
        $cookie->name = $name;//cookie的名称
        $cookie->value = $value;//cookie的值
        $cookie->expire = ($expire > 0) ? time() + $expire : 0;//存活的时间。如果设置为0，或省略，该Cookie将在浏览器关闭时消失。
        $cookie->domain =  self::setDomainCookie($domain);//为空时，cookie的域带www
        $cookie->path = $path;
        $cookie->httpOnly = $httpOnly;//true:无法通过js读取cookie，反之可以
        $ret = Yii::$app->response->cookies->add($cookie);
        return $ret;
    }

    /**
     * 查询cookie
     *
     * @author Ron 2019-03-14 ：兼容获取需要立即生效的cookie值
     * @param string $name cookie的键
     * @param string $default 默认值
     * @param boolean $isNowEffective 是否需要立即生效的cookie值
     * @return mixed
     */
    public static function getYiiCookie($name, $default = '', $isNowEffective = false)
    {
        $cookieVal = Yii::$app->request->cookies->getValue($name, $default);
        if ($isNowEffective || empty($cookieVal) || $cookieVal == 'deleted') {
            $nowCookieVal = static::getCookieNowEffective($name);
            if (!empty($nowCookieVal)) $cookieVal = $nowCookieVal;
        }
        return  $cookieVal;
    }

    /**
     * 查询cookie的对象
     *
     * @param string $name cookie的键
     * @return object
     */
    public static function getYiiCookieObj($name)
    {
        $cookieObj = Yii::$app->request->cookies->get($name);
        if($cookieObj) {
            return $cookieObj;
        }
        $cookieObj = Yii::$app->response->cookies->get($name);
        return $cookieObj;
    }

    /**
     * 判断cookie是否存在
     *
     * @param string $name cookie的键
     * @return bool
     */
    public static function isHasYiiCookie($name)
    {
        $isCookie = Yii::$app->request->cookies->has($name);
        return $isCookie;
    }

    /**
     * 删除cookie
     *
     * @param string $name cookie的键
     * @return bool
     */
    public static function removeYiiCookie($name)
    {
        Yii::$app->response->cookies->remove($name);
        return true;
    }

    /**
     * 处理全局的cookie，让设置后的cookie能马上使用，不需要刷新页面
     *
     * @param string $name 键
     * @param string|int $value 值
     * @param int $must 是否必须处理
     */
    public static function setCookieNowEffective($name, $value, $must = 0)
    {
        if (!array_key_exists($name, $_COOKIE) || empty($_COOKIE[$name]) || $must) {
            //为了即时使用cookie
            $_COOKIE[$name] = $value;
        }
    }

    /**
     * 获取全局立马能使用的cookie
     *
     * @author Ron 2019-03-14
     * @param string $name 键
     * @return string
    */
    public static function getCookieNowEffective($name)
    {
        return (isset($_COOKIE[$name]) && ! empty($_COOKIE[$name])) ? $_COOKIE[$name] : '';
    }

    /**
     * 处理全局的domain cookie
     *
     * @param string $domain 域
     * @return mixed
     */
    public static function setDomainCookie($domain = '')
    {
        if (YII_APP_NAME == 'site1' && empty($domain)) {
            $getDomain = PubFun::getConfParam('setDomain');
            if (empty($getDomain)) return '';
            if (YII_ENV == 'test') {
                $domain = $getDomain['testDomain'];
            } elseif (YII_ENV == 'prod') {
                $domain = $getDomain['prodDomain'];
            } else {
                $domain = $getDomain['devDomain'];
            }
        }
        return $domain;
    }

    /**
     * 清除所有cookie信息
     *
     * @author Ron 2018-11-29
    */
    public static function cleanAllCookies()
    {
        $past = time() - 3600;
        foreach ($_COOKIE as $key => $value) {
            if ($key != '__icc')
                setcookie($key, '', $past);
        }
    }


}