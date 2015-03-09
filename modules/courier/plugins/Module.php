<?php

class Courier_Plugin_Module
{
    public static function init()
    {

    }

    public static function loadCss()
    {
        return array(
            STATIC_PATH . DS . 'jquery.datepicker' . DS . 'css' . DS . 'jquery-ui.min.css',
        );
    }

    public static function loadJs()
    {
        return array(
            STATIC_PATH . DS . 'private' . DS . 'share' . DS . 'bind.js',
            STATIC_PATH . DS . 'private' . DS . 'register.js',
			STATIC_PATH . DS . 'private' . DS . 'share' . DS . 'ValidateController.js',
			STATIC_PATH . DS . 'private' . DS . 'share' . DS . 'AjaxController.js',
            PRIVATE_STATIC_PATH . DS . 'pickup' . DS . 'func' . DS . 'module-func.js',
            STATIC_PATH . DS . 'jquery.datepicker' . DS . 'jquery-ui.js',
            STATIC_PATH . DS . 'private' . DS . 'MessageController.js',
            STATIC_PATH . DS . 'history.js',
            'https://maps.googleapis.com/maps/api/js?key=AIzaSyC-vd0w6BzFMNBxyUwj84laKLJd_MzApLM&v=3.exp&libraries=places&language=en',
            STATIC_PATH . DS . 'private' . DS . 'map.js',
        );
    }
}