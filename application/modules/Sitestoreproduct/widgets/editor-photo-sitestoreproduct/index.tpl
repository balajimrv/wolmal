<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/styles/style_sitestoreproduct.css'); ?>

<div class="sr_sitestoreproduct_editor_profile_info">
  <div class="sr_sitestoreproduct_editor_profile_photo">
    <?php echo $this->htmlLink($this->user->getHref(), $this->itemPhoto($this->user, 'thumb.profile', '', array('align' => 'center'))) ?>
  </div>

  <?php if (!$this->user->isSelf($this->viewer()) && $this->user->email): ?>
    <div class="sr_sitestoreproduct_editor_product_stat"><b><?php echo $this->htmlLink(array('route'=>'sitestoreproduct_editor_general','action' => 'editor-mail', 'user_id' => $this->user->user_id), $this->translate('Email %s', $this->user->getTitle()), array('class' => 'smoothbox sr_sitestoreproduct_icon_send buttonlink')) ?></b></div>
  <?php endif; ?>
    
  <div class="sr_sitestoreproduct_editor_product_stat">
    <?php echo $this->htmlLink($this->user->getHref(), $this->translate('View full profile'), array('class' => 'sr_sitestoreproduct_icon_editor_profile buttonlink'));?></b>
  </div>
    
  <div class="sr_sitestoreproduct_editor_product_stat">
    <?php echo $this->htmlLink(array('route' => "sitestoreproduct_editor_general", 'action' => 'home'), $this->translate('View all Editors'), array('class' => 'sr_sitestoreproduct_icon_editor buttonlink')) ?>
  </div>    
</div>	