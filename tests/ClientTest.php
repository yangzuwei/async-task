<?php

include dirname(__DIR__) . '/vendor/autoload.php';

use Wilson\Async\Task\Lamb;

//$db = new \DB\MedooPDO();

$lamb = new Lamb('Mary');

(new Wilson\Async\Client\SwooleSender())->sendTask($lamb);

(new Wilson\Async\Client\SwooleSender())->sendCommand('echo "123" >> '.__DIR__.'/number.txt');
