<?php

class Staff_StaffController extends Base_Controller_Action_Backend
{
    private $session;
    public function initOther()
    {
        $auth = Zend_Auth::getInstance();
        //check type user and redirect
        if ($auth->getStorage()->read() != null) {
            $role = $auth->getStorage()->read()->detail->role;
            switch (true) {
                case (Auth_Constant_Server::STAFF_TYPE == $auth->getStorage()->read()->auth_type && ($role == 3 || $role == 5 || $role == 7)):
                    $url = '/staff';
                    break;
                default:
                    $url = '/auth/index/login';
                    $this->redirect($url);
                    break;
            }
        } else {
            $url = '/auth/index/login';
            $this->redirect($url);
        }
    }

    public function indexAction()
    {
        $auth = Zend_Auth::getInstance()->getStorage()->read();
        $role = $auth->detail->role;
        $this->_listAction($role);
    }

    public function generateHeadTitle()
    {
        $data = array(
            array(
                'title' => 'Email',
                'sort_field' => 'Email'
            ),
            array(
                'title' => 'Role',
                'sort_field' => 'Role'
            ),
            array(
                'title' => 'Status',
                'sort_field' => 'Status'
            ),
        );
        return $data;
    }

    private function _listAction($role)
    {
        $param_sort = $this->getRequest()->getParam('sort');
        $type = isset($param_sort['sort_type']) ? $param_sort['sort_type'] : null;
        $sort = isset($param_sort['sort_field']) ? $param_sort['sort_field'] : null;
        $sorts = null;
        $type_current = $type;
        if ($sort && $type) {
            $sorts = $sort . ' ' . $type;
        }
        $sort_session = new Zend_Session_Namespace('sort');
        if (isset($sort_session->sort)) {
            if ($sort_session->sort == $sort) {
                if ($type == 'ASC') {
                    $type = 'DESC';
                } else if ($type == 'DESC') {
                    $type = 'ASC';
                }
            } else {
                $sort_session->sort = $sort;
                $type = 'DESC';
            }
        } else {
            $sort_session->sort = $sort;
            $type = 'DESC';
        }
        $table = Staff_Model_DbTable_Staff::getInstance();
        $auth = Zend_Auth::getInstance()->getStorage()->read();
        $area_ids = $auth->detail->area_ids;
        $area_ids = explode(',', $area_ids);

        $page = $this->_getParam('page') ? $this->_getParam('page') : 1;
        if (isset($dataParams['btn_submit']))
            $page = 1;
        $dataParams['page'] = $page;
        $this->view->dataParams = $dataParams;
        $this->view->dataHeadTitle = $this->generateHeadTitle();
        $this->view->sort = array('sortType' => $type_current, 'sortField' => $sort, 'type' => $type);

        if ($role == Auth_Constant_Server::LOCAL_AREA_ADMIN) {
            $this->view->staffs = $this->paginatorAction($table->getAllByLocalAdmin($area_ids, $sort, true), $page);
        } else {
            $this->view->staffs = $this->paginatorAction($table->getAll($role, $sorts, true), $page);
        }

        $this->_response->setBody($this->view->render($this->_verifyScriptName('staff/list.phtml')));
    }
    public function createAction()
    {
        $auth = Zend_Auth::getInstance()->getStorage()->read();
        if ($auth) {
                $role = $auth->detail->role;
        } else {
                $role = null;
        }
        $area = $auth->detail->area_ids;
        switch (true) {
            case $role == Auth_Constant_Server::LOCAL_AREA_ADMIN :
                $area_ids = Configuration_Model_DbTable_Suburb::getInstance()->getSuburbById($area);
                break;
            default:
                $area_ids = Configuration_Model_DbTable_Suburb::getInstance()->getSuburb();
                break;
        }
        $this->view->role = $role;
        $this->view->area = $area_ids;
        $this->_response->setBody($this->view->render($this->_verifyScriptName('staff/new.phtml')));
    }
    public function validateAction()
    {
        if ($this->getRequest()->isPost()) {
            $type = $this->getRequest()->getParam('type');
//            $option = $this->getRequest()->getParam('option');
            $text = $this->getRequest()->getParam('text');
            $result = "unavailable";
            $registry = Zend_Registry::getInstance();
            $message= "";
                switch($type){
                    case "email":
                        if(!filter_var($text, FILTER_VALIDATE_EMAIL)) {
                            $message = "Oops. Looks like there is a problem with this email, please check it and try again.";
                        }else{
                            $message = 'available';
                        }
                        break;
                    case "app_email":
                        if(!filter_var($text, FILTER_VALIDATE_EMAIL)){
                            $message = "Oops. Looks like there is a problem with this email, please check it and try again.";
                        }else{
                            $auth_table = Auth_Model_DbTable_Auth::getInstance();
                            $where = $auth_table->getAdapter()->quoteInto('app_email = ?', $text);
                            $auth_email = $auth_table->fetchRow($where);
                            if(count($auth_email) == 0){
                                $message = 'available';
                            }else{
                                $message = Base_Helper_Message::$listMessage['registration']['customer']['app_email']['taken'];
                            }
                        }
                        break;
                    case "app_password":
                        if($text == null){
                            $message = "This field is required";
                        }else{
                            Zend_Registry::getInstance()->session->params->password = $text;
                            $message = 'available';
                        }
                        break;
                    case "re_password":
                        if($text == null){
                            $message = "This field is required";
                        }elseif($text == Zend_Registry::getInstance()->session->params->password ){
                            $message = 'available';
                        }else{
                            $message = "Re-Password and Password not match" ;
                        }
                        break;
                }
            $clientData = array(
                'message' => $message,
                'result' => $result,
            );
            $this->_response->setBody($this->_helper->getHelper('json')->encodeJson($clientData));
        }
    }
    public function addAction()
    {
        $registry = Zend_Registry::getInstance();
        if ($registry->session->success->create) {
            Zend_Registry::getInstance()->session->success->create = '';
        }
        if ($registry->session->error->create) {
            Zend_Registry::getInstance()->session->error->create = '';
        }
        $result = Base_Constant_Client::SUCCESSFUL;
        $url = "";
        if ($this->getRequest()->isPost() && $this->_request->isPost('create')) {
            $params = $this->_request->getParams();
            $auth_table = Auth_Model_DbTable_Auth::getInstance();
            $where = $auth_table->getAdapter()->quoteInto('app_email = ?', $params['app_email']);
            $auth_email = $auth_table->fetchRow($where);
            $message = array();
            $data = array();
            $inputRegx = array(
                'app_email' => 'required',
                'app_password' => 'required',
                're_password' => 'required',
                'role' => 'required',
                'email' => 'email',
            );
            foreach($inputRegx as $key => $value){
                if(!$params[$key] && !$params[$key] == 'email' && !$params[$key] == 'area_ids'){
                    $message[$key] = "This field is required";
                }else{
                    switch($key){
                        case "app_email":
                            if(!filter_var($params['app_email'], FILTER_VALIDATE_EMAIL)){
                                $message[$key] = Base_Helper_Message::$listMessage['registration']['customer'][$key]['message'];
                            }elseif(count($auth_email) <> 0){
                                $message[$key] = Base_Helper_Message::$listMessage['registration']['customer'][$key]['taken'];
                            }
                            break;
                        case "email":
                            if(!filter_var($params['email'], FILTER_VALIDATE_EMAIL)){
                                $message[$key] = 'We\'re sorry, that email is error syntax.';
                            }
                            break;
                        case "app_password" :
                            if($params[$key] == null && count($params[$key]) < 8) {
                                $message[$key] = Base_Helper_Message::$listMessage['registration']['customer'][$key]['message'];
                            }
                            break;
                        case "re_password":
                            if($params[$key] != $params['app_password']) {
                                $message[$key] = "Re-Password and Password not match" ;
                            }
                            break;
                        case "role":
                            if($params[$key] == Auth_Constant_Server::LOCAL_AREA_STAFF || $params[$key] == Auth_Constant_Server::LOCAL_AREA_ADMIN) {
                                if($params['area_ids'] == null){
                                    $message['area_ids'] = "Please select area for Local Admin Staff/Admin";
                                }
                            }
                            break;
                    }
                }
            }

            Zend_Registry::getInstance()->session->success->fieldParams = $params;
            switch (true) {
                case $message != null:
                    $registry->session->error->create = $message;
                    $url = "/staff/staff/create";
                    break;
                default:
                        if (isset($params['area_ids'])) {
                            $area_ids = implode(',', $params['area_ids']);
                        }else{
                            $area_ids = 0;
                        }
                    if ($result != Base_Constant_Client::FAILED){
                        $auth_data = array(
                            'app_email' => $params['app_email'],
                            'app_password' => sha1($params['app_password']),
                            'auth_type' => Auth_Constant_Server::STAFF_TYPE,
                            'disabled' => '0',
                        );
                    $newAuthId = $auth_table->insert($auth_data);
                    $table = Staff_Model_DbTable_Staff::getInstance();
                    $staff_data = array(
                        'auth_id' => (int)$newAuthId,
                        'email' => $params['email'],
                        'role' => $params['role'],
                        'area_ids' => $area_ids,
                        'created' => date("Y-m-d H:i:s", time()),
                        'updated' => date("Y-m-d H:i:s", time()),
                    );
                    $table->insert($staff_data);
                    $url = "/staff/staff";
                    $registry->session->success->create = array(
                        'message' => 'The staff has been created successfully!');
                    }
                        Zend_Registry::getInstance()->session->error->create = '';
                        Zend_Registry::getInstance()->session->success->fieldParams = '';
                    break;
            }

        }
        $this->redirect($url);
    }

    public function editAction()
    {
        $id = $this->getRequest()->getParam('id', 0);
        $message = "";
        $auth_tbl = Auth_Model_DbTable_Auth::getInstance();
        $table = Staff_Model_DbTable_Staff::getInstance();
        $result = Base_Constant_Client::SUCCESSFUL;
        if ($this->getRequest()->getParam('click')) {
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
                        $data['suburbs'] = Configuration_Model_DbTable_Suburb::getInstance()->getSuburb();
                        foreach ($staffs as $key => $value) {
                            $data['staff'] = array(
                                'app_email' => $auths[0]['app_email'],
                                'auth_id' => $value['auth_id'],
                                'id' => $value['id'],
                                'email' => $value['email'],
                                'role' => $value['role'],
                                'area_ids' => $value['area_ids'],
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
            $clientData = array(
                'message' => $message,
                'result' => $result,
                'detail' => $this->view->render($this->_verifyScriptName('staff/update.phtml')),
            );
            $this->_response->setBody($this->_helper->getHelper('json')->encodeJson($clientData));

        }
    }

    public function updateAction(){
        $auth_tbl = Auth_Model_DbTable_Auth::getInstance();
        $table = Staff_Model_DbTable_Staff::getInstance();
        $registry = Zend_Registry::getInstance();
        $params = $this->getRequest()->getParams();
        $result = Base_Constant_Client::SUCCESSFUL;
        $message = "";
        if ($this->_request->isPost() && $this->_request->isPost('create')) {
            if (!filter_var($params['email'], FILTER_VALIDATE_EMAIL)) {
                $message = "That email address has error syntax";
                $result = Base_Constant_Client::FAILED;
                $registry->session->success->updatebase = array(
                    'message' => 'Updated fail');
                $url = '';
            } else {
                $where = $auth_tbl->getAdapter()->quoteInto('auth_id = ?', $params['auth_id']);
                $staff = Auth_Constant_Server::LOCAL_AREA_STAFF;
                $admin = Auth_Constant_Server::LOCAL_AREA_ADMIN;
                $area_ids = 0;
                if($params['role'] == $staff || $params['role'] == $admin){
                    if(isset($params['area_ids']) && $params['area_ids']!= null){
                        $area_ids = implode(',', $params['area_ids']);
                    }else{
                        $result = Base_Constant_Client::FAILED;
                        $message = "Please select area for local staff/admin";
                        $url = '';
                    }
                }
                if($message == ""){
                    $data['profile'] = array(
                        'email' => $params['email'],
                        'role' => $params['role'],
                        'area_ids' => $area_ids,
                        'updated' => date("Y-m-d H:i:s", time())
                    );
                    $where2 = $auth_tbl->getAdapter()->quoteInto('id = ?', $params['auth_id']);
                    if ($params['password'] != null) {
                        $data['auth'] = array(
                            "app_password" => sha1($params['password']),
                        );
                        $auth_tbl->update($data['auth'], $where2);
                    }
                    $table->update($data['profile'], $where);
                    $message = "success";
                    $result = Base_Constant_Client::SUCCESSFUL;
                    $registry->session->success->updatebase = array(
                        'message' => 'Updated successfully!');
                    $url = '/staff/staff';
                }
            }
        }
        $clientData = array(
            'message' => $message,
            'result' => $result,
            'url' => $url,
        );
        $this->_response->setBody($this->_helper->getHelper('json')->encodeJson($clientData));
    }
    public function disableAction()
    {
        $id = $this->getRequest()->getParam('id', 0);
        $auth_table = Staff_Model_DbTable_Auth::getInstance();
        $auths = $auth_table->fetchAll('id = ' . $id);
        $total = count($auths);
        switch ($total) {
            case 0:
                $this->forward('error', 'index', 'base');
                break;
            case 1:
                $this->view->auth = $auths[0];
                break;
            default:
                throw new Exception('Too many ' . $id);
                break;
        }
        if ($auths[0]['disabled'] == 1)
            $auth_data = array(
                'disabled' => '0',
            );
        else if ($auths[0]['disabled'] == 0)
            $auth_data = array(
                'disabled' => '1',
            );
        $result = Base_Constant_Client::SUCCESSFUL;
        $message = '';
        try {
            $where = $auth_table->getAdapter()->quoteInto('id = ?', $id);
            $auth_table->update($auth_data, $where);
        } catch (Zend_Db_Exception $dbException) {
            $result = Base_Constant_Client::FAILED;
            $message = $dbException->getMessage();
        }
        $clientData = array(
            'result' => $result,
            'message' => $message,
            'params' => $auth_data,
        );
        $this->redirect('staff/staff/list');
    }
}