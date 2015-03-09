<?php

abstract class Base_Model_DbTable_Abstract extends Zend_Db_Table_Abstract
{

    const AREA_BACKEND = 'backend';
    const AREA_FRONTEND = 'frontend';
    protected $_area = NULL;
    protected $_name = NULL;
    protected static $_instance = NULL;

    public function __construct($config = array())
    {
        parent::__construct($config);
    }
}