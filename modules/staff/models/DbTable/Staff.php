<?php
class Staff_Model_DbTable_Staff extends Base_Model_DbTable_Backend
{
    protected $_name = 'staff';
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

    public function authenticate($username, $password)
    {
        if (!$username || !$password) {
            return false;
        }
        $auth = Zend_Auth::getInstance();
        $authAdapter = new Zend_Auth_Adapter_DbTable($this->getAdapter());
        $authAdapter->setTableName($this->_name)
            ->setIdentityColumn('app_email')
            ->setCredentialColumn('app_password');
        $authAdapter->setIdentity($username);
        $authAdapter->setCredential(sha1($password));
        if ($auth->authenticate($authAdapter)->isValid()) {
            $data = $authAdapter->getResultRowObject(null, array('app_password'));
            $auth->getStorage()->write($data);
            return true;
        }
        return false;
    }

    public function getAll($role = null, $sort = null, $isSelect = false)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select();
        $select->from('staff', '*')
            ->joinLeft(
                array('auth' => 'auth'),
                'auth.id = staff.auth_id',
                array('disabled' => 'disabled')

            )
        ;
        $auth = Zend_Auth::getInstance();
        $type = $auth->getStorage()->read()->auth_type;
        if ($type == Auth_Constant_Server::STAFF_TYPE) {
            $role = $auth->getStorage()->read()->detail->role;
        }
        switch(true){
            case Auth_Constant_Server::SUPER_ADMIN == $role:
                $select->where('role < ' . $role);
                break;
            case Auth_Constant_Server::INTERNATIONAL_ADMIN == $role:
                $array = array(
                    Auth_Constant_Server::INTERNATIONAL_STAFF,
                    Auth_Constant_Server::LOCAL_AREA_ADMIN,
                    Auth_Constant_Server::LOCAL_AREA_STAFF,
                );
                $select->where('role IN (?)', $array);
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

    public function getAllByLocalAdmin($area_ids = null, $sort = null, $isSelect = false)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select();
        $select->from('staff', '*')
            ->joinLeft(
                array('auth' => 'auth'),
                'auth.id = staff.auth_id',
                array('disabled' => 'disabled')
            );
        $sql = '';
        foreach ($area_ids as $key => $val) {
            $sql .= 'area_ids like \'%,' . $val . ',%\''
            . ' or area_ids like \'' . $val . ',%\''
            . ' or area_ids like \'%,' . $val.'\' '
            . ' or area_ids = \'' . $val.'\' ';
            if(($key+1) < count($area_ids)) $sql .= ' or ';
        }
        $select->where($sql);

        $select->where('role = ?', Auth_Constant_Server::LOCAL_AREA_STAFF);
        if ($sort) {
            $select->order($sort);
        } else {
            $select->order('id DESC');
        }
        if ($this->_wheres)
            $select->where(implode(' and ', $this->_wheres));

        return $isSelect ? $select : $db->fetchAll($select);
    }

}