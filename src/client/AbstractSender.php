<?php
/**
 * Created by PhpStorm.
 * User: yangzuwei
 * Date: 2018/11/15
 * Time: 下午5:26
 */

namespace TaskClient;

use Task\AbstractTask;

abstract class AbstractSender
{
    protected $client;

    public function getConfig()
    {
        $config = [];
        //CI框架配置读取
        if(defined('CI_VERSION')){
            $config = require APPPATH . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'swoole.php';

        }else{
            $config = require dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'swoole.php';
        }
        return $config;
    }

    public abstract function sendTask(AbstractTask $task);
    public abstract function sendCommand($command);
}