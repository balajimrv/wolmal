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

<?php 
	$this->headLink()
        ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/styles/style_sitestoreproduct.css');
?>
<?php 
  include_once APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/scripts/Adintegration.tpl';
?>
<?php if($this->format_form !='smoothbox'):?>
	<div class="sr_sitestoreproduct_view_top">
		<?php echo $this->htmlLink($this->sitestoreproduct->getHref(), $this->itemPhoto($this->sitestoreproduct, 'thumb.icon', '', array('align' => 'left'))) ?>
		<h2>	
			<?php echo $this->sitestoreproduct->__toString() ?>	
			<?php echo $this->translate('&raquo; '); ?>
			<?php echo $this->htmlLink($this->sitestoreproduct->getHref(array('tab'=>$this->tab_selected_id)), $this->translate('Videos')) ?>
		</h2>
	</div>
	<?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.communityads', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.advideodelete', 3) && $review_communityad_integration): ?>
		<div class="layout_right" id="communityad_videodelete">
			<?php echo $this->content()->renderWidget("sitestoreproduct.review-ads", array('limit' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.advideodelete', 3), 'tab' => 'videodelete', 'communityadid' => 'communityad_videodelete', 'isajax' => 0)); ?>
		</div>
	<?php endif; ?>
<?php endif; ?>

<div class="layout_middle">
  <?php if($this->format_form !='smoothbox'):?>
	  <form method="post" class="global_form">
	<?php else: ?>
	  <form method="post" class="global_form_popup">
	<?php endif; ?>
	  <div>
	    <div>
	      <h3><?php echo $this->translate('Delete Product Video?'); ?></h3>
	      <p> 
	        <?php echo $this->translate('Are you sure that you want to delete the video titled "%1$s" last modified %2$s? It will not be recoverable after being deleted.', $this->title, $this->timestamp($this->sitestoreproduct_video->modified_date)) ?>
	      </p>
	      <br />
	      <p>
	        <input type="hidden" name="confirm" value="true"/>
	        <button type='submit' ><?php echo $this->translate('Delete'); ?></button>
	        	<?php echo $this->translate('or'); ?> <?php if($this->format_form !='smoothbox'):?><?php echo $this->htmlLink($this->sitestoreproduct->getHref(array('tab'=>$this->tab_selected_id)),$this->translate('cancel')) ?> <?php else:?><a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'><?php echo $this->translate('cancel'); ?></a> <?php endif; ?>
	      </p>
	    </div>
	  </div>
	</form>
</div>	