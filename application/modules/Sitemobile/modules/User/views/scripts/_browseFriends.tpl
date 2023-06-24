<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: _browseUsers.tpl 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */
/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
?>
<?php $viewer = Engine_Api::_()->user()->getViewer(); ?>
<?php $this->addHelperPath(APPLICATION_PATH . '/application/modules/Sitemobile/modules/User/View/Helper', 'User_View_Helper'); ?>
<?php if($this->autoContentLoad == 1) : ?>

<?php if(!empty ($this->totalUsers)): ?>
<div class="ui-member-list-head">
  <?php echo $this->translate(array('%s friend found.', '%s friends found.', $this->totalUsers), $this->locale()->toNumber($this->totalUsers)) ?>
</div>
<?php else: ?>

<div class="tip">
  <span>
    <?php echo $this->translate("You currently do not have any friends here."); ?>
  </span>
</div>
<?php endif; ?>
<?php $viewer = Engine_Api::_()->user()->getViewer(); ?>
<div class="sm-content-list">
  <ul id="browsemembers_ul" class="ui-member-list friends-list" data-role="listview" data-icon="none">
  <?php endif;?>
  
    <?php foreach ($this->friends as $membership):
      if (!isset($this->friendUsers[$membership->resource_id]))
        continue;
      $member = $this->friendUsers[$membership->resource_id];
      ?>
      <li>
        <?php if ($this->userFriendshipSM($member)) : ?>
          <div class="ui-item-member-action">
            <?php echo $this->userFriendshipSM($member) ?>
            <a href="<?php echo $this->url(array('action' => 'compose', 'to' => $membership->resource_id), 'messages_general', true); ?>" class="userlink userlink-message"></a>             
          </div>
        <?php endif; ?>
        <a href="<?php echo $member->getHref() ?>">
          <?php echo $this->itemPhoto($member, 'thumb.icon') ?>
          <div class="ui-list-content">
            <h3><?php echo $member->getTitle() ?></h3>
          </div>	
        </a>
      </li>
    <?php endforeach; ?>  
  <?php if($this->autoContentLoad == 1):?> 
  </ul>
  <div class='browsefriends_viewmore' id="browsefriends_viewmore" style='display:none;'>
    <div class="feeds_loading" id="feed_loading-sitefeed" >
      <i class="icon_loading"></i>
    </div>
  </div>
</div>	
<?php endif;?>
<script type="text/javascript">

  sm4.core.runonce.add(function() {
     
     var activepage_id = sm4.activity.activityUpdateHandler.getIndexId();
    sm4.core.Module.core.activeParams[activepage_id] = {'currentPage' : '<?php echo sprintf('%d', $this->page) ?>', 'totalPages' : '<?php echo sprintf('%d', $this->totalPages) ?>', 'formValues' : {}, 'contentUrl' : sm4.core.baseUrl + 'friends', 'activeRequest' : false, 'container' : 'browsemembers_ul' };   
  });
   
</script>