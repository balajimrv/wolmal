<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesadvancedactivity
 * @package    Sesadvancedactivity
 * @copyright  Copyright 2016-2017 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: index.tpl  2017-01-12 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
?>
<?php
 $this->headTranslate(array('More','Close','Permalink of this Post','Copy link of this feed:','Go to this feed','You won\'t see this post in Feed.',"Undo","Hide all from",'You won\'t see',"post in Feed.","Select","It is a long established fact that a reader will be distracted")); ?>

<?php
  //Web cam upload for profile photo
  if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sesadvancedactivity.profilephotoupload', 1) && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sesalbum')):
    $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/scripts/webcam.js'); 
  endif; 
?>
<?php 
$viewer = $this->viewer();

$settings = Engine_Api::_()->getApi('settings', 'core');
$showwelcometab = $settings->getSetting('sesadvancedactivity.showwelcometab', 1);
$makelandingtab = $settings->getSetting('sesadvancedactivity.makelandingtab', 2);
$tabvisibility = $settings->getSetting('sesadvancedactivity.tabvisibility', 0);

if($tabvisibility == 2) {
  $signup_date = explode(' ', $viewer->creation_date);
  $finalSignupDate = date_create($signup_date[0]);
  $todayDate = date_create(date('Y-m-d'));
  $diff = date_diff($finalSignupDate,$todayDate); 
  $diff_days = $diff->d;
  $numberofdays = $settings->getSetting('sesadvancedactivity.numberofdays', 3);
} elseif($tabvisibility == 1) {
  $numberoffriends = $settings->getSetting('sesadvancedactivity.numberoffriends', 3); 
  $friendsCount = $this->viewer()->membership()->getMemberCount($this->viewer());
}
$welcomeflag = 'false';
if($showwelcometab) {
  if($tabvisibility == 2 && $numberofdays > $diff_days) {
    $welcomeflag = 'true';
  } elseif($tabvisibility == 1 && $numberoffriends > $friendsCount) {
    $welcomeflag = 'true';
  } elseif($tabvisibility == 0) {
    $welcomeflag = 'true';
  }
}
?>


<script type="application/javascript">
var privacySetAct = false;
 <?php if( !$this->feedOnly && $this->action_id){ ?>
 sesJqueryObject(document).ready(function(e){
   sesJqueryObject('.tab_<?php echo $this->identity; ?>.tab_layout_sesadvancedactivity_feed').find('a').click();
 });
 <?php } ?>
 </script>
<?php if( !$this->feedOnly && $this->isMemberHomePage): ?>
<div class="sesact_tabs_wrapper sesbasic_clearfix sesbasic_bxs">
  <ul id="sesadv_tabs_cnt" class="sesact_tabs sesbasic_clearfix">
    <?php if($showwelcometab): ?>
      <?php if($welcomeflag == 'true'): ?>
        <li data-url="1" class="sesadv_welcome_tab <?php if($makelandingtab == 2): ?> active <?php endif; ?>">
          <a href="javascript:;">
          <?php if($this->welcomeicon == 'icon'){ ?>
            <i class="fa fa-smile-o" aria-hidden="true"></i>
          <?php }else if($this->welcomeicon){ ?>
            <i class="_icon"><img src="<?php echo $this->welcomeicon; ?>" ></i>
         <?php } ?>
            <span><?php echo $this->translate($this->welcometabtext); ?></span>
          </a>
        </li>
      <?php endif; ?>
    <?php endif; ?>
    <li data-url="2" class="sesadv_update_tab <?php if(empty($showwelcometab) || $makelandingtab == 0): ?> active <?php endif; ?>">
      <a href="javascript:;">
        <?php if($this->welcomeicon == 'icon'){ ?>
            <i class="fa fa-globe" aria-hidden="true"></i>
          <?php }else if($this->whatsnewicon){ ?>
            <i class="_icon"><img src="<?php echo $this->whatsnewicon; ?>" ></i>
         <?php } ?>
      	<span><?php echo $this->translate($this->whatsnewtext); ?></span>
        <span id="count_new_feed"></span>
      </a>
    </li>
    <?php $instagramTable = Engine_Api::_()->getDbtable('instagram', 'sesbasic'); ?>
    <?php if($instagramTable->enable('sesadvancedactivity')) { ?> 
      <?php //Start Instragram Work ?>
        <li data-url="3" class="sesadv_instragram_tab">
          <?php        
            $instagramApi = $instagramTable->getApi();
            $status = true;
            if( !$instagramApi || empty($_SESSION['sesbasic_instagram'])) {
              $status =  false;
            }
            // Not logged in
            if( !$instagramTable->isConnected() ) {
              $status = false;
            }
          ?> 
          <?php if(!$status) { ?>
            <a href="javascript:void(0);" data-href="sesbasic/auth/instagram/" class="showloginpopupinstragram" id="showloginpopupinstragram">
              <?php if($this->instagramicon == 'icon'){ ?>
                <i class="fa fa-instagram" aria-hidden="true"></i>
              <?php }else if($this->instagramicon){ ?>
                <i class="_icon"><img src="<?php echo $this->instagramicon; ?>" ></i>
              <?php } ?>
              <span><?php echo $this->translate($this->instagramtext); ?></span>
            </a>
          <?php } else { ?>
            <a href="javascript:void(0);" class="" onclick="getcontentInstragram();">
              <?php if($this->instagramicon == 'icon'){ ?>
                <i class="fa fa-instagram" aria-hidden="true"></i>
              <?php }else if($this->instagramicon){ ?>
                <i class="_icon"><img src="<?php echo $this->instagramicon; ?>" ></i>
              <?php } ?>
              <span><?php echo $this->translate($this->instagramtext); ?></span>
            </a>
          <?php } ?>
        </li>
      <?php //Start Instragram Work ?>
    <?php } ?>
    
    <?php //if($status) { ?>
      <li data-url="4" class="_righttabs" id="instagramlogutli">
        <div id="instagramlogut" style="display:none;">
          <a href="javascript:void(0);" class="sesadv_tooltip" onclick="instagramlogut()" title="<?php echo $this->translate("Logout"); ?>"><i class="fa fa-power-off"></i></a>
        </div>
      </li>
    <?php //} ?>
  </ul>
</div>

<div id="sesadv_tab_1" class="sesadv_tabs_content" style="display:none;">
  <div class="sesbasic_loading_container sesadv_loading_img" style="height:100px;"  data-href="sesadvancedactivity/ajax/welcome/"></div>
</div>

<?php if($instagramTable->enable('sesadvancedactivity')) { ?> 
  <div id="sesadv_tab_3" class="sesadv_tabs_content" style="display:none;">
    <div id="sesbasic_loading_container" class="sesbasic_loading_container sesadv_loading_img" style="height:100px;"></div>
    <div id="instagram_album">
    </div>
  </div>

  <script>
  
    sesJqueryObject(document).on('click','.showloginpopupinstragram',function(e){
      var href = sesJqueryObject(this).data('href');
      authSesmediaimporterWindow =  window.open(href,'Instagram', "width=780,height=410,toolbar=0,scrollbars=0,status=0,resizable=0,location=0,menuBar=0");  
    });
    
    function instagramlogut() {
      window.location.href = 'sesadvancedactivity/index/instagram-logout';
    }

    function getcontentInstragram() {
      $('sesadv_tab_1').style.display = 'none';
      $('sesadv_tab_2').style.display = 'none';
      $('sesadv_tab_3').style.display = 'block';
      getInAlbumsInstragram('');
    }
    
    function getInAlbumsInstragram(param) {
      //Makes An AJAX Request On Load which retrieves the albums
      sesJqueryObject.ajax({
        type: 'post',
        url: 'sesadvancedactivity/index/load-instagram-gallery',
        data: {
            extra_params: param
        },
        success: function( data ) {
          if(sesJqueryObject('#showloginpopupinstragram')) {
            sesJqueryObject("#showloginpopupinstragram").attr({
              "href" : "javascript:void(0);",
              "data-href" : "",
              "class": "",
              "onclick": "getcontentInstragram();"
            });
          }
          if($('instagramlogut'))
            $('instagramlogut').style.display = 'block';
          //Hide The Spinner
          document.getElementById("sesbasic_loading_container").style.display = "none";
          //Put the Data in the Div
          sesJqueryObject('#instagram_album').html(data);
        }
      });
    }
  </script>
<?php } ?>

<div id="sesadv_tab_2" class="sesadv_tabs_content" <?php if(!empty($showwelcometab) && $makelandingtab != 0): ?> style="display:none;"<?php endif; ?>>
<?php endif; ?>
  <?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sesadvancedactivity/externals/styles/styles.css'); ?>
	<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/styles/emoji.css'); ?>    
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/styles/customscrollbar.css'); ?>

<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/scripts/tooltip.js'); ?>

<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/scripts/jquery.min.js'); ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/scripts/customscrollbar.concat.min.js'); ?>
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/styles/mention/jquery.mentionsInput.css'); ?>    

 <?php $this->headScript()->appendFile($this->layout()->staticBaseUrl .'application/modules/Sesbasic/externals/scripts/mention/underscore-min.js'); ?>
  <?php $this->headScript()->appendFile($this->layout()->staticBaseUrl .'application/modules/Sesbasic/externals/scripts/mention/jquery.mentionsInput.js'); ?>
 
<?php if( (!empty($this->feedOnly) || !$this->endOfFeed ) &&
    (empty($this->getUpdate) && empty($this->checkUpdate)) ): 
    
    $adsEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesadvancedactivity.adsenable', 0);
    ?>
  <script type="text/javascript">
  
  function defaultSettingsSesadv(){
      var activity_count = <?php echo sprintf('%d', $this->activityCount) ?>;
      var next_id = <?php echo sprintf('%d', $this->nextid) ?>;
      var subject_guid = '<?php echo $this->subjectGuid ?>';
      var endOfFeed = <?php echo ( $this->endOfFeed ? 'true' : 'false' ) ?>;
      var activityViewMore = window.activityViewMore = function(next_id, subject_guid) {
        if( en4.core.request.isRequestActive() ) return;
        var hashTag = sesJqueryObject('#hashtagtextsesadv').val();
        var url = '<?php echo $this->url(array('module' => 'core', 'controller' => 'widget', 'action' => 'index', 'content_id' => $this->identity), 'default', true) ?>';         
        $('feed_viewmore').style.display = 'none';
        $('feed_loading').style.display = '';
          var request = new Request.HTML({
          url : url+"?hashtag="+hashTag+'&isOnThisDayPage='+isOnThisDayPage+'&isMemberHomePage='+isMemberHomePage,
          data : {
            format : 'html',
            'maxid' : next_id,
            'feedOnly' : true,
            'nolayout' : true,
            'subject' : subject_guid,
            'contentCount':sesJqueryObject('#activity-feed').find("[id^='activity-item-']").length,
            'filterFeed':sesJqueryObject('.sesadvancedactivity_filter_tabs .active > a').attr('data-src'),
          },
          evalScripts : true,
          onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
            Elements.from(responseHTML).inject($('activity-feed'));
            en4.core.runonce.trigger();
            Smoothbox.bind($('activity-feed'));
            <?php if($adsEnable){ ?>
            displayGoogleAds();
            <?php  } ?>
          }
        });
       request.send();
      }
      
      if( next_id > 0 && !endOfFeed ) {
        sesJqueryObject('#feed_viewmore').show();
        sesJqueryObject('#feed_loading').hide();
        if(sesJqueryObject('#feed_viewmore_link').length){
          $('feed_viewmore_link').removeEvents('click').addEvent('click', function(event){
            event.stop();
            activityViewMore(next_id, subject_guid);
          });
        }
      } else {
        
        sesJqueryObject('#feed_viewmore').hide();
        sesJqueryObject('#feed_loading').hide();
      }
      
   //   
  }
  <?php if($adsEnable){ ?>
  function displayGoogleAds(){
    try{
      sesJqueryObject('ins').each(function(){
          (adsbygoogle = window.adsbygoogle || []).push({});
      });
      if(sesJqueryObject('script[src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"]').length == 0){        
        var script = document.createElement('script');
        script.src = '//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js';
        document.head.appendChild(script);  
      }
    }catch(e){
      //silence  
    }
  }
  <?php } ?>
    en4.core.runonce.add(function() {defaultSettingsSesadv();<?php if($adsEnable){ ?>displayGoogleAds();<?php } ?>});
    defaultSettingsSesadv();
 <?php if(!$this->feedOnly && $this->autoloadTimes > 0 && $this->scrollfeed){ ?>
    var autoloadTimes = '<?php echo $this->autoloadTimes; ?>';
    var counterLoadTime = 0;
    window.addEvent('load', function() {
      sesJqueryObject(window).scroll( function() {
        var containerId = '#activity-feed';
         if(typeof sesJqueryObject(containerId).offset() != 'undefined') {
          var heightOfContentDiv = sesJqueryObject(containerId).height();
          var fromtop = sesJqueryObject(this).scrollTop();
          if(fromtop > heightOfContentDiv - 100 && sesJqueryObject('#feed_viewmore').css('display') == 'block' && autoloadTimes > counterLoadTime){
            document.getElementById('feed_viewmore_link').click();
            counterLoadTime++;
          }
        }
      });
    });
  <?php } ?>
  </script>
<?php endif; ?>

<?php if( !empty($this->feedOnly) && empty($this->checkUpdate)): // Simple feed only for AJAX
  echo $this->activityLoop($this->activity, array(
    'action_id' => $this->action_id,
    'viewAllComments' => $this->viewAllComments,
    'viewAllLikes' => $this->viewAllLikes,
    'getUpdate' => $this->getUpdate,
    'ulInclude'=>$this->feedOnly,
    'contentCount'=>$this->contentCount,
    'userphotoalign' => $this->userphotoalign,
    'filterFeed'=>$this->filterFeed,
    'isMemberHomePage' => $this->isMemberHomePage,
    'isOnThisDayPage' => $this->isOnThisDayPage
  ));
  return; // Do no render the rest of the script in this mode
endif; ?>

<?php if( !empty($this->checkUpdate) ): // if this is for the live update
  if ($this->activityCount){ ?>
   <script type='text/javascript'>
          document.title = '(<?php echo $this->activityCount; ?>) ' + SesadvancedactivityUpdateHandler.title;
          SesadvancedactivityUpdateHandler.options.next_id = "<?php echo $this->firstid; ?>";
          <?php if($this->autoloadfeed){ ?>
            SesadvancedactivityUpdateHandler.getFeedUpdate("<?php echo $this->firstid; ?>");
            $("feed-update").empty();
          <?php } ?>
          sesJqueryObject('#count_new_feed').html("<span><?php echo $this->activityCount; ?></span>");
        </script>
   <div class='tip' style="display:<?php echo ($this->autoloadfeed) ? 'none' : '' ?>">
          <span>
            <a href='javascript:void(0);' onclick='javascript:SesadvancedactivityUpdateHandler.getFeedUpdate("<?php echo $this->firstid ?>");$("feed-update").empty();sesJqueryObject("#count_new_feed").html("");sesJqueryObject("#count_new_feed").hide();'>
              <?php echo $this->translate(array(
                  '%d new update is available - click this to show it.',
                  '%d new updates are available - click this to show them.',
                  $this->activityCount),
                $this->activityCount); ?>
            </a>
          </span>
        </div>
 <?php } 
  return; // Do no render the rest of the script in this mode
endif; ?>

<?php if( !empty($this->getUpdate) ): // if this is for the get live update ?>
<script type="text/javascript">
     SesadvancedactivityUpdateHandler.options.last_id = <?php echo sprintf('%d', $this->firstid) ?>;
   </script>
<?php endif; ?>
<style>
 #scheduled_post, #datetimepicker_edit{display:block !important;}
 </style>

<?php if( $this->enableComposer && !$this->isOnThisDayPage): ?>
<script type="application/javascript">
var sesadvancedactivityDesign = '<?php echo $this->design; ?>';
var userphotoalign = '<?php echo $this->userphotoalign; ?>';
var enableStatusBoxHighlight = '<?php echo $this->enableStatusBoxHighlight; ?>';
var counterLoopComposerItem = 1;
var composeInstance;
 en4.core.runonce.add(function () {
     composeInstance = new Composer('activity_body',{
        overText : true,
        allowEmptyWithoutAttachment : false,
        allowEmptyWithAttachment : true,
        hideSubmitOnBlur : false,
        submitElement : false,
        useContentEditable : true  ,
        menuElement : 'compose-menu',
        baseHref : '<?php echo $this->baseUrl() ?>',
        lang : {
          'Post Something...' : '<?php echo $this->string()->escapeJavascript($this->translate('Post Something...')) ?>'
        }
    });
      sesJqueryObject(document).on('submit','#activity-form',function(e){
        var activatedPlugin = composeInstance.getActivePlugin();
        if(activatedPlugin)
         var pluginName = activatedPlugin.getName();
        else 
          var pluginName = '';
        if(sesJqueryObject('#reaction_id').val() != '' || sesJqueryObject('#tag_location').val() != ''){
          //silence  
        }else if(pluginName != 'buysell'){
         
          if( composeInstance.pluginReady ) {
            if( !composeInstance.options.allowEmptyWithAttachment && composeInstance.getContent() == '' ) {
              sesJqueryObject('.sesact_post_box').addClass('_blank');
              e.preventDefault();
              return;
            }
          } else {
            if( !composeInstance.options.allowEmptyWithoutAttachment && composeInstance.getContent() == '' ) {
              e.preventDefault();
              sesJqueryObject('.sesact_post_box').addClass('_blank');
              return;
            }
          }
        }else{
          if(!sesJqueryObject('#buysell-title').val()){
              if(!sesJqueryObject('.buyselltitle').length) {
                var errorHTMlbuysell = '<div class="sesact_post_error buyselltitle"><?php echo $this->translate("Please enter the title of your product.");?></div>';
                sesJqueryObject('.sesact_sell_composer_title').append(errorHTMlbuysell);
                sesJqueryObject('#buysell-title').parent().addClass('_blank');
                sesJqueryObject('#buysell-title').css('border','1px solid red');
              }
              e.preventDefault();
              return;
          }else if(!sesJqueryObject('#buysell-price').val()){
              if(!sesJqueryObject('.buysellprice').length) {
                var errorHTMlbuysell = '<div class="sesact_post_error buysellprice"><?php echo $this->translate("Please enter the price of your product.");?></div>';
                sesJqueryObject('.sesact_sell_composer_price').append(errorHTMlbuysell);
                sesJqueryObject('#buysell-price').parent().parent().addClass('_blank');
                sesJqueryObject('#buysell-price').css('border','1px solid red');
              }
              e.preventDefault();
              return;
          }
            var field = '<input type="hidden" name="attachment[type]" value="buysell">';
            if(!sesJqueryObject('.fileupload-cnt').length)
              sesJqueryObject('#activity-form').append('<div style="display:none" class="fileupload-cnt">'+field+'</div>');
            else
              sesJqueryObject('.fileupload-cnt').html(field);
        }
        sesJqueryObject('.sesact_post_box').removeClass('_blank');
      <?php if($this->submitWithAjax){ ?>
        e.preventDefault();
        var url = "<?php echo $this->url(array('module' => 'sesadvancedactivity', 'controller' => 'index', 'action' => 'post'), 'default', true) ?>";
        submitActivityFeedWithAjax(url,'<i class="fa fa-circle-o-notch fa-spin"></i>','<?php echo $this->translate("Share") ?>',this);
        return;
     <?php } ?>
      });
 });
 sesJqueryObject(document).on('keyup', '#buysell-title, #buysell-price', function() {
  if(!sesJqueryObject(this).val())
    return;
  sesJqueryObject(this).parent().removeClass('_blank');
  sesJqueryObject(this).parent().parent().removeClass('_blank');
  sesJqueryObject(this).css('border', '');
  sesJqueryObject(this).parent().find('.sesact_post_error').remove();

 });
</script>
  <div class="sesact_post_container_wrapper sesbasic_clearfix sesbasic_bxs">
	<div class="sesact_post_container_overlay"></div>
	<div class="sesact_post_container sesbasic_clearfix">
    <form enctype="multipart/form-data" method="post" action="<?php echo $this->url(array('module' => 'sesadvancedactivity', 'controller' => 'index', 'action' => 'post'), 'default', true) ?>" class="" id="activity-form">
      <?php  if($this->design == 3){ ?>
       <div class="sesact_post_head sesbasic_clearfix">
        <div id="sesact_post_media_options_before"></div>
       </div>
      <?php  }
       ?>
    	<div class="sesact_post_box sesbasic_clearfix">
      	<div class="sesact_post_box_img">
        <?php echo $this->htmlLink('javascript:;', $this->itemPhoto($this->viewer(), 'thumb.icon', $this->viewer()->getTitle()), array()) ?>
        </div>
       <?php if($this->design == 2){ ?>
        <div class="sesact_post_box_close" style="display:none;"><a class="fa fa-close sesact_post_box_close_a sesadv_tooltip" title="<?php echo $this->escape($this->translate('Close')) ?>" href="javascript:;"></a></div>
       <?php } ?>
          <textarea style="display:none;" id="activity_body" class="resetaftersubmit" cols="1" rows="1" name="body" placeholder="<?php echo $this->escape($this->translate('Post Something...')) ?>"></textarea>
        <input type="hidden" name="return_url" value="<?php echo $this->url() ?>" />
        <?php if( $this->viewer() && $this->subject() && !$this->viewer()->isSelf($this->subject())): ?>
          <input type="hidden" name="subject" value="<?php echo $this->subject()->getGuid() ?>" />
        <?php endif; ?>
        <input type="hidden" name="reaction_id" class="resetaftersubmit" id="reaction_id" value="" />
        <?php if( $this->formToken ): ?>
          <input type="hidden" name="token" value="<?php echo $this->formToken ?>" />
        <?php endif ?>
         <input type="hidden" id="hashtagtextsesadv" name="hashtagtextsesadv" value="<?php echo isset($_GET['hashtag']) ? $_GET['hashtag'] : ''; ?>" />
        <input type="hidden" name="fancyalbumuploadfileids" class="resetaftersubmit" id="fancyalbumuploadfileids">
        <div class="sesact_post_error"><?php echo $this->translate("It seems, that the post is blank. Please write or attach something to share your post.");?></div>
        <div id="sesadvancedactivity-menu" class="sesadvancedactivity-menu sesact_post_tools">
          <span class="sesadvancedactivity-menu-selector" id="sesadvancedactivity-menu-selector"></span>
        <?php if($this->design == 1 || $this->design == 3){ ?>
        <?php if(in_array('shedulepost',$this->composerOptions)){ ?>
          <?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesadvancedactivity/externals/scripts/schedule/bootstrap.min.js'); ?>
          <?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesadvancedactivity/externals/scripts/schedule/bootstrap-datetimepicker.min.js'); ?>
            <span class="sesact_post_tool_i tool_i_sheduled_post">
              <a href="javascript:;" id="sesadvancedactivity_shedulepost" class="sesadv_tooltip" title="<?php echo $this->translate("Schedule Post"); ?>"></a>
            </span>
          <div class="sesact_popup_overlay sesadvancedactivity_shedulepost_overlay" style="display:none;"></div>
          <div class="sesact_popup sesadvancedactivity_shedulepost_select sesbasic_bxs" style="display:none;">
            <div class="sesact_popup_header"><?php echo $this->translate("Schedule Post"); ?></div>
            <div class="sesact_popup_cont">
              <b><?php echo $this->translate("Schedule Your Post"); ?></b>
              <p><?php echo $this->translate("Select date and time on which you want to publish your post."); ?></p>
              <div class="sesact_time_input_wrapper">
                <div id="datetimepicker" class="input-append date sesact_time_input">
                  <input type="text" name="scheduled_post" id="scheduled_post" class="resetaftersubmit"></input>
                  <span class="add-on" title="Select Time"><i data-time-icon="icon-time" data-date-icon="icon-calendar"></i></span>
                </div>
                <div class="sesact_error sesadvancedactivity_shedulepost_error"></div>
              </div>
            </div>
            <div class="sesact_popup_btns">
             <button type="submit" class="schedule_post_schedue"><?php echo $this->translate("Schedule"); ?></button>
             <button class="close schedule_post_close"><?php echo $this->translate("Cancel"); ?></button>
            </div>
          </div>
          
          <?php } ?>
          <?php if(in_array('tagUseses',$this->composerOptions)){ ?>
            <span class="sesact_post_tool_i tool_i_tag">
              <a href="javascript:;" id="sesadvancedactivity_tag" class="sesadv_tooltip" title="<?php echo $this->translate('Tag People'); ?>">&nbsp;</a>
            </span>
          <?php } ?>
          <?php if(in_array('locationses',$this->composerOptions)){ ?>
            <span class="sesact_post_tool_i tool_i_location">
              <a href="javascript:;" id="sesadvancedactivity_location" title="<?php echo $this->translate('Check In'); ?>" class="sesadv_tooltip">&nbsp;</a>
            </span>
          <?php } ?>
          <?php if(in_array('smilesses',$this->composerOptions)){ ?>
            <span class="sesact_post_tool_i tool_i_emoji">
              <a href="javascript:;" class="sesadv_tooltip emoji_comment_select activity_emoji_content_a" title="<?php echo $this->translate('Emoticons'); ?>">&nbsp;</a>
            </span>
          <?php } ?>
        <?php } ?>
            
        </div>
        <div class="sesact_post_tags sesbasic_text_light">
          <span style="display:none;" id="dash_elem_act">-</span>	<span id="tag_friend_cnt" style="display:none;"> with </span> <span id="location_elem_act"></span>
        </div>
      </div>
      <div id="sescomposer-tray-container"></div>
      <div class="sesact_post_tag_container sesbasic_clearfix sesact_post_tag_cnt" style="display:none;">
        <span class="tag">With</span>
        <div class="sesact_post_tags_holder">
          <div id="toValues-element">
          </div>
        	<div class="sesact_post_tag_input">
          	<input type="text" class="resetaftersubmit" placeholder="<?php echo $this->translate('Who are you with?'); ?>" id="tag_friends_input" />
            <div id="toValues-wrapper" style="display:none">
            <input type="hidden" id="toValues" name="tag_friends" class="resetaftersubmit">
            </div>
          </div>
        </div>	
      </div>
      <div class="sesact_post_tag_container sesbasic_clearfix sesact_post_location_container" style="display:none;">
        <span class="tag">At</span>
        <div class="sesact_post_tags_holder">
          <div id="locValues-element"></div>
        	<div class="sesact_post_tag_input">
          	<input type="text" placeholder="<?php echo $this->translate('Where are you?'); ?>" name="tag_location" id="tag_location" class="resetaftersubmit"/>
            <input type="hidden" name="activitylng" id="activitylng" value="" class="resetaftersubmit">
            <input type="hidden" name="activitylat" id="activitylat" value="" class="resetaftersubmit">
          </div>
        </div>	
      </div>
    <?php if($this->design == 2){ ?>
      <div class="sesact_post_media_options sesbasic_clearfix">
        <div id="sesact_post_media_options_before"></div>
        <?php if(in_array('shedulepost',$this->composerOptions)){ ?>
          <?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesadvancedactivity/externals/scripts/schedule/bootstrap.min.js'); ?>
          <?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesadvancedactivity/externals/scripts/schedule/bootstrap-datetimepicker.min.js'); ?>
            <span class="sesact_post_media_options_icon tool_i_sheduled_post" style="display:none;">
              <a href="javascript:;" id="sesadvancedactivity_shedulepost" class="sesadv_tooltip" title="<?php echo $this->translate('Schedule Post'); ?>"><span><?php echo $this->translate('Schedule Post'); ?></span></a>
            </span>
          <div class="sesact_popup_overlay sesadvancedactivity_shedulepost_overlay" style="display:none;"></div>
          <div class="sesact_popup sesadvancedactivity_shedulepost_select sesbasic_bxs" style="display:none;">
            <div class="sesact_popup_header"><?php echo $this->translate('Schedule Post'); ?></div>
            <div class="sesact_popup_cont">
              <b><?php echo $this->translate("Schedule Your Post"); ?></b>
              <p><?php echo $this->translate("Select date and time on which you want to publish your post."); ?></p>
              <div class="sesact_time_input_wrapper">
                <div id="datetimepicker" class="input-append date sesact_time_input">
                  <input type="text" name="scheduled_post" id="scheduled_post" class="resetaftersubmit"></input>
                  <span class="add-on sesadv_tooltip" title="View Calendar"><i data-time-icon="icon-time" data-date-icon="icon-calendar"></i></span>
                </div>
                <div class="sesact_error sesadvancedactivity_shedulepost_error"></div>
              </div>
            </div>
            <div class="sesact_popup_btns">
             <button type="submit" class="schedule_post_schedue"><?php echo $this->translate('Schedule'); ?></button>
             <button class="close schedule_post_close"><?php echo $this->translate('Cancel'); ?></button>
            </div>
          </div>
          
          <?php } ?>
        <?php if(in_array('tagUseses',$this->composerOptions)){ ?>
					<span class="sesact_post_media_options_icon tool_i_tag" style="display:none;">
          	<a href="javascript:;" id="sesadvancedactivity_tag" class="sesadv_tooltip" title="<?php echo $this->translate('Tag People'); ?>"><span><?php echo $this->translate('Tag People'); ?></span></a>
          </span>
        <?php } ?>
        <?php if(in_array('locationses',$this->composerOptions)){ ?>
          <span class="sesact_post_media_options_icon tool_i_location" style="display:none;">
          	<a href="javascript:;" id="sesadvancedactivity_location" title="Check In" class="sesadv_tooltip"><span><?php echo $this->translate('Check In'); ?></span></a>
          </span>
        <?php } ?>
        <?php if(in_array('smilesses',$this->composerOptions)){ ?>
          <span class="sesact_post_media_options_icon tool_i_emoji" style="display:none;">
            <a href="javascript:;" class="sesadv_tooltip emoji_comment_select activity_emoji_content_a" title="<?php echo $this->translate('Emoticons'); ?>"><span><?php echo $this->translate('Emoticons'); ?></span></a>
            
      		</span>
        <?php } ?>
      </div>
   <?php } ?>
      <div id="compose-menu" class="sesact_compose_menu">
        <input type="hidden" name="privacy" id="privacy" value="<?php echo $this->usersettings; ?>">
        <div class="sesact_compose_menu_btns notclose">
        <?php if($this->allowprivacysetting){ ?>
        	<div class="sesact_privacy_chooser sesact_pulldown_wrapper">
          	<a href="javascript:void(0);" class="sesact_privacy_btn"><i id="sesadv_privacy_icon"></i><span id="adv_pri_option"><?php echo $this->translate('Everyone'); ?></span><i class="fa fa-caret-down"></i></a>
            <div class="sesact_pulldown">
              <div class="sesact_pulldown_cont isicon">
                <ul class="adv_privacy_optn">
                  <li data-src="everyone" class=""><a href="javascript:;"><i class="sesact_public"></i><span><?php echo $this->translate('Everyone'); ?></span></a></li>
                  <li data-src="networks"><a href="javascript:;"><i class="sesact_network"></i><span><?php echo $this->translate('Friends & Networks'); ?></span></a></li>
                  <li data-src="friends"><a href="javascript:;"><i class="sesact_friends"></i><span><?php echo $this->translate('Friends Only'); ?></span></a></li>
                  <li data-src="onlyme"><a href="javascript:;"><i class="sesact_me"></i><span><?php echo $this->translate('Only Me'); ?></span></a></li>
                  <?php if($this->allownetworkprivacy){ ?>
                  <?php if(count($this->usernetworks)){ ?>
                  <li class="_sep"></li>
                  <?php foreach($this->usernetworks as $usernetworks){ ?>
                    <li data-src="network_list" class="network sesadv_network" data-rel="<?php echo $usernetworks->getIdentity(); ?>"><a href="javascript:;"><i class="sesact_network"></i><span><?php echo $this->translate($usernetworks->getTitle()); ?></span></a></li>
                  <?php }
                  if(count($this->usernetworks) > 1){
                   ?>
                  <li class="multiple mutiselect" data-rel="network-multi"><a href="javascript:;"><i class="sesact_network"></i><span><?php echo $this->translate('Multiptle Networks'); ?></span></a></li>
                  <?php 
                    }
                  } ?>
                  <?php } ?>
                  <?php if($this->allowlistprivacy){ ?>
                  <?php if(count($this->userlists)){ ?>
                  <li class="_sep"></li>
                  <?php foreach($this->userlists as $userlists){ ?>
                    <li data-src="members_list" class="lists sesadv_list" data-rel="<?php echo $userlists->getIdentity(); ?>"><a href="javascript:;"><i class="sesact_list"></i><span><?php echo $this->translate($userlists->getTitle()); ?></span></a></li>
                  <?php } 
                   if(count($this->userlists) > 1){
                  ?>
                  <li class="multiple mutiselect" data-rel="lists-multi"><a href="javascript:;"><i class="sesact_list"></i><span><?php echo $this->translate('Multiptle Lists'); ?></span></a></li>
                  <?php 
                    }
                  } ?>
                  <?php } ?>
                </ul>
              </div>													
            </div>
          </div>
        <?php } ?>
        	<button id="compose-submit" type="submit"><?php echo $this->translate("Share") ?></button>
        </div>
      </div>
  	</form>
  <?php //if($this->design == 2){ ?>
    <div class="sesact_popup_overlay sesact_confirmation_popup_overlay" style="display:none;"></div>
    <div class="sesact_popup sesact_confirmation_popup sesbasic_bxs" style="display:none;">
      <div class="sesact_popup_header"><?php echo $this->translate("Finish Your Post?"); ?></div>
      <div class="sesact_popup_cont"><?php echo $this->translate("If you leave now, your post won't be saved."); ?></div>
      <div class="sesact_popup_btns">
        <button id="discard_post"><?php echo $this->translate("Discard Post"); ?></button>
        <button id="goto_post"><?php echo $this->translate("Go to Post"); ?></button>
      </div>
    </div>
  <?php //} ?>
    <?php
  if (APPLICATION_ENV == 'production')
    $this->headScript()
      ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.min.js');
  else
    $this->headScript()
      ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
      ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
      ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
      ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js')
      ;
?>
    
    <?php
      $this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'externals/mdetect/mdetect' . ( APPLICATION_ENV != 'development' ? '.min' : '' ) . '.js')
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesadvancedactivity/externals/scripts/composer.js');
    ?>

     
    <?php foreach( $this->composePartials as $partial ): ?>
      <?php echo $this->partial($partial[0], $partial[1]) ?>
    <?php endforeach; ?>
    
  </div>
  </div>
<?php endif; ?>
<script type="text/javascript">
      sesJqueryObject(document).on('click','#sesadvancedactivity_tag, .sestag_clk',function(e){
        that = sesJqueryObject(this);
        if(sesJqueryObject(this).hasClass('.sestag_clk'))
           that = sesJqueryObject('#sesadvancedactivity_tag');
         if(sesJqueryObject(that).hasClass('active')){
           sesJqueryObject(that).removeClass('active');
           sesJqueryObject('.sesact_post_tag_cnt').hide();
           return;
         }
         sesJqueryObject('.sesact_post_tag_cnt').show();
         sesJqueryObject(that).addClass('active');
      });
      sesJqueryObject(document).on('click','#sesadvancedactivity_location, .seloc_clk',function(e){
        that = sesJqueryObject(this);
        if(sesJqueryObject(this).hasClass('.seloc_clk'))
           that = sesJqueryObject('#sesadvancedactivity_location');
         if(sesJqueryObject(this).hasClass('active')){
           sesJqueryObject(this).removeClass('active');
           sesJqueryObject('.sesact_post_location_container').hide();
           return;
         }
         sesJqueryObject('.sesact_post_location_container').show();
         sesJqueryObject(this).addClass('active');
      });
      <?php if(!$this->advcomment){ ?>
      var requestEmoji;
      sesJqueryObject('.emoji_comment_select').click(function(){
        sesJqueryObject('.emoji_content').removeClass('from_bottom');
        var topPositionOfParentDiv =  sesJqueryObject(this).offset().top + 35;
        topPositionOfParentDiv = topPositionOfParentDiv+'px';
        if(sesadvancedactivityDesign == 2){
          var leftSub = 55;  
        }else
          var leftSub = 264;
        var leftPositionOfParentDiv =  sesJqueryObject(this).offset().left - leftSub;
        leftPositionOfParentDiv = leftPositionOfParentDiv+'px';
        sesJqueryObject('._emoji_content').css('top',topPositionOfParentDiv);
        sesJqueryObject('._emoji_content').css('left',leftPositionOfParentDiv).css('z-index',99);
        sesJqueryObject('._emoji_content').show();
        var eTop = sesJqueryObject(this).offset().top; //get the offset top of the element
        var availableSpace = sesJqueryObject(document).height() - eTop;
        if(availableSpace < 400){
            sesJqueryObject('.emoji_content').addClass('from_bottom');
        }
          if(sesJqueryObject(this).hasClass('active')){
            sesJqueryObject(this).removeClass('active');
            sesJqueryObject('.emoji_content').hide();
            return false;
           }
            sesJqueryObject(this).addClass('active');
            sesJqueryObject('.emoji_content').show();
            if(sesJqueryObject(this).hasClass('complete'))
              return false;
             if(typeof requestEmoji != 'undefined')
              requestEmoji.cancel();
             var that = this;
             var url = '<?php echo $this->url(array('module' => 'sesadvancedactivity', 'controller' => 'ajax', 'action' => 'emoji'), 'default', true) ?>';
             requestEmoji = new Request.HTML({
              url : url,
              data : {
                format : 'html',
              },
              evalScripts : true,
              onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
                sesJqueryObject('.ses_emoji_holder').html(responseHTML);
                sesJqueryObject(that).addClass('complete');
                sesJqueryObject('.emoji_content').show();
                jqueryObjectOfSes(".sesbasic_custom_scroll").mCustomScrollbar({
									theme:"minimal-dark"
								});
              }
            });
           requestEmoji.send();
      });
      //emoji select in comment
      sesJqueryObject(document).click(function(e){
        if(sesJqueryObject(e.target).attr('id') == 'sesadvancedactivityemoji-edit-a')
          return;
        var container = sesJqueryObject('.ses_emoji_container');
        if ((!container.is(e.target) && container.has(e.target).length === 0)){
           sesJqueryObject('.ses_emoji_container').parent().find('a').removeClass('active');
           sesJqueryObject('.ses_emoji_container').hide();
        }
      });
      <?php } ?>
    </script>
<script type="text/javascript">
sesJqueryObject('#discard_post').click(function(){
hideStatusBoxSecond();
sesJqueryObject('.sesact_confirmation_popup_overlay').hide();
sesJqueryObject('.sesact_confirmation_popup').hide();
sesJqueryObject('.sesact_post_media_options').removeClass('_sesadv_composer_active');
});
sesJqueryObject('#goto_post').click(function(){
sesJqueryObject('.sesact_confirmation_popup').hide();  
sesJqueryObject('.sesact_confirmation_popup_overlay').hide();
});
<?php if($this->allowprivacysetting){ ?>
//set default privacy of logged-in user
sesJqueryObject(document).ready(function(e){
var privacy = sesJqueryObject('#privacy').val();
if(privacy){
if(privacy == 'everyone')
  sesJqueryObject('.adv_privacy_optn >li[data-src="everyone"]').find('a').trigger('click');  
else if(privacy == 'networks')
  sesJqueryObject('.adv_privacy_optn >li[data-src="networks"]').find('a').trigger('click'); 
else if(privacy == 'friends')
  sesJqueryObject('.adv_privacy_optn >li[data-src="friends"]').find('a').trigger('click'); 
else if(privacy == 'onlyme')
  sesJqueryObject('.adv_privacy_optn >li[data-src="onlyme"]').find('a').trigger('click'); 
else if(privacy && privacy.indexOf('network_list_') > -1){
  var exploidV =  privacy.split(',');
  for(i=0;i<exploidV.length;i++){
     var id = exploidV[i].replace('network_list_','');
     sesJqueryObject('.sesadv_network[data-rel="'+id+'"]').addClass('active');
  }
 sesJqueryObject('#adv_pri_option').html("<?php echo $this->translate('Multiple Networks'); ?>");
 sesJqueryObject('.sesact_privacy_btn').attr('title',"<?php echo $this->translate('Multiple Networks'); ?>");;
 sesJqueryObject('#sesadv_privacy_icon').removeAttr('class').addClass('sesact_network');
}else if(privacy && privacy.indexOf('member_list_') > -1){
  var exploidV =  privacy.split(',');
  for(i=0;i<exploidV.length;i++){
     var id = exploidV[i].replace('member_list_','');
     sesJqueryObject('.sesadv_list[data-rel="'+id+'"]').addClass('active');
  }
  sesJqueryObject('#adv_pri_option').html('Multiple Lists');
 sesJqueryObject('.sesact_privacy_btn').attr('title','Multiple Lists');
 sesJqueryObject('#sesadv_privacy_icon').removeAttr('class').addClass('sesact_list');
}
}
privacySetAct = true;
});
<?php  }else{ ?>
var privacySetAct = true;
<?php } ?>
sesJqueryObject(document).on('click','.adv_privacy_optn li a',function(e){
e.preventDefault();
if(!sesJqueryObject(this).parent().hasClass('multiple')){
sesJqueryObject('.adv_privacy_optn > li').removeClass('active');
var text = sesJqueryObject(this).text();
sesJqueryObject('.sesact_privacy_btn').attr('title',text);;
sesJqueryObject(this).parent().addClass('active');
sesJqueryObject('#adv_pri_option').html(text);
sesJqueryObject('#sesadv_privacy_icon').remove();
sesJqueryObject('<i id="sesadv_privacy_icon" class="'+sesJqueryObject(this).find('i').attr('class')+'"></i>').insertBefore('#adv_pri_option');

if(sesJqueryObject(this).parent().hasClass('sesadv_network'))
  sesJqueryObject('#privacy').val(sesJqueryObject(this).parent().attr('data-src')+'_'+sesJqueryObject(this).parent().attr('data-rel'));
else if(sesJqueryObject(this).parent().hasClass('sesadv_list'))
  sesJqueryObject('#privacy').val(sesJqueryObject(this).parent().attr('data-src')+'_'+sesJqueryObject(this).parent().attr('data-rel'));
else
sesJqueryObject('#privacy').val(sesJqueryObject(this).parent().attr('data-src'));
}
sesJqueryObject('.sesact_privacy_btn').parent().removeClass('sesact_pulldown_active');
});

sesJqueryObject(document).on('click','.mutiselect',function(e){
if(sesJqueryObject(this).attr('data-rel') == 'network-multi')
var elem = 'sesadv_network';
else
var elem = 'sesadv_list';
var elemens = sesJqueryObject('.'+elem);
var html = '';
for(i=0;i<elemens.length;i++){
html += '<li><input class="checkbox" type="checkbox" value="'+sesJqueryObject(elemens[i]).attr('data-rel')+'">'+sesJqueryObject(elemens[i]).text()+'</li>';
}
en4.core.showError('<form id="'+elem+'_select" class="_privacyselectpopup"><p>It is a long established fact that a reader will be distracted</p><ul class="sesbasic_clearfix">'+html+'</ul><div class="_privacyselectpopup_btns sesbasic_clearfix"><button type="submit">Select</button><button class="close" onclick="Smoothbox.close();return false;">Close</button></div></form>');
sesJqueryObject ('._privacyselectpopup').parent().parent().addClass('_privacyselectpopup_wrapper');
//pre populate
var valueElem = sesJqueryObject('#privacy').val();
if(valueElem && valueElem.indexOf('network_list_') > -1 && elem == 'sesadv_network'){
var exploidV =  valueElem.split(',');
for(i=0;i<exploidV.length;i++){
   var id = exploidV[i].replace('network_list_','');
   sesJqueryObject('.checkbox[value="'+id+'"]').prop('checked', true);
}
}else if(valueElem && valueElem.indexOf('member_list_') > -1 && elem == 'sesadv_list'){
var exploidV =  valueElem.split(',');
for(i=0;i<exploidV.length;i++){
   var id = exploidV[i].replace('member_list_','');
   sesJqueryObject('.checkbox[value="'+id+'"]').prop('checked', true);
}
}
});
sesJqueryObject(document).on('submit','#sesadv_list_select',function(e){
e.preventDefault();
var isChecked = false;
var sesadv_list_select = sesJqueryObject('#sesadv_list_select').find('[type="checkbox"]');
var valueL = '';
for(i=0;i<sesadv_list_select.length;i++){
if(!isChecked)
  sesJqueryObject('.adv_privacy_optn > li').removeClass('active');
if(sesJqueryObject(sesadv_list_select[i]).is(':checked')){
  isChecked = true;
  var el = sesJqueryObject(sesadv_list_select[i]).val();
  sesJqueryObject('.lists[data-rel="'+el+'"]').addClass('active');
  valueL = valueL+'member_list_'+el+',';
}
}
if(isChecked){
 sesJqueryObject('#privacy').val(valueL);
 sesJqueryObject('#adv_pri_option').html("<?php echo $this->translate('Multiple Lists'); ?>");
 sesJqueryObject('.sesact_privacy_btn').attr('title',"<?php echo $this->translate('Multiple Lists'); ?>");;
sesJqueryObject(this).find('.close').trigger('click');
}
sesJqueryObject('#sesadv_privacy_icon').removeAttr('class').addClass('sesact_list');
});
sesJqueryObject(document).on('submit','#sesadv_network_select',function(e){
e.preventDefault();
var isChecked = false;
var sesadv_network_select = sesJqueryObject('#sesadv_network_select').find('[type="checkbox"]');
var valueL = '';
for(i=0;i<sesadv_network_select.length;i++){
if(!isChecked)
  sesJqueryObject('.adv_privacy_optn > li').removeClass('active');
if(sesJqueryObject(sesadv_network_select[i]).is(':checked')){
  isChecked = true;
  var el = sesJqueryObject(sesadv_network_select[i]).val();
  sesJqueryObject('.network[data-rel="'+el+'"]').addClass('active');
  valueL = valueL+'network_list_'+el+',';
}
}
if(isChecked){
 sesJqueryObject('#privacy').val(valueL);
 sesJqueryObject('#adv_pri_option').html('Multiple Network');
 sesJqueryObject('.sesact_privacy_btn').attr('title','Multiple Network');;
sesJqueryObject(this).find('.close').trigger('click');
}
sesJqueryObject('#sesadv_privacy_icon').removeAttr('class').addClass('sesact_network');
});
var input = document.getElementById('tag_location');
var autocomplete = new google.maps.places.Autocomplete(input);
google.maps.event.addListener(autocomplete, 'place_changed', function () {
  var place = autocomplete.getPlace();
  if (!place.geometry) {
    return;
  }
  sesJqueryObject('#locValues-element').html('<span class="tag">'+sesJqueryObject('#tag_location').val()+' <a href="javascript:void(0);" class="loc_remove_act notclose">x</a></span>');
  sesJqueryObject('#dash_elem_act').show();
  sesJqueryObject('#location_elem_act').show();
  sesJqueryObject('#location_elem_act').html('at <a href="javascript:;" class="seloc_clk">'+sesJqueryObject('#tag_location').val()+'</a>');
  sesJqueryObject('#tag_location').hide();
  document.getElementById('activitylng').value = place.geometry.location.lng();
  document.getElementById('activitylat').value = place.geometry.location.lat();
});
sesJqueryObject(document).on('click','.loc_remove_act',function(e){
sesJqueryObject('#activitylng').val('');
sesJqueryObject('#activitylat').val('');
sesJqueryObject('#tag_location').val('');
sesJqueryObject('#locValues-element').html('');
sesJqueryObject('#tag_location').show();
sesJqueryObject('#location_elem_act').hide();
if(!sesJqueryObject('#toValues-element').children().length)
   sesJqueryObject('#dash_elem_act').hide();
})    

// Populate data
var maxRecipients = 50;
var to = {
id : false,
type : false,
guid : false,
title : false
};

function removeFromToValue(id) {    
//check for edit form
if(sesJqueryObject('#sessmoothbox_main').length){
  removeFromToValueEdit(id);
  return;
}
  
// code to change the values in the hidden field to have updated values
// when recipients are removed.
var toValues = $('toValues').value;
var toValueArray = toValues.split(",");
var toValueIndex = "";

var checkMulti = id.search(/,/);

// check if we are removing multiple recipients
if (checkMulti!=-1){
  var recipientsArray = id.split(",");
  for (var i = 0; i < recipientsArray.length; i++){
    removeToValue(recipientsArray[i], toValueArray);
  }
}
else{
  removeToValue(id, toValueArray);
}
$('tag_friends_input').disabled = false;
var firstElem = sesJqueryObject('#toValues-element > span').eq(0).text();
var countElem = sesJqueryObject('#toValues-element').children().length;
var html = '';

if(!firstElem.trim()){
  sesJqueryObject('#tag_friend_cnt').html('');
  sesJqueryObject('#tag_friend_cnt').hide();
  if(!sesJqueryObject('#tag_location').val())
  sesJqueryObject('#dash_elem_act').hide();
  return;
}else if(countElem == 1){
  html = '<a href="javascript:;" class="sestag_clk">'+firstElem.replace('x','')+'</a>';
}else if(countElem > 2){
  html = '<a href="javascript:;" class="sestag_clk">'+firstElem.replace('x','')+'</a>';
  html = html + ' and <a href="javascript:;" class="sestag_clk">'+(countElem-1)+' others</a>';
}else{
  html = '<a href="javascript:;" class="sestag_clk">'+firstElem.replace('x','')+'</a>';
  html = html + ' and <a href="javascript:;" class="sestag_clk">'+sesJqueryObject('#toValues-element > span').eq(1).text().replace('x','')+'</a>';
}
sesJqueryObject('#tag_friend_cnt').html('with '+html);
sesJqueryObject('#tag_friend_cnt').show();
sesJqueryObject('#dash_elem_act').show();

}

function removeToValue(id, toValueArray){
for (var i = 0; i < toValueArray.length; i++){
  if (toValueArray[i]==id) toValueIndex =i;
}

toValueArray.splice(toValueIndex, 1);
$('toValues').value = toValueArray.join();
}

en4.core.runonce.add(function() {
  
  new Autocompleter.Request.JSON('tag_friends_input', '<?php echo $this->url(array('module' => 'sesadvancedactivity', 'controller' => 'index', 'action' => 'suggest'), 'default', true) ?>', {
    'minLength': 1,
    'delay' : 250,
    'selectMode': 'pick',
    'autocompleteType': 'message',
    'multiple': false,
    'className': 'message-autosuggest',
    'filterSubset' : true,
    'tokenFormat' : 'object',
    'tokenValueKey' : 'label',
    'injectChoice': function(token){
      if(token.type == 'user'){
        var choice = new Element('li', {
          'class': 'autocompleter-choices',
          'html': token.photo,
          'id':token.label
        });
        new Element('div', {
          'html': this.markQueryValue(token.label),
          'class': 'autocompleter-choice'
        }).inject(choice);
        this.addChoiceEvents(choice).inject(this.choices);
        choice.store('autocompleteChoice', token);
      }
      else {
        var choice = new Element('li', {
          'class': 'autocompleter-choices friendlist',
          'id':token.label
        });
        new Element('div', {
          'html': this.markQueryValue(token.label),
          'class': 'autocompleter-choice'
        }).inject(choice);
        this.addChoiceEvents(choice).inject(this.choices);
        choice.store('autocompleteChoice', token);
      }
        
    },
    onPush : function(choice){
      if( $('toValues').value.split(',').length >= maxRecipients ){
        $('tag_friends_input').disabled = true;
      }
      var firstElem = sesJqueryObject('#toValues-element > span').eq(0).text();
      var countElem = sesJqueryObject('#toValues-element  > span').children().length;
      var html = '';
      if(countElem == 1){
        html = '<a href="javascript:;" class="sestag_clk">'+firstElem.replace('x','')+'</a>';
      }else if(countElem > 2){
        html = '<a href="javascript:;" class="sestag_clk">'+firstElem.replace('x','')+'</a>';
        html = html + ' and <a href="javascript:;"  class="sestag_clk">'+(countElem-1)+' others</a>';
      }else{
        html = '<a href="javascript:;" class="sestag_clk">'+firstElem.replace('x','')+'</a>';
        html = html + ' and <a href="javascript:;" class="sestag_clk">'+sesJqueryObject('#toValues-element > span').eq(1).text().replace('x','')+'</a>';
      }
      sesJqueryObject('#tag_friend_cnt').html('with '+html);
      sesJqueryObject('#tag_friend_cnt').show();
      sesJqueryObject('#dash_elem_act').show();
    }
  });
  
  new Composer.OverText($('tag_friends_input'), {
    'textOverride' : '<?php echo $this->translate('') ?>',
    'element' : 'label',
    'isPlainText' : true,
    'positionOptions' : {
      position: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
      edge: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
      offset: {
        x: ( en4.orientation == 'rtl' ? -4 : 4 ),
        y: 2
      }
    }
  });

});
</script>
<script type="application/javascript">
var isMemberHomePage = <?php echo !empty($this->isMemberHomePage) ? $this->isMemberHomePage : 0; ?>;
var isOnThisDayPage = <?php echo !empty($this->isOnThisDayPage) ? $this->isOnThisDayPage : 0; ?>;
         function  preventSubmitOnSocialNetworking(){
           if(sesJqueryObject('.composer_facebook_toggle_active').length)
            sesJqueryObject('.composer_facebook_toggle').click();
           if(sesJqueryObject('.composer_twitter_toggle_active').length)
            sesJqueryObject('.composer_twitter_toggle_active').click();  
          }
          sesJqueryObject(document).on('click','.schedule_post_schedue',function(e){
           e.preventDefault();
           var value = sesJqueryObject('#scheduled_post').val();
           if(sesJqueryObject('.sesadvancedactivity_shedulepost_error').css('display') == 'block' || !value){
            return;   
           }
           sesJqueryObject('.sesadvancedactivity_shedulepost_overlay').hide();
           sesJqueryObject('.sesadvancedactivity_shedulepost_select').hide();
           sesJqueryObject('.sesadvancedactivity_shedulepost').addClass('active');
           preventSubmitOnSocialNetworking();
          });
          sesJqueryObject(document).on('click','#sesadvancedactivity_shedulepost',function(e){
           e.preventDefault();
           sesJqueryObject('.sesadvancedactivity_shedulepost_overlay').show();
           sesJqueryObject('.sesadvancedactivity_shedulepost_select').show();
           sesJqueryObject(this).addClass('active');
           makeDateTimePicker();
           sesadvtooltip();
          });
          sesJqueryObject(document).on('click','.schedule_post_close',function(e){
              e.preventDefault();
            sesJqueryObject('.sesadvancedactivity_shedulepost_overlay').hide();
            sesJqueryObject('.sesadvancedactivity_shedulepost_select').hide();
            if(sesJqueryObject('.sesadvancedactivity_shedulepost_error').css('display') == 'block')
              sesJqueryObject('.sesadvancedactivity_shedulepost_error').html('').hide();
            sesJqueryObject('#scheduled_post').val('');
             sesJqueryObject('#sesadvancedactivity_shedulepost').removeClass('active');
             sesJqueryObject('.bootstrap-datetimepicker-widget').hide();
          });
          var schedule_post_datepicker;
          function makeDateTimePicker(){
            if(sesJqueryObject('.sesadvancedactivity_shedulepost_edit_overlay').length){
              var elem = 'scheduled_post_edit';
              var datepicker = 'datetimepicker_edit';
            }else{
              var elem = 'scheduled_post';
              var datepicker  = 'datetimepicker';
            }
            //if(!sesJqueryObject('#'+elem).val()){
              var now = new Date();
              now.setMinutes(now.getMinutes() + 10);
           // }
            schedule_post_datepicker = sesJqueryObject('#'+datepicker).datetimepicker({
            format: 'dd/MM/yyyy hh:mm:ss',
            maskInput: false,           // disables the text input mask
            pickDate: true,            // disables the date picker
            pickTime: true,            // disables de time picker
            pick12HourFormat: true,   // enables the 12-hour format time picker
            pickSeconds: true,         // disables seconds in the time picker
            startDate: now,      // set a minimum date
            endDate: Infinity          // set a maximum date
          });
          schedule_post_datepicker.on('changeDate', function(e) {
            var time = e.localDate.toString();
            var timeObj = new Date(time).getTime();
            //add 10 minutes
            var now = new Date();
            now.setMinutes(now.getMinutes() + 10);
            if(sesJqueryObject('.sesadvancedactivity_shedulepost_edit_overlay').length){
              var error = 'sesadvancedactivity_shedulepost_edit_error';
            }else{
              var error = 'sesadvancedactivity_shedulepost_error';
            }
            if(timeObj < now.getTime()){
              sesJqueryObject('.'+error).html("<?php echo $this->translate('choose time 10 minutes greater than current time.'); ?>").show();
              return false;
            }else{
             sesJqueryObject('.'+error).html('').hide();
            }
          });  
          }
          </script>      
      <script type="application/javascript">
         function  preventSubmitOnSocialNetworking(){
           if(sesJqueryObject('.composer_facebook_toggle_active').length)
            sesJqueryObject('.composer_facebook_toggle').click();
           if(sesJqueryObject('.composer_twitter_toggle_active').length)
            sesJqueryObject('.composer_twitter_toggle_active').click();  
          }
          sesJqueryObject(document).on('click','.schedule_post_schedue',function(e){
           e.preventDefault();
           var value = sesJqueryObject('#scheduled_post').val();
           if(sesJqueryObject('.sesadvancedactivity_shedulepost_error').css('display') == 'block' || !value){
            return;   
           }
           sesJqueryObject('.sesadvancedactivity_shedulepost_overlay').hide();
           sesJqueryObject('.sesadvancedactivity_shedulepost_select').hide();
           sesJqueryObject('.sesadvancedactivity_shedulepost').addClass('active');
           preventSubmitOnSocialNetworking();
          });
          sesJqueryObject(document).on('click','#sesadvancedactivity_shedulepost',function(e){
           e.preventDefault();
           sesJqueryObject('.sesadvancedactivity_shedulepost_overlay').show();
           sesJqueryObject('.sesadvancedactivity_shedulepost_select').show();
           sesJqueryObject(this).addClass('active');
           makeDateTimePicker();
          });
          sesJqueryObject(document).on('click','.schedule_post_close',function(e){
              e.preventDefault();
            sesJqueryObject('.sesadvancedactivity_shedulepost_overlay').hide();
            sesJqueryObject('.sesadvancedactivity_shedulepost_select').hide();
            if(sesJqueryObject('.sesadvancedactivity_shedulepost_error').css('display') == 'block')
              sesJqueryObject('.sesadvancedactivity_shedulepost_error').html('').hide();
            sesJqueryObject('#scheduled_post').val('');
             sesJqueryObject('#sesadvancedactivity_shedulepost').removeClass('active');
             sesJqueryObject('.bootstrap-datetimepicker-widget').hide();
          });
          var schedule_post_datepicker;
          function makeDateTimePicker(){
            if(sesJqueryObject('.sesadvancedactivity_shedulepost_edit_overlay').length){
              var elem = 'scheduled_post_edit';
              var datepicker = 'datetimepicker_edit';
            }else{
              var elem = 'scheduled_post';
              var datepicker  = 'datetimepicker';
            }
            //if(!sesJqueryObject('#'+elem).val()){
              var now = new Date();
              now.setMinutes(now.getMinutes() + 10);
           // }
            schedule_post_datepicker = sesJqueryObject('#'+datepicker).datetimepicker({
            format: 'dd/MM/yyyy hh:mm:ss',
            maskInput: false,           // disables the text input mask
            pickDate: true,            // disables the date picker
            pickTime: true,            // disables de time picker
            pick12HourFormat: true,   // enables the 12-hour format time picker
            pickSeconds: true,         // disables seconds in the time picker
            startDate: now,      // set a minimum date
            endDate: Infinity          // set a maximum date
          });
          schedule_post_datepicker.on('changeDate', function(e) {
            var time = e.localDate.toString();
            var timeObj = new Date(time).getTime();
            //add 10 minutes
            var now = new Date();
            now.setMinutes(now.getMinutes() + 10);
            if(sesJqueryObject('.sesadvancedactivity_shedulepost_edit_overlay').length){
              var error = 'sesadvancedactivity_shedulepost_edit_error';
            }else{
              var error = 'sesadvancedactivity_shedulepost_error';
            }
            if(timeObj < now.getTime()){
              sesJqueryObject('.'+error).html("<?php echo $this->translate('choose time 10 minutes greater than current time.'); ?>").show();
              return false;
            }else{
             sesJqueryObject('.'+error).html('').hide();
            }
          });  
          }
          </script> 

<?php if(empty($this->subjectGuid) && !$this->isOnThisDayPage){ ?>

  <?php if($this->isMemberHomePage){ 
    echo $this->partial(
        '_homesuggestions.tpl',
        'sesadvancedactivity',
        array()
        );
  ?>
    <?php echo $this->partial(
            '_homefeedtabs.tpl',
            'sesadvancedactivity',
            array('identity'=>$this->identity,'lists'=>$this->lists)
          );
    } ?>
<?php }else{
if(!$this->isOnThisDayPage && $this->subject() && $this->subject()->getType() == 'user'){
  echo $this->partial(
        '_subjectfeedtabs.tpl',
        'sesadvancedactivity',
        array('identity'=>$this->identity,'lists'=>$this->lists)
        );
    }
  }
 ?>
<?php if ($this->updateSettings && !$this->action_id && !$this->isOnThisDayPage): // wrap this code around a php if statement to check if there is live feed update turned on ?>
  <script type="text/javascript">
    var SesadvancedactivityUpdateHandler;
    en4.core.runonce.add(function() {
      try {
          SesadvancedactivityUpdateHandler = new SesadvancedactivityUpdateHandler({
            'baseUrl' : en4.core.baseUrl,
            'basePath' : en4.core.basePath,
            'identity' : 4,
            'delay' : <?php echo $this->updateSettings;?>,
            'last_id': <?php echo sprintf('%d', $this->firstid) ?>,
            'subject_guid' : '<?php echo $this->subjectGuid ?>'
          });
          setTimeout("SesadvancedactivityUpdateHandler.start()",1250);
          //activityUpdateHandler.start();
          window._SesadvancedactivityUpdateHandler = SesadvancedactivityUpdateHandler;
      } catch( e ) {
        //if( $type(console) ) console.log(e);
      }
      if(sesJqueryObject('#activity-feed').children().length)
       sesJqueryObject('.sesadv_noresult_tip').hide();
      else
       sesJqueryObject('.sesadv_noresult_tip').show();
    });
  </script>
<?php endif;?>

<?php if( $this->post_failed == 1 ): ?>
  <div class="tip">
    <span>
      <?php $url = $this->url(array('module' => 'user', 'controller' => 'settings', 'action' => 'privacy'), 'default', true) ?>
      <?php echo $this->translate('The post was not added to the feed. Please check your %1$sprivacy settings%2$s.', '<a href="'.$url.'">', '</a>') ?>
    </span>
  </div>
<?php endif; ?>

<?php // If requesting a single action and it does not exist, show error ?>
<?php if( !$this->activity ): ?>
  <?php if( $this->action_id ): ?>
    <h2><?php echo $this->translate("Activity Item Not Found") ?></h2>
    <p>
      <?php echo $this->translate("The page you have attempted to access could not be found.") ?>
    </p>
  <?php return;?>
  <?php endif; ?>
<?php endif; ?>
<div class="sesadv_tip sesact_tip_box sesadv_noresult_tip" style="display:<?php echo !sprintf('%d', $this->activityCount) ? 'block' : 'none'; ?>;">
<?php if(!$this->isOnThisDayPage){ ?>
  <span>
    <?php echo $this->translate("Nothing has been posted here yet - be the first!") ?>
  </span>
 <?php }else{ ?>
 <span>
    <?php echo $this->translate('No memories for you on this day.') ?>
  </span>
 <?php } ?>
</div>
<div id="feed-update"></div>
<?php echo $this->activityLoop($this->activity, array(
  'action_id' => $this->action_id,
  'viewAllComments' => $this->viewAllComments,
  'viewAllLikes' => $this->viewAllLikes,
  'getUpdate' => $this->getUpdate,
  'isOnThisDayPage'=>$this->isOnThisDayPage,
  'isMemberHomePage' => $this->isMemberHomePage,
  'userphotoalign' => $this->userphotoalign,
  'filterFeed'=>$this->filterFeed,
)) ?>
<?php if(!$this->isOnThisDayPage): ?>
<div class="sesact_view_more sesadv_tip sesact_tip_box" id="feed_viewmore" style="display: none;">
	<a href="javascript:void(0);" id="feed_viewmore_link" class="sesbasic_animation sesbasic_linkinherit"><i class="fa fa-repeat"></i><span><?php echo $this->translate('View More');?></span></a>
</div>
<div class="sesadv_tip sesact_tip_box" id="feed_loading" style="display: none;">
  <span><i class="fa fa-circle-o-notch fa-spin"></i></span>
</div>
<?php if( !$this->feedOnly && $this->isMemberHomePage && !$this->isOnThisDayPage): ?>
</div>
<?php endif; ?>
<div class="sesadv_tip sesact_tip_box" id="feed_no_more_feed" style="display:none;">
	<span>No more post</span>
</div>
<script type="application/javascript">

  sesJqueryObject(document).ready(function() {
    var welcomeactive = sesJqueryObject('#sesadv_tabs_cnt li.active');
    if(sesJqueryObject(welcomeactive).attr('data-url') == 1) {
      sesJqueryObject(welcomeactive).find('a').trigger('click');
    }
  });

  sesJqueryObject(document).on('click','#sesadv_tabs_cnt li a',function(e) {
   console.log(sesJqueryObject(this));
    var id = sesJqueryObject(this).parent().attr('data-url');
    var instid = sesJqueryObject(this).parent().parent().attr('data-url');

    if(instid == 4) return;
    
    sesJqueryObject('.sesadv_tabs_content').hide();
    

    sesJqueryObject('#sesadv_tabs_cnt > li').removeClass('active');
    sesJqueryObject(this).parent().addClass('active'); 
    sesJqueryObject('#sesadv_tab_'+id).show();
    
    if(id != 3) {
      $('instagramlogutli').style.display = 'none';    
    } else {
      $('instagramlogutli').style.display = 'block';    
    }

    if(id == 1 || id == 3) {
      sesJqueryObject('#feed_no_more_feed').addClass('dNone');  
    }else
      sesJqueryObject('#feed_no_more_feed').removeClass('dNone'); 
    if(id == 3) return;
    if(sesJqueryObject('#sesadv_tab_'+id).find('.sesadv_loading_img').length){
      var url = en4.core.baseUrl+sesJqueryObject('#sesadv_tab_'+id).find('.sesadv_loading_img').attr('data-href');
      //get content
      if(typeof requestsent != 'undefined')
        requestsent.cancel();
      requestsent = (new Request.HTML({
      method: 'post',
      'url': url,
      'data': {
        format: 'html'
      },
      onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
       sesJqueryObject('#sesadv_tab_'+id).html(responseHTML);
      }
    }));
     requestsent.send();
    }
  });
 
</script>
<?php endif; ?>
<?php if($this->isOnThisDayPage){ ?>
<div class="sesact_feed_thanks_block centerT">
	<img src="application/modules/Sesadvancedactivity/externals/images/thanks.png"alt="" />
  <span><?php echo $this->translate("Thanks for coming!"); ?></span>
</div>
<?php } ?>
<?php if($this->enablewidthsetting): ?>
  <style type="text/css">
  .sesact_feed ul.feed .feed_attachment_album_photo img {   
    max-width: <?php echo $this->sesact_image1_width ?>px !important;
    max-height: <?php echo $this->sesact_image1_height ?>px !important;
    width: auto;
  }
	div.feed_images_2 > [class*='feed_attachment_'] .feed_attachment_photo img,
	div.feed_images_3 > [class*='feed_attachment_'] .feed_attachment_photo img,
	div.feed_images_4 > [class*='feed_attachment_'] .feed_attachment_photo img,
	div.feed_images_5 > [class*='feed_attachment_'] .feed_attachment_photo img,
	div.feed_images_6 > [class*='feed_attachment_'] .feed_attachment_photo img,
	div.feed_images_7 > [class*='feed_attachment_'] .feed_attachment_photo img,
	div.feed_images_8 > [class*='feed_attachment_'] .feed_attachment_photo img,
	div.feed_images_9 > [class*='feed_attachment_'] .feed_attachment_photo img{
		max-width:100% !important;
		max-height:inherit !important;
	}
  .feed_images_2 > [class*='feed_attachment_'] {
    height:<?php echo $this->sesact_image2_height ?>px;
    width:<?php echo $this->sesact_image2_width ?>px;
  }
  .feed_images_3 > [class*='feed_attachment_']:first-child{
    height:<?php echo $this->sesact_image3_bigheight ?>px;
    width:<?php echo $this->sesact_image3_bigwidth ?>px;
  }
  .feed_images_3 > [class*='feed_attachment_']{
    height:<?php echo $this->sesact_image3_smallheight ?>px;
    width:<?php echo $this->sesact_image3_smallwidth ?>px;
  }
  .feed_images_4 > [class*='feed_attachment_']:first-child{
    height:<?php echo $this->sesact_image4_bigheight ?>px;
    width:<?php echo $this->sesact_image4_bigwidth ?>px;
  }
  .feed_images_4 > [class*='feed_attachment_']{
    height:<?php echo $this->sesact_image4_smallheight ?>px;
    width:<?php echo $this->sesact_image4_smallwidth ?>px;
  }
  .feed_images_5 > [class*='feed_attachment_']:first-child{
    height:<?php echo $this->sesact_image5_bigheight ?>px;
    width:<?php echo $this->sesact_image5_bigwidth ?>px;
  }
  .feed_images_5 > [class*='feed_attachment_']{
    height:<?php echo $this->sesact_image5_smallheight ?>px;
    width:<?php echo $this->sesact_image5_smallwidth ?>px;
  }
  .feed_images_6 > [class*='feed_attachment_']{
    height:<?php echo $this->sesact_image6_height ?>px;
    width:<?php echo $this->sesact_image6_width ?>px;
  }
  .feed_images_7 > [class*='feed_attachment_']:nth-child(4),
  .feed_images_7 > [class*='feed_attachment_']:nth-child(5),
  .feed_images_7 > [class*='feed_attachment_']:nth-child(6),
  .feed_images_7 > [class*='feed_attachment_']:nth-child(7){
    height:<?php echo $this->sesact_image7_smallheight ?>px;
    width:<?php echo $this->sesact_image7_smallwidth ?>px;
  }
  .feed_images_7 > [class*='feed_attachment_']:nth-child(1),
  .feed_images_7 > [class*='feed_attachment_']:nth-child(2),
  .feed_images_7 > [class*='feed_attachment_']:nth-child(3){
    height:<?php echo $this->sesact_image7_bigheight ?>px;
    width:<?php echo $this->sesact_image7_bigwidth ?>px;
  }
  .feed_images_8 > [class*='feed_attachment_']{
    height:<?php echo $this->sesact_image8_height ?>px;
    width:<?php echo $this->sesact_image8_width ?>px;
  }
  .feed_images_9 > [class*='feed_attachment_']{
    height:<?php echo $this->sesact_image9_height ?>px;
    width:<?php echo $this->sesact_image9_width ?>px;
  }
  </style>
<?php endif;
 ?>