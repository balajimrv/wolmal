<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Storebill.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Model_Storebill extends Core_Model_Item_Abstract {

  protected $_searchTriggers = false;
  protected $_modifiedTriggers = false;
  
  protected $_statusChanged;
  
  /**
   * @var Engine_Payment_Plugin_Abstract
   */
  protected $_plugin;

  public function didStatusChange() {
    return (bool) $this->_statusChanged;
  }

  public function onPaymentSuccess() {
    $this->_statusChanged = false;

    if (in_array($this->status, array('initial', 'trial', 'pending', 'active', 'overdue', 'expired'))) {

      //$this->setActive(true);

      // Change status
      if ($this->status != 'active') {
        $this->status = 'active';
        $this->_statusChanged = true;
      }
    }
    $this->save();
    return $this;
  }

  public function onPaymentPending() {
    $this->_statusChanged = false;
    if (in_array($this->status, array('initial', 'trial', 'pending', 'active', 'overdue', 'expired'))) {
      // Change status
      if ($this->status != 'pending') {
        $this->status = 'pending';
        $this->_statusChanged = true;
      }
    }
    $this->save();
    return $this;
  }

  public function onPaymentFailure() {
    $this->_statusChanged = false;
    if (in_array($this->status, array('initial', 'trial', 'pending', 'active', 'overdue', 'expired'))) {
      // Change status
      if ($this->status != 'overdue') {
        $this->status = 'overdue';
        $this->_statusChanged = true;
      }

      $session = new Zend_Session_Namespace('Store_Bill_Payment_Sitestoreproduct');
      $session->unsetAll();
    }
    $this->save();
    return $this;
  }

  public function onExpiration() {
    $this->_statusChanged = false;
    if (in_array($this->status, array('initial', 'trial', 'pending', 'active', 'expired'))) {
      // Change status
      if ($this->status != 'expired') {
        $this->status = 'expired';
//        $this->approved = 0;
//        $this->enable = 0;
////        $this->status = 3;
//        $this->order_status = 3;
        $this->_statusChanged = true;
      }
    }
    $this->save();
    return $this;
  }

  public function onRefund() {
    $this->_statusChanged = false;
    if (in_array($this->status, array('initial', 'trial', 'pending', 'active', 'refunded'))) {
      // Change status
      if ($this->status != 'refunded') {
        $this->status = 'refunded';
        $this->_statusChanged = true;
      }
    }
    $this->save();
    return $this;
  }
}