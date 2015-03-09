<?php

class Base_Plugin_System
{
    public static function loadJs()
    {
        return array(
            STATIC_PATH . DS . 'jquery-1.8.2.min.js',
            STATIC_PATH . DS . 'jcf.js',
            STATIC_PATH . DS . 'jcf.select.js',
            STATIC_PATH . DS . 'jcf.checkbox.js',
            STATIC_PATH . DS . 'bag.js',
        );
    }
}