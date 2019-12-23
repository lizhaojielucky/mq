<?php
/**
 * filename: Consumer.php.
 * author: china.php@qq.com
 * datetime: 2019/12/21
 */

namespace mq\library;


use mq\exception\MqException;

/** 消费者
 * Class Consumer
 * @package mq\library
 */
class Consumer
{

    private static $connection;
    private static $channel;
    private static $queue;
    private static $exchangeName = "tony-e";
    private static $instance = null;
    /*
     数组格式:参见生产者
    */
    private static $config;

    /**
     * Consumer constructor.
     * @param $config 配置参数
     * @param $queueName 队列名
     * @throws MqException
     */
    private function __construct($config, $queueName)
    {
        self::$config = $config;
        try {
            //建立连接
            self::$connection = new \AMQPConnection(self::$config);
            self::$connection->connect();
            //创建信道
            self::$channel = new \AMQPChannel(self::$connection);
            //创建队列
            self::$queue = new \AMQPQueue(self::$channel);
            self::$queue->setName($queueName);
            self::$queue->setFlags(AMQP_DURABLE);
            self::$queue->declareQueue();
        } catch (\Exception $e) {
            throw new MqException("初始化失败：" . $e->getMessage());
        }
    }

    private function __clone()
    {
    }

    /**初始化
     * @param $config
     * @param string $queueName
     * @return Consumer|null
     * @throws MqException
     */
    public static function getInstance($config, $queueName = "q")
    {
        if (empty(self::$instance)) {
            self::$instance = new self($config, $queueName);
        }
        return self::$instance;
    }


    /**队列绑定交换机
     * @param $routeKey
     * @return null
     * @throws MqException
     */
    public static function bindExchange($routeKey)
    {
        try {
            //以指定的路由键绑定队列，路由键需与发布到交换机的消息的路由键要一至
            self::$queue->bind(self::$exchangeName, $routeKey);
        } catch (\Exception $e) {
            throw new MqException("绑定交换机失败：" . $e->getMessage());
        }
        return self::$instance;
    }

    /**
     * 消费消息
     * @throws MqException
     */
    public static function consumeMessage($fn)
    {
        try {
            self::$queue->consume($fn);
        } catch (\Exception $e) {
            throw new MqException("消费消息异常：" . $e->getMessage());
        }
    }

}