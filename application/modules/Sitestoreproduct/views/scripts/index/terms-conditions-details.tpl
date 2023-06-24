<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: terms-conditions-details.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>
<div class="global_form_popup">
  <div class="mbot10"><h3><?php echo $this->translate("%s store Terms and Conditions", $this->store_title) ?></h3></div>
  <div>
    <?php if( !empty($this->termsConditions) ) : ?>
      <?php echo $this->termsConditions; ?>
    <?php else: ?>
      <?php echo $this->translate("There are no terms & conditions provided by the seller.") ?>
    <?php endif; ?>
  </div>
  <div class='buttons clr mleft10 mtop10'>
    <button type='button' name="cancel" onclick="javascript:parent.Smoothbox.close();">
      <?php echo $this->translate("Close") ?>
    </button>
  </div>
</div>