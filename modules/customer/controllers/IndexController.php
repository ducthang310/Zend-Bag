<?php

class Customer_IndexController extends Auth_Controller_Action_Backend
{
    public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array())
    {
        parent::__construct($request, $response, $invokeArgs);

    }

    public function indexAction()
    {
        $this->getListPickUp();
    }

}