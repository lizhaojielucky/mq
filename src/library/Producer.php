<?php
/**
 * filename: Producer.php.
 * author: china.php@qq.com
 * datetime: 2019/12/20
 */

namespace mq\library;

use mq\exception\MqException;

/** 生产者
 * Class Producer
 * @package mq\library
 */
class Producer
{
    private static $connection;
    private static $channel;
    private static $exchange;
    private static $exchangeName = "tony-e";
    private static $instance = null;
    private static $config;

    /*
     数组格式
     [
       'host' => '127.0.0.1',
       'port' => '5672',
       'login' => 'guest',
       'password' => 'guest',
       'vhost'=>'/'
   ];*/

    private function __construct($config = [])
    {
        self::$config = $config;

        try {
            //建立连接
            self::$connection = new \AMQPConnection(self::$config);
            self::$connection->connect();
            //创建信道
            self::$channel = new \AMQPChannel(self::$connection);
            //创建交换机
            self::$exchange = new \AMQPExchange(self::$channel);
            //初始化交换机
            self::$exchange->setName(self::$exchangeName);
            //设置交换机类型
            self::$exchange->setType(AMQP_EX_TYPE_DIRECT);
            self::$exchange->setFlags(AMQP_DURABLE);
            self::$exchange->declareExchange();
        } catch (\Exception $e) {
            throw new MqException($e->getMessage());
        }
    }

    private function __clone()
    {
    }

    /** 初始化
     * @param array $config
     * @return Producer|null
     * @throws MqException
     */
    public static function getInstance($config = [])
    {
        if (empty(self::$instance)) {
            self::$instance = new self($config);
        }
        return self::$instance;
    }

    /**生产消息
     * @param $message
     * @param $key
     * @return bool
     * @throws MqException
     */
    public static function publishMessage($message, $key)
    {
        try {
            $result = self::$exchange->publish($message, $key);
            return $result;

        } catch (\Exception $e) {
            throw new MqException($e->getMessage());
        }
    }


    /**关闭
     * @throws MqException
     */
    private static function close()
    {
        try {
            self::$channel->close();
            self::$connection->disconnect();
        } catch (\Exception $e) {
            throw new MqException($e->getMessage());
        }
    }
}