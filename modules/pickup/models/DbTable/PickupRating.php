<?php
class Pickup_Model_DbTable_PickupRating extends Base_Model_DbTable_Backend
{
    protected $_name = 'pickup_rating';
    protected static $_instance = NULL;

    public function __construct($config = array())
    {
        parent::__construct($config);
    }
    public static function getInstance() {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function getRate($pickup_id){
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select();
        $select->from('rating', '*')
            ->joinLeft(
                array('pickup_rating' => 'pickup_rating'),
                'rating.id = pickup_rating.rating_id and pickup_rating.pickup_id='.$pickup_id,
                array('rating_customer_value'=>'rating_customer_value',
                    'rating_courier_value'=>'rating_courier_value')
            )
            ->joinLeft(
                array('pickup' => 'pickup'),
                'pickup.id=pickup_rating.pickup_id ',
                array('pickup_id'=>'pickup.id')
            );
        $pickups = $db->fetchRow($select);
//        echo '<br>'.$select->__toString().'<br>';
//        echo count($pickups).'::::::end';
        return $pickups;
    }
}