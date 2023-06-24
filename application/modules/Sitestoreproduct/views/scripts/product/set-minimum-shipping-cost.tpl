<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: account-delete.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<form method="post" class="global_form_popup">
  <?php if (!empty($this->showMessage)): ?>
    <ul class="form-errors">
      <li>
        <?php echo $this->translate('Entered cost is invalid. Please enter value greator than or equals to zero.'); ?>
      </li>
    </ul>
  <?php endif; ?>
  <div>
    <h3 class="mbot5"><?php echo $this->translate("Minimum shipping cost for shipping methods"); ?></h3>
    <div class="form-wrapper" id="minimum_shipping_cost-wrapper">
      <div class="form-label mbot10" id="minimum_shipping_cost-label">
        <label class="optional mright10" for="search"><?php echo $this->translate('Enter minimum shipping cost'); ?></label>
        <input type="text" value="<?php echo Engine_Api::_()->sitestore()->getStoreMinShippingCost($this->store_id); ?>" id="minimum_shipping_cost" name="minimum_shipping_cost" style="width:40%;" />
      </div>

      <input type="hidden" name="store_id" value="<?php echo $this->store_id ?>"/>  
      <button type='submit'><?php echo $this->translate('Set') ?></button>
      <?php echo $this->translate(" or ") ?> 
      <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'>
        <?php echo $this->translate("cancel") ?>
      </a>
    </div>
  </div>
</form>
