<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Usergateway.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Model_Usergateway extends Core_Model_Item_Abstract {

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
    if (null === $this->_plugin) {
      $class = $this->plugin; // "Sitestoreproduct_Plugin_Gateway_PayPal";

      $classArray = explode('_', $class);

      if($classArray[0] === 'Payment') {
        $classArray[0] = 'Sitestoreproduct';
      }
        
      $classChange = implode('_', $classArray);
      $class = $classChange;     

      Engine_Loader::loadClass($class);
      $plugin = new $class($this);

      if (!($plugin instanceof Engine_Payment_Plugin_Abstract)) {
        throw new Engine_Exception(sprintf('Payment plugin "%1$s" must ' .
                        'implement Engine_Payment_Plugin_Abstract', $class));
      }
      $this->_plugin = $plugin;
    }
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