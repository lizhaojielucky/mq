---
#消息队列mq
##使用说明
- 本消息队列使用的rabbitmq为驱动
- 先安装好amqp扩展
- 样例 
---
- 生产者

require_once 'vendor/autoload.php';
$config = [
    'host' => '127.0.0.1',
    'port' => '5672',
    'login' => 'guest',
    'password' => 'guest',
    'vhost'=>'/',
    'connect_timeout'=>3
];
$routeKey = 'weixin';

\mq\library\Producer::getInstance($config)->publishMessage("send message:".date("Y-m-d H:i:s"),$routeKey);

- 消费者

require_once 'vendor/autoload.php';
$config = [
    'host' => '127.0.0.1',
    'port' => '5672',
    'login' => 'guest',
    'password' => 'guest',
    'vhost'=>'/',
    'connect_timeout'=>3
];
$routeKey = 'weixin';
\mq\library\Consumer::getInstance($config,"weixin")->bindExchange($routeKey)->consumeMessage();
