<?php

class Base_Controller_Helper_StandardFilename extends Zend_Controller_Action_Helper_Abstract
{
    protected static $_instance = NULL;

    public static function getInstance() {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    public function direct($filename, $prefix = '', $suffix = '')
    {
        mb_regex_encoding('UTF-8');
        $pattern = '/[^A-Za-z0-9_\-]/';
        $mb4pattern = '[^A-Za-z0-9_\-]';
        $replacement = '-';

        $filename = mb_strtolower($filename, 'UTF-8');
        $prefix = mb_strtolower($prefix, 'UTF-8');
        $suffix = mb_strtolower($suffix, 'UTF-8');
        $filename = mb_ereg_replace($mb4pattern, $replacement, $filename);
        $prefix = mb_ereg_replace($mb4pattern, $replacement, $prefix);
        $suffix = mb_ereg_replace($mb4pattern, $replacement, $suffix);
        $filename = trim($filename);
        $prefix = trim($prefix);
        $suffix = trim($suffix);

        $length = 255 - mb_strlen($prefix, 'UTF-8') - mb_strlen($suffix, 'UTF-8');
        $filename = mb_substr($prefix . '-' . $filename . '-' . $suffix, 0, $length, 'UTF-8');
        return $filename;
    }
}