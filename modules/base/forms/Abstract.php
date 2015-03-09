<?php

abstract class Base_Form_Abstract extends Zend_Form
{

    public function __construct($options = null)
    {
        parent::__construct($options);
    }
    public static function array21dArray($array = array())
    {
        $array1d = array();
        is_array($array) || $array = array();
        $stack = $array;

        while (0 < count($stack)) {
            $current = array_pop($stack);
            switch (true) {
                case is_string($current):
                    $array1d[] = $current;
                    break;
                case is_array($current):
                    foreach ($current as $element) {
                        array_push($stack, $element);
                    }
                    break;
                default:
                    break;
            }
        }

        return array_reverse($array1d);
    }
    public static function array2String($array = array(), $glue = PHP_EOL)
    {
        is_array($array) ? NULL : $array = array();
        is_string($glue) ? NULL : $glue = '';

        return implode($glue, self::array21dArray($array));
    }

}