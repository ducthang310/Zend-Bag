<?php

class Home_IndexController extends Base_Controller_Action_Backend
{
    public function initOther() {
        parent::initOther();
        $this->view->title = 'Homepage';
    }
    public function indexAction()
    {
        Zend_Layout::getMvcInstance()->setLayout('home');
    }
    public function registerAction()
    {
        Zend_Layout::getMvcInstance()->setLayout('layout');
        $this->_response->setBody($this->view->render($this->_verifyScriptName('select-type-register.phtml')));
    }

    public function policyAction(){
        Zend_Layout::getMvcInstance()->setLayout('layout');
        $this->_response->setBody($this->view->render($this->_verifyScriptName('policy.phtml')));
    }

    public function termAction(){
        Zend_Layout::getMvcInstance()->setLayout('layout');
        $this->_response->setBody($this->view->render($this->_verifyScriptName('term.phtml')));
    }

    public function cronAction()
    {
        foreach (Zend_Registry::getInstance()->appConfig['cron'] as $module) {
            $class = ucfirst($module) . '_Plugin_Cron';
            call_user_func($class . '::run');
        }
    }
}