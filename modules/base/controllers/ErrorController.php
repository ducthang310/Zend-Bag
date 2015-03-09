<?php

class Base_ErrorController extends Zend_Controller_Action
{

    public function init()
    {
        parent::init();
        $registry = Zend_Registry::getInstance();

        $this->_helper->viewRenderer->setNoRender(true);

        $layout = Zend_Layout::getMvcInstance();
        $layoutPath = $registry->modulePaths['base'] . DS . 'views' . DS . 'layouts';
        if (!is_dir($layoutPath)) {
            $layoutPath = WWW_PATH . DS . 'views' . DS . 'layouts';
        }
        if ($layout) {
            $layout->setLayoutPath($layoutPath);
            $layout->setLayout('layout_error');
        } else {
            Zend_Layout::startMvc(array(
                "layout" => 'layout_error',
                "layoutPath" => $layoutPath
            ));
        }
    }

    public function errorAction()
    {
        $errors = $this->_getParam('error_handler');

        if (!$errors || !$errors instanceof ArrayObject) {
            $this->view->message = 'You have reached the error page';
            $this->_response->setBody($this->view->render('exception_for_client.phtml'));
            return;
        }

        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:

                $this->getResponse()->setHttpResponseCode(404);
                $this->view->message = 'Page not found';
                break;
            default:

                $this->getResponse()->setHttpResponseCode(500);
                $this->view->message = 'Application error';
                break;
        }

        $this->view->exception_for_client = false;

        if ($this->getInvokeArg('displayExceptions') == true) {
            $this->view->exception_for_client = true;
        }
        $this->view->exception = $errors->exception;
        $this->view->request = $errors->request;

        $this->_response->setBody($this->view->render('exception_for_client.phtml'));


        Base_Helper_Log::getInstance()->log($this->view->render('exception.phtml'));
    }

}

