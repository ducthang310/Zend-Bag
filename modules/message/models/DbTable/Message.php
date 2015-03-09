<?php
class Message_Model_DbTable_Message extends Base_Model_DbTable_Backend
{
    protected $_name = 'message';
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

    public function getPrivateMessages($type = null, $id = null, $reader = null)
    {
        $select = $this->select();
        $select ->where('pickup_id = ?', $id)
                ->where('message_type = ?', $type)
                ->order('created ASC');
        if($reader != null){
            $select->where('to_login_id = ?' , $reader)
                    ->where('reader != ?' , $reader);
        };
        $result = $this->fetchAll($select)->toArray();
        return $result;
    }

    public function getNewMessage($type = null, $idPickup = null, $lastId = null, $newId = null)
    {
        $select = $this->select();
        $select ->where('pickup_id = ?', $idPickup)
                ->where('message_type = ?', $type);
            if($lastId != null){
                $select->where('id > ?', $lastId);
            }else{
                $select->where('id >= ?', $newId);
            }
            $select->order('created ASC');
        $result = $this->fetchAll($select)->toArray();
        return $result;
    }

    public function checkStatusMessage($type = null , $id = null){
        $auth = Zend_Auth::getInstance();
        switch(true){
            case !$auth->getStorage()->read() :
                break;
            case $auth->getStorage()->read()->auth_type == Auth_Constant_Server::COURIER_TYPE :
                $id_all = Message_Constant_Client::ALL_COURIER;
                break;
            case $auth->getStorage()->read()->auth_type == Auth_Constant_Server::CUSTOMER_TYPE :
                $id_all = Message_Constant_Client::ALL_CUSTOMER;
                break;
            case $auth->getStorage()->read()->auth_type == Auth_Constant_Server::STAFF_TYPE:
                $id_all = Message_Constant_Client::ALL_STAFF;
                break;
        }
        $select = $this->select();
        $to_login_where = 'to_login_id='.$id.' or to_login_id like "'.$id.',%" or to_login_id like "%,'.$id.',%" or to_login_id like "%,'.$id.'"';
        $reader_where = 'reader!='.$id.' and reader not like "'.$id.',%" and reader not like "%,'.$id.',%" and reader not like "%,'.$id.'"';
        $select->where('message_type = ' . $type)
            ->where($to_login_where)
            ->where($reader_where);
            $result = $this->fetchAll($select);
        if(count($result) == 0 ){
           return $this->_allMessage($id_all, $type ,$id);
        }else{
            return $result;
        }
    }

    private function _allMessage($id_all , $type ,$id_user){
        $reader_where = 'reader!='.$id_user.' and reader not like "'.$id_user.',%" and reader not like "%,'.$id_user.',%" and reader not like "%,'.$id_user.'"';
        $select = $this->select();
        $all_where = 'to_login_id='.$id_all.' or to_login_id like "'.$id_all.',%" or to_login_id like "%,'.$id_all.',%" or to_login_id like "%,'.$id_all.'"';
        $select->where('message_type = ' . $type)
            ->where($all_where)
            ->where($reader_where);
        $result = $this->fetchAll($select);
        return $result;
    }

    public function getAllBroadCastMessage($userId = null, $sort = null, $isSelect = false)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select();
        $select->from('message')
            ->order('created DESC');

        $sql = '';
        foreach ($userId as $key => $val) {
            $sql .= 'to_login_id like \'%,' . $val . ',%\''
                . ' or to_login_id like \'' . $val . ',%\''
                . ' or to_login_id like \'%,' . $val.'\' '
                . ' or to_login_id = \'' . $val.'\' ';
            if(($key+1) < count($userId)) $sql .= ' or ';
        }
        $select->where($sql);

        $select->where('message_type = ?', Message_Constant_Client::BROADCAST_MESSAGE);
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