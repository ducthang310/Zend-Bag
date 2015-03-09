<?php

class Customer_PickupController extends Base_Controller_Action_Backend
{
    public function indexAction()
    {
        $this->redirect('/pickup');
    }

    public function bookAction()
    {
        if($this->_request->isPost() && $this->getRequest()->getPost('book')) {
            $datetime = new DateTime();
            $timeNow = $datetime->format('Y\-m\-d\ h:i:s');

            $params = $this->_request->getParams();
            $result = Base_Constant_Client::SUCCESSFUL;
            $message = '';

            $table = Pickup_Model_DbTable_Pickup::getInstance();
            $data = array(
                'customer_id' => Zend_Auth::getInstance()->getStorage()->read()->id,
                'status' => 'waiting',
                'awaiting_active_time' => $timeNow,
                'from_address' => $params['from_address'],
                'to_address' => $params['to_address'],
                'detail' => $params['detail'],
                'created' => $timeNow,
                'updated' => $timeNow
            );
            try {
                $table->insert($data);
                $newId = $table->getAdapter()->lastInsertId();
            } catch (Zend_Db_Exception $dbException) {
                $result = Base_Constant_Client::FAILED;
                $message = $dbException->getMessage();
            }
            $clientData = array(
                'result' => $result,
                'message' => $message,
                'params' => $params,
            );
            try {
                $where = $table->getAdapter()->quoteInto('id = ?', $newId);
                $pickups = $table->fetchAll($where);
                $total = count($pickups);
                switch ($total) {
                    case 0:
                        $this->forward('error', 'index', 'base');
                        break;
                    case 1:
                        $this->view->pickup = $pickups[0];
                        break;
                    default:
                        throw new Exception('Too many ' . $newId);
                        break;
                }
            } catch (Zend_Db_Exception $dbException) {
                $result = Base_Constant_Client::FAILED;
                $message = $dbException->getMessage();
            }
            $this->_response->setBody($this->view->render($this->_verifyScriptName('pickup/detail.phtml')));
        }else{
            $this->_response->setBody($this->view->render($this->_verifyScriptName('pickup/book.phtml')));
        }
    }
}