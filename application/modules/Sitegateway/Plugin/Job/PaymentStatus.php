<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Encode.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegateway_Plugin_Job_PaymentStatus extends Core_Plugin_Job_Abstract {

    protected function _execute() {
        // Get job and params
        $job = $this->getJob();
        set_time_limit(0);
        // No video id?
        if (!($payout_id = $this->getParam('payout_id'))) {
            $this->_setState('failed', 'No payout identity provided.');
            $this->_setWasIdle();
            return;
        }
        if (!($resource_type = $this->getParam('resource_type'))) {
            $this->_setState('failed', 'No resource type provided.');
            $this->_setWasIdle();
            return;
        }
        if (!($resource_id = $this->getParam('resource_id'))) {
            $this->_setState('failed', 'No resource id provided.');
            $this->_setWasIdle();
            return;
        }

        // Get video object
        $resource = Engine_Api::_()->getItem($resource_type, $resource_id);
        if (!$resource) {
            $this->_setState('failed', 'Resource is missing.');
            $this->_setWasIdle();
            return;
        }
        $transaction = Engine_Api::_()->sitegateway()->getTransaction($resource_type, $resource_id);
        // Process
        try {
            $this->_process($resource,$transaction, $payout_id);
            $this->_setIsComplete(true);
        } catch (Exception $e) {
            $this->_setState('failed', 'Exception: ' . $e->getMessage());
        }
    }

    public function _process($resource,$transaction, $payout_id) {
        $sitegatewayApi = Engine_Api::_()->sitegateway();
        $adminGateway = $sitegatewayApi->getAdminPaymentGateway('Sitegateway_Plugin_Gateway_MangoPay');
        
        $payoutDetail = $adminGateway->getService()->viewPayout($payout_id);
        if ($payoutDetail->Status == 'CREATED') {
            //CREATED
            if (isset($resource->payout_status)) {
                $resource->payout_status = $payout_status = 'pending';
            }
        } else if ($payoutDetail->Status == 'SUCCEEDED') {
            //SUCCEEDED
            if (isset($resource->payout_status)) {
                $resource->payout_status = $payout_status = 'success';
            }
        } else {
            //FAILED
            if (isset($resource->payout_status)) {
                $resource->payout_status = $payout_status = 'failed';
            }
        }
        $transaction->payout_status = $payout_status;
        $transaction->save();
        $resource->save();
    }

}
