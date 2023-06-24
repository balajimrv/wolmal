<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: GetOrderStatus.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_View_Helper_GetOrderStatus extends Zend_View_Helper_Abstract {

  /**
   * Assembles action string
   * 
   * @return string
   */
  public function getOrderStatus($order_status, $user_class = null, $admin_class = null)
  {
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $statusValues = array();
    switch($order_status)
    {
      case 0 :        
        $current_status = $view->translate('Approval Pending');
        if( !empty($user_class) ) 
        {
          $statusValues['class'] = 'seaocore_txt_light';
        }
				if( !empty($admin_class) ) 
        {
          $statusValues['class'] = 'seaocore_txt_light';
        }
        break;
      case 1 :
        $current_status = $view->translate('Payment Pending');
        if( !empty($user_class) ) 
        {
          $statusValues['class'] = 'seaocore_txt_light';
        }
				if( !empty($admin_class) ) 
        {
          $statusValues['class'] = 'seaocore_txt_light';
        }
        break;
      case 2 :
        $current_status = $view->translate('Processing');
        if( !empty($user_class) ) 
        {
          $statusValues['class'] = 'sitestoreproduct_order_status_processing';
        }
				if( !empty($admin_class) ) 
        {
          $statusValues['class'] = 'sitestoreproduct_order_status_processing';
        }
        break;
      case 3 :
        $current_status = $view->translate('On Hold');
        if( !empty($user_class) ) 
        {
          $statusValues['class'] = 'sitestoreproduct_order_status_on_hold';
        }
				if( !empty($admin_class) ) 
        {
          $statusValues['class'] = 'sitestoreproduct_order_status_on_hold';
        }
        break;
      case 4 :
        $current_status = $view->translate('Fraud');
        if( !empty($user_class) ) 
        {
          $statusValues['class'] = 'seaocore_txt_red';
        }
				if( !empty($admin_class) ) 
        {
          $statusValues['class'] = 'seaocore_txt_red';
        }
        break;
      case 5 :
        $current_status = $view->translate('Completed');
        if( !empty($user_class) ) 
        {
          $statusValues['class'] = 'sitestoreproduct_order_status_complete';
        }
				if( !empty($admin_class) ) 
        {
          $statusValues['class'] = 'sitestoreproduct_order_status_complete';
        }
        break;
      case 6 :
        $current_status = $view->translate('Canceled');
        if( !empty($user_class) ) 
        {
          $statusValues['class'] = 'seaocore_txt_red';
        }
				if( !empty($admin_class) ) 
        {
          $statusValues['class'] = 'seaocore_txt_red';
        }
        break;
      default :
        $current_status = $view->translate('No Order Status Found');
    }
    
    if( !empty($user_class) ) 
    {
      $statusValues['title'] = $current_status;
      return $statusValues;
    }
		
		if( !empty($admin_class) ) 
    {
      $statusValues['title'] = $current_status;
      return $statusValues;
    }
    
    return $current_status;
  }
}