<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: bill-payment.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>
   
<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/styles/style_sitestoreproduct_dashboard.css'); ?>

<?php if( !empty($this->noAdminGateway) ) : ?>
  <div class="tip">
    <span>
      <?php echo $this->translate("Site Administrator has not enabled any payment gateway. So you can't pay your bill. Please contact to site administrator.") ?>
    </span>
  </div>
  <?php return; ?>
<?php endif; ?>

<div class="global_form_popup sitestoreproduct_dashbord_popup_form">
  <?php echo $this->form->render($this); ?>
</div>
