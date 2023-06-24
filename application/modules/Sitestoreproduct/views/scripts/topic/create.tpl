<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: create.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/styles/style_sitestoreproduct.css');?>

<?php 
  include_once APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/scripts/Adintegration.tpl';
?>

<div class="sr_sitestoreproduct_view_top">

  <?php echo $this->htmlLink($this->sitestoreproduct->getHref(), $this->itemPhoto($this->sitestoreproduct, 'thumb.icon', '', array('align' => 'left'))) ?>

  <h2>	
    <?php echo $this->sitestoreproduct->__toString() ?>	
    <?php echo $this->translate('&raquo; '); ?>
    <?php echo $this->htmlLink($this->sitestoreproduct->getHref(array('tab'=> $this->tab_selected_id)), $this->translate('Discussions')) ?>
  </h2>
</div>

<?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.communityads', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.addiscussioncreate', 3) && $review_communityad_integration): ?>
  <div class="layout_right" id="communityad_discussioncreate">
    <?php echo $this->content()->renderWidget("sitestoreproduct.review-ads", array('limit' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.addiscussioncreate', 3), 'tab' => 'discussioncreate', 'communityadid' => 'communityad_discussioncreate', 'isajax' => 0)); ?>
  </div>
<?php endif; ?>

<div class="layout_middle">
  <?php echo $this->form->render($this) ?>
</div>