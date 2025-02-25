<?php

use Medoo\Medoo;

class BaseDatos extends Medoo
{
    private static $instance = null;

    private function __construct()
    {
        $config = require __DIR__ . '/config/medoo.php';
        parent::__construct($config);
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }


    public static function __callStatic($name, $arguments)
    {
        return call_user_func_array([self::getInstance(), $name], $arguments);
    }
}
