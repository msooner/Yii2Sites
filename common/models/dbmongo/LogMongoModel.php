<?php
/**
 * User: Ron
 * Date: 2017/09/20 下午2:42
 * 日志处理类
 */

namespace common\models\dbmongo;

use common\models\core\MongoBaseModel;

/**
 * @property int mongoDbType
 */
class LogMongoModel extends MongoBaseModel {
    public function __construct()
    {
        parent::__construct();
        $this->mongoDbType = 2;
        $this->selectCollection('log');
    }

    /**
     * 写日志
     *
     * @param array
     */
    public function addLog($logArr)
    {
        $this->insert($logArr);
    }

}