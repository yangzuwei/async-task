<?php

include dirname(__DIR__) . '/vendor/autoload.php';


class DB
{
    public static $db = [];

    public function init()
    {
        for ($i = 0; $i < 5; $i++) {
            self::$db[] = new Wilson\Async\DB\MedooPDO();
        }
    }

}

$db = new DB();
$db->init();
echo count(DB::$db) . PHP_EOL;
array_pop(DB::$db);
echo count(DB::$db) . PHP_EOL;

//while(true){
//    $result = $db->query('select now()');
//    var_dump($result->fetchAll()[0]);
//    var_dump($db->error());
//    sleep(1);
//}


