<?php

class Customer_Constant_Server extends Base_Php_Overloader
{

    private static $_instance = NULL;

    const ENABLE = 0;
    const DISABLE = 1;
    static $_STATUS = array(
        self::ENABLE => 'ENABLE',
        self::DISABLE => 'DISABLE'
    );


    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
}