<?php

class Customer_RegisterController extends Base_Controller_Action_Backend
{
    static $personal = 1;
    static $userData = 2;
    static $preferences = 3;

    private $_uploadHelper;
    private $_validateHelper;

    private $_session;
    private $_messageHelper;
    private $_messagesRegister;

    private $_divPersonal;
    private $_divUpload;
    private $_divPreferences;

    private $_tableAuth;
    private $_tableCustomer;

    public function initOther()
    {
        $this->_session = Zend_Registry::getInstance()->session->customerRegister;

        $this->_tableCustomer = Customer_Model_DbTable_Customer::getInstance();
        $this->_tableAuth = Auth_Model_DbTable_Auth::getInstance();

        $this->_uploadHelper = new Base_Controller_Helper_Upload();
        $this->_validateHelper = new Base_Controller_Helper_Validate();

        $this->_messageHelper = Base_Helper_Message::getInstance();
        $this->_messagesRegister = $this->_messageHelper->registration;

        $this->_divPersonal = '#register-personal-content';
        $this->_divUpload = '#cupload-id-content';
        $this->_divPreferences = '#cpreferences-content';
    }

    public function indexAction()
    {
        $this->_session->step = self::$personal;
        $this->personalAction();
    }

    public function getFields($page)
    {
        /**
         * Fields for step 1 personal
         */
        $stepPersonal = array(
            'firstname' => 'name',
            'lastname' => 'name',
            'email' => 'email',

            'contact_number' => 'tel',
            'mobile' => 'tel',
            'fax' => 'tel',

            'image' => 'required',
            'address' => 'address',
            'suburb' => 'required',
            'postcode' => 'postcode',
            'customer_state' => 'required',
            'country' => 'required',

            'card_type' => 'required',
            'card_number' => 'number',
            'card_name' => 'required',
            'ccv' => 'ccv',
            'expiry_month' => 'number',
            'expiry_year' => 'number',
        );
        /**
         * Fields for auth table
         */
        $userData = array(
            'app_email' => 'email',
            'app_password' => 'required',
        );
        /**
         * Fields for step 2 Preferences
         */
        $stepPreferences = array(
            'preferred_region' => 'required',
            'preferred_pickup_address' => 'required',
            'preferred_pickup_suburb' => 'required',
            'preferred_pickup_state' => 'required',
            'preferred_pickup_postcode' => 'postcode',

            'preferred_delivery_address' => 'required',
            'preferred_delivery_suburb' => 'required',
            'preferred_delivery_state' => 'required',
            'preferred_delivery_postcode' => 'postcode',
        );
        if (self::$personal == $page) return $stepPersonal;
        if (self::$preferences == $page) return $stepPreferences;
        if (self::$userData == $page) return $userData;
    }

    public function personalAction()
    {
        $pageMessage = 'registration';
        $typeMessage = 'customer';
        $this->view->active = 'personal';

        if ($this->_request->isPost() && $this->_request->getParam('register_personal')) {
            $params = $this->_request->getParams();
            $messages = array();
            $data = array();
            $userData = array();
            $messageError = '';
            $result = false;

            /** Get fiels value, type data in customer personal */
            $inputPersonal = $this->getFields(self::$personal);
            /**
             * Get customer info (value / message)
             * @var &$data , &$messages
             */
            $this->getInfo($data, $messages, $inputPersonal, $pageMessage, $typeMessage);

            /** Get fiels value, type data in customer personal */
            $inputUser = $this->getFields(self::$userData);
            /**
             * Get user (auth table) info (value / message)
             * @var &$userData , &$messages
             */
            $this->getInfo($userData, $messages, $inputUser, $pageMessage, $typeMessage);

            /** Keep data user enter */
            $this->_session->user = null;
            $this->_session->auth = null;
            $this->_session->personal_temp = array_merge($data, $userData, $params);

            if (count($messages) < 1) {
                /**
                 * Combine month and year to field expiry_date
                 * after delete 2 field form @var $data
                 */
                $data['expiry_date'] = $data['expiry_month'] . '/' . $data['expiry_year'];
                unset($data['expiry_month']);
                unset($data['expiry_year']);
                /**
                 * Combine firstname and lastname to field search_name
                 * insert content to 2 fields : contact_firstname, contact_lastname
                 */
                $data['search_name'] = $params['firstname'] . ' , ' . $params['lastname'];
                $data['contact_firstname'] = $params['firstname'];
                $data['contact_lastname'] = $params['lastname'];

                $this->_session->user = $data;
                $this->_session->auth = $userData;

                /** Prepare date time*/
                $datetime = new DateTime();
                $timeNow = $datetime->format('Y\-m\-d\ h:i:s');
                /**
                 * Extra fields for customer personal info (customer table)
                 */
                $this->_session->user['id'] = '';
                $this->_session->user['updated'] = $timeNow;
                $this->_session->user['created'] = $timeNow;

                /** Add more 2 fields for auth table */
                $this->_session->auth['auth_type'] = Auth_Constant_Server::CUSTOMER_TYPE;
                $this->_session->auth['disabled'] = 0;

                try {
                    switch (true) {
                        case 0 < count($this->_session->user) :
                            $newId = $this->_tableAuth->insert($this->_session->auth);

                            if ($newId) {
                                $this->_session->user['auth_id'] = $newId;
                                $newCustomer = $this->_tableCustomer->insert($this->_session->user);
                            }
                            break;
                        default :
                            $this->view->messages = 'Register has failed. Please follow all steps in registration form.';
                            $this->_response->setBody($this->view->render($this->_verifyScriptName('error.phtml')));
                            return;
                            break;
                    }
                } catch (Zend_Db_Exception $dbException) {
                    $result = Base_Constant_Client::FAILED;
                    $messageError = $dbException->getMessage();
                }
                if (!isset($newCustomer) && isset($newId)) {
                    $this->_tableAuth->delete('id = ' . $newId);
                    $this->view->message = $messageError;
                    $this->_response->setBody($this->view->render($this->_verifyScriptName('error.phtml')));
                    return;
                }
                /**
                 * Success
                 */
                if (!$result) {
                    /**
                     * move file from "temp" path to "upload" path
                     */
                    if ($newCustomer) {
                        $this->_uploadHelper->moveImage($data['image']);
                    }
                    /**
                     * Delete session registration
                     * and hide menu left
                     */
                    unset(Zend_Registry::getInstance()->session->customerRegister);
                    $this->view->hideMenuLeft = true;
                    /**
                     * Redirect to success page
                     */
                    Zend_Registry::getInstance()->session->registerSuccess->flag = true;
                    $this->redirect('customer/register/success');
                    return;
                }else{
                    $this->view->message = $messageError;
                    $this->_response->setBody($this->view->render($this->_verifyScriptName('error.phtml')));
                    return;
                }
            } else {
                $this->view->messages = $messages;
            }
            $this->_session->personal_temp = array_merge($data, $userData, $params);
        }
        $this->view->div = $this->_divPersonal;
        $this->_response->setBody($this->view->render($this->_verifyScriptName('account/customer-register.phtml')));
    }

    public function preferencesAction()
    {
        $this->view->active = 'preferences';
        $messageError = '';

        if ($this->_request->isPost() && $this->_request->getParam('register_preferences')) {

            $params = $this->_request->getParams();
            $pageMessage = 'registration';
            $typeMessage = 'customer_preferences';
            $messages = array();
            $data = array();
            $result = false;

            /** Get fields in preferences step */
            $inputReg = $this->getFields(self::$preferences);
            /** Check and validate customer preference data */
            $this->getInfo($data, $messages, $inputReg, $pageMessage, $typeMessage);

            $this->_session->preferences = null;
            /** Keep data user enter */
            $this->_session->preferences_temp = array_merge($data, $params);

            if (count($messages) < 1) {
                $this->_session->preferences = $data;
                /** Prepare date time*/
                $datetime = new DateTime();
                $timeNow = $datetime->format('Y\-m\-d\ h:i:s');
                /**
                 * Extra fields for customer personal info (customer table)
                 */
                $this->_session->user['id'] = '';
                $this->_session->user['updated'] = $timeNow;
                $this->_session->user['created'] = $timeNow;

                /** Add more 2 fields for auth table */
                $this->_session->auth['auth_type'] = Auth_Constant_Server::CUSTOMER_TYPE;
                $this->_session->auth['disabled'] = 0;

                try {
                    switch (true) {
                        case 0 < count($this->_session->user) :
                            $newId = $this->_tableAuth->insert($this->_session->auth);
                            $data = array_merge(
                                $this->_session->preferences,
                                $this->_session->user
                            );
                            if ($newId) {
                                $data['auth_id'] = $newId;
                                $newCustomer = $this->_tableCustomer->insert($data);
                            }
                            break;
                        default :
                            $this->view->messages = 'Register has failed. Please follow all steps in registration form.';
                            $this->_response->setBody($this->view->render($this->_verifyScriptName('error.phtml')));
                            return;
                            break;
                    }
                } catch (Zend_Db_Exception $dbException) {
                    $result = Base_Constant_Client::FAILED;
                    $messageError = $dbException->getMessage();
                }
                if (!isset($newCustomer) && isset($newId)) {
                    $this->_tableAuth->delete('id = ' . $newId);
                    $this->view->message = $messageError;
                    $this->_response->setBody($this->view->render($this->_verifyScriptName('error.phtml')));
                    return;
                }
                /**
                 * Success
                 */
                if (!$result) {
                    /**
                     * move file from "temp" path to "upload" path
                     */
                    if ($newCustomer) {
                        $this->_uploadHelper->moveImage($data['image']);
                    }
                    /**
                     * Delete session registration
                     * and hide menu left
                     */
                    unset(Zend_Registry::getInstance()->session->customerRegister);
                    $this->view->hideMenuLeft = true;
                    /**
                     * Redirect to success page
                     */
                    Zend_Registry::getInstance()->session->registerSuccess->flag = true;
                    $this->redirect('customer/register/success');
                    return;
                }else{
                    $this->view->message = $messageError;
                    $this->_response->setBody($this->view->render($this->_verifyScriptName('error.phtml')));
                    return;
                }
            } else {
                $this->_session->preferences_temp = array_merge($data, $params);
                $this->view->messages = $messages;
            }
        }
        $this->view->div = $this->_divPreferences;
        $this->_response->setBody($this->view->render($this->_verifyScriptName('account/customer-register.phtml')));
    }

    public function successAction()
    {
        if (Zend_Registry::getInstance()->session->registerSuccess->flag) {
            unset(Zend_Registry::getInstance()->session->registerSuccess->flag);
        } else {
            $this->redirect(BASE_URL);
        }
        $this->view->message = 'Your register has been completed successfully!.' .
            '<br><a class="login-link" href="' . BASE_URL . '/auth/index/login">Go to login page</a>.';
        $this->_response->setBody($this->view->render($this->_verifyScriptName('success.phtml')));
    }

    function getInfo(&$data, &$messages, $inputRegex, $pageMessage, $typeMessage)
    {
        $params = $this->_request->getParams();
        foreach ($inputRegex as $key => $val) {
            switch ($key) {
                case 'is_company':
                    $data[$key] = $val;
                    break;
                case 'image':
                    /**
                     * Upload file bill
                     * @var &$data , &$messages
                     */
                    $this->checkAndUpload($data, $messages, 'image', 'registration', $typeMessage);
                    break;
                case 'alternative_email':
                    if (!trim($params[$key])) {
                        break;
                    }
                case 'email':
                    $params[$key] = str_replace('"', '', $params[$key]);
                    if ($this->_validateHelper->validation($params[$key], $val)) {
                        $data[$key] = $params[$key];
                        $where = $this->_tableCustomer->getAdapter()->quoteInto($key . ' = ?', $params[$key]);
                        $row = $this->_tableCustomer->fetchAll($where);
                        switch (count($row)) {
                            case 0:
                                break;
                            default:
                                $messages[$key] = $this->_messagesRegister->customer->email->taken;
                                break;
                        }
                    } else {
                        $messages[$key] = Base_Helper_Message::$listMessage[$pageMessage][$typeMessage][$key]['message'];
                        $data[$key] = '';
                    }
                    break;
                case 'preferred_pickup_suburb_ids':
                case 'preferred_delivery_suburb_ids':
                    if ($this->_validateHelper->validation($params[$key], $val)) {
                        $data[$key] = implode(",", $params[$key]);
                    } else {
                        $messages[$key] = Base_Helper_Message::$listMessage[$pageMessage][$typeMessage][$key]['message'];
                        $data[$key] = '';
                    }
                    break;
                case 'app_email':
                    $params[$key] = str_replace('"', '', $params[$key]);
                    if ($this->_validateHelper->validation($params[$key], $val)) {
                        $data[$key] = $params[$key];
                        $where = $this->_tableAuth->getAdapter()->quoteInto('app_email = ?', str_replace('"', "", $params['app_email']));
                        $row = $this->_tableAuth->fetchAll($where);
                        switch (count($row)) {
                            case 0:
                                break;
                            default:
                                $messages['app_email'] = $this->_messagesRegister->customer->app_email->taken;
                                $data[$key] = '';
                                break;
                        }
                    } else {
                        $messages[$key] = Base_Helper_Message::$listMessage[$pageMessage][$typeMessage][$key]['message'];
                        $data[$key] = '';
                    }
                    break;
                default:
                    if ('app_password' == $key) {
                        if ($params[$key] == '' && $this->_session->password != '') {
                            $data[$key] = sha1($this->_session->password);
                            break;
                        }
                    }
                    if ($this->_validateHelper->validation($params[$key], $val)) {
                        $data[$key] = $params[$key];
                        if ('app_password' == $key) {
                            $data[$key] = sha1($params[$key]);
                            $this->_session->password = $params[$key];
                        }
                    } else {
                        $messages[$key] = Base_Helper_Message::$listMessage[$pageMessage][$typeMessage][$key]['message'];
                        $data[$key] = '';
                    }
                    break;
            }
        }
    }

    public function checkAndUpload(&$data, &$messages, $input, $pageMessage, $typeMessage)
    {
        $params = $this->_request->getParams();
        switch (true) {
            /** No upload (if no exist) */
            case '' == $params[$input . '_temp'] && '' == $_FILES[$input]['name'] :
                $messages[$input] = Base_Helper_Message::$listMessage[$pageMessage][$typeMessage][$input]['message'];
                $data[$input] = '';
                break;
            /** No upload (if have exist file) */
            case '' != $params[$input . '_temp'] && '' == $_FILES[$input]['name'] :
                $data[$input] = $params[$input . '_temp'];
                break;
            /** Do upload */
            default :
                $info = pathinfo($_FILES[$input]['name']);
                $imgEx = strtolower($info['extension']);
                if (in_array($imgEx, Base_Constant_Server::$allows)) {
                    $data[$input] = $_FILES[$input]['name'];
                    $errors = array();
                    $data[$input] = $this->_uploadHelper->upload($input, $errors);
                    if (!$data[$input]) {
                        $messages[$input] = implode(' ', $errors);
                        break;
                    }
                    /** If has exist file */
                    if ('' != $params[$input . '_temp']) {
                        /** Remove exist file */
                        $this->_uploadHelper->deleteImage($params[$input . '_temp'], 'temp');
                    }
                } else {
                    $messages[$input] = 'This file is not support. Please check again.';
                }
                break;
        }
    }
}