<?php
/**
 * User: Ron
 * Date: 2018/07/12 下午3:37
 * To change this template use File | Settings | File Templates.
 */
/**
 * mongoDB 简单 封装
 * 请注意：mongoDB 支持版本 3.2+
 * 具体参数及相关定义请参见： https://docs.mongodb.com/manual/reference/command/
 *
 * @author color_wind
 */
namespace common\components;
final class YiiMongo {

    //--------------  定义变量  --------------//
    private static $ins     = [];
    private static $connType = "mongodb";
    private $_conn          = null;
    private $_db            = null;

    /**
     * 创建实例
     * @param  string $confkey
     * @return \m_mgdb
     */
    static function getConn($connType = NULL) {
        $configMongo = require(\Yii::getAlias('@YiiSiteApp') . '/config/' . YII_ENV . '/mongo.config.php');
        if (!$connType) {
            $connType = self::$connType;
        }
        if (!isset(self::$ins[$connType]) && ($conf = $configMongo[$connType])) {
            $mongoManager = new YiiMongo($conf);
            self::$ins[$connType] = $mongoManager;
        }
        return self::$ins[$connType];
    }


    /**
     * 构造方法
     * 单例模式
     */
    private function __construct(array $conf) {
        try{
            $this->_conn = new \MongoDB\Driver\Manager($conf["newDsn"]."/{$conf["db"]}");
        }catch (\Exception $e) {
            //链接错误
        }
        $this->_db = $conf["db"];
    }


    /**
     * 插入数据
     * @param  string $collname
     * @param  array  $documents    [["name"=>"values", ...], ...]
     * @param  array  $writeOps     ["ordered"=>boolean,"writeConcern"=>array]
     * @return \MongoDB\Driver\Cursor
     */
    function insert($collname, array $documents, array $writeOps = []) {
        $cmd = [
            "insert"    => $collname,
            "documents" => $documents,
        ];
        $cmd += $writeOps;
        return $this->command($cmd);
    }


    /**
     * 删除数据
     * @param  string $collname
     * @param  array  $deletes      [["q"=>query,"limit"=>int], ...]
     * @param  array  $writeOps     ["ordered"=>boolean,"writeConcern"=>array]
     * @return \MongoDB\Driver\Cursor
     */
    function del($collname, array $deletes, array $writeOps = []) {
        foreach($deletes as &$_){
            if(isset($_["q"]) && !$_["q"]){
                $_["q"] = (Object)[];
            }
            if(isset($_["limit"]) && !$_["limit"]){
                $_["limit"] = 0;
            }
        }
        $cmd = [
            "delete"    => $collname,
            "deletes"   => $deletes,
        ];
        $cmd += $writeOps;
        return $this->command($cmd);
    }


    /**
     * 更新数据
     * @param  string $collname
     * @param  array  $updates      [["q"=>query,"u"=>update,"upsert"=>boolean,"multi"=>boolean], ...]
     * @param  array  $writeOps     ["ordered"=>boolean,"writeConcern"=>array]
     * @return \MongoDB\Driver\Cursor
     */
    function update($collname, array $updates, array $writeOps = []) {
        $cmd = [
            "update"    => $collname,
            "updates"   => $updates,
        ];
        $cmd += $writeOps;
        return $this->command($cmd);
    }


    /**
     * 查询
     * @param  string $collname
     * @param  array  $filter     [query]     参数详情请参见文档。
     * @return \MongoDB\Driver\Cursor
     */
    function query($collname, array $filter, array $writeOps = []){
        $cmd = [
            "find"      => $collname,
            "filter"    => $filter
        ];
        $cmd += $writeOps;
        return $this->command($cmd);
    }



    function command(array $param) {
        $cmd = new \MongoDB\Driver\Command($param);
        try{
            return $this->_conn->executeCommand($this->_db, $cmd);
        } catch (\Exception $e) {
            return false;
        }
    }


    /**
     * 获取当前mongoDB Manager
     * @return MongoDB\Driver\Manager
     */
    function getMongoManager() {
        return $this->_conn;
    }

}
