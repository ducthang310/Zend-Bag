<?php

class Pickup_Constant_Server extends Base_Php_Overloader
{

    private static $_instance = NULL;
    const AWAITING = 1;
    const ASSIGNED = 2;
    const PICKED_UP = 3;
    const DELIVERED = 4;
    const ACCEPTED = 5;
    const RATED = 6;
    const CANCELLED = 7;
    const ITEM_PER_PAGE = 6;
    const UNASSIGNED = 1;

    public static $_STATUS = array(
        self::AWAITING => 'AWAITING',
        self::ASSIGNED => 'ASSIGNED',
        self::PICKED_UP => 'PICKED_UP',
        self::DELIVERED => 'DELIVERED',
        self::ACCEPTED => 'ACCEPTED',
        self::RATED => 'RATED',
        self::CANCELLED => 'CANCELLED',
    ) ;

    public static $_STATUS_ACTION = array(
        self::AWAITING => 'AWAITING',
        self::ASSIGNED => 'ASSIGN',
        self::PICKED_UP => 'PICK UP',
        self::DELIVERED => 'DELIVER',
        self::ACCEPTED => 'ACCEPT',
        self::RATED => 'ADD RATE',
        self::CANCELLED => 'CANCEL',
    ) ;

    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function getValue($key){
        return self::$_STATUS[$key]['value'];
    }

    public function getLabel($key){
        return self::$_STATUS[$key]['label'];
    }
}