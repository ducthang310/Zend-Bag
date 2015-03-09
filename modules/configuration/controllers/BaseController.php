<?php

class Configuration_BaseController extends Base_Controller_Action_Backend
{
    private $_flag;

    public function indexAction()
    {
        $this->_list();
    }

    private function _list()
    {
        $table = Configuration_Model_DbTable_Configuration::getInstance();
        $config = $table->fetchAll($table->select()->order('config_key ASC'));
        $data = array();
        foreach ($config as $key => $value) {
            $data[] = array(
                'id' => $value['id'],
                'config_label' => Configuration_Constant_Server::getKeyLabel($value['config_key']),
                'config_value' => $value['config_value'],
                "config_unit" => Configuration_Constant_Server::getKeyUnit($value['config_key'])
            );
        }
        $this->view->configlist = $data;
        $this->_response->setBody($this->view->render($this->_verifyScriptName('/configuration/base_index.phtml')));
    }

    public function detailAction()
    {
    }

//    public function addAction()
//    {
//        if ($this->_request->isPost() && $this->getRequest()->getParam('insert')) {
//            $params = $this->_request->getParams();
//            $params = new Base_Php_Overloader($params);
//
//            if ($this->_isValid($params->key) == false || $this->_isValid($params->value) == false) {
//                $_SESSION['message'] = Base_Constant_Client::FAILED;
//                $this->redirect('configuration/base/add');
//            } else {
//                $result = Base_Constant_Client::SUCCESSFUL;
//                $message = '';
//                $newId = null;
//                $data = array(
//                    'config_key' => (int)$params->key,
//                    'config_value' => $params->value,
//                    'updated' => date("Y-m-d H:i:s", time()),
//                );
//                $table = Configuration_Model_DbTable_Configuration::getInstance();
//                $configKeys = $table->fetchAll('config_key = ' . $params->key);
//                $total = count($configKeys);
//                switch ($total) {
//                    case 0:
//                        $data['created'] = date("Y-m-d H:i:s", time());
//                        break;
//                    case 1:
//                        break;
//                    default:
//                        throw new Exception('Too many ' . $params->key);
//                        break;
//                }
//                try {
//                    if (isset($data['created'])) {
//                        $table->insert($data);
//                    } else {
//                        $where = $table->getAdapter()->quoteInto('config_key = ?', $params->key);
//                        $table->update($data, $where);
//                    }
//                    $_SESSION['message'] = $result;
//                } catch (Zend_Db_Exception $dbException) {
//                    $result = Base_Constant_Client::FAILED;
//                    $message = $dbException->getMessage();
//                    $_SESSION['message'] = $message;
//                }
//                $this->redirect('configuration/base/index');
//            }
//
//        } else {
//            $data = Configuration_Constant_Server::getAllKeyConfig();
//            $this->view->configKeys = $data;
//            $this->_response->setBody($this->view->render($this->_verifyScriptName('base/common/insert.phtml')));
//        }
//    }

    public function editAction()
    {
        $id = $this->getRequest()->getParam('id', 0);
        $table = Configuration_Model_DbTable_Configuration::getInstance();
        $message = '';
        $detail = '';
        $result = Base_Constant_Client::FAILED;
        $registry = Zend_Registry::getInstance();
        if($registry->session->success->updatebase){
            Zend_Registry::getInstance()->session->success->updatebase = '';
        }
        if($this->getParam("click") == 1) {
            if ($id != (string)(int)$id || $id <= 0) {
                $this->forward('error', 'index', 'base');
                $message = "error";
                $result = Base_Constant_Client::FAILED;
            } else {
                $configKeys = $table->fetchAll('id = ' . $id);
                $total = count($configKeys);
                switch ($total) {
                    case 0:
                        $this->forward('error', 'index', 'base');
                        $message = "error";
                        $result = Base_Constant_Client::FAILED;
                        break;
                    case 1:
                        $value = $configKeys[0];
                        $data = array(
                            "id" => $value['id'],
                            "config_key" => $value['config_key'],
                            "config_value" => $value['config_value'],
                            "config_label" => Configuration_Constant_Server::getKeyLabel($value['config_key']),
                            "config_unit" => Configuration_Constant_Server::getKeyUnit($value['config_key'])
                        );
                        $this->view->configdata = $data;
                        $message = "success";
                        $result = Base_Constant_Client::SUCCESSFUL;
                        break;
                    default:
                        $message = "error";
                        $result = Base_Constant_Client::FAILED;
                        throw new Exception('Too many ' . $id);
                        break;
                }
            }
            $clientData = array(
                'message' => $message,
                'result' => $result,
                'detail' =>  $this->view->render($this->_verifyScriptName('base/common/update.phtml')),
            );
            $this->_response->setBody($this->_helper->getHelper('json')->encodeJson($clientData));
        }
        else{
            $registry = Zend_Registry::getInstance();
            if ($this->_request->isPost()) {
                $params = $this->getRequest()->getParams();
                $configValue = isset($params['config_value']) ? $params['config_value'] : '';
                $validate = new Base_Controller_Helper_Validate();
                $valid_email = $validate->validation($configValue , 'email');
                $result = Base_Constant_Client::SUCCESSFUL;
                if($params['config_key'] == Configuration_Constant_Server::getKeyValue('REPORTED_EMAIL') && !$valid_email){
                    $result = Base_Constant_Client::FAILED;
                    $message = 'Invalid format. Acceptable format like abc"@xxx.ccc ';
                }
                if($params['config_key']<13 && !$validate->validation($configValue,'float')){
                    $result = Base_Constant_Client::FAILED;
                    $message = 'Invalid format. Input value just contains number and a dot ';
                }
                if(strlen($configValue) >= 255){
                    $result = Base_Constant_Client::FAILED;
                    $message = 'Inputted Value Is Too Long';
                }
                if ($result == Base_Constant_Client::FAILED) {
                    $detail = $this->view->render($this->_verifyScriptName('base/common/update.phtml'));
                    $registry->session->error->updatebase = array(
                        'message' => 'Saved fail');
                } else {
                    if($params['config_key']>13){
                        $data = array(
                            'config_value' => $configValue,
                            'updated' => date("Y-m-d H:i:s", time())
                        );
                    }
                    else {
                        $data = array(
                            'config_value' => (float)(($configValue)),
                            'updated' => date("Y-m-d H:i:s", time())
                        );
                    }
                    $where = $table->getAdapter()->quoteInto('config_key = ?', $params['config_key']);
                    $table->update($data, $where);
                    $message = "success";
                    $result = Base_Constant_Client::SUCCESSFUL;
                    $registry->session->success->updatebase = array(
                        'message' => 'Base Data Is Saved Successfully');
                }
            }
            $clientData = array(
                'message' => $message,
                'result' => $result,
                'detail' =>  $detail,
                'url' => '/configuration/base'
            );
                $this->_response->setBody($this->_helper->getHelper('json')->encodeJson($clientData));
        }
    }

//    public function deleteAction()
//    {
//        $params = $this->_request->getParams();
//        $result = Base_Constant_Client::SUCCESSFUL;
//        $message = '';
//        $table = Configuration_Model_DbTable_Configuration::getInstance();
//        try {
//            $where = $table->getAdapter()->quoteInto('id = ?', $params['id']);
//            $table->delete($where);
//        } catch (Zend_Db_Exception $dbException) {
//            $result = Base_Constant_Client::FAILED;
//            $message = $dbException->getMessage();
//        }
//
//        $clientData = array(
//            'result' => $result,
//            'message' => $message,
//            'params' => $params,
//        );
//        $_SESSION['message'] = $result;
//        $this->redirect('configuration/base/index');
//    }

    private function _isValid($content)
    {
        $this->_flag = true;
        if ($content == null || $content == '') {
            $this->_flag = false;
        }
        return $this->_flag;
    }
}