<?php
/**
 * 在未安装swoole的机器上可以使用这个
 * Created by PhpStorm.
 * User: wilson
 * Date: 2018/11/6
 * Time: 下午12:40
 */

namespace Wilson\Async\Client;

use Wilson\Async\Task\AbstractTask;

class CommonSender extends AbstractSender
{
    public function __construct()
    {
        $config = $this->getConfig();

        $this->client = stream_socket_client("tcp://" . $config['ip'] . ":" . $config['swoole_task_server_port'], $errno, $errstr);
        if (!$this->client) {
            echo "ERROR: $errno - $errstr\n";
        }
    }

    public function sendTask(AbstractTask $task)
    {
        $serializedTask = serialize($task);
        return fwrite($this->client, $serializedTask . PHP_EOL, strlen($serializedTask) + 1);
    }

    public function sendCommand($command)
    {
        $serializedCommand = serialize($command);
        return fwrite($this->client, $serializedCommand . PHP_EOL, strlen($serializedCommand) + 1);
    }

    public function __destruct()
    {
        fclose($this->client);
    }
}