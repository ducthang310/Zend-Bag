<?php


abstract class Auth_Controller_Action_Backend extends Base_Controller_Action_Backend
{
    protected $_area = self::AREA_BACKEND;

    public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array())
    {
        parent::__construct($request, $response, $invokeArgs);

    }

}