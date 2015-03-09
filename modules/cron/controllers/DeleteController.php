<?php

class Cron_DeleteController extends Base_Controller_Action_Backend
{
    public function indexAction()
    {
        $this->deleteTemp();
    }

    public function deleteTemp()
    {
        $path = WWW_PATH . DS . Base_Constant_Server::MEDIA . DS . Base_Constant_Server::MEDIA_TEMP;
        if ($handle = opendir($path)) {
            while (false !== ($file = readdir($handle))) {
                if (filemtime($path . DS . $file) < (time() - (Zend_Registry::getInstance()->appConfig['cron']['time_delete_temp']))) {
                    unlink($path . DS . $file);
                }
            }
            closedir($handle);
        }
    }
}

?>