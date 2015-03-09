<?php

class Message_Plugin_Module
{
    public static function init()
    {

    }

    public static function loadCss()
    {
        return array(
        );
    }

    public static function loadJs()
    {
        return array(
            STATIC_PATH . DS . 'private' . DS . 'MessageController.js',
        );
    }
}