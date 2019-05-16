<?php
/**
 * User: Ron
 * Date: 2017/11/21 下午3:37
 * 处理数据对象缓存，减少在一个请求中相同数据的重复调用。
 */

namespace common\components;

class VarCache {

    private static $_instance = null;

    public static $_data = [];

    public function getInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public static function set($key, $data)
    {
        if ($data == null) {
            unset(self::$_data[$key]);
        } else {
            self::$_data[$key] = $data;
        }
        return true;
    }

    public static function get($key)
    {
        if (isset(self::$_data[$key])) {
            return self::$_data[$key];
        }
        return null;
    }

    public static function add($key, $data)
    {
        if (isset(self::$_data[$key]) && is_array(self::$_data[$key])) {
            self::$_data[$key][] = $data;
//            array_push(self::$_data[$key],$data);
        } else {
            self::$_data[$key] = array($data);
//            array_push(self::$_data[$key],array($data));
        }
        return true;
    }

}