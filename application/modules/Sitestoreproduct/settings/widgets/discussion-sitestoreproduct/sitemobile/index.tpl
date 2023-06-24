<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2013-04-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<?php if ($this->paginator->getTotalItemCount() > 0): ?>
  <div class="sm-content-list" id="profile_sitestorediscussions">
		<ul data-role="listview" data-inset="false" data-icon="false">
		  <?php foreach ($this->paginator as $topic):
          $lastpost = $topic->getLastPost();
          $lastposter = Engine_Api::_()->getItem('user', $topic->lastposter_id);
      ?>
			<li data-icon="arrow-r">
				<a href="<?php echo $topic->getHref(array( 'content_id' => $this->identity)); ?>">
					<h3><?php echo $topic->getTitle() ?></h3>
					<p class="ui-li-aside"><strong> <?php echo $this->translate(array('%s reply', '%s replies', $topic->post_count - 1), $this->locale()->toNumber($topic->post_count - 1)) ?></strong></p>
					<p><?php echo $this->translate('Last Post') ?> <?php echo $this->translate('by'); ?> <strong><?php echo $lastposter->getTitle() ?></strong></p>
				</a>
			</li>
      <?php endforeach;?>
		</ul>

		<?php if ($this->paginator->count() > 1): ?>
			<?php
			echo $this->paginationAjaxControl(
							$this->paginator, $this->identity, "profile_sitestorediscussions");
			?>
		<?php endif; ?>
	</div>
<?php else:?>
	<div class="tip">
		<span>
			<?php echo $this->translate('No discussion topics have been posted yet.'); ?>
		</span>
	</div>
<?php endif;?>