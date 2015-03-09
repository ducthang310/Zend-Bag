<?php

class Pickup_IndexController extends Base_Controller_Action_Backend
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

    public function indexAction()
    {
        $this->listAction();
    }

    public function listAction()
    {
        $status = $this->getRequest()->getParam('status');
        $pickups = Pickup_Model_DbTable_Pickup::getInstance()->getAll($status,true);
        $page = $this->_getParam('page');
        $this->view->pickups = $this->paginatorAction($pickups, $page);
        $this->_response->setBody($this->view->render($this->_verifyScriptName('list.phtml')));
    }

    public function getConfigData()
    {
        $table = Configuration_Model_DbTable_Configuration::getInstance();
        $datas = $table->fetchAll();
        $config = array(0, 0, 0, 0);
        foreach ($datas as $data) {
            if ($data->config_key == Configuration_Constant_Server::getKeyValue('BASE_PRICE')) {
                $config[0] = (float)$data->config_value;
            }
            if ($data->config_key == Configuration_Constant_Server::getKeyValue('DELIVERY_SIGNATURE_FEE')) {
                $config[1] = (float)$data->config_value;
            }
            if ($data->config_key == Configuration_Constant_Server::getKeyValue('DELIVERY_INSURE_FEE')) {
                $config[2] = (float)$data->config_value;
            }
            if ($data->config_key == Configuration_Constant_Server::getKeyValue('CREDIT_CARD_FEE')) {
                $config[3] = (float)$data->config_value;
            }
            if ($data->config_key == Configuration_Constant_Server::getKeyValue('DELIVERED_TIME')) {
                $config[4] = (float)$data->config_value;
            }
            if ($data->config_key == Configuration_Constant_Server::getKeyValue('PAYPAL_ID')) {
                $config[15] = $data->config_value;
            }
            if ($data->config_key == Configuration_Constant_Server::getKeyValue('PAYPAL_SECRET')) {
                $config[16] = $data->config_value;
            }
            if ($data->config_key == Configuration_Constant_Server::getKeyValue('CURRENCY')) {
                $config[17] = $data->config_value;
            }
            if ($data->config_key == Configuration_Constant_Server::getKeyValue('CANCEL_FEE')) {
                $config[18] = (float)$data->config_value;
            }
            if ($data->config_key == Configuration_Constant_Server::getKeyValue('CANCELED_TIME')) {
                $config[19] = (float)$data->config_value;
            }
            if ($data->config_key == Configuration_Constant_Server::getKeyValue('REPORTED_EMAIL')) {
                $config[20] = $data->config_value;
            }
        }
        return $config;
    }

    public function bookAction()
    {
        $auth = Zend_Auth::getInstance();
        $auth_id = $auth->getStorage()->read()->id;
        $type = $auth->getStorage()->read()->auth_type;
        if ($type == Auth_Constant_Server::COURIER_TYPE)
            return $this->redirect('auth/');
        $config = $this->getConfigData();
        $table = Customer_Model_DbTable_Customer::getInstance();
        $data = $table->fetchRow('auth_id = '.$auth_id);
        if($data){
            $tmp = $data->suburb;
            $tmp1 = $data->suburb;
            $prefer_address = array(
                'pickup_address' => $tmp,
                'delivery_address' => $tmp1
            );
            $this->view->prefer_address = $prefer_address;
        }
        $this->view->config = $config;
        $this->_response->setBody($this->view->render($this->_verifyScriptName('pickup/book.phtml')));
    }

    public function checkPaypal($total, $cutomer_id)
    {
        $config = $this->getConfigData();
        $clientId = $config[15];
        $clientSecret = $config[16];
        $currency = strtoupper($config[17]);

        $host = 'https://api.sandbox.paypal.com';
        $url = $host . '/v1/oauth2/token';
        $postArgs = 'grant_type=client_credentials';
        $token = Base_Helper_Paypal::get_access_token($clientId, $clientSecret, $url, $postArgs);
        $url = $host . '/v1/payments/payment';

        $table = Customer_Model_DbTable_Customer::getInstance();
        $where = $table->getAdapter()->quoteInto('auth_id = ?', $cutomer_id);
        $customer = $table->fetchRow($where);
        if (!$customer) {
            return false;
        }
        $expire = $customer->expiry_date;
        $expireArr = explode("/", $expire);

        if ($customer->card_type == 1) {
            $card_type = "visa";
        } else {
            $card_type = "mastercard";
        }

        $url = $host . '/v1/vault/credit-card';
        $creditcard = array(
            'payer_id' => $customer->email,
            'number' => $customer->card_number,
            'type' => $card_type,
            'expire_month' => (int)$expireArr[0],
            'expire_year' => ((int)$expireArr[1]),
            'first_name' => $customer->firstname,
            'last_name' => $customer->lastname
        );

        try {
            $json = json_encode($creditcard);
            $json_resp = Base_Helper_Paypal::make_post_call($url, $json, $token);
            if (isset($json_resp['name']) && $json_resp['name'] == "VALIDATION_ERROR") {
                $details = $json_resp['details'];
                $error = "";
                for ($i = 0; $i < count($details); $i++) {

                    switch($details[$i]["field"]){
                        case "expire_year":
                            $error .= " Expire Year invalid, please check your infomation.";
                            break;
                        case "expire_month":
                            $error .= " Expire Month invalid, please check your infomation.";
                            break;
                        case "type":
                            $error .= " Type card invalid, please check your infomation.";
                            break;
                        case "number":
                            $error .= " Number card invalid, please check your infomation.";
                            break;
                        default:
                            $error .= $details[$i]["issue"];
                            break;
                    }
                }
                Zend_Registry::getInstance()->session->book->error = $error;
                return false;
            }
            $credit_card_id = $json_resp['id'];
            $url = $host . '/v1/payments/payment';
            $payment = array(
                'intent' => 'sale',
                'payer' => array(
                    'payment_method' => 'credit_card',
                    'funding_instruments' => array(array(
                        'credit_card_token' => array(
                            'credit_card_id' => $credit_card_id,
                            'payer_id' => $customer->email
                        )
                    ))
                ),
                'transactions' => array(array(
                    'amount' => array(
                        'total' => $total,
                        'currency' => $currency
                    ),
                    'description' => 'payment using a saved card'
                ))
            );
            $json = json_encode($payment);
            $paypal = Base_Helper_Paypal::make_post_call($url, $json, $token);
            if (isset($paypal['name']) && $paypal['name'] == "VALIDATION_ERROR") {
                $details = $paypal['details'];
                $error = "";
                for ($i = 0; $i < count($details); $i++) {
                    switch($details[$i]["field"]){
                        case "expire_year":
                            $error .= " Expire Year invalid, please check your Expire Year.";
                            break;
                        case "expire_month":
                            $error .= " Expire Month invalid, please check your Expire Month.";
                            break;
                        case "type":
                            $error .= " Type card invalid, please check your Type card.";
                            break;
                        case "number":
                            $error .= " Number card invalid, please check your Number card.";
                            break;
                        default:
                            $error .= $details[$i]["issue"];
                            break;
                    }
                }
                Zend_Registry::getInstance()->session->book->error = $error;
                return false;
            } else {
                return true;
            }

        } catch (Zend_Db_Exception $dbException) {
            $result = Base_Constant_Client::FAILED;
            $message = $dbException->getMessage();
        }
    }

    public function isValidDate($date){
        if($date<time()){
            return false;
        }
        return true;
    }

    public function addAction()
    {
        $config = $this->getConfigData();
        $params = $this->_request->getParams();
//        var_dump($params); die;
        $pickup_hour = ($params['pickup_hour'] == '-1' ? date("H",time()) : $params['pickup_hour']);
        $pickup_minute = $params['pickup_minute'] == '-1' ? date("i", time()) : $params['pickup_minute'] * 15;
        $pickup_date = $params['pickup_date'] == '' ? date("m/d/Y", time()) : $params['pickup_date'];
        $pickup_datetime = strtotime($pickup_date . ' ' . $pickup_hour . ':' . $pickup_minute . ':' . date("s", time()));
        $registry = Zend_Registry::getInstance();
        $booking = array(
            'pickup_address' => $params['pickup_address'],
            'delivery_address' => $params['delivery_address'],
            'note_pickup_address' => $params['note_pickup_address'],
            'note_delivery_address' => $params['note_delivery_address'],
            'pickup_hour' => $params['pickup_hour'],
            'pickup_minute' => $params['pickup_minute'],
            'pickup_date' => $params['pickup_date'],
            'payment_checkbox' => $params['payment_checkbox'],
            'term' => $params['term'],
        );
        if ($this->_request->isPost() && $this->getRequest()->getPost('book')) {
            $auth = Zend_Auth::getInstance();
            $id = $auth->getStorage()->read()->id;
            $type = $auth->getStorage()->read()->auth_type;
            if ($type == Auth_Constant_Server::COURIER_TYPE)
                return $this->redirect('pickup');
            if (!$params['pickup_address'] || !$params['delivery_address'] || !$this->isValidDate($pickup_datetime) || !$params['term']) {
                if (strlen($params['note_delivery_address']) >= 511) {
                    $message['note_delivery_address'] = 'Note is too long';
                }
                if (strlen($params['note_pickup_address']) >= 511) {
                    $message['note_pickup_address'] = 'Note is too long';
                }
                if (strlen($params['delivery_address']) >=255) {
                    $message['delivery_address'] = 'Delivery Address is too long';
                }
                if (strlen($params['pickup_address']) >=255) {
                    $message['pickup_address'] = 'Pickup Address is too long';
                }
                if(!$params['delivery_address']){
                    $message['delivery_address'] = 'Delivery Address is required';
                }
                if(!$params['pickup_address']){
                    $message['pickup_address'] = 'Pickup Address is required';
                }
                if(!$this->isValidDate($pickup_datetime)){
                    $message['pickup_date'] = 'The date to deliver must be greater than or equal to the current date';
                }
                if(!$params['term']){
                    $message['term'] = 'You must agree terms and conditions';
                }
                Zend_Registry::getInstance()->session->error->booking = array_merge($booking,array('message' => $message));
                return $this->redirect('pickup/index/book');
            }
            $fee = $params['payment_checkbox'];
            $total_fee = $config[0];
            $sign_fee = 0;
            $insure_fee = 0;
            foreach ($fee as $value) {
                if ($value == 1) $sign_fee = $config[$value];
                if ($value == 2) $insure_fee = $config[$value];
                $total_fee += $config[$value];
            }
            $credit_fee = $total_fee * $config[3] / 100;
            $total_fee += $credit_fee;
            $credit_fee = number_format((float)$credit_fee, 2, '.', '');
            $total_fee = number_format((float)$total_fee, 2, '.', '');
            $params = new Base_Php_Overloader($params);
            $result = Base_Constant_Client::SUCCESSFUL;
            $message = '';
            $newId = null;
            $table = Pickup_Model_DbTable_Pickup::getInstance();
            $data = array(
                'customer_id' => $id,
                'status' => Pickup_Constant_Server::AWAITING,
                'from_address' => $params->pickup_address,
                'to_address' => $params->delivery_address,
                'note_from_address' => $params->note_pickup_address,
                'note_to_address' => $params->note_delivery_address,
                'awaiting_active_time' => date("Y-m-d H:i:s", time()),
                'base_charge_fee' => number_format((float)$config[0], 2, '.', ''),
                'delivery_signature_fee' => number_format((float)$sign_fee, 2, '.', ''),
                'delivery_insure_fee' => number_format((float)$insure_fee, 2, '.', ''),
                'credit_fee' => $credit_fee,
                'total_fee' => $total_fee,
                'preferred_pickup_datetime' => date("Y-m-d H:i:s", $pickup_datetime),
                'created' => date("Y-m-d H:i:s", time()),
                'updated' => date("Y-m-d H:i:s", time()),
                'search_urbs' => $params->pickup_address . ' , ' . $params->delivery_address,
            );
            try {
                if($newId = $table->insert($data)){
                    $auth = Zend_Auth::getInstance();
                    $auth_id = $auth->getStorage()->read()->detail->auth_id;
                    $customer_table = Customer_Model_DbTable_Customer::getInstance();
                    $customer_reference = Customer_Model_DbTable_Customer::getInstance()->getReferenceDetail($auth_id);
                    $where_reference = $table->getAdapter()->quoteInto('auth_id = ?', $auth_id);
                    if ($customer_reference == "") {
                        $reference_new = "Purple Bag It - " . $newId;
                    } else {
                        $reference_new = $customer_reference . ',' . $newId;
                    }
                    $data_reference = array(
                        'reference' => $reference_new
                    );
                    $customer_table->update($data_reference,$where_reference);
                }
                $registry->session->success->pickup = array( 'message' => 'Booked successfully');
                Zend_Registry::getInstance()->session->error->booking = '';
            } catch (Zend_Db_Exception $dbException) {
                $registry->session->error->pickup = array( 'message' => 'Updated fail');
                $result = Base_Constant_Client::FAILED;
                $message = $dbException->getMessage();
            }
            $clientData = array(
                'result' => $result,
                'message' => $message,
                'params' => $params,
            );
            $this->redirect('/auth');
        } elseif ($this->_request->isPost() && $this->getRequest()->getPost('cancel')) {
                $this->redirect('pickup');
        }

    }

    public function editAction()
    {
        $auth = Zend_Auth::getInstance();
        $auth_id = $auth->getStorage()->read()->id;
        $type = $auth->getStorage()->read()->auth_type;
        $id = $this->getRequest()->getParam('id', 0);
        if ($id != (string)(int)$id || $id <= 0) {
            $this->forward('error', 'index', 'base');
        } else {
            $table = Pickup_Model_DbTable_Pickup::getInstance();
            $pickups = $table->fetchAll('id = ' . $id);
            $total = count($pickups);
            switch ($total) {
                case 0:
                    $this->forward('error', 'index', 'base');
                    break;
                case 1:
                    $this->view->pickup = $pickups[0];
                    $customer_tbl = Customer_Model_DbTable_Customer::getInstance();
                    $customer = $customer_tbl->fetchAll('id=' . $pickups[0]->customer_id);
                    if (count($customer) == 1) {
                        $this->view->customer = $customer[0]->firstname . $customer[0]->lastname;
                    } else {
                        $this->view->customer = '';
                    }
                    $courier_tbl = Courier_Model_DbTable_Courier::getInstance();
                    $courier = $courier_tbl->fetchAll('id=' . $pickups[0]->courier_id);
                    if (count($courier) == 1) {
                        $this->view->courier = $courier[0]->firstname . $courier[0]->lastname;
                    } else $this->view->courier = '';
                    switch (true) {
                        case Auth_Constant_Server::CUSTOMER_TYPE == $type && $auth_id == $pickups[0]->customer_id:
                            $this->_response->setBody($this->view->render($this->_verifyScriptName('customer/edit.phtml')));
                            break;
                        case Auth_Constant_Server::COURIER_TYPE == $type && $auth_id == $pickups[0]->courier_id:
                            $this->_response->setBody($this->view->render($this->_verifyScriptName('courier/edit.phtml')));
                            break;
                        case Auth_Constant_Server::STAFF_TYPE == $type:
                            $this->_response->setBody($this->view->render($this->_verifyScriptName('edit.phtml')));
                            break;
                        default:
                            $this->redirect('pickup');
                            break;
                    }

                    break;
                default:
                    throw new Exception('Too many ' . $id);
                    break;
            }
        }
    }

    private function invalidAddress($address){
        $message = '';
        if (strlen($address->delivery_address) >=255) {
            $message = 'Delivery Address is too long';
        }
        if (strlen($address->pickup_address) >=255) {
            $message = 'Pickup Address is too long';
        }
        if (!$address->delivery_address) {
            $message = 'Delivery Address is required';
        }
        if (!$address->pickup_address) {
            $message = 'Pickup Address is required';
        }
        return $message;
    }

    public function updateAction()
    {
        $registry = Zend_Registry::getInstance();
        $message = '';
        $result = Base_Constant_Client::FAILED;
        $detail = '';
        $auth = Zend_Auth::getInstance()->getStorage()->read();
        $url = 'auth';
        if ($this->_request->getParam('click')) {
            if ($this->_request->isPost()) {
                $params = $this->_request->getParams();
                $params = new Base_Php_Overloader($params);
                $id = $params->id;
                $pickup_table = Pickup_Model_DbTable_Pickup::getInstance();
                $db_data = $pickup_table->fetchRow('id=' . $id);
                $db_sts = $db_data['status'];
                if ($db_sts >= Pickup_Constant_Server::ASSIGNED
                    || $auth->auth_type == Auth_Constant_Server::COURIER_TYPE
                ) {
                    $message = "Can't update information :: PickUp is assigned.  ";
                    if ($auth->auth_type == Auth_Constant_Server::COURIER_TYPE) {
                        $message = "Permission denied";
                    }
//                    $detail = $this->view->render($this->_verifyScriptName('pickup/customer_view.phtml'));
                } elseif ($this->invalidAddress($params)){
                    $message = $this->invalidAddress($params);
                } else {
                    $data = array(
                        'from_address' => $params->pickup_address,
                        'note_from_address' => $params->note_pickup_address,
                        'to_address' => $params->delivery_address,
                        'note_to_address' => $params->note_delivery_address,
                        'updated' => date("Y-m-d H:i:s", time()),
                        'search_urbs' => $params->pickup_address . ' , ' . $params->delivery_address,
                    );
                    try {
                        $where = $pickup_table->getAdapter()->quoteInto('id = ?', $params->id);
                        $pickup_table->update($data, $where);
                        $message = "Updated successfully";
                        $result = Base_Constant_Client::SUCCESSFUL;
                        $registry->session->success->pickup = array(
                            'message' => 'Updated successfully');
                    } catch (Zend_Db_Exception $dbException) {
                        $result = Base_Constant_Client::FAILED;
                        $message = $dbException->getMessage();
//                        $registry->session->error->pickup = array(
//                            'message' => 'Updated Fail');
                    }
                }
                $clientData = array(
                    'result' => $result,
                    'message' => $message,
                    'detail' => $detail,
                    'url' => $url
                );
                $this->_response->setBody($this->_helper->getHelper('json')->encodeJson($clientData));
            }
        } else {
            $clientData = array(
                'result' => $result,
                'message' => $message,
                'detail' => $detail,
                'url' => $url
            );
            $this->_response->setBody($this->_helper->getHelper('json')->encodeJson($clientData));
        }
    }

    public function searchAction()
    {
        $status = Pickup_Constant_Server::AWAITING;
        $pickups = Pickup_Model_DbTable_Pickup::getInstance()->getAll($status);
        $this->view->pickups = $pickups;
        $this->_response->setBody($this->view->render($this->_verifyScriptName('list.phtml')));
    }

    public function viewrateAction()
    {
        $status = Pickup_Constant_Server::AWAITING;
        $pickups = Pickup_Model_DbTable_Pickup::getInstance()->getAll($status);
        $this->view->pickups = $pickups;
        $this->_response->setBody($this->view->render($this->_verifyScriptName('view_rate.phtml')));
    }

    public function ratecourierAction()
    {
        $this->rateAction('pickup/rate_courier.phtml');
    }

    public function ratecustomerAction()
    {
        $this->rateAction('pickup/rate_customer.phtml');
    }

    public function rateAction($layout = '')
    {
        $result = Base_Constant_Client::FAILED;
        $message = '';
        $detail = '';
        $url = '';
        if (!$this->getRequest()->getParam('click')) {
            $message = 'Permission is denied';
        } else {
//            $auth = Zend_Auth::getInstance();
//            $auth_id = $auth->getStorage()->read()->id;
//            $type = $auth->getStorage()->read()->auth_type;
            $params = $this->getRequest()->getParams();
            $params = new Base_Php_Overloader($params);
            $id = $params->id;
            $pickup_tbl = Pickup_Model_DbTable_Pickup::getInstance();
            $pickup = $pickup_tbl->fetchRow('id=' . $id);
//            $status = $pickup['status'];
//            $customer_id = $pickup['customer_id'];
//            switch (true) {
//                case Auth_Constant_Server::CUSTOMER_TYPE == $type && $customer_id == $auth_id &&
//                    $status == Pickup_Constant_Server::ACCEPTED || $status == Pickup_Constant_Server::RATED:
//                    break;
//                case Auth_Constant_Server::COURIER_TYPE == $type && $status == Pickup_Constant_Server::RATED:
//                    break;
//                default:
//                    $message = 'Permission is denied';
//                    break;
//            }
            $result = Base_Constant_Client::SUCCESSFUL;
            $message = ' Get successfully';
            $rating = Pickup_Model_DbTable_PickupRating::getInstance()->getRate($id);
            $this->view->rating = $rating;
            $this->view->pickup = $pickup;
            $this->view->pickup_id = $id;
            $detail = $this->view->render($this->_verifyScriptName($layout));
        }
        $clientData = array(
            'result' => $result,
            'message' => $message,
            'detail' => $detail,
            'url' => $url
        );
        $this->_response->setBody($this->_helper->getHelper('json')->encodeJson($clientData));
    }

    public function updaterateAction()
    {
        if ($this->getRequest()->getParam('click')) {
            $detail = '';
            $url = '/auth';
            $params = $this->_request->getParams();
            $ratingValue = $params['rating_value'];
            $ratingId = $params['rating_id'];
            $params = new Base_Php_Overloader($params);
            $result = Base_Constant_Client::FAILED;
            $message = '';
            $rating_table = Pickup_Model_DbTable_PickupRating::getInstance();
            $pickup_table = Pickup_Model_DbTable_Pickup::getInstance();
            $rating = $rating_table->fetchAll('pickup_id = ' . $params->pickup_id);
            $auth = Zend_Auth::getInstance();
            $auth_type = $auth->getStorage()->read()->auth_type;
            if ($params->flag_rate == 1 && $auth_type == Auth_Constant_Server::CUSTOMER_TYPE) {
                $pickup_data = array(
                    'status' => Pickup_Constant_Server::RATED,
                    'rated_active_time' => date("Y-m-d H:i:s", time()),
                    'updated' => date("Y-m-d H:i:s", time()),
                    'customer_note' => $params->note,
                );
            } elseif ($auth_type == Auth_Constant_Server::COURIER_TYPE) {
                $pickup_data = array(
                    'courier_note' => $params->note,
                    'updated' => date("Y-m-d H:i:s", time()),
                    'courier_rate_flag' => 1,
                );

            } else {

            }
            $total_rated = count($rating);

            switch (true) {
                case 0 == $total_rated && Auth_Constant_Server::STAFF_TYPE != $auth_type:
                    $data = array(
                        'pickup_id' => $params->pickup_id,
                        'rating_id' => (int)$ratingId,
                        'created' => date("Y-m-d H:i:s", time()),
                        'updated' => date("Y-m-d H:i:s", time()),
                    );
                    try {
                        if (!empty($ratingValue)) {
                            if ($params->flag_rate == 1 && $auth_type == Auth_Constant_Server::CUSTOMER_TYPE) {
                                $data['rating_courier_value'] = (int)$ratingValue;
                            } elseif ($auth_type == Auth_Constant_Server::COURIER_TYPE) {
                                $data['rating_customer_value'] = (int)$ratingValue;
                            }
                            $newId = $rating_table->insert($data);
                            $where = $pickup_table->getAdapter()->quoteInto('id = ?', $params->pickup_id);
                            $pickup_table->update($pickup_data, $where);
                            $result = Base_Constant_Client::SUCCESSFUL;
                            Zend_Registry::getInstance()->session->success->pickup = array(
                                'message' => 'Rated successfully');
                        } else {
                            $result = Base_Constant_Client::FAILED;
                            $message = 'Please select your rating';
                        }
                    } catch (Zend_Db_Exception $dbException) {
                        $result = Base_Constant_Client::FAILED;
                        $message = $dbException->getMessage();
                    }
                    break;
                case 1 == $total_rated && Auth_Constant_Server::STAFF_TYPE != $auth_type:
                    $data = array(
                        'rating_id' => (int)$ratingId,
                        'updated' => date("Y-m-d H:i:s", time()),
                    );
                    try {
                        if (!empty($ratingValue)) {
                            if ($params->flag_rate == 1 && $auth_type == Auth_Constant_Server::CUSTOMER_TYPE) {
                                $data['rating_courier_value'] = (int)$ratingValue;
                            } elseif ($auth_type == Auth_Constant_Server::COURIER_TYPE) {
                                $data['rating_customer_value'] = (int)$ratingValue;
                            }
                            $where1 = $rating_table->getAdapter()->quoteInto('pickup_id=?', $params->pickup_id);
                            $rating_table->update($data, $where1);
                            $where = $pickup_table->getAdapter()->quoteInto('id = ?', $params->pickup_id);
                            $pickup_table->update($pickup_data, $where);
                            $result = Base_Constant_Client::SUCCESSFUL;
                            $message = 'Updated successfully';
                            Zend_Registry::getInstance()->session->success->pickup = array(
                                'message' => 'Rated successfully');
                        } else {
                            $result = Base_Constant_Client::FAILED;
                            $message = 'Please select your rating';
                        }
                    } catch (Zend_Db_Exception $dbException) {
                        $result = Base_Constant_Client::FAILED;
                        $message = $dbException->getMessage();
                    }
                    break;
                default:
                    $result = Base_Constant_Client::FAILED;
                    $message = 'Can not change your rated';
                    break;
            }
            $clientData = array(
                'result' => $result,
                'message' => $message,
                'detail' => $detail,
                'url' => $url
            );
            $this->_response->setBody($this->_helper->getHelper('json')->encodeJson($clientData));
        } else {
            $this->redirect('/auth');
        }
    }

    public function cancelAction()
    {
        $registry = Zend_Registry::getInstance();
        $auth = Zend_Auth::getInstance();
        $type = $auth->getStorage()->read()->auth_type;
        if ($type == Auth_Constant_Server::COURIER_TYPE) {
            return $this->redirect('auth/');
        }
        $url = '/auth';
        $result = Base_Constant_Client::FAILED;
        $message = '';
        $detail = '';
        $config_data = $this->getConfigData();
        $params = $this->_request->getParams();
        $params = new Base_Php_Overloader($params);
        $id = $params->id;
        $pickup_table = Pickup_Model_DbTable_Pickup::getInstance();
        $pickups = $pickup_table->fetchAll('id = ' . $id);
        $pickup_data = array(
            'status' => Pickup_Constant_Server::CANCELLED,
            'cancelled_active_time' => date("Y-m-d H:i:s", time()),
            'updated' => date("Y-m-d H:i:s", time()),
        );
        $total = count($pickups);
        switch ($total) {
            case 0:
                $this->forward('error', 'index', 'base');
                break;
            case 1:
                $pickup = $pickups[0];
                $current_status = $pickup->status;
                $total_fee = $pickup->total_fee;
                $created = $pickup->created;
                if (($config_data[19]*60*60 + strtotime($created)) < time()) {
                    $result = Base_Constant_Client::FAILED;
                    $message = 'The time to cancel is over';
                }
                elseif ($current_status == Pickup_Constant_Server::AWAITING || $current_status == Pickup_Constant_Server::ASSIGNED) {
                    if ($current_status == Pickup_Constant_Server::ASSIGNED && $config_data[18]) {
                        $cancel_fee = $total_fee * $config_data[18] / 100;
                        $pickup_data['cancel_fee'] = number_format((float)$cancel_fee, 2, '.', '');
                    }
                    $where = $pickup_table->getAdapter()->quoteInto('id = ?', $id);
                    $pickup_table->update($pickup_data, $where);
                    $content = array(
                        'sender' => Zend_Registry::getInstance()->appConfig['smtp']['username'],
                        'nameSender' => 'DLIVR',
                        'recipient' => $config_data[20],
                        'nameRecipient' => 'DLIVR',
                        'subject' => 'Cancel Pickup',
                        'body' => 'The Pickup ' . $pickup->id .  ' is cancelled by customer'
                    );
                    if(!Base_Helper_Mail::sendMail($content)) {
                        Base_Helper_Log::getInstance()->log(PHP_EOL . date('H:i:s :::: ') . ' [PICKUP] Can not send mail to HO: ' . 'The Pickup ' . $pickup->id . ' is cancelled by customer');
                    }
                    $message = "Updated successfully";
                    $result = Base_Constant_Client::SUCCESSFUL;
                    $registry->session->success->pickup = array(
                        'message' => 'Updated successfully');
                    if ($current_status == Pickup_Constant_Server::ASSIGNED){
                        // report to courier
                        $courier_auth_id = $pickup->courier_id;
                        $courier_tbl = Courier_Model_DbTable_Courier::getInstance();
                        $couriers = $courier_tbl->fetchAll('auth_id = '.$courier_auth_id);
                        if(count($couriers) == 1){
                            $receiver = $couriers[0]['email'];
                            $receiver_name = $couriers[0]['contact_firstname'].$couriers[0]['contact_lastname'];
                            $content = array(
                                'sender' => Zend_Registry::getInstance()->appConfig['smtp']['username'],
                                'nameSender' => 'DLIVR',
                                'recipient' => $receiver,
                                'nameRecipient' => $receiver_name,
                                'subject' => 'Cancel Pickup',
                                'body' => 'The Pickup (ID = ' . $id .  ') is cancelled by customer'
                            );
                            if(!Base_Helper_Mail::sendMail($content)) {
                                Base_Helper_Log::getInstance()->log(PHP_EOL . date('H:i:s :::: ') . ' [PICKUP] Can not send mail to HO: ' . 'The Pickup (ID = ' . $pickup->id . ') is cancelled by customer');
                            }
                        } else {
                            Base_Helper_Log::getInstance()->log(PHP_EOL . date('H:i:s :::: ') . ' [PICKUP] Too many couriers with auth_id = ' . $courier_auth_id );
                        }
                    }
                    break;
                } else {
                    $message = "Updated Fail";
                    $result = Base_Constant_Client::FAILED;
                    $registry->session->error->pickup = array(
                        'message' => 'Updated Fail');
                    break;
                }
                break;
            default:
                throw new Exception('Too many ' . $id);
                break;
        }
        $clientData = array(
            'result' => $result,
            'message' => $message,
            'detail' => $detail,
            'url' => $url
        );
        $this->_response->setBody($this->_helper->getHelper('json')->encodeJson($clientData));
    }

    public function assignedAction()
    {
        $auth = Zend_Auth::getInstance();
        $auth_id = $auth->getStorage()->read()->id;
        $type = $auth->getStorage()->read()->auth_type;
        $result = Base_Constant_Client::FAILED;
        $message = '';
        $url = '/auth';
        $detail = '';
        if ($type != Auth_Constant_Server::STAFF_TYPE) {
            $result = Base_Constant_Client::FAILED;
            $message = 'Permission is denied';
        }
        $new_status = Pickup_Constant_Server::ASSIGNED;
        $params = $this->_request->getParams();
        $params = new Base_Php_Overloader($params);
        $id = $params->id;
        $courier_id = $params->courier_authID;
        $pickup_table = Pickup_Model_DbTable_Pickup::getInstance();
        $pickups = $pickup_table->fetchAll('id = ' . $id);
        $total = count($pickups);
        $pickup_data = array(
            'courier_id' => $courier_id,
            'status' => Pickup_Constant_Server::ASSIGNED,
            'assigned_active_time' => date("Y-m-d H:i:s", time()),
            'updated' => date("Y-m-d H:i:s", time()),
        );
        switch ($total) {
            case 0:
                $this->forward('error', 'index', 'base');
                break;
            case 1:
                $current_status = $pickups[0]->status;
                if ($current_status >= $new_status) {
                    $result = Base_Constant_Client::FAILED;
                    $message = 'Pickup is assigned to another';
                    break;
                }
                $result = Base_Constant_Client::SUCCESSFUL;
                $where = $pickup_table->getAdapter()->quoteInto('id = ?', $id);
                $pickup_table->update($pickup_data, $where);
                break;
            default:
                throw new Exception('Too many ' . $id);
                break;
        }
        $clientData = array(
            'result' => $result,
            'message' => $message,
            'detail' => $detail,
            'url' => $url
        );
        $this->_response->setBody($this->_helper->getHelper('json')->encodeJson($clientData));
    }

    public function isValid($status)
    {
        $auth = Zend_Auth::getInstance();
        $type = $auth->getStorage()->read()->auth_type;
        switch (true) {
            case Auth_Constant_Server::COURIER_TYPE == $type && ($status == Pickup_Constant_Server::ASSIGNED):
            case Auth_Constant_Server::COURIER_TYPE == $type && ($status == Pickup_Constant_Server::UNASSIGNED):
                break;
        }
    }

    public function isValidUser()
    {
        $auth = Zend_Auth::getInstance();
        $auth_id = $auth->getStorage()->read()->id;
        $type = $auth->getStorage()->read()->auth_type;
        $courier_tbl = Courier_Model_DbTable_Courier::getInstance();
        $courier_info = $courier_tbl->fetchRow('auth_id=' . $auth_id);
        $message = '';
        switch (true) {
            case $type == Auth_Constant_Server::COURIER_TYPE:
                if (!$courier_info['can_assign']) {
                    $message = 'You can\'t assign yourself. Please contact admin';
                }
                if (!$courier_info['head_office_approved']) {
                    $message = 'You are unapproved. Please contact admin';
                }
                break;
            case $type == Auth_Constant_Server::CUSTOMER_TYPE:
                break;
            case $type == Auth_Constant_Server::STAFF_TYPE:
                break;
        }
        return $message;
    }

    public function changestatusAction()
    {
        $auth = Zend_Auth::getInstance();
        $auth_id = $auth->getStorage()->read()->id;
        $courier_tbl = Courier_Model_DbTable_Courier::getInstance();
        $message = '';
        $result = Base_Constant_Client::FAILED;
        $template = $this->getViewTemplate();
        $url = '/auth';
        if ($this->isValidUser()) {
            $message = $this->isValidUser();
            $result = Base_Constant_Client::FAILED;
        } else {
            $params = $this->_request->getParams();
            $params = new Base_Php_Overloader($params);
            $id = $params->id;
            $new_status = $params->status;
            $pickup_table = Pickup_Model_DbTable_Pickup::getInstance();
            $pickups = $pickup_table->fetchAll('id = ' . $id);
            $pickup_data = array(
                'status' => $new_status,
                'updated' => date("Y-m-d H:i:s", time()),
            );
            $registry = Zend_Registry::getInstance();
            switch ($new_status) {
                case Pickup_Constant_Server::ASSIGNED:
                    $pickup_data['assigned_active_time'] = date("Y-m-d H:i:s", time());
                    break;
                case Pickup_Constant_Server::PICKED_UP:
                    $pickup_data['picked_up_active_time'] = date("Y-m-d H:i:s", time());
                    break;
                case Pickup_Constant_Server::CANCELLED:
                    $pickup_data['cancelled_active_time'] = date("Y-m-d H:i:s", time());
                    break;
                case Pickup_Constant_Server::DELIVERED:
                    $pickup_data['delivered_active_time'] = date("Y-m-d H:i:s", time());
                    break;
                case Pickup_Constant_Server::ACCEPTED:
                    $pickup_data['accepted_active_time'] = date("Y-m-d H:i:s", time());
                    break;
                case Pickup_Constant_Server::RATED:
                    $pickup_data['rated_active_time'] = date("Y-m-d H:i:s", time());
                    break;
            }
            $total = count($pickups);
            switch ($total) {
                case 0:
                    $this->forward('error', 'index', 'base');
                    break;
                case 1:
                    $current_status = $pickups[0]->status;
                    if ($new_status == Pickup_Constant_Server::UNASSIGNED && $current_status == Pickup_Constant_Server::ASSIGNED) {
                        $where = $pickup_table->getAdapter()->quoteInto('id = ?', $id);
                        $courier_id = $pickups[0]->courier_id;
                        $pickup_data['courier_id'] = null;
                        $pickup_table->update($pickup_data, $where);
                        $courier_data = array(
                            'can_assign' => 0,
                            'updated' => date("Y-m-d H:i:s", time()),
                        );
                        $where1 = $courier_tbl->getAdapter()->quoteInto('auth_id=?', $courier_id);
                        $courier_tbl->update($courier_data, $where1);
                        $customer_auth_id = $pickups[0]->customer_id;
                        $customer_tbl = Customer_Model_DbTable_Customer::getInstance();
                        $customers = $customer_tbl->fetchAll('auth_id='.$customer_auth_id);
                        $config_data = $this->getConfigData();
                        if(count($customers) == 1){
                            $content = array(
                                "sender" => Zend_Registry::getInstance()->appConfig['smtp']['username'],
                                "nameSender" => 'DLIVR',
                                "recipient" => $config_data[20],
                                'nameRecipient' => 'Super admin',
                                "subject" => "Pickup is unassigned",
                                "body" => "The Pickup (id=".$pickups[0]->id.") is unassigned by courier "
                            );
                        }
                        if( !Base_Helper_Mail::sendMail($content)){
                            Base_Helper_Log::getInstance()->log(PHP_EOL . date('H:i:s :::: ') . ' [PICKUP] Can not send mail to ' . $config_data[20]);
                        }
                        $result = Base_Constant_Client::SUCCESSFUL;
                        $message = 'Updated successfully!';
                        $url = '/auth';
                        $registry->session->success->pickup = array(
                            'message' => $message
                        );
                        break;
                    } elseif ($current_status >= $new_status) {
                        $result = Base_Constant_Client::FAILED;
                        $message = "Can't change pickup's status";
                        break;
                    } else {
                        if ($new_status == Pickup_Constant_Server::ASSIGNED) {
                            $pickup_data['courier_id'] = $auth_id;
                            if ($this->checkPaypal(number_format($pickups[0]['total_fee'], 2, '.', ','), $pickups[0]['customer_id'])) {
                                $where = $pickup_table->getAdapter()->quoteInto('id = ?', $id);
                                $pickup_table->update($pickup_data, $where);

//                                $table = Customer_Model_DbTable_Customer::getInstance();
//                                $where_reference = $pickup_table->getAdapter()->quoteInto('auth_id = ?', $pickups[0]['customer_id']);
//                                $customer = Customer_Model_DbTable_Customer::getInstance()->getDetail($pickups[0]['customer_id']);
//                                 if(!$customer['reference'] && $customer['reference'] == "" ){
//                                     $reference_new = "Purple Bag It - ".$id;
//                                }else{
//                                     $reference =  $customer['reference'];
//                                     $reference_new = $reference.','.$id;
//                                 }
//                                $data_reference = array(
//                                    'reference' => $reference_new
//                                );
//                                $table->update($data_reference,$where_reference);

                                $table = Customer_Model_DbTable_Customer::getInstance();
                                $where = $table->getAdapter()->quoteInto('auth_id = ?',  $pickups[0]['customer_id']);
                                $customer = $table->fetchRow($where);
                                $body = "Your pickup (ID = " . $id . ") is assigned. Please check detail in your  current pickup";

                                $content = array(
                                    "sender" => Zend_Registry::getInstance()->appConfig['smtp']['username'],
                                    "nameSender" => "DLIVR",
                                    "subject" => "Pickup is assigned",
                                    "nameRecipient" => "DLIVR"
                                );
                                $content['body'] = $body;
                                $content['recipient'] = $customer->email;
                                if(!Base_Helper_Mail::sendMail($content)){
                                    Base_Helper_Log::getInstance()->log(PHP_EOL . date('H:i:s :::: ') . ' [PICKUP] Can not send mail to ' . $content['recipient']);
                                }

                                $result = Base_Constant_Client::SUCCESSFUL;
                                $message = 'Updated successfully!';
                                $url = '/auth';
                            } else {
                                $result = Base_Constant_Client::FAILED;
                                $message = "The customer has encountered an error in payment process for this pickup. Please try again later";
                                $table = Customer_Model_DbTable_Customer::getInstance();
                                $where = $table->getAdapter()->quoteInto('auth_id = ?',  $pickups[0]['customer_id']);
                                $customer = $table->fetchRow($where);
                                $bodyAdmin = "Courier not assign. Please contact and check info of Customer with email " . $customer->email . " and mobile ".$customer->mobile .". With ".Zend_Registry::getInstance()->session->book->error;
                                $body = "Courier not assign please check info Paypal with " .Zend_Registry::getInstance()->session->book->error;

                                $content = array(
                                    "sender" => Zend_Registry::getInstance()->appConfig['smtp']['username'],
                                    "nameSender" => "DLIVR",
                                    "subject" => "Error Not Assign For Courier",
                                    "nameRecipient" => "DLIVR"
                                );
                                //send email notify for customer
                                $content['body'] = $body;
                                $content['recipient'] = $customer->email;
                                if(!Base_Helper_Mail::sendMail($content)){
                                    $result = Base_Constant_Client::FAILED;
                                    Base_Helper_Log::getInstance()->log(PHP_EOL . date('H:i:s :::: ') . ' [PICKUP] Can not send mail to ' . $content['recipient']);
                                }
                                //send email notify for admin
                                $content['body'] = $bodyAdmin;
                                $content['recipient'] = Zend_Registry::getInstance()->appConfig['smtp']['username'];
                                if( !Base_Helper_Mail::sendMail($content)){
                                    $result = Base_Constant_Client::FAILED;
                                    Base_Helper_Log::getInstance()->log(PHP_EOL . date('H:i:s :::: ') . ' [PICKUP] Can not send mail to ' . $content['recipient']);
                                }
                            }
                        } else {
                            $where = $pickup_table->getAdapter()->quoteInto('id = ?', $id);
                            $pickup_table->update($pickup_data, $where);
                            $result = Base_Constant_Client::SUCCESSFUL;
                            $message = 'Updated successfully!';
                            if($new_status == Pickup_Constant_Server::DELIVERED){
                                // report to customer
                                $table = Customer_Model_DbTable_Customer::getInstance();
                                $where = $table->getAdapter()->quoteInto('auth_id = ?',  $pickups[0]['customer_id']);
                                $customer = $table->fetchRow($where);
                                $body = "Courier delivered your pickup (ID = " . $id . ")";

                                $content = array(
                                    "sender" => Zend_Registry::getInstance()->appConfig['smtp']['username'],
                                    "nameSender" => "DLIVR",
                                    "subject" => "Pickup is delivered",
                                    "nameRecipient" => "DLIVR"
                                );
                                $content['body'] = $body;
                                $content['recipient'] = $customer->email;
                                if(!Base_Helper_Mail::sendMail($content)){
                                    Base_Helper_Log::getInstance()->log(PHP_EOL . date('H:i:s :::: ') . ' [PICKUP] Can not send mail to ' . $content['recipient']);
                                }
                            }
                        }

                        $registry->session->success->pickup = array(
                            'message' => $message
                        );
                    }
                    break;
                default:
                    throw new Exception('Too many ' . $id);
                    break;
            }
        }
        $clientData = array(
            'result' => $result,
            'message' => $message,
            'detail' => $this->view->render($this->_verifyScriptName($template)),
            'url' => $url
        );
        $this->_response->setBody($this->_helper->getHelper('json')->encodeJson($clientData));
    }

    public function getViewTemplate()
    {
        $auth = Zend_Auth::getInstance();
        $auth_type = $auth->getStorage()->read()->auth_type;
        $template = '';
        switch (true) {
            case Auth_Constant_Server::CUSTOMER_TYPE == $auth_type;
                $template = 'pickup/customer_view.phtml';
                break;
            case Auth_Constant_Server::COURIER_TYPE == $auth_type;
                $template = 'pickup/courier_view.phtml';
                break;
            case Auth_Constant_Server::STAFF_TYPE == $auth_type;
                $template = 'pickup/staff_view.phtml';
                break;
        }
        return $template;
    }

    public function detailAction()
    {
        $template = $this->getViewTemplate();
        $table = Pickup_Model_DbTable_Pickup::getInstance();
        $auth = Zend_Auth::getInstance();
        switch (true) {
            case (Auth_Constant_Server::STAFF_TYPE == $auth->getStorage()->read()->auth_type):
                $canDrag = 0;
                break;
            case (Auth_Constant_Server::CUSTOMER_TYPE == $auth->getStorage()->read()->auth_type):
                $canDrag = 1;
                break;
            case (Auth_Constant_Server::COURIER_TYPE == $auth->getStorage()->read()->auth_type):
                $canDrag = 0;
                break;
            default:
                break;
        }

        if ($this->getParam("click")) {
            $result = Base_Constant_Client::SUCCESSFUL;
            $pickup = $table->fetchAll("id =" . $this->getParam("id"));
            $total = count($pickup);
            switch ($total) {
                case 0:
                    $result = Base_Constant_Client::FAILED;
                    $message = "No result match";
                    break;
                case 1:
                    $this->view->detail = $pickup[0];
                    $canDrag = ($pickup[0]['status'] == Pickup_Constant_Server::AWAITING) ? $canDrag : 0;
                    $this->view->config = $this->getConfigData();
                    $message = "";
                    break;
                default:
                    $result = Base_Constant_Client::FAILED;
                    $message = "No result match";
                    break;
            }

            $clientData = array(
                'result' => $result,
                'message' => $message,
                'detail' => $this->view->render($this->_verifyScriptName($template)),
                'canDrag' => $canDrag,
            );
            $this->_response->setBody($this->_helper->getHelper('json')->encodeJson($clientData));
        }
    }

    public function getcourierlistAction()
    {
        $param = $this->_request->getParams();
        $table = Courier_Model_DbTable_Courier::getInstance();
        $couriers = $table->getAll();
        $this->view->pickup_id = $param['pickup_id'];
        $this->view->couriers = $couriers;
        $result = Base_Constant_Client::SUCCESSFUL;
        $message = 'List couriers successfully';
        $clientData = array(
            'result' => $result,
            'message' => $message,
            'detail' => $this->view->render($this->_verifyScriptName('courier/courier_list.phtml')),
        );
        $this->_response->setBody($this->_helper->getHelper('json')->encodeJson($clientData));
    }

    public function getfeeAction()
    {
        $config = $this->getConfigData();
        $params = $this->_request->getParams();
        $params = new Base_Php_Overloader($params);
        $id = $params->id;
        $pickup_table = Pickup_Model_DbTable_Pickup::getInstance();
        $pickup = $pickup_table->fetchRow('id = ' . $id);
        $cancel_fee = $pickup['total_fee'] * $config[18] / 100;

        if (($config[19]*60*60 + strtotime($pickup['created'])) < time()) {
            $result = Base_Constant_Client::FAILED;
            $message = 'The time to cancel is over. You only can cancel pickup after booking '.$config[19] .' hour';
        }
        else{
            if (($config[19]*60*60 + strtotime($pickup['created'])) >= time() && $pickup['status'] == Pickup_Constant_Server::AWAITING) {
                $cancel_fee = 0;
            }
            $result = Base_Constant_Client::SUCCESSFUL;
            $message = 'List couriers successfully';
        }
        $clientData = array(
            'result' => $result,
            'cancel_fee' => number_format((float)$cancel_fee, 2, '.', ''),
            'message' => $message,
            'detail' => $this->view->render($this->_verifyScriptName('courier/courier_list.phtml')),
        );
        $this->_response->setBody($this->_helper->getHelper('json')->encodeJson($clientData));
    }
}
