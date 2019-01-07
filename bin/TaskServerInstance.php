<?php
$autoloader = dirname(__DIR__) . '/vendor/autoload.php';
if(file_exists($autoloader)){
	include $autoloader;
}

(new SwooleServer\TaskServer())->run();