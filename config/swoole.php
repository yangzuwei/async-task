<?php

return  [
        'swoole_task_server_port' => 9501,
        'swoole_work_num' => 2,   //一般设置为服务器CPU数的1-4倍
        'swoole_is_daemonize' => false,//以守护进程执行
        'swoole_task_worker_num' => 8,//task进程的数量
    ];


