<?php

class Configuration_Model_DbTable_Suburb extends Base_Model_DbTable_Backend
{
    protected $_name = 'suburbs';
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

    public function getRegion()
    {

        $result = $this->fetchAll($this->select()->from($this, array('region' => new Zend_Db_Expr('Distinct region'))));
        $result = $result->toArray();
        sort($result);
        return $result;
    }

    public function getSuburbByRegion($region)
    {
        $where = $this->getAdapter()->quoteInto('region = ?', $region);
        $result = $this->fetchAll(
            $this->select()->from($this, array('suburb' => new Zend_Db_Expr('distinct suburb'), 'id'))
                ->where($where)
        );
        $result = $result->toArray();
        sort($result);
        return $result;
    }

    public function getIdSuburbByRegion($region)
    {
        $where = $this->getAdapter()->quoteInto('region = ?', $region);
        $result = $this->fetchAll(
            $this->select()->from($this, array('id' => new Zend_Db_Expr('distinct id')))
                ->where($where)
        );
        $result = $result->toArray();
        sort($result);
        return $result;
    }
    public function getSuburb()
    {
        $result = $this->fetchAll($this->select()->from($this, array('suburb' => new Zend_Db_Expr('distinct suburb'), 'id','region')));
        $result = $result->toArray();
        sort($result);
        return $result;
    }
    public function getCountry()
    {
        $result = $this->fetchAll($this->select()->from($this, array('country' => new Zend_Db_Expr('distinct country'))));
        $result = $result->toArray();
        sort($result);
        return $result;
    }
    public function getState()
    {
        $result = $this->fetchAll($this->select()->from($this, array('state' => new Zend_Db_Expr('distinct state'))));
        $result = $result->toArray();
        sort($result);
        return $result;
    }
    public function getSuburbById($ids)
    {
        $where = " id IN ({$ids})";
        $result = $this->fetchAll(
            $this->select()->from($this, array('suburb' => new Zend_Db_Expr('distinct suburb'), 'id'))
                 ->where($where)
        );
        $result = $result->toArray();
        sort($result);
        return $result;
    }

    public function getPostCodeById($ids)
    {
        $where = " id IN ({$ids})";
        $result = $this->fetchAll(
            $this->select()->from($this, array('postcode' => new Zend_Db_Expr('distinct postcode'), 'id'))
                ->where($where)
        );
        $result = $result->toArray();
        sort($result);
        return $result;
    }

    public function getAll($sort = null, $isSelect = false)
    {
        $table = Configuration_Model_DbTable_Suburb::getInstance();
        $select = $table->select();
        if ($sort) {
            $select->order($sort);
        } else {
            $select->order('id DESC');
        }
        return $isSelect ? $select : $table->fetchAll($select);
    }

    public function getSuburbByUrb($urb = 'Sydney City')
    {
        $where = $this->getAdapter()->quoteInto(" suburb = ?",$urb);
        $result = $this->fetchAll(
            $this->select()->from($this, array('suburb' => new Zend_Db_Expr('distinct suburb'), 'id'))
                ->where($where)
        );
        $result = $result->toArray();
        sort($result);
        return $result;
    }
}