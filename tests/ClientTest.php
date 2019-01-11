<?php

include dirname(__DIR__) . '/vendor/autoload.php';

use Task\Lamb;

//$db = new \DB\MedooPDO();

$lamb = new Lamb('Mary');

(new Client\SwooleSender())->sendTask($lamb);

(new Client\CommonSender())->sendCommand('echo "123" >> tests/number.txt');
