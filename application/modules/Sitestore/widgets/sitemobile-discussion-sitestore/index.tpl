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

<?php if ($this->paginator->getTotalItemCount() > 0): ?>
	<?php if ($this->canPost): ?>
		<div class="seaocore_add" data-role="controlgroup" data-type="horizontal">
			<?php  echo $this->htmlLink(array(
          'route' => 'sitestore_extended',
          'controller' => 'topic',
          'action' => 'create',
          'subject' => $this->subject()->getGuid(),
          'store_id' => $this->store_id,
          'tab' => Zend_Controller_Front::getInstance()->getRequest()->getParam('tab')),
          $this->translate('Post New Topic'),
              array(
					'class' => 'buttonlink icon_sitestore_post_new','data-role' => "button", 'data-icon' => "plus", "data-iconpos" => "left", "data-inset" => 'false', 'data-mini' => "true", 'data-corners' => "true", 'data-shadow' => "true"))
			?>
		</div>
	<?php endif; ?>
    
  <div class="sm-content-list" id="profile_sitestorediscussions">
		<ul data-role="listview" data-inset="false" data-icon="false">
		  <?php foreach ($this->paginator as $topic):
          $lastpost = $topic->getLastPost();
          $lastposter = $topic->getLastPoster();
      ?>
			<li data-icon="arrow-r">
				<a href="<?php echo $topic->getHref(); ?>">
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
			<?php echo $this->translate('No discussion topics have been posted in this Store yet.'); ?>
			<?php
			if ($this->canPost):
				$show_link = $this->htmlLink(array(
            'route' => 'sitestore_extended',
            'controller' => 'topic',
            'action' => 'create',
            'subject' => $this->subject()->getGuid(),
            'store_id' => $this->store_id,
            'tab' => Zend_Controller_Front::getInstance()->getRequest()->getParam('tab')),
                $this->translate('here'));
				$show_label = Zend_Registry::get('Zend_Translate')->_('Click %s to start a discussion.');
				$show_label = sprintf($show_label, $show_link);
				echo $show_label;
			endif;
			?>
		</span>
	</div>
<?php endif;?>