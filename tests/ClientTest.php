<?php

include dirname(__DIR__) . '/vendor/autoload.php';

use Task\Lamb;

//$db = new \DB\MedooPDO();

$lamb = new \Task\Bubble();

(new TaskClient\SwooleSender())->sendTask($lamb);

//(new TaskClient\CommonSender())->sendCommand('echo "123" >> number.txt');
