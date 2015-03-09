<?php

class Staff_CourierController extends Base_Controller_Action_Backend
{
    public function indexAction()
    {
        $this->getData('courier/index.phtml');
    }


    public function rejectAction()
    {
        $params = $this->getRequest()->getParams();
        $result = Base_Constant_Client::SUCCESSFUL;
        $message = "";
        $status = Courier_Constant_Server::REJECTED;
        $params = new Base_Php_Overloader($params);
        if ($this->getRequest()->isPost() && $this->getRequest()->getParam('reject')) {
            $message_reject = array(
                'content' => "Your " . Courier_Constant_Server::$_DOCUMENT[$params->type] . " was rejected because :  " . $params->message,
                'id' => $params->id,
            );
            if (!$this->_confirmApproved($params->type, $params->id, $status)) {
                $result = Base_Constant_Client::FAILED;
                $message = "$params->type , $params->id , $status";
            } else {
                if (!$this->_sendMessage($message_reject)) {
                    $result = Base_Constant_Client::FAILED;
                    $message = "$params->type , $params->id , $status";
                };
            }
            $clientData = array(
                "result" => $result,
                'message' => $message,
            );
            $this->_response->setBody($this->_helper->getHelper('json')->encodeJson($clientData));
        }
    }

    private function _confirmApproved($type = null, $id = null, $status = null)
    {
        $table = Courier_Model_DbTable_Courier::getInstance();
        $data = array(
            $type => $status,
            'updated' => date("Y-m-d H:i:s", time()),
        );
        $where = $table->getAdapter()->quoteInto('auth_id = ?', $id);
        if (!$table->update($data, $where)) {
            return false;
        } else {
            return true;
        }
    }

    public function approvedAction()
    {
        $params = $this->getRequest()->getParams();
        $result = Base_Constant_Client::SUCCESSFUL;
        $message = "";
        $status = Courier_Constant_Server::APPROVED;
        $params = new Base_Php_Overloader($params);
        if ($this->getRequest()->isPost() && $this->getRequest()->getParam('approved')) {
            if (!$this->_confirmApproved($params->type, $params->id, $status)) {
                $result = Base_Constant_Client::FAILED;
                $message = "fail";
            }
            $clientData = array(
                "result" => $result,
                'message' => $message,
            );
            $this->_response->setBody($this->_helper->getHelper('json')->encodeJson($clientData));
        }
    }

    private function _sendMessage($content)
    {
        if (isset($content) && $content != null) {
            $auth = Zend_Auth::getInstance();
            $auth_id = $auth->getStorage()->read()->detail->auth_id;
            $message = array(
                "content" => $content['content'],
                "to_login_id" => (int)$content['id'],
                "from_login_id" => $auth_id,
                "pickup_id" => 0,
                "created" => date("Y-m-d H:i:s", time()),
                "message_type" => Message_Constant_Client::BROADCAST_MESSAGE,
                "is_read" => 0,
                "reader" => 0,
            );
            $message_table = Message_Model_DbTable_Message::getInstance();
            if ($message_table->insert($message)) {
                return true;
            } else {
                return false;
            }
        };
    }

    public function confirmAction()
    {
        $params = $this->getRequest()->getParams();
        $result = Base_Constant_Client::SUCCESSFUL;
        $registry = Zend_Registry::getInstance();
        if (Zend_Registry::getInstance()->session->success->approved) {
            Zend_Registry::getInstance()->session->success->approved = '';
        }
        $table = Courier_Model_DbTable_Courier::getInstance();
        if ($this->getRequest()->getParam('confirm')) {
            switch (true) {
                case isset($params['head_office_approved']):
                    $data = array(
                        'head_office_approved' => (int)$params['head_office_approved'],
                        'can_assign' => (int)$params['can_assign'],
                    );
                    $message = array(
                        "content" => "Welcome ! Courier !",
                        "id" => $params['id']
                    );
                    if ($params['head_office_approved'] == Courier_Constant_Server::HEAD_OFFICE_APPROVED) {
                        $this->_sendMessage($message);
                        $message_approved = "This courier has been approved successfully";
                    } else {
                        $message_approved = "This courier has been unapproved successfully";
                    }
                    break;
                default :
                    $data = array(
                        'can_assign' => (int)$params['can_assign'],
                    );
                    break;
            }
            $where = $table->getAdapter()->quoteInto('auth_id = ?', $params['id']);
            if ($table->update($data, $where)) {
                $registry->session->success->approved = array(
                    'message' => $message_approved);
            } else {
                $registry->session->error->approved = array(
                    'message' => "This courier has been approved fail");
            }
        };
        $clientData = array(
            'message' => $params['head_office_approved'],
            'result' => $result,
            'url' => '/staff/courier',
        );
        $this->_response->setBody($this->_helper->getHelper('json')->encodeJson($clientData));
    }

    public function profileAction()
    {
        $auth = $this->getRequest()->getParam('id', 0);
        $result = Base_Constant_Client::SUCCESSFUL;
        $message = '';
        $table = Courier_Model_DbTable_Courier::getInstance();
        if ($this->getRequest()->getParam('approved')) {
            if ($auth != (string)(int)$auth || $auth <= 0) {
                $this->forward('error', 'index', 'base');
                $message = "error";
                $result = Base_Constant_Client::FAILED;
            } else {
                $courier = $table->fetchAll('auth_id = ' . $auth);
                $total = count($courier);
                switch ($total) {
                    case 0:
                        $this->forward('error', 'index', 'base');
                        $message = "error";
                        $result = Base_Constant_Client::FAILED;
                        break;
                    case 1:
                        $suburbs = Configuration_Model_DbTable_Suburb::getInstance();
                        foreach ($courier as $key => $value) {
                            $data = array(
                                'id' => $value['auth_id'],
                                'email' => $value['email'],
                                'image' => $value['image'],
                                'abn' => $value['abn'],
                                'contact_name' => $value['contact_firstname'] . ' ' . $value['contact_lastname'],
                                'contact_number' => $value['contact_number'],
                                'mobile' => $value['mobile'],
                                'can_assign' => $value['can_assign'],
                                "photo" => $value['photo'],
                                "utility_bill" => $value['utility_bill'],
                                "bank_statement" => $value['bank_statement'],
                                "bank_statement_approved" => $value['bank_statement_approved'],
                                "utility_bill_approved" => $value['utility_bill_approved'],
                                "photo_approved" => $value['photo_approved'],
                                "head_office_approved" => $value['head_office_approved'],
                            );
                            $area = array(
                                'preference_pickup' => $suburbs->getSuburbById($value['preferred_pickup_suburb_ids']),
                                'preference_delivery' => $suburbs->getSuburbById($value['preferred_delivery_suburb_ids']),
                            );
                        }
                        $this->view->area = $area;
                        $this->view->courier = $data;
                        $message = "success";
                        $result = Base_Constant_Client::SUCCESSFUL;
                        break;
                    default:
                        $message = "error";
                        $result = Base_Constant_Client::FAILED;
                        throw new Exception('Too many ' . $auth);
                        break;
                }
            }
            $clientData = array(
                'message' => $message,
                'result' => $result,
                'detail' => $this->view->render($this->_verifyScriptName('courier/approved.phtml')),
            );
            $this->_response->setBody($this->_helper->getHelper('json')->encodeJson($clientData));
        }
    }

    public function downloadAction()
    {
        if ($this->getRequest()->getParam('file')) {
            ignore_user_abort(true);
            set_time_limit(0);
            $path = WWW_PATH . DIRECTORY_SEPARATOR . PROJECT_MEDIA . DIRECTORY_SEPARATOR . "upload" . DIRECTORY_SEPARATOR;
            $dl_file = preg_replace("([^\w\s\d\-_~,;:\[\]\(\].]|[\.]{2,})", '', $this->getRequest()->getParam('file'));
            $dl_file = filter_var($dl_file, FILTER_SANITIZE_URL);
            $fullPath = $path . $dl_file;
            if (file_exists($fullPath) && is_readable($fullPath)) {
                switch (true) {
                    case !$this->getRequest()->getParam('download') :
                        if ($fd = fopen($fullPath, "r")) {
                            $fsize = filesize($fullPath);
                            $path_parts = pathinfo($fullPath);
                            $ext = strtolower($path_parts["extension"]);
                            switch ($ext) {
                                case "pdf":
                                    header("Content-type: application/pdf");
                                      break;
                                default;
                                    header("Content-type: application/octet-stream");
                                    break;
                            }
                            header("Content-Disposition: attachment; filename=\"" . $path_parts["basename"] . "\"");
                            header("Content-length: $fsize");
                            header("Cache-control: private");
                            while (!feof($fd)) {
                                $buffer = fread($fd, 2048);
                                echo $buffer;
                            }
                        }
                        fclose($fd);
                        exit;
                        break;
                    default;
                        exit;
                        break;
                }

            } else {
                $error = true;
                exit($error);
            }
        }

    }
}