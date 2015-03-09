<?php

/**
 * only process base/admin
 */
class Auth_Plugin_Acl extends Zend_Controller_Plugin_Abstract
{
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $user = Zend_Auth::getInstance()->getStorage()->read();
        $role = null;
        if ($user) {
            $type = $user->auth_type;
            if ($user->auth_type == Auth_Constant_Server::STAFF_TYPE) {
                $role = $user->detail->role;
            }
        } else {
            $type = Auth_Constant_Server::GUEST_TYPE;
        }
        $module = $request->getModuleName();
        $controller = $request->getControllerName();
        $action = $request->getActionName();
        $this->authenticate($type, $role, $module, $controller, $action);
    }

    public function authenticate($type, $role, $module, $controller, $action)
    {
        // escape error handler
        $front = Zend_Controller_Front::getInstance();
        $params = array($type, $role, $module, $controller, $action);
        $count = count($params);
        $plugins = new Base_Php_Overloader($front->getParam("bootstrap")->getOption('plugins'));
        $errorModule = $plugins->errorHandler->params->module;
        $errorController = $plugins->errorHandler->params->controller;
        $errorAction = $plugins->errorHandler->params->action;
        $redirecting = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');

        /*
         * the conditions are for to be passed or redirected, default is passed
         * array(typeCondition, roleCondition, moduleCondition, controllerCondition, actionCondition, redirectOrNot, url)
         * order by typeCondition ASC, roleCondition ASC, moduleCondition ASC, controllerCondition ASC, actionCondition ASC
         */
        $permission = array(
            array(null, null, $errorModule, $errorController, $errorAction, FALSE, null),
            array(null, null, 'configuration', 'suburb', 'getsurbub', FALSE, null),
            array(Auth_Constant_Server::COURIER_TYPE, null, 'auth', 'index', 'login', TRUE, '/auth'),
            array(Auth_Constant_Server::COURIER_TYPE, null, 'configuration', null, null, TRUE, '/auth'),
            array(Auth_Constant_Server::COURIER_TYPE, null, 'courier', 'register', null, TRUE, '/auth'),
            array(Auth_Constant_Server::COURIER_TYPE, null, 'courier', null, null, FALSE, null),
            array(Auth_Constant_Server::COURIER_TYPE, null, 'customer', null, null, TRUE, '/auth'),
            array(Auth_Constant_Server::COURIER_TYPE, null, 'home', 'index', 'term', FALSE, null),
            array(Auth_Constant_Server::COURIER_TYPE, null, 'home', 'index', 'policy', FALSE, null),
            array(Auth_Constant_Server::COURIER_TYPE, null, 'home', null, null, TRUE, '/auth'),
            array(Auth_Constant_Server::COURIER_TYPE, null, 'message', 'broadcast', 'status', FALSE, null),
            array(Auth_Constant_Server::COURIER_TYPE, null, 'message', 'broadcast', null, TRUE, '/auth'),
            array(Auth_Constant_Server::COURIER_TYPE, null, 'message', 'index', null, FALSE, null),
            array(Auth_Constant_Server::COURIER_TYPE, null, 'message', 'private', null, FALSE, null),
            array(Auth_Constant_Server::COURIER_TYPE, null, 'pickup', null, 'add', TRUE, '/auth'),
            array(Auth_Constant_Server::COURIER_TYPE, null, 'pickup', null, 'book', TRUE, '/auth'),
            array(Auth_Constant_Server::COURIER_TYPE, null, 'pickup', null, 'cancel', TRUE, '/auth'),
            array(Auth_Constant_Server::COURIER_TYPE, null, 'pickup', null, 'edit', TRUE, '/auth'),
            array(Auth_Constant_Server::COURIER_TYPE, null, 'pickup', null, 'getcourierlist', TRUE, '/auth'),
            array(Auth_Constant_Server::COURIER_TYPE, null, 'pickup', null, 'getfee', TRUE, '/auth'),
            array(Auth_Constant_Server::COURIER_TYPE, null, 'pickup', null, 'update', TRUE, '/auth'),
            array(Auth_Constant_Server::COURIER_TYPE, null, 'pickup', null, null, FALSE, null),
            array(Auth_Constant_Server::COURIER_TYPE, null, 'staff', null, null, TRUE, '/auth'),
            array(Auth_Constant_Server::CUSTOMER_TYPE, null, 'auth', 'index', 'login', TRUE, '/auth'),
            array(Auth_Constant_Server::CUSTOMER_TYPE, null, 'configuration', null, null, TRUE, '/auth'),
            array(Auth_Constant_Server::CUSTOMER_TYPE, null, 'courier', null, null, TRUE, '/auth'),
            array(Auth_Constant_Server::CUSTOMER_TYPE, null, 'customer', 'register', null, TRUE, '/auth'),
            array(Auth_Constant_Server::CUSTOMER_TYPE, null, 'customer', null, null, FALSE, null),
            array(Auth_Constant_Server::CUSTOMER_TYPE, null, 'home', 'index', 'term', FALSE, null),
            array(Auth_Constant_Server::CUSTOMER_TYPE, null, 'home', 'index', 'policy', FALSE, null),
            array(Auth_Constant_Server::CUSTOMER_TYPE, null, 'home', null, null, TRUE, '/auth'),
            array(Auth_Constant_Server::CUSTOMER_TYPE, null, 'message', 'broadcast', 'status', FALSE, null),
            array(Auth_Constant_Server::CUSTOMER_TYPE, null, 'message', 'broadcast', null, TRUE, '/auth'),
            array(Auth_Constant_Server::CUSTOMER_TYPE, null, 'message', 'index', null, FALSE, null),
            array(Auth_Constant_Server::CUSTOMER_TYPE, null, 'message', 'private', null, FALSE, null),
            array(Auth_Constant_Server::CUSTOMER_TYPE, null, 'pickup', null, 'assigned', TRUE, '/auth'),
            array(Auth_Constant_Server::CUSTOMER_TYPE, null, 'pickup', null, 'getcourierlist', TRUE, '/auth'),
            array(Auth_Constant_Server::CUSTOMER_TYPE, null, 'pickup', null, 'getfee', FALSE, null),
            array(Auth_Constant_Server::CUSTOMER_TYPE, null, 'pickup', null, null, FALSE, null),
            array(Auth_Constant_Server::CUSTOMER_TYPE, null, 'staff', null, null, TRUE, '/auth'),
            array(Auth_Constant_Server::GUEST_TYPE, null, 'auth', 'index', 'forgot', FALSE, null),
            array(Auth_Constant_Server::GUEST_TYPE, null, 'auth', 'index', 'login', FALSE, null),
            array(Auth_Constant_Server::GUEST_TYPE, null, 'auth', 'index', 'logout', TRUE, '/auth/index/login'),
            array(Auth_Constant_Server::GUEST_TYPE, null, 'auth', 'index', 'noaccess', FALSE, null),
            array(Auth_Constant_Server::GUEST_TYPE, null, 'auth', null, null, TRUE, '/auth/index/login'),
            array(Auth_Constant_Server::GUEST_TYPE, null, 'base', 'validate', null, FALSE, null),
            array(Auth_Constant_Server::GUEST_TYPE, null, 'base', 'simplevalidate', null, FALSE, null),
            array(Auth_Constant_Server::GUEST_TYPE, null, 'base', null, null, TRUE, '/auth/index/login'),
            array(Auth_Constant_Server::GUEST_TYPE, null, 'configuration', null, null, TRUE, '/auth'),
            array(Auth_Constant_Server::GUEST_TYPE, null, 'courier', 'register', null, FALSE, null),
            array(Auth_Constant_Server::GUEST_TYPE, null, 'courier', null, null, TRUE, '/auth'),
            array(Auth_Constant_Server::GUEST_TYPE, null, 'customer', 'register', null, FALSE, null),
            array(Auth_Constant_Server::GUEST_TYPE, null, 'customer', null, null, TRUE, '/auth'),
            array(Auth_Constant_Server::GUEST_TYPE, null, 'home', null, null, FALSE, null),
            array(Auth_Constant_Server::GUEST_TYPE, null, 'message', null, null, TRUE, '/auth'),
            array(Auth_Constant_Server::GUEST_TYPE, null, 'pickup', null, null, TRUE, '/auth'),
            array(Auth_Constant_Server::GUEST_TYPE, null, 'staff', null, null, TRUE, '/auth'),
            array(Auth_Constant_Server::STAFF_TYPE, Auth_Constant_Server::INTERNATIONAL_ADMIN, 'staff', 'report', null, TRUE, '/auth'),
            array(Auth_Constant_Server::STAFF_TYPE, Auth_Constant_Server::INTERNATIONAL_STAFF, 'staff', 'report', null, TRUE, '/auth'),
            array(Auth_Constant_Server::STAFF_TYPE, Auth_Constant_Server::INTERNATIONAL_STAFF, 'staff', 'staff', null, TRUE, '/auth'),
            array(Auth_Constant_Server::STAFF_TYPE, Auth_Constant_Server::LOCAL_AREA_ADMIN, 'staff', 'report', null, TRUE, '/auth'),
            array(Auth_Constant_Server::STAFF_TYPE, Auth_Constant_Server::LOCAL_AREA_STAFF, 'staff', 'report', null, TRUE, '/auth'),
            array(Auth_Constant_Server::STAFF_TYPE, Auth_Constant_Server::LOCAL_AREA_STAFF, 'staff', 'staff', null, TRUE, '/auth'),
            array(Auth_Constant_Server::STAFF_TYPE, Auth_Constant_Server::SUPER_ADMIN, 'configuration', null, null, FALSE, null),
            array(Auth_Constant_Server::STAFF_TYPE, null, 'auth', 'index', 'login', TRUE, '/auth'),
            array(Auth_Constant_Server::STAFF_TYPE, null, 'configuration', null, null, TRUE, '/auth'),
            array(Auth_Constant_Server::STAFF_TYPE, null, 'courier', null, null, TRUE, '/auth'),
            array(Auth_Constant_Server::STAFF_TYPE, null, 'customer', null, null, TRUE, '/auth'),
            array(Auth_Constant_Server::STAFF_TYPE, null, 'home', 'index', 'term', FALSE, null),
            array(Auth_Constant_Server::STAFF_TYPE, null, 'home', 'index', 'policy', FALSE, null),
            array(Auth_Constant_Server::STAFF_TYPE, null, 'home', null, null, TRUE, '/auth'),
            array(Auth_Constant_Server::STAFF_TYPE, null, 'message', 'broadcast', null, FALSE, null),
            array(Auth_Constant_Server::STAFF_TYPE, null, 'message', 'private', null, TRUE, '/auth'),
            array(Auth_Constant_Server::STAFF_TYPE, null, 'pickup', null, 'add', TRUE, '/auth'),
            array(Auth_Constant_Server::STAFF_TYPE, null, 'pickup', null, 'book', TRUE, '/auth'),
            array(Auth_Constant_Server::STAFF_TYPE, null, 'pickup', null, 'edit', TRUE, '/auth'),
            array(Auth_Constant_Server::STAFF_TYPE, null, 'pickup', null, 'update', TRUE, '/auth'),
            array(Auth_Constant_Server::STAFF_TYPE, null, 'pickup', null, 'updaterate', TRUE, '/auth'),
            array(Auth_Constant_Server::STAFF_TYPE, null, 'pickup', null, null, FALSE, null),

        );
        foreach ($permission as $permission) {
            for ($i = 0; $i <= 4; $i++) {
                if (isset($permission[$i]) && $params[$i] != $permission[$i]) {
                    continue 2;
                }
            }
            if ($permission[5]) {
                $redirecting->gotoUrl($permission[6])->redirectAndExit();
            }
            // if a rule is matched, not check remain rules
            return;
        }
    }
}