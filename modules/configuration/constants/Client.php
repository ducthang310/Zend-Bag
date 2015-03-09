<?php

class Configuration_Constant_Client extends Base_Php_Overloader
{
    const FAILED = 'failed';

    const SUCCESSFUL = 'successful';

    const EXPIRED = 'expired';

    private static $_instance = NULL;

    public static $_rating = array('Fantastic', 'Good', 'Just OK', 'Terrible');

    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

}