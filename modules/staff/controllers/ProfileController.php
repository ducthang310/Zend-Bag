<?php

class Staff_ProfileController extends Base_Controller_Action_Backend
{
    public function _construct()
    {
        $auth = Zend_Auth::getInstance();
        //check type user and redirect
        if ($auth->getStorage()->read() == null) {
            $this->redirect('/auth');
        } elseif ($auth->getStorage()->read()->auth_type != Auth_Constant_Server::STAFF_TYPE) {
            $this->redirect('/');
        }
    }

    public function indexAction()
    {
        $this->viewAction();
    }

    public function viewAction()
    {
        $auth = Zend_Auth::getInstance();
        $auth_tbl = Auth_Model_DbTable_Auth::getInstance();
        $table = Staff_Model_DbTable_Staff::getInstance();
        $id = $auth->getStorage()->read()->detail->auth_id;
        if ($id != (string)(int)$id || $id <= 0) {
            $this->forward('error', 'index', 'base');
        } else {
            $auths = $auth_tbl->fetchAll('id = ' . $id);
            $staffs = $table->fetchAll('auth_id = ' . $id);
            $total = count($staffs);
            switch (true) {
                case count($auths) == 0:
                    $result = Base_Constant_Client::FAILED;
                    $this->forward('error', 'index', 'base');
                    break;
                case count($staffs) == 1 && count($auths) == 1:
                    foreach($staffs as $key => $value){
                        $data = array(
                            'auth_id' => $value['auth_id'],
                            'id' => $value['id'],
                            'email' => $value['email'],
                            'role' => $value['role'],
                            'area_ids' => Configuration_Model_DbTable_Suburb::getInstance()->getSuburbById($value['area_ids'])
                        );
                    };
                    $this->view->staff = $data;
                    break;
                default:
                    $result = Base_Constant_Client::FAILED;
                    throw new Exception('Too many ' . $id);
                    break;
            }
        }
        $this->_response->setBody($this->view->render($this->_verifyScriptName('profile/detail.phtml')));
    }

    public function updateAction()
    {
        $params = $this->_request->getParams();
        $auth = Zend_Auth::getInstance();
        $auth_id = $auth->getStorage()->read()->detail->auth_id;
        $result = Base_Constant_Client::SUCCESSFUL;
        $registry = Zend_Registry::getInstance();
        $message = '';
        if ($this->getRequest()->isPost() && $this->_request->isPost('update')) {
            if($registry->session->success->updateprofile){
                Zend_Registry::getInstance()->session->success->updateprofile = '';
            }
            if($registry->session->error->updateprofile){
                Zend_Registry::getInstance()->session->error->updateprofile = '';
            }
                $auth_tbl = Auth_Model_DbTable_Auth::getInstance();
                $table = Staff_Model_DbTable_Staff::getInstance();
                $data = array();
                if ($params['app_password'] != null) {
                    $data = array(
                        'app_password' => sha1($params['app_password']),
                    );
                }
            if(!filter_var($params['email'], FILTER_VALIDATE_EMAIL)){
                $registry->session->error->updateprofile = array(
                    'message' => 'That email address has error syntax');
                $this->redirect('staff/profile');
            }else{
                $data2 = array(
                    'email' => $params['email']
                );
                try {
                    if ($data != null) {
                        $where = $auth_tbl->getAdapter()->quoteInto('id = ?', $auth_id);
                        $auth_tbl->update($data, $where);
                    }
                    $where2 = $table->getAdapter()->quoteInto('auth_id = ?', $auth_id);
                    $table->update($data2, $where2);
                    $registry->session->success->updateprofile = array(
                        'message' => 'Updated Success !');
                } catch (Zend_Db_Exception $dbException) {
                    $result = Base_Constant_Client::FAILED;
                }
            }

            $clientData = array(
                'result' => $result,
                'message' => $message,
                'url' => '/staff/profile',
            );

        }
        $this->redirect('staff/profile');
    }
}