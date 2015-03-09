<?php

class Configuration_RegionController extends Base_Controller_Action_Backend
{
    private $_flag;

    public function indexAction()
    {
//        $this->_listSuburb();
        $this->getListRegion();
    }

    private function _listSuburb()
    {
        $table = Configuration_Model_DbTable_Suburb::getInstance();
        $config = $table->fetchAll();
        $data = array();
        foreach ($config as $key => $value) {
            $data[] = array(
                'id' => $value['id'],
                'country' => $value['country'],
                'state' => $value['state'],
                'region' => $value['region'],
                'suburb' => $value['suburb'],
                'postcode' => $value['postcode'],
            );
        }
        $this->view->configlist = $data;
        $this->_response->setBody($this->view->render($this->_verifyScriptName('/configuration/region_index.phtml')));
    }

    public function generateHeadTitle()
    {
        $data = array(
            array(
                'title' => 'Id',
                'sort_field' => 'id'
            ),
            array(
                'title' => 'Country',
                'sort_field' => 'country'
            ),
            array(
                'title' => 'State',
                'sort_field' => 'state'
            ),
            array(
                'title' => 'Region',
                'sort_field' => 'segion'
            ),
            array(
                'title' => 'Suburb',
                'sort_field' => 'suburb'
            ),
            array(
                'title' => 'Postcode',
                'sort_field' => 'postcode'
            ),
        );
        return $data;
    }



    public function getListRegion(){
        $param_sort = $this->getRequest()->getParam('sort');
        $type = isset($param_sort['sort_type']) ? $param_sort['sort_type'] : null;
        $sort = isset($param_sort['sort_field']) ? $param_sort['sort_field'] : null;
        $sorts = null;
        $type_current = $type;
        if ($sort && $type) {
            $sorts = $sort . ' ' . $type;
        }
        $sort_session = new Zend_Session_Namespace('sort');
        if (isset($sort_session->sort)) {
            if ($sort_session->sort == $sort) {
                if ($type == 'ASC') {
                    $type = 'DESC';
                } else if ($type == 'DESC') {
                    $type = 'ASC';
                }
            } else {
                $sort_session->sort = $sort;
                $type = 'DESC';
            }
        } else {
            $sort_session->sort = $sort;
            $type = 'DESC';
        }
        $suburbs = Configuration_Model_DbTable_Suburb::getInstance()->getAll($sorts, true);
        $page = $this->_getParam('page') ? $this->_getParam('page') : 1;
        if (isset($dataParams['btn_submit']))
            $page = 1;
        $dataParams['page'] = $page;
        $this->view->dataParams = $dataParams;
        $this->view->dataHeadTitle = $this->generateHeadTitle();
        $this->view->sort = array('sortType' => $type_current, 'sortField' => $sort, 'type' => $type);
        $this->view->configlist = $this->paginatorAction($suburbs, $page);
        $this->_response->setBody($this->view->render($this->_verifyScriptName('configuration/region_index.phtml')));
    }

    public function addAction()
    {
        $registry = Zend_Registry::getInstance();
        if ($this->_request->isPost()) {
            if ($this->_request->getParam('add')) {
                $params = $this->_request->getParams();
                $params = new Base_Php_Overloader($params);
                if (!$this->validate($params)) {
                    $_SESSION['message'] = Base_Constant_Client::FAILED;
                    $message = 'Field can not blank ';
                    if (strlen($params->postcode) > 5)
                        $message = 'Post code must be less than 5 characters ';
                    elseif (!is_numeric($params->postcode)){
                        $message = 'Post code must be number ';
                    }
                    $registry->session->error->region = array(
                        'message' => $message);
                    $this->redirect('configuration/region/add');
                } else {
                    $result = Base_Constant_Client::SUCCESSFUL;
                    $message = '';
                    $newId = null;
                    $data = array(
                        'country' => $params->country,
                        'state' => $params->state,
                        'region' => $params->region,
                        'suburb' => $params->suburb,
                        'postcode' => (int)$params->postcode,
                        'created' => date("Y-m-d H:i:s", time()),
                        'updated' => date("Y-m-d H:i:s", time()),
                    );
                    try {
                        $table = Configuration_Model_DbTable_Suburb::getInstance();
                        $table->insert($data);
                        $registry->session->success->region = array(
                            'message' => 'Added successfully');
                        $this->redirect('configuration/region');
                    } catch (Zend_Db_Exception $dbException) {
                        $result = Base_Constant_Client::FAILED;
                        $message = $dbException->getMessage();
                        $registry->session->success->region = array(
                            'message' => 'Added Fail: ' . $message);
                        $this->redirect('configuration/region/add');
                    }
                }
            } elseif ($this->_request->getParam('cancel')) {
                $this->redirect('configuration/region');
            }
        } else {
            $data = Configuration_Constant_Server::getCountry();
            $this->view->configdata = $data;
            $this->_response->setBody($this->view->render($this->_verifyScriptName('/configuration/region_add.phtml')));
        }
    }

    public function editAction()
    {
        $id = $this->getRequest()->getParam('id', 0);
        $table = Configuration_Model_DbTable_Suburb::getInstance();
        $message = ' something wrong';
        $detail = '';
        $result = Base_Constant_Client::FAILED;
        $registry = Zend_Registry::getInstance();
        if ($registry->session->success->region) {
            Zend_Registry::getInstance()->session->success->region = '';
        }
        if ($registry->session->error->region) {
            Zend_Registry::getInstance()->session->error->region = '';
        }
        if ($this->getParam("click") == 1) {
            if ($id != (string)(int)$id || $id <= 0) {
                $this->forward('error', 'index', 'base');
            } else {
                $region = $table->fetchAll('id = ' . $id);
                $total = count($region);
                switch ($total) {
                    case 0:
                        $this->forward('error', 'index', 'base');
                        break;
                    case 1:
                        $this->view->region = $region[0];
                        $message = "success";
                        $result = Base_Constant_Client::SUCCESSFUL;
                        $detail = $this->view->render($this->_verifyScriptName('region/common/update.phtml'));
                        break;
                    default:
                        throw new Exception('Too many ' . $id);
                        break;
                }
            }
            $clientData = array(
                'message' => $message,
                'result' => $result,
                'detail' => $detail,
            );
            $this->_response->setBody($this->_helper->getHelper('json')->encodeJson($clientData));
        } else {
            if ($this->_request->isPost()) {
                $params = $this->getRequest()->getParams();
                $params = new Base_Php_Overloader($params);
                $data_country = isset($params->country) ? $params->country : '';
                $data_state = isset($params->state) ? $params->state : '';
                $data_region = isset($params->region) ? $params->region : '';
                $data_suburb = isset($params->suburb) ? $params->suburb : '';
                $data_postcode = isset($params->postcode) ? (int)$params->postcode : '';
                if (!$this->validate($this->getRequest()->getParams())) {
                    $message = "Field can't blank ";
                    if (strlen($data_postcode) > 5) {
                        $message = 'Post code must be less than 5 characters ';
                    }
                    if (!is_numeric($params->postcode)){
                        $message = 'Post code must be number ';
                    }
                    $result = Base_Constant_Client::FAILED;
                    $detail = $this->view->render($this->_verifyScriptName('region/common/update.phtml'));
                    $registry->session->error->region = array(
                        'message' => $message);
                } else {
                    $data = array(
                        'country' => $data_country,
                        'state' => $data_state,
                        'region' => $data_region,
                        'suburb' => $data_suburb,
                        'postcode' => (int)$data_postcode,
                        'updated' => date("Y-m-d H:i:s", time())
                    );
                    $where = $table->getAdapter()->quoteInto('id = ?', $params->id);
                    if ($table->update($data, $where)) {
                        $_SESSION['message'] = Base_Constant_Client::SUCCESSFUL;
                        $message = "updated success";
                        $result = Base_Constant_Client::SUCCESSFUL;
                        $registry->session->success->region = array(
                            'message' => 'Updated successfully');
                    } else {
                        $_SESSION['message'] = Base_Constant_Client::FAILED;
                        $message = "fail";
                        $result = Base_Constant_Client::FAILED;
                        $registry->session->error->region = array(
                            'message' => 'Updated fail');
                    }
                }
            }
            $clientData = array(
                'message' => $message,
                'result' => $result,
                'detail' => $detail,
                'url' => '/configuration/region'
            );
            $this->_response->setBody($this->_helper->getHelper('json')->encodeJson($clientData));
        }
    }

    public function deleteAction()
    {
        $params = $this->_request->getParams();
        $registry = Zend_Registry::getInstance();
        if ($this->_request->getParam('id')) {
            $result = Base_Constant_Client::SUCCESSFUL;
            $message = '';
            $table = Configuration_Model_DbTable_Suburb::getInstance();
            try {
                $where = $table->getAdapter()->quoteInto('id = ?', $params['id']);
                $table->delete($where);
                $_SESSION['message'] = Base_Constant_Client::SUCCESSFUL;
                $registry->session->success->region = array(
                    'message' => 'Deleted successfully');
            } catch (Zend_Db_Exception $dbException) {
                $result = Base_Constant_Client::FAILED;
                $message = $dbException->getMessage();
                $registry->session->error->region = array(
                    'message' => 'Deleted fail');
                $_SESSION['message'] = Base_Constant_Client::FAILED;
            }
            $this->redirect('configuration/region/index');
        }
        $this->redirect('configuration/region/index');
    }

    private function _isValid($content)
    {
        $this->_flag = true;
        if ($content == null) {
            $this->_flag = false;
        }
        return $this->_flag;
    }

    private function validate($param){
        $param = new Base_Php_Overloader($param);
        if (strlen($param->country) > 63 || strlen($param->state) > 7 || strlen($param->region) > 255||
            strlen($param->suburb) > 255 || strlen($param->postcode) > 5 ||
            !$param->country || !$param->state || !$param->region || !$param->suburb
            || !is_numeric($param->postcode)){
            return false;
        }
        return true;
    }

    public function resultAction()
    {

        $this->_search();
    }

    private function _search()
    {
        if ($this->getRequest()->getParam("key") && $this->getRequest()->getParam("key") != null) {
            $table = Configuration_Model_DbTable_Suburb::getInstance();
            $key = $this->getRequest()->getParam('key');
            $where = $table->select()->where('suburb like ?', $key);
            $result = $table->fetchAll($where);
            if (count($result) <= 0) {
                $this->view->result = "NO RESULT FOUNDED";
            } else {
                $data = array();
                foreach ($result as $key => $value) {
                    $data[] = array(
                        'id' => $value['id'],
                        'country' => $value['country'],
                        'state' => $value['state'],
                        'region' => $value['region'],
                        'suburb' => $value['suburb'],
                        'postcode' => $value['postcode'],
                    );
                }
                $this->view->configdata = $data;
            }
            $this->_response->setBody($this->view->render($this->_verifyScriptName('region/index.phtml')));
        } else {
            $this->redirect('configuration/region/index');
        };

    }
}