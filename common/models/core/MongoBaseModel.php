<?php
/**
 * User: Ron
 * Date: 2017/09/20 下午1:39
 * mongo基础类
 */

namespace common\models\core;

use common\components\YiiMongo;
use common\components\PubFun;

class MongoBaseModel {

    public $ctPrefix = 'who_';

    public $mongoDbType = 'mongodb';


    public $collectionName = '';

    public $mongodbConn = null;

    public function connMongoDd()
    {
        $this->mongodbConn = YiiMongo::getConn($this->mongoDbType);


        return $this->mongodbConn;
    }

    public function selectCollection($collectionName)
    {
        //截取目录，只获取类名
        $collectionNameArr = explode('\\',$collectionName);
        $collectionName = end($collectionNameArr);
        //将驼峰式类名转换为下划线式数据表
        $collectionName = preg_replace('/(?<=[a-z])([A-Z])/', '_$1', $collectionName);
        $collectionName = str_replace('_model', '', strtolower($collectionName));
        $this->collectionName = $this->ctPrefix . $collectionName;
        $this->connMongoDd();
        if ($this->mongodbConn) {
            return true;
        }
        return false;
    }

    public function insert($data, $options = array())
    {
        if ($this->mongodbConn && $this->collectionName) {
            $result = $this->mongodbConn->insert($this->collectionName, $data, $options);
            return $result;
        }
        return false;
    }

    public function find($whereArr = [], $fields = [], $sort = [], $page = 0, $pageSize = 20)
    {
        $writeOps = [];
        //查询字段
        if($fields){
            $projection = [];
            foreach($fields as $v){
                $projection[$v] = 1;
            }
            $writeOps['projection'] = $projection;
        }
        //排序
        if($sort){
            $writeOps['sort'] = $sort;
        }
        //分页
        if ($page > 0) {
            $writeOps['limit'] = $pageSize;
            $skipNum = ($page - 1) * $pageSize;
            if ($skipNum) {
                $writeOps['limit'] = $skipNum;
            }
        }
        if ($this->mongodbConn && $this->collectionName) {
            $result = $this->mongodbConn->query($this->collectionName, $whereArr, $writeOps);
            if($result) {
                return $result->toArray();
            }
            return $result;
        }
        return false;
    }

    public function remove($whereArr = array(), $options = array())
    {
        if ($this->mongodbConn && $this->collectionName) {
            $result = $this->mongodbConn->del($this->collectionName, $whereArr, $options);
            return $result;
        }
        return false;
    }

}