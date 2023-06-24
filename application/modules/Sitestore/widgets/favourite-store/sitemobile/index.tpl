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

<div class="sm-content-list" id="favourite_stores">
	<ul data-role="listview" data-icon="arrow-r">
		<?php foreach( $this->userListings as $sitestore ): ?>
			<li>
				<a href="<?php echo Engine_Api::_()->sitestore()->getHref($sitestore->store_id_for, $sitestore->owner_id,$sitestore->getSlug()); ?>">
					<?php echo $this->itemPhoto($sitestore, 'thumb.icon'); ?>
					<h3><?php echo $sitestore->getTitle() ?></h3>
        </a>
			</li>
		<?php endforeach; ?>
	</ul>
	<?php if ($this->userListings->count() > 1): ?>
		<?php
		echo $this->paginationAjaxControl(
						$this->userListings, $this->identity, "favourite_stores", array("category_id" => $this->category_id, "featured" => $this->featured, "sponsored" => $this->sponsored));
		?>
  <?php endif;?>
</div>