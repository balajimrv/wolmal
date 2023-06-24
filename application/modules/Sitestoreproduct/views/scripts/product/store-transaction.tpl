<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: store-transaction.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>
   
<?php $paginationCount = count($this->paginator); ?>
<?php if(empty($this->call_same_action)) : ?>
  <div class="sitestoreproduct_manage_store">
  <h3 class="mbot10">
    <?php echo $this->translate('Transactions') ?>
  </h3>

  <?php if( empty($this->commissionFreePackage) ) : ?>
  <div class='tabs mbot10'>
    <ul class="navigation">
      <li <?php if( empty($this->tab) ) : ?> class="active" <?php endif; ?>>
        <a href="javascript:void(0)" onclick="manage_store_dashboard(54, 'store-transaction/tab/0', 'product')">
          <?php echo $this->translate("Order Related Transactions") ?>
        </a>
      </li>		
      <li <?php if( !empty($this->tab) ) : ?> class="active" <?php endif; ?>>
        <a href="javascript:void(0)" onclick="manage_store_dashboard(54, 'store-transaction/tab/1', 'product')">
          <?php echo $this->translate("Payments to Site Administrator") ?>
        </a>
      </li>    
    </ul>
  </div>
  <?php endif; ?>
<?php endif; ?>
<?php if( empty($this->tab) ) : ?>
  <?php include APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/scripts/_orderRelatedTransaction.tpl'; ?>
<?php else: ?>
  <?php include APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/scripts/_siteAdminRelatedTransaction.tpl'; ?>
<?php endif; ?>
<?php if(empty($this->call_same_action)) : ?>
  </div>
<?php endif; ?>