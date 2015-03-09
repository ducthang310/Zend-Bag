<?php

class Configuration_SuburbController extends Base_Controller_Action_Backend
{
    public function indexAction()
    {

    }

    public function getsurbubAction()
    {
        $params = $this->_request->getParams();
        $result = Base_Constant_Client::SUCCESSFUL;
        $message = '';
        $errors = array();
        $table = Configuration_Model_DbTable_Suburb::getInstance();
        try {
            $result = $table->getSuburbByRegion($params['region']);
        } catch (Zend_Db_Exception $dbException) {
            $result = Base_Constant_Client::FAILED;
            $message = $dbException->getMessage();
        }

        $clientData = array(
            'result' => $result,
            'message' => $message,
            'params' => $params,
        );
        $this->_response->setBody($this->_helper->getHelper('json')->encodeJson($clientData));
    }
}