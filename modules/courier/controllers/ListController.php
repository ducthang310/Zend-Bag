<?php

class Courier_ListController extends Auth_Controller_Action_Backend
{
    public function initOther()
    {
        $auth = Zend_Auth::getInstance();
        //check type user and redirect
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
        } else {
            $url = '/auth/index/login';
            $this->redirect($url);
        }
    }

    public function indexAction()
    {
        $this->getListPickUp();
    }
}