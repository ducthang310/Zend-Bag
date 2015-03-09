<?php

class Base_Helper_Session extends Base_Php_Overloader
{
    private static $_instance = NULL;

    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function __get($name)
    {
        if (array_key_exists($name, $this->_data)) {
            return $this->_data[$name];
        }
        return $this->_data[$name] = new Zend_Session_Namespace($name);
    }

    public function __set($name, $value)
    {
        if (array_key_exists($name, $this->_data)) {
            $this->_data[$name] = $value;
            Zend_Session::namespaceUnset($name);
        }
    }
    public function __unset($name)
    {
        if (array_key_exists($name, $this->_data)) {
            unset($this->_data[$name]);
            Zend_Session::namespaceUnset($name);
        }
    }

}
