<?php
include dirname(__DIR__).'/src/Server/TaskServer.php';
include dirname(__DIR__).'/function/functions.php';
(new \Wilson\Async\Server\TaskServer())->run();