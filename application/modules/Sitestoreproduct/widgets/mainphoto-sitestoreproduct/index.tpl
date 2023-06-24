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
<div class="sr_sitestoreproduct_profile_cover_photo_wrapper">
	<?php if (!empty($this->sitestoreproduct->featured) && $this->show_featured): ?> 
		<div class="sr_sitestoreproduct_profile_sponsorfeatured"  style='background: <?php echo $this->featured_color; ?>;'>
			<?php echo $this->translate('FEATURED');?>	
		</div>
	<?php endif; ?>
	<div class='sr_sitestoreproduct_profile_cover_photo <?php if ($this->can_edit):?>sr_sitestoreproduct_photo_edit_wrapper<?php endif;?>'>
		<?php if (!empty($this->can_edit)) : ?>
			<a class='sr_sitestoreproduct_photo_edit' href="<?php echo $this->url(array('action' => 'change-photo', 'product_id' => $this->sitestoreproduct->product_id), "sitestoreproduct_dashboard", true) ?>">
				<i class="sr_sitestoreproduct_icon"></i>
				<?php echo $this->translate('Change Picture'); ?>
			</a>
		<?php endif;?>
		<?php if($this->sitestoreproduct->newlabel):?>
			<i class="sr_sitestoreproduct_list_new_label" title="<?php echo $this->translate('New'); ?>"></i>
		<?php endif;?>

		<?php echo $this->itemPhoto($this->sitestoreproduct, 'thumb.profile', '' , array('align' => 'center')); ?>
	</div>
	<?php if (!empty($this->sitestoreproduct->sponsored) && $this->show_sponsered): ?>
		<div class="sr_sitestoreproduct_profile_sponsorfeatured" style='background: <?php echo $this->sponsored_color; ?>;'>
			<?php echo $this->translate('SPONSORED'); ?>
		</div>
	<?php endif; ?>
	<?php if($this->ownerName): ?>
	  <div class='sr_sitestoreproduct_profile_cover_name'>
	    <?php echo $this->htmlLink($this->sitestoreproduct->getOwner()->getHref(), $this->sitestoreproduct->getOwner()->getTitle()) ?>
	  </div>
	<?php endif; ?>
</div>

