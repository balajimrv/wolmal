<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Blog
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Blog
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
?>
<div class="sm-content-list">
	<ul data-role="listview" <?php if (Engine_Api::_()->sitemobile()->isApp()): ?>data-icon="angle-right"<?php else : ?>data-icon="arrow-r"<?php endif;?> id="profile_blogs">
		<?php foreach ($this->paginator as $item): ?>
			<li>
				<a href="<?php echo $item->getHref(); ?>">
          <?php if (Engine_Api::_()->sitemobile()->isApp()): ?> 
            <?php echo $this->itemPhoto($item->getOwner(), 'thumb.icon') ?>
          <?php endif?>
					<h3><?php echo $item->getTitle() ?></h3>
					<p><?php echo $this->timestamp($item->creation_date) ?></p>
				</a> 
			</li>
		<?php endforeach; ?>
	</ul>
	<?php if ($this->paginator->count() > 1): ?>
		<?php
		echo $this->paginationAjaxControl(
						$this->paginator, $this->identity, 'profile_blogs');
		?>
	<?php endif; ?>
</div>	