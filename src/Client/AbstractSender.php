<?php
/**
 * Created by PhpStorm.
 * User: yangzuwei
 * Date: 2018/11/15
 * Time: 下午5:26
 */

namespace Wilson\Async\Client;

use Wilson\Async\Task\AbstractTask;

abstract class AbstractSender
{
    protected $client;

    public function getConfig()
    {
        if (defined('CI_VERSION')) {//CI框架配置读取
            $config = require APPPATH . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'swoole.php';
        } elseif (function_exists('env')) { //laravel框架配置
            $config = require config_path('swoole.php');
        } else {//无框架使用
            $config = require dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'swoole.php';
        }
        return $config;
    }

    public abstract function sendTask(AbstractTask $task);

    public abstract function sendCommand($command);
}