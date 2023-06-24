<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Orderdownpayment.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Model_Orderdownpayment extends Core_Model_Item_Abstract {

  // Properties
  protected $_statusChanged;
  protected $_searchTriggers = false;

  public function didStatusChange() {
    return (bool) $this->_statusChanged;
  }

  public function onPaymentSuccess() {
    $this->_statusChanged = false;

    if (in_array($this->payment_status, array('initial', 'trial', 'pending', 'active', 'overdue', 'expired'))) {

      //$this->setActive(true);

      // Change status
      if ($this->payment_status != 'active') {
        $this->payment_status = 'active';
        $this->_statusChanged = true;
      }
    }
    $this->save();
    return $this;
  }

  public function onPaymentPending() {
    $this->_statusChanged = false;
    if (in_array($this->payment_status, array('initial', 'trial', 'pending', 'active', 'overdue', 'expired'))) {
      // Change status
      if ($this->payment_status != 'pending') {
        $this->payment_status = 'pending';
        $this->_statusChanged = true;
      }
    }
    $this->save();
    return $this;
  }

  public function onPaymentFailure() {
    $this->_statusChanged = false;
    if (in_array($this->payment_status, array('initial', 'trial', 'pending', 'active', 'overdue', 'expired'))) {
      // Change status
      if ($this->payment_status != 'overdue') {
        $this->payment_status = 'overdue';
        $this->_statusChanged = true;
      }

      $session = new Zend_Session_Namespace('Payment_Sitestoreproduct');
      $session->unsetAll();
    }
    $this->save();
    return $this;
  }

  public function onExpiration() {
    $this->_statusChanged = false;
    if (in_array($this->payment_status, array('initial', 'trial', 'pending', 'active', 'expired'))) {
      // Change status
      if ($this->payment_status != 'expired') {
        $this->payment_status = 'expired';
        $this->approved = 0;
        $this->enable = 0;
//        $this->status = 3;
        $this->order_status = 3;
        $this->_statusChanged = true;
      }
    }
    $this->save();
    return $this;
  }

  public function onRefund() {
    $this->_statusChanged = false;
    if (in_array($this->payment_status, array('initial', 'trial', 'pending', 'active', 'refunded'))) {
      // Change status
      if ($this->payment_status != 'refunded') {
        $this->payment_status = 'refunded';
        $this->_statusChanged = true;
      }
    }
    $this->save();
    return $this;
  }
}