<?php

class Staff_Controller_Action_Base extends Auth_Controller_Action_Backend
{
    public function indexAction()
    {
        $this->getListPickUp('history.phtml');
    }

    public function getListPickUp($fileHtml = 'history.phtml')
    {
        $status = $this->getRequest()->getParam('status');
        $param_sort = $this->getRequest()->getParam('sort');
        $dataParams = $this->getRequest()->getParams();
        $type = isset($param_sort['sort_type']) ? $param_sort['sort_type'] : null;
        $sort = isset($param_sort['sort_field']) ? $param_sort['sort_field'] : null;
        $dataSearch = isset($dataParams['search']) ? $dataParams['search'] : array();
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
        $pickups = Pickup_Model_DbTable_Pickup::getInstance()->getAll($status, $sorts, $dataSearch, true);
        $dataConfig = Configuration_Model_DbTable_Configuration::getInstance()->getAll();
        $page = $this->_getParam('page') ? $this->_getParam('page') : 1;
        if (isset($dataParams['btn_submit'])) {
            if ($dataSearch['date']['date_to'] != '' && $dataSearch['date']['date_to'] < $dataSearch['date']['date_from'])
                $this->view->errorDate = "The 'DATE TO' must be greater than or equal to 'DATE FROM'";
            $page = 1;
        }
        $dataParams['page'] = $page;
        $this->view->dataParams = $dataParams;
        $this->view->dataConfig = $dataConfig;
        $this->view->dataHeadTitle = $this->generateHeadTitle();
        $this->view->sort = array('sortType' => $type_current, 'sortField' => $sort, 'type' => $type);
        $this->view->pickups = $this->paginatorAction($pickups, $page);
        $this->_response->setBody($this->view->render($this->_verifyScriptName($fileHtml)));
    }

    public function generateHeadTitle()
    {
        $data = array(
            array(
                'title' => 'Id',
                'sort_field' => 'id'
            ),
            array(
                'title' => 'PU Date&Time',
                'sort_field' => 'picked_up_active_time'
            ),
            array(
                'title' => 'PU Sub',
                'sort_field' => 'from_address'
            ),
            array(
                'title' => 'Del Sub',
                'sort_field' => 'to_address'
            ),
            array(
                'title' => 'Status',
                'sort_field' => 'status'
            ),
            array(
                'title' => 'Deli Date&Time',
                'sort_field' => 'delivered_active_time'
            ),
            array(
                'title' => 'Target',
                'sort_field' => 'created'
            ),
            array(
                'title' => 'Escalation Status',
                'sort_field' => 'status'
            ),
//            array(
//                'title' => 'Msg',
//                'sort_field' => 'note'
//            ),
            array(
                'title' => 'Courier',
                'sort_field' => 'courier_id'
            ),
            array(
                'title' => 'Customer',
                'sort_field' => 'customer_id'
            ),
        );
        return $data;
    }

}