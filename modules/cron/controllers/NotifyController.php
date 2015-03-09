<?php
class Cron_NotifyController extends Base_Controller_Action_Backend
{
    public function indexAction(){
        $this->notifyEscStatus();
    }

    public function notifyEscStatus()
    {
        $pickups = $this->getActivePickup();
        $configs = Configuration_Model_DbTable_Configuration::getInstance()->getAll();
        $keyConfig = Configuration_Constant_Server::getKeyValue('REPORTED_EMAIL');
        foreach ($configs as $data) {
            if ($data['config_key'] == $keyConfig) {
                $reported_email = $data['config_value'];
                echo '>>>mail::: '.$reported_email;
            }
        }

        $front = Zend_Controller_Front::getInstance();
        $email = new Base_Php_Overloader($front->getParam("bootstrap")->getOption('smtp'));
        $username = $email->username;
        if (!isset($reported_email)) {
            $reported_email = $username;
        }
        foreach ($pickups as $pickup) {
            $target = $this->view->calculatorTarget($pickup, $configs);
            if ((($target['h'] < 0 || $target['m'] < 0 ) && $pickup['status'] != Pickup_Constant_Server::DELIVERED)
                || (($target['h'] > 0 || $target['m'] > 0 ) && $pickup['status'] == Pickup_Constant_Server::DELIVERED)
            ) { // take much time in a status
                try{
                    $content = array(
                        "sender" => $username,
                        "nameSender" => 'Dlivr',
                        "recipient" => $reported_email,
                        'nameRecipient' => 'Super admin',
                        "subject" => "Escalation Report",
                        "body" => "The Pickup (id=". $pickup['id'] .") is over escalation point (Target is ".$target['h'] . ' : ' . $target['m']." Hours). \r\nCurrent status of parcel is " . Pickup_Constant_Server::$_STATUS[$pickup->status],
                    );
                    if(!Base_Helper_Mail::sendMail($content)) {
                        Base_Helper_Log::getInstance()->log(PHP_EOL . date('H:i:s :::: ') . ' [CRON] Can not send mail to  ' . $reported_email . ' or ' . $username );
                    }
                }
                catch (Exception $e){
                    Base_Helper_Log::getInstance()->log(PHP_EOL . date('H:i:s :::: ') . ' [CRON] Exception:::: '.$e->getMessage());
                    break;
                }
            }
        }
    }

    private function getActivePickup()
    {
        $table = Pickup_Model_DbTable_Pickup::getInstance();
        $table->select('id , created, delivered_active_time, delivery_signature_fee');
        $pickups = $table->fetchAll('status <= ' . Pickup_Constant_Server::ACCEPTED);
        return $pickups;
    }
}