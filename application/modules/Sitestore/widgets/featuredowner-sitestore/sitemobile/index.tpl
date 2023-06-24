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

<div class="sm-content-list" id="profile_featuredowners">
	<ul data-role="listview" data-icon="arrow-r">
		<?php foreach ($this->featuredowners as $item): ?>
			<li>
				<a href="<?php echo $item->getOwner()->getHref(); ?>">
					<?php echo $this->itemPhoto($item->getOwner(), 'thumb.icon'); ?>
					<h3><?php echo $item->getOwner()->getTitle() ?></h3>
				</a> 
			</li>
		<?php endforeach; ?>
	</ul>
	<?php if ($this->featuredowners->count() > 1): ?>
		<?php
		echo $this->paginationAjaxControl(
						$this->featuredowners, $this->identity, "profile_featuredowners");
		?>
	<?php endif; ?>
</div>