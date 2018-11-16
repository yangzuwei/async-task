<?php
/**
 * 可考虑使用原生的PDO实现
 * Created by PhpStorm.
 * User: wilson
 * Date: 2018/7/20
 * Time: 16:57
 */

namespace DB;

use Medoo\Medoo;

class MedooPDO extends Medoo
{
    protected $config;

    public function __construct()
    {
        $this->config = getConfig('db');
        $options = [
            'database_type' => 'mysql',
            'database_name' => $this->config['database_name'],
            'server' => $this->config['server'],
            'username' => $this->config['username'],
            'password' => $this->config['password'],
            'options'=>[
                \PDO::ATTR_PERSISTENT => true
            ],
        ];
        parent::__construct($options);
    }
}