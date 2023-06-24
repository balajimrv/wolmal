<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php if (!empty($this->sitestore->sponsored)): ?>
  <?php $sponsored = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.sponsored.image', 1);
  if (!empty($sponsored)) { ?>
    <div class="sitestore_profile_sponsorfeatured" style='background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.sponsored.color', '#fc0505'); ?>;'>
      <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/images/star-img.png', '') ?>
      <?php echo $this->translate('SPONSORED'); ?>
      <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/images/star-img.png', '') ?>
    </div>
  <?php } ?>
<?php endif; ?>
<div class='sitestore_photo <?php if ($this->can_edit) : ?>sitestore_photo_edit_wrapper<?php endif; ?>'>
  <?php if (!empty($this->can_edit)) : ?>
    <a href="<?php echo $this->url(array('action' => 'profile-picture', 'store_id' => $this->sitestore->store_id), 'sitestore_dashboard', true) ?>" class="sitestore_photo_edit">  	  
      <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/images/edit_pencil.png', '') ?>
      <?php echo $this->translate('Change Picture'); ?>
    </a>
  <?php endif; ?>

  <?php echo $this->itemPhoto($this->sitestore, 'thumb.profile', '', array('align' => 'left')); ?>
</div>
<?php if (!empty($this->sitestore->featured)): ?>
  <?php $feature = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.feature.image', 1);
  if (!empty($feature)) { ?>
    <div class="sitestore_profile_sponsorfeatured"  style='background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.featured.color', '#0cf523'); ?>;'>
      <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/images/star-img.png', '') ?>
      <?php echo $this->translate('FEATURED'); ?>
    <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/images/star-img.png', '') ?>
    </div>
  <?php } ?>
<?php endif; ?>