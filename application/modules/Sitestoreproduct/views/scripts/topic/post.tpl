<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: post.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
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
    <?php echo $this->translate('&raquo; '); ?>
    <?php echo $this->topic->__toString() ?>
	</h2>
</div>

<?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.communityads', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.advideodelete', 3) && $review_communityad_integration): ?>
	<div class="layout_right" id="communityad_videodelete">
		<?php echo $this->content()->renderWidget("sitestoreproduct.review-ads", array('limit' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.advideodelete', 3), 'tab' => 'videodelete', 'communityadid' => 'communityad_videodelete', 'isajax' => 0)); ?>
	</div>
<?php endif; ?>

<div class="layout_middle">
	<?php if($this->message) echo $this->message ?>
	<?php if($this->form) echo $this->form->render($this) ?>
</div>