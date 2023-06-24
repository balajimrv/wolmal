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

<?php $orderViewUrl = $this->htmlLink($this->url(array('action' => 'store', 'store_id' => $this->order_obj->store_id, 'type' => 'index', 'menuId' => 55, 'method' => 'order-view', 'order_id' => $this->order_id), 'sitestore_store_dashboard', true), '#'.$this->order_id, array('target' => '_blank')); ?>

<?php if( $this->gateway_id == 3 ) : ?>
  <div class="global_form_popup">
    <h3><?php echo $this->translate("Approve Payment") ?></h3>
    <p><?php echo $this->translate("Here, approve payment made for the order: %s", $orderViewUrl) ?></p>
    <?php echo $this->form->render($this) ?>
  </div>
<?php else: ?>
  <form method="post" class="global_form_popup">
    <div>
      <h3><?php echo $this->translate("Approve Payment") ?></h3>
      <p><?php echo $this->translate("Here, approve payment made for the order: %s", $orderViewUrl) ?></p>
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

