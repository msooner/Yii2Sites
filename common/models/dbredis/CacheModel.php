<?php
/**
 * User: Ron
 * Date: 2017/09/21 上午11:05
 * redis 缓存处理类
 */

namespace common\models\dbredis;

use common\models\core\RedisBaseModel;
use common\components\PubFun;
use common\components\LogManage;

class CacheModel extends RedisBaseModel {

    protected static $_pathSign = 3;//日志目录标识
    protected static $_pathSignRead = 4;//日志目录标识

    private static function save($key, $value, $ttl = NULL)
    {
        return ($ttl) ? self::$_redis_cache->setex($key, $ttl, $value) : self::$_redis_cache->set($key, $value);
    }

    /**
     * 读取数据
     *
     * @param string $key
     * @param bool $isResultArray true 返回数组类型数据，否则返回对象类型数据
     * @param string $redisNamespace redis缓存前缀，当传入前缀时可以解决PC与M需要使用相同键值的问题
     * @return mixed
     */
    public static function getRedisValue($key, $isResultArray = true, $redisNamespace = '')
    {
        if (in_array($key, array('-userInfo', '-user'))) {
            return null;
        }
        $data = '';
        if (!PubFun::isEmptyStr($key)) {
            self::$_redis_namespace = $redisNamespace;//此行代码必须在 self::init(); 之前处理
            self::init();
            $data = self::$_redis_cache->get(self::$_redis_namespace . $key);
        }
        return $data ? json_decode($data, $isResultArray) : null;
    }

    /**
     * 保存数据
     *
     * @param string $key
     * @param mixed $value 需要缓存的值
     * @param string $redisNamespace redis缓存前缀，当传入前缀时可以解决PC与M需要使用相同键值的问题
     */
    public static function setRedisValue($key, $value, $redisNamespace = '')
    {
        if (!PubFun::isEmptyStr($key)) {
            self::$_redis_namespace = $redisNamespace;//此行代码必须在 self::init(); 之前处理
            self::init();
            self::save(self::$_redis_namespace . $key, json_encode($value));
        }
    }

    /**
     * @param $key
     * @return mixed
     */
    public static function getRedisKeyTTL($key)
    {
        if (!PubFun::isEmptyStr($key)) {
            self::init();
            return self::$_redis_cache->ttl(self::$_redis_namespace . $key);
        }
        return false;
    }

    /**
     * 保存数据
     *
     * @param string $key
     * @param mixed $value 需要缓存的值
     * @param int $seconds 缓存有效期（秒）
     * @param string $redisNamespace redis缓存前缀，当传入前缀时可以解决PC与M需要使用相同键值的问题
     */
    public static function setRedisValueWithExpire($key, $value, $seconds = 7200, $redisNamespace = '')
    {
        if (!PubFun::isEmptyStr($key)) {
            self::$_redis_namespace = $redisNamespace;//此行代码必须在 self::init(); 之前处理
            self::init();
            self::save(self::$_redis_namespace . $key, json_encode($value), $seconds);
        }
    }

    public static function getRedisHashAll($key)
    {
        if (!PubFun::isEmptyStr($key)) {
            self::init();
            return self::$_redis_cache->hgetall(self::$_redis_namespace . $key);
        }
        return null;
    }

    public static function setRedisHashAll($key, $array, $time = 7200)
    {
        if (!PubFun::isEmptyStr($key) and !empty($array)) {
            self::init();
            self::$_redis_cache->hmset(self::$_redis_namespace . $key, $array);
            return self::$_redis_cache->expire(self::$_redis_namespace . $key, $time);
        }
        return null;
    }

    public static function getRedisHashGet($key, $oneKey)
    {
        if (!PubFun::isEmptyStr($key) && !empty($oneKey)) {
            self::init();
            return self::$_redis_cache->hget(self::$_redis_namespace . $key, $oneKey);
        }
        return null;
    }

    public static function setRedisHashSet($key, $oneKey, $value, $time = 7200)
    {
        if (!PubFun::isEmptyStr($key) && !empty($oneKey)) {
            self::init();
            self::$_redis_cache->hset(self::$_redis_namespace . $key, $oneKey, $value);
            self::$_redis_cache->expire(self::$_redis_namespace . $key, $time);
        }
    }

    public static function setRedisHashDel($key, $oneKey)
    {
        if (!PubFun::isEmptyStr($key) && !empty($oneKey)) {
            self::init();
            return self::$_redis_cache->hDel(self::$_redis_namespace . $key, $oneKey);

        }
        return false;
    }

    public static function redisTtl($key)
    {
        if (!PubFun::isEmptyStr($key)) {
            self::init();
            return self::$_redis_cache->ttl(self::$_redis_namespace . $key);
        }
        return null;
    }

    public static function deleteKey($key)
    {
        if (!PubFun::isEmptyStr($key)) {
            self::init();
            return self::$_redis_cache->del(self::$_redis_namespace . $key);
        }
        return false;
    }

    public static function getKeys($key)
    {
        if (!PubFun::isEmptyStr($key)) {
            self::init();
            return self::$_redis_cache->Keys(self::$_redis_namespace . $key);
        }
        return null;
    }



}