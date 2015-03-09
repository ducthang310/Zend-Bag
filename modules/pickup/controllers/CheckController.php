<?php
class Pickup_CheckController extends Base_Controller_Action_Backend
{
    public function initOther()
    {
        $auth = Zend_Auth::getInstance();
        //check type user and redirect
        if (!$auth->getStorage()->read()) {
            $url = '/auth/index/login';
            $this->redirect($url);
        }
    }

    public function checkAction(){
        $params = $this->getRequest()->getParams();
        if($this->getRequest()->isPost() && $this->getRequest()->getParam('check')){
            $ids = explode(',',$params['id']);
            $array_status = explode(',',$params['status']);
            $results = array();
            foreach($ids as $key => $value){
                $results[$value] = $array_status[$key];
            }
            $change_status = false;
            $message = 'nothing change';
            foreach ($results as $pickup_id => $status){
                $db_status = Pickup_Model_DbTable_Pickup::getInstance()->checkStatus($pickup_id,$status);
                if($db_status != $status){
                    $message = 'Pickup ID = ' . $pickup_id . ' ::: changed status ' . $status. ' >>> ' . $db_status;
                    $change_status = true;
                    break;
                }
            }

            $clientData = array(
                'flag' => $change_status,
                'message' => $message,
            );
            $this->_response->setBody($this->_helper->getHelper('json')->encodeJson($clientData));
        }
    }
}
