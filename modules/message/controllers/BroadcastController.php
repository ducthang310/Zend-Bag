<?php

class Message_BroadcastController extends Message_Controller_Action_Base
{
    public function boxAction()
    {
        $courier = Courier_Model_DbTable_Courier::getInstance()->getAll();
        $lists = array();
        foreach($courier as $key => $value){
            if($value['is_company'] == Courier_Constant_Server::IS_COMPANY){
                $lists['courier'][$value['auth_id']] = array(
                    "full_name" => $value['company_name'],
                ) ;
            }else{
                $lists['courier'][$value['auth_id']] = array(
                    "full_name" => $value['contact_lastname'],
                ) ;
            }
        }
        $staff = Staff_Model_DbTable_Staff::getInstance()->getAll();
        foreach($staff as $key => $value){
                $lists['staff'][$value['auth_id']] = array(
                    "email" => $value['email'],
                ) ;
        }
        $customer = Customer_Model_DbTable_Customer::getInstance()->getAll();
        foreach($customer as $key => $value){
            $lists['customer'][$value['auth_id']] = array(
                "full_name" => $value['firstname'],
            );
        };
        if(isset($lists['staff']) && $lists['staff'] != null){
            $this->view->staff = $lists['staff'];
        }
        if(isset($lists['customer']) && $lists['customer'] != null){
            $this->view->customer = $lists['customer'];
        }
        if(isset($lists['courier']) && $lists['courier'] != null){
            $this->view->courier = $lists['courier'];
        }
        $this->_response->setBody($this->view->render($this->_verifyScriptName('common/message.phtml')));
    }

    public function indexAction(){
        $user = Zend_Auth::getInstance()->getStorage()->read();
        $id_user = $user->detail->auth_id;
        $type_message = Message_Constant_Client::BROADCAST_MESSAGE;
        $table = Message_Model_DbTable_Message::getInstance();
        $data = $table->select()->order('created DESC')
                ->where('from_login_id = ' . $id_user)
                ->where('message_type = ' .$type_message);

        $page = $this->_getParam('page') ? $this->_getParam('page') : 1;
        if (isset($dataParams['btn_submit']))
            $page = 1;
        $dataParams['page'] = $page;
        if($data!=null){
            $this->view->dataParams = $dataParams;
            $this->view->data = $this->paginatorAction($data, $page);
        }
        $this->_response->setBody($this->view->render($this->_verifyScriptName('common/box.phtml')));
    }
    public function statusAction(){
        $user_id = Zend_Auth::getInstance()->getStorage()->read()->detail->auth_id;
        $type = Message_Constant_Client::BROADCAST_MESSAGE;
        $result = Message_Model_DbTable_Message::getInstance()->checkStatusMessage($type,$user_id);
        if(isset($result) && count($result) == 0){
            $status = false;
        }else{
            $status = true;
        }
        $clientData = array(
            'result' => $status,
        );
        $this->_response->setBody($this->_helper->getHelper('json')->encodeJson($clientData));

    }
    public function sendAction(){
        $params = $this->getRequest()->getParams();

        $user_id = Zend_Auth::getInstance()->getStorage()->read()->detail->auth_id;
        $allCourier = Message_Constant_Client::ALL_COURIER;
        $allCustomer = Message_Constant_Client::ALL_CUSTOMER;
        $allStaff = Message_Constant_Client::ALL_STAFF;
        $registry = Zend_Registry::getInstance();
        if(isset($params['staff'])){
            $total = count(array_search($allStaff, $params['staff']));
            switch(true){
                case $total != 0:
                    $lists['staff'] = $allStaff;
                    break;
                default :
                    $lists['staff'] = implode(',',$params['staff']);
                    break;
            }
        }
        if(isset($params['customer'])){
            $total = count(array_search($allCustomer, $params['customer']));
            switch(true){
                case $total != 0:
                    $lists['customer'] = $allCustomer;
                    break;
                default :
                    $lists['customer'] = implode(',',$params['customer']);
                    break;
            }
        }
        if(isset($params['courier'])){
            $total = count(array_search($allCourier, $params['courier']));
            switch(true){
                case $total != 0 :
                    $lists['courier'] = $allCourier;
                    break;
                default :
                    $lists['courier'] = implode(',',$params['courier']);
                    break;
            }
        }
        if($params['message'] == null){
            $registry->session->param->content = $params['message'];
            $registry->session->error->message = "Message Content Can't Blank";
            $this->redirect("/message/broadcast/box");
        }
        if($lists != null){
            $data = array(
                "message_type" => Message_Constant_Client::BROADCAST_MESSAGE,
                "from_login_id" => $user_id,
                "to_login_id" => implode(',',$lists),
                "pickup_id" => 0,
                "reader" => 0,
                "created" => date("Y-m-d H:i:s", time()),
                "content" => $params["message"],
                "is_read" =>0,
            );
            $table = Message_Model_DbTable_Message::getInstance();
            $table->insert($data);
            $registry->session->send->message = "Your message has been sent successfully !";
            $this->redirect("/message/broadcast");
        }else{
            $registry->session->param->content = $params['message'];
            $registry->session->error->message = "Please Select Recipient";
            $this->redirect("/message/broadcast/box");
        }
    }
}