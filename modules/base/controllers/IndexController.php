<?php

class Base_IndexController extends Base_Controller_Action_Backend
{

    public function indexAction()
    {
        $this->_response->setBody($this->view->render($this->_verifyScriptName('index.phtml')));
    }

    public function errorAction()
    {
        $clientData = array(
            'result' => Base_Constant_Client::FAILED,
            'message' => 'There is an error',
        );
        if (NULL === $this->_request->getParam('ajax')) {
            $this->_response->setBody($this->view->render($this->_verifyScriptName('error.phtml')));
        } else {
            $this->_response->setBody($this->_helper->getHelper('json')->encodeJson($clientData));
        }
    }

    public function cleartempAction()
    {
        Base_Helper_App::clearTemp();
        $this->_helper->layout()->disableLayout();
        $this->_response->setBody($this->view->render($this->_verifyScriptName('cleartemp.phtml')));
    }

    public function clearlogAction()
    {
        $this->_clearLog();
        $this->_helper->layout()->disableLayout();
        $this->_response->setBody($this->view->render($this->_verifyScriptName('clearlog.phtml')));
    }

}