<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initApp()
    {
        $front = Zend_Controller_Front::getInstance();
        $plugins = $this->getOption('plugins');
        foreach ($plugins as $plugin) {
            $path = isset($plugin['path']) ? $plugin['path'] : '';
            $class = isset($plugin['class']) ? $plugin['class'] : '';
            $index = isset($plugin['index']) ? $plugin['index'] : '';
            $params = isset($plugin['params']) ? $plugin['params'] : array();
            if ($class) {
                if ($path) {
                    require_once $path;
                }
                $front->registerPlugin(new $class($params), $index);
            }
        }
        Zend_Session::setOptions(array(
            'name' => sha1(preg_match('/^\/+(admin|base)\/?/', $_SERVER['REQUEST_URI'])),
            'cookie_domain' => PROJECT_HOST
        ));
        Zend_Session::start();
    }
}

