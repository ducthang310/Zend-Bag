<?php

class Base_ValidateController extends Base_Controller_Action
{
    public  $eMessage, $message, $type_name, $params, $pattern;
    public  $page, $user, $name, $div, $type, $text, $option, $empty;

    public function initOther(){
        /**
         * Get validate Regex from  @var Base_Constant_Server class
         */
        $this->pattern = Base_Constant_Server::$validateRegex;
    }

    public function indexAction()
    {
        /**
         * page, user, name, div, type, text, option;
         */
        $this->message = 'available';
        $this->params = $this->_request->getParams();
        $this->eMessage = Base_Helper_Message::$listMessage;

        if(isset($this->params['page'])) $this->page = $this->params['page'];
        if(isset($this->params['user'])) $this->user = $this->params['user'];
        if(isset($this->params['name'])) $this->name = $this->params['name'];
        if(isset($this->params['div']))  $this->div = $this->params['div'];
        if(isset($this->params['text'])) $this->text = trim($this->params['text']);
        if(isset($this->params['option'])) $this->option = $this->params['option'];
        if(isset($this->params['empty'])) $this->empty = explode(",", str_replace(' ','', $this->params['empty']));

        /**
         * CHECK REQUIRED */
        $this->checkRequired();

        /**
         * CHECK PATTERN */
        $this->compare();
        /**
         * FILTER EMPTY FIELDS */
        switch(true){
            case null != $this->empty && 0 < count($this->empty) && '' == $this->text:
                foreach($this->empty as $child){
                    if($this->name == $child){
                        $this->message = 'available';
                    }
                }
                break;
            default:
                break;
        }
        $clientData = array(
            'result' => Base_Constant_Client::SUCCESSFUL,
            'message' => $this->message,
            'params' => $this->params,
        );
        $this->_response->setBody($this->_helper->getHelper('json')->encodeJson($clientData));
    }

    public function compare(){
        switch(true){
            case 'email' == $this->name && 'login' == $this->option:
                break;
            case 'email' == $this->name &&  0 <= strpos($this->user,'courier'):
                $this->preMatch('email');
                $table = Courier_Model_DbTable_Courier::getInstance();
                $this->checkMailFromDB($table,'email');
                break;
            case 'email' == $this->name && 0 <= strpos($this->user,'customer'):
                if($this->preMatch('email')){
                    $table = Customer_Model_DbTable_Customer::getInstance();
                    $this->checkMailFromDB($table,'email');
                }
                break;
            case 'app_email' == $this->name:
                $this->preMatch('email');
                $table = Auth_Model_DbTable_Auth::getInstance();
                if($this->preMatch('email')){
                    $this->checkMailFromDB($table,'app_email');
                }
                break;
            case 'email' == $this->name:
                $this->preMatch('email');
                break;
            case 'alternative_email' == $this->name:
                $this->preMatch('email');
                break;
            case 'postcode' == $this->name:
                $this->preMatch('postcode');
                break;
            case 'company_name' == $this->name:
                $this->preMatch('company_name');
                break;
            case 'general_number' == $this->name:
                $this->preMatch('tel');
                break;
            case 'contact_number' == $this->name:
                $this->preMatch('tel');
                break;
            case 'mobile' == $this->name:
                $this->preMatch('tel');
                break;
            case 'address' == $this->name:
                $this->preMatch('address');
                break;
            case 'suburb' == $this->name:
                $this->preMatch('address');
                break;
            case 'fax' == $this->name:
                $this->preMatch('tel');
                break;
            case 'lastname' == $this->name || 'firstname' == $this->name || 'contact_lastname' == $this->name || 'contact_firstname' == $this->name:
                $this->preMatch('name');
                break;
            case 'app_password' == $this->name:
                $this->preMatch('password');
                break;
            case 'ccv' == $this->name:
                $this->preMatch('ccv');
                break;
            case 'card_number' == $this->name:
                $this->preMatch('number');
                break;
            default:
                break;
        }
    }

    public function checkRequired(){
        if (preg_match($this->pattern['required'], $this->text) === 1) {

        }else{
            $this->message = $this->eMessage[$this->page][$this->user][$this->name]['message'];
        }
    }
    public function preMatch($pattern){
        if (preg_match($this->pattern[$pattern], $this->text) === 1) {

        }else{
            $this->message = $this->eMessage[$this->page][$this->user][$this->name]['message'];
        }
    }
    public function checkMailFromDB($table, $field){
        $this->text = strtolower($this->text);
        $where = $table->getAdapter()->quoteInto( $field . ' = ?', $this->text);
        $row = $table->fetchAll($where);
        switch (count($row)){
            case 0:
                break;
            default:
                $this->message = $this->eMessage[$this->page][$this->user][$this->name]['taken'];
                break;
        }
    }
}
