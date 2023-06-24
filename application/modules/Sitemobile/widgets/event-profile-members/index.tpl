<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Event
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Event
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
?>

<a id="event_profile_members_anchor"></a>

<script type="text/javascript">
  var eventMemberSearch = <?php echo Zend_Json::encode($this->search) ?>;
  var eventMemberPage = Number('<?php echo $this->members->getCurrentPageNumber() ?>');
  var waiting = '<?php echo $this->waiting ?>';

  function getAjaxContent(search, page, waiting) {
    var ajax = sm4.core.request.send({
      type: "POST", 
      dataType: "html", 
      url : sm4.core.baseUrl + 'core/widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
      data: {
        'format' : 'html',
        'subject' : $.mobile.activePage.attr("data-subject"),
        'search' : search,
        'page' : page,
        'waiting' : waiting
      }
    },{
      'element' : $.mobile.activePage.find('#event_profile_members_anchor').parent()
    }
  );   
  }

  sm4.core.runonce.add(function() {
    $('#event_members_search_input').bind('keypress', function(e) {
      if( e.which != 13) return;
      getAjaxContent(this.value, null, null);
    });
  });

  var paginateEventMembers = function(page) {
    getAjaxContent(eventMemberSearch, page, waiting);
  }
</script>

<?php if (!empty($this->waitingMembers) && $this->waitingMembers->getTotalItemCount() > 0): ?>
  <script type="text/javascript">
    var showWaitingMembers = function() {
      getAjaxContent(null, null, true);
    }
  </script>
<?php endif; ?>

<?php if(!$this->autoContentLoad) : ?>
<?php if (!$this->waiting): ?>
  <div>
    <input id="event_members_search_input" type="text" placeholder="<?php echo $this->translate('Search Guests'); ?>" role="search" data-type="search" class="ui-input-text" data-mini="true" value="<?php echo $this->search;?>">

    <div class="sm-item-members-count">
      <?php if ('' == $this->search): ?>
        <?php echo $this->translate(array('This event has <strong>%1$s</strong> guest.', 'This event has <strong>%1$s</strong> guests.', $this->members->getTotalItemCount()), $this->members->getTotalItemCount()) ?>
      <?php else: ?>
        <?php echo sprintf($this->translate(array('This event has %1$s guest that matched the query "%2$s".', 'This event has %1$s guests tha matched the query "%2$s".', $this->members->getTotalItemCount())), $this->locale()->toNumber($this->members->getTotalItemCount()), $this->escape($this->search)) ?>
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
    <?php echo $this->translate(array('This event has %1$s member waiting approval or waiting for a invite response.', 'This event has %1$s members waiting approval or waiting for a invite response.', $this->members->getTotalItemCount()), $this->members->getTotalItemCount()) ?>
  </div>
<?php endif; ?>
<?php endif; ?>

<?php if ($this->members->getTotalItemCount() > 0): ?>
<?php if (Engine_Api::_()->sitemobile()->isApp()): ?>
  <div class="sm-content-list" id="event_members_browseul">
    <ul data-role="listview" data-inset="false" data-icon="angle-right">
<?php else : ?>
  <div class="sm-content-list">
    <ul data-role="listview" data-inset="false">
<?php endif ; ?>
			<?php
			foreach ($this->members as $member):
				if (!empty($member->resource_id)) {
					$memberInfo = $member;
					$member = $this->item('user', $memberInfo->user_id);
				} else {
					$memberInfo = $this->event->membership()->getMemberInfo($member);
				}
				?>
				<li <?php if ($this->event->isOwner($this->viewer()) && ((!$this->event->isOwner($member) && $memberInfo->active == true) || ($memberInfo->active == false && $memberInfo->resource_approved == false) || ($memberInfo->active == false && $memberInfo->resource_approved == true) )): ?> data-icon="cog" <?php else: ?> data-icon="false" <?php endif; ?> data-inset="true" >
					<a href="<?php echo $member->getHref(); ?>">
			<?php echo $this->itemPhoto($member, 'thumb.icon'); ?>
						<h3><?php echo $member->getTitle() ?></h3>
						<p>
							<?php // Titles ?>
							<?php if ($this->event->getParent()->getGuid() == ($member->getGuid())): ?>
								<strong><?php echo $this->translate('(%s)', ( $memberInfo->title ? $memberInfo->title : $this->translate('owner'))) ?></strong>
			<?php endif; ?>
						</p>
						<p>
							<?php if ($memberInfo->rsvp == 0): ?>
								<?php echo $this->translate('Not Attending') ?>
							<?php elseif ($memberInfo->rsvp == 1): ?>
								<?php echo $this->translate('Maybe Attending') ?>
							<?php elseif ($memberInfo->rsvp == 2): ?>
								<?php echo $this->translate('Attending') ?>
							<?php else: ?>
								<?php echo $this->translate('Awaiting Reply') ?>
			<?php endif; ?>
						</p>
					</a>
			<?php if ($this->event->isOwner($this->viewer()) && ((!$this->event->isOwner($member) && $memberInfo->active == true) || ($memberInfo->active == false && $memberInfo->resource_approved == false) || ($memberInfo->active == false && $memberInfo->resource_approved == true) )): ?>
						<a href="#manage_<?php echo $member->getGuid() ?>" data-rel="popup"></a>
						<div data-role="popup" id="manage_<?php echo $member->getGuid() ?>" <?php echo $this->dataHtmlAttribs("popup_content", array('data-theme' => "c")); ?> >
							<ul data-role="listview" data-inset="true" data-coners="false">
								<li data-role="divider">
								<?php echo $this->translate('Options'); ?>
								</li>
								<?php // Remove/Promote/Demote member ?>
								<?php if ($this->event->isOwner($this->viewer())): ?>
										<?php if (!$this->event->isOwner($member) && $memberInfo->active == true): ?>
										<li data-shadow="false" data-coners="false">
											<?php
											echo $this->htmlLink(array('route' => 'event_extended', 'controller' => 'member', 'action' => 'remove', 'event_id' => $this->event->getIdentity(), 'user_id' => $member->getIdentity()), $this->translate('Remove Member'), array(
													'class' => 'smoothbox'
											))
											?>
										</li>
										<?php endif; ?>
										<?php if ($memberInfo->active == false && $memberInfo->resource_approved == false): ?>
										<li data-shadow="false" data-coners="false">
						<?php
						echo $this->htmlLink(array('route' => 'event_extended', 'controller' => 'member', 'action' => 'approve', 'event_id' => $this->event->getIdentity(), 'user_id' => $member->getIdentity()), $this->translate('Approve Request'), array(
								'class' => 'smoothbox'
						))
						?>
										</li>
										<li data-shadow="false" data-coners="false">
										<?php
										echo $this->htmlLink(array('route' => 'event_extended', 'controller' => 'member', 'action' => 'remove', 'event_id' => $this->event->getIdentity(), 'user_id' => $member->getIdentity()), $this->translate('Reject Request'), array(
												'class' => 'smoothbox'
										))
										?>
										</li>
									<?php endif; ?>
									<?php if ($memberInfo->active == false && $memberInfo->resource_approved == true): ?>
										<li data-shadow="false" data-coners="false">
								<?php
								echo $this->htmlLink(array('route' => 'event_extended', 'controller' => 'member', 'action' => 'cancel', 'event_id' => $this->event->getIdentity(), 'user_id' => $member->getIdentity()), $this->translate('Cancel Invite'), array(
										'class' => 'smoothbox'
								))
								?>
										</li>
					<?php endif; ?>
				<?php endif; ?>
							</ul>
						</div>
			<?php endif; ?>
				</li>
		<?php endforeach; ?>
    <?php if(!$this->autoContentLoad) : ?>
		</ul>
		<?php if ($this->members->count() > 1 && !Engine_Api::_()->sitemobile()->isApp()): ?>
			<?php
			echo $this->paginationAjaxControl(
							$this->members, $this->identity, 'event_profile_members_anchor');
			?>
		<?php endif; ?>
	</div>
   <?php endif; ?>
<?php endif; ?>

<script type="text/javascript">
        <?php if (Engine_Api::_()->sitemobile()->isApp()) :?>
        <?php $current_url = $this->url(array('action' => 'index')); ?>    
         sm4.core.runonce.add(function() { 
              var activepage_id = sm4.activity.activityUpdateHandler.getIndexId();
              sm4.core.Module.core.activeParams[activepage_id] = {'currentPage' : '<?php echo sprintf('%d', $this->page) ?>', 'totalPages' : '<?php echo sprintf('%d', $this->totalPages) ?>', 'formValues' : <?php echo json_encode($this->search);?>, 'contentUrl' : '<?php echo $current_url; ?>', 'activeRequest' : false, 'container' : 'event_members_browseul' };  
          });
         
   <?php endif; ?>   
</script>   