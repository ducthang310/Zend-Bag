<?php

class Staff_CustomerController extends Base_Controller_Action_Backend
{
    public function indexAction()
    {
        $this->getData('customer/index.phtml');
    }

    public function profileAction()
    {
        $auth = $this->getRequest()->getParam('id', 0);
        $result = Base_Constant_Client::SUCCESSFUL;
        $message = '';
        $table = Customer_Model_DbTable_Customer::getInstance();
        if ($this->getRequest()->getParam('click')){
            if ($auth != (string)(int)$auth || $auth <= 0) {
                $this->forward('error', 'index', 'base');
                $message = "error";
                $result = Base_Constant_Client::FAILED;
            } else {
                $customer = $table->fetchAll('auth_id = ' . $auth);
                $total = count($customer);
                switch ($total) {
                    case 0:
                        $this->forward('error', 'index', 'base');
                        $message = "error";
                        $result = Base_Constant_Client::FAILED;
                        break;
                    case 1:
                            foreach ($customer as $key => $value){
                                $data = array(
                                    'id' => $value['auth_id'],
                                    'email' => $value['email'],
                                    'image' => $value['image'],
                                    'address' => $value['address'],
                                    'suburb' => $value['suburb'],
                                    'customer_state' => $value['customer_state'],
                                    'abn' => $value['abn'],
                                    'contact_name' => $value['firstname'] .' '.$value['lastname'],
                                    'contact_number' => $value['contact_number'],
                                    'mobile' => $value['mobile'],
                                );
                            }
                        $this->view->customer = $data;
                        $message = "success";
                        $result = Base_Constant_Client::SUCCESSFUL;
                        break;
                    default:
                        $message = "error";
                        $result = Base_Constant_Client::FAILED;
                        throw new Exception('Too many ' . $auth);
                        break;
                }
            }
            $clientData = array(
                'message' => $message,
                'result' => $result,
                'detail' =>  $this->view->render($this->_verifyScriptName('customer/profile.phtml')),
            );
            $this->_response->setBody($this->_helper->getHelper('json')->encodeJson($clientData));
        }
    }
}