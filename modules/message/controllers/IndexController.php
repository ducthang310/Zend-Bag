<?php

class Message_IndexController extends Message_Controller_Action_Base
{
    public function indexAction()
    {
        $this->listAction();
    }

    public function listAction()
    {

        $user = Zend_Auth::getInstance();
        if ($user->getStorage()->read()) {
            $type = $user->getStorage()->read()->auth_type;
            $id_user = $user->getStorage()->read()->detail->auth_id;
        } else {
            $id_user = null;
            $type = Auth_Constant_Server::GUEST_TYPE;
        }
        $table = Message_Model_DbTable_Message::getInstance();
        switch(true){
            case Auth_Constant_Server::COURIER_TYPE == $type:
                $all = Message_Constant_Client::ALL_COURIER;
                break;
            case Auth_Constant_Server::CUSTOMER_TYPE == $type:
                $all = Message_Constant_Client::ALL_CUSTOMER;
                break;
            default:
                $all = Message_Constant_Client::ALL_STAFF;
                break;
        }

        $toLoginId = array($id_user,$all);
        $list_message = $table->getAllBroadCastMessage($toLoginId,null,true);
        $page = $this->_getParam('page') ? $this->_getParam('page') : 1;
        if (isset($dataParams['btn_submit']))
            $page = 1;
        $dataParams['page'] = $page;
        if($list_message!=null){
            $this->view->dataParams = $dataParams;
            $this->view->data = $this->paginatorAction($list_message, $page);
        }
        $this->_response->setBody($this->view->render($this->_verifyScriptName('index.phtml')));
    }

    public function detailAction(){
        $user = Zend_Auth::getInstance()->getStorage()->read();
        $id_user = $user->detail->auth_id;
        if($this->getRequest()->isPost() && $this->getRequest()->getParam('click')){
        $id = $this->getRequest()->getParam('message_id');
        $table = Message_Model_DbTable_Message::getInstance();
        $message = $table->fetchAll('id = ' . $id);
        $total = count($message);
        switch ($total) {
            case 0:
                $this->forward('error', 'index', 'base');
                break;
            case 1:
                foreach($message as $key => $value){
                        $data = array(
                            "id"    => $value['id'],
                            "created" => $value["created"],
                            "content" => $value["content"],
                        );
                };
                $reader = $value['reader'];
                $reader = explode(',',$reader);
                if(!in_array($id_user,$reader)){
                    $reader[] = $id_user;
                    $where = $table->getAdapter()->quoteInto('id = ? ' , $id);
                    $update = array(
                        'reader' => implode(',',$reader),
                    );
                    $table->update($update,$where);
                }
                $this->view->message = $data;
                $message = "success";
                $result = Base_Constant_Client::SUCCESSFUL;
                $detail = $this->view->render($this->_verifyScriptName('common/detail.phtml'));
                break;
            default:
                throw new Exception('Too many ' . $id);
                break;
        }
            $clientData = array(
                'message' => $message,
                'result' => $result,
                'detail' => $detail,
            );
            $this->_response->setBody($this->_helper->getHelper('json')->encodeJson($clientData));
        }
    }
}