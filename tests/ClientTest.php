<?php

include dirname(__DIR__) . '/vendor/autoload.php';

use SwooleClient\TaskClient;
use Task\Lamb;

$lamb = new Lamb('Mary');
(new TaskClient())->sendTask($lamb);


