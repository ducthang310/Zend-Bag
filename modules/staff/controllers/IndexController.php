<?php

class Staff_IndexController extends Base_Controller_Action_Backend
{
    public function indexAction()
    {
        $this->redirect('staff/pickup');
    }
}