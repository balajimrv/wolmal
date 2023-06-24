<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
?>

<?php if (!empty($this->showContent)):?>

	<?php if (in_array("profileFields", $this->showContent)): ?>
		<?php echo $this->fieldValueLoopSM($this->subject(), $this->fieldStructure); ?>
	<?php endif;?>

	<div class="profile_fields">
		<h4><?php echo $this->translate("Member Information");?></h4>
		<ul>
			<?php if (in_array("memberType", $this->showContent) && !empty($this->memberType)): ?>
				<li>
					<span><?php echo $this->translate('Member Type') ?></span>
					<span><?php echo $this->translate($this->memberType) ?></span>
				</li>
			<?php endif ?>
			<?php if (in_array("networks", $this->showContent) && !empty($this->networks) && count($this->networks) > 0): ?>
				<li>
					<span><?php echo $this->translate('Networks') ?></span>
					<span><?php echo $this->fluentList($this->networks) ?></span>
				</li>
			<?php endif ?>
			<?php if (in_array("profileViews", $this->showContent)) :?>
				<li>
					<span><?php echo $this->translate('Profile Views') ?></span>
					<span><?php echo $this->translate(array('%s view', '%s views', $this->subject->view_count), $this->locale()->toNumber($this->subject->view_count)) ?></span>
				</li>
			<?php endif;?>
			<?php if (in_array("friends", $this->showContent)) :?>
				<?php $direction = Engine_Api::_()->getApi('settings', 'core')->getSetting('user.friends.direction'); ?>
				<?php if ($direction == 0): ?>
					<li>
						<span><?php echo $this->translate('Followers') ?></span>
						<span><?php echo $this->translate(array('%s follower', '%s followers', $this->subject->member_count), $this->locale()->toNumber($this->subject->member_count)) ?></span>
					</li>
				<?php else: ?>
					<li>
						<span><?php echo $this->translate('Friends') ?></span>
						<span><?php echo $this->translate(array('%s friend', '%s friends', $this->subject->member_count), $this->locale()->toNumber($this->subject->member_count)) ?></span>
					</li>
				<?php endif ?>
			<?php endif;?>
			<?php if (in_array("lastUpdated", $this->showContent)) :?>
				<li>
					<span><?php echo $this->translate('Last Update') ?></span>
					<span><?php echo $this->timestamp($this->subject->modified_date) ?></span>
				</li>
			<?php endif;?>
			<?php if (in_array("joined", $this->showContent)) :?>
				<li>
					<span><?php echo $this->translate('Joined') ?></span>
					<span><?php echo $this->timestamp($this->subject->creation_date) ?><span>
				</li>
			<?php endif;?>
			<?php if ( in_array("enabled", $this->showContent) && !$this->subject->enabled && $this->viewer->isAdmin()): ?>
				<li>
					<span><?php echo $this->translate('Enabled') ?></span>
					<span><?php echo $this->translate('No') ?></span>
				</li>
			<?php endif ?>
		</ul>
	</div>
<?php endif;?>