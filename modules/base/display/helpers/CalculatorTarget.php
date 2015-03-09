<?php

class Zend_View_Helper_CalculatorTarget extends Zend_View_Helper_Placeholder_Container_Standalone
{
    public function calculatorTarget($pickup, $dataConfig)
    {
        $result = array(
            'h' => false,
            'm' => false
        );
        if(isset($pickup->status)) {
            switch (true) {
                case $pickup->status == Pickup_Constant_Server::CANCELLED:
                    break;
                case $pickup->status == Pickup_Constant_Server::RATED:
                    break;
                default :
                    $keyConfig = Configuration_Constant_Server::getNextConfigKeyByStatus($pickup->status);
                    break;
            }
            $keyDelivery = Configuration_Constant_Server::getNextConfigKeyByStatus(Pickup_Constant_Server::DELIVERED);
            $time_delivery = 0;
            // get time in config table by key
            foreach ($dataConfig as $key => $val) {
                if ($val['config_key'] == $keyDelivery)
                    $time_delivery = $val['config_value'];
                if (isset($keyConfig)&& $val['config_key'] == $keyConfig) {
                    $valueConfig = $val;
                    break;
                }
            }
            if (isset($valueConfig)) {
                $time = $valueConfig['config_value'];
                $diff = 0;
                switch (true) {
                    case $pickup->status == Pickup_Constant_Server::DELIVERED:
                        // TARGET= [Actual Delivered Time] - [BOOKING Date Time]-[Delivery Time]
                        $diff = strtotime(date($pickup->delivered_active_time)) - strtotime(date($pickup->created)) - $time * 3600;
                        break;
                    case $pickup->status == Pickup_Constant_Server::ACCEPTED:
                        // If Signature Req then TARGET= (Booking Date/Time) +(Delivery Time) + ( ACCEPTED_ESC_STATUS 24 hours ) - Current time
                        if ($pickup->delivery_signature_fee != 0)
                            $diff = strtotime(date($pickup->created)) + ($time_delivery * 3600) + ($time * 3600) - strtotime(date("Y-m-d H:i:s"));
                        else
                            //TARGET= [Actual Delivered Time] - [BOOKING Date Time]
                            $diff = strtotime(date($pickup->delivered_active_time)) - strtotime(date($pickup->created));
                        break;
                    default:
                        // TARGET= (Booking Date/Time) + ( TIME IN config table by status) - Current time
                        $diff = strtotime(date($pickup->created) . "+{$time} hours ") - strtotime(date("Y-m-d H:i:s"));
                        break;
                }
                $result['h'] = (int)$result['h'] <= 0 ? (int)round($diff /3600) : (int)floor($diff /3600) ;//hours
                $result['m'] = $result['h'] == 0 && round(($diff / 60) % 60) <0 ? round(($diff / 60) % 60) : abs(round(($diff / 60) % 60)) ;//minutes
            }
        }
        return $result;
    }
}
