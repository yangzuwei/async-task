<?php

include dirname(__DIR__) . '/vendor/autoload.php';


$lamb = new \Task\Lamb('Mary');

$db = new \DB\MedooPDO();
$lamb->setDB($db);

$lamb->handler();


