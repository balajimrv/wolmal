<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: delete.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

?>
<div class='global_form'>
  <form method="post" class="global_form" data-ajax="false">
    <div>
      <div>
        <h3><?php echo $this->translate("Delete Product?"); ?></h3>
        <p>
          <?php echo $this->translate('Are you sure that you want to delete the Product with the title "%1$s" last modified %2$s? It will not be recoverable after being deleted. Buyer and seller both will not be able to view this product in to the placed orders.', $this->sitestoreproduct->title, $this->timestamp($this->sitestoreproduct->modified_date)); ?>
        </p>
        <br />
        <p>
          <input type="hidden" name="confirm" value="true"/>
          <button type='submit'><?php echo $this->translate('Delete'); ?></button>
          <?php echo $this->translate('or'); ?> <a href="javascript:void(0)" onclick="window.location.href='<?php echo $this->url(array('controller' => "product", "action" => "manage", "store_id" => $this->store_id),  'sitestoreproduct_extended', true) ?>';return false;"><?php echo $this->translate('cancel'); ?></a>
        </p>
      </div>
    </div>
  </form>
</div>