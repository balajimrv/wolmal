<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: delete-payment-request.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<form method="post" class="global_form_popup">
  <div>
      <h3><?php echo $this->translate("Cancel Payment Request") ?></h3>
      <p>
        <?php echo $this->translate("Are you sure that you want to cancel this payment request? This payment request will not be recoverable after being canceled.") ?>
      </p>
      <br />
      <p>
        <button type='submit'><?php echo $this->translate("Cancel Payment Request") ?></button>
        <?php echo $this->translate(" or ") ?> 
        <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'>
        <?php echo $this->translate("cancel") ?></a>
      </p>
  </div>
</form>