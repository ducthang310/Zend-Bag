<?php

class Message_Constant_Client extends Base_Php_Overloader
{
    const FAILED = 'failed';

    const SUCCESSFUL = 'successful';

    const EXPIRED = 'expired';

    const ALL_CUSTOMER = -1;
    const ALL_COURIER = -2;
    const ALL_STAFF = -3;

    const BROADCAST_MESSAGE = 1;
    const PRIVATE_MESSAGE = 2;

    private static $_instance = NULL;

    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

   public static $_message_label = array(
       Auth_Constant_Server::COURIER_TYPE => "ALL COURIER",
       Auth_Constant_Server::CUSTOMER_TYPE => 'ALL CUSTOMER',
       Auth_Constant_Server::STAFF_TYPE => "ALL STAFF"
   );

}