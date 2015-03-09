<?php

class Base_Controller_Helper_Validate extends Zend_Controller_Action_Helper_Abstract
{
    public function validation($input,$type){
        /**
         * Get validate Regex from  @var Base_Constant_Server class
        */
        $patterns = Base_Constant_Server::$validateRegex;

        switch(true){
            case 'optional' == $type:
                return true;
                break;
            case 'required' == $type && '' != trim($input) :
                return true;
                break;
            case 'email' == $type && 1 === preg_match($patterns['email'], strtolower($input)):
                return true;
                break;
            case 'name' == $type && 1 === preg_match($patterns['name'], $input):
                return true;
                break;
            case 'address' == $type && 1 === preg_match($patterns['address'], $input):
                return true;
                break;
            case 'password' == $type && 1 === preg_match($patterns['password'], $input):
                return true;
                break;
            case 'company_name' == $type && 1 === preg_match($patterns['company_name'], $input):
                return true;
                break;
            case 'tel' == $type && 1 === preg_match($patterns['tel'], $input):
                return true;
                break;
            case 'array' == $type && 0 < count($input):
                return true;
                break;
            case 'number' == $type && 1 === preg_match($patterns['number'], $input):
                return true;
                break;
            case 'ccv' == $type && 1 === preg_match($patterns['ccv'], $input):
                return true;
                break;
            case 'state' == $type && 7 >= strlen($input) && 0 < strlen($input):
                return true;
                break;
            case 'image' == $type && 1 === preg_match($patterns['image'], $input):
                return true;
                break;
            case 'array_checkbox' == $type && 1 <= count($input) && '0' != $input[0]:
                return true;
                break;
            case 'postcode' == $type && 1 === preg_match($patterns['postcode'], $input):
                return true;
                break;
            case 'float' == $type && 1 === preg_match($patterns['float'], $input):
                return true;
                break;
            default:
                break;
        }
        return false;
    }
}