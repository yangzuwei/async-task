<?php

namespace SwooleServer;

use Monolog\Logger;
use Task\TaskInterface;
use Monolog\Handler\StreamHandler;

class TaskServer
{
    private $serv;

    public function __construct()
    {
        $config = require_once dirname(dirname(__DIR__)).DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'swoole.php';

        $this->serv = new \Swoole\Server("127.0.0.1", $config['swoole_task_server_port']);
        $this->serv->set(array(
            'worker_num' => $config['swoole_work_num'],   //一般设置为服务器CPU数的1-4倍
            'daemonize' => $config['swoole_is_daemonize'],  //以守护进程执行
            'max_request' => 10000,
            'dispatch_mode' => 2,
            'task_worker_num' => $config['swoole_task_worker_num'],  //task进程的数量
            "task_ipc_mode " => 3,  //使用消息队列通信，并设置为争抢模式
            "log_file" => "logs/taskqueueu.log",//日志
        ));
        $this->serv->on('Receive', array($this, 'onReceive'));
        // bind callback
        $this->serv->on('Task', array($this, 'onTask'));
        $this->serv->on('Finish', array($this, 'onFinish'));
        $this->serv->start();
    }

    public function onReceive($serv, $fd, $from_id, $data)
    {
        //投递异步任务
        $task_id = $serv->task($data);
        $logger = new Logger('task_log');
        $logger->pushHandler(new StreamHandler('./logs/receive.log', Logger::INFO));
        $logger->addInfo("AsyncTask[$task_id] receive: $data" . PHP_EOL);
        echo "Dispath AsyncTask: id=$task_id\n";
    }

    public function onTask($serv, $task_id, $from_id, $data)
    {
        //处理任务 任务必须要实现 TaskInterface接口
        echo "New AsyncTask[id=$task_id]" . PHP_EOL;
        $task = unserialize($data);
        if ($task instanceof TaskInterface) {
            $task->handler();//集成task任务接口方式
            $result = 'task is TaskInterface';
        } else {
            exec($task);//外部命令方式
            $result = 'task is command';
        }
        $serv->finish($result);
    }

    //处理异步任务的结果
    public function onFinish($serv, $task_id, $data)
    {
        $logger = new Logger('task_log');
        $logger->pushHandler(new StreamHandler('./logs/result.log', Logger::INFO));
        $logger->addInfo("AsyncTask[$task_id] Finish: $data" . PHP_EOL);
    }
}
