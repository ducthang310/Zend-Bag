<?php

class Configuration_Constant_Server extends Base_Php_Overloader
{

    private static $_instance = NULL;

    private static $_country = array(
        'AUSTRALIAN' => "Australian",
    );

    private static $_keys = array(
        'BASE_PRICE' => array(
            'label' => 'BASE PRICE',
            'value' => 1,
            'unit'  => '$'
        ),
        'CREDIT_CARD_FEE' =>  array(
            'label' => 'CREDIT CARD FEES',
            'value' => 2,
            'unit'  => '%'
        ),
        'DELIVERY_SIGNATURE_FEE' =>  array(
            'label' => 'REQUEST SIGNATURE ON DELIVERY',
            'value' => 3,
            'unit'  => '$'
        ),
        'DELIVERY_INSURE_FEE' =>  array(
            'label' => 'INSURE DELIVERY',
            'value' => 4,
            'unit'  => '$'
        ),
        'CANCEL_FEE' =>  array(
            'label' => 'CANCELLATION PERCENTAGE',
            'value' => 5,
            'unit'  => '%'
        ),
        'DELIVERY_TIME' =>  array(
            'label' => 'DELIVERY TIME',
            'value' => 6,
            'unit' => 'hours'
        ),
        'AWAITING_TIME' =>  array(
            'label' => 'AWAITING',
            'value' => 7,
            'unit' => 'hours'
        ),
        'ASSIGNED_TIME' =>  array(
            'label' => 'ASSIGNED',
            'value' => 8,
            'unit' => 'hours'
        ),
        'PICKED_UP_TIME' =>  array(
            'label' => 'PICKED UP',
            'value' => 9,
            'unit' => 'hours'
        ),
        'DELIVERED_TIME' =>  array(
            'label' => 'DELIVERED',
            'value' => 10,
            'unit' => 'hours'
        ),
        'CANCELED_TIME' =>  array(
            'label' => 'CANCELED',
            'value' => 11,
            'unit' => 'hours'
        ),
        'ACCEPTED_TIME' =>  array(
            'label' => 'ACCEPTED',
            'value' => 12,
            'unit' => 'hours'
        ),
        'RATING_MAX_POINT' =>  array(
            'label' => 'RATING MAX POINT',
            'value' => 13,
            'unit' => 'point'
        ),
        'REPORTED_EMAIL' =>  array(
            'label' => 'REPORTED EMAIL HQ',
            'value' => 14,
            'unit' => 'email'
        ),
        'PAYPAL_ID' =>  array(
            'label' => 'PAYPAL ID',
            'value' => 15,
            'unit' => 'text'
        ),
        'PAYPAL_SECRET' =>  array(
            'label' => 'PAYPAL SECRET',
            'value' => 16,
            'unit' => 'text'
        ),
        'CURRENCY' =>  array(
            'label' => 'CURRENCY',
            'value' => 17,
            'unit' => 'text'
        ),
    );

    private static $_status = array(
        '0' => array(
            'label' => 'ENABLE'
        ),
        '1' => array(
            'label' => 'DISABLE'
        ),
    );

    public static function getStatusLabel($key){
        return self::$_status[$key]['label'];
    }

    public static function getKeyValue($key) {
            return self::$_keys[$key]['value'];
    }

    public static function getKeyLabel($value) {
        $label = null;
        $value = intval($value);
        foreach(self::$_keys as $keys => $values){
            if($value == $values['value']){
                $label = $values['label'];
            }
        }
        return $label;
    }
    public static function getNextConfigKeyByStatus($currentPickupStatus = Pickup_Constant_Server::AWAITING) {
        switch(true) {
            case Pickup_Constant_Server::AWAITING == $currentPickupStatus:
                return self::getKeyValue('AWAITING_TIME');
                break;
            case Pickup_Constant_Server::ASSIGNED == $currentPickupStatus:
                return self::getKeyValue('ASSIGNED_TIME');
                break;
            case Pickup_Constant_Server::PICKED_UP == $currentPickupStatus:
                return self::getKeyValue('PICKED_UP_TIME');
                break;
            case Pickup_Constant_Server::DELIVERED == $currentPickupStatus:
                return self::getKeyValue('DELIVERED_TIME');
                break;
            case Pickup_Constant_Server::ACCEPTED == $currentPickupStatus:
                return self::getKeyValue('ACCEPTED_TIME');
                break;
            default:
                return self::getKeyValue('AWAITING_TIME');
                break;
        }
    }


    public static function getKeyUnit($value) {
        $unit = null;
        $value = intval($value);
        foreach(self::$_keys as $keys => $values){
            if($value == $values['value']){
                $unit = $values['unit'];
            }
        }
        return $unit;
    }

    public static function getCountry(){
        return self::$_country;
    }

    public static function getAllKeyConfig(){
        return self::$_keys;
    }

    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

}