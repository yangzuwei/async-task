<?php

include dirname(__DIR__) . '/vendor/autoload.php';

$db = new \DB\MedooPDO();

while(true){
    $result = $db->query('select now()');
    var_dump($result->fetchAll()[0]);
    var_dump($db->error());
    sleep(1);
}


