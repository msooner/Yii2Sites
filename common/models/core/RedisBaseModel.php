<?php
/**
 * User: Ron
 * Date: 2017/09/21 上午11:04
 * redis 基础类
 */

namespace common\models\core;


class RedisBaseModel {
    //redis连接
    public static $_redis_cache = null;

    //redis前缀key
    public static $_redis_namespace = '';

    public static function init()
    {
        //判断是否初始化
        if (self::$_redis_cache == null || self::$_redis_namespace == '') {
            $configRedis = require(\Yii::getAlias('@YiiSiteApp') . '/config/' . YII_ENV . '/redis.config.php');
            if (empty(self::$_redis_namespace)) self::$_redis_namespace = \Yii::$app->params['redis_namespace']['redis_namespace_' . YII_SITE_TYPE];
            $useType = isset(\Yii::$app->params['redis_namespace']['useType'][YII_APP_NAME]) ? \Yii::$app->params['redis_namespace']['useType'][YII_APP_NAME] : [];
            if (!empty($useType) && in_array(YII_SITE_TYPE, $useType)) {
                $applicationOneConfig = $configRedis['redisApplicationOne'];
                try {
                    self::$_redis_cache = new \Redis();
                    self::$_redis_cache->connect($applicationOneConfig['host'], $applicationOneConfig['port']);
                    if ($applicationOneConfig['redisPassword']) {
                        self::$_redis_cache->auth($applicationOneConfig['redisPassword']);
                    }
                }catch (\RedisException $e) {
                    //TO DO 可写日志
                }
            } else {
                //使用redis集群
                $applicationConfig = $configRedis['redisApplication'];
                foreach ($applicationConfig['host'] as $redisHost) {
                    $connectArr[] = $redisHost;
                }
                //链接redis超时时间。
                if (!isset($applicationConfig['connTimeOut']) || !$applicationConfig['connTimeOut']) {
                    $applicationConfig['connTimeOut'] = 5;
                }
                //读取redis数据超时时间
                if (!isset($applicationConfig['readTimeOut']) || !$applicationConfig['readTimeOut']) {
                    $applicationConfig['readTimeOut'] = 5;
                }
                $connectArr[] = $applicationConfig['connTimeOut'];
                $connectArr[] = $applicationConfig['readTimeOut'];
                try {
                    self::$_redis_cache = new \RedisCluster(NULL, $connectArr);
                } catch (\RedisException $e) {
                    //TO DO 可写日志
                }
            }
        }
    }

    /**
     * @return null
     */
    public function getRedis()
    {
        self::init();
        return self::$_redis_cache;
    }

}