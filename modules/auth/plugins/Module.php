<?php

class Auth_Plugin_Module
{
    public static function init()
    {

    }

    public static function loadCss()
    {
        return array(
            /*STATIC_PATH . DS . 'bootstrap-2.1.1' . DS . 'css' . DS . 'bootstrap.css',*/
        );
    }

    public static function loadJs()
    {
        return array(
            STATIC_PATH . DS . 'jquery-1.11.1.min.js',
            STATIC_PATH . DS . 'jcf.js',
            STATIC_PATH . DS . 'jcf.checkbox.js',
            STATIC_PATH . DS . 'bag.js',
            STATIC_PATH . DS . 'private' . DS . 'share' . DS . 'ValidateController.js',
        );
    }
}