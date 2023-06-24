<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Order.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Model_Order extends Core_Model_Item_Abstract {

    // Properties
    protected $_parent_type = 'store_order';
    protected $_parent_is_owner = true;
    protected $_package;
    protected $_statusChanged;
    protected $_product;
    protected $_searchTriggers = false;

    public function getOwner($recurseType = null) {
        $owner_id = $this->buyer_id;
        if (!empty($owner_id))
            return Engine_Api::_()->getItem('user', $owner_id);
    }

    public function getRichContent() {

        $getIdentity = $this->getIdentity();

        // CHECK IF ANY PRODUCT IS DELETED FROM SITE OR NOT AND IS THAT PRODUCT IS THE SINGLE PRODUCT OF THIS ORDER
        $orderProductIds = Engine_Api::_()->getDbtable('orderProducts', 'sitestoreproduct')->getOrderProductIds($getIdentity);
        $productCount = array();

        foreach ($orderProductIds as $orderProductId) {
            $isProductExist = Engine_Api::_()->getDbtable('products', 'sitestoreproduct')->isProductExist($orderProductId['product_id']);
            if (!empty($isProductExist)) {
                $productCount[] = $orderProductId['product_id'];
            }
        }

        // IF ORDER PRODUCT IS DELETED FROM THE SITE, THEN DELETE THE ACTIVITY FEED ENTRY ALSO
        if (empty($productCount)) {
            $activityTable = Engine_Api::_()->getDbtable('actions', 'activity');

            $select = $activityTable->select()
                    ->from($activityTable->info('name'))
                    ->where('type =?', 'sitestoreproduct_order_place')
                    ->where('object_type =?', 'sitestoreproduct_order')
                    ->where('object_id =?', $getIdentity);

            $orderActivity = $activityTable->fetchAll($select);

            foreach ($orderActivity as $activity) {
                Engine_Api::_()->getItem('activity_action', $activity->action_id)->delete();
            }
            return;
        }

        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $str = $view->orderRichContent($getIdentity);
        return $str;
    }

    /**
     * Gets a proxy object for the comment handler
     *
     * @return Engine_ProxyObject
     * */
    public function comments() {
        return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('comments', 'core'));
    }

    /**
     * Gets a proxy object for the like handler
     *
     * @return Engine_ProxyObject
     * */
    public function likes() {
        return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('likes', 'core'));
    }

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

    public function getProductParams() {
//    $string = strip_tags($this->desc);
//    $desc = Engine_String::strlen($string) > 250 ? Engine_String::substr($string, 0, (247)) . '...' : $string;
        $price = Engine_Api::_()->getDbtable('orders', 'sitestoreproduct')->getGrandTotal($this->parent_id);
        $desc = 'sitestoreproduct';
        return array(
            'title' => 'order', //$this->title,
            'description' => $desc,
            'price' => @round($price, 2),
            'extension_type' => 'sitestoreproduct_order',
            'extension_id' => $this->parent_id,
        );
    }

    public function getProduct() {
        if (null === $this->_product) {
            $productsTable = Engine_Api::_()->getDbtable('products', 'payment');
            $this->_product = $productsTable->fetchRow($productsTable->select()
                            ->where('extension_type = ?', 'sitestoreproduct_order')
                            ->where('extension_id = ?', $this->getIdentity())
                            ->limit(1));
            // Create a new product?
            if (!$this->_product) {
                $this->_product = $productsTable->createRow();
                $this->_product->setFromArray($this->getProductParams());
                $this->_product->save();
            }
        }

        return $this->_product;
    }

    public function getGatewayIdentity() {
        return $this->getProduct()->sku;
    }

    public function getGatewayParams() {
        $params = array();

        $order_ids = Engine_Api::_()->getDbtable('orders', 'sitestoreproduct')->getOrderIds($this->parent_id);
        $price = Engine_Api::_()->getDbtable('orders', 'sitestoreproduct')->getGrandTotal($this->parent_id);
        $siteTitle = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title', '');
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;

        $tempOrderCount = 1;
        $tempCount = @COUNT($order_ids);
        $orderTitle = '';

        foreach ($order_ids as $order_id) {
            if ($tempOrderCount != 1) {
                if ($tempCount == $tempOrderCount)
                    $orderTitle .= $view->translate(" SITESTOREPRODUCT_CHECKOUT_AND ");
                else
                    $orderTitle .= ', ';
            }
            $orderTitle .= '#' . $order_id['order_id'];
            $tempOrderCount++;
        }

        // General
        $params['name'] = $siteTitle . ' Order Nos ' . $orderTitle;
        $params['price'] = @round($price, 2);
//    $params['tax'] = @round(10, 2);
        $params['description'] = 'Orders ' . $orderTitle . ' on ' . $siteTitle;
        $params['vendor_product_id'] = $this->getGatewayIdentity();
        $params['tangible'] = false;
        // Non-recurring
        $params['recurring'] = false;

        return $params;
    }

    /**
     * Process ipn of order transaction
     *
     * @param Payment_Model_Order $order
     * @param Engine_Payment_Ipn $ipn
     */
    public function onPaymentIpn(Payment_Model_Order $order, Engine_Payment_Ipn $ipn) {
        $gateway = Engine_Api::_()->getItem('payment_gateway', $order->gateway_id);
        $gateway->getPlugin()->onUserOrderTransactionIpn($order, $ipn);
        return true;
    }

    public function payoutStatus() {

        $gateway = Engine_Api::_()->getItem('sitestoreproduct_usergateway', $this->gateway_id);
        if (!Engine_Api::_()->sitegateway()->isEscrowGateway($gateway->plugin))
            return false;
        if ($this->order_status != 5)
            return false;

        if ($this->gateway_type != 'escrow') {
            return false;
        }

        if (empty($this->payout_status) && empty($this->refund_status))
            return 'both';

        if ($this->payout_status == 'failed') {
            return 'payout';
        }
        if ($this->refund_status == 'failed') {
            return 'refund';
        }
        return false;
    }

}
