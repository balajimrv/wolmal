<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */
?>

<div class="sm-content-list" id="profile_links">
	<ul data-role="listview" data-icon="arrow-r">
		<?php foreach( $this->paginator as $link ): ?>
			<li>
				<a href="<?php echo $link->getHref(); ?>">
          <?php if($link->photo_id != 0):?>
						<?php echo $this->itemPhoto($link, 'thumb.icon'); ?>
          <?php endif;?>
					<h3><?php echo $link->getTitle() ?></h3>
          <?php if( !$link->getOwner()->isSelf($link->getParent()) ): ?>
						<p>   
							<?php echo $this->translate('Posted by') ?>
							<strong><?php echo $link->getOwner()->getTitle(); ?></strong>
						</p>
						<p>
							<?php echo $this->timestamp(strtotime($link->creation_date)) ?>
						</p>
          <?php endif;?>
        </a>
			</li>
		<?php endforeach; ?>
	</ul>
	<?php if ($this->paginator->count() > 1): ?>
		<?php
		echo $this->paginationAjaxControl(
						$this->paginator, $this->identity, 'profile_links');
		?>
	<?php endif; ?>
</div>