<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesadvancedactivity
 * @package    Sesadvancedactivity
 * @copyright  Copyright 2016-2017 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: _activityText.tpl  2017-01-12 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

?>
<?php if( empty($this->actions) ) {
  $actions = array();
} else {
   $actions = $this->actions;
} 
 $attachmentShowCount = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesadvancedactivity.attachment.count',5);
?>
<?php $this->headScript()
           ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesadvancedactivity/externals/scripts/core.js')
           ->appendFile($this->layout()->staticBaseUrl . 'externals/flowplayer/flowplayer-3.2.13.min.js')
           ->appendFile($this->layout()->staticBaseUrl . 'externals/html5media/html5media.min.js')
           ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesadvancedactivity/externals/scripts/editComposer.js')?>

<?php if( !$this->getUpdate && ($this->ulInclude)):
  $date = '';
 ?>
<div class="sesact_feed sesbasic_bxs sesbasic_clearfix">
  <ul class='feed sesbasic_clearfix sesbasic_bxs' id="activity-feed">
  <?php endif ?>
  <?php
    //google key
    $googleKey = Engine_Api::_()->getApi('settings', 'core')->getSetting('ses.mapApiKey', '');
    if($this->isMemberHomePage){
      $adsEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesadvancedactivity.adsenable', 0);
      $peopleymkEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesadvancedactivity.peopleymk', 1);
      $adsRepeat = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesadvancedactivity.adsrepeatenable', 0);
      $pymkrepeatenable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesadvancedactivity.pymkrepeatenable', 0);
      $adsRepeatTime = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesadvancedactivity.adsrepeattimes', 15);
      $peopleymkrepeattimes = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesadvancedactivity.peopleymkrepeattimes', 5);
      $islanguageTranslate = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesadvancedactivity.translate', 0);
      $languageTranslate = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesadvancedactivity.language', 'en');
      $contentCount = $this->contentCount;
    }
    foreach( $actions as $action ): //(goes to the end of the file)
    
     //google ads code start here
     if($this->isMemberHomePage && $adsEnable && ($contentCount && $contentCount%$adsRepeatTime == 0) && ($adsRepeat || (!$adsRepeat && $contentCount/$adsRepeatTime == 1))){
     ?>
     <li class="sesbasic_clearfix">
     <?php    
       $content =  $this->content()->renderWidget('sesadvancedactivity.ad-campaign');
       echo preg_replace('#<script(.*?)>(.*?)</script>#is', '', $content)
     ?>
     <script type="application/javascript">
     en4.core.runonce.add(function() {
        var url = '<?php echo $this->url(array('module' => 'core', 'controller' => 'utility', 'action' => 'advertisement'), 'default', true) ?>';
        var processClick = window.processClick = function(adcampaign_id, ad_id) {
          (new Request.JSON({
            'format': 'json',
            'url' : url,
            'data' : {
              'format' : 'json',
              'adcampaign_id' : adcampaign_id,
              'ad_id' : ad_id
            }
          })).send();
        }
      });
     </script>
    </li>
    <?php
    }

    //People You may know plugin widget
    if($this->isMemberHomePage && Engine_Api::_()->sesbasic()->isModuleEnable('sespymk') && $peopleymkEnable && ($contentCount && $contentCount%$peopleymkrepeattimes == 0) && ($pymkrepeatenable || (!$pymkrepeatenable && $contentCount/$peopleymkrepeattimes == 1))){
    ?>
    <?php
      echo $this->content()->renderWidget('sespymk.suggestion-carousel', array('showdetails' => array('friends', 'mutualfriends'), 'viewType' => 'horizontal', 'height' => '220', 'heightphoto' => '150', 'width' => '150', 'itemCount' => '15', 'onlyphotousers' => 1, 'anfheader' => 1, 'itemCount' => 8, 'anffeed' => 1));
    ?>
    <?php } 
    
    //google ads code end here
      try { // prevents a bad feed item from destroying the entire page
        // Moved to controller, but the items are kept in memory, so it should not hurt to double-check
        if( !$action->getTypeInfo()->enabled ) continue;
        if( !$action->getSubject() || !$action->getSubject()->getIdentity() ) continue;
        if( !$action->getObject() || !$action->getObject()->getIdentity() ) continue;
        
        ob_start();
      ?>
    <?php if($this->isOnThisDayPage){ ?>
     <?php if($date != $action->date){ ?>
      <li class="onthisday">
        <?php
          $date1=date_create(date('Y-m-d',strtotime($action->date)));
          $date2=date_create(date('Y-m-d'));
          $date_diff = date_diff($date1,$date2);
          if($date_diff == 1)
            $year = 'YEAR';
          else 
            $year = 'YEARS';
          echo $date_diff->y." ".$year." AGO TODAY";
        ?>
      </li>
    <?php } ?>
    <?php $date = $action->date; ?>
    <?php } ?>
    <?php if( !$this->noList ): ?>
    	<li id="activity-item-<?php echo $action->action_id ?>" data-activity-feed-item="<?php echo $action->action_id ?>" class="sesbasic_clearfix _photo<?php echo $this->userphotoalign; ?>"><?php endif; ?>
      <?php $this->commentForm->setActionIdentity($action->action_id) ?>
      <?php if(!$this->isOnThisDayPage && !Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sesadvancedcomment')){ ?>
      <script type="text/javascript">
        (function(){
          var action_id = '<?php echo $action->action_id ?>';
          en4.core.runonce.add(function(){
            $('activity-comment-body-' + action_id).autogrow();
            en4.activity.attachComment($('activity-comment-form-' + action_id));
          });
        })();
      </script>
      <?php } ?>
      <div class="sesact_feed_header sesbasic_clearfix">
      <?php // User profile photo ?>
      <div class='sesact_feed_item_photo'>
        <?php echo $this->htmlLink($action->getSubject()->getHref(), $this->itemPhoto($action->getSubject(), 'thumb.profile', $action->getSubject()->getTitle(),array('class'=>'ses_tooltip','data-src'=>$action->getSubject()->getGuid())))?>
      </div>
      <div class="sesact_feed_header_cont sesbasic_clearfix">
        <?php if($this->viewer()->getIdentity() && (empty($this->filterFeed) || $this->filterFeed != 'hiddenpost')){ ?>
          <div class="sesact_feed_options sesact_pulldown_wrapper">
            <a href="javascript:void(0);" class="sesact_feed_options_btn"><i class="fa fa-angle-down"></i></a>
            <div class="sesact_pulldown">
              <div class="sesact_pulldown_cont">
                <ul>
                 <?php if(!$this->isOnThisDayPage && $this->viewer()->getIdentity() && ($action->getTypeInfo()->type == 'share' || $action->getTypeInfo()->type == 'status' || strpos($action->getTypeInfo()->type ,'post') !== false ) && (
                $this->activity_moderate || (
                  $this->allow_delete && (
                    ('user' == $action->subject_type && $this->viewer()->getIdentity() == $action->subject_id) ||
                    ('user' == $action->object_type && $this->viewer()->getIdentity()  == $action->object_id)
                  )
                )
            ) ): ?>
                <?php if($action->params['type'] != 'facebookpostembed'): ?>
                  <li><a id="sesact_edit_<?php echo $action->getIdentity(); ?>" href="javascript:;" class="sessmoothbox" data-url="<?php echo $this->url(array('module'=> 'sesadvancedactivity', 'controller'=> 'index', 'action' => 'edit-post', 'action_id' => $action->action_id),'default',true); ?>"><span><?php echo $this->translate("Edit Feed");?></span></a></li>
                <?php endif; ?>
               <?php endif; ?>
               <?php if( $this->viewer()->getIdentity()  && (
                $this->activity_moderate || (
                  $this->allow_delete && (
                    ('user' == $action->subject_type && $this->viewer()->getIdentity() == $action->subject_id) ||
                    ('user' == $action->object_type && $this->viewer()->getIdentity()  == $action->object_id)
                  )
                )
            ) ): ?>
                  <li><a class="sessmoothbox" href="javascript:;" data-url="<?php echo $this->url(array('module'=> 'sesadvancedactivity', 'controller'=> 'index', 'action' => 'delete', 'action_id' => $action->action_id),'default',true); ?>"><span><?php echo $this->translate("Delete Feed");?></span></a></li>
             <?php endif; ?>
             <?php if(!$action->schedule_time){ ?>
                 <?php if(Engine_Api::_()->getDbTable('savefeeds','sesadvancedactivity')->isSaved(array('action_id'=>$action->getIdentity(),'user_id'=>$this->viewer()->getIdentity()))){ ?>
                  <li><a href="javascript:;" class="unsave_feed_adv" data-save="<?php echo $this->translate('Save Feed'); ?>" data-unsave="<?php echo $this->translate('Unsave Feed'); ?>" data-actionid="<?php echo $action->getIdentity(); ?>"><span><?php echo $this->translate("Unsave Feed");?></span></a></li>
                 <?php }else{ ?>
                  <li><a href="javascript:;" class="save_feed_adv"  data-save="<?php echo $this->translate('Save Feed'); ?>" data-unsave="<?php echo $this->translate('Unsave Feed'); ?>" data-actionid="<?php echo $action->getIdentity(); ?>"><span><?php echo $this->translate("Save Feed");?></span></a></li>
                 <?php } ?>
                  <li class="_sep"></li>
                  <li><a href="<?php echo $action->getHref(); ?>" class="sesadv_feed_link"><span><?php echo $this->translate("Feed Link");?></span></a></li>
                <?php if(!$this->isOnThisDayPage){ ?>
                 <?php if($this->viewer()->getIdentity() == $action->getSubject()->getIdentity()){ 
                       if($action->commentable)
                         $text = $this->translate('Disable Comments');
                       else
                          $text = $this->translate('Enable Comments');
                 ?>
                  <li><a href="javascript:;" class="sesadvcommentable" data-commentable="<?php echo $action->commentable; ?>" data-save="<?php echo  $this->translate('Enable Comments'); ?>"  data-unsave="<?php echo  $this->translate('Disable Comments'); ?>" data-href="sesadvancedactivity/ajax/commentable/action_id/<?php echo $action->getIdentity(); ?>"><span><?php echo $text;?></span></a></li>
                <?php } ?>
               <?php } ?>
               <?php if($this->viewer()->getIdentity() != $action->getSubject()->getIdentity()){ ?>
                  <li><a href="javascript:;" class="sesadv_hide_feed" data-name="<?php echo $action->getSubject()->getTitle(); ?>" data-actionid="<?php echo $action->getIdentity(); ?>"><span><?php echo $this->translate("Hide Feed");?></span></a></li>
                  <li><a href="javascript:;" class="sesadv_hide_feed_all sesadv_hide_feed_all_<?php echo $action->getIdentity(); ?>" data-actionid="<?php echo $action->getIdentity(); ?>" data-name="<?php echo $action->getSubject()->getTitle(); ?>"><span><?php echo $this->translate("Hide all by ".$action->getSubject()->getTitle());?></span></a></li>
                  
                  <?php $reportEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesadvancedactivity.reportenable', 1);
                  if($reportEnable): ?>
                    <li>
                      <?php echo $this->htmlLink(Array("module"=> "core", "controller" => "report", "action" => "create", "route" => "default", "subject" => $action->getGuid()), '<span>'. $this->translate("Report") . '</span>', array('onclick' => "openSmoothBoxInUrl(this.href);return false;" ,"class" => "")); ?>
                    </li>
                  <?php endif; ?>
               <?php } ?>
             <?php }else{ ?>
              <li><a href="javascript:;" class="sesadv_reschedule_post" data-value="<?php echo date('d-m-Y H:i:s',strtotime($action->schedule_time)); ?>" data-actionid="<?php echo $action->getIdentity(); ?>"><span><?php echo $this->translate("Reschedule Post");?></span></a></li>
             <?php } ?>
                </ul>
              </div>													
            </div>
          </div>
        <?php } ?>
        <?php // Main Content ?>
          <?php $contentData = $this->getContent($action); ?>
          <div class="sesact_feed_header_title <?php echo ( empty($action->getTypeInfo()->is_generated) ? 'feed_item_posted' : 'feed_item_generated' ) ?>">					<?php if(strip_tags($action->body) && $islanguageTranslate){ ?>
            <span class="floatR sesact_feed_header_tl_link">
              <a href="javascript:void(0);" onClick="socialSharingPopUp('https://translate.google.com/#auto/<?php echo $languageTranslate; ?>/<?php echo urlencode(strip_tags($action->body)); ?>','Google');return false;"><?php echo $this->translate("Translate"); ?></a>
            </span>	
          <?php } 
          if($this->filterFeed == 'hiddenpost'){ ?>
          	<div class="sesact_feed_options sesact_pulldown_wrapper">
            	<a href="javascript:void(0);" class="allowed_hide_post_sesadv sesadv_tooltip sesact_feed_options_btn" data-src="<?php echo $action->getIdentity() ?>" title="Allowed"><i class="fa fa-circle-o"></i></a>
           	</div>
          <?php } ?>
           <?php echo isset($contentData[0]) ? $contentData[0] : '' ; ?>
              <?php $location = Engine_Api::_()->getDbTable('locations','sesbasic')->getLocationData('activity_action',$action->getIdentity()); ?>
              <?php $members = Engine_Api::_()->getDbTable('tagusers','sesadvancedactivity')->getActionMembers($action->getIdentity()); ?>
              <?php if($memberTotalCount = count($members)){ ?>
                  with 
                  <?php 
                      $counterMember = 1;
                      foreach($members as $member){
                        $user = Engine_Api::_()->getItem('user',$member['user_id']);
                        if(!$user)
                          contunue;
                   ?>                    
                    <?php if($counterMember == 2 && $memberTotalCount == 2){ ?>
                      and
                    <?php }else if($counterMember == 2 && $memberTotalCount > 2){ ?>
                     and
                      <a href="javascript:;" class="sessmoothbox" data-url="sesadvancedactivity/ajax/tag-people/action_id/<?php echo $action->getIdentity(); ?>"><?php echo $this->translate(($memberTotalCount - 1).' others') ?></a>
                    <?php 
                      break;
                    } ?>
                    <a href="<?php echo $user->getHref(); ?>" class="ses_tooltip" data-src="<?php echo $user->getGuid(); ?>"><?php echo $user->getTitle(); ?></a>
                  <?php 
                    $counterMember++;
                      } ?>
               
              <?php } ?>
              <?php if($location){ ?>
                  in <a href="<?php echo $this->url(array('resource_id' => $action->getIdentity(),'resource_type'=>$action->getType(),'action'=>'get-direction'), 'sesbasic_get_direction', true); ?>" onClick="openSmoothBoxInUrl(this.href);return false;"><?php echo $location->venue;  ?></a>
              <?php } ?>
          </div> 
<?php
        $icon_type = 'activity_icon_'.$action->type;
        list($attachment) = $action->getAttachments();
        if( is_object($attachment) && $action->attachment_count > 0 && $attachment->item ):
          $icon_type .= ' item_icon_'.$attachment->item->getType() . ' ';
        endif;
?>
            
          <div class="sesact_feed_header_btm">
          	<i class="sesact_feed_header_btm_icon <?php echo $icon_type ?>"></i>
            <?php echo $this->timestamp($action->getTimeValue()) ?>
            <span class="sesbasic_text_light">&middot;</span> 
            <?php if($action->privacy == 'onlyme'){
                    $classPrivacy = 'sesact_me';
                    $titlePrivacy = 'Only Me';
                  }else if($action->privacy == 'friends'){
                    $classPrivacy = 'sesact_friends';
                    if($action->getSubject()->getIdentity() != $this->viewer()->getIdentity())
                      $titlePrivacy = ucwords($action->getSubject()->getTitle()).'\'s friends';
                    else
                      $titlePrivacy = 'Your\'s friends';
                  }else if($action->privacy == 'networks'){
                    $classPrivacy = 'sesact_network';
                    $titlePrivacy = 'Friends And Networks';
                  }else if(strpos($action->privacy,'network_list') !== false){
                    $classPrivacy = 'sesact_network';
                    $explode = explode(',',$action->privacy);
                    $titlePrivacy = '';
                    $counter = 1;
                    foreach($explode as $ex){
                      $item = Engine_Api::_()->getItem('network',str_replace('network_list_','',$ex));
                      if(!$item)
                        continue;
                      $titlePrivacy = $item->getTitle().', '.$titlePrivacy;
                      $counter++;
                    }
                    $titlePrivacy = rtrim($titlePrivacy,', ');
                    if($counter > 2)
                      $titlePrivacy = 'Multiptle Network ( '.$titlePrivacy.')';
                  }else if(strpos($action->privacy,'members_list') !== false || strpos($action->privacy,'member_list') !== false){
                    $classPrivacy = 'sesact_list';
                    $explode = explode(',',$action->privacy);
                    $titlePrivacy = '';
                    $counter = 1;
                    foreach($explode as $ex){
                      $item = Engine_Api::_()->getItem('user_list',str_replace('member_list_','',$ex));
                      if(!$item)
                        continue;
                      $titlePrivacy = $item->getTitle().', '.$titlePrivacy;
                      $counter++;
                    }
                    $titlePrivacy = rtrim($titlePrivacy,', ');
                    if($counter > 2)
                      $titlePrivacy = 'Multiptle Lists ( '.$titlePrivacy.')';
                      
                  }else{
                    $classPrivacy = 'sesact_public';
                    $titlePrivacy = 'Everyone';
                  }
             ?>
            <span class="sesact_feed_header_pr _user"><i class="sesadv_tooltip sesbasic_text_light <?php echo $classPrivacy; ?>" title="<?php echo 'Shared with: '.$titlePrivacy; ?>"></i></span>
          </div>
        
        </div>
      </div>
      <div class='feed_item_body sesbasic_clearfix'>

        <?php if(!empty($contentData[1])) { ?>
          <span class="sesact_feed_item_bodytext" >
            <?php  
            if(isset($contentData[1])) {
              echo $contentData[1];
            } else {
              echo '';
            } ?>
          </span> 
        <?php } ?>
        <?php // Main Content ?>        
        <?php 
         $buysellActive = false;
         $buysellattachment = '';
         $action->intializeAttachmentcount();
        if($action->type == 'post_self_buysell' || ($action->attachment_count == 1 && count($action->getAttachments()) == 1 && $buysellattachment = current($action->getAttachments()))){
          if($action->type == 'post_self_buysell' || (!empty($buysellattachment->item) && $buysellattachment->item->getType() == 'sesadvancedactivity_buysell')){
            if(empty($buysellattachment)){
              $buysell = $action->getBuySellItem();
            }else{
              $changeAction = $action;
              $buysellAction = $buysellattachment->meta->action_id;
              $buysell = Engine_Api::_()->getItem('sesadvancedactivity_buysell',$buysellattachment->meta->id);
              $action = Engine_Api::_()->getItem('sesadvancedactivity_action',$buysell->action_id);  
              $buysellattachment = '';
              
            }
              if($buysell){
              $locationBuySell = Engine_Api::_()->getDbTable('locations','sesbasic')->getLocationData('sesadvancedactivity_buysell',$buysell->getIdentity()); 
              $buysellActive = true;
              ?>
        <?php } ?>
        <?php 
          }
        }
        ?>
        <?php // Attachments 
          $action->intializeAttachmentcount();
        ?>
        <?php $classnumber = $action->attachment_count; ?>
        <?php $countAttachment = count($action->getAttachments()); 
              $counterAttachment = 0;
              $totalAttachmentAttachInFeed = $action->params;
              $totalAttachmentAttachInFeed = !empty($totalAttachmentAttachInFeed['count']) ? $totalAttachmentAttachInFeed['count'] : $countAttachment;
              
              $viewMoreText = $totalAttachmentAttachInFeed - $attachmentShowCount;
              $showCountAttachment = $attachmentShowCount - 1;
              if($classnumber > $attachmentShowCount)
                $classnumber = $attachmentShowCount;
        ?>
        <?php
        if(!$countAttachment && $location && $googleKey && $action->type != 'post_self_buysell' && !$action->reaction_id){ ?>
        	<div class="feed_item_map">
            <div class="feed_item_map_overlay" onClick="style.pointerEvents='none'"></div>
          	<iframe class="feed_item_map_map" frameborder="0" allowfullscreen="" src="https://www.google.com/maps/embed/v1/place?q=<?php echo $location->venue; ?>&key=<?php echo $googleKey; ?>" style="border:0"></iframe>
          </div>
        <?php } ?>
       <?php if($buysellActive){ ?>
        <div class="sesact_feed_item_buysell">
          <div class="sesact_feed_item_buysell_title"><?php echo $buysell->title; ?></div>
          <div class="sesact_feed_item_buysell_price"><?php echo Engine_Api::_()->sesbasic()->getCurrencyPrice($buysell->price,$buysell->currency); ?></div>
          <?php if($locationBuySell){ ?>
            <div class="sesact_feed_item_buysell_location sesbasic_text_light">
            	<i class="fa fa-map-marker"></i>
              <span><a href="<?php echo $this->url(array('resource_id' => $buysell->getIdentity(),'resource_type'=>$buysell->getType(),'action'=>'get-direction'), 'sesbasic_get_direction', true); ?>" onClick="openSmoothBoxInUrl(this.href);return false;"><?php echo $locationBuySell->venue; ?></a></span>
            </div>
          <?php } ?>
          <?php if($buysell->description){ ?>
          	<div class="sesact_feed_item_buysell_des"><?php echo $this->viewMoreActivity($buysell->description); ?></div>
          <?php } ?>
        </div>
      <?php } ?>
        <?php if($action->reaction_id && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sesadvancedcomment')){ ?> 
          <?php $reaction = Engine_Api::_()->getItem('sesadvancedcomment_emotionfile',$action->reaction_id); ?>
          <?php if($reaction){ ?>
              <div class="feed_item_sticker"><img src="<?php echo Engine_Api::_()->storage()->get($reaction->photo_id, '')->getPhotoUrl(); ?>"></div>
          <?php } ?>
        <?php } ?>
        <?php if( $action->getTypeInfo()->attachable && $action->attachment_count > 0 ): // Attachments ?>
          <?php 
            //Core link image work if width is greater than 250
            $width = '250';
            $attachment = $action->getAttachments();
             if (!empty($attachment) && $attachment[0]->item->getType() == "core_link") {
              $attachment = $attachment[0];
                if($attachment->item->photo_id)
                {
                  $photoURL = $attachment->item->getPhotoUrl();
                if(strpos($photoURL,'http') === false){
                  $baseURL =(!empty($_SERVER["HTTPS"]) && strtolower($_SERVER["HTTPS"] == 'on')) ? "https://" : 'http://';
                  $photoURL = $baseURL. $_SERVER['HTTP_HOST'].$photoURL;
                }
                  if($photoURL){
                    $imageHeightWidthData = getimagesize($photoURL); 
                    $width = isset($imageHeightWidthData[0]) ? $imageHeightWidthData[0] : '250';
                  }
                 }
              }
                        
          ?>
          
           <div class='<?php if($width > 250): ?> link_attachment_big <?php endif; ?> feed_item_attachments <?php //if(strpos($action->type, '_photo')): ?> feed_images feed_images_<?php echo $classnumber; ?><?php //endif; ?>'>
            <?php if( $action->attachment_count > 0 && count($action->getAttachments()) > 0 ): ?>
              <?php if( count($action->getAttachments()) == 1 &&
                      null != ( $richContent = current($action->getAttachments())->item->getRichContent()) ): ?>                    
                <?php echo $richContent; ?>
              <?php else: ?>
                <?php foreach( $action->getAttachments() as $attachment ): 
                      if($attachmentShowCount == $counterAttachment)
                        break;
                ?>
                  <span class='feed_attachment_<?php echo $attachment->meta->type ?><?php if(!empty($attachment->item->ses_aaf_gif) && $attachment->item->ses_aaf_gif == 1){ ?> sesact_attachement_gif<?php } ?>'>
                  <?php if( $attachment->meta->mode == 0 ): // Silence ?>
                  <?php elseif( $attachment->meta->mode == 1 ): // Thumb/text/title type actions ?>
                   
                    <div>
                      <?php 
                        if ($attachment->item->getType() == "core_link")
                        {
                          $attribs = Array('target'=>'_blank');
                        }
                        else
                        {
                          $attribs = Array();
                        } 
                      ?>
                      <?php if( $attachment->item->getPhotoUrl() ): ?>
                      <?php if($countAttachment > 1 )
                              $imageType = 'thumb.normalmain';
                             else
                              $imageType = 'thumb.main';
                      ?>
                        <?php echo $this->htmlLink($attachment->item->getHref(), $this->itemPhoto($attachment->item, $imageType, $attachment->item->getTitle()),array_merge( $attribs,array()));
                         ?>
                      <?php endif; ?>
                      <?php if($attachment->item instanceof Core_Model_Link and ($attachment->item->ses_aaf_gif == 1 /* && ($gifInfo = getimagesize($attachment->item->title)) && !empty($gifInfo[2]) */ || $attachment->item->ses_aaf_gif == 2) ){ ?>
                        <?php if($attachment->item->ses_aaf_gif == 1){ ?>
                        <div class="composer_link_gif_content">
                          <img src="<?php echo  $attachment->item->title; ?>" data-original="<?php echo  $attachment->item->description; ?>" data-still="<?php echo  $attachment->item->title; ?>">
                          <a href="javascript:;" class="link_play_activity" title="PLAY"></a>
                       </div>
                       <?php }else{ 
                          $explodeCode = explode('|| IFRAMEDATA',$attachment->item->description);
                       ?>
                       <div class="composer_link_iframe_content sesbasic_clearfix">
                         <div class="composer_link_iframe sesbasic_clearfix">
                          	<?php echo $explodeCode[1]; ?>
                         </div>
                         <div class="composer_link_iframe_content_info sesbasic_clearfix">
                           <div class="feed_item_link_title">
                            <a href="<?php echo  $attachment->item->getHref(); ?>" target="_blank"> <?php echo  $attachment->item->title; ?></a>
                           </div>
                           <div class="feed_item_link_desc">
                             <?php echo  $explodeCode[0]; ?>
                           </div>
                         </div>
                       </div>
                       <?php } ?>
                      <?php }else if($action->type == 'sesadvancedactivity_event_share'){ ?>
                        <?php echo $this->partial('_events.tpl','sesadvancedactivity',array('events'=>$attachment->item,'share'=>false)); ?>
                      <?php } 
                      else if($attachment->item->getType() == 'sesadvancedactivity_file'){ ?>
                      <div class="sesact_attachment_file sesbasic_clearfix">
                      	<div class="sesact_attachment_file_img">
                        	<?php 
                          $storage = Engine_Api::_()->getItem('storage_file',$attachment->item->item_id);      
                          $filetype = current(explode('_',Engine_Api::_()->sesadvancedactivity()->file_types($storage->mime_major.'/'.$storage->mime_minor)));
                         ?>
                          <?php if($filetype){ ?>
                            <img src="application/modules/Sesadvancedactivity/externals/images/file-icons/<?php echo $filetype.'.png'; ?>">
                          <?php }else{ ?>
                          <img src="application/modules/Sesadvancedactivity/externals/images/file-icons/default.png">
                          <?php } ?>
                        </div>
                        <div class="sesact_attachment_file_info">
                          <div class='feed_item_link_title'>
                            <?php   echo $storage->name; //$this->htmlLink($attachment->item->getHref(), $storage->name ? $attachment->name : '');
                            ?>
                          </div>
                          <div class="sesact_attachment_file_type sesbasic_text_light"><?php echo ucfirst($filetype); ?></div>
                          <div class='sesact_attachment_file_btns'>
                          <?php if($this->viewer()->getIdentity() != 0){ ?>
                            <a href="<?php echo $storage->map(); ?>" class="sesbasic_button" target="_blank"><span><?php echo $this->translate("Download");?></span></a>
                          <?php } ?>
                          <?php if($filetype == 'image'){ ?>
                            <a href="<?php echo $storage->map(); ?>" class="sesbasic_button sesadvactivity_popup_preview"><span><?php echo $this->translate("Preview");?></span></a>
                         <?php }else if($filetype == 'pdf'){ ?>
                            <a href="<?php echo $storage->map(); ?>" target="_blank" class="sesbasic_button"><span><?php echo $this->translate("Preview");?></span></a>
                         <?php } ?>
                          </div>
                        </div>
                      </div>
                      <?php }else{
                         ?>
                      <div>
                        <div class='feed_item_link_title'>
                          <?php   echo $this->htmlLink($attachment->item->getHref(), $attachment->item->getTitle() ? $attachment->item->getTitle() : '', $attribs);
                          ?>
                        </div>
                        <div class='feed_item_link_desc'>
                          <?php if($attachment->item->getType() == 'activity_action'){
                            echo $this->getContent($attachment->item,array(),false,true);
                          }else{ ?>
                          <?php $attachmentDescription = $attachment->item->getDescription();?>
                          <?php if ($action->body != $attachmentDescription): ?>
                            <?php echo $this->viewMoreActivity($attachmentDescription); ?>
                          <?php endif; 
                          }
                          ?>
                        </div>
                      </div>
                      <?php 
                      } ?>
                    </div>
                  <?php elseif( $attachment->meta->mode == 2 ): // Thumb only type actions 
                        if($action->type == 'post_self_buysell'){
                          $imageAttribs = array('data-url'=>'sesadvancedactivity/ajax/feed-buy-sell/action_id/'.$action->getIdentity().'/photo_id/'.$attachment->item->getIdentity().'/main_action/'.(!empty($changeAction) ? $changeAction->getIdentity() : $action->getIdentity()),'class'=>'sessmoothbox');
                          $linkHref = 'javascript:;';
                          $classbuysell ="sesadvancedactivity_buysell";
                          }else{
                          $imageAttribs = array();
                          $classbuysell = '';
                          $linkHref = $attachment->item->getHref();
                          }
                  ?>
                    <?php if($counterAttachment == $showCountAttachment && $viewMoreText > 0){ ?>
                      <?php $imageMoreText = '<p class="_photocounts"><span>+'.$viewMoreText.'</span></p>'; ?>
                    <?php }else{$imageMoreText = '';} ?>
                      <div class="feed_attachment_photo <?php echo $classbuysell; ?>">
                      <?php echo $this->htmlLink($linkHref, $this->itemPhoto($attachment->item, 'thumb.main', $attachment->item->getTitle()).$imageMoreText, $imageAttribs) ?>
                      </div>
                  <?php elseif( $attachment->meta->mode == 3 ): // Description only type actions ?>
                    <?php echo $this->viewMoreActivity($attachment->item->getDescription()); ?>
                  <?php elseif( $attachment->meta->mode == 4 ): // Multi collectible thingy (@todo) ?>
                  <?php endif; ?>
                  </span>
                <?php 
                $counterAttachment++;
                endforeach; ?>
                <?php endif; ?>
            <?php endif; ?>
          </div>
        <?php endif; ?>
        <?php if($action->type == 'post_self_buysell' && $this->viewer()->getIdentity() != 0){ ?>
        	<div class="sesact_feed_item_buysell_btn">
           <?php if(!$buysell->is_sold){ ?>
            <?php if($action->subject_id != $this->viewer()->getIdentity()){ ?>
              <button onClick="openSmoothBoxInUrl('sesadvancedactivity/ajax/message/action_id/<?php echo $action->getIdentity(); ?>');return false;"><i class="fa fa-commenting"></i>Message Seller</button>
            <?php }else{ ?>
              <button class="mark_as_sold_buysell mark_as_sold_buysell_<?php echo $action->getIdentity(); ?>" data-sold="<?php echo $this->translate('Sold'); ?>" data-href="<?php echo $action->getIdentity(); ?>"><i class="fa fa-check"></i>Mark as Sold</button>
            <?php } ?>
           <?php }else{ ?>
              <button><i class="fa fa-check"></i>Sold</button>
           <?php } ?>
         </div>
        <?php } ?>
        <?php if(!empty($changeAction)){
          $action = $changeAction;
          $changeAction = '';
        } ?>
       <?php if($action->schedule_time){ ?>
        <div class="sesact_feed_schedule_post_time">
        	This post will be publish on <b><?php echo date('Y-m-d H:i:s',strtotime($action->schedule_time)); ?></b>.
        </div>
        <?php } ?>
      </div>
      <?php if(!$action->schedule_time && (empty($this->filterFeed) || $this->filterFeed != 'hiddenpost')){ ?>
      <div class="comment_cnt sesact_comments sesbasic_clearfix" id='comment-likes-activity-item-<?php echo $action->action_id ?>'>
          <?php echo $this->activity($action, array('noList' => true,'isOnThisDayPage'=>$this->isOnThisDayPage,'viewAllLikes'=>$this->viewAllLikes), 'update',$this->viewAllComments); ?>   
      </div> <!-- End of Comment Likes -->
      <?php } ?>      
    <?php if( !$this->noList ): ?></li><?php endif; ?>
  
  <?php
        $contentCount++;
        ob_end_flush();
      } catch (Exception $e) {
        ob_end_clean();
        if( APPLICATION_ENV === 'development' ) {
          echo $e->__toString();
        }
      };
    endforeach;
  ?>
  
  <?php if( !$this->getUpdate  && ($this->ulInclude)): ?>
  </ul>
</div>
<?php endif ?>