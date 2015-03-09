<?php

class Configuration_RatingController extends Base_Controller_Action_Backend
{
    private $_flag;

    public function indexAction()
    {
        $this->_list();
    }

    private function _list()
    {
        $table = Configuration_Model_DbTable_Rating::getInstance();
        $data = $table->fetchAll($table->select()->order('id ASC'));
        $this->view->ratinglist = $data;
        $this->_response->setBody($this->view->render($this->_verifyScriptName('/configuration/rating_index.phtml')));
    }

    public function addAction()
    {
        $registry = Zend_Registry::getInstance();
        if ($this->_request->isPost()) {
            if ($this->_request->getParam('add')) {
                $params = $this->_request->getParams();
                $params = new Base_Php_Overloader($params);
                $data_question = isset($params->question) ? $params->question : '';
                if (!$data_question || strlen($data_question) > 511) {
                    $message = "Field can't blank ";
                    if (strlen($data_question) > 511) {
                        $message = 'Question is too long. Max length is 512 characters ';
                    }
                    $registry->session->error->rating = array(
                        'message' => $message);
                    $this->redirect('configuration/rating/add');
                } else {
                    $result = Base_Constant_Client::SUCCESSFUL;
                    $message = '';
                    $newId = null;
                    $data = array(
                        'question' => $params->question,
                        'created' => date("Y-m-d H:i:s", time()),
                        'updated' => date("Y-m-d H:i:s", time()),
                    );
                    try {
                        $table = Configuration_Model_DbTable_Rating::getInstance();
                        $table->insert($data);

                        $registry->session->success->rating = array(
                            'message' => 'Added successfully');
                        $this->redirect('configuration/rating');
                    } catch (Zend_Db_Exception $dbException) {
                        $result = Base_Constant_Client::FAILED;
                        $message = $dbException->getMessage();
                        $_SESSION['message'] = Base_Constant_Client::FAILED;
                        $registry->session->error->rating = array(
                            'message' => 'Added Fail');
                        $this->redirect('configuration/rating/add');
                    }
                }
            } elseif ($this->_request->getParam('cancel')) {
                $this->redirect('configuration/rating');
            }
        } else {
            $this->_response->setBody($this->view->render($this->_verifyScriptName('/configuration/rating_add.phtml')));
        }
    }

    public function editAction()
    {
        $id = $this->getRequest()->getParam('id', 0);
        $table = Configuration_Model_DbTable_Rating::getInstance();
        $message = ' Something Wrong';
        $detail = '';
        $result = Base_Constant_Client::FAILED;
        $registry = Zend_Registry::getInstance();
        if ($registry->session->success->rating) {
            Zend_Registry::getInstance()->session->success->rating = '';
        }
        if ($registry->session->error->rating) {
            Zend_Registry::getInstance()->session->error->rating = '';
        }
        if ($this->getRequest()->getParam('click') == 1) {
            if ($id != (string)(int)$id || $id <= 0) {
                $this->forward('error', 'index', 'base');
            } else {
                $question = $table->fetchAll('id = ' . $id);
                $total = count($question);
                switch ($total) {
                    case 0:
                        $this->forward('error', 'index', 'base');
                        break;
                    case 1:
                        $this->view->question = $question[0];
                        $message = "success";
                        $result = Base_Constant_Client::SUCCESSFUL;
                        $detail = $this->view->render($this->_verifyScriptName('rating/common/update.phtml'));
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
                $data_question = isset($params->question) ? $params->question : '';
                if (!$data_question || strlen($data_question) > 511) {
                    $message = "Field can't blank ";
                    if (strlen($data_question) > 511) {
                        $message = 'Question is too long ';
                    }
                    $result = Base_Constant_Client::FAILED;
                    $detail = $this->view->render($this->_verifyScriptName('rating/common/update.phtml'));
                    $registry->session->error->rating = array(
                        'message' => $message);
                } else {
                    $data = array(
                        'question' => $data_question,
                        'updated' => date("Y-m-d H:i:s", time())
                    );
                    $where = $table->getAdapter()->quoteInto('id = ?', $params->id);
                    if ($table->update($data, $where)) {
                        $message = "updated success";
                        $result = Base_Constant_Client::SUCCESSFUL;
                        $registry->session->success->rating = array(
                            'message' => 'Updated successfully');
                    } else {
                        $message = "fail";
                        $result = Base_Constant_Client::FAILED;
                        $registry->session->error->rating = array(
                            'message' => 'Updated fail');
                    }
                }
            }
            $clientData = array(
                'message' => $message,
                'result' => $result,
                'detail' => $detail,
                'url' => '/configuration/rating'
            );
            $this->_response->setBody($this->_helper->getHelper('json')->encodeJson($clientData));
        }
    }

    public function activeAction()
    {
        $active = 0;
        $params = $this->_request->getParams();
        $message = ' Something Wrong';
        $detail = '';
        $result = Base_Constant_Client::FAILED;
        $registry = Zend_Registry::getInstance();
        if ($this->_request->getParam('id')) {
            $result = Base_Constant_Client::SUCCESSFUL;
            $message = '';
            $table = Configuration_Model_DbTable_Rating::getInstance();
            $row = $table->fetchRow('id=' . $this->_request->getParam('id'));
            $active = ($row['active']) ? 0 : 1;
            try {
                $where = $table->getAdapter()->quoteInto('id = ?', $params['id']);
                $data = array(
                    'active' => $active,
                    'updated' => date("Y-m-d H:i:s", time())
                );
                $table->update($data, $where);
                $message = "updated success";
                $result = Base_Constant_Client::SUCCESSFUL;
                $registry->session->success->rating = array(
                    'message' => 'Updated successfully');
            } catch (Zend_Db_Exception $dbException) {
                $result = Base_Constant_Client::FAILED;
                $message = "updated fail";
                $registry->session->error->rating = array(
                    'message' => 'Updated fail');
            }
        }
        $clientData = array(
            'message' => $message,
            'result' => $result,
            'detail' => $detail,
            'url' => '/configuration/rating'
        );
        $this->_response->setBody($this->_helper->getHelper('json')->encodeJson($clientData));
    }

//    public function deleteAction()
//    {
//        $params = $this->_request->getParams();
//        if ($this->_request->getParam('id')) {
//            $result = Base_Constant_Client::SUCCESSFUL;
//            $message = '';
//            $table = Configuration_Model_DbTable_Rating::getInstance();
//            try {
//                $where = $table->getAdapter()->quoteInto('id = ?', $params['id']);
//                $table->delete($where);
//                $_SESSION['message'] = Base_Constant_Client::SUCCESSFUL;
//            } catch (Zend_Db_Exception $dbException) {
//                $result = Base_Constant_Client::FAILED;
//                $message = $dbException->getMessage();
//                $_SESSION['message'] = Base_Constant_Client::FAILED;
//            }
//            $this->redirect('configuration/rating/index');
//        }
//        $this->redirect('configuration/rating/index');
//    }

    private function _isValid($content)
    {
        $this->_flag = true;
        if ($content == null) {
            $this->_flag = false;
        }
        return $this->_flag;
    }
}