<?php

class Pickup_Model_DbTable_Pickup extends Base_Model_DbTable_Backend
{
    protected $_name = 'pickup';
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

    public function getAll($status = null, $sort = null, $searchParams = array(), $isSelect = false)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select();
        $select->from('pickup', '*')
            ->joinLeft(
                array('customer' => 'customer'),
                'customer.auth_id = pickup.customer_id',
                array('customer_firstname' => 'firstname',
                    'customer_lastname' => 'lastname',
                    'customer_search_name'=>'search_name'
                )
            )
            ->joinLeft(
                array('courier' => 'courier'),
                'courier.auth_id = pickup.courier_id',
                array('courier_firstname' => 'firstname',
                    'courier_lastname' => 'lastname',
                    'courier_search_name'=>'search_name'
                )
            );

        $auth = Zend_Auth::getInstance();
        if ($auth->getStorage()->read()) {
            $id = $auth->getStorage()->read()->id;
            $type = $auth->getStorage()->read()->auth_type;
        }
        switch (true) {
            case !$auth->getStorage()->read():
                break;
            case $type == Auth_Constant_Server::CUSTOMER_TYPE:
                $this->_wheres[] = ' pickup.customer_id = ' . $id;
                $this->getCondition($status, $searchParams, 'customer');
                break;
            case $type == Auth_Constant_Server::COURIER_TYPE:
                $this->_wheres[] = Zend_Registry::getInstance()->controllerName == 'list' ? ' pickup.courier_id IS NULL' : ' pickup.courier_id = ' . $id;
                $this->getCondition($status, $searchParams, 'courier');
                if(Zend_Registry::getInstance()->controllerName=='list')
                $this->filterBySubUrbs($auth->getStorage()->read(),'courier');
                break;
            case $type == Auth_Constant_Server::STAFF_TYPE:
                if (isset($searchParams['field']))
                    $this->filterByField($searchParams['field']);
                $this->filterBySubUrbs($auth->getStorage()->read());
                $this->getCondition($status, $searchParams, 'staff');
                break;
        }
        if ($sort) {
            $select->order($sort);
        } else {
            if($this->_sort_id)
            $select->order('pickup.id ASC');
            else
            $select->order('pickup.id DESC');
        }
        // filter created
        if (isset($searchParams['date']['date_from']) && '' != $searchParams['date']['date_from'])
            $this->_wheres[] = $this->getAdapter()->quoteInto(" pickup.created >= ? ", $searchParams['date']['date_from'] . ' 00:00:00');
        if (isset($searchParams['date']['date_to']) && '' != $searchParams['date']['date_to'])
            $this->_wheres[] = $this->getAdapter()->quoteInto(" pickup.created <= ? ", $searchParams['date']['date_to'] . ' 23:59:59');
        if (isset($searchParams['field']['destination']) && trim($searchParams['field']['destination'])) {
            $this->_wheres[] = $this->getAdapter()->quoteInto(" pickup.to_address like ?", '%' . trim($searchParams['field']['destination']) . '%');
        }
        if ($this->_wheres)
            $select->where(implode(' and ', $this->_wheres));

        return $isSelect ? $select : $db->fetchAll($select);
    }

    public function checkStatus($pickup_id,$status)
    {
        $table = Pickup_Model_DbTable_Pickup::getInstance();
        $result = $table->fetchAll('id = ' . $pickup_id);
        return $result[0]['status'];
    }

    public function getIdCustomer($pickup_id){
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
                    ->from(array($this->_name) ,
                             array('customer_id')
                        )
                    ->where('id = ?', $pickup_id);
        $result = $db->fetchAll($select);
        return $result;
    }

    public function getIdCourier($pickup_id){
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
            ->from(array($this->_name) ,
                array('courier_id')
            )
            ->where('id = ?', $pickup_id);
        $result = $db->fetchAll($select);
        return $result;
    }

    public function getReportPicktup($from , $to){
            $db = Zend_Db_Table::getDefaultAdapter();
            $select = $db->select();
            $select->from('pickup', '*')
                ->joinLeft(
                    array('customer' => 'customer'),
                    'customer.auth_id = pickup.customer_id',
                    array('customer_firstname' => 'firstname',
                        'customer_lastname' => 'lastname',
                        'customer_search_name'=>'search_name'
                    )
                )
                ->joinLeft(
                    array('courier' => 'courier'),
                    'courier.auth_id = pickup.courier_id',
                    array('courier_firstname' => 'firstname',
                        'courier_lastname' => 'lastname',
                        'courier_search_name'=>'search_name'
                    )
                );
            $select->where(' pickup.created >= ? ', $from);
            $select->where(' pickup.created < ? ', $to);
            $auth = Zend_Auth::getInstance();
            if ($auth->getStorage()->read()) {
                $id = $auth->getStorage()->read()->id;
                $type = $auth->getStorage()->read()->auth_type;
            }
            $this->filterBySubUrbs($auth->getStorage()->read());
            return $db->fetchAll($select);
    }
}