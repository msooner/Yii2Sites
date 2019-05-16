<?php
/**
 * User: Ron
 * Date: 2017/11/28 下午5:28
 * RabbitMq 发送队列消息处理类
 */

namespace common\service\common;

use common\service\core\BaseService;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Wire\AMQPTable;
use PhpAmqpLib\Message\AMQPMessage;
use common\components\LogManage;

class RabbitMqService extends BaseService {

    private $_ch = null;//handle对象
    private $_conn = null;//链接
    private $_configQueue = [];//配置
    private $_pathSign = 2;//日志目录标识

    public function __construct()
    {
        parent::__construct();
        $this->_configQueue = require(__DIR__ . '/../../../common/config/' . YII_ENV . '/queue.php');
    }

    /**
     * 建立队列链接
     *
     * @param string $exchange 默认接受消息的exchange
     * @param string $queue 默认的队列名称
     * @param string $routingKey 队列名称的routingKey
     * @param string $type 队列类型
     * @param array|null $argument
     * @return null|\PhpAmqpLib\Channel\AMQPChannel
     * @throws \Exception
     */
    public function getChannel($exchange, $queue, $routingKey, $type, $argument = null)
    {
        $rabbitMqConfig = $this->_configQueue['hostConfig'];
        try {
            $this->_conn = new AMQPStreamConnection($rabbitMqConfig['host'], $rabbitMqConfig['port'], $rabbitMqConfig['user'], $rabbitMqConfig['password']);
            $this->_ch = $this->_conn->channel();
            //创建一个队列
            if ($argument != null && !empty($argument)) $argument = new AMQPTable($argument);
            //Declares queue, creates if needed
            $this->_ch->queue_declare($queue, false, true, false, false, false, $argument);
            //创建exchange
            $this->_ch->exchange_declare($exchange, $type, false, true, false);
            //exchange和队列进行绑定。当消息队列不存在是会自动创建
            $this->_ch->queue_bind($queue, $exchange, $routingKey);
            return $this->_ch;
        } catch (\Exception $e) {
            $errorMessage = "创建消息错误,exchange名称:{$exchange},队列名称:{$queue},routingKey的名称:{$routingKey},队列类型:{$type}，错误内容:" . $e->getMessage();
            (new LogManage())->writeLogMsg($errorMessage, $this->_pathSign);
            throw new \Exception($errorMessage);
        }
    }

    /**
     * 发送队列消息
     *
     * @param string $messageStr 消息的内容，字符串类型
     * @param string $messageType 业务类型
     * @param array $exchangeAndQueueArr 接受消息的exchange和队列名称，数组。格式如 array('exchange'=>'exchangeName','queue'=>'queueName','type'=>'typeName','routingKey'=>'routingKeyName'); typeName目前只使用topic
     * @param array $arrayTags 消息队列的标签。数组格式 如 array('keyName'=>'valName'); 内容可自定义。暂时还没用上。可用于标记消息的说明等。
     * @return bool
     * @throws \Exception
     */
    public function sendMessage($messageStr, $messageType, $exchangeAndQueueArr = [], $arrayTags = [])
    {
        $rabbitMqConfig = isset($this->_configQueue[$messageType]) ? $this->_configQueue[$messageType] : [];
        if (empty($rabbitMqConfig)) return false;

        $logManage = new LogManage();
        $logManage->writeLogMsg("开始发送消息:{$messageStr}", $this->_pathSign);
        //判断消息的类型。不同的消息有不同的$exchange 和  $queue等
        $exchange = isset($rabbitMqConfig['exchange']) ? $rabbitMqConfig['exchange'] : '';
        $queue = isset($rabbitMqConfig['queue']) ? $rabbitMqConfig['queue'] : '';
        $routingKey = isset($rabbitMqConfig['routingKey']) ? $rabbitMqConfig['routingKey'] : '';
        $type = isset($rabbitMqConfig['type']) ? $rabbitMqConfig['type'] : '';
        $argument = isset($rabbitMqConfig['argument']) ? $rabbitMqConfig['argument'] : null;
        //设置改参数后会覆盖掉之前消息类型设置的 $exchange 和  $queue
        if ($exchangeAndQueueArr) {
            if (isset($exchangeAndQueueArr['exchange'])) $exchange = $exchangeAndQueueArr['exchange'];
            if (isset($exchangeAndQueueArr['queue'])) $queue = $exchangeAndQueueArr['queue'];
            if (isset($exchangeAndQueueArr['routingKey'])) $routingKey = $exchangeAndQueueArr['routingKey'];
            if (isset($exchangeAndQueueArr['type'])) $type = $exchangeAndQueueArr['type'];
            if (isset($exchangeAndQueueArr['argument'])) $argument = $exchangeAndQueueArr['argument'];
        }
        //delivery_mode   2表示持久化
        if (empty($arrayTags)) $arrayTags = ['content_type' => 'text/json', 'delivery_mode' => 2];
        //获取消息通道
        $this->getChannel($exchange, $queue, $routingKey, $type, $argument);
        try {
            //发送消息
            $msg = new AMQPMessage($messageStr, $arrayTags);
            $this->_ch->basic_publish($msg, $exchange, $routingKey);
            $this->close();
            $logManage->writeLogMsg("消息已完成发送:{$messageStr}", $this->_pathSign);
        } catch (\Exception $e) {
            $errorMessage = "消息发送失败,exchange名称:{$exchange},队列名称:{$queue},routingKey的名称：{$routingKey}，队列类型：{$type}，消息内容:{$messageStr},错误内容：" . $e->getMessage();
            $logManage->writeLogMsg($errorMessage, $this->_pathSign);
            throw new \Exception($errorMessage);
        }
        return true;
    }

    /**
     * 关闭handler/connect
     */
    public function close()
    {
        $this->_ch->close();
        $this->_conn->close();
    }

}