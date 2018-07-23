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
    private static $instance;
    private $pdo;

    private function __construct()
    {
        $config = require_once dirname(__DIR__).DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'db.php';
        // Initialize
        $database = new Medoo([
            'database_type' => 'mysql',
            'database_name' => $config['database_name'],
            'server' => $config['server'],
            'username' => $config['username'],
            'password' => $config['password'],
        ]);

        $this->pdo = $database;
    }

    private function __clone()
    {
    }

    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance->pdo;
    }
}