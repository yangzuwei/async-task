<?php

include dirname(__DIR__) . '/vendor/autoload.php';


$lamb = new Wilson\Async\Task\Lamb('Mary');

$db = new Wilson\Async\DB\MedooPDO();
$lamb->setDB($db);

$lamb->handler();


