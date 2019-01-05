<?php
$auloader = dirname(__DIR__) . '/vendor/autoload.php'
if(file_exists($auloader)){
	include $auloader;
}

(new SwooleServer\TaskServer())->run();