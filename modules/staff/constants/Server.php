<?php

class Staff_Constant_Server extends Base_Php_Overloader
{

    private static $_instance = NULL;
    const ROLE_CUSTOMER = 1;
    const ROLE_COURIER = 2;
    const ROLE_LOCAL_STAFF = 3;
    const ROLE_LOCAL_ADMIN = 4;
    const ROLE_INTERNAL_STAFF = 5;
    const ROLE_INTERNAL_ADMIN = 6;
    const ROLE_SUPER_ADMIN = 7;
    const DISABLE = 'Disabled';
    const ENABLE = 'Enabled';

    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    private static $_Option = array(
        Courier_Constant_Server::CAN_NOT_ASSIGN => array(
            'label' => 'No',
            'value' => Courier_Constant_Server::CAN_NOT_ASSIGN
        ),
        Courier_Constant_Server::CAN_ASSIGN => array(
            'label' => 'Yes',
            'value' => Courier_Constant_Server::CAN_ASSIGN
        ),

    );

    public static function getOption()
    {
        return self::$_Option;
    }
}