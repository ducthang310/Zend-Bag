<?php

class Base_Plugin_Preparing extends Zend_Controller_Plugin_Abstract
{
    public function routeStartup(Zend_Controller_Request_Abstract $request)
    {
        $front = Zend_Controller_Front::getInstance();
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
        if (!isset($viewRenderer->view)) {
            $viewRenderer->initView();
        }
        $view = $viewRenderer->view;
        foreach ($front->getControllerDirectory() as $moduleName => $controllerPath) {
            $loader = new Zend_Application_Module_Autoloader(array(
                'namespace' => ucfirst($moduleName),
                'basePath' => dirname($controllerPath),
            ));
            $loader->addResourceType('controller', 'controllers', 'Controller');
            $loader->addResourceType('constant', 'constants', 'Constant');
            $loader->addResourceType('php', 'phps', 'Php');
            $loader->addResourceType('validate', 'validates', 'Validate');
            $loader->addResourceType('module_helper', 'helpers', 'Helper');
            $loader->addResourceType('helper', 'controllers/helpers', 'Controller_Helper');
            Zend_Controller_Action_HelperBroker::addPath($controllerPath . DS . 'helpers', ucfirst($moduleName) . '_Controller_Helper_');
            $viewHelpers = array(
                'View' => dirname($controllerPath) . DS . 'views' . DS . 'helpers',
                'Display' => dirname($controllerPath) . DS . 'display' . DS . 'helpers'
            );
            foreach ($viewHelpers as $directory => $viewHelper) {
                if (is_dir($viewHelper)) {
                    $view->addHelperPath($viewHelper);
                    $prefix = ucfirst($moduleName) . '_' . $directory . '_Helper';
                    $view->addHelperPath($viewHelper, $prefix);
                }
            }
        }
    }

    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
        $front = Zend_Controller_Front::getInstance();
        $currentModule = $front->getRequest()->getModuleName();
        $registry = Zend_Registry::getInstance();
        $registry->session = Base_Helper_Session::getInstance();
        $appConfig = $front->getParam("bootstrap")->getOptions();
        $registry->appConfig = $appConfig;
        $registry->currentModule = $currentModule;

        $modulePaths = array();
        foreach ($front->getControllerDirectory() as $moduleName => $controllerPath) {
            $modulePaths[$moduleName] = dirname($controllerPath);
        }
        $registry->modulePaths = $modulePaths;
        $registry->controllerPaths = $front->getControllerDirectory();
        $logName = date('Y-m-d') . '.log';
        !is_dir(LOG_PATH) ? mkdir(LOG_PATH, 0777, TRUE) : NULL;
        $registry->logging = new Base_Php_Overloader();
        $registry->logging->logDir = LOG_PATH;
        $registry->logging->logName = $logName;
        Base_Helper_Log::getInstance()->setLogName($logName);
        if (PHP_VERSION_ID < 50600) {
            mb_http_input('UTF-8');
            mb_http_output('UTF-8');
            mb_internal_encoding('UTF-8');
            iconv_set_encoding('input_encoding', 'UTF-8');
            iconv_set_encoding('output_encoding', 'UTF-8');
            iconv_set_encoding('internal_encoding', 'UTF-8');
            iconv_set_encoding('internal_encoding', 'UTF-8');
        } else {
            ini_set('input_encoding', 'UTF-8');
            ini_set('output_encoding', 'UTF-8');
            ini_set('default_charset', 'UTF-8');
            ini_set('default_charset', 'UTF-8');
            ini_set('mbstring.http_input', 'UTF-8');
            ini_set('mbstring.http_output', 'UTF-8');
            ini_set('mbstring.internal_encoding', 'UTF-8');
        }

        if ('base' != strtolower($currentModule)) {
            Base_Plugin_Module::init();
        }
        $class = ucfirst($currentModule) . '_Plugin_Module';
        call_user_func($class . '::init');
    }

    public function dispatchLoopShutdown()
    {
        $exceptions = $this->getResponse()->getException();

        foreach ($exceptions ? array_reverse($exceptions) : array() as $e) {
            Base_Helper_Log::getInstance()->log($e->getMessage() . $e->getTraceAsString());
        }
    }

}