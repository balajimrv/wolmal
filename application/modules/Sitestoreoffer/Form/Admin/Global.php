<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreoffer
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Global.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreoffer_Form_Admin_Global extends Engine_Form {

  public function init() {
    $this
            ->setTitle('General Settings')
            ->setDescription('These settings affect all members in your community.');

    $this->addElement('Text', 'sitestorecoupon_max_limit', array(
        'label' => 'Coupon Creation Limit',
        'description' => 'Maximum limit to create coupons by store admins. (Note: Please Enter 0 for unlimited coupon creation).',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorecoupon.max.limit', 0),
    ));

    $this->addElement('Radio', 'sitestorecoupon_coupon_url', array(
        'label' => 'Coupons URL',
        'description' => 'Do you want to show the Coupons URL on coupon create form?',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorecoupon.coupon.url', 0),
    ));

    $this->addElement('Radio', 'sitestorecoupon_approve', array(
        'label' => 'Coupon Approval Moderation',
        'description' => 'Do you want new Coupons to be automatically approved?',
        'multiOptions' => array(
            1 => 'Yes, automatically approve Coupon.',
            0 => 'No, site admin approval will be required for all Coupon.'
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorecoupon.approve', 1),
    ));

    $this->addElement('Text', 'sitestoreoffer_manifestUrl', array(
        'label' => 'Store Coupons URL alternate text for "store-coupons"',
        'allowEmpty' => false,
        'required' => true,
        'description' => 'Please enter the text below which you want to display in place of "storecoupon" in the URLs of this plugin.',
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreoffer.manifestUrl', "store-coupons"),
    ));
    
    if(Engine_Api::_()->sitestoreproduct()->isSitestorereservationModuleExist()){
    $this->addElement('Radio', 'sitestorereservation_coupon', array(
        'label' => 'Enable Downpayment for coupons',
        'description' => 'Do you want to enable downpayment for coupons.',
        'multiOptions' => array(
            1 => 'Yes.',
            0 => 'No.'
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereservation.coupon', 0),
    ));
    }
    
    $this->addElement('Radio', 'sitestorecoupon_isprivate', array(
        'label' => 'Make Coupons as Private / Public',
        'description' => 'Do you want to use coupon as private?',
        'multiOptions' => array(
            1 => 'Yes, Use as Private.',
            0 => 'No, Use as Public.'
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorecoupon.isprivate', 0),
    ));

    $this->addElement('Radio', 'sitestoreoffer_offer_show_menu', array(
        'label' => 'Coupons Link',
        'description' => 'Do you want to show the Coupons link on Stores Navigation Menu? (You might want to show this if Coupons from Stores are an important component on your website. This link will lead to a widgetized store listing all Store Coupons, with a search form for Store Coupons and multiple widgets.',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreoffer.offer.show.menu', 1),
    ));


    // Order of store offer store
    $this->addElement('Radio', 'sitestoreoffer_order', array(
        'label' => 'Default Ordering in Store Coupons listing',
        'description' => 'Select the default ordering of coupons in Store Coupons listing. (This widgetized store will list all Store Coupons. Sponsored coupons are coupons created by paid Stores.)',
        'multiOptions' => array(
            1 => 'All coupons in descending order of creation.',
            2 => 'All coupons in alphabetical order.',
            3 => 'Hot coupons followed by others in descending order of creation.',
            4 => 'Sponsored coupons followed by others in descending order of creation.(If you have enabled packages.)',
            5 => 'Hot coupons followed by sponsored coupons followed by others in descending order of creation.',
            6 => 'Sponsored coupons followed by hot coupons followed by others in descending order of creation.(If you have enabled packages.)',
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreoffer.order', 1),
    ));


    $this->addElement('Text', 'sitestoreoffer_truncation_limit', array(
        'label' => 'Title Truncation Limit',
        'description' => 'What maximum limit should be applied to the number of characters in the titles of items in the widgets? (Enter a number between 1 and 999. Titles having more characters than this limit will be truncated. Complete titles will be shown on mouseover.)',
        'required' => true,
        'maxlength' => 3,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreoffer.truncation.limit', 13),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

    $this->addElement('Button', 'submit', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'ignore' => true
    ));
  }

}

?>