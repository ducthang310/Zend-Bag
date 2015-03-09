<?php

class Configuration_Model_DbTable_Configuration extends Base_Model_DbTable_Backend
{
    protected $_name = 'configuration';
    protected static $_instance = NULL;

    public function __construct($config = array())
    {
        parent::__construct($config);
    }

    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function getAll()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select();
        $select->from('configuration', '*');
        return $db->fetchAll($select);
    }
}