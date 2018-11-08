<?php

namespace SwooleServer;

use DB\MedooPDO;
use Task\AbstractTask;

class TaskServer
{
    private $serv;
    private $rootPath;

    protected static $DB = [];
    const DB_POOL_SIZE = 2;

    public function __construct()
    {
        $this->rootPath = dirname(dirname(__DIR__));
        $config = require_once $this->rootPath . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'swoole.php';

        $this->serv = new \Swoole\Server($config['ip'], $config['swoole_task_server_port']);
        $this->serv->set(array(
            'worker_num' => $config['swoole_work_num'],   //一般设置为服务器CPU数的1-4倍
            'daemonize' => $config['swoole_is_daemonize'],  //以守护进程执行
            'max_request' => 10000,
            'dispatch_mode' => 2,
            'task_worker_num' => $config['swoole_task_worker_num'],  //task进程的数量
            "task_ipc_mode " => 3,  //使用消息队列通信，并设置为争抢模式
            "log_file" => $this->rootPath . "/logs/taskqueueu.log",//日志
        ));
    }

    public function run()
    {
        $this->serv->on('Receive', array($this, 'onReceive'));
        // bind callback
        $this->serv->on('Task', array($this, 'onTask'));
        $this->serv->on('Finish', array($this, 'onFinish'));
        $this->serv->on('Start', array($this, 'onStart'));
        $this->serv->on('WorkerStart', array($this, 'onWorkerStart'));
        $this->serv->start();
    }

    public function onStart($serv)
    {
        //获取主进程ID 方便关闭和reload
        file_put_contents($this->rootPath . DIRECTORY_SEPARATOR . 'bin/swoole.pid', $serv->master_pid);
    }

    public function onWorkerStart($serv, $id)
    {
        //初始化连接池
        $this->InitDBPool();
    }

    public function onReceive($serv, $fd, $from_id, $data)
    {
        //投递异步任务
        $tasks = explode(PHP_EOL, $data);
        foreach ($tasks as $data) {
            if ($data != '') {
                $task_id = $serv->task($data);
                echo "Dispath $data ---> AsyncTask: id=$task_id\n ";
            }
        }
    }

    public function onTask($serv, $task_id, $from_id, $data)
    {
        //echo date('Y-m-d H:i:s') . "New AsyncTask[id=$task_id] ---> $data" . PHP_EOL;
        //处理任务 任务必须要实现 TaskInterface接口
        $task = unserialize($data);
        if ($task instanceof AbstractTask) {
            $result = $this->runTask($task);
            $cate = 'object';
            echo $cate . PHP_EOL;
        } else {
            $result = exec($task);//外部命令方式
            $cate = 'command';
            echo $cate . PHP_EOL;
        }
        $serv->finish("this is task $cate $data and it's result is {$result} " . PHP_EOL);
    }

    protected function runTask(AbstractTask $task)
    {
        $db = $this->tryTask($task);
        //如果task执行结果中 DB掉线则重连
        if ($task->isDBGone()) {
            $db = $this->chooseAvailable($task);
            //连接池空则放入一个可用对象
            //每个进程至少保证有一个可用的连接 进程之间是禁止共享连接资源的
            if (empty(self::$DB)) {
                echo "re init" . PHP_EOL;
                array_push(self::$DB, (new MedooPDO()));
                $db = $this->tryTask($task);
            }
        }
        //归还连接池资源
        array_push(self::$DB, $db);
        return null;
    }

    public function tryTask(AbstractTask $task)
    {
        $db = array_pop(self::$DB);
        var_dump(count(self::$DB));
        //注入连接池资源
        $task->setDB($db);
        $task->handler();
        return $db;
    }

    public function chooseAvailable($task)
    {
        $db = null;
        //从连接池中获取剩余可用的资源
        while (self::$DB) {
            $db = $this->tryTask($task);
            if ($task->isDBGone() == false) {
                break;
            }
        }
        return $db;
    }

    public function InitDBPool()
    {
        self::$DB = [];
        for ($i = 0; $i < self::DB_POOL_SIZE; $i++) {
            self::$DB[] = new MedooPDO();
        }
        return;
    }

    //处理异步任务的结果
    public function onFinish($serv, $task_id, $data)
    {
        echo date('Y-m-d H:i:s') . "Finished task [id=$task_id] ---> $data" . PHP_EOL;
    }
}
