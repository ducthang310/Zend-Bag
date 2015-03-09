<?php
class Base_Php_Overloader extends ArrayObject
{

    private static $_instance = NULL;
    protected $_data = array();
    public function __construct($thing = null)
    {
        $array = array();
        switch (true) {
            case is_array($thing):
                $array = $thing;
                break;
            case is_object($thing) && 'stdClass' == get_class($thing):
                $array = get_object_vars($thing);
                break;

            default:
                break;
        }
        foreach ($array as $attribute => $value) {
            $this->_data[$attribute] = $this->initData($value);
        }
    }

    protected function initData($thing)
    {
        $new = new self();
        switch (true) {
            case is_array($thing):
                $array = $thing;
                break;
            case is_object($thing) && 'stdClass' == get_class($thing):
                $array = get_object_vars($thing);
                break;

            default:
                return $thing;
                break;
        }
        foreach ($thing as $attribute => $value) {
            $new->_data[$attribute] = $this->initData($value);
        }
        return $new;
    }

    public function __set($name, $value)
    {

        $this->_data[$name] = $value;
    }

    public function __get($name)
    {

        if (array_key_exists($name, $this->_data)) {
            return $this->_data[$name];
        }


        return null;
    }
    public function __isset($name)
    {

        return isset($this->_data[$name]);
    }
    public function __unset($name)
    {

        unset($this->_data[$name]);
    }
    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function getData()
    {
        return $this->_data;
    }
    static public function object2array($object)
    {
        if (is_object($object)) {


            $object = get_object_vars($object);
        }

        if (is_array($object)) {
            return array_map(NULL, $object);
        } else {

            return $object;
        }
    }
    static public function array2object($array)
    {
        if (is_array($array)) {
            return (object)array_map(NULL, $array);
        } else {

            return $array;
        }
    }
}
