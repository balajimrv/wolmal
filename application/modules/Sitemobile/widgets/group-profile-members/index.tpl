<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Group
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Group
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
?>
<a id="group_profile_members_anchor"></a>

<script type="text/javascript">
  var groupMemberSearch = '<?php echo $this->search ?>';
  var groupMemberPage = Number(<?php echo sprintf('%d', $this->members->getCurrentPageNumber()) ?>);
  var waiting = '<?php echo $this->waiting ?>';
  function getAjaxContent(search, page, waiting) {
    var ajax = sm4.core.request.send({
      type: "POST", 
      dataType: "html", 
      url : sm4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
      data: {
        'format' : 'html',
        'subject' : $.mobile.activePage.attr("data-subject"),
        'search' : search,
        'page' : page,
        'waiting' : waiting
      }
    },{
      'element' : $.mobile.activePage.find('#group_profile_members_anchor').parent()
    }
  );   
  }

  sm4.core.runonce.add(function() {
    $('#group_members_search_input').bind('keypress', function(e) {
      if( e.which != 13) return;
      getAjaxContent(this.value, null, null);
    });
  });

  var paginateGroupMembers = function(page) {
    getAjaxContent(groupMemberSearch, page, waiting);
  }

</script>

<?php if (!empty($this->waitingMembers) && $this->waitingMembers->getTotalItemCount() > 0): ?>
  <script type="text/javascript">

    var showWaitingMembers = function() {
      getAjaxContent(null, null, true);
    }

    var showFullMembers = function() {
      getAjaxContent(null, null, null);
    }
  </script>
<?php endif; ?>

<?php if (!$this->waiting): ?>
  <div class="group_members_info">
    <input id="group_members_search_input" type="text" placeholder="<?php echo $this->translate('Search Members'); ?>" role="search" data-type="search" class="ui-input-text" data-mini="true" value="<?php echo $this->search;?>">
    <div class="sm-item-members-count">
      <?php if ('' == $this->search): ?>
        <?php echo $this->translate(array('This group has %1$s member.', 'This group has %1$s members.', $this->members->getTotalItemCount()), $this->members->getTotalItemCount()) ?>
      <?php else: ?>
        <?php echo sprintf($this->translate(array('This group has %1$s members that matched the query "%2$s".', 'This group has %1$s members that matched the query "%2$s".', $this->members->getTotalItemCount())), $this->locale()->toNumber($this->members->getTotalItemCount()), $this->escape($this->search)) ?>
      <?php endif; ?>
    </div>
    <?php if (!empty($this->waitingMembers) && $this->waitingMembers->getTotalItemCount() > 0): ?>
      <div class="sm-item-members-count">
        <?php echo $this->htmlLink('javascript:void(0);', $this->translate('See Waiting'), array('onclick' => 'showWaitingMembers(); return false;')) ?>
      </div>
    <?php endif; ?>
  </div>
<?php else: ?>
  <div class="sm-item-members-count">
    <?php echo $this->translate(array('This group has %s member waiting for approval or waiting for an invite response.', 'This group has %s members waiting for approval or waiting for an invite response.', $this->members->getTotalItemCount()), $this->members->getTotalItemCount()) ?>
		<?php if( !empty($this->fullMembers) && $this->fullMembers->getTotalItemCount() > 0 ): ?>
			<div class="group_members_total">
				<?php echo $this->htmlLink('javascript:void(0);', $this->translate('View all approved members'), array('onclick' => 'showFullMembers(); return false;')) ?>
			</div>
		<?php endif; ?>
  </div>
<?php endif; ?>

<?php if ($this->members->getTotalItemCount() > 0): ?>
	<div class="sm-content-list">	
		<ul  data-role="listview" data-inset="false">
			<?php
			foreach ($this->members as $member):
				if (!empty($member->resource_id)) {
					$memberInfo = $member;
					$member = $this->item('user', $memberInfo->user_id);
				} else {
					$memberInfo = $this->group->membership()->getMemberInfo($member);
				}
				$listItem = $this->list->get($member);
				$isOfficer = ( null !== $listItem );
				?>
	
				<li <?php if ($this->group->isOwner($this->viewer()) && ((!$this->group->isOwner($member) && $memberInfo->active == true) || ($memberInfo->active == false && $memberInfo->resource_approved == false) || ( $memberInfo->active == false && $memberInfo->resource_approved == true) || ($memberInfo->active && ($isOfficer || !$this->group->isOwner($member))))): ?> data-icon="cog" <?php else: ?>  data-icon="false" <?php endif; ?> data-inset="true">
					<a href="<?php echo $member->getHref(); ?>">
			<?php echo $this->itemPhoto($member, 'thumb.icon'); ?>
						<h3><?php echo $member->getTitle() ?></h3>
					</a>
			<?php if ($this->group->isOwner($this->viewer()) && ((!$this->group->isOwner($member) && $memberInfo->active == true) || ($memberInfo->active == false && $memberInfo->resource_approved == false) || ( $memberInfo->active == false && $memberInfo->resource_approved == true) || ($memberInfo->active && ($isOfficer || !$this->group->isOwner($member))))): ?>
						<a href="#manage_<?php echo $member->getGuid() ?>" data-rel="popup"></a>
						<div data-role="popup" id="manage_<?php echo $member->getGuid() ?>" <?php echo $this->dataHtmlAttribs("popup_content", array('data-theme' => "c")); ?> >
							<ul data-role="listview" data-inset="true" data-coners="false">
								<li data-role="divider" data-coners="false">
				<?php echo $this->translate('Options'); ?>
								</li>
	
								<?php // Remove/Promote/Demote member ?>
								<?php if ($this->group->isOwner($this->viewer())): ?>
	
										<?php if (!$this->group->isOwner($member) && $memberInfo->active == true): ?>
										<li data-shadow="false">
											<?php
											echo $this->htmlLink(array('route' => 'group_extended', 'controller' => 'member', 'action' => 'remove', 'group_id' => $this->group->getIdentity(), 'user_id' => $member->getIdentity()), $this->translate('Remove Member'), array(
													'class' => 'buttonlink'
											))
											?>
										</li>
										<?php endif; ?>
										<?php if ($memberInfo->active == false && $memberInfo->resource_approved == false): ?>
										<li data-shadow="false">
						<?php
						echo $this->htmlLink(array('route' => 'group_extended', 'controller' => 'member', 'action' => 'approve', 'group_id' => $this->group->getIdentity(), 'user_id' => $member->getIdentity()), $this->translate('Approve Request'), array(
								'class' => 'smoothbox'
						))
						?>
										</li>
										<li data-shadow="false" data-coners="false">
										<?php
										echo $this->htmlLink(array('route' => 'group_extended', 'controller' => 'member', 'action' => 'reject', 'group_id' => $this->group->getIdentity(), 'user_id' => $member->getIdentity()), $this->translate('Reject Request'), array(
												'class' => 'smoothbox'
										))
										?>
										</li>
									<?php endif; ?>
									<?php if ($memberInfo->active == false && $memberInfo->resource_approved == true): ?>
										<li data-shadow="false">
										<?php
										echo $this->htmlLink(array('route' => 'group_extended', 'controller' => 'member', 'action' => 'remove', 'group_id' => $this->group->getIdentity(), 'user_id' => $member->getIdentity()), $this->translate('Cancel Invite'), array(
												'class' => 'smoothbox'
										))
										?>
										</li>
									<?php endif; ?>
	
										<?php if ($memberInfo->active): ?>
											<?php if ($isOfficer): ?>
											<li data-shadow="false" data-coners="false">
											<?php
											echo $this->htmlLink(array('route' => 'group_extended', 'controller' => 'member', 'action' => 'demote', 'group_id' => $this->group->getIdentity(), 'user_id' => $member->getIdentity()), $this->translate('Demote Officer'), array(
													'class' => 'smoothbox'
											))
											?>
											</li>
								<?php elseif (!$this->group->isOwner($member)): ?>
											<li data-shadow="false" data-coners="false">
								<?php
								echo $this->htmlLink(array('route' => 'group_extended', 'controller' => 'member', 'action' => 'promote', 'group_id' => $this->group->getIdentity(), 'user_id' => $member->getIdentity()), $this->translate('Make Officer'), array(
										'class' => 'smoothbox'
								))
								?>
											</li>
						<?php endif; ?>
					<?php endif; ?>
				<?php endif; ?>
							</ul>
						</div>
			<?php endif; ?>
				</li>
		<?php endforeach; ?>
		</ul>
	
		<?php if ($this->members->count() > 1): ?>
			<?php
			echo $this->paginationAjaxControl(
							$this->members, $this->identity, 'group_profile_members_anchor');
			?>
		<?php endif; ?>
	</div>

<?php endif; ?>