<?php
/**
 * Created by PhpStorm.
 * User: wilson
 * Date: 2018/7/20
 * Time: 16:57
 */

namespace DB;

use Medoo\Medoo;

class MysqlPDO
{
    private $pdo;

    public function __construct()
    {
        $config = require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'db.php';

        $this->pdo = new Medoo([
            'database_type' => 'mysql',
            'database_name' => $config['database_name'],
            'server' => $config['server'],
            'username' => $config['username'],
            'password' => $config['password'],
            'options'=>[
                \PDO::ATTR_PERSISTENT => true
            ],
        ]);
    }

    public function getInstance()
    {
        return $this->pdo;
    }
}