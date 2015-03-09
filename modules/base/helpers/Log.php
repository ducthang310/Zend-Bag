<?php

class Base_Helper_Log
{

    private static $_instance = NULL;
    private $logName = NULL;

    protected function __construct()
    {
        is_dir(LOG_PATH) || mkdir(LOG_PATH, 0777, TRUE);
    }
    public function setLogName($logName)
    {
        $this->logName = $logName;
    }
    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function log($message)
    {
        $pointer = fopen(LOG_PATH . DS . $this->logName, 'a');
        fwrite($pointer, pack("CCC", 0xef, 0xbb, 0xbf));
        fwrite($pointer, $message);
        fclose($pointer);
    }

}
