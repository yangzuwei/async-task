<?php

namespace Task;

abstract class AbstractTask
{
    protected static $DB;//此处应该由server 来分配资源

    /**
     * 在server中可以注入资源
     */
    public function setDB($db = null)
    {
        return self::$DB = $db;
    }

    public abstract function handler();
}