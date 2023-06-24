<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesadvancedactivity
 * @package    Sesadvancedactivity
 * @copyright  Copyright 2016-2017 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: _subjectfeedtabs.tpl  2017-01-12 00:00:00 SocialEngineSolutions $
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
        'subject' : en4.core.subject.guid,
      },
      evalScripts : true,
      onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
        
        sesJqueryObject('#activity-feed').html(responseHTML);
        if(sesJqueryObject('#activity-feed').children().length)
         sesJqueryObject('.sesadv_noresult_tip').hide();
        else
         sesJqueryObject('.sesadv_noresult_tip').show();
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
  $lists = $this->lists;
 ?>
<div class="sesact_feed_filters sesbasic_clearfix sesbasic_bxs sesbm">
  <ul class="sesadvancedactivity_filter_tabs sesbasic_clearfix">
    <li style="display:none;" class="sesadvancedactivity_filter_img"><i class='fa fa-circle-o-notch fa-spin'></i></li>
    <?php 
     $counter = 1;
     foreach($lists as $activeList){ 
       if($activeList['filtertype'] == 'all' || $activeList['filtertype'] == 'post_self_buysell' || $activeList['filtertype'] == 'post_self_file')
        {
     ?>
      <li class="sesadvancedactivity_filter_tabsli"><a href="javascript:;" data-src="<?php echo $activeList['filtertype']; ?>"><?php echo $this->translate($activeList['title']); ?></a></li>
     <?php 
      }
     } ?>
   <?php if($this->viewer()->getIdentity() && $this->subject()->getGuid() == $this->viewer()->getGuid()){ ?>
     <li class="sesadvancedactivity_filter_tabsli"><a href="javascript:;" data-src="hiddenpost"><?php echo $this->translate("Posts You've Hidden"); ?></a></li>
     <li class="sesadvancedactivity_filter_tabsli"><a href="javascript:;" data-src="taggedinpost"><?php echo $this->translate("Posts You're Tagged In"); ?></a></li>
   <?php } ?>
  </ul>
</div>
<script type="application/javascript">
sesJqueryObject(document).ready(function(e){
  var elem = sesJqueryObject('.sesadvancedactivity_filter_tabs').children();
  if(elem.length == 2){
      sesJqueryObject('.sesact_feed_filters').hide();
  }else{
    sesJqueryObject(elem).eq(1).addClass('active');  
  }
});
</script>