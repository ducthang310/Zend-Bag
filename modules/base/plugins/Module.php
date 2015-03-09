<?php

class Base_Plugin_Module
{
    public static function init()
    {
        $paths = array(
            WWW_PATH . DS . Base_Constant_Server::MEDIA . DS . Base_Constant_Server::ORIGINAL_PICTURE_DIRECTORY,
            WWW_PATH . DS . Base_Constant_Server::MEDIA . DS . Base_Constant_Server::SMALL_PICTURE_DIRECTORY,
            WWW_PATH . DS . Base_Constant_Server::MEDIA . DS . Base_Constant_Server::MEDIUM_PICTURE_DIRECTORY,
            WWW_PATH . DS . Base_Constant_Server::MEDIA . DS . Base_Constant_Server::LARGE_PICTURE_DIRECTORY,
            WWW_PATH . DS . Base_Constant_Server::MEDIA . DS . Base_Constant_Server::MEDIA_TEMP,
            WWW_PATH . DS . Base_Constant_Server::MEDIA . DS . Base_Constant_Server::MEDIA_UPLOAD,
        );
        foreach ($paths as $path) {
            if (!is_dir($path)) {
                mkdir($path, 0777, true);
            }
        }
    }

    public static function loadCss()
    {
        return array(
            STATIC_PATH . DS . 'bootstrap-2.1.1' . DS . 'css' . DS . 'bootstrap.css',
        );
    }

    public static function loadJs()
    {
        return array(
            STATIC_PATH . DS . 'jquery-1.8.2.js',
            STATIC_PATH . DS . 'bootstrap-2.1.1' . DS . 'js' . DS . 'bootstrap.js'
        );
    }
}