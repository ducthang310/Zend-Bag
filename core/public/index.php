<?php
define('PROJECT_HOST', $_SERVER['HTTP_PROJECT_HOST']);
define('APPLICATION_ENV', $_SERVER['HTTP_APPLICATION_ENV']);
define('DS', DIRECTORY_SEPARATOR);
define('UDS', '/');
define('BASE_URL', (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['SERVER_NAME']);
define('WWW_PATH', dirname(dirname(dirname(__FILE__))));
define('CORE_PATH', WWW_PATH . '/core');
define('APPLICATION_PATH', CORE_PATH . '/application');
define('LIBRARY_PATH', WWW_PATH . '/library');
define('STATIC_PATH', WWW_PATH . '/static');
define('PRIVATE_STATIC_PATH', STATIC_PATH . '/private');
define('VAR_PATH', WWW_PATH . '/var');
define('CACHE_PATH', VAR_PATH . '/cache');
define('LOG_PATH', VAR_PATH . '/log');
define('LAST_PROJECT_TEMP', 'temp/temp/temp/temp');
define('PROJECT_MEDIA', 'media');
define('IS_WIN', 'WIN' === strtoupper(substr(PHP_OS, 0, 3)));
define('IS_CLI', 'CLI' == strtoupper(PHP_SAPI) ? true : false);
ini_set('date.timezone', $_SERVER['HTTP_APPLICATION_TIMEZONE']);
set_include_path(LIBRARY_PATH . '/framework');
function loadApplicationConfig()
{
    require_once 'Zend/Config/Ini.php';
    $default = new Zend_Config_Ini(
        APPLICATION_PATH . '/configs/application.ini',
        APPLICATION_ENV,
        array('allowModifications' => true)
    );
    $options = $default->toArray();
    return $options;
}

$options = loadApplicationConfig();
require_once 'Zend/Application.php';
$application = new Zend_Application(APPLICATION_ENV, $options);
$application->bootstrap()->run();