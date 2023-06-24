<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    FormValidators.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_Api_Siteapi_FormValidators extends Siteapi_Api_Validators {
    /*
     * form validators for album of day
     */

    public function albumOfDayValidators() {
        $formValidators['startdate'] = array(
            'required' => true,
            'allowEmpty' => false,
        );

        $formValidators['enddate'] = array(
            'required' => true,
            'allowEmpty' => false,
        );

        return $formValidators;
    }

    /*
     *  billing fields form validators 
     */

    public function billingAddressValidators() {
        $billingFields = array('f_name_billing', 'phone_billing', 'address_billing', 'country_billing', 'city_billing', 'zip_billing');

        $formValidators = array();

        foreach ($billingFields as $row => $value)
            $formValidators[$value] = array('required' => true, 'allowEmpty' => false);

        return $formValidators;
    }

    /*
     *  shipping fields form validators 
     */

    public function shippingAddressValidators() {
        $shippingFields = array('f_name_shipping', 'phone_shipping', 'address_shipping', 'country_shipping', 'city_shipping', 'zip_shipping');

        $formValidators = array();

        foreach ($shippingFields as $row => $value)
            $formValidators[$value] = array('required' => true, 'allowEmpty' => false);

        return $formValidators;
    }

    public function tellaFriendFormValidators()
    {
        $formValidators = array();
        
        $formValidators['sender_name'] = array(
            'required' => true,
            'allowEmpty' => false,
        );
        
        $formValidators['sender_email'] = array(
            'required' => true,
            'allowEmpty' => false,
        );
        
        $formValidators['receiver_emails'] = array(
            'required' => true,
            'allowEmpty' => false,
        );
        
        $formValidators['message'] = array(
            'required' => true,
            'allowEmpty' => false,
        );
        
        return $formValidators;
        
    }

}
