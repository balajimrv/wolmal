<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Paymentreq.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Model_Paymentreq extends Core_Model_Item_Abstract {

  protected $_searchTriggers = false;
  protected $_modifiedTriggers = false;
  /**
   * @var Engine_Payment_Plugin_Abstract
   */
  protected $_plugin;

  /**
   * Get the payment plugin
   *
   * @return Engine_Payment_Plugin_Abstract
   */
  public function getPlugin() {
    // if (null === $this->_plugin) {
      
      $class = "Sitestoreproduct_Plugin_Gateway_Admin_PayPal";
      $paymentMethod = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.paymentmethod', 'paypal');
      if(Engine_Api::_()->hasModuleBootstrap('sitegateway') && $paymentMethod != 'paypal') {
        $class = "Sitegateway_Plugin_Gateway_".ucfirst($paymentMethod);
      }    
      
      Engine_Loader::loadClass($class);
      $plugin = new $class($this);

      if (!($plugin instanceof Engine_Payment_Plugin_Abstract)) {
        throw new Engine_Exception(sprintf('Payment plugin "%1$s" must ' .
                        'implement Engine_Payment_Plugin_Abstract', $class));
      }
      $this->_plugin = $plugin;
    // }
   return $this->_plugin;
   
  }

  /**
   * Get the payment gateway
   * 
   * @return Engine_Payment_Gateway
   */
  public function getGateway() {
    return $this->getPlugin()->getGateway();
  }

  /**
   * Get the payment service api
   * 
   * @return Zend_Service_Abstract
   */
  public function getService() {
    return $this->getPlugin()->getService();
  }

}