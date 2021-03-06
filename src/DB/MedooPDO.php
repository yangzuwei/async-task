<?php
/**
 * 可考虑使用原生的PDO实现
 * Created by PhpStorm.
 * User: wilson
 * Date: 2018/7/20
 * Time: 16:57
 */

namespace Wilson\Async\DB;

use Medoo\Medoo;

class MedooPDO extends Medoo
{
    protected $config;

    public function __construct()
    {
        $this->config = getConfig();
        $options = [
            'database_type' => 'mysql',
            'database_name' => $this->config['database_name'],
            'server' => $this->config['database_host'],
            'username' => $this->config['username'],
            'password' => $this->config['password'],
            'options'=>[
                \PDO::ATTR_PERSISTENT => true
            ],
        ];
        parent::__construct($options);
    }
}