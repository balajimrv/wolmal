<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitestore_Widget_SitemobileCustomManageordersController extends Engine_Content_Widget_Abstract {

    public function indexAction() {
        try {
            if (!Engine_Api::_()->core()->hasSubject()) {
                return $this->setNoRender();
            }

            $isPaymentToSiteEnable = true;
            $isAdminDrivenStore = Engine_Api::_()->getApi('settings', 'core')->getSetting('is.sitestore.admin.driven', 0);
            if (empty($isAdminDrivenStore)) {
                $isPaymentToSiteEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.payment.for.orders', 0);
            }
            $this->view->isPaymentToSiteEnable = $isPaymentToSiteEnable;
            $this->view->isDownPaymentEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereservation.downpayment', 0);

            // STORE ID
            $this->view->store_id = $store_id = $this->_getParam('store_id', null);

            $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
            $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'view');
            if (empty($isManageAdmin)) {
                return $this->setNoRender();
            }

            $this->view->canEdit = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');

            //SEND TAB TO TPL FILE
            $this->view->tab_selected_id = $this->_getParam('tab');
            $this->view->call_same_action = $this->_getParam('call_same_action', 0);

            $params = array();
            $params['store_id'] = $store_id;
            $params['page'] = $this->_getParam('page', 1);
            $params['limit'] = 8;

            $isSearch = $this->_getParam('search', null);

            if (!empty($isSearch)) {
                $params['search'] = 1;
                $this->view->newOrderStatus = $params['order_status'] = $this->_getParam('status');
            }

            if (isset($_POST['search'])) {
                $params['search'] = 1;
                $params['order_id'] = $_POST['order_id'];
                $params['username'] = $_POST['username'];
                $params['billing_name'] = $_POST['billing_name'];
                $params['shipping_name'] = $_POST['shipping_name'];
                $params['creation_date_start'] = $_POST['creation_date_start'];
                $params['creation_date_end'] = $_POST['creation_date_end'];
                $params['order_min_amount'] = $_POST['order_min_amount'];
                $params['order_max_amount'] = $_POST['order_max_amount'];
                $params['commission_min_amount'] = $_POST['commission_min_amount'];
                $params['commission_max_amount'] = $_POST['commission_max_amount'];
                $params['delivery_time'] = $_POST['delivery_time'];
                $params['order_status'] = $_POST['order_status'];
                $params['downpayment'] = $_POST['downpayment'];
            }

            //MAKE PAGINATOR
            $this->view->paginator = Engine_Api::_()->getDbtable('orders', 'sitestoreproduct')->getOrdersPaginator($params);
            $this->view->total_item = $this->view->paginator->getTotalItemCount();
        } catch (Exception $e) {
            // var_dump($e);die;
            throw $e;
        }
    }

    public function _getParam($key, $default) {
        $param = parent::_getParam($key);
        if (empty($param)) {
            $param = Zend_Controller_Front::getInstance()->getRequest()->getParam($key, $default);
        }

        return $param;
    }
}
