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
        //CI框架配置读取
        if(defined('CI_VERSION')){
            return BASEPATH . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'swoole.php';
        }
        return require dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'swoole.php';
    }

    public abstract function sendTask(AbstractTask $task);
    public abstract function sendCommand($command);
}