<?php

abstract class Base_Controller_Action extends Zend_Controller_Action
{
    const AREA_BACKEND = 'backend';
    const AREA_FRONTEND = 'frontend';
    protected $_area = NULL;

    public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array())
    {
        parent::__construct($request, $response, $invokeArgs);
    }

    public function init()
    {
        parent::init();

        $this->view->doctype('XHTML1_STRICT');
        $this->view->headMeta()->appendHttpEquiv('Content-Type', 'text/html; charset=UTF-8');
        $this->_helper->viewRenderer->setNoRender(true);

        $this->view->getHelper('baseUrl')->setBaseUrl(BASE_URL);
        defined('LAST_TEMP') || define('LAST_TEMP', BASE_URL . UDS . Base_Constant_Server::LAST_TEMP_DIRECTORY . UDS);

        $registry = Zend_Registry::getInstance();
        $registry->moduleName = $this->_request->getModuleName();
        $registry->controllerName = $this->_request->getControllerName();
        $registry->actionName = $this->_request->getActionName();
        $modulePath = $registry->modulePaths[$registry->moduleName];
        $controllerPath = $registry->controllerPaths[$registry->moduleName];
        $registry->modulePath = $modulePath;
        $this->view->moduleName = $registry->moduleName;
        $this->view->controllerName = $registry->controllerName;
        $this->view->actionName = $registry->actionName;
        $this->view->siteName = $registry->appConfig['siteName'];

        $layoutPath = $modulePath . DS . 'views' . DS . 'layouts';
        if (!is_dir($layoutPath)) {
            $layoutPath = WWW_PATH . DS . 'views' . DS . 'layouts';
        }
        Zend_Layout::startMVC(array(
            "layout" => 'layout',
            "layoutPath" => $layoutPath
        ));
        $registry['mvcOptions'] = array(
            "layout" => 'layout',
            "layoutPath" => $layoutPath
        );
        is_null($this->_request->getParam('ajax')) || $this->_helper->layout()->disableLayout();

        $registry->headLink = $this->_cacheHeadLink();
        $registry->headScript = $this->_cacheHeadScript();

        $registry->currentUser = new Base_Php_Overloader(Zend_Auth::getInstance()->getIdentity());
        $this->initOther();
    }

    protected function _cacheHeadLink()
    {
        $registry = Zend_Registry::getInstance();
        $headLink = $this->_loadHeadLink();
        return $headLink;
    }

    protected function _loadHeadLink()
    {
        $registry = Zend_Registry::getInstance();

        $headLink = array();

        foreach (call_user_func(ucfirst($registry->moduleName) . '_Plugin_Module' . '::loadCss') as $index => $css) {
            Base_Constant_Server::TARGET !== $index && $headLink['file_' . count($headLink)] = $css;
            Base_Constant_Server::TARGET === $index && $headLink[$index] = $css;
        }
        $headLink['file_' . count($headLink)] = PRIVATE_STATIC_PATH . DS . 'system.css';
        $dirIterator = new RecursiveDirectoryIterator(PRIVATE_STATIC_PATH . DS . $registry->moduleName . DS . 'css');
        $iterator = new RecursiveIteratorIterator($dirIterator, RecursiveIteratorIterator::CHILD_FIRST | RecursiveIteratorIterator::LEAVES_ONLY);
        foreach ($iterator as $path) {
            if ('.css' != substr($path->__toString(), -4)) {
                continue;
            }
            $headLink['file_' . count($headLink)] = $path->__toString();
        }
        return $headLink;
    }

    protected function _cacheHeadScript()
    {
        $registry = Zend_Registry::getInstance();
        $headScript = $this->_loadHeadScript();
        return $headScript;
    }

    protected function _loadHeadScript()
    {
        $registry = Zend_Registry::getInstance();
        $headScript = array();
        foreach (call_user_func('Base_Plugin_System' . '::loadJs') as $index => $js) {
            Base_Constant_Server::TARGET !== $index && $headScript['file_' . count($headScript)] = $js;
            Base_Constant_Server::TARGET === $index && $headScript[$index] = $js;
        }

        foreach (call_user_func(ucfirst($registry->moduleName) . '_Plugin_Module' . '::loadJs') as $index => $js) {
            Base_Constant_Server::TARGET !== $index && $headScript['file_' . count($headScript)] = $js;
            Base_Constant_Server::TARGET === $index && $headScript[$index] = $js;
        }

        $dirIterator = new RecursiveDirectoryIterator(PRIVATE_STATIC_PATH . DS . $registry->moduleName . DS . 'func');
        $iterator = new RecursiveIteratorIterator($dirIterator, RecursiveIteratorIterator::CHILD_FIRST | RecursiveIteratorIterator::LEAVES_ONLY);
        foreach ($iterator as $path) {
            if ('.js' != substr($path->__toString(), -3)) {
                continue;
            }
            $headScript['file_' . count($headScript)] = $path->__toString();
        }

        $headScript['inline_' . count($headScript)] = 'var SERVER = ' . json_encode(array(
                'BASE_URL' => BASE_URL,
                'APPLICATION_ENV' => APPLICATION_ENV,
                'DS' => DS,
                'UDS' => UDS
            ));
        $reflection = new ReflectionClass('Base_Constant_Client');
        $headScript['inline_' . count($headScript)] =
            'var Base_Constant_Client = ' . Zend_Json_Encoder::encode($reflection->getConstants()) . ';';

        $dirIterator = new RecursiveDirectoryIterator(PRIVATE_STATIC_PATH . DS . $registry->moduleName . DS . 'exe');
        $iterator = new RecursiveIteratorIterator($dirIterator, RecursiveIteratorIterator::CHILD_FIRST | RecursiveIteratorIterator::LEAVES_ONLY);
        foreach ($iterator as $path) {
            if ('.js' != substr($path->__toString(), -3)) {
                continue;
            }
            $headScript['file_' . count($headScript)] = $path->__toString();
        }
        return $headScript;
    }

    public function initOther()
    {
    }

    protected function _verifyScriptName($name)
    {
        return $name;
    }

    protected function _clearLog()
    {
        $registry = Zend_Registry::getInstance();

        try {
            if (!is_dir(LOG_PATH)) {
                return;
            }
            $dirIterator = new RecursiveDirectoryIterator(LOG_PATH);
            $iterator = new RecursiveIteratorIterator($dirIterator, RecursiveIteratorIterator::CHILD_FIRST | RecursiveIteratorIterator::LEAVES_ONLY);
            foreach ($iterator as $path) {
                switch (true) {
                    case $path->isDir():
                        break;

                    case $registry->logging->logName == basename($path->__toString()):
                        break;

                    case '.log' === substr($path->__toString(), -4):

                        unlink($path->__toString());
                        break;
                    default:
                        break;
                }
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function preDispatch()
    {

    }
    public function postDispatch()
    {
        is_null($this->_request->getParam('ajax')) || $this->getResponse()->setHeader('Content-Type', 'text/plain', true);
    }
}