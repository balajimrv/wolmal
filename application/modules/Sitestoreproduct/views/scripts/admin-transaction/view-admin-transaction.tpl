<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: view-admin-transaction.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<h2><?php echo $this->translate('Transaction Details'); ?></h2>

<dl class="payment_transaction_details">
  <dd>
    <?php echo $this->translate('Transaction ID') ?>
  </dd>
  <dt>
    <?php echo $this->translate('%s', $this->locale()->toNumber($this->transaction_id)) ?>
  </dt>
  
  <dd>
    <?php echo $this->translate('Store Id') ?>
  </dd>
  <dt>
   <?php echo $this->translate('%s', $this->htmlLink($this->sitestore->getHref(), $this->locale()->toNumber($this->store_id), array('target' => '_blank'))) ?>
  </dt>

  <dd>
    <?php echo $this->translate('Store Name') ?>
  </dd>
  <dt>
   <?php echo $this->translate('%s', $this->htmlLink($this->sitestore->getHref(), $this->sitestore->getTitle(), array('target' => '_blank'))) ?>
  </dt>
  
  <dd>
    <?php echo $this->translate('Owner Name') ?>
  </dd>
  <dt>
   <?php echo $this->translate('%s', $this->htmlLink($this->userObj->getHref(), $this->userObj->getTitle(), array('target' => '_blank'))) ?>
  </dt>

  <dd>
    <?php echo $this->translate('Payment Gateway') ?>
  </dd>
  <dt>
    <?php if( $this->payment_gateway ): ?>
      <?php echo $this->translate('%s', $this->payment_gateway) ?>
    <?php else: ?>
      <i><?php echo $this->translate('Unknown Gateway') ?></i>
    <?php endif; ?>
  </dt>

  <dd>
    <?php echo $this->translate('Payment Type') ?>
  </dd>
  <dt>
    <?php echo $this->translate(ucfirst($this->payment_type)) ?>
  </dt>

  <dd>
    <?php echo $this->translate('Payment State') ?>
  </dd>
  <dt>
    <?php echo $this->translate(ucfirst($this->payment_state)) ?>
  </dt>

  <dd>
    <?php echo $this->translate('Response Amount') ?>
  </dd>
  <dt>
    <?php echo $this->payment_amount; ?>
  </dt>

  <dd>
    <?php echo $this->translate('Gateway Transaction ID') ?>
  </dd>
  <dt>
    <?php if( !empty($this->gateway_transaction_id) ): ?>
      <?php echo $this->htmlLink(array(
          'route' => 'admin_default',
          'module' => 'sitestoreproduct',
          'controller' => 'payment',
          'action' => 'detail-transaction',
          'transaction_id' => $this->transaction_id,
        ), $this->gateway_transaction_id, array(
          'target' => '_blank',
      )) 
      ?>
    <?php else: ?>
      -
    <?php endif; ?>
  </dt>
  
  <dd>
    <?php echo $this->translate('Date') ?>
  </dd>
  <dt>
    <?php echo gmdate('M d,Y, g:i A',strtotime($this->date)) ?>
  </dt>

</dl>

<div class='buttons'>
  <button type='button' name="cancel" onclick="javascript:parent.Smoothbox.close();"><?php echo $this->translate("Close") ?></button>
</div>