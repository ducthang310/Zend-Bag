<?php

class Staff_ReportController extends Base_Controller_Action_Backend
{
    public function indexAction()
    {
        $from = date('Y') . '-01-01 00:00:00';
        $to = date('Y') . '-12-31 23:59:59';

        $table = Pickup_Model_DbTable_Pickup::getInstance();
        $searchParams['date']['date_from'] = $from;
        $searchParams['date']['date_to'] = $to;
        $pickups = $table->getAll(null,null,$searchParams,false);
        $fullFillDay = array();
        $unFullFillDay = array();
        $cancelleDay = array();

        $fullFillMon = array();
        $unFullFillMon = array();
        $cancelleMon = array();

        $fullFillY = array();
        $unFullFillY = array();
        $cancelleY = array();

        $newOrderDay = 0;
        $newOrderMon = 0;
        $newOrderY = 0;

        foreach ($pickups as $pickup){
            $newOrderY ++;
            if($pickup['status'] == Pickup_Constant_Server::ASSIGNED || $pickup['status'] == Pickup_Constant_Server::PICKED_UP || $pickup['status'] == Pickup_Constant_Server::DELIVERED || $pickup['status'] == Pickup_Constant_Server::ACCEPTED || $pickup['status'] == Pickup_Constant_Server::RATED){
                $fullFillY[] = $pickup;
            }
            elseif($pickup['status'] == Pickup_Constant_Server::AWAITING){
                $unFullFillY[] = $pickup;
            }
            else{
                $cancelleY[] = $pickup;
            }
        }

        foreach ($pickups as $pickup) {
            if (date('Y-m-d h:m:s', strtotime($pickup['created'])) >= (date('Y-m-d'). ' 00:00:00') &&
                date('Y-m-d h:m:s', strtotime($pickup['created'])) <= (date('Y-m-d') . ' 23:59:59') ) {
                $newOrderDay ++;
                if($pickup['status'] == Pickup_Constant_Server::ASSIGNED || $pickup['status'] == Pickup_Constant_Server::PICKED_UP || $pickup['status'] == Pickup_Constant_Server::DELIVERED || $pickup['status'] == Pickup_Constant_Server::ACCEPTED || $pickup['status'] == Pickup_Constant_Server::RATED){
                    $fullFillDay[] = $pickup;
                }
                elseif($pickup['status'] == Pickup_Constant_Server::AWAITING){
                    $unFullFillDay[] = $pickup;
                }
                else{
                    $cancelleDay[] = $pickup;
                }
            }
        }

        $week = $this->_getWeek();
        $WeekBegin = $week['start']. ' 00:00:00';
        $WeekLast = $week['finish']. ' 23:59:59';
        $newOrderW = 0;
        $fullFillW = array();
        $unFullFillW = array();
        $cancelleW = array();

        foreach ($pickups as $pickup) {
            if (date('Y-m-d H:i:s', strtotime($pickup['created'])) >= date('Y-m-d H:i:s', strtotime($WeekBegin)) &&
                date('Y-m-d H:i:s', strtotime($pickup['created'])) <= date('Y-m-d H:i:s', strtotime($WeekLast)) ) {
                $newOrderW ++;
                if($pickup['status'] == Pickup_Constant_Server::ASSIGNED || $pickup['status'] == Pickup_Constant_Server::PICKED_UP || $pickup['status'] == Pickup_Constant_Server::DELIVERED || $pickup['status'] == Pickup_Constant_Server::ACCEPTED || $pickup['status'] == Pickup_Constant_Server::RATED){
                    $fullFillW[] = $pickup;
                }
                elseif($pickup['status'] == Pickup_Constant_Server::AWAITING){
                    $unFullFillW[] = $pickup;
                }
                else{
                    $cancelleW[] = $pickup;
                }
            }
        }

        foreach ($pickups as $pickup) {
            if (date('Y-m-d H:i:s', strtotime($pickup['created'])) >= (date('Y-m') . '-01 00:00:00') &&
                date('Y-m-d H:i:s', strtotime($pickup['created'])) <= (date('Y-m-d',strtotime('last day of this month')) .'23:59:59')) {
                $newOrderMon ++;
                if ($pickup['status'] == Pickup_Constant_Server::ASSIGNED || $pickup['status'] == Pickup_Constant_Server::PICKED_UP || $pickup['status'] == Pickup_Constant_Server::DELIVERED || $pickup['status'] == Pickup_Constant_Server::ACCEPTED || $pickup['status'] == Pickup_Constant_Server::RATED) {
                    $fullFillMon[] = $pickup;
                } elseif ($pickup['status'] == Pickup_Constant_Server::AWAITING) {
                    $unFullFillMon[] = $pickup;
                } else {
                    $cancelleMon[] = $pickup;
                }
            }
        }


        $this->view->fullFillDay = count($fullFillDay);
        $this->view->unFullFillDay = count($unFullFillDay);
        $this->view->cancelleDay = count($cancelleDay);

        $this->view->fullFillMon = count($fullFillMon);
        $this->view->unFullFillMon = count($unFullFillMon);
        $this->view->cancelleMon = count($cancelleMon);

        $this->view->fullFillY = count($fullFillY);
        $this->view->unFullFillY = count($unFullFillY);
        $this->view->cancelleY = count($cancelleY);

        $this->view->newOrderDay = $newOrderDay;
        $this->view->newOrderMon = $newOrderMon;
        $this->view->newOrderY = $newOrderY;

        $this->view->newOrderW = $newOrderW;
        $this->view->fullFillW = count($fullFillW);
        $this->view->unFullFillW = count($unFullFillW);
        $this->view->cancelleW = count($cancelleW);

        $this->_response->setBody($this->view->render($this->_verifyScriptName('report/index.phtml')));
    }

    public function rateAction()
    {
        $this->_response->setBody($this->view->render($this->_verifyScriptName('report/rate.phtml')));
    }

    private function _getWeek() {

        if(date('D')!='Mon')
        {
            $week['start'] = date('Y-m-d',strtotime('monday this week'));

        }else{
            $week['start'] = date('Y-m-d');
        }

        if(date('D')!='Sun')
        {
            $week['finish'] = date('Y-m-d',strtotime('sunday this week'));
        }else{

            $week['finish'] = date('Y-m-d');
        }

        return $week;
    }

    public function exportAction()
    {
        $params  = $this->getRequest()->getParams();
        if($params && $params['period'] != null){
            $periodType = "";
            $period =  $params['period'];
            switch(true){
                case $period == "week":
                    $week = $this->_getWeek();
                    $from = $week['start']. ' 00:00:00';
                    $to = $week['finish']. ' 23:59:59';
                    $periodType = "This Week (from : ".$from ." to ".$to ." )";
                    break;
                case $period == "month":
                    $from = date('Y-m') . '-01 00:00:00';
                    $to = date('Y-m-d',strtotime('last day of this month')) .' 23:59:59';
                    $periodType = "This Month (from : ".$from ." to ".$to ." )";
                    break;
                case $period == "year":
                    $from = date('Y') . '-01-01 00:00:00';
                    $to = date('Y') . '-12-31 23:59:59';
                    $periodType = "This Year (from : ".$from ." to ".$to ." )";
                    break;
                case $period == "day":
                    $from = date('Y-m-d') . ' 00:00:00';
                    $to = date('Y-m-d') . ' 23:59:59';
                    $periodType = "Today (from : ".$from ." to ".$to ." )";
                    break;
            }
        $searchParams['date']['date_from'] = $from;
        $searchParams['date']['date_to'] = $to;
        //Query pickup of Year
        $table = Pickup_Model_DbTable_Pickup::getInstance();
        $searchParams['date']['date_from'] = $from;
        $searchParams['date']['date_to'] = $to;
        $pickups = $table->getAll(null,null,$searchParams,false);
        // calculate for Year
            $fullFill = array();
            $unFullFill = array();
            $cancel = array();
        foreach ( $pickups as $pickup) {
            if ($pickup['status'] == Pickup_Constant_Server::ASSIGNED || $pickup['status'] == Pickup_Constant_Server::PICKED_UP || $pickup['status'] == Pickup_Constant_Server::DELIVERED || $pickup['status'] == Pickup_Constant_Server::ACCEPTED || $pickup['status'] == Pickup_Constant_Server::RATED) {
                $fullFill[] = $pickup;
            } elseif ($pickup['status'] == Pickup_Constant_Server::AWAITING) {
                $unFullFill[] = $pickup;
            } else {
                $cancel[] = $pickup;
            }
        }
            $full = 'FULLFILLED ORDERS';
            $unFull = 'UNFULLFILLED ORDERS';
            $cancelled = 'CANCELLED ORDERS';
        $array= array(
             $periodType => array(
                 $full =>  $fullFill,
                 $unFull =>  $unFullFill,
                 $cancelled => $cancel)
        );
        $headings = array('NO.', 'ID', 'Customer Name', 'Courier Name', 'From' , 'To' , 'Pick Up Status', 'BASE PICKUP FEE', 'INSURANCE' ,'SIGNATURE ON DELIVERY', 'CREDIT CARD FEE', ' TOTAL FEE');
        Base_Helper_Csv::downloadCsv($headings, $array );
     }
    }
}