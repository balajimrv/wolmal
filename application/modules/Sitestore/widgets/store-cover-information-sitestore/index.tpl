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

<?php $minHeight=110;
if($this->sitestore->sponsored)
  $minHeight =$minHeight +20;
if($this->sitestore->featured)
  $minHeight =$minHeight +20;
?>
<div class="sitestore_cover_information_wrapper">

  <div class='sitestore_cover_wrapper' id="sitestore_cover_photo" style='min-height:<?php echo $minHeight;?>px; height:<?php echo (!empty($this->sitestore->store_cover) || !empty($this->can_edit)) ? $this->columnHeight:$minHeight; ?>px;'  >
  </div>
  <?php if($this->showContent):?>
  <div class="sitestore_cover_information b_medium">
    <?php if (in_array('mainPhoto', $this->showContent)): ?>
      <div class="sp_coverinfo_profile_photo_wrapper">
        <div class="sp_coverinfo_profile_photo b_dark">
          <?php if (!empty($this->sitestore->sponsored)): ?>
            <?php $sponsored = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.sponsored.image', 1);
            if (!empty($sponsored)) { ?>
              <div class="sitestore_profile_sponsorfeatured" style='background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.sponsored.color', '#fc0505'); ?>;'>
                <?php echo $this->translate('SPONSORED'); ?>
              </div>
            <?php } ?>
          <?php endif; ?>
          <div class='sitestore_photo <?php if ($this->can_edit) : ?>sitestore_photo_edit_wrapper<?php endif; ?>'>
            <?php if (!empty($this->can_edit)) : ?>
              <a href="<?php echo $this->url(array('action' => 'profile-picture', 'store_id' => $this->sitestore->store_id), 'sitestore_dashboard', true) ?>" class="sitestore_photo_edit">  	  
                <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/images/edit_pencil.png', '') ?>
                <?php echo $this->translate('Change Picture'); ?>
              </a>
            <?php endif; ?>
            <table>
              <tr valign="middle">
                <td>
                  <?php echo $this->itemPhoto($this->sitestore, 'thumb.profile', '', array('align' => 'left')); ?>
                </td>
              </tr>
            </table>
          </div>
          <?php if (!empty($this->sitestore->featured)): ?>
            <?php $feature = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.feature.image', 1);
            if (!empty($feature)) { ?>
              <div class="sitestore_profile_sponsorfeatured"  style='background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.featured.color', '#0cf523'); ?>;'>
                <?php echo $this->translate('FEATURED'); ?>
              </div>
            <?php } ?>
          <?php endif; ?>
        </div>	
      </div>
    <?php endif; ?>

    <div class="sp_coverinfo_buttons">
      <?php if (in_array('likeButton', $this->showContent)): ?>
        <div>
					<?php echo $this->content()->renderWidget("seaocore.like-button"); ?>
				</div>	
      <?php endif; ?>
      <?php if (in_array('followButton', $this->showContent)): ?>
				<div>
	        <?php echo $this->content()->renderWidget("seaocore.seaocore-follow"); ?>
				</div>	
      <?php endif; ?>
      <?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremember')) : ?>
				<?php $joinMembers = Engine_Api::_()->getDbTable('membership', 'sitestore')->hasMembers($this->viewer_id, $this->sitestore->store_id);
				if (empty($joinMembers) && in_array('joinButton', $this->showContent) && $this->viewer_id != $this->sitestore->owner_id && !empty($this->allowStore)): ?>
					<div>
					<?php if (!empty($this->viewer_id)) : ?>
						<?php if (!empty($this->sitestore->member_approval)): ?>
              <div class="seaocore_button">
                <a  href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->escape($this->url(array( 'action' => 'join', 'store_id' => $this->sitestore->store_id), 'sitestore_profilestoremember', true)); ?>'); return false;" ><span><?php echo $this->translate("Join Store"); ?></span></a>
              </div> 
						<?php else: ?>
              <div class="seaocore_button">
                <a href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->escape($this->url(array( 'action' => 'request', 'store_id' => $this->sitestore->store_id), 'sitestore_profilestoremember', true)); ?>'); return false;" ><span><?php echo $this->translate("Join Store"); ?></span></a>
              </div>
						<?php endif; ?>
					<?php endif; ?>
					</div>
				<?php endif; ?>
				
				<?php if (in_array('addButton', $this->showContent)): ?>
					<?php $hasMembers = Engine_Api::_()->getDbTable('membership', 'sitestore')->hasMembers($this->viewer_id, $this->sitestore->store_id, $params = 'Invite'); ?>
					<?php if (!empty($hasMembers) && !empty($this->can_edit)) : ?>
					<div class="seaocore_button">
						<a href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->escape($this->url(array( 'action' => 'invite-members', 'store_id' => $this->sitestore->store_id), 'sitestore_profilestoremember', true)); ?>'); return false;" ><i class="add_people"></i><span><?php echo $this->translate("Add People"); ?></span></a>	
					</div>
					<?php elseif (!empty($hasMembers) && empty($this->sitestore->member_invite)): ?>
					<div class="seaocore_button">
						<a href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->escape($this->url(array( 'action' => 'invite-members', 'store_id' => $this->sitestore->store_id), 'sitestore_profilestoremember', true)); ?>'); return false;" ><i class="add_people"></i><span><?php echo $this->translate("Add People"); ?></span></a>
					</div>
					<?php endif; ?>
				<?php endif; ?>
			<?php endif; ?>
			
    </div>
    <div class="sp_coverinfo_status">
      <?php if (in_array('title', $this->showContent)): ?>
        <h2><?php echo $this->sitestore->getTitle() ?></h2>
      <?php endif; ?>
      <div class="sp_coverinfo_stats seaocore_txt_light">
        <?php if (in_array('likeCount', $this->showContent) && isset($this->sitestore->like_count)): ?>
          <a id= "sitestore_store_num_of_like_<?php echo $this->sitestore->store_id;?>" href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->escape($this->url(array('module' => 'seaocore', 'controller' => 'like', 'action' => 'likelist', 'resource_type' => 'sitestore_store', 'resource_id' => $this->sitestore->store_id, 'call_status' => 'public'), 'default', true)); ?>'); return false;" ><?php echo $this->translate(array('%s like', '%s likes', $this->sitestore->like_count),$this->locale()->toNumber($this->sitestore->like_count)); ?></a>
        <?php endif; ?>
        
        <?php if (in_array('followCount', $this->showContent) && isset($this->sitestore->follow_count)): ?>
					<?php if (in_array('likeCount', $this->showContent) && isset($this->sitestore->like_count)): ?>
						&middot; 
					<?php endif; ?>
					<a id= "sitestore_store_num_of_follows_<?php echo $this->sitestore->store_id;?>" href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->escape($this->url(array('module' => 'seaocore', 'controller' => 'follow', 'action'=>'get-followers', 'resource_type'	=> 'sitestore_store', 'resource_id' => $this->sitestore->store_id, 'format' => 'smoothbox', 'call_status' => 'public'), 'default'	, true)); ?>'); return false;" ><?php echo $this->translate(array('%s follower', '%s followers', $this->sitestore->follow_count),$this->locale()->toNumber($this->sitestore->follow_count)); ?></a>	
        <?php endif; ?>

				<?php if (in_array('memberCount', $this->showContent) && isset($this->sitestore->member_count) && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremember')): ?>
					<?php //if (in_array('likeCount', $this->statistics) && isset($this->sitestore->like_count)): ?>
					&middot; 
					<?php //endif; ?>
						<?php $memberTitle = Engine_Api::_()->getApi('settings', 'core')->getSetting( 'storemember.member.title' , 1);
						if ($this->sitestore->member_title && $memberTitle) {
							if ($this->sitestore->member_count == 1) : ?>
							<a href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->escape($this->url(array('action'=>'member-join', 'store_id' => $this->sitestore->store_id, 'params' => 'memberJoin', 'format' => 'smoothbox'), 'sitestoremember_approve'	, true)); ?>'); return false;" ><?php echo $this->sitestore->member_count . ' member'; ?></a>
						<?php	else: ?>
						<a href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->escape($this->url(array('action'=>'member-join', 'store_id' => $this->sitestore->store_id, 'params' => 'memberJoin', 'format' => 'smoothbox'), 'sitestoremember_approve'	, true)); ?>'); return false;" ><?php echo $this->sitestore->member_count . ' ' .  $this->sitestore->member_title;?></a>
						<?php 	endif; ?>
						<?php } else { ?>
						<a href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->escape($this->url(array('action'=>'member-join', 'store_id' => $this->sitestore->store_id, 'params' => 'memberJoin', 'format' => 'smoothbox'), 'sitestoremember_approve'	, true)); ?>'); return false;" ><?php echo $this->translate(array('%s member', '%s members', $this->sitestore->member_count),$this->locale()->toNumber($this->sitestore->member_count)); ?></a>
					  <?php 	} ?>
				<?php endif; ?>
				
      </div>
    </div>
  </div>
  <?php endif; ?>
</div>
<div class="clr"></div>
<script type="text/javascript">
    document.seaoCoverPhoto= new SitestoreCoverPhoto({
      block :$('sitestore_cover_photo'),
      photoUrl:'<?php echo $this->url(array('action' => 'get-cover-photo', 'store_id' => $this->sitestore->store_id), 'sitestore_profilestore', true); ?>',
      buttons:'seao_cover_options',
      positionUrl :'<?php echo $this->url(array('action' => 'reset-position-cover-photo', 'store_id' => $this->sitestore->store_id), 'sitestore_dashboard', true); ?>',
      position :<?php  echo $this->cover_params ? json_encode($this->cover_params): json_encode(array('top' => 0, 'left' => 0)); ?>
    });
  </script>

<script type="text/javascript">
	function showSmoothBox(url) {
		Smoothbox.open(url);
	}
</script>