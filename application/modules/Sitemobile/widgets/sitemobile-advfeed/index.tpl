<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
?>
<?php if( !empty($this->getUpdate) && $this->firstid): // if this is for the get live update ?>
   <script type="text/javascript">
     sm4.activity.activityUpdateHandler.updateOptions({last_id : <?php echo sprintf('%d', $this->firstid) ?>});
   </script>
<?php endif; ?>
 <?php if (empty($this->getUpdate) && empty($this->checkUpdate)):?>  
 <script type="text/javascript">  
  sm4.core.runonce.add(function() {
    sm4.activity.makeFeedOptions('sitefeed', <?php echo json_encode($this->allParams);?>, <?php echo json_encode($this->attachmentsURL);?>); 
  });    
</script>
<?php endif;?>
<?php
if (!empty($this->feedOnly) && empty($this->checkUpdate)): // Simple feed only for AJAX
  echo $this->advancedActivityLoopSM($this->activity, array(
      'action_id' => $this->action_id,
      'viewAllComments' => $this->viewAllComments,
      'viewAllLikes' => $this->viewAllLikes,
      'feedOnly' => $this->feedOnly,
      'groupedFeeds' => $this->groupedFeeds,
      'getUpdate' => $this->getUpdate,
      'hashtag' => $this->hashtag
  ));
  return; // Do no render the rest of the script in this mode
endif;

?>
   
<?php if( !empty($this->checkUpdate) ): // if this is for the live update
  if ($this->activityCount):
  ?>
          <a href='javascript://' data-role="button"  data-theme="d" class="ui-link" onclick='sm4.activity.activityUpdateHandler.getFeedUpdate()'>
              <?php echo $this->translate(array(
                  '%d new update.',
                  '%d new updates.',
                  $this->activityCount),
                $this->activityCount)
                      ?>
            </a>

     <?php   endif;
  return; // Do no render the rest of the script in this mode
endif; ?>

<?php include APPLICATION_PATH.'/application/modules/Sitemobile/modules/Advancedactivity/views/scripts/_composer.tpl';?>
 
 <?php if ($this->post_failed == 1): ?>
  <div class="tip">
    <span>
  <?php $url = $this->url(array('module' => 'user', 'controller' => 'settings', 'action' => 'privacy'), 'default', true) ?>
  <?php echo $this->translate('The post was not added to the feed. Please check your %1$sprivacy settings%2$s.', '<a href="' . $url . '">', '</a>') ?>
    </span>
  </div>
<?php endif; ?>

<script type="text/javascript">  
  sm4.core.runonce.add(function() {  
    var url = sm4.core.baseUrl + 'advancedactivity/friends/suggest';
    sm4.core.Module.autoCompleter.attach("aff_mobile_aft_search", url, {
      'singletextbox': false, 
      'limit':10, 
      'minLength': 1, 
      'showPhoto' : true, 
      'search' : 'search'
    }, 'toValues-temp');
  }); 
      
      
</script>   


  <?php // If requesting a single action and it doesn't exist, show error ?>
<?php $nofeed_group = false;?>
<div id="showadvfeed-sitefeed">
    <?php if (!$this->activity && $this->actionFilter =='all'): ?>

    <?php if ($this->action_id): ?>
      <h2><?php echo $this->translate("Activity Item Not Found") ?></h2>
      <p>
          <?php echo $this->translate("The page you have attempted to access could not be found.") ?>
      </p>
    <?php return;
  else: 
      $nofeed_group = true;
     ?>
      <div class="tip">
        <span>
    <?php $message = $this->search ? 'No results found!' : "Nothing has been posted here yet - be the first!"; ?>
    <?php echo $this->translate($message) ?>
        </span>
      </div>
    <?php //delete feed popup work.  ?>
      <div data-role="popup" id="popupDialog" data-overlay-theme="a" data-theme="c" data-dismissible="false" style="max-width:400px;" class="ui-corner-all">
        <div data-role="header" data-theme="a" class="ui-corner-top">
          <h1><?php echo $this->translate('Delete Activity Item?'); ?></h1>
        </div>
        <div data-role="content" data-theme="d" class="ui-corner-bottom ui-content">
          <h3 class="ui-title"></h3>
          <p><?php echo $this->translate('Are you sure that you want to delete this activity item? This action cannot be undone.') ?></p>

          <a href="#" data-role="button" data-inline="true" data-rel="back" data-transition="flow" data-theme="b" onclick="javascript:sm4.activity.activityremove()"><?php echo $this->translate("Delete"); ?></a>
          <a href="#" data-role="button" data-inline="true" data-rel="back" data-theme="c"><?php echo $this->translate("Cancel"); ?></a>
        </div>
      </div>
      <div data-role="popup" id="popupDialog-Comment" data-overlay-theme="a" data-theme="c" data-dismissible="false" style="max-width:400px;" class="ui-corner-all">
        <div data-role="header" data-theme="a" class="ui-corner-top">
          <h1><?php echo $this->translate('Delete Comment?'); ?></h1>
        </div>
        <div data-role="content" data-theme="d" class="ui-corner-bottom ui-content">
          <h3 class="ui-title"></h3>
          <p><?php echo $this->translate('Are you sure that you want to delete this comment? This action cannot be undone.'); ?></p>              

          <a href="#" data-role="button" data-inline="true" data-rel="back" data-transition="flow" data-theme="b" onclick="javascript:sm4.activity.activityremove()"><?php echo $this->translate("Delete"); ?></a>
          <a href="#" data-role="button" data-inline="true" data-rel="back" data-theme="c" onclick="$.mobile.activePage.addClass('dnone');"><?php echo $this->translate("Cancel"); ?></a>
        </div>
      </div>
    <?php endif; ?>
  <?php endif; ?>
      
      <!--To display suggestion widget-->
      <div class="add_other_content_feed"></div>
  <?php // SHOW SITE ACTIVITY FEED. ?>
  <?php if ($this->activity || $this->actionFilter !='all'): ?>
    <?php
    if (!empty($this->subjectGuid) && !$this->action_id):
      echo $this->partial('application/modules/Sitemobile/modules/Advancedactivity/views/scripts/widgets-feed/profile-content-tabs.tpl', 'advancedactivity', array());
    elseif (empty($this->subjectGuid) && ($this->enableContentTabs || $this->canCreateCustomList)):
      echo $this->partial('application/modules/Sitemobile/modules/Advancedactivity/views/scripts/widgets-feed/content-tabs.tpl', 'advancedactivity', array(
          'filterTabs' => $this->filterTabs,
          'actionFilter' => $this->actionFilter
      ));
    endif;
    ?>
  <?php endif; ?> 

  <div id="feed-update" style="display:none">
    
  </div>
<div id="aaf_feed_update_loading" class='aaf_feed_loading' style="display: none;">
  <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitemobile/modules/Core/externals/images/loading.gif' alt="Loading" />
  <?php echo $this->translate("Updating ...") ?>
</div>
<?php if (!$this->activity && $this->actionFilter !='all'): ?>
       <ul class='feeds' id="activity-feed-sitefeed"></ul>
      <?php endif; ?>
  <?php
  echo $this->advancedActivityLoopSM($this->activity, array(
      'action_id' => $this->action_id,
      'viewAllComments' => $this->viewAllComments,
      'viewAllLikes' => $this->viewAllLikes,
      'feedOnly' => $this->feedOnly,
      'groupedFeeds' => $this->groupedFeeds,
      'hashtag' => $this->hashtag
  ));
  ?>

  <div class="feed_viewmore" id="feed_viewmore-sitefeed" style="display: none;">
<?php
echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array(
    'id' => 'feed_viewmore_link-sitefeed',
    'class' => 'ui-btn-default icon_viewmore'
))
?>
  </div>

  <div class="feeds_loading" id="feed_loading-sitefeed" style="display: none;">
    <i class="icon_loading"></i>
  </div>

  <div class="feeds_no_more tip" id="feed_no_more-sitefeed" style="display: <?php echo ( (!$this->endOfFeed || !empty($this->action_id)) || $nofeed_group) ? 'none':''?>;">
    <span>
<?php echo $this->translate($this->search ? "There are no more results to show." :"There are no more posts to show.") ?>
    </span>  
  </div>
</div>
<div class="iscroll-pullup sm_aaf_pullUp dnone">
  <span class="iscroll-pull-icon"></span><span class="iscroll-pull-label" data-iscroll-loading-text="<?php echo $this->translate("Loading ...") ?>" data-iscroll-pulled-text="<?php echo $this->translate('Release to load more...') ?>"><?php echo $this->translate('Pull up to load more...') ?></span>
</div>
<div class="iscroll-pulldown sm_aaf_pullDown dnone">
  <span class="iscroll-pull-icon"></span><span class="iscroll-pull-label" data-iscroll-loading-text="<?php echo $this->translate("Updating ...") ?>" data-iscroll-pulled-text="<?php echo $this->translate('Release to refresh...') ?>"><?php echo $this->translate('Pull down to refresh...') ?></span>
</div>
<script type="text/javascript">
  <?php if ($this->updateSettings && !$this->action_id): // wrap this code around a php if statement to check if there is live feed update turned on ?>
  var smActivityPageShow = function(){
  $(document).off('pagebeforeshow', smActivityPageShow);
   sm4.activity.activityUpdateHandler.initialize({
     url :'<?php echo $this->url(array('module' => 'core', 'controller' => 'widget', 'action' => 'index', 'content_id' => $this->identity), 'default', true) ?>',
     last_id: <?php echo sprintf('%d', $this->firstid) ?>,
  //   showImmediately : "<?php echo $this->aafShowImmediately? true : false; ?>",
     delay : <?php echo $this->updateSettings;?>
   }); 
  };
  $(document).on('pagebeforeshow', smActivityPageShow);
  <?php endif;?>
</script>