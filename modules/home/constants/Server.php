<?php

class Home_Constant_Server extends Base_Php_Overloader
{
    private static $_instance = NULL;

    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
}