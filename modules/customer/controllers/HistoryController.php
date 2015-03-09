<?php

class Customer_HistoryController extends Auth_Controller_Action_Backend
{
    public function indexAction()
    {
        $this->getListPickUp('history.phtml');
    }
}