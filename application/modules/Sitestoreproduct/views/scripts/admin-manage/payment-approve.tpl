<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: payment-approve.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<?php if( empty($this->paymentPending) ) : ?>
  <div class="global_form_popup">
    <h3><?php echo $this->translate("Approve Payment") ?></h3>
    <p><?php echo $this->translate("Here, approve payment made for the order(s): %s", $this->payment_approve_message)  ?></p>
    <?php echo $this->form->render($this) ?>
  </div>
<?php else: ?>
  <form method="post" class="global_form_popup">
    <div>
      <h3><?php echo $this->translate("Approve Payment") ?></h3>
      <p><?php echo $this->translate("Here, approve payment made for the order(s): %s", $this->payment_approve_message)  ?></p>
      <br />
      <p>
        <input type="hidden" name="confirm" value="true"/>
        <button type='submit'><?php echo $this->translate('Approve Payment'); ?></button>
        <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'>
          <?php echo $this->translate("cancel") ?>
        </a>
      </p>
    </div>
  </form>
<?php endif; ?>

