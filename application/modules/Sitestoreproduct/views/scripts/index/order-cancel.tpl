<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: order-cancel.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<div class='global_form_popup'>
  <form method="post" class="global_form">
    <div>
      <div>
        <h3><?php echo $this->translate("Cancel this Order?"); ?></h3>
        <p>
          <?php echo $this->translate('Are you sure you want to cancel this order? (If you cancel this order, then buyer will not be able to make payment (if payment is pending) and you will not be able to complete this order.)'); ?>
        </p>
        <br />
        <p>
          <button type='submit'><?php echo $this->translate('Continue'); ?></button>
          <?php echo $this->translate('or'); ?>
          <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'>
          <?php echo $this->translate("cancel") ?></a>
        </p>
      </div>
    </div>
  </form>
</div>