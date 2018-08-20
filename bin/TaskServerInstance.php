<?php

include dirname(__DIR__) . '/vendor/autoload.php';

(new SwooleServer\TaskServer())->run();