<?php
//独立项目和集成项目自动加载器判断
$autoloadPaths = [
    dirname(__DIR__) . '/vendor/autoload.php',
    dirname(dirname(dirname(__DIR__))) . '/autoload.php',
];
foreach ($autoloadPaths as $path) {
    if (file_exists($path)) {
        include $path;
    }
}
(new \Wilson\Async\Server\TaskServer())->run();