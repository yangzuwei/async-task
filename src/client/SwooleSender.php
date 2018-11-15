<?php
/**
 * 使用swoole客户端
 */

namespace TaskClient;

use Task\AbstractTask;

class SwooleSender extends AbstractSender
{
    public function __construct()
    {
        $config = $this->getConfig();
        $this->client = new \Swoole\Client(SWOOLE_SOCK_TCP);
        if (!$this->client->connect($config['ip'], $config['swoole_task_server_port'], -1)) {
            exit("connect failed. Error: {$this->client->errCode}\n");
        }
    }

    public function sendTask(AbstractTask $task)
    {
        return $this->client->send(serialize($task).PHP_EOL);
    }

    public function sendCommand($command)
    {
        return $this->client->send(serialize($command).PHP_EOL);
    }

    public function __destruct()
    {
        $this->client->close();
    }
}


