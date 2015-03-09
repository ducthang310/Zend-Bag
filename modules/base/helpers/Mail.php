<?php

class Base_Helper_Mail
{

   static  public function sendMail($content)
    {
            $front = Zend_Controller_Front::getInstance();
            $email = new Base_Php_Overloader($front->getParam("bootstrap")->getOption('smtp'));
            $smtp = $email->domain;
            $username = $email->username;
            $password = $email->password;
            $port = $email->port;
            $config = new Zend_Mail_Transport_Smtp ($smtp,
                array('auth' => 'login',
                    'username' => $username,
                    'password' => $password,
                    'ssl' => "ssl",
                    'port' => $port));
            Zend_Mail::setDefaultTransport($config);
            $mail = new Zend_Mail ('UTF-8');
            $mail->setBodyHtml($content['body']);// Email body
            $mail->setFrom($content['sender'],$content['nameSender']);// Sender (Email, Name)
            $mail->addTo(isset($content['recipient']) ? $content['recipient'] : '', isset($content['nameRecipient']) ? $content['nameRecipient'] : '');// Recipient (Email, Name)
            $mail->addCc($content['sender'],$content['nameSender']);// CC to // Reply to Sender
            $mail->setSubject($content['subject']); // Subject Email

            try {
                $flag = $mail->send($config);
            }
            catch(Zend_Exception $e) {
                Base_Helper_Log::getInstance()->log(PHP_EOL . date('H:i:s :::: ') . ' [MAIL] ' . $e->getMessage());
                $flag = false;
            }
            if($flag){
                return true;
            }else{
                return false;
            }
    }
}