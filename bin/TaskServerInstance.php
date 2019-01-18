<?php
include dirname(__DIR__).'/src/Server/TaskServer.php';
(new \Wilson\Async\Server\TaskServer())->run();