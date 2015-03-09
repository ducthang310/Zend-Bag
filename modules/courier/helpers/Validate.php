<?php

class Courier_Helper_Validate extends Zend_Controller_Action_Helper_Abstract
{
    public function isValidMobile($value)
    {
        if (preg_match("/^(\+)?(\([0-9]+\)\-?\s?)*([0-9]+\-[0-9]+)*([0-9]+)*$/", $value)) {
            return true;
        }
        return false;
    }

    public function isValidString($value){
        if ($value && $value != null) {
            return true;
        }
        return false;
    }
}