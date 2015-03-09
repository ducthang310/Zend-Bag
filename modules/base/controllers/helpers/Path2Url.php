<?php

class Base_Controller_Helper_Path2Url extends Zend_Controller_Action_Helper_Abstract
{
    private static $_instance = NULL;

    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }


    public function direct($path)
    {
        $url = str_replace(WWW_PATH, BASE_URL, $path);
        if (IS_WIN) {
            return str_replace(DS, UDS, $url);
        } else {
            return $url;
        }
    }
}