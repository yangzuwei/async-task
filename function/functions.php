<?php
/**
 * Created by PhpStorm.
 * User: yangzuwei
 * Date: 2018/11/16
 * Time: 上午11:44
 */

function getConfig($configFileName = 'swoole')
{
    return require dirname(__DIR__).'/config/'.$configFileName.'.php';
}