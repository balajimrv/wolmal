<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: view.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/common_style_css.tpl';
?>
<?php if (empty($this->viewPrivacy) && !empty($this->sitestoreMemberEnabled) && empty($this->hasMembers)) : ?>
<div class="layout_middle">
	<div class="layout_left">
		<?php //echo $this->sitemain; ?>
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
		<?php if (!empty($this->viewer_id)): ?>
			<div class="generic_layout_container layout_sitestore_options_sitestore">
				<?php if (!empty($this->member_approval)) : ?>
					<br /><?php echo $this->htmlLink(array('route' => 'sitestore_profilestoremember', 'action' => 'join', 'store_id' => $this->sitestore->store_id), $this->translate('Join Store'), array(  ' class' => 'buttonlink smoothbox icon_sitestore_join')); ?>
					<?php else : ?>
						<?php if (empty($this->hasMembers)) : ?>
							<br /><?php echo $this->htmlLink(array('route' => 'sitestore_profilestoremember', 'action' => 'request', 'store_id' => $this->sitestore->store_id), $this->translate('Join Store'), array(  ' class' => 'buttonlink smoothbox icon_sitestore_join')); ?>
						<?php else : ?>
							<br /><?php echo $this->htmlLink(array('route' => 'sitestore_profilestoremember', 'action' => 'cancel', 'store_id' => $this->sitestore->store_id), $this->translate('Cancel Member Request for Store'), array(  ' class' => 'buttonlink smoothbox icon_sitestore_join')); ?>
						<?php endif ;?>
				<?php endif;  ?>
			</div>
		<?php endif;  ?>
	</div>
	
	<div class="layout_middle">
		<div id='profile_status'>
		  <h2>
		    <?php echo $this->sitestore->getTitle() ?>
		  </h2>
		</div>
		
		<div class="generic_layout_container layout_core_container_tabs">
			<div class='tabs_alt tabs_parent'>
				<ul class="main_tabs">
<!--					<li id="info" class="active">
						<a href="javascript:void(0);" onclick="showtab('info')"><?php //echo $this->translate('Info') ?></a>
					</li>-->
					<?php if ($this->sitestore->overview) :?>
					<li id="overview" class="active">
						<a href="javascript:void(0);" onclick="showtab('overview')"><?php echo $this->translate('Overview') ?></a>
					</li>
					<?php endif; ?>
				</ul>
			</div>
<!--			<div id="infocontentshow">
				 <?php 	//echo $this->content()->renderWidget("sitestore.info-sitestore", array()); ?>
			</div>-->
			<?php if ($this->sitestore->overview) :?>
				<div id="overviewcontentshow" >
					<?php echo $this->content()->renderWidget("sitestore.overview-sitestore", array()); ?>
				</div>
			<?php endif; ?>
	  </div>
	</div>

</div>
<?php elseif (empty($this->viewPrivacy) && !empty($this->sitestoreMemberEnabled) && !empty($this->hasMembers)) : ?>
<div class="layout_middle">
	<div class="layout_left">
		<?php //echo $this->sitemain; ?>
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
		<?php if (!empty($this->viewer_id)): ?>
			<div class="generic_layout_container layout_sitestore_options_sitestore">
				<?php if (!empty($this->member_approval)) : ?>
					<br /><?php echo $this->htmlLink(array('route' => 'sitestore_profilestoremember', 'action' => 'join', 'store_id' => $this->sitestore->store_id), $this->translate('Join Store'), array(  ' class' => 'buttonlink smoothbox icon_sitestore_join')); ?>
					<?php else : ?>
						<?php if (empty($this->hasMembers)) : ?>
							<br /><?php echo $this->htmlLink(array('route' => 'sitestore_profilestoremember', 'action' => 'request', 'store_id' => $this->sitestore->store_id), $this->translate('Request Member for Store'), array(  ' class' => 'buttonlink smoothbox icon_sitestore_join')); ?>
						<?php else : ?>
							<br /><?php echo $this->htmlLink(array('route' => 'sitestore_profilestoremember', 'action' => 'cancel', 'store_id' => $this->sitestore->store_id), $this->translate('Cancel Member Request for Store'), array(  ' class' => 'buttonlink smoothbox icon_sitestore_join')); ?>
						<?php endif ;?>
				<?php endif;  ?>
			</div>
		<?php endif;  ?>
	</div>
	
	<div class="layout_middle">
		<div id='profile_status'>
		  <h2>
		    <?php echo $this->sitestore->getTitle() ?>
		  </h2>
		</div>
		
		<div class="generic_layout_container layout_core_container_tabs">
			<div class='tabs_alt tabs_parent'>
				<ul class="main_tabs">
<!--					<li id="info" class="active">
						<a href="javascript:void(0);" onclick="showtab('info')"><?php //echo $this->translate('Info') ?></a>
					</li>-->
					<?php if ($this->sitestore->overview) :?>
					<li id="overview" class="active">
						<a href="javascript:void(0);" onclick="showtab('overview')"><?php echo $this->translate('Overview') ?></a>
					</li>
					<?php endif; ?>
				</ul>
			</div>
<!--			<div id="infocontentshow">
				 <?php //	echo $this->content()->renderWidget("sitestore.info-sitestore", array()); ?>
			</div>-->
			<?php if ($this->sitestore->overview) :?>
				<div id="overviewcontentshow">
					<?php echo $this->content()->renderWidget("sitestore.overview-sitestore", array()); ?>
				</div>
			<?php endif; ?>
	  </div>
	</div>

</div>
<?php else : ?>
<?php echo $this->sitemain; ?>
<?php endif; ?>
<?php //if ($this->sitestore->overview) : ?>
<!--<script type="text/javascript">

	en4.core.runonce.add(function() {
		$('overviewcontentshow').style.display = 'none';
	});
	</script>
	<?php //endif; ?>-->
	<script type="text/javascript">
	function showtab(showtabcontent) {
		if (showtabcontent == 'info') {
			$('info').addClass('active');
			$('overview').removeClass('active');
			//$('infocontentshow').style.display = 'block';
			$('overviewcontentshow').style.display = 'none';
		} else {
			$('overview').addClass('active');
			$('info').removeClass('active');
			$('overviewcontentshow').style.display = 'block';
			//$('infocontentshow').style.display = 'none';
		}
	}
</script>