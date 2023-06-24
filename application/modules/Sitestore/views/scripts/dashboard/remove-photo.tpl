<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: remove-photo.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/payment_navigation_views.tpl'; ?>

<div class="sitestore_viewstores_head">
  <?php echo $this->htmlLink($this->sitestore->getHref(), $this->itemPhoto($this->sitestore, 'thumb.icon', '', array('align' => 'left'))) ?>
  <h2>	
    <?php echo $this->sitestore->__toString() ?>
  </h2>
</div>

<div class='global_form'>
  <form method="post" action="<?php echo $this->url(array('store_id' => $this->sitestore->store_id, 'action' => 'remove-photo'), 'sitestore_dashboard', true) ?>" class="global_form" enctype="application/x-www-form-urlencoded">
    <div>
      <div>
        <h3><?php echo $this->translate("Remove Photo"); ?></h3>
        <p class="form-description"><?php echo $this->translate("Do you want to remove profile photo of your Store? Doing so will set your Store's profile photo back to the default photo.") ?></p>
        <div class="form-elements">
          <div id="buttons-wrapper" class="form-wrapper"><fieldset id="fieldset-buttons">		
              <button type="submit" id="submit" name="submit" value="submit"><?php echo $this->translate("Remove Photo"); ?></button>			
              <?php echo $this->translate("or"); ?> <a onclick="parent.Smoothbox.close();" href="<?php echo $this->url(array('store_id' => $this->sitestore->store_id, 'action' => 'profile-picture'), 'sitestore_dashboard', true) ?>" type="button" id="cancel" name="cancel"><?php echo $this->translate("cancel"); ?></a></fieldset></div>
          <input type="hidden" id="token" value="ec82d65d6feea1b453c59302a8dbdfba" name="token">
        </div>
      </div>
    </div>
  </form>
</div>