<?php

class Base_Helper_Message extends Base_Php_Overloader
{

    private static $_instance = NULL;

    public function __construct() {
        parent::__construct(self::$listMessage);
    }

    public function showVerticalLine($personal,$div = null){
        if(!$personal || count($personal) < 1){
            return false;
        }
        foreach ($personal as $key => $val) {
            $element = '*[name="' . $key . '"]';
            $parent = 'parent()';
            if ($val) {
                if ($key == "preferred_delivery_suburb_ids"):
                    $element = 'input.delivery_suburb';
                    $parent = 'parent().parent().parent()';
                endif;
                if ($key == "preferred_pickup_suburb_ids"):
                    $element = 'input.pickup_suburb';
                    $parent = 'parent().parent().parent()';
                endif;
                if($key == "expiry_date"):
                    $element = '*[name="expiry_month"], *[name="expiry_year"]';
                    $parent = 'parent().parent()';
                endif;
                ?>
                var divPersonal = $('<?php echo $div . ' ' . $element ?>');
                divPersonal.<?php echo $parent ?>.find('.pseudo').addClass('optional').removeClass('required');
            <?php
            } else {
                if ($key == 'alternative_email') { ?>
                    var divPersonal = $('<?php echo $div . ' ' . $element ?>');
                    divPersonal.<?php echo $parent ?>.find('.pseudo').removeClass('optional required');
                <?php
                }
            }
        }
    }
    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    static $listMessage = array(
        'registration' => array(
            'courier_company' => array(
                'company_name' => array(
                    'message' => 'This can only be letters, numbers, spaces, apostrophes, and hyphens.',
                    'instruction' => 'Your Company Name is needed as the legal operating name of the business we are engaging with. This can only be letters, numbers, spaces, apostrophes, and hyphens.',
                ),
                'abn' => array(
                    'message' => 'This is your Company Number or Business Number.',
                    'instruction' => 'Company Number or Business Number',
                ),
                'contact_firstname' => array(
                    'message' => 'This can only be letters, spaces, apostrophes, and hyphens.',
                    'instruction' => 'Your contact first name is needed to help us, and the DLIVR community, know who you are without compromising your personal security. This can only be letters, spaces, apostrophes, and hyphens.',
                ),

                'contact_lastname' => array(
                    'message' => 'This can only be letters, spaces, apostrophes, and hyphens.',
                    'instruction' => 'Your contact last name is needed to help us know who you are. Your last name will never be made public. This can only be letters, spaces, apostrophes, and hyphens.',
                ),

                'email' => array(
                    'taken' => 'We\'re sorry. That email is already in our system.',
                    'message' => 'Oops. Looks like there is a problem with your email, please check it and try again.',
                    'instruction' => 'We use your email and mobile for communication purposes with you. Details are in our privacy policy.',
                ),

                'app_email' => array(
                    'taken' => 'We\'re sorry. That email is already in our system.',
                    'message' => 'Oops. Looks like there is a problem with your email, please check it and try again.',
                    'instruction' => 'We use your email and mobile for communication purposes with you. Details are in our privacy policy.',
                ),
                'app_password' => array(
                    'message' => 'Password must be at least 8 characters and contain uppercase and lowercase letters and numbers only. (accept spaces and "-").',
                    'instruction' => 'App Password to secure. Details are in our privacy policy.',
                ),
                'alternative_email' => array(
                    'taken' => 'We\'re sorry. That email is already in our system.',
                    'message' => 'Oops. Looks like there is a problem with your email, please check it and try again.',
                    'instruction' => 'This is the backup email if your regular one doesn\'t  work for any reason.',
                ),

                'general_number' => array(
                    'message' => 'Oops. Looks like there is a problem with your number, please check it and try again.',
                    'instruction' => 'We use your number for communication purposes with you. Details are in our privacy policy.',
                ),

                'contact_number' => array(
                    'message' => 'Oops. Looks like there is a problem with your number, please check it and try again.',
                    'instruction' => 'We use your number for communication purposes with you. Details are in our privacy policy.',
                ),

                'mobile' => array(
                    'message' => 'Oops. Looks like there is a problem with your mobile number, please check it and try again.',
                    'instruction' => 'We use your mobile number for communication purposes with you. Details are in our privacy policy.',
                ),

                'fax' => array(
                    'message' => 'Oops. Looks like there is a problem with your fax number, please check it and try again.',
                    'instruction' => 'We use your fax number for communication purposes with you. Details are in our privacy policy.',
                ),

                'image' => array(
                    'message' => 'Oops. Looks like there is a problem with your Profile Picture, please check it and try again.',
                    'instruction' => 'We need to know how to reach you and to verify your details. Please upload your Profile Picture',
                ),

                'address' => array(
                    'message' => 'This can only be letters, numbers, slashes, spaces, apostrophes, and hyphens.',
                    'instruction' => 'We need to know how to reach you and to verify your business address. This can only be letters, numbers, slashes, spaces, apostrophes, and hyphens.',
                ),
                'suburb' => array(
                    'message' => 'This can only be letters, numbers, slashes, spaces, apostrophes, and hyphens.',
                    'instruction' => 'We need to know how to reach you and to verify your business address. This can only be letters, numbers, slashes, spaces, apostrophes, and hyphens.',
                ),
                'courier_state' => array(
                    'message' => 'You must select a State or Province',
                    'instruction' => 'We need to know how to reach you and to verify your business address. You must select a State or Province.',
                ),
                'country' => array(
                    'message' => 'You must select a Country.',
                    'instruction' => 'We need to know how to reach you and to verify your business address. You must select a Country.',
                ),
                'postcode' => array(
                    'message' => 'Oops. Looks like there is a problem with Postal or Zip code, please check it and try again.',
                    'instruction' => 'We need to know how to reach you and to verify your business address. You must enter a postal or zip code.',
                ),
                'bank_institution' => array(
                    'message' => 'Oops. Looks like there is a problem with Banking Detail, please check it and try again.',
                    'instruction' => 'We need to know how to reach you and to verify your Banking Detail.',
                ),
                'bank_bsb' => array(
                    'message' => 'Oops. Looks like there is a problem with Bank BSB, please check it and try again.',
                    'instruction' => 'We need to know how to reach you and to verify your Bank BSB.',
                ),
                'bank_account' => array(
                    'message' => 'Oops. Looks like there is a problem with Account NO, please check it and try again.',
                    'instruction' => 'We need to know how to reach you and to verify your Account NO.',
                ),
                'photo' => array(
                    'message' => 'Oops. Looks like there is a problem with your file, please check it and try again.',
                    'instruction' => 'We need to know how to reach you and to verify your Photo ID.',
                ),
                'utility_bill' => array(
                    'message' => 'Oops. Looks like there is a problem with your file, please check it and try again.',
                    'instruction' => 'We need to know how to reach you and to verify your Utility Bill.',
                ),
                'bank_statement' => array(
                    'message' => 'Oops. Looks like there is a problem with your file, please check it and try again.',
                    'instruction' => 'We need to know how to reach you and to verify your Bank Statement.',
                ),
            ),


            'courier_individual' => array(
                'firstname' => array(
                    'message' => 'This can only be letters, spaces, apostrophes, and hyphens.',
                    'instruction' => 'Your first name is needed to help us, and the DLIVR community, know who you are without compromising your personal security. This can only be letters, spaces, apostrophes, and hyphens.',
                ),
                'lastname' => array(
                    'message' => 'This can only be letters, spaces, apostrophes, and hyphens.',
                    'instruction' => 'Your last name is needed to help us know who you are. Your last name will never be made public. This can only be letters, spaces, apostrophes, and hyphens.',
                ),
                'contact_firstname' => array(
                    'message' => 'This can only be letters, spaces, apostrophes, and hyphens.',
                    'instruction' => 'Your contact first name is needed to help us, and the DLIVR community, know who you are without compromising your personal security. This can only be letters, spaces, apostrophes, and hyphens.',
                ),

                'contact_lastname' => array(
                    'message' => 'This can only be letters, spaces, apostrophes, and hyphens.',
                    'instruction' => 'Your contact last name is needed to help us know who you are. Your last name will never be made public. This can only be letters, spaces, apostrophes, and hyphens.',
                ),
                'email' => array(
                    'taken' => 'We\'re sorry. That email is already in our system.',
                    'message' => 'Oops. Looks like there is a problem with your email, please check it and try again.',
                    'instruction' => 'We use your email and mobile for communication purposes with you. Details are in our privacy policy.',
                ),

                'app_email' => array(
                    'taken' => 'We\'re sorry. That email is already in our system.',
                    'message' => 'Oops. Looks like there is a problem with your email, please check it and try again.',
                    'instruction' => 'We use your email and mobile for communication purposes with you. Details are in our privacy policy.',
                ),
                'app_password' => array(
                    'message' => 'Password must be at least 8 characters and contain uppercase and lowercase letters and numbers only. (accept spaces and "-").',
                    'instruction' => 'App Password to secure. Details are in our privacy policy.',
                ),
                'alternative_email' => array(
                    'message' => 'Oops. Looks like there is a problem with your email, please check it and try again.',
                    'taken' => 'We\'re sorry. That email is already in our system.',
                    'instruction' => 'This is the backup email if your regular one doesn\'t  work for any reason.',
                ),

                'general_number' => array(
                    'message' => 'Oops. Looks like there is a problem with your number, please check it and try again.',
                    'instruction' => 'We use your number for communication purposes with you. Details are in our privacy policy.',
                ),

                'contact_number' => array(
                    'message' => 'Oops. Looks like there is a problem with your number, please check it and try again.',
                    'instruction' => 'We use your number for communication purposes with you. Details are in our privacy policy.',
                ),

                'mobile' => array(
                    'message' => 'Oops. Looks like there is a problem with your mobile number, please check it and try again.',
                    'instruction' => 'We use your mobile number for communication purposes with you. Details are in our privacy policy.',
                ),

                'fax' => array(
                    'message' => 'Oops. Looks like there is a problem with your fax number, please check it and try again.',
                    'instruction' => 'We use your fax number for communication purposes with you. Details are in our privacy policy.',
                ),

                'image' => array(
                    'message' => 'Oops. Looks like there is a problem with your Profile Picture, please check it and try again.',
                    'instruction' => 'We need to know how to reach you and to verify your details. Please upload your Profile Picture',
                ),
                'address' => array(
                    'message' => 'This can only be letters, numbers, slashes, spaces, apostrophes, and hyphens.',
                    'instruction' => 'We need to know how to reach you and to verify your details. This can only be letters, numbers, slashes, spaces, apostrophes, and hyphens.',
                ),
                'suburb' => array(
                    'message' => 'This can only be letters, numbers, slashes, spaces, apostrophes, and hyphens.',
                    'instruction' => 'We need to know how to reach you and to verify your details. This can only be letters, numbers, slashes, spaces, apostrophes, and hyphens.',
                ),
                'courier_state' => array(
                    'message' => 'You must select a State or Province',
                    'instruction' => 'We need to know how to reach you and to verify your details. You must select a State or Province.',
                ),
                'country' => array(
                    'message' => 'You must select a Country.',
                    'instruction' => 'We need to know how to reach you and to verify your details. You must select a Country.',
                ),
                'postcode' => array(
                    'message' => 'Oops. Looks like there is a problem with Postal or Zip code, please check it and try again.',
                    'instruction' => 'We need to know how to reach you and to verify your details. You must enter a postal or zip code.',
                ),
                'bank_institution' => array(
                    'message' => 'Oops. Looks like there is a problem with Banking Detail, please check it and try again.',
                    'instruction' => 'We need to know how to reach you and to verify your Banking Detail.',
                ),
                'bank_bsb' => array(
                    'message' => 'Oops. Looks like there is a problem with Bank BSB, please check it and try again.',
                    'instruction' => 'We need to know how to reach you and to verify your Bank BSB.',
                ),
                'bank_account' => array(
                    'message' => 'Oops. Looks like there is a problem with Account NO, please check it and try again.',
                    'instruction' => 'We need to know how to reach you and to verify your Account NO.',
                ),
                'photo' => array(
                    'message' => 'Oops. Looks like there is a problem with your file, please check it and try again.',
                    'instruction' => 'We need to know how to reach you and to verify your Photo ID.',
                ),
                'utility_bill' => array(
                    'message' => 'Oops. Looks like there is a problem with your file, please check it and try again.',
                    'instruction' => 'We need to know how to reach you and to verify your Utility Bill.',
                ),
                'bank_statement' => array(
                    'message' => 'Oops. Looks like there is a problem with your file, please check it and try again.',
                    'instruction' => 'We need to know how to reach you and to verify your Bank Statement.',
                ),
                'preferred_region' => array(
                    'message' => 'You must select a Region.',
                    'instruction' => 'We need to know how to reach you and to verify your region. You must select a Region.',
                ),
                'preferred_pickup_suburb_ids' => array(
                    'message' => 'You must select a Pickup Suburb.',
                    'instruction' => 'We need to know how to reach you and to verify your pickup suburb. You must select a Pickup Suburb.',
                ),
                'preferred_delivery_suburb_ids' => array(
                    'message' => 'You must select a Delivery Suburb.',
                    'instruction' => 'We need to know how to reach you and to verify your delivery suburb. You must select a Delivery Suburb.',
                ),
            ),
            'courier_preferences' => array(
                'preferred_region' => array(
                    'message' => 'You must select a Region.',
                    'instruction' => 'We need to know how to reach you and to verify your region. You must select a Region.',
                ),
                'preferred_pickup_suburb_ids' => array(
                    'message' => 'You must select a Pickup Suburb.',
                    'instruction' => 'We need to know how to reach you and to verify your pickup suburb. You must select a Pickup Suburb.',
                ),
                'preferred_delivery_suburb_ids' => array(
                    'message' => 'You must select a Delivery Suburb.',
                    'instruction' => 'We need to know how to reach you and to verify your delivery suburb. You must select a Delivery Suburb.',
                ),
            ),

            'customer' => array(
                'company_name' => array(
                    'message' => 'This can only be letters, numbers, spaces, apostrophes, and hyphens.',
                    'instruction' => 'Your Company Name is needed as the legal operating name of the business we are engaging with. This can only be letters, numbers, spaces, apostrophes, and hyphens.',
                ),
                'abn' => array(
                    'message' => 'This is your Company Number or Business Number.',
                    'instruction' => 'Company Number or Business Number',
                ),
                'firstname' => array(
                    'message' => 'This can only be letters, spaces, apostrophes, and hyphens.',
                    'instruction' => 'Your first name is needed to help us, and our drivers, know who you are without compromising your personal security. This can only be letters, spaces, apostrophes, and hyphens.',
                ),
                'lastname' => array(
                    'message' => 'This can only be letters, spaces, apostrophes, and hyphens.',
                    'instruction' => 'Your last name is needed to help us know who you are. Your last name will never be made public. This can only be letters, spaces, apostrophes, and hyphens.',
                ),
                'contact_firstname' => array(
                    'message' => 'This can only be letters, spaces, apostrophes, and hyphens.',
                    'instruction' => 'Your first name is needed to help us, and the DLIVR community, know who you are without compromising your personal security. This can only be letters, spaces, apostrophes, and hyphens.',
                ),

                'contact_lastname' => array(
                    'message' => 'This can only be letters, spaces, apostrophes, and hyphens.',
                    'instruction' => 'Your last name is needed to help us know who you are. Your last name will never be made public. This can only be letters, spaces, apostrophes, and hyphens.',
                ),
                'email' => array(
                    'taken' => 'We\'re sorry. That email is already in our system.',
                    'message' => 'Oops. Looks like there is a problem with your email, please check it and try again.',
                    'instruction' => 'We use your email and mobile for communication purposes with you. Details are in our privacy policy.',
                ),
                'alternative_email' => array(
                    'message' => 'Oops. Looks like there is a problem with your email, please check it and try again.',
                    'taken' => 'We\'re sorry. That email is already in our system.',
                    'instruction' => 'This is the backup email if your regular one doesn\'t  work for any reason.',
                ),
                'app_email' => array(
                    'message' => 'Oops. Looks like there is a problem with your email, please check it and try again.',
                    'taken' => 'We\'re sorry. That email is already in our system.',
                    'instruction' => 'This is the account username to login. Details are in our privacy policy.',
                ),
                'app_password' => array(
                    'message' => 'Password must be at least 8 characters and contain uppercase and lowercase letters and numbers only. (accept spaces and "-").',
                    'instruction' => 'App Password to secure. Details are in our privacy policy.',
                ),
                'alternate_email' => array(
                    'message' => 'Oops. Looks like there is a problem with your email, please check it and try again.',
                    'taken' => 'We\'re sorry. That email is already in our system.',
                    'instruction' => 'This is the backup email if your regular one doesn\'t  work for any reason.',
                ),
                'general_number' => array(
                    'message' => 'Oops. Looks like there is a problem with your number, please check it and try again.',
                    'instruction' => 'We use your number for communication purposes with you. Details are in our privacy policy.',
                ),

                'contact_number' => array(
                    'message' => 'Oops. Looks like there is a problem with your number, please check it and try again.',
                    'instruction' => 'We use your number for communication purposes with you. Details are in our privacy policy.',
                ),

                'mobile' => array(
                    'message' => 'Oops. Looks like there is a problem with your mobile number, please check it and try again.',
                    'instruction' => 'We use your mobile number for communication purposes with you. Details are in our privacy policy.',
                ),

                'fax' => array(
                    'message' => 'Oops. Looks like there is a problem with your fax number, please check it and try again.',
                    'instruction' => 'We use your fax number for communication purposes with you. Details are in our privacy policy.',
                ),

                'image' => array(
                    'message' => 'Oops. Looks like there is a problem with your Profile Picture, please check it and try again.',
                    'instruction' => 'We need to know how to reach you and to verify your details. Please upload your Profile Picture',
                ),
                'address' => array(
                    'message' => 'This can only be letters, numbers, slashes, spaces, apostrophes, and hyphens.',
                    'instruction' => 'We need to know how to reach you and to verify your details. This can only be letters, numbers, slashes, spaces, apostrophes, and hyphens.',
                ),
                'suburb' => array(
                    'message' => 'This can only be letters, numbers, slashes, spaces, apostrophes, and hyphens.',
                    'instruction' => 'We need to know how to reach you should any delivery issues arise. This can only be letters, numbers, slashes, spaces, apostrophes, and hyphens.',
                ),
                'customer_state' => array(
                    'message' => 'You must select a State or Province',
                    'instruction' => 'We need to know how to reach you should any delivery issues arise. You must select a State or Province',
                ),
                'country' => array(
                    'message' => 'You must select a Country.',
                    'instruction' => 'We need to know how to reach you should any delivery issues arise. You must select a Country.',
                ),
                'postcode' => array(
                    'message' => 'Oops. Looks like there is a problem with Postal or Zip code, please check it and try again.',
                    'instruction' => 'We need to know how to reach you and to verify your details. You must enter a postal or zip code.',
                ),
                'card_number' => array(
                    'message' => 'Oops. Looks like there is a problem with Card Number, please check it and try again.',
                    'instruction' => 'We need to know how to reach you and to verify your Card Number.',
                ),
                'card_type' => array(
                    'message' => 'Oops. Looks like there is a problem with Card Type, please check it and try again.',
                    'instruction' => 'We need to know how to reach you and to verify your Card Type.',
                ),
                'card_name' => array(
                    'message' => 'Oops. Looks like there is a problem with Name on Card, please check it and try again.',
                    'instruction' => 'We need to know how to reach you and to verify your Name on Card.',
                ),
                'expiry_month' => array(
                    'message' => 'You must select Expiry Month.',
                    'instruction' => 'We need to know how to reach you and to verify your Expiry Date.',
                ),
                'expiry_year' => array(
                    'message' => 'You must select Expiry Year.',
                    'instruction' => 'We need to know how to reach you and to verify your Expiry Date.',
                ),
                'ccv' => array(
                    'message' => 'Oops. Looks like there is a problem with your CCV, please check it and try again.',
                    'instruction' => 'We need to know how to reach you and to verify your Card.',
                ),

                'reference' => array(
                    'message' => 'Oops. Looks like there is a problem with Reference, please check it and try again.',
                    'instruction' => 'We need to know how to reach you and to verify your Reference. You must enter a Reference.',
                ),
                'preferred_region' => array(
                    'message' => 'You must select a Region.',
                    'instruction' => 'We need to know how to reach you and to verify your region. You must select a Region.',
                ),
                'preferred_pickup_address' => array(
                    'message' => 'Oops. Looks like there is a problem with Pickup Address, please check it and try again.',
                    'instruction' => 'We need to know how to reach you and to verify your Pickup Detail. You must enter a Pickup Address.',
                ),

                'preferred_pickup_suburb' => array(
                    'message' => 'Oops. Looks like there is a problem with Pickup Suburb, please check it and try again.',
                    'instruction' => 'We need to know how to reach you and to verify your Pickup Detail. You must enter a Pickup Suburb.',
                ),
                'preferred_pickup_state' => array(
                    'message' => 'You must select a Pickup State.',
                    'instruction' => 'We need to know how to reach you and to verify your Pickup Detail. You must select a Pickup State.',
                ),
                'preferred_pickup_postcode' => array(
                    'message' => 'Oops. Looks like there is a problem with Pickup Postcode, please check it and try again.',
                    'instruction' => 'We need to know how to reach you and to verify your Pickup Detail. You must enter a Pickup Postcode.',
                ),

                'preferred_delivery_address' => array(
                    'message' => 'Oops. Looks like there is a problem with Delivery Address, please check it and try again.',
                    'instruction' => 'We need to know how to reach you and to verify your Delivery Detail. You must enter a Delivery Address.',
                ),
                'preferred_delivery_suburb' => array(
                    'message' => 'Oops. Looks like there is a problem with Delivery Suburb, please check it and try again.',
                    'instruction' => 'We need to know how to reach you and to verify your Delivery Detail. You must enter a Delivery Suburb.',
                ),
                'preferred_delivery_state' => array(
                    'message' => 'You must select a Delivery State.',
                    'instruction' => 'We need to know how to reach you and to verify your Delivery Detail. You must select a Delivery State.',
                ),
                'preferred_delivery_postcode' => array(
                    'message' => 'Oops. Looks like there is a problem with Delivery Postcode, please check it and try again.',
                    'instruction' => 'We need to know how to reach you and to verify your Delivery Detail. You must enter a Delivery Postcode.',
                ),
            ),
            'customer_preferences' => array(
                'reference' => array(
                    'message' => 'Oops. Looks like there is a problem with Reference, please check it and try again.',
                    'instruction' => 'We need to know how to reach you and to verify your Reference. You must enter a Reference.',
                ),
                'preferred_region' => array(
                    'message' => 'You must select a Region.',
                    'instruction' => 'We need to know how to reach you and to verify your region. You must select a Region.',
                ),
                'preferred_pickup_address' => array(
                    'message' => 'Oops. Looks like there is a problem with Pickup Address, please check it and try again.',
                    'instruction' => 'We need to know how to reach you and to verify your Pickup Detail. You must enter a Pickup Address.',
                ),

                'preferred_pickup_suburb' => array(
                    'message' => 'You must enter a Pickup Suburb.',
                    'instruction' => 'We need to know how to reach you and to verify your Pickup Detail. You must enter a Pickup Suburb.',
                ),
                'preferred_pickup_state' => array(
                    'message' => 'You must select a Pickup State.',
                    'instruction' => 'We need to know how to reach you and to verify your Pickup Detail. You must select a Pickup State.',
                ),
                'preferred_pickup_postcode' => array(
                    'message' => 'Oops. Looks like there is a problem with Pickup Postcode, please check it and try again.',
                    'instruction' => 'We need to know how to reach you and to verify your Pickup Detail. You must enter a Pickup Postcode.',
                ),

                'preferred_delivery_address' => array(
                    'message' => 'Oops. Looks like there is a problem with Delivery Address, please check it and try again.',
                    'instruction' => 'We need to know how to reach you and to verify your Delivery Detail. You must enter a Delivery Address.',
                ),
                'preferred_delivery_suburb' => array(
                    'message' => 'Oops. Looks like there is a problem with Delivery Suburb, please check it and try again.',
                    'instruction' => 'We need to know how to reach you and to verify your Delivery Detail. You must enter a Delivery Suburb.',
                ),
                'preferred_delivery_state' => array(
                    'message' => 'You must select a Delivery State.',
                    'instruction' => 'We need to know how to reach you and to verify your Delivery Detail. You must select a Delivery State.',
                ),
                'preferred_delivery_postcode' => array(
                    'message' => 'You must enter a Delivery Postcode.',
                    'instruction' => 'We need to know how to reach you and to verify your Delivery Detail. You must enter a Delivery Postcode.',
                ),
            ),
        ),
        'log_in' => array(
            'customer_courier' => array(
                'email' => array(
                    'message' => 'Oops. Looks like there is a problem with your email, please check it and try again.',
                    'taken' => 'We\'re sorry. That email is not in our system.',
                    'instruction' => '',
                ),
                'password' => array(
                    'message' => 'Oops. That password does not match the email you have entered. Please try again.',
                    'instruction' => '',
                ),
            )
        ),
        'request_delivery' => array(
            'customer' => array(
                'pickup_location' => array(
                    'message' => 'You can also just type the address.',
                    'instruction' => 'Enter your location using the search field or drag and drop the pin on the map.',
                ),
                'delivery_location' => array(
                    'message' => 'You can also just type the address.',
                    'instruction' => 'Enter your location using the search field or drag and drop the pin on the map.',
                ),
                'preferred_pickup_time_and_date' => array(
                    'message' => '',
                    'instruction' => 'Let us know the best time and date to pick up your item. We\'ll confirm for you as soon as a driver has been allocated.',
                )),
        ),
        'edit_profile' => array(
            'courier' => array(
                'preferred_pickup_time_and_date' => array(
                    'message' => '',
                    'instruction' => 'Let us know the best time and date to pick up your item. We\'ll confirm for you as soon as a driver has been allocated.',
                ),
                'company_name' => array(
                    'message' => 'This can only be letters, numbers, spaces, apostrophes, and hyphens.',
                    'instruction' => 'Your Company Name is needed as the legal operating name of the business we are engaging with. This can only be letters, numbers, spaces, apostrophes, and hyphens.',
                ),
                'abn' => array(
                    'message' => 'This is your Company Number or Business Number.',
                    'instruction' => 'Company Number or Business Number',
                ),
                'firstname' => array(
                    'message' => 'This can only be letters, spaces, apostrophes, and hyphens.',
                    'instruction' => 'Your first name is needed to help us, and the DLIVR community, know who you are without compromising your personal security. This can only be letters, spaces, apostrophes, and hyphens.',
                ),
                'lastname' => array(
                    'message' => 'This can only be letters, spaces, apostrophes, and hyphens.',
                    'instruction' => 'Your last name is needed to help us know who you are. Your last name will never be made public. This can only be letters, spaces, apostrophes, and hyphens.',
                ),
                'contact_firstname' => array(
                    'message' => 'This can only be letters, spaces, apostrophes, and hyphens.',
                    'instruction' => 'Your contact first name is needed to help us, and the DLIVR community, know who you are without compromising your personal security. This can only be letters, spaces, apostrophes, and hyphens.',
                ),
                'contact_lastname' => array(
                    'message' => 'This can only be letters, spaces, apostrophes, and hyphens.',
                    'instruction' => 'Your contact last name is needed to help us know who you are. Your contact last name will never be made public. This can only be letters, spaces, apostrophes, and hyphens.',
                ),
                'email' => array(
                    'taken' => 'We\'re sorry. That email is already in our system.',
                    'message' => 'Oops. Looks like there is a problem with your email, please check it and try again.',
                    'instruction' => 'We use your email and mobile for communication purposes with you. Details are in our privacy policy.',
                ),

                'app_email' => array(
                    'taken' => 'We\'re sorry. That email is already in our system.',
                    'message' => 'Oops. Looks like there is a problem with your email, please check it and try again.',
                    'instruction' => 'We use your email and mobile for communication purposes with you. Details are in our privacy policy.',
                ),

                'alternative_email' => array(
                    'taken' => 'We\'re sorry. That email is already in our system.',
                    'message' => 'Oops. Looks like there is a problem with your email, please check it and try again.',
                    'instruction' => 'This is the backup email if your regular one doesn\'t  work for any reason.',
                ),

                'general_number' => array(
                    'message' => 'Oops. Looks like there is a problem with your number, please check it and try again.',
                    'instruction' => 'We use your number for communication purposes with you. Details are in our privacy policy.',
                ),

                'contact_number' => array(
                    'message' => 'Oops. Looks like there is a problem with your number, please check it and try again.',
                    'instruction' => 'We use your number for communication purposes with you. Details are in our privacy policy.',
                ),

                'mobile' => array(
                    'message' => 'Oops. Looks like there is a problem with your mobile number, please check it and try again.',
                    'instruction' => 'We use your mobile number for communication purposes with you. Details are in our privacy policy.',
                ),

                'fax' => array(
                    'message' => 'Oops. Looks like there is a problem with your fax number, please check it and try again.',
                    'instruction' => 'We use your fax number for communication purposes with you. Details are in our privacy policy.',
                ),

                'image' => array(
                    'message' => 'Oops. Looks like there is a problem with your Profile Picture, please check it and try again.',
                    'instruction' => 'We need to know how to reach you and to verify your details. Please upload your Profile Picture',
                ),
                'address' => array(
                    'message' => 'This can only be letters, numbers, slashes, spaces, apostrophes, and hyphens.',
                    'instruction' => 'We need to know how to reach you and to verify your details. This can only be letters, numbers, slashes, spaces, apostrophes, and hyphens.',
                ),
                'suburb' => array(
                    'message' => 'This can only be letters, numbers, slashes, spaces, apostrophes, and hyphens.',
                    'instruction' => 'We need to know how to reach you and to verify your details. This can only be letters, numbers, slashes, spaces, apostrophes, and hyphens.',
                ),
                'courier_state' => array(
                    'message' => 'You must select a State or Province',
                    'instruction' => 'We need to know how to reach you and to verify your details. You must select a State or Province.',
                ),
                'country' => array(
                    'message' => 'You must select a Country.',
                    'instruction' => 'We need to know how to reach you and to verify your details. You must select a Country.',
                ),
                'postcode' => array(
                    'message' => 'Oops. Looks like there is a problem with Postal or Zip code, please check it and try again.',
                    'instruction' => 'We need to know how to reach you and to verify your details. You must enter a postal or zip code.',
                ),
                'bank_institution' => array(
                    'message' => 'Oops. Looks like there is a problem with Banking Detail, please check it and try again.',
                    'instruction' => 'We need to know how to reach you and to verify your Banking Detail.',
                ),
                'bank_bsb' => array(
                    'message' => 'Oops. Looks like there is a problem with Bank BSB, please check it and try again.',
                    'instruction' => 'We need to know how to reach you and to verify your Bank BSB.',
                ),
                'bank_account' => array(
                    'message' => 'Oops. Looks like there is a problem with Account NO., please check it and try again.',
                    'instruction' => 'We need to know how to reach you and to verify your Account NO.',
                ),

                'utility_bill' => array(
                    'message' => 'Oops. Looks like there is a problem with your file, please check it and try again.',
                    'instruction' => 'We need to know how to reach you and to verify your Utility Bill.',
                ),
                'photo' => array(
                    'message' => 'Oops. Looks like there is a problem with your file, please check it and try again.',
                    'instruction' => 'We need to know how to reach you and to verify your Photo.',
                ),
                'bank_statement' => array(
                    'message' => 'Oops. Looks like there is a problem with your file, please check it and try again.',
                    'instruction' => 'We need to know how to reach you and to verify your Bank Statement.',
                ),
                'preferred_region' => array(
                    'message' => 'You must select a Region.',
                    'instruction' => 'We need to know how to reach you and to verify your region. You must select a Region.',
                ),
                'preferred_pickup_suburb_ids' => array(
                    'message' => 'You must select a Pickup Suburb.',
                    'instruction' => 'We need to know how to reach you and to verify your pickup suburb. You must select a Pickup Suburb.',
                ),
                'preferred_delivery_suburb_ids' => array(
                    'message' => 'You must select a Delivery Suburb.',
                    'instruction' => 'We need to know how to reach you and to verify your delivery suburb. You must select a Delivery Suburb.',
                ),
            ),
            'courier_preferences' => array(
                'preferred_region' => array(
                    'message' => 'You must select a Region.',
                    'instruction' => 'We need to know how to reach you and to verify your region. You must select a Region.',
                ),
                'preferred_pickup_suburb_ids' => array(
                    'message' => 'You must select a Pickup Suburb.',
                    'instruction' => 'We need to know how to reach you and to verify your pickup suburb. You must select a Pickup Suburb.',
                ),
                'preferred_delivery_suburb_ids' => array(
                    'message' => 'You must select a Delivery Suburb.',
                    'instruction' => 'We need to know how to reach you and to verify your delivery suburb. You must select a Delivery Suburb.',
                ),
            ),


            'customer' => array(
                'company_name' => array(
                    'message' => 'This can only be letters, numbers, spaces, apostrophes, and hyphens.',
                    'instruction' => 'Your Company Name is needed as the legal operating name of the business we are engaging with. This can only be letters, numbers, spaces, apostrophes, and hyphens.',
                ),
                'abn' => array(
                    'message' => 'This is your Company Number or Business Number.',
                    'instruction' => 'Company Number or Business Number',
                ),
                'firstname' => array(
                    'message' => 'This can only be letters, spaces, apostrophes, and hyphens.',
                    'instruction' => 'Your first name is needed to help us, and our drivers, know who you are without compromising your personal security. This can only be letters, spaces, apostrophes, and hyphens.',
                ),
                'lastname' => array(
                    'message' => 'This can only be letters, spaces, apostrophes, and hyphens.',
                    'instruction' => 'Your last name is needed to help us know who you are. Your last name will never be made public. This can only be letters, spaces, apostrophes, and hyphens.',
                ),
                'contact_firstname' => array(
                    'message' => 'This can only be letters, spaces, apostrophes, and hyphens.',
                    'instruction' => 'Your first name is needed to help us, and the DLIVR community, know who you are without compromising your personal security. This can only be letters, spaces, apostrophes, and hyphens.',
                ),

                'contact_lastname' => array(
                    'message' => 'This can only be letters, spaces, apostrophes, and hyphens.',
                    'instruction' => 'Your last name is needed to help us know who you are. Your last name will never be made public. This can only be letters, spaces, apostrophes, and hyphens.',
                ),
                'email' => array(
                    'message' => 'Oops. Looks like there is a problem with your email, please check it and try again.',
                    'taken' => 'We\'re sorry. That email is already in our system.',
                    'instruction' => 'We use your email and mobile for communication purposes with you. Details are in our privacy policy.',
                ),
                'alternative_email' => array(
                    'message' => 'Oops. Looks like there is a problem with your email, please check it and try again.',
                    'taken' => 'We\'re sorry. That email is already in our system.',
                    'instruction' => 'This is the backup email if your regular one doesn\'t  work for any reason.',
                ),
                'app_email' => array(
                    'message' => 'Oops. Looks like there is a problem with your email, please check it and try again.',
                    'taken' => 'We\'re sorry. That email is already in our system.',
                    'instruction' => 'This is the account username to login. Details are in our privacy policy.',
                ),
                'app_password' => array(
                    'message' => 'Password must be at least 8 characters and contain uppercase and lowercase letters and numbers only. (accept spaces and "-").',
                    'instruction' => 'App Password to secure. Details are in our privacy policy.',
                ),
                'alternate_email' => array(
                    'message' => 'Oops. Looks like there is a problem with your email, please check it and try again.',
                    'taken' => 'We\'re sorry. That email is already in our system.',
                    'instruction' => 'This is the backup email if your regular one doesn\'t  work for any reason.',
                ),
                'general_number' => array(
                    'message' => 'Oops. Looks like there is a problem with your number, please check it and try again.',
                    'instruction' => 'We use your number for communication purposes with you. Details are in our privacy policy.',
                ),

                'contact_number' => array(
                    'message' => 'Oops. Looks like there is a problem with your number, please check it and try again.',
                    'instruction' => 'We use your number for communication purposes with you. Details are in our privacy policy.',
                ),

                'mobile' => array(
                    'message' => 'Oops. Looks like there is a problem with your mobile number, please check it and try again.',
                    'instruction' => 'We use your mobile number for communication purposes with you. Details are in our privacy policy.',
                ),

                'fax' => array(
                    'message' => 'Oops. Looks like there is a problem with your fax number, please check it and try again.',
                    'instruction' => 'We use your fax number for communication purposes with you. Details are in our privacy policy.',
                ),

                'image' => array(
                    'message' => 'Oops. Looks like there is a problem with your Profile Picture, please check it and try again.',
                    'instruction' => 'We need to know how to reach you and to verify your details. Please upload your Profile Picture',
                ),
                'address' => array(
                    'message' => 'This can only be letters, numbers, slashes, spaces, apostrophes, and hyphens.',
                    'instruction' => 'We need to know how to reach you and to verify your details. This can only be letters, numbers, slashes, spaces, apostrophes, and hyphens.',
                ),
                'suburb' => array(
                    'message' => 'This can only be letters, numbers, slashes, spaces, apostrophes, and hyphens.',
                    'instruction' => 'We need to know how to reach you should any delivery issues arise. This can only be letters, numbers, slashes, spaces, apostrophes, and hyphens.',
                ),
                'customer_state' => array(
                    'message' => 'You must select a State or Province',
                    'instruction' => 'We need to know how to reach you should any delivery issues arise. You must select a State or Province',
                ),
                'country' => array(
                    'message' => 'You must select a Country.',
                    'instruction' => 'We need to know how to reach you should any delivery issues arise. You must select a Country.',
                ),
                'postcode' => array(
                    'message' => 'Oops. Looks like there is a problem with Postal or Zip code, please check it and try again.',
                    'instruction' => 'We need to know how to reach you and to verify your details. You must enter a postal or zip code.',
                ),
                'card_number' => array(
                    'message' => 'Oops. Looks like there is a problem with your Card Number, please check it and try again.',
                    'instruction' => 'We need to know how to reach you and to verify your Card Number.',
                ),
                'card_type' => array(
                    'message' => 'You must select Card Type',
                    'instruction' => 'We need to know how to reach you and to verify your Card Type.',
                ),
                'card_name' => array(
                    'message' => 'Oops. Looks like there is a problem with your Name on Card, please check it and try again.',
                    'instruction' => 'We need to know how to reach you and to verify your Name on Card.',
                ),
                'expiry_month' => array(
                    'message' => 'You must select Expiry Month.',
                    'instruction' => 'We need to know how to reach you and to verify your Expiry Date.',
                ),
                'expiry_year' => array(
                    'message' => 'You must select Expiry Year.',
                    'instruction' => 'We need to know how to reach you and to verify your Expiry Date.',
                ),
                'ccv' => array(
                    'message' => 'Oops. Looks like there is a problem with your CCV, please check it and try again.',
                    'instruction' => 'We need to know how to reach you and to verify your Card.',
                ),
            ),
            'customer_preferences' => array(
                'reference' => array(
                    'message' => 'You must enter a Reference.',
                    'instruction' => 'We need to know how to reach you and to verify your Reference. You must enter a Reference.',
                ),
                'preferred_region' => array(
                    'message' => 'You must select a Region.',
                    'instruction' => 'We need to know how to reach you and to verify your region. You must select a Region.',
                ),
                'preferred_pickup_address' => array(
                    'message' => 'You must enter a Pickup Address.',
                    'instruction' => 'We need to know how to reach you and to verify your Pickup Detail. You must enter a Pickup Address.',
                ),

                'preferred_pickup_suburb' => array(
                    'message' => 'You must enter a Pickup Suburb.',
                    'instruction' => 'We need to know how to reach you and to verify your Pickup Detail. You must enter a Pickup Suburb.',
                ),
                'preferred_pickup_state' => array(
                    'message' => 'You must select a Pickup State.',
                    'instruction' => 'We need to know how to reach you and to verify your Pickup Detail. You must select a Pickup State.',
                ),
                'preferred_pickup_postcode' => array(
                    'message' => 'You must enter a Pickup Postcode.',
                    'instruction' => 'We need to know how to reach you and to verify your Pickup Detail. You must enter a Pickup Postcode.',
                ),

                'preferred_delivery_address' => array(
                    'message' => 'You must enter a Delivery Address.',
                    'instruction' => 'We need to know how to reach you and to verify your Delivery Detail. You must enter a Delivery Address.',
                ),
                'preferred_delivery_suburb' => array(
                    'message' => 'You must select Delivery Suburb.',
                    'instruction' => 'We need to know how to reach you and to verify your Delivery Detail. You must enter a Delivery Suburb.',
                ),
                'preferred_delivery_state' => array(
                    'message' => 'You must select a Delivery State.',
                    'instruction' => 'We need to know how to reach you and to verify your Delivery Detail. You must select a Delivery State.',
                ),
                'preferred_delivery_postcode' => array(
                    'message' => 'You must enter a Delivery Postcode.',
                    'instruction' => 'We need to know how to reach you and to verify your Delivery Detail. You must enter a Delivery Postcode.',
                ),
            ),

        ),
        'all_the_same' => array(
            'customer_courier' => array(
                'all_same_registration_process' => array(
                    'message' => 'All the same as the registration process',
                    'instruction' => 'Let us know the best time and date to pick up your item. We\'ll confirm for you as soon as a driver has been allocated.',
                ))
        ),
    );
}
