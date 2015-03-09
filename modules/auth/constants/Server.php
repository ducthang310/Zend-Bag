<?php

class Auth_Constant_Server extends Base_Php_Overloader
{

    private static $_instance = NULL;
    const STAFF_TYPE    = 0;
    const CUSTOMER_TYPE = 1;
    const COURIER_TYPE  = 2;
    const GUEST_TYPE  = 3;
    //role admin
    const COURIER = 1;
    const CUSTOMER = 2;
    const LOCAL_AREA_ADMIN = 3;
    const LOCAL_AREA_STAFF = 4;
    const INTERNATIONAL_ADMIN = 5;
    const INTERNATIONAL_STAFF = 6;
    const SUPER_ADMIN = 7;

    private static $_role = array(
        '6' => array(
            'label' => 'INTERNATIONAL STAFF',
            'value' => 6,
        ),
        '5' => array(
            'label' => 'INTERNATIONAL ADMIN',
            'value' => 5,
        ),
        '4' => array(
            'label' => 'LOCAL AREA STAFF',
            'value' => 4
        ),
        '3' => array(
            'label' => 'LOCAL AREA ADMIN',
            'value' => 3,
        ),
    );
    private static $_role_super_admin = array(
        self::SUPER_ADMIN => array(
            'label' => 'SUPER ADMIN',
            'value' => self::SUPER_ADMIN,
        ),
    );
    public static function getSuperAdmin($key){
        return self::$_role_super_admin[$key]['label'];
    }
    public static function getLabelRoleByKey($key){
        return self::$_role[$key]['label'];
    }
    public static function getRole($role){
        $list = self::$_role;
        switch(true){
            case $role == self::SUPER_ADMIN :
                return $list;
                break;
            case $role == self::INTERNATIONAL_ADMIN :
                $array = array(4 => 4, 6 => 6 , 3 => 3);
                return array_intersect_key($list, $array);
                break;
            case $role == self::LOCAL_AREA_ADMIN:
                return $array = array(self::LOCAL_AREA_STAFF => $list[self::LOCAL_AREA_STAFF]);
                break;
        }
    }
    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
}