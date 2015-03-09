<?php
class Auth_Model_DbTable_Auth extends Base_Model_DbTable_Backend
{
    protected $_name = 'auth';
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
    public function authenticate($email, $password)
    {
        if (!$email || !$password) {
            return false;
        }
        $auth = Zend_Auth::getInstance();
        $authAdapter = new Zend_Auth_Adapter_DbTable($this->getAdapter());
        $authAdapter->setTableName($this->_name)
            ->setIdentityColumn('app_email')
            ->setCredentialColumn('app_password');
        $authAdapter->setIdentity($email);
        $authAdapter->setCredential(sha1($password));
        if ($auth->authenticate($authAdapter)->isValid()) {
            $data = $authAdapter->getResultRowObject(null, array('password'));
            $data->area = $this->_area;
            $auth->getStorage()->write($data);
            //check user has disabled ?
            if($data->disabled == 1){
                Zend_Registry::getInstance()->session->auth->error = 'This account has been disabled.';
                return false;
            }

            switch (true){
                case (Auth_Constant_Server::STAFF_TYPE == $data->auth_type):
                    $table = Staff_Model_DbTable_Staff::getInstance();
                    break;
                case (Auth_Constant_Server::CUSTOMER_TYPE == $data->auth_type):
                    $table = Customer_Model_DbTable_Customer::getInstance();
                    break;
                case (Auth_Constant_Server::COURIER_TYPE == $data->auth_type):
                    $table = Courier_Model_DbTable_Courier::getInstance();
                    break;
                default:
                    Zend_Registry::getInstance()->session->auth->error = 'This account unknown type.';
                    break;
            }

            $where = $table->getAdapter()->quoteInto('auth_id = ?', $data->id);
            $users = $table->fetchAll($where);
            switch (count($users)){
                case 0:
                    $auth->clearIdentity();
                    Zend_Registry::getInstance()->session->auth->error = 'This login has error.';
                    return false;
                    break;
                case 1:
                    $data->detail = $users[0];
                    $auth->getStorage()->write($data);
                    return true;
                    break;
                default:
                    $auth->clearIdentity();
                    Zend_Registry::getInstance()->session->auth->error = 'Error. Too much accounts with same ID.';
                    return false;
                    break;
            }
        }
        Zend_Registry::getInstance()->session->auth->error = "Oops. That password does not match the email you have entered. Please try again.";
//        Zend_Registry::getInstance()->session->auth->blankPass = 0;
        return false;
    }
    public function getAuth($id)
    {
        $result = $this->fetchAll('id ='.  $id);
        $result = $result->toArray();
        return $result;
    }
}