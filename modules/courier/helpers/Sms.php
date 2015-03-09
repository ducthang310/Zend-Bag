<?php

class Courier_Helper_Sms extends Base_Php_Overloader
{
    private static $_instance = NULL;
    private $_url;
    private $_username;
    private $_password;
    private $_source;
    private $_reference;

    public function __construct()
    {
        $this->_url = Zend_Registry::getInstance()->appConfig['smsBroadCast']['url'];
        $this->_username = Zend_Registry::getInstance()->appConfig['smsBroadCast']['username'];
        $this->_password = Zend_Registry::getInstance()->appConfig['smsBroadCast']['password'];
        $this->_source = Zend_Registry::getInstance()->appConfig['smsBroadCast']['source'];
        $this->_reference = Zend_Registry::getInstance()->appConfig['smsBroadCast']['reference'];
    }

    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    private function callApi($postData)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->_url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }

    public function sendSms($to, $text)
    {
        $postData = array(
            'username' => $this->_username,
            'password' => $this->_password,
            'to' => $to,
            'from' => $this->_source,
            'message' => $text,
            'ref' => $this->_reference,
        );

        $responses = $this->callApi($postData);
        $responses = explode("\n", $responses);
        foreach ($responses as $response) {
            $detail = explode(':', $response);
            switch (true) {
                case 'OK' == $detail[0]:
                    return true;
                    break;
                case 'BAD' == $detail[0]:
                    Base_Helper_Log::getInstance()->log('The message to ' . $detail[1] . ' was NOT successful. Reason: ' . $detail[2] . '\n');
                    return false;
                    break;
                case 'ERROR' == $detail[0]:
                    Base_Helper_Log::getInstance()->log('There was an error with this request. Reason: ' . $detail[1]);
                    return false;
                    break;
            }
        }
    }
}


