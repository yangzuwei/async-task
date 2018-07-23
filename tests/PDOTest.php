<?php

include dirname(__DIR__) . '/vendor/autoload.php';

use DB\MysqlPDO;

$db = MysqlPDO::getInstance();

//var_dump($db);exit();
$result = $db->query('select now()');
var_dump($result->fetchAll());


