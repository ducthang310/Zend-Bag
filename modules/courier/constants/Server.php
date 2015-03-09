<?php

class Courier_Constant_Server extends Base_Php_Overloader
{

    private static $_instance = NULL;
    const HEAD_OFFICE_APPROVED = 1;
    const HEAD_OFFICE_NOT_APPROVED = 0;
    const UNAPPROVED = 0;
    const APPROVED = 1;
    const REJECTED = 2;
    const CAN_ASSIGN = 1;
    const CAN_NOT_ASSIGN = 0;
    const IS_COMPANY = 1;
    const ALL_AREA = 1;
    const SELECT_AREA = 0;

    static $_STATUS = array(
        self::APPROVED => 'APPROVED',
        self::UNAPPROVED => 'UNAPPROVED',
        self::REJECTED => 'REJECTED',
    );

    static $_AREA = array(
        'ALL_AREA' => array(
            'label' => 'ALL AREAS',
            'value' => self::ALL_AREA
        ),
        'SELECT_AREA' => array(
            'label' => 'THESE SUBURBS ONLY',
            'value' => self::SELECT_AREA
        ),
    );

    static $_CAN_ASSIGN = array(
        self::CAN_ASSIGN => 'YES',
        self::CAN_NOT_ASSIGN => 'NO'
    );

    static $_DOCUMENT = array(
        'photo_approved' => "PHOTO ID",
        'utility_bill_approved' => 'UTILITY BILL',
        'bank_statement_approved' => 'BANK STATEMENT'
    );

    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
}