<?php

class Auth_IndexController extends Auth_Controller_Action_Backend
{
    private $eMessage;

    public function initOther(){
        $this->eMessage = Base_Helper_Message::getInstance();
    }
    public function indexAction(){
        $auth = Zend_Auth::getInstance();
        //check type user and redirect
        if($auth->getStorage()->read()!=null){
            switch (true){
                case (Auth_Constant_Server::STAFF_TYPE == $auth->getStorage()->read()->auth_type):
                    $url = '/staff';
                    break;
                case (Auth_Constant_Server::CUSTOMER_TYPE == $auth->getStorage()->read()->auth_type):
                    $url = '/customer';
                    break;
                case (Auth_Constant_Server::COURIER_TYPE == $auth->getStorage()->read()->auth_type):
                    $url = '/courier';
                    break;
                default:
                    break;
            }
        }else{
            $url = '/auth/index/login';
        }
        $this->redirect($url);
    }
    public function loginAction()
    {
        Zend_Registry::getInstance()->session->auth->blankUser = 1;
        Zend_Registry::getInstance()->session->auth->blankPass = 1;
        if ($this->_request->isPost() && $this->_request->isPost('login')) {
            $username = $this->_request->getParam('email');
            $password = $this->_request->getParam('password');
            $remember = $this->_request->getParam('remember');

            $valid = new Base_Controller_Helper_Validate();
            if(!$valid->validation($username,"email")){
                Zend_Registry::getInstance()->session->auth->error = $this->eMessage->log_in->customer_courier->email->instruction;
                $this->redirect('/auth');
            }

            Zend_Registry::getInstance()->session->auth->email = $username;
            if(!$username || $username == null){
                Zend_Registry::getInstance()->session->auth->blankUser = 0;
            }

            if(!$password || $password == null){
                Zend_Registry::getInstance()->session->auth->blankPass = 0;
            }

            $user = Auth_Model_DbTable_Auth::getInstance();
            $where = $user->getAdapter()->quoteInto('app_email = ?', $username);
            $info = $user->fetchRow($where);
            if(!$info){
                Zend_Registry::getInstance()->session->auth->existEmail = $this->eMessage->log_in->customer_courier->email->message;
                $this->redirect('/auth');
            }

            if( $info && $info->login_fail == 2){
                $data = array(
                    'login_fail' => 0,
                );
                $user->update($data, $where);
                $this->redirect('/auth/index/forgot');
            }
            else {

                if ($user->authenticate($username, $password)) {
                    $destination = Zend_Registry::getInstance()->session->destination;

                    if ($remember) {
                        Zend_Session::RememberMe((int)Zend_Registry::getInstance()->appConfig['auth']['time_remember_login']);
                    } else {
                        Zend_Session::ForgetMe();
                    }

                    $data = array(
                        'login_fail' => 0,
                    );
                    $user->update($data, $where);

                    if (isset($destination->url)) {
                        $url = $destination->url;
                        unset($destination->url);
                        $this->redirect($url);
                    }
                    $this->redirect('/auth');
                } elseif($info){
                    $data = array(
                        'login_fail' => ($info->login_fail + 1),
                    );
                    Zend_Registry::getInstance()->session->auth->count_fail = 'You have '.(2 - $info->login_fail).' times left to login with valid username and password ';
                    $user->update($data, $where);
                }
            }
        }

        if(Zend_Registry::getInstance()->session->auth){
            $this->view->error = Zend_Registry::getInstance()->session->auth->error;
            $this->view->blankPass = Zend_Registry::getInstance()->session->auth->blankPass;
            $this->view->blankUser = Zend_Registry::getInstance()->session->auth->blankUser;
            $this->view->email = Zend_Registry::getInstance()->session->auth->email;
            $this->view->count_fail = Zend_Registry::getInstance()->session->auth->count_fail;
            $this->view->existEmail = Zend_Registry::getInstance()->session->auth->existEmail;

            unset(Zend_Registry::getInstance()->session->auth);
        }

        Zend_Layout::getMvcInstance()->setLayout('layout');
        $this->_response->setBody($this->view->render($this->_verifyScriptName('login-form.phtml')));
    }

    public function logoutAction()
    {
        $auth = Zend_Auth::getInstance();
        $auth->clearIdentity();
        Zend_Session::destroy();
        $this->redirect('/auth/index/login');
    }

    public function noaccessAction()
    {
        Zend_Layout::getMvcInstance()->setLayout('no_access');
        echo $this->view->render($this->_verifyScriptName('no_access.phtml'));
    }

    public function forgotAction()
    {
        if ($this->_request->isPost() && $this->_request->isPost('forgot')) {
            $email = $this->_request->getParam('email');
            $user = Auth_Model_DbTable_Auth::getInstance();
            $where = $user->getAdapter()->quoteInto('app_email = ?', $email);
            $result = $user->fetchAll($where);
            $total = count($result);

            if($total){
                try {
                    $pass = $this->randomPassAction();
                    $data = array(
                        "app_password" => sha1($pass)
                    );

                    $content = array(
                        "sender" => Zend_Registry::getInstance()->appConfig['smtp']['username'],
                        "nameSender" => "DLIVR",
                        "recipient" => $email,
                        "subject" => "Forgot email",
                        "body" => "New password from DLIVR: ".$pass
                    );
                    if(Base_Helper_Mail::sendMail($content)){
                        $user->update($data, $where);
                        Zend_Registry::getInstance()->session->forgot->success = "Please check new password in email " . $email;
                        $this->redirect('/auth/index/forgot');
                    }
                    else{
                        Zend_Registry::getInstance()->session->auth->error = 'Sorry. System alert email has some problems. Please contact system
Administrator !';
                        Base_Helper_Log::getInstance()->log(PHP_EOL . date('H:i:s :::: ') . ' [AUTH] Can not send mail to ' . $email);
                    }
                } catch (Zend_Db_Exception $dbException) {
                    Zend_Registry::getInstance()->session->auth->error = 'Error !';
                }
            }
            else{
                Zend_Registry::getInstance()->session->auth->error = $this->eMessage->log_in->customer_courier->email->message;
            }
        }
        if(Zend_Registry::getInstance()->session->auth){
            $this->view->error = Zend_Registry::getInstance()->session->auth->error;
            Zend_Registry::getInstance()->session->auth->error = null;
        }
        Zend_Layout::getMvcInstance()->setLayout('layout');
        echo $this->view->render($this->_verifyScriptName('forgot-form.phtml'));
    }

    function randomPassAction() {
        $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
        $pass = array();
        $alphaLength = strlen($alphabet) - 1;
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass);
    }
}