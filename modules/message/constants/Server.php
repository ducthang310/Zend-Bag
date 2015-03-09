<?php

class Message_Constant_Server extends Base_Php_Overloader
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
        'CANCELLATION_PERCENTAGE' => array(
            'label' => 'CANCELLATION PERCENTAGE',
            'value' => 2,
            'unit'  => '%'
        ),
        'CREDIT_CARD_FEE' =>  array(
            'label' => 'CREDIT CARD FEE',
            'value' => 3,
            'unit'  => '%'
        ),
        'DELIVERY_SIGNATURE_FEE' =>  array(
            'label' => 'DELIVERY SIGNATURE FEE',
            'value' => 4,
            'unit'  => '$'
        ),
        'DELIVERY_INSURE_FEE' =>  array(
            'label' => 'DELIVERY INSURE FEE',
            'value' => 5,
            'unit'  => '$'
        ),
        'ASAP_FEE' =>  array(
            'label' => 'ASAP FEE',
            'value' => 6,
            'unit'  => '$'
        ),
        'CANCEL_FEE' =>  array(
            'label' => 'CANCEL FEE',
            'value' => 7,
            'unit'  => '$'
        ),
        'DELIVERY_TIME' =>  array(
            'label' => 'DELIVERY TIME',
            'value' => 8,
            'unit' => 'hours'
        ),
        'AWAITING_TIME' =>  array(
            'label' => 'AWAITING TIME',
            'value' => 9,
            'unit' => 'hours'
        ),
        'ASSIGNED_TIME' =>  array(
            'label' => 'ASSIGNED TIME',
            'value' => 10,
            'unit' => 'hours'
        ),
        'PICKED_UP_TIME' =>  array(
            'label' => 'PICKED UP TIME',
            'value' => 11,
            'unit' => 'hours'
        ),
        'DELIVERED_TIME' =>  array(
            'label' => 'DELIVERED TIME',
            'value' => 12,
            'unit' => 'hours'
        ),
        'CANCELED_TIME' =>  array(
            'label' => 'CANCELED TIME',
            'value' => 13,
            'unit' => 'hours'
        ),
        'ACCEPTED_TIME' =>  array(
            'label' => 'ACCEPTED TIME',
            'value' => 14,
            'unit' => 'hours'
        ),
        'RATING_MAX_POINT' =>  array(
            'label' => 'RATING MAX POINT',
            'value' => 15,
            'unit' => 'point'
        ),
    );

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