<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesadvancedactivity
 * @package    Sesadvancedactivity
 * @copyright  Copyright 2016-2017 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: _homefeedtabs.tpl  2017-01-12 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
?>
<script type="application/javascript">
var filterResultrequest;
 sesJqueryObject(document).on('click','ul.sesadvancedactivity_filter_tabs li a',function(e){
//    if(sesJqueryObject(this).parent().hasClass('active') || sesJqueryObject(this).hasClass('viewmore'))
//     return false;
   if(sesJqueryObject(this).hasClass('viewmore'))
    return false;
   sesJqueryObject('.sesadvancedactivity_filter_img').show();
   sesJqueryObject('.sesadvancedactivity_filter_tabsli').removeClass('active sesadv_active_tabs');
   sesJqueryObject(this).parent().addClass('active sesadv_active_tabs');
   var filterFeed = sesJqueryObject(this).attr('data-src');
   if(typeof filterResultrequest != 'undefined')
    filterResultrequest.cancel();
    var url = '<?php echo $this->url(array('module' => 'core', 'controller' => 'widget', 'action' => 'index', 'content_id' => $this->identity), 'default', true) ?>';
    var hashTag = sesJqueryObject('#hashtagtextsesadv').val();  
    filterResultrequest = new Request.HTML({
      url : url+"?hashtag="+hashTag+'&isOnThisDayPage='+isOnThisDayPage+'&isMemberHomePage='+isMemberHomePage,
      data : {
        format : 'html',
        'filterFeed' : filterFeed,
        'feedOnly' : true,
        'nolayout' : true,
        'subject' : '<?php echo !empty($this->subjectGuid) ? $this->subjectGuid : "" ?>',
      },
      evalScripts : true,
      onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
        
        sesJqueryObject('#activity-feed').html(responseHTML);
        if(sesJqueryObject('#activity-feed').children().length){
         sesJqueryObject('.sesadv_noresult_tip').hide();
          if(sesJqueryObject('#feed_viewmore').css('display') == 'none' && sesJqueryObject('#feed_loading').css('display') == 'none')
          sesJqueryObject('#feed_no_more_feed').show();
        }
        else{
          sesJqueryObject('#feed_no_more_feed').hide();
         sesJqueryObject('.sesadv_noresult_tip').show();
          
        }
        //initialize feed autoload counter
        counterLoadTime = 0;
        sesadvtooltip();
        Smoothbox.bind($('activity-feed'));
        sesJqueryObject('.sesadvancedactivity_filter_img').hide();
      }
    });
   filterResultrequest.send();
 });

</script>
<?php 
  $filterViewMoreCount = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesadvancedactivity.visiblesearchfilter',6);
  $lists = $this->lists;
 ?>
<div class="sesact_feed_filters sesbasic_clearfix sesbasic_bxs sesbm">
  <ul class="sesadvancedactivity_filter_tabs sesbasic_clearfix">
    <li style="display:none;" class="sesadvancedactivity_filter_img"><i class='fa fa-circle-o-notch fa-spin'></i></li>
   <?php 
   $counter = 1;
   $netwrokStarted = false;
   $listsCount = count($lists);
   foreach($lists as $activeList){
    if($counter > $filterViewMoreCount)
      break;
    if(isset($activeList['network_id'])){
      if(!$netwrokStarted){  $netwrokStarted = true; ?>
        <li class="_sep sesbm"></li>
     <?php
      } ?>
    <li class="sesadvancedactivity_filter_tabsli <?php echo $counter == 1 ? 'active sesadv_active_tabs' : ''; ?>"><a href="javascript:;" data-src="<?php echo 'network_filter_'.$activeList['network_id']; ?>"><?php echo $this->translate($activeList['title']); ?></a></li>
   <?php   
    }else{
    ?>
    <li class="sesadvancedactivity_filter_tabsli <?php echo $counter == 1 ? 'active sesadv_active_tabs' : ''; ?>"><a href="javascript:;" data-src="<?php echo $activeList['filtertype']; ?>"><?php echo $this->translate($activeList['title']); ?></a></li>
   <?php 
   }
    ++$counter;
   } ?>
   <?php if($listsCount > $filterViewMoreCount){ ?>
    <li class="sesact_feed_filter_more sesact_pulldown_wrapper">
    	<a href="javascript:;" class="viewmore"><?php echo $this->translate("More"); ?>&nbsp;<i class="fa fa-angle-down"></i></a>
    	<div class="sesact_pulldown">
				<div class="sesact_pulldown_cont isicon">
        	<ul>
          <?php 
           $counter = 1;
           foreach($lists as $activeList){
            if($counter <= $filterViewMoreCount){
              ++$counter;
              continue;
             }
             if(isset($activeList['network_id'])){
                if(!$netwrokStarted){ $netwrokStarted = true; ?>
                  <li class="_sep sesbm"></li>
               <?php
                } ?>
              <li class="sesadvancedactivity_filter_tabsli"><a href="javascript:;" data-src="<?php echo 'network_filter_'.$activeList['network_id']; ?>"><?php echo $this->translate($activeList['title']); ?></a></li>
             <?php   
              }else{
            ?>
            <li class="sesadvancedactivity_filter_tabsli"><a href="javascript:;" data-src="<?php echo $activeList['filtertype']; ?>"><?php echo $this->translate($activeList['title']); ?></a></li>
           <?php 
              }
           } ?>
           <!-- <li class="_sep sesbm"></li>-->
        	</ul>
        </div>													
      </div>
    </li>
    <li class="sesadvancedactivity_filter_tabsli sesact_feed_filter_setting"><a href="javascript:;" class="sessmoothbox viewmore sesadv_tooltip " title="<?php echo $this->translate('Settings');?>" data-url="sesadvancedactivity/ajax/settings/"><i class="fa fa-cog" aria-hidden="true"></i></a></li> 
  <?php } ?>  
    
  </ul>
</div>