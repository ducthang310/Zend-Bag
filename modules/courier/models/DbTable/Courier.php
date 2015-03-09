<?php

class Courier_Model_DbTable_Courier extends Base_Model_DbTable_Backend
{
    protected $_name = 'courier';
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

    public function getDetail($id)
    {
        $result = $this->fetchAll('auth_id =' . $id);
        $result = $result->toArray();
        return $result;
    }

    public function getAll($sort = null, $searchParams = array(),$isSelect = false)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select();

        $select->from('courier', '*')
            ->joinLeft(
                array('auth' => 'auth'),
                'auth.id = courier.auth_id',
                array('disabled' => 'disabled')
            );

        $auth = Zend_Auth::getInstance();
        switch (true) {
            case !$auth->getStorage()->read():
                break;
            default:
                if (isset($searchParams['field']))
                    $this->filterByField($searchParams['field']);
                if (isset($auth->getStorage()->read()->detail->role))
                    $role = $auth->getStorage()->read()->detail->role;
                if ($role == Auth_Constant_Server::LOCAL_AREA_ADMIN || $role == Auth_Constant_Server::LOCAL_AREA_STAFF) {
                    $this->filterBySuburb($auth->getStorage()->read());
                }
                break;
        }
        if ($sort) {
            $select->order($sort);
        } else {
            $select->order('id DESC');
        }
        if ($this->_wheres)
            $select->where(implode(' and ', $this->_wheres));

        return $isSelect ? $select : $db->fetchAll($select);
    }

    public function filterBySuburb($auth)
    {
        $data = '(';
        if ($auth->detail->area_ids != 0) {
            $ids = "" . $auth->detail->area_ids;
            $subUrbs = Configuration_Model_DbTable_Suburb::getInstance()->getSuburbById($ids);
            if ($subUrbs) {
                $i = 1;
                foreach ($subUrbs as $subUrb) {
                    $data .= $i == 1 ? "`courier`.`suburb` LIKE  '%{$subUrb['suburb']}%' " : " OR `courier`.`suburb` LIKE  '%{$subUrb['suburb']}%'";
                    $i++;
                }
            }
            if ($data != '(') {
                $data .= ')';
                $this->_wheres[] = $data;
            }
        }
    }

    public function filterByField($textField = array())
    {
        if (count($textField)) {
            $data = array();
            foreach ($textField as $key => $val) {
                if ('' != trim($val)) {
                    if ($key == 'courier.auth_id')
                        $data[] = $this->getAdapter()->quoteInto(" {$key} like ?", trim($val) . '%');
                    else
                        $data[] = $this->getAdapter()->quoteInto(" {$key} like ?", '%' . trim($val) . '%');
                }
            }
            if (count($data)) {
                $this->_wheres[] = implode(' AND ', $data);
            }
        }
    }

    public function getCanAssignList()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select();

        $select->from('courier', '*')
            ->join(
                array('auth' => 'auth'),
                'auth.id = courier.auth_id ',
                array('disabled' => 'disabled')
            );
        $select->where(' head_office_approved = 1 ')
            ->where(' can_assign = 1 ')
            ->where('auth.disabled = 0');
        return $db->fetchAll($select);
    }

    public function getCourierBySuburb($subUrbs)
    {
        $data = '';
        if ($subUrbs) {
            $i = 1;
            foreach ($subUrbs as $subUrb) {
                $data .= $i == 1 ? "`courier`.`suburb` LIKE  '%{$subUrb['suburb']}%' " : " OR `courier`.`suburb` LIKE  '%{$subUrb['suburb']}%'";
                $i++;
            }
        }
        if ($data) {
            $db = Zend_Db_Table::getDefaultAdapter();
            $select = $db->select();
            $select->from('courier', 'auth_id');
            $select->where($data);
            if ($results = $db->fetchAll($select)) {
                if (!empty($results)) {
                    $ids = array();
                    foreach ($results as $result) {
                        $ids[] = $result['auth_id'];
                    }
                    return $ids;
                }
            }
        }
        return array();
    }
}