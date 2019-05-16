<?php
/**
 * User: Ron
 * Date: 2017/09/21 上午9:36
 * yac 基础类
 */

namespace common\models\core;

class YacBaseModel {
    public static $yacObj;

    //缓存键名前缀
    public static $yacKeyFix = 'yiiyac_';

    public static function conn()
    {
        if (!self::$yacObj) {
            self::$yacObj = new \Yac(self::$yacKeyFix);
        }
        return self::$yacObj;
    }

    public static function get($keyOrKeyValArr)
    {
        if (class_exists('Yac')) {
            self::conn();
            if (is_array($keyOrKeyValArr)) {
                $newKeyOrKeyValArr = array();
                foreach ($keyOrKeyValArr as $key => $val) {
                    $newKeyOrKeyValArr[md5($key)] = $val;
                }
            } else {
                $newKeyOrKeyValArr = md5($keyOrKeyValArr);
            }
            $resultVal = self::$yacObj->get($newKeyOrKeyValArr);
            return $resultVal;
        }
        return '';
    }

    public static function set($keyOrKeyValArr, $value, $time = 0)
    {
        if (class_exists('Yac')) {
            self::conn();
            try {
                if (is_array($keyOrKeyValArr)) {
                    $newKeyOrKeyValArr = array();
                    foreach ($keyOrKeyValArr as $key => $val) {
                        $newKeyOrKeyValArr[md5($key)] = $val;
                    }
                    self::$yacObj->set($newKeyOrKeyValArr);
                } else {
                    $newKeyOrKeyValArr = (string)md5($keyOrKeyValArr);
                    $value = (string)$value;
                    self::$yacObj->set($newKeyOrKeyValArr, $value);
                }
                if ($time > 0) {
                    self::$yacObj->delete($newKeyOrKeyValArr, $time);
                }
            } catch (\Exception $e) {
            }

        }
    }

    public static function delete($key, $time)
    {
        if (class_exists('Yac')) {
            self::conn();
            try {
                if (is_array($key)) {
                    foreach ($key as $k => $v) {
                        $newKey[md5($k)] = $v;
                    }
                } else {
                    $newKey = md5($key);
                }
                self::$yacObj->delete($newKey, $time);
            } catch (\Exception $e) {
            }
        }
    }

    public static function flush()
    {
        if (class_exists('Yac')) {
            self::conn();
            try {
                self::$yacObj->flush();
            }catch (\Exception $e) {
            }
        }
    }

    public static function info()
    {
        if (class_exists('Yac')) {
            self::conn();
            return self::$yacObj->info();
        }
        return '';
    }

}