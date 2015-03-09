<?php
class Courier_Model_DbTable_Pickup extends Base_Model_DbTable_Backend
{
    protected $_name = 'pickup';
    protected static $_instance = NULL;

    public function __construct($config = array())
    {
        parent::__construct($config);
    }
    public static function getInstance() {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
}