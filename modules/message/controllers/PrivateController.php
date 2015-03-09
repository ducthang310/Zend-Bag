<?php

class Message_PrivateController extends Message_Controller_Action_Base
{

    public function statusAction(){
        $params = $this->getRequest()->getParams();
        $user = Zend_Auth::getInstance()->getStorage()->read();
        $id_user = $user->detail->auth_id;
        $status = array();
        $type  = Message_Constant_Client::PRIVATE_MESSAGE;
        $table = Message_Model_DbTable_Message::getInstance();
        $ids = explode(',',$params['id']);
        foreach ($ids as $key => $value){
            $messageDetail = $table->fetchAll(
                $table->select()
                    ->where("message_type = " . $type)
                    ->where('to_login_id = ' . $id_user)
                    ->where("pickup_id = " . $value)
                    ->where('reader  != ' . $id_user)
                    ->order("created ASC")
            );
            if($messageDetail && count($messageDetail) == 0){
                $status[$value] = false;
            }else{
                $status[$value] = true;
            }
        };
        $clientData = array(
            'result' => $status,
        );
        $this->_response->setBody($this->_helper->getHelper('json')->encodeJson($clientData));
    }

    public function viewAction()
    {
        $params = $this->getRequest()->getParams();
        $registry = Zend_Registry::getInstance();
        $user = Zend_Auth::getInstance()->getStorage()->read();
        $id_user = $user->detail->auth_id;
        if ($this->getRequest()->isPost()) {
            $type = Message_Constant_Client::PRIVATE_MESSAGE;
            $result = Base_Constant_Client::SUCCESSFUL;
            $table = Message_Model_DbTable_Message::getInstance();
            $messageDetail = Message_Model_DbTable_Message::getInstance()->getPrivateMessages($type, $params['id']);
            $table_pickup = Pickup_Model_DbTable_Pickup::getInstance();
            $customer_id = $table_pickup->getIdCustomer($params['id']);
            $courier_id = $table_pickup->getIdCourier($params['id']);
            if($customer_id[0]['customer_id'] == $id_user){
                $to_id = $courier_id[0]['courier_id'];
            }else{
                $to_id = $customer_id[0]['customer_id'];
            }
            $message = '';
            $data = array();
            $ids = array();
            foreach ($messageDetail as $key => $value) {
                if ($value["from_login_id"] == $id_user) {
                    $class = "mine";
                } else {
                    $class = "their";
                }
                $data[] = array(
                    'content' => $value['content'],
                    'style' => $class,
                    'id' => $value['id'],
                    'pickup' => $value['pickup_id']
                );
                $lastId[] = $value['id'];
            }
            $Ids = array(
                'to_id' => $to_id,
                'from_id' => $id_user,
                'pickup_id' => $params['id']
            );
            $registry->session->message->private = $Ids;
            $message = "SUCCESSFUL";
            if ($data != null) {
                $lastId = max($lastId);
                $Ids['last_id'] = $lastId;
                $this->view->message = $data;
                $registry->session->message->private = $Ids;
                $update['reader'] = $id_user;
                $where[] = $table->getAdapter()->quoteInto('pickup_id = ? ' , $params['id']);
                $where[] = $table->getAdapter()->quoteInto('to_login_id = ?' , $id_user);
                $table->update($update,$where);
            } else {
                    $data[] = array(
                        'content' => null,
                        'style' => null,
                        'pickup' => $params['id'],
                    );
                $this->view->message = $data;
            }
            $clientData = array(
                'result' => $result,
                'message' => $message,
                'list' => $this->view->render($this->_verifyScriptName('common/private.phtml'))
            );
            $this->_response->setBody($this->_helper->getHelper('json')->encodeJson($clientData));
        }
    }

    public function loadAction()
    {
        $params = $this->getRequest()->getParams();
        $registry = Zend_Registry::getInstance();
        $user = Zend_Auth::getInstance()->getStorage()->read();
        $id_user = $user->detail->auth_id;
        $ids = $registry->session->message->private;
        if ($this->getRequest()->isPost()) {
            $type = Message_Constant_Client::PRIVATE_MESSAGE;
            $result = Base_Constant_Client::SUCCESSFUL;
            $table = Message_Model_DbTable_Message::getInstance();
            $messageDetail = $table->getPrivateMessages($type,$ids['pickup_id'], $id_user);
            $message = '';
            $data = array();
            foreach ($messageDetail as $key => $value) {
                $data[] = array(
                    'from' => 1,
                    'content' => htmlspecialchars($value['content']),
                );
                $lastId[] = $value['id'];
            }
            $message = "SUCCESSFUL";
            if ($data != null) {
                $items = $data;
                $lastId = max($lastId);
                $ids['last_id'] = $lastId;
                $registry->session->message->private = $ids;
                $update = array(
                    'reader' => $id_user
                );
                $where[] = $table->getAdapter()->quoteInto('pickup_id = ? ' , $ids['pickup_id']);
                $where[] = $table->getAdapter()->quoteInto('to_login_id = ?' , $id_user);
                $table->update($update, $where);
            }else{
                $items = null;
            }
            $clientData = array(
                'result' => $result,
                'message' => $message,
                'items' => $items
            );
            $this->_response->setBody($this->_helper->getHelper('json')->encodeJson($clientData));
        }
    }
    public function sendAction()
    {
        $registry = Zend_Registry::getInstance();
        $params = $this->getRequest()->getParams();
        if ($this->getRequest()->isPost()) {
            $user = Zend_Auth::getInstance()->getStorage()->read();
            $id_user = $user->detail->auth_id;
            $ids = $registry->session->message->private;
            $params = new Base_Php_Overloader($params);
            $datetime = new DateTime();
            $timeNow = $datetime->format('Y\-m\-d\ H:i:s');
            $result = Base_Constant_Client::SUCCESSFUL;
            if(!$params->content){
                $result = Base_Constant_Client::FAILED;
                $message = 'Message not empty !';
                return;
            }
            if($ids['to_id']){
                $to_id = $ids['to_id'];
            }else{
                $to_id = 0;
            }
            $data = array(
                "message_type" => Message_Constant_Client::PRIVATE_MESSAGE,
                "to_login_id" => $to_id,
                "from_login_id" => $id_user,
                "pickup_id" => $ids['pickup_id'],
                "content" => $params->content,
                "created" => $timeNow,
                "is_read" => 0,
                "reader" => $id_user,
            );
            $table = Message_Model_DbTable_Message::getInstance();
            if ($newid = $table->insert($data)) {
                $type = Message_Constant_Client::PRIVATE_MESSAGE;
                $result = Base_Constant_Client::SUCCESSFUL;
                if(isset($ids['last_id'])){
                    $lastId = $ids['last_id'];
                }else{
                    $lastId = null;
                }
                $messageDetail = Message_Model_DbTable_Message::getInstance()->getNewMessage($type, $ids['pickup_id'], $lastId , $newid);
                $message = '';
                $data = array();

                foreach ($messageDetail as $key => $value) {
                    if ($value["from_login_id"] == $id_user) {
                        $class = "mine";
                    } else {
                        $class = "their";
                    }
                    $data[] = array(
                        'content' => htmlspecialchars($value['content']),
                        'style' => $class,
                        'id' => $value['id'],
                    );
                    $last_id[] = $value['id'];
                }
                if($messageDetail != null){
                    $ids['last_id'] = max($last_id);
                }
                $registry->session->message->private = $ids;
            } else {
                $result = Base_Constant_Client::FAILED;
                $message = 'FAILED';
            }
            $clientData = array(
                'result' => $result,
                'message' => $message,
                'items' => $data,
            );
            $this->_response->setBody($this->_helper->getHelper('json')->encodeJson($clientData));
        }
    }
}