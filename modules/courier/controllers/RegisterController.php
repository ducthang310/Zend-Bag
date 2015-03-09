<?php

class Courier_RegisterController extends Base_Controller_Action_Backend
{
    static $company     = 1;
    static $individual  = 2;
    static $upload      = 3;
    static $preferences = 4;

    private $uploadHelper;
    private $validateHelper;
    private $randString;
    private $session;

    private $messageHelper;
    private $messagesRegister;

    private $divCompany;
    private $divIndividual;
    private $divUpload;
    private $divPreferences;

    private $tableAuth;
    private $tableCourier;

    public function initOther()
    {
        $this->session = Zend_Registry::getInstance()->session->courierRegister;

        $this->tableCourier = Courier_Model_DbTable_Courier::getInstance();
        $this->tableAuth = Auth_Model_DbTable_Auth::getInstance();

        $this->uploadHelper = new Base_Controller_Helper_Upload();
        $this->validateHelper = new Base_Controller_Helper_Validate();

        $this->messageHelper = Base_Helper_Message::getInstance();
        $this->messagesRegister = $this->messageHelper->registration;

        $this->randString = substr(str_shuffle(Zend_Registry::getInstance()->appConfig['secure']['encryptString']), 0, 10);

        $this->divCompany = '#register-company-content';
        $this->divIndividual = '#register-individual-content';
        $this->divUpload = '#cupload-id-content';
        $this->divPreferences = '#cpreferences-content';
    }

    public function getFields($page)
    {
        /**
         * Same fields for step 1 (company / individual
         */
        $stepSame = array(
            'contact_firstname' => 'name',
            'contact_lastname' => 'name',
            'email' => 'email',

            'alternative_email' => 'email',
            'general_number' => 'tel',
            'contact_number' => 'tel',
            'mobile' => 'tel',
            'fax' => 'tel',

            'image' => 'required',
            'address' => 'address',
            'suburb' => 'required',
            'postcode' => 'postcode',
            'courier_state' => 'state',
            'country' => 'required',
            'bank_institution' => 'required',
            'bank_bsb' => 'required',
            'bank_account' => 'required',
        );
        /**
         * Fields for only company type
         */
        $stepCompany = array(
            'is_company' => 1,
            'company_name' => 'company_name',
            'abn' => 'required',
        );
        /**
         * Fields for only individual type
         */
        $stepIndividual = array(
            'is_company' => 0,
            'firstname' => 'name',
            'lastname' => 'name',
        );
        /**
         * Fields for step 3 Preferences
         */
        $stepUpload = array(
            'photo' => '',
            'utility_bill' => '',
            'bank_statement' => '',
        );
        /**
         * Fields for step 3 Preferences
         */
        $stepPreferences = array(
            'preferred_region' => 'required',
            'preferred_area_is_all' => '',
            'preferred_pickup_suburb_ids' => 'array_checkbox',
            'preferred_delivery_suburb_ids' => 'array_checkbox',
        );

        if (self::$company == $page) return array_merge($stepCompany, $stepSame);
        if (self::$individual == $page) return array_merge($stepIndividual, $stepSame);
        if (self::$upload == $page) return $stepUpload;
        if (self::$preferences == $page) return $stepPreferences;
    }

    public function indexAction()
    {
        $this->session->step = self::$individual;
        $this->companyAction();
    }

    public function companyAction()
    {
        $pageMessage = 'registration';
        $typeMessage = 'courier_company';
        $this->view->active = 'company';

        if ($this->_request->isPost() && $this->_request->getParam('register_company')) {
            $params = $this->_request->getParams();
            $messages = array();
            $data = array();
            $userData = array();
            $password = '';
            /** Get fiels value, type data in courier company*/
            $inputRegex = $this->getFields(self::$company);
            /**
             * Get courier info (value / message)
             * @var &$data , &$messages
             */
            $this->getInfo($data, $messages, $inputRegex, $pageMessage, $typeMessage);
            /**
             * Get user (auth table) info (value / message)
             * @var &$userData , &$messages, &$password
             */
            $this->getUserInfo($userData, $messages, $password, $pageMessage, $typeMessage);
            /** Get password non-encrypt */
            $this->session->password = $password;

            $this->session->user = null;
            $this->session->auth = null;
            $this->session->individual_temp = null;

            if (count($messages) < 1) {
                $data['search_name'] = $params['contact_firstname'] . ' , ' . $params['contact_lastname'];
                $this->session->user = $data;
                $this->session->auth = $userData;
//                $this->view->active = 'upload';
                $this->session->step = self::$upload;
                $this->redirect('courier/register/upload');
            } else {
                $this->view->messages = $messages;
            }
            $this->session->company_temp = array_merge($data, $userData, $params);
        }
        $this->view->div = $this->divCompany;
        $this->_response->setBody($this->view->render($this->_verifyScriptName('account/courier-register.phtml')));
    }

    public function individualAction()
    {
        $pageMessage = 'registration';
        $typeMessage = 'courier_individual';
        $this->view->active = 'individual';

        if ($this->_request->isPost() && $this->_request->getParam('register_individual')) {
            $params = $this->_request->getParams();

            $messages = array();
            $data = array();
            $userData = array();
            $password = '';
            /**
             * Get fiels value, type data in courier individual
             */
            $inputRegex = $this->getFields(self::$individual);
            /**
             * Get courier info (value / message)
             * @var &$data , &$messages
             */
            $this->getInfo($data, $messages, $inputRegex, $pageMessage, $typeMessage);
            /**
             * Get user (auth table) info (value / message)
             * @var &$userData , &$messages, &$password
             */
            $this->getUserInfo($userData, $messages, $password, $pageMessage, $typeMessage);
            /**
             * Get password non-encrypt
             */
            $this->session->password = $password;

            $this->session->user = null;
            $this->session->auth = null;
            $this->session->company_temp = null;

            if (count($messages) < 1) {
                $data['search_name'] = $params['contact_firstname'] . ' , ' . $params['contact_lastname'];
                $this->session->user = $data;
                $this->session->auth = $userData;
//                $this->view->active = 'upload';
                $this->session->step = self::$upload;
                $this->redirect('courier/register/upload');
            } else {
                $this->view->messages = $messages;
            }
            /** Keep data user enter */
            $this->session->individual_temp = array_merge($data, $userData, $params);
        }
        /** Set div to show messages validation */
        $this->view->div = $this->divIndividual;
        $this->_response->setBody($this->view->render($this->_verifyScriptName('account/courier-register.phtml')));
    }

    public function uploadAction()
    {
        $this->view->active = 'upload';

        if ($this->_request->isPost() && $this->_request->getParam('register_upload')) {
            $params = $this->_request->getParams();
            $pageMessage = 'registration';
            $typeMessage = 'courier_individual';
            $messages = array();
            $data = array();
            /**
             * Upload file photo ID, utility_bill, bank_statement
             * @var &$data , &$messages
             */
            $inputData = $this->getFields(self::$upload);
            foreach ($inputData as $key => $val){
                $this->doUpload($data, $messages, $key, $pageMessage, $typeMessage);
            }
            /**
             * Keep data user has entered
             * merge session data, new data form, and $param data
             */
            if ($this->session->upload_temp) {
                $this->session->upload_temp = array_merge(array_filter($this->session->upload_temp), $data, $params);
            } else {
                $this->session->upload_temp = array_merge($data, $params);
            }

            if (count($messages) < 1) {
//                $this->view->active = 'preferences';
                $this->session->upload = $this->session->upload_temp = $data;
                $this->session->step = self::$preferences;
                $this->redirect('courier/register/preferences');
            }
            $this->view->messages = $messages;
        }
        $this->view->div = $this->divUpload;
        $this->_response->setBody($this->view->render($this->_verifyScriptName('account/courier-register.phtml')));
    }

    public function preferencesAction()
    {
        $this->view->active = 'preferences';
        $messageError = '';
        if ($this->_request->isPost() && $this->_request->getParam('register_preferences')) {
            $params = $this->_request->getParams();

            $pageMessage = 'registration';
            $typeMessage = 'courier_preferences';
            $messages = array();
            $data = array();
            $result = false;

            /** Get fields in preferences step */
            $inputReg = $this->getFields(self::$preferences);
            /** Check and validate courier preference data */
            $this->getInfo($data, $messages, $inputReg, $pageMessage, $typeMessage);
            $this->session->preferences = null;
            /** Keep data user enter */
            $this->session->preferences_temp = array_merge($data, $params);

            if (count($messages) < 1) {
                $this->session->preferences = $data;
                $datetime = new DateTime();
                $timeNow = $datetime->format('Y\-m\-d\ h:i:s');
                /**
                 * Extra fields for courier personal info (courier table)
                 */
                $this->session->user['id'] = '';
                $this->session->user['head_office_approved'] = Courier_Constant_Server::HEAD_OFFICE_NOT_APPROVED;
                $this->session->user['can_assign'] = 1;
                $this->session->user['updated'] = $timeNow;
                $this->session->user['created'] = $timeNow;

                $userFieldCount = count($this->session->user);
                $uploadFieldCount = count($this->session->upload);
                $preferencesFieldCount = count($this->session->preferences);

                switch (true) {
                    case 0 < $userFieldCount && 0 < $uploadFieldCount && 0 < $preferencesFieldCount:
                        try {
                            /**
                             * Insert to auth table */
                            $newId = $this->tableAuth->insert($this->session->auth);
                            /**
                             * Insert to courier table
                             */
                            $data = array_merge(
                                $this->session->preferences,
                                $this->session->upload,
                                $this->session->user
                            );
                            $data['auth_id'] = $newId;
                            $newCourier = $this->tableCourier->insert($data);
                        } catch (Zend_Db_Exception $dbException) {
                            $result = Base_Constant_Client::FAILED;
                            $messageError = $dbException->getMessage();
                        }
                        break;
                    default :
                        $this->view->messages = 'Register has failed. Please follow all steps in registration form.';
                        $this->_response->setBody($this->view->render($this->_verifyScriptName('error.phtml')));
                        return;
                        break;
                }
                /**
                 * If insert to courier error -> delete new row in auth table
                 */
                if (!isset($newCourier) && isset($newId)) {
                    $this->tableAuth->delete('id = ' . $newId);
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
                    $this->uploadHelper->moveImage($data['image']);
                    $this->uploadHelper->moveImage($data['photo']);
                    $this->uploadHelper->moveImage($data['utility_bill']);
                    $this->uploadHelper->moveImage($data['bank_statement']);
                    /**
                     * Send password via SMS to courier
                     */
                    if (Zend_Registry::getInstance()->appConfig['smsBroadCast']['activeBySms']) {
                        $number = $this->session->user['mobile'];
                        $messageSms = 'Activate your DLIVR account:' . $this->session->password;
                        Courier_Helper_Sms::getInstance()->sendSms($number, $messageSms);
                    }
                    /** Delete session registration */
                    unset(Zend_Registry::getInstance()->session->courierRegister);
                    $this->view->hideMenuLeft = true;
                    Zend_Registry::getInstance()->session->registerSuccess->flag = true;
                    $this->redirect('courier/register/success');
                    return;
                }else{
                    $this->view->message = $messageError;
                    $this->_response->setBody($this->view->render($this->_verifyScriptName('error.phtml')));
                    return;
                }
            } else {
                $this->view->messages = $messages;
            }
        }

        $this->view->div = $this->divPreferences;
        $this->_response->setBody($this->view->render($this->_verifyScriptName('account/courier-register.phtml')));
    }

    function getInfo(&$data, &$messages, $inputRegex, $pageMessage, $typeMessage)
    {
        $params = $this->_request->getParams();
        if(isset($params['preference_area']) && $params['preference_area'] == Courier_Constant_Server::ALL_AREA){
            $preference_suburb = Configuration_Model_DbTable_Suburb::getInstance()->getIdSuburbByRegion($params['preferred_region']);
            $all_preference = array();
            foreach($preference_suburb as $key => $value ){
                $all_preference[] = $value['id'];
            }
            $params['preferred_pickup_suburb_ids'] = $all_preference;
            $params['preferred_delivery_suburb_ids'] = $all_preference;
            $params['preferred_area_is_all'] = Courier_Constant_Server::ALL_AREA;
        }else{
            $params['preferred_area_is_all'] = Courier_Constant_Server::SELECT_AREA;
        }
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
                    $this->doUpload($data, $messages, 'image', 'registration', $typeMessage);
                    break;
                case 'alternative_email':
                    if (!trim($params[$key])) {
                        break;
                    }
                case 'email':
                    $params[$key] = str_replace('"', '', $params[$key]);
                    if ($this->validateHelper->validation($params[$key], $val)) {
                        $data[$key] = $params[$key];
                        $where = $this->tableCourier->getAdapter()->quoteInto($key . ' = ?', $params[$key]);
                        $row = $this->tableCourier->fetchAll($where);
                        switch (count($row)) {
                            case 0:
                                break;
                            default:
                                $messages[$key] = $this->messagesRegister->courier_company->email->taken;
                                break;
                        }
                    } else {
                        $messages[$key] = Base_Helper_Message::$listMessage[$pageMessage][$typeMessage][$key]['message'];
                        $data[$key] = '';
                    }
                    break;
                case 'preferred_area_is_all':
                    $data[$key] = $params[$key];
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

    function getUserInfo(&$userData, &$messages, &$password, $pageMessage, $typeMessage)
    {
        $params = $this->_request->getParams();
        $userData = array(
            'app_email' => ($this->validateHelper->validation(str_replace('"', "", $params['app_email']), 'required')
                && $this->validateHelper->validation(str_replace('"', "", $params['app_email']), 'email')) ?
                str_replace('"', "", $params['app_email']) : '',
            'app_password' => sha1($this->randString),
            'auth_type' => Auth_Constant_Server::COURIER_TYPE,
            'disabled' => 0,
        );

        foreach ($userData as $key => $val) {
            if (trim($val) == '') {
                $messages[$key] = Base_Helper_Message::$listMessage[$pageMessage][$typeMessage][$key]['message'];
            }
        }

        $where = $this->tableAuth->getAdapter()->quoteInto('app_email = ?', str_replace('"', "", $params['app_email']));
        $row = $this->tableAuth->fetchAll($where);
        switch (count($row)) {
            case 0:
                break;
            default:
                $messages['app_email'] = $this->messagesRegister->courier_company->app_email->taken;
                break;
        }
        $password = $this->randString;
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
                $data[$input] = $params[$input . '_temp'];
                break;
            /** Do upload */
            default :
                $data[$input] = $_FILES[$input]['name'];
                $errors = array();
                /**
                 * Check upload image or upload file */
                switch ($input){
                    case 'image':
                        $data[$input] = $this->uploadHelper->upload($input, $errors);
                        break;
                    default:
                        $data[$input] = $this->uploadHelper->uploadFile($input, $errors);
                        break;
                }
                /** Get error messages */
                if (!$data[$input]) {
                    $messages[$input] = implode(' ', $errors);
                    break;
                }
                /** If has exist file */
                if ('' != $params[$input . '_temp']) {
                    /** Remove exist file */
                    $this->uploadHelper->deleteImage($params[$input . '_temp'], 'temp');
                }
                break;
        }
    }
    public function successAction(){
        if(Zend_Registry::getInstance()->session->registerSuccess->flag){
            unset(Zend_Registry::getInstance()->session->registerSuccess->flag);
        }else{
            $this->redirect(BASE_URL);
        }
        $this->view->message = 'Your register has been completed successfully!' .
            '<br>We have sent password to your mobile number.
                                            You can use that password to
                                            <a class="login-link" href="' . BASE_URL . '/auth/index/login">login</a>.';
        $this->_response->setBody($this->view->render($this->_verifyScriptName('success.phtml')));
    }
}