<?php

class Customer_ProfileController extends Base_Controller_Action_Backend
{
    static $personal = 1;
    static $preferences = 2;

    private $uploadHelper;
    private $validateHelper;
    private $session;

    private $messageHelper;
    private $messagesRegister;

    const IS_COMPANY = Courier_Constant_Server::IS_COMPANY;

    private $divCompany = '#profile-personal-content';
    private $divPreferences = '#cpreferences-content';

    private $tableAuth;
    private $tableCustomer;

    private $id;
    private $detail;
    private $authInfo;
    private $timeNow;

    public function initOther()
    {
        $auth = Zend_Auth::getInstance();
        if ($auth->getStorage()->read() == null) {
            $this->redirect("/auth");
        }

        $this->session = Zend_Registry::getInstance()->session->customerProfile;
        $this->id = Zend_Auth::getInstance()->getStorage()->read()->id;

        $this->tableCustomer = Customer_Model_DbTable_Customer::getInstance();
        $this->tableAuth = Auth_Model_DbTable_Auth::getInstance();

        $customerInfo = $this->tableCustomer->getDetail($this->id);
        $this->detail = $customerInfo[0];
        $this->view->detail = new Base_Php_Overloader($this->detail);

        $authInfo = $this->tableAuth->getAuth($this->id);
        $this->authInfo = $authInfo[0];
        $this->view->authInfo = new Base_Php_Overloader($this->authInfo);

        $this->uploadHelper = new Base_Controller_Helper_Upload();
        $this->validateHelper = new Base_Controller_Helper_Validate();

        $this->messageHelper = Base_Helper_Message::getInstance();
        $this->messagesRegister = $this->messageHelper->edit_profile;

        $datetime = new DateTime();
        $this->timeNow = $datetime->format('Y\-m\-d\ h:i:s');
    }

    public function getFields($page)
    {
        /**
         * Fields for step 1 (company / individual)
         */
        $stepPersonal = array(
            'firstname' => 'name',
            'lastname' => 'name',
            /*'contact_firstname' => 'name',
            'contact_lastname' => 'name',

            'alternative_email' => 'email',
            'general_number' => 'tel',*/
            'contact_number' => 'tel',
            'mobile' => 'tel',
            'fax' => 'tel',

            'image' => 'image',
            'address' => 'address',
            'suburb' => 'address',
            'postcode' => 'postcode',
            'customer_state' => 'required',
            'country' => 'required',

            'card_type' => 'required',
            'card_number' => 'required',
            'card_name' => 'required',
            'ccv' => 'ccv',
            'expiry_month' => 'required',
            'expiry_year' => 'required',
        );

        /**
         * Fields for step 2 Preferences
         */
        $stepPreferences = array(
            'reference' => 'required',
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
    }

    public function indexAction()
    {
        $this->detailAction();
    }

    public function detailAction()
    {
        $this->view->active = 'personal';
        if ($this->_request->isPost() && $this->_request->getParam('update_info')) {
            $params = $this->_request->getParams();
            $pageMessage = 'edit_profile';
            $typeMessage = 'customer';
            $messages = array();
            $data = array();
            $result = false;
            $messageError = '';

            /**
             * Get fields value, type data in courier individual
             */
            $inputRegex = $this->getFields(self::$personal);
            /**
             * Get courier info (value / message)
             * @var &$data , &$messages
             */
            $this->getInfo($data, $messages, $inputRegex, $pageMessage, $typeMessage);

            /**
             * Keep data user has entered **/
            $this->view->detail = new Base_Php_Overloader(array_merge($this->detail, $params));
            /**
             * Check same data **/
            if (count($data) == 0) {
                $this->view->message = Base_Helper_Message::$listMessage['all_the_same']['customer_courier']['all_same_registration_process']['message'];
                $this->_response->setBody($this->view->render($this->_verifyScriptName('account/customer-profile.phtml')));
                return;
            }

            if (count($messages) < 1) {
                try {
                    $condition = $this->tableCustomer->getAdapter()->quoteInto('auth_id = ?', (int)$this->id);
                    /**
                     * Combine month and year to field expiry_date
                     * delete 2 fields form @var $data
                     */
                    $data['expiry_date'] = $params['expiry_month'] . '/' . $params['expiry_year'];
                    unset($data['expiry_month']);
                    unset($data['expiry_year']);
                    /**
                     * Combine firstname and lastname to field search_name
                     * insert content to 2 fields : contact_firstname, contact_lastname
                     */
                    $data['search_name'] = $params['firstname'] . ' , ' . $params['lastname'];
                    $data['contact_firstname'] = $params['firstname'];
                    $data['contact_lastname'] = $params['lastname'];
                    $data['updated'] = $this->timeNow;
                    $this->tableCustomer->update($data, $condition);
                } catch (Zend_Db_Exception $dbException) {
                    $result = Base_Constant_Client::FAILED;
                    $messageError = $dbException->getMessage();
                }
                if (!$result) {
                    $this->view->message = Base_Constant_Client::SUCCESSFUL;
                    /** Refresh view data */
                    $this->refreshData($this->view->detail,$this->view->authInfo);
                }else{
                    $this->view->message = $messageError;
                    $this->_response->setBody($this->view->render($this->_verifyScriptName('error.phtml')));
                    return;
                }
            } else {
                $this->view->message = 'Update your profile is not successful. Please check again.';
                $this->view->messages = $messages;
            }
        }
        $this->_response->setBody($this->view->render($this->_verifyScriptName('account/customer-profile.phtml')));
    }

    public function preferencesAction()
    {
        $this->view->active = 'preferences';
        if ($this->_request->isPost() && $this->_request->getParam('update_preferences')) {
            $params = $this->_request->getParams();
            $pageMessage = 'edit_profile';
            $typeMessage = 'customer_preferences';
            $messages = array();
            $data = array();
            $result = false;
            $messageError = '';

            /**
             * Get fields value, type data in courier individual
             */
            $inputRegex = $this->getFields(self::$preferences);
            /**
             * Get courier info (value / message)
             * @var &$data , &$messages
             */
            $this->getInfo($data, $messages, $inputRegex, $pageMessage, $typeMessage);

            /**
             * Keep data user has entered **/
            $this->view->detail = new Base_Php_Overloader(array_merge($this->detail, $params));
            /**
             * Check same data **/
            if (count($data) == 0) {
                $this->view->message = Base_Helper_Message::$listMessage['all_the_same']['customer_courier']['all_same_registration_process']['message'];
                $this->_response->setBody($this->view->render($this->_verifyScriptName('account/customer-profile.phtml')));
                return;
            }

            if (count($messages) < 1) {
                try {
                    $where = $this->tableCustomer->getAdapter()->quoteInto('auth_id = ?', (int)$this->id);
                    $data['updated'] = $this->timeNow;
                    $this->tableCustomer->update($data, $where);
                } catch (Zend_Db_Exception $dbException) {
                    $result = Base_Constant_Client::FAILED;
                    $messageError = $dbException->getMessage();
                }
                if (!$result) {
                    $this->view->message = Base_Constant_Client::SUCCESSFUL;
                    /** Refresh view data */
                    $this->refreshData($this->view->detail,$this->view->authInfo);
                }else{
                    $this->view->message = $messageError;
                    $this->_response->setBody($this->view->render($this->_verifyScriptName('error.phtml')));
                    return;
                }
            }else {
                $this->view->message = 'Update your profile is not successful. Please check again.';
                $this->view->messages = $messages;
            }
        }
        $this->_response->setBody($this->view->render($this->_verifyScriptName('account/customer-profile.phtml')));
    }
    public function changepasswordAction()
    {
        if ($this->_request->isPost() && $this->_request->getParam('update')) {
            $params = $this->_request->getParams();
            $result = false;
            switch (true) {
                case $params['password'] != $params['repassword']:
                    $this->view->message = 'Password mismatch. Please try again';
                    break;
                case $this->validateHelper->validation($params['password'], 'password') && $params['password'] == $params['repassword']:
                    $userData = array(
                        'app_password' => sha1($params['password']),
                    );
                    try {
                        $where = $this->tableAuth->getAdapter()->quoteInto('id = ?', (int)$this->id);
                        $this->tableAuth->update($userData, $where);
                    } catch (Zend_Db_Exception $dbException) {
                        $result = Base_Constant_Client::FAILED;
                        $this->view->message = $dbException->getMessage();
                    }
                    if(!$result){
                        $this->view->message = Base_Constant_Client::SUCCESSFUL;
                    }
                    break;
                default:
                    $this->view->message = Base_Helper_Message::$listMessage['registration']['customer']['app_password']['message'];
                    $message['required'] = Base_Helper_Message::$listMessage['registration']['customer']['app_password']['message'];
                    break;
            }
        }
        $this->_response->setBody($this->view->render($this->_verifyScriptName('account/change-password.phtml')));
    }

    function getInfo(&$data, &$messages, $inputRegex, $pageMessage, $typeMessage)
    {
        $params = $this->_request->getParams();

        foreach ($inputRegex as $key => $val) {
            /**
             * Check same fields "expiry_month" and "expiry_year"
            */
            if($key == 'expiry_year' || $key == 'expiry_month'){
                $expiry_date = $params['expiry_month'] . '/' . $params['expiry_year'];
                if($expiry_date == $this->detail['expiry_date']){
                    continue;
                }
            }
            /**
             * Check same other fields
             */
            if ($key != 'image' && $key != 'expiry_month' && $key != 'expiry_year' && isset($params[$key])
                && gettype($params[$key]) == 'string' &&  trim($params[$key]) == $this->detail[$key])
            {
                unset($data[$key]);
                continue;
            }

            switch ($key) {
                case 'image':
                    /**
                     * Upload file
                     * @var &$data , &$messages
                     */
                    $this->doUpload($data, $messages, $key, $pageMessage, $typeMessage);
                    break;
                case 'alternative_email':
                    if (!trim($params[$key])) {
                        break;
                    }
                case 'email':
                    $params[$key] = str_replace('"', '', $params[$key]);
                    if ($this->validateHelper->validation($params[$key], $val)) {
                        $data[$key] = $params[$key];
                        $where = $this->tableCustomer->getAdapter()->quoteInto($key . ' = ?', $params[$key]);
                        $row = $this->tableCustomer->fetchAll($where);
                        switch (count($row)) {
                            case 0:
                                break;
                            default:
                                $messages[$key] = $this->messagesRegister->customer->email->taken;
                                break;
                        }
                    } else {
                        $messages[$key] = Base_Helper_Message::$listMessage[$pageMessage][$typeMessage][$key]['message'];
                        $data[$key] = '';
                    }
                    break;
                case 'preferred_pickup_suburb_ids':
                case 'preferred_delivery_suburb_ids':
                    if (isset($params[$key]) && $this->validateHelper->validation($params[$key], $val)) {
                        $data[$key] = implode(",", $params[$key]);
                    } else {
                        $messages[$key] = Base_Helper_Message::$listMessage[$pageMessage][$typeMessage][$key]['message'];
                        $data[$key] = '';
                    }
                    break;
                case 'contact_firstname':
                case 'contact_lastname':
                    if (!trim($params[$key])) {
                        break;
                    }
                default:
                    if ($this->validateHelper->validation($params[$key], $val)) {
                        $data[$key] = $params[$key];
                    } else {
                        $messages[$key] = Base_Helper_Message::$listMessage[$pageMessage][$typeMessage][$key]['message'];
                        $data[$key] = '';
                    }
                    break;
            }
        }
    }

    public function doUpload(&$data, &$messages, $input, $pageMessage, $typeMessage)
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
                unset($data[$input]);
                break;
            /** Do upload */
            default :
                $data[$input] = $_FILES[$input]['name'];
                $errors = array();
                $data[$input] = $this->uploadHelper->upload($input, $errors);
                if (!$data[$input]) {
                    $messages[$input] = implode(' ', $errors);
                    break;
                }
                /**
                 * If has exist file
                 * delete old file in upload folder
                 */
                if ('' != $params[$input . '_temp']) {
                    /** Remove exist file */
                    $this->uploadHelper->deleteImage($params[$input . '_temp'], 'upload');
                }
                /**
                 * Move image from temp to upload folder
                 * and delete old file in upload folder
                 */
                $this->uploadHelper->moveImage($data[$input]);
                break;
        }
    }

    public function refreshData(&$detail,&$authInfo)
    {
        $personalInfo = $this->tableCustomer->getDetail($this->id);
        $detail = new Base_Php_Overloader($personalInfo[0]);
        $authInfo = $this->tableAuth->getAuth($this->id);
        $authInfo = new Base_Php_Overloader($authInfo[0]);
    }
}