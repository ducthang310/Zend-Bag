<?php

class Zend_View_Helper_GenerateEscStatus extends Zend_View_Helper_Placeholder_Container_Standalone
{
    public function generateEscStatus($pickup, $target)
    {
        switch (true) {
            case $pickup->status == Pickup_Constant_Server::AWAITING && $target['h'] > 0 && $target['m'] > 0:
                $escStatus = 'NOT ASSIGNED';
                break;
            case $pickup->status == Pickup_Constant_Server::ASSIGNED && $target['h'] > 0 && $target['m'] > 0:
                $escStatus = 'NOT PICKED UP';
                break;
            case $pickup->status == Pickup_Constant_Server::CANCELLED:
                $escStatus = 'CANCELLED';
                break;
            case $pickup->status == Pickup_Constant_Server::PICKED_UP && $target['h'] > 0 && $target['m'] > 0:
                $escStatus = 'NOT DELIVERED';
                break;
            case $pickup->status == Pickup_Constant_Server::ACCEPTED:
                $escStatus = 'NOT RATED';
                break;
            case $pickup->status == Pickup_Constant_Server::DELIVERED && $target['h'] > 0 && $target['m'] > 0 && $pickup->delivery_signature_fee > 0:
                $escStatus = 'NOT ACCEPTED';
                break;
            default :
                $escStatus = '';
                break;
        }
        return $escStatus;
    }
}
