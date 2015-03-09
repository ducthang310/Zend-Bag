<?php

class Courier_HistoryController extends Auth_Controller_Action_Backend
{
    public function initOther(){
        $auth = Zend_Auth::getInstance();
        if (!$auth->getStorage()->read()) {
            $url = '/auth/index/login';
            $this->redirect($url);
        }
        $auth_id = $auth->getStorage()->read()->id;
        $tmp = Courier_Model_DbTable_Courier::getInstance()->getDetail($auth_id);
        $detail = $tmp[0];
        $this->view->isApproved = false;
        if($detail['head_office_approved']){
            $this->view->isApproved = true;
        }
    }
    public function indexAction()
    {
        $this->getListPickUp('history.phtml');
    }
}