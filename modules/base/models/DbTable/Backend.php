<?php

abstract class Base_Model_DbTable_Backend extends Base_Model_DbTable_Abstract
{
    protected $_area = self::AREA_BACKEND;
    protected $_wheres = array();
    protected $_sort_id = false;

    public function getCondition($status = array(), $searchParams = array(), $userType)
    {
        $controllerName = Zend_Registry::getInstance()->controllerName;
        switch (true) {
            // type customer
            case $userType == 'customer':
                switch (true) {
                    case $controllerName == 'history':
                        $status = array(
                            Pickup_Constant_Server::DELIVERED,
                            Pickup_Constant_Server::ACCEPTED,
                            Pickup_Constant_Server::RATED,
                            Pickup_Constant_Server::CANCELLED
                        );
                        if (isset($searchParams['filter']['completed']) && 'completed' == $searchParams['filter']['completed']) {
                            $status = array(
                                Pickup_Constant_Server::ACCEPTED,
                                Pickup_Constant_Server::RATED,
                                Pickup_Constant_Server::CANCELLED
                            );
                        }
                        break;
                    default:
                        $status = array(
                            Pickup_Constant_Server::AWAITING,
                            Pickup_Constant_Server::ASSIGNED,
                            Pickup_Constant_Server::PICKED_UP
                        );
                        break;
                }
                break;
            // type courier
            case $userType == 'courier':
                switch (true) {
                    case $controllerName == 'history':
                        $status = array(
                            Pickup_Constant_Server::DELIVERED,
                            Pickup_Constant_Server::ACCEPTED,
                            Pickup_Constant_Server::RATED,
                            Pickup_Constant_Server::CANCELLED,
                        );
                        if (isset($searchParams['filter']['completed']) && 'completed' == $searchParams['filter']['completed']) {
                            $status = array(
                                Pickup_Constant_Server::ACCEPTED,
                                Pickup_Constant_Server::RATED,
                            );
                        }
                        break;
                    case $controllerName == 'list':
                        $status = array(
                            Pickup_Constant_Server::AWAITING,
                        );
                        break;
                    default:
                        $status = array(
                            Pickup_Constant_Server::ASSIGNED,
                            Pickup_Constant_Server::PICKED_UP
                        );
                        break;
                }
                break;
            // type courier
            case $userType == 'staff':
                switch (true) {
                    case $controllerName == 'history':
                        if (isset($searchParams['filter']['completed']) && 'completed' == $searchParams['filter']['completed']) {
                            $status = array(
                                Pickup_Constant_Server::ACCEPTED,
                                Pickup_Constant_Server::RATED,
                                Pickup_Constant_Server::CANCELLED,
                            );
                        }
                        break;
                    case $controllerName == 'pickup':
                        $status = array(
                            Pickup_Constant_Server::AWAITING,
                            Pickup_Constant_Server::ASSIGNED,
                            Pickup_Constant_Server::PICKED_UP,
                            Pickup_Constant_Server::DELIVERED,
                        );
                        break;
                }
                break;
            default : break;
        }
        // if status add to condition.
        if ($status)
            $this->_wheres[] = " pickup.status IN (" . implode(',', $status) . ")";
    }

    public function filterByField($textField = array())
    {
        if (count($textField)) {
            if ($textField['destination'])
                unset ($textField['destination']);
            $data = array();
            foreach ($textField as $key => $val) {
                if ('' != trim($val)) {
                    if ($key == 'pickup.id'){
                        $data[] = $this->getAdapter()->quoteInto(" {$key} like ?", (int)trim($val) . '%');
                        $this->_sort_id = true;
                    }
                    else
                        $data[] = $this->getAdapter()->quoteInto(" {$key} like ?", '%' . trim($val) . '%');
                }
            }
            if (count($data)) {
                $this->_wheres[] = implode(' AND ', $data);
            }
        }
    }

    public function filterBySubUrbs($auth, $type = 'staff')
    {
        $data = '(';
        switch (true) {
            case $type == 'staff':
                $role = array(
                    Auth_Constant_Server::SUPER_ADMIN,
                    Auth_Constant_Server::INTERNATIONAL_STAFF,
                    Auth_Constant_Server::INTERNATIONAL_ADMIN
                );
                if (!in_array($auth->detail->role, $role)) {
                    $ids = "" . $auth->detail->area_ids;
                    $subUrbs = Configuration_Model_DbTable_Suburb::getInstance()->getSuburbById($ids);
                    if ($subUrbs) {
                        $customerIds = Customer_Model_DbTable_Customer::getInstance()->getCustomerBySuburb($subUrbs);
                        $courierIds = Courier_Model_DbTable_Courier::getInstance()->getCourierBySuburb($subUrbs);
                        $data .= !empty($courierIds) ? "`pickup`.`courier_id` IN (" . implode(',', $courierIds) . ")" : '';
                        if(!empty($customerIds) && !empty($courierIds)){
                            $data .= " OR `pickup`.`customer_id` IN (" . implode(',', $customerIds) . ")" ;
                        }else{
                            if(!empty($customerIds))
                            $data .= " `pickup`.`customer_id` IN (" . implode(',', $customerIds) . ")";
                        }
                    }
                }
                break;
            case $type == 'courier':
                $preferred = Courier_Model_DbTable_Courier::getInstance()->getDetail($auth->detail->auth_id);
                $preferred_ids = array_unique(array_merge(explode(",",$preferred[0]['preferred_pickup_suburb_ids']),explode(",",$preferred[0]['preferred_delivery_suburb_ids'])));
                $subUrbs = Configuration_Model_DbTable_Suburb::getInstance()->getSuburbById(implode(",",$preferred_ids));
                $customerIds = Customer_Model_DbTable_Customer::getInstance()->getCustomerBySuburb($subUrbs);
                $data .= !empty($customerIds) ? " `pickup`.`customer_id` IN (" . implode(',', $customerIds) . ")" : ' `pickup`.`customer_id` = 0';
                break;
            default :
                break;
        }
        if ($data != '(') {
            $data .= ')';
            $this->_wheres[] = $data;
        }
    }
}