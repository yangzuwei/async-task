<?php
//独立项目和集成项目自动加载器判断
$autoloadPaths = [
    dirname(__DIR__) . '/vendor/autoload.php',
    dirname(dirname(dirname(dirname(__DIR__)))) . '/vendor/autoload.php',
];
foreach ($autoloadPaths as $path) {
    if (file_exists($path)) {
        include $path;
        break;
    }
}

function getConfig()
{
    $config = require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'swoole.php';
    return $config;
}

(new \Wilson\Async\Server\TaskServer())->run();