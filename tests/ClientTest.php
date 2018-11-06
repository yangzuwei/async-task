<?php

include dirname(__DIR__) . '/vendor/autoload.php';

use Task\Lamb;

$lamb = new Lamb('Mary');
(new TaskClient\SwooleSender())->sendTask($lamb);

(new TaskClient\CommonSender())->sendCommand('echo "123" >> number.txt');
