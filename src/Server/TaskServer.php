<?php

namespace Wilson\Async\Server;

use Wilson\Async\DB\MedooPDO;
use Wilson\Async\Task\AbstractTask;

class TaskServer
{
    private $serv;
    private $rootPath;

    protected static $DB = [];
    const DB_POOL_SIZE = 1;

    public function __construct()
    {
        $this->rootPath = dirname(dirname(__DIR__));//require_once $this->rootPath . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR .
        if(extension_loaded('swoole')){
            $this->initServer();
        }else{
            exit('Make sure you have the extension swoole!');
        }
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

    public function initServer()
    {
        $config = getConfig();
        $this->serv = new \Swoole\Server($config['ip'], $config['swoole_task_server_port']);
        $this->serv->set(array(
            'worker_num' => $config['swoole_work_num'],   //一般设置为服务器CPU数的1-4倍
            'daemonize' => $config['swoole_is_daemonize'],  //以守护进程执行
            'max_request' => 10000,
            'dispatch_mode' => 2,
            'task_worker_num' => $config['swoole_task_worker_num'],  //task进程的数量
            "task_ipc_mode " => 3,  //使用消息队列通信，并设置为争抢模式
            "log_file" => $this->rootPath . "/logs/taskqueueu.log",//日志
            'chroot' => $this->rootPath,
            'user' => 'nginx',
            'group' => 'nginx',
        ));
    }

    /**
     * server启动时记录主进程号方便编写shell脚本
     * @param $serv
     */
    public function onStart($serv)
    {
        //获取主进程ID 方便关闭和reload
        file_put_contents($this->rootPath . DIRECTORY_SEPARATOR . 'bin/swoole.pid', $serv->master_pid);
    }

    /**
     * worker进程启动时回调 每个进程一个连接池 不可以在进程间共享连接池资源
     * @param $serv
     * @param $id
     */
    public function onWorkerStart($serv, $id)
    {
        //初始化连接池
        $this->InitDBPool();
    }

    /**
     * 数据接收
     * @param $serv
     * @param $fd
     * @param $from_id
     * @param $data
     */
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

    /**
     * 任务回调
     * @param $serv
     * @param $task_id
     * @param $from_id
     * @param $data
     */
    public function onTask($serv, $task_id, $from_id, $data)
    {
        //echo date('Y-m-d H:i:s') . "New AsyncTask[id=$task_id] ---> $data" . PHP_EOL;
        //处理任务 任务必须要实现 TaskInterface接口
        $task = unserialize($data);
        $cate = '';

        if (is_object($task) && $task instanceof AbstractTask) {
            $result = $this->runTask($task);
            $cate = 'object';
        }

        if (is_string($task)) {
            $result = $this->runCommand($task);
            $cate = 'command';
        }

        $serv->finish("this is task $cate $data and it's result is {$result} " . PHP_EOL);
    }

    /**
     * 运行外部命令
     * @param $task
     * @return string
     */
    protected function runCommand($task)
    {
        $result = exec($task);//外部命令方式
        return $result;
    }

    /**
     * 运行任务对象
     * @param AbstractTask $task
     * @return null
     */
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

    /**
     * 尝试连接DB之后再运行任务
     * @param AbstractTask $task
     * @return mixed
     */
    public function tryTask(AbstractTask $task)
    {
        $db = array_pop(self::$DB);
        //注入连接池资源
        $task->setDB($db);
        $task->handler();
        return $db;
    }

    /**
     * 选择可用的连接
     * @param $task
     * @return mixed|null
     */
    public function chooseAvailable($task)
    {
        $db = null;
        $i = 0;
        //从连接池中获取剩余可用的资源
        while (self::$DB && $i < self::DB_POOL_SIZE) {
            $db = $this->tryTask($task);
            if ($task->isDBGone() == false) {
                break;
            }
            $i++;
        }
        return $db;
    }

    /**
     * 初始化连接池
     * @return void
     */
    public function InitDBPool()
    {
        self::$DB = [];
        for ($i = 0; $i < self::DB_POOL_SIZE; $i++) {
            try{
                self::$DB[] = new MedooPDO();
            }catch (\Error $e){
                echo $e->getMessage();
            }finally{
                return ;
            }
        }
        return;
    }

    /**
     * 处理异步任务的结果
     * @param $serv
     * @param $task_id
     * @param $data
     */
    public function onFinish($serv, $task_id, $data)
    {
        echo date('Y-m-d H:i:s') . "Finished task [id=$task_id] ---> $data" . PHP_EOL;
    }
}
