<?php
/**
 * 在未安装swoole的机器上可以使用这个
 * Created by PhpStorm.
 * User: wilson
 * Date: 2018/11/6
 * Time: 下午12:40
 */

namespace TaskClient;

use Task\AbstractTask;

class CommonSender
{
    private $fp;

    public function __construct()
    {
        $config = require dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'swoole.php';

        $this->fp = stream_socket_client("tcp://" . $config['ip'] . ":" . $config['swoole_task_server_port'], $errno, $errstr);
        if (!$this->fp) {
            echo "ERROR: $errno - $errstr\n";
        }
    }

    public function sendTask(AbstractTask $task)
    {
        $serializedTask = serialize($task);
        return fwrite($this->fp, $serializedTask . PHP_EOL, strlen($serializedTask) + 1);
    }

    public function sendCommand($command)
    {
        $serializedCommand = serialize($command);
        return fwrite($this->fp, $serializedCommand . PHP_EOL, strlen($serializedCommand) + 1);
    }

    public function __destruct()
    {
        fclose($this->fp);
    }
}