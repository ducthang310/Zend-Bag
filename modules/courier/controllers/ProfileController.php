<?php

class Courier_ProfileController extends Base_Controller_Action_Backend
{
    static $personal = 1;
    static $upload = 2;
    static $preferences = 3;

    private $uploadHelper;
    private $validateHelper;
    private $session;

    private $messageHelper;
    private $messagesRegister;

    const IS_COMPANY = Courier_Constant_Server::IS_COMPANY;

    private $divCompany = '#profile-company-content';
    private $divIndividual = '#profile-individual-content';
    private $divUpload = '#cupload-id-content';
    private $divPreferences = '#cpreferences-content';

    private $tableAuth;
    private $tableCourier;

    private $isCompany;
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

        $this->session = Zend_Registry::getInstance()->session->courierProfile;

        $this->id = Zend_Auth::getInstance()->getStorage()->read()->id;
        $this->isCompany = Zend_Auth::getInstance()->getStorage()->read()->detail->is_company;

        $this->tableCourier = Courier_Model_DbTable_Courier::getInstance();
        $this->tableAuth = Auth_Model_DbTable_Auth::getInstance();

        $courierInfo = $this->tableCourier->getDetail($this->id);
        $this->detail = $courierInfo[0];
        $this->view->detail = new Base_Php_Overloader($this->detail);

        /**
         * Check head_office_approved
         * and set approved status
         */
        if ($this->detail['head_office_approved']) {
            $this->view->isApproved = true;
        }

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
         * Same fields for step 1 (company / individual)
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
            'company_name' => 'company_name',
            'abn' => 'required',
        );
        /**
         * Fields for only individual type
         */
        $stepIndividual = array(
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

        if (self::$personal == $page && self::IS_COMPANY == $this->isCompany) return array_merge($stepCompany, $stepSame);
        if (self::$personal == $page && self::IS_COMPANY != $this->isCompany) return array_merge($stepIndividual, $stepSame);
        if (self::$upload == $page) return $stepUpload;
        if (self::$preferences == $page) return $stepPreferences;
    }

    /**
     * Personal detail (both company and indivisual)
     */
    public function indexAction()
    {
        $this->companyAction();
    }

    public function companyAction()
    {
        $this->view->active = 'company';

        if ($this->_request->isPost() && $this->_request->getParam('update_info')) {

            $params = $this->_request->getParams();
            $pageMessage = 'edit_profile';
            $typeMessage = 'courier';
            $messages = array();
            $data = array();
            $result = false;
            /**
             * Get fields value, type data in courier individual
             */
            $inputRegex = $this->getFields(self::$personal);
            /**
             * Get courier info (value / message)
             * @var &$data , &$messages
             */
            $this->getInfo($data, $messages, $inputRegex, $pageMessage, $typeMessage);
            ;
            /**
             * Keep data user has entered **/
            $this->view->detail = new Base_Php_Overloader(array_merge($this->detail, $params));

            if (count($data) == 0) {
                $this->view->message = Base_Helper_Message::$listMessage['all_the_same']['customer_courier']['all_same_registration_process']['message'];
                $this->_response->setBody($this->view->render($this->_verifyScriptName('account/courier-profile.phtml')));
                return;
            }

            if (count($messages) < 1) {
                if($this->isCompany == self::IS_COMPANY){
                    $data['firstname'] = $params['contact_firstname'];
                    $data['lastname'] = $params['contact_lastname'];
                }
                $data['search_name'] = $params['contact_firstname'] . ' , ' . $params['contact_lastname'];
                $data['updated'] = $this->timeNow;

                try {
                    $condition = $this->tableCourier->getAdapter()->quoteInto('auth_id = ?', (int)$this->id);
                    $this->tableCourier->update($data, $condition);
                } catch (Zend_Db_Exception $dbException) {
                    $result = Base_Constant_Client::FAILED;
                    $messageError = $dbException->getMessage();
                }
                if (!$result) {
                    $this->view->message = Base_Constant_Client::SUCCESSFUL;
                    /** Refresh data */
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

        $this->view->div = $this->divCompany;
        $this->_response->setBody($this->view->render($this->_verifyScriptName('account/courier-profile.phtml')));
    }

    public function uploadAction()
    {
        $this->view->active = 'upload';

        if ($this->_request->isPost() && $this->_request->getParam('update_upload')) {

            $params = $this->_request->getParams();
            $pageMessage = 'edit_profile';
            $typeMessage = 'courier_individual';
            $messages = array();
            $data = array();
            $result = false;
            /**
             * Upload file photo ID, utility_bill, bank_statement
             * @var &$data , &$messages
             */
            $inputData = $this->getFields(self::$upload);
            foreach ($inputData as $key => $val) {
                $this->doUpload($data, $messages, $key, $pageMessage, $typeMessage);
            }

            /**
             * Check the same data
             */
            if (count($data) == 0) {
                $this->view->message = Base_Helper_Message::$listMessage['all_the_same']['customer_courier']['all_same_registration_process']['message'];
                $this->_response->setBody($this->view->render($this->_verifyScriptName('account/courier-profile.phtml')));
                return;
            }

            if (count($messages) < 1) {
                try {
                    $data['updated'] = $this->timeNow;
                    $where = $this->tableCourier->getAdapter()->quoteInto('auth_id = ?', (int)$this->id);
                    $this->tableCourier->update($data, $where);

                } catch (Zend_Db_Exception $dbException) {
                    $result = Base_Constant_Client::FAILED;
                    $this->view->message = $dbException->getMessage();
                }
                if (!$result) {
                    $this->view->message = Base_Constant_Client::SUCCESSFUL;
                    /** Refresh data */
                    $this->refreshData($this->view->detail,$this->view->authInfo);
                }else{
                    $this->view->messages = $messages;
                }
            }else {
                $this->view->message = 'Update your profile is not successful. Please check again.';
                $this->view->messages = $messages;
                $this->view->div = $this->divUpload;
            }
        }

        $this->_response->setBody($this->view->render($this->_verifyScriptName('account/courier-profile.phtml')));
    }

    public function preferencesAction()
    {
        $this->view->active = 'preferences';
        if ($this->_request->isPost() && $this->_request->getParam('update_preferences')) {

            $pageMessage = 'registration';
            $typeMessage = 'courier_preferences';
            $messages = array();
            $data = array();
            $result = false;

            /** Get fields in preferences step */
            $inputRegex = $this->getFields(self::$preferences);
            /** Check and validate courier preference data */
            $this->getInfo($data, $messages, $inputRegex, $pageMessage, $typeMessage);

            $this->session->preferences = null;
            /**
             * Keep data user has entered **/
            $this->view->detail = new Base_Php_Overloader(array_merge($this->detail, $data));
            $this->session->preferences = null;

            /**
             * Check the same data
             */
            if (count($data) == 0) {
                $this->view->message = Base_Helper_Message::$listMessage['all_the_same']['customer_courier']['all_same_registration_process']['message'];
                $this->_response->setBody($this->view->render($this->_verifyScriptName('account/courier-profile.phtml')));
                return;
            }

            if (count($messages) < 1) {
                try {
                    $condition = $this->tableCourier->getAdapter()->quoteInto('auth_id = ?', (int)$this->id);
                    $data['updated'] = $this->timeNow;
                    $this->tableCourier->update($data, $condition);
                } catch (Zend_Db_Exception $dbException) {
                    $result = Base_Constant_Client::FAILED;
                    $this->view->message = $dbException->getMessage();
                }
                if (!$result) {
                    $this->view->message = Base_Constant_Client::SUCCESSFUL;
                    /** Refresh data */
                    $this->refreshData($this->view->detail,$this->view->authInfo);
                } else {
                    $this->view->messages = $messages;
                }
            } else {
                $this->view->message = 'Update your profile is not successful. Please check again.';
                $this->view->messages = $messages;
            }
        }
        $this->view->div = $this->divPreferences;
        $this->_response->setBody($this->view->render($this->_verifyScriptName('account/courier-profile.phtml')));
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
                    if (!$result) {
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
    /** Function */
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
            if ($key != 'image' && isset($params[$key]) && gettype($params[$key]) == 'string'
                                                        &&  trim($params[$key]) == $this->detail[$key]) {
                unset($data[$key]);
            } else {
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
                            $data[$key] = '';
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
                                    $messages[$key] = $this->messagesRegister->customer->email->taken;
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
                            if($data[$key] == $this->detail[$key]) unset($data[$key]);
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

                switch ($input){
                    case 'image':
                        $data[$input] = $this->uploadHelper->upload($input, $errors);
                        break;
                    default:
                        $data[$input] = $this->uploadHelper->uploadFile($input, $errors);
                        break;
                }

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
                /**
                 * Change approve status for photo, utility_bill, bank_statement
                 * and delete old file in upload folder
                 */
                if ($input == 'photo' || $input == 'utility_bill' || $input = 'bank_statement') {
                    $data[$input.'_approved'] = null;
                }
                break;
        }
    }

    public function refreshData(&$detail,&$authInfo)
    {
        $personalInfo = $this->tableCourier->getDetail($this->id);
        $detail = new Base_Php_Overloader($personalInfo[0]);

        $authInfo = $this->tableAuth->getAuth($this->id);
        $authInfo = new Base_Php_Overloader($authInfo[0]);
    }
}
