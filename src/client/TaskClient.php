<?php

namespace SwooleClient;

use Task\TaskInterface;

class TaskClient
{
    private $client;

    public function __construct()
    {
        $config = require_once dirname(dirname(__DIR__)).DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'swoole.php';
        $this->client = new \Swoole\Client(SWOOLE_SOCK_TCP);
        if (!$this->client->connect('127.0.0.1', $config['swoole_task_server_port'], -1)) {
            exit("connect failed. Error: {$this->client->errCode}\n");
        }
    }

    public function sendTask(TaskInterface $task)
    {
        $this->client->send(serialize($task));
    }

    public function sendCommand($command)
    {
        $this->client->send(serialize($command));
    }

    public function __destruct()
    {
        $this->client->close();
    }
}




