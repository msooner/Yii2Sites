<?php
/**
 * User: Ron
 * Date: 2017/09/20 下午2:56
 * To change this template use File | Settings | File Templates.
 */

namespace common\service\appLog;

use common\components\PubFun;
use common\service\core\BaseService;
use common\models\dbmongo\LogMongoModel;
use common\models\UserLoginOpLogModel;

class AppLogService extends BaseService {
    public function addLog($data)
    {
        $data['time'] = date('Y-m-d H:i:s', time());
        $data['id'] = PubFun::getUniqueValue();
        $data['site'] = 'PC';
        $logModel = new LogMongoModel();
        $logModel->addLog($data);
    }

    public function addApiLog($data)
    {
        $this->addLog($data);
    }
}
