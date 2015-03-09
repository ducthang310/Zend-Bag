<?php


abstract class Base_Controller_Action_Backend extends Base_Controller_Action
{
    protected $_area = self::AREA_BACKEND;

    public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array())
    {
        parent::__construct($request, $response, $invokeArgs);
        $paths = $this->view->getScriptPaths();
        $this->view->setScriptPath(WWW_PATH . DS . 'views' . DS . 'scripts');
        $this->view->addScriptPath($paths);
    }

    public function paginatorAction(Zend_Db_Select $data = null, $page = null)
    {
        if (!$data instanceof Zend_Db_Select && !is_array($data)) {
            throw new Exception('The parameter $data must be instance of Zend_Db_Select or Array');
        }
        $page = (int)$page;
        if($page < 0) {
            throw new Exception('The parameter $page must be greater and equal to zero');
        }
        else {
            $page = $page ? $page : 1;
        }
        $paginator = Zend_Paginator::factory($data);
        $paginator->setItemCountPerPage(isset(Zend_Registry::getInstance()->appConfig['paginator']['itemperpage'])? Zend_Registry::getInstance()->appConfig['paginator']['itemperpage'] : 0);
        $paginator->setCurrentPageNumber($page);
        $paginator->setPagerange(isset(Zend_Registry::getInstance()->appConfig['paginator']['pagerange']) ? Zend_Registry::getInstance()->appConfig['paginator']['pagerange'] : '');

        return $paginator;
    }

    public function getListPickUp($fileHtml = 'index.phtml')
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
        $page = $this->_getParam('page') ? $this->_getParam('page') : 1;
        if (isset($dataParams['btn_submit'])) {
            if ($dataSearch['date']['date_to'] != '' && $dataSearch['date']['date_to'] < $dataSearch['date']['date_from'])
                $this->view->errorDate = "The 'DATE TO' must be greater than or equal to 'DATE FROM'";
            $page = 1;
        }
        $dataParams['page'] = $page;
        $this->view->dataParams = $dataParams;
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
                'title' => 'Date',
                'sort_field' => 'created'
            ),
            array(
                'title' => 'From',
                'sort_field' => 'from_address'
            ),
            array(
                'title' => 'To',
                'sort_field' => 'to_address'
            ),
            array(
                'title' => 'Status',
                'sort_field' => 'status'
            ),
        );
        return $data;
    }

    public function getData($fileHtml = 'index.phtml')
    {
//        $auth_user = Zend_Auth::getInstance()->getStorage()->read();
//        foreach($auth_user as $key => $value){
//            $role = $value['role'];
//            $area = $value['area_ids'];
//        };
        $param_sort = $this->getRequest()->getParam('sort');
        $dataParams = $this->getRequest()->getParams();
        if(isset($dataParams['search']['field']['courier.head_office_approved']) &&  $dataParams['search']['field']['courier.head_office_approved'] == -1)
        { unset($dataParams['search']['field']['courier.head_office_approved']);}
        if(isset($dataParams['search']['field']['courier.can_assign']) &&  $dataParams['search']['field']['courier.can_assign'] == -1)
        { unset($dataParams['search']['field']['courier.can_assign']);}
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
        $controllerName = Zend_Registry::getInstance()->controllerName;
        if ($controllerName == 'courier') {

            $model = Courier_Model_DbTable_Courier::getInstance();
        } elseif ($controllerName == 'customer') {
            $model = Customer_Model_DbTable_Customer::getInstance();
        }
        $data = $model->getAll($sorts, $dataSearch,true);
        $page = $this->_getParam('page') ? $this->_getParam('page') : 1;
        if (isset($dataParams['btn_submit']))
            $page = 1;
        $dataParams['page'] = $page;
        $this->view->dataParams = $dataParams;
        $this->view->dataHeadTitle = $this->generateHeadSearchTitle();
        $this->view->sort = array('sortType' => $type_current, 'sortField' => $sort, 'type' => $type);
        $this->view->data = $this->paginatorAction($data, $page);
        $this->_response->setBody($this->view->render($this->_verifyScriptName($fileHtml)));
    }

    public function generateHeadSearchTitle()
    {
        $data = array(
            array(
                'title' => 'Date',
                'sort_field' => 'created'
            ),
            array(
                'title' => 'Company Name',
                'sort_field' => 'company_name'
            ),
            array(
                'title' => 'abn',
                'sort_field' => 'abn'
            ),
            array(
                'title' => 'Full name',
                'sort_field' => 'contact_firstname'
            ),
        );
        return $data;
    }
}