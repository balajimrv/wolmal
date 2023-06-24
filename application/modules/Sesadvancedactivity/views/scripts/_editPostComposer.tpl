<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesadvancedactivity
 * @package    Sesadvancedactivity
 * @copyright  Copyright 2016-2017 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: _editPostComposer.tpl  2017-01-12 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
?>
<div class="sesact_editpost sesbasic_bxs sesbasic_clearfix">	
  <div class="sesact_post_container sesbasic_clearfix">
  	<div class="sesact_editpost_title"><?php echo $this->translate("Edit Post"); ?></div>
    <form data-status="<?php echo $this->action->type == 'status' ? '1' : 0; ?>" method="post" class="edit-activity-form"" enctype="application/x-www-form-urlencoded">
    	<div class="sesact_post_box sesbasic_clearfix">
      	<div class="sesact_post_box_img">
        <?php echo $this->htmlLink('javascript:;', $this->itemPhoto($this->viewer(), 'thumb.icon', $this->viewer()->getTitle()), array()) ?>
        </div>
        <div class="compose-container" id="compose-container">
        <textarea id="edit_activity_body" cols="1" rows="1" name="body" placeholder="<?php echo $this->escape($this->translate('Post Something...')) ?>"><?php echo $this->action->body; ?></textarea>
        </div>
        <input type="hidden" name="userphotoalign" value="<?php echo  $this->userphotoalign; ?>">
        <input type="hidden" name="action_id" value="<?php echo  $this->action->getIdentity(); ?>">
        <div id="sesadvancedactivity-menu" class="sesadvancedactivity-menu sesact_post_tools">
          <span class="sesadvancedactivity-menu-selector" id="sesadvancedactivity-menu-selector"></span>
        <?php if(in_array('tagUseses',$this->composerOptions)){ ?>
					<span class="sesact_post_tool_i tool_i_tag">
          	<a href="javascript:;" id="sesadvancedactivity_tag_edit"  class="sesadv_tooltip" title="<?php echo $this->translate('Tag People'); ?>">&nbsp;</a>
          </span>
        <?php } ?>
        <?php if(in_array('locationses',$this->composerOptions)){ ?>
          <span class="sesact_post_tool_i tool_i_location">
          	<a href="javascript:;" id="sesadvancedactivity_location_edit"  title="<?php echo $this->translate('Check In'); ?>" class="sesadv_tooltip">&nbsp;</a>
          </span>
        <?php } ?>
        <?php if(in_array('smilesses',$this->composerOptions)){ ?>
          <span class="sesact_post_tool_i tool_i_emoji">
            <a href="javascript:;" id="sesadvancedactivityemoji-edit-a"  class="sesadv_tooltip" title="<?php echo $this->translate('Emoticons'); ?>">&nbsp;</a>
            <div id="sesadvancedactivityemoji_edit" class="ses_emoji_container sesbasic_bxs">
            	<div class="ses_emoji_container_arrow"></div>
              <div class="ses_emoji_container_inner sesbasic_clearfix">
              	<div class="ses_emoji_holder">
                	<div class="sesbasic_loading_container" style="height:100%;"></div>
                </div>
              </div>
            </div>
          </span>
        <?php } ?>
        </div>
        
        <div class="sesact_post_tags sesbasic_text_light">
          <span style="display:<?php echo $this->location || count($this->members) ? 'block' : 'none'; ?>;" id="dash_elem_act_edit">-</span>	<span id="tag_friend_cnt_edit" style="display:none;"> with </span> <span id="location_elem_act_edit"><?php echo $this->location ? 'at <a href="javascript:;" class="seloc_clk_edit">'.$this->location->venue.'</a>' : ''; ?></span>
        </div>
      </div>
     <?php if($this->action->type == 'post_self_buysell'){ ?>
      <div id="sescomposer-tray-container-edit">
        <div id="compose-tray-edit" class="compose-tray">
        <div id="compose-buysell-edit-body" class="compose-body">
        	<div class="sesact_sell_composer">
          <div class="sesact_sell_composer_title">
            <input type="text" id="buysell-title-edit" value="<?php echo $this->item->getTitle(); ?>" placeholder="<?php echo $this->translate('What are you selling?'); ?>" name="buysell-title">
            <span id="buysell-title-count-edit" class="sesbasic_text_light">100</span>
          </div>
          <div class="sesact_sell_composer_price">
          	<span class="sesact_sell_composer_price_currency">
              <?php $fullySupportedCurrencies = Engine_Api::_()->sesbasic()->getSupportedCurrency();
                  $currentCurrency = $this->item->currency;;
                  if(Engine_Api::_()->sesbasic()->multiCurrencyActive()){
                    $currencyData = '<select name ="buysell-currency">';
                    foreach ($fullySupportedCurrencies as $key => $values) {
                      if($currentCurrency == $key)
                        $active ='selected';
                      else
                        $active ='';
                      $currencyData .= '<option val="'.$key.'" '.$active.' >'.$key.'</option>';
                    }
                      $currencyData .= "</select>";
                  }else{
                      $currencyData = Engine_Api::_()->sesadvancedactivity()->getCurrencySymbol();
                  }
              ?>
            <?php echo $currencyData; ?>
            </span>
            <span class="sesact_sell_composer_price_input"><input type="text" id="buysell-price-edit" value="<?php echo $this->item->price; ?>" placeholder="<?php echo $this->translate('Add price'); ?>" name="buysell-price" /></span>
          </div>
          <div class="sesact_sell_composer_location">
          	<i class="sesbasic_text_light fa fa-map-marker"></i>
            <span id="locValuesbuysell-element-edit"></span>
            <span id="buyselllocal-edit">
              <input type="text" id="buysell-location-edit" value="<?php echo !empty($this->locationBuySell) ? $this->locationBuySell->venue : '' ?>" placeholder="<?php echo $this->translate('Add location (optional)'); ?>" name="buysell-location" autocomplete="off">
              <input type="hidden" name="activitybuyselllng" value="<?php echo !empty($this->locationBuySell) ? $this->locationBuySell->lng : '' ?>" id="activitybuyselllng-edit">
              <input type="hidden" name="activitybuyselllat" value="<?php echo !empty($this->locationBuySell) ? $this->locationBuySell->lat : '' ?>" id="activitybuyselllat-edit">
            </span>
          </div>
          <div class="sesact_sell_composer_des">
            <textarea id="buysell-description-edit" placeholder="<?php echo $this->translate('Describe your item (optional)'); ?>" name="buysell-description"><?php echo $this->item->getDescription(); ?></textarea>
          </div>
          
          <?php if( $this->action->attachment_count){ ?>
            <div class="sesact_sell_composer_images sesbasic_custom_horizontal_scroll">
              <?php foreach( $this->action->getAttachments() as $attachment){ ?>
              	<div class="_buyselleditimg"><img src="<?php echo $attachment->item->getPhotoUrl() ?>" alt="" /></div>
               <?php } ?>
            </div>
          <?php } ?>
          </div>
        </div>
      </div>
    </div>
    <?php } ?>
    <script type="application/javascript">
  function sessmoothboxcallback(){
    if(sesJqueryObject('#buysell-location-edit').val()){
      sesJqueryObject('#locValuesbuysell-element-edit').html('<span class="tag">'+sesJqueryObject('#buysell-location-edit').val()+' <a href="javascript:void(0);" class="buysellloc_remove_act_edit">x</a></span>');
        sesJqueryObject('#locValuesbuysell-element-edit').show();
        sesJqueryObject('#buyselllocal-edit').hide();
        document.getElementById('activitybuyselllng-edit').value = sesJqueryObject('#activitybuyselllng-edit').val();
        document.getElementById('activitybuyselllat-edit').value = sesJqueryObject('#activitybuyselllat-edit').val();
    }
      if(document.getElementById('buysell-location-edit')) {
        var input = document.getElementById('buysell-location-edit');
        var autocomplete = new google.maps.places.Autocomplete(input);
        google.maps.event.addListener(autocomplete, 'place_changed', function () {
          var place = autocomplete.getPlace();
          if (!place.geometry) {
            return;
          }
          sesJqueryObject('#locValuesbuysell-element-edit').html('<span class="tag">'+sesJqueryObject('#buysell-location-edit').val()+' <a href="javascript:void(0);" class="buysellloc_remove_act_edit">x</a></span>');
          sesJqueryObject('#locValuesbuysell-element-edit').show();
          sesJqueryObject('#buyselllocal-edit').hide();
          document.getElementById('activitybuyselllng-edit').value = place.geometry.location.lng();
          document.getElementById('activitybuyselllat-edit').value = place.geometry.location.lat();
        });
        sesJqueryObject('#buysell-title-edit').trigger('input');
      }
			jqueryObjectOfSes(".sesbasic_custom_horizontal_scroll").mCustomScrollbar({
					axis:"x",
					theme:"light-3",
					advanced:{autoExpandHorizontalScroll:true}
		});
         sesJqueryObject('#edit_activity_body').hashtags();
        isOnEditField = true;
         <?php if(count($this->mentionData)){ ?>
           mentionsCollectionValEdit = <?php echo json_encode($this->mentionData); ?>;
         <?php } ?>
         EditFieldValue = <?php echo json_encode($this->action->body,JSON_HEX_QUOT | JSON_HEX_TAG); ?>;
          sesJqueryObject('textarea#edit_activity_body').mentionsInput({
            onDataRequest:function (mode, query, callback) {
              sesJqueryObject.getJSON('sesadvancedactivity/ajax/friends/query/'+query, function(responseData) {
                responseData = _.filter(responseData, function(item) { return item.name.toLowerCase().indexOf(query.toLowerCase()) > -1 });
                callback.call(this, responseData);
              });
            },
            //defaultValue:<?php echo json_encode($this->action->body,JSON_HEX_QUOT | JSON_HEX_TAG); ?>,
          onCaret: true
          });
         sesJqueryObject('textarea#edit_activity_body').mentionsInput("update");
         sesJqueryObject('textarea#edit_activity_body').focus();
    sesadvtooltip();
  } 
  function sessmoothboxcallbackclose(){
     sesJqueryObject('#toValuesChanges').attr('id','toValues');
     sesJqueryObject('#toValuesChanges-element').attr('id','toValues-element');
     sesJqueryObject('#toValuesChanges-wrapper').attr('id','toValues-wrapper');
      sesJqueryObject('textarea#edit_activity_body').mentionsInput('reset');
  }
  function sessmoothboxcallbackBefore(){
    sesJqueryObject('#toValues').attr('id','toValuesChanges');
    sesJqueryObject('#toValues-element').attr('id','toValuesChanges-element');
    sesJqueryObject('#toValues-wrapper').attr('id','toValuesChanges-wrapper');
  }
</script>
       <div class="sesact_post_tag_container sesbasic_clearfix sesact_post_tag_cnt_edit" style="display:<?php echo  !count($this->members) ? 'none' : 'none'; ?>;">
        <span class="tag">With</span>
        <div class="sesact_post_tags_holder">
          <div id="toValues-element">
          <?php $tagUserIds = ''; ?>
            <?php foreach($this->members as $members){
                  $user = Engine_Api::_()->getItem('user',$members['user_id']);
                  if(!$user)
                    contunue;
                  $tagUserIds = $members['user_id'].','.$tagUserIds;
            ?>
              <span id="tospan_<?php echo $user->getIdentity(); ?>" class="tag"><?php echo $user->getTitle(); ?> <a href="javascript:void(0);" onclick="this.parentNode.destroy();removeFromToValue('<?php echo $user->getIdentity(); ?>', 'toValues');">x</a></span>
           <?php } ?>
          </div>
        	<div class="sesact_post_tag_input">
          	<input type="text" placeholder="<?php echo $this->translate('Who are you with?'); ?>" id="tag_friends_input_edit" />
            <div id="toValues-wrapper" style="display:none">
            <input type="hidden" id="toValues" name="tag_friends" value="<?php echo rtrim($tagUserIds,','); ?>">
            </div>
          </div>
        </div>	
      </div>
      <div class="sesact_post_tag_container sesbasic_clearfix sesact_post_location_container sesact_post_location_container_edit" style="display:<?php echo !$this->location ? 'none' : 'none'; ?>;">
        <span class="tag">At</span>
        <div class="sesact_post_tags_holder">
          <div id="locValuesEdit-element"></div>
        	<div class="sesact_post_tag_input">
          	<input type="text" placeholder="<?php echo $this->translate('Where are you?'); ?>" name="tag_location" id="tag_location_edit" value="<?php echo $this->location ? $this->location->venue : ''; ?>"/>
            <input type="hidden" name="activitylng" id="activitylngEdit" value="<?php echo !empty($this->location->lat) ? $this->location->lng : '' ?>">
            <input type="hidden" name="activitylat" id="activitylatEdit" value="<?php echo !empty($this->location->lng) ? $this->location->lat : '' ?>">
          </div>
        </div>	
      </div>
      
      <div id="compose-menu" class="sesact_compose_menu">
        <input type="hidden" name="privacy" id="privacy_edit" value="<?php echo $this->action->privacy; ?>">
      	<div class="sesact_compose_menu_btns">
        	<div class="sesact_privacy_chooser sesact_pulldown_wrapper">
          	<a href="javascript:void(0);" class="sesact_privacy_btn  sesact_privacy_btn_edit sesadv_tooltip"><i id="sesadv_privacy_icon_edit"></i><span id="adv_pri_option_edit"><?php echo $this->translate('Everyone'); ?></span><i class="fa fa-caret-down"></i></a>
            <div class="sesact_pulldown">
              <div class="sesact_pulldown_cont isicon">
                <ul class="adv_privacy_optn_edit">
                  <li  data-src="everyone" class=""><a href="javascript:;"><i class="sesact_public"></i><span><?php echo $this->translate('Everyone'); ?></span></a></li>
                  <li data-src="networks"><a href="javascript:;"><i class="sesact_network"></i><span><?php echo $this->translate('Friends & Networks'); ?></span></a></li>
                  <li data-src="friends"><a href="javascript:;"><i class="sesact_friends"></i><span><?php echo $this->translate('Friends Only'); ?></span></a></li>
                  <li data-src="onlyme"><a href="javascript:;"><i class="sesact_me"></i><span><?php echo $this->translate('Only Me'); ?></span></a></li>
                  <?php if(count($this->usernetworks)){ ?>
                  <li class="_sep"></li>
                  <?php foreach($this->usernetworks as $usernetworks){ ?>
                    <li data-src="network_list" class="network sesadv_network sesadv_network_edit" data-rel="<?php echo $usernetworks->getIdentity(); ?>"><a href="javascript:;"><i class="sesact_network"></i><span><?php echo $usernetworks->getTitle(); ?></span></a></li>
                  <?php } ?>
                  <li class="multiple mutiselectedit" data-rel="network-multi"><a href="javascript:;"><i class="sesact_network"></i><span><?php echo $this->translate('Multiptle Networks'); ?></span></a></li>
                  <?php } ?>
                  <?php if(count($this->userlists)){ ?>
                  <li class="_sep"></li>
                  <?php foreach($this->userlists as $userlists){ ?>
                    <li data-src="members_list" class="lists sesadv_list sesadv_list_edit" data-rel="<?php echo $userlists->getIdentity(); ?>"><a href="javascript:;"><i class="sesact_list"></i><span><?php echo $userlists->getTitle(); ?></span></a></li>
                  <?php } ?>
                  <li class="multiple mutiselectedit" data-rel="lists-multi"><a href="javascript:;"><i class="sesact_list"></i><span><?php echo $this->translate('Multiptle Lists'); ?></span></a></li>
                  <?php } ?>
                </ul>
              </div>													
            </div>
          </div>
        	<button id="compose-submit" type="submit"><?php echo $this->translate("Save") ?></button>
        </div>
        <span class="composer_targetpost_edit_toggle sesadv_tooltip <?php echo ($this->targetPost) ? 'composer_targetpost_edit_toggle_active' : '' ?>" title="<?php echo $this->translate('Choose Preferred Audience'); ?>" href="javascript:void(0);">
          
          <?php if($this->targetPost){ ?>
          <input id="compose-targetpost-edit-form-input" class="compose-form-input" type="checkbox" checked="checked" name="post_to_targetpost" style="display: none;">
          <input type="hidden" id="country_name_edit" name="targetpost[country_name]" value="<?php echo $this->targetPost->country_name; ?>">
          <input type="hidden" id="city_name_edit" name="targetpost[city_name]" value="<?php echo $this->targetPost->city_name; ?>">
          <input type="hidden" id="location_send_edit" name="targetpost[location_send]" value="<?php echo $this->targetPost->location_send; ?>">
          <input type="hidden" id="location_city_edit" name="targetpost[location_city]" value="<?php echo $this->targetPost->location_city; ?>">
          <input type="hidden" id="location_country_edit" name="targetpost[location_country]" value="<?php echo $this->targetPost->location_country; ?>">
          <input type="hidden" id="gender_send_edit" name="targetpost[gender_send]" value="<?php echo $this->targetPost->gender_send; ?>">
          <input type="hidden" id="age_min_send_edit" name="targetpost[age_min_send]" value="<?php echo $this->targetPost->age_min_send; ?>">
          <input type="hidden" id="age_max_send_edit" name="targetpost[age_max_send]" value="<?php echo $this->targetPost->age_max_send; ?>">
          <input type="hidden" id="targetpostlat_edit" name="targetpost[targetpostlat]" value="<?php echo $this->targetPost->lat; ?>">
          <input type="hidden" id="targetpostlng_edit" name="targetpost[targetpostlng]" value="<?php echo $this->targetPost->lng; ?>">
          <input type="hidden" id="targetpostlatcity_edit" name="targetpost[targetpostlatcity]" value="<?php echo $this->targetPost->lat; ?>">
          <input type="hidden" id="targetpostlngcity_edit" name="targetpost[targetpostlngcity]" value="<?php echo $this->targetPost->lng; ?>">
          <?php }else{ ?>
          <input id="compose-targetpost-edit-form-input" class="compose-form-input" type="checkbox" name="post_to_targetpost" style="display: none;">
          <?php } ?>
        </span>
      </div>
  	</form>
    

<script type="text/javascript">
var savingtextActivityPost = "<i class='fa fa-circle-o-notch fa-spin'></i>";
var savingtextActivityPostOriginal = "<?php echo $this->translate('Save') ?>";
//set default privacy of logged-in user
 en4.core.runonce.add(function() {
  var privacy = sesJqueryObject('#privacy_edit').val();
  if(privacy){
    if(privacy == 'everyone')
      sesJqueryObject('.adv_privacy_optn_edit >li[data-src="everyone"]').find('a').trigger('click');  
    else if(privacy == 'networks')
      sesJqueryObject('.adv_privacy_optn_edit >li[data-src="networks"]').find('a').trigger('click'); 
    else if(privacy == 'friends')
      sesJqueryObject('.adv_privacy_optn_edit >li[data-src="friends"]').find('a').trigger('click'); 
    else if(privacy == 'onlyme')
      sesJqueryObject('.adv_privacy_optn_edit >li[data-src="onlyme"]').find('a').trigger('click'); 
    else if(privacy && privacy.indexOf('network_list_') > -1){
      var exploidV =  privacy.split(',');
      for(i=0;i<exploidV.length;i++){
         var id = exploidV[i].replace('network_list_','');
         sesJqueryObject('.sesadv_network_edit[data-rel="'+id+'"]').addClass('active');
      }
     sesJqueryObject('#adv_pri_option_edit').html('<?php echo $this->translate("Multiple Networks"); ?>');
     sesJqueryObject('.sesact_privacy_btn_edit').attr('title','<?php echo $this->translate("Multiple Networks"); ?>');;
     sesJqueryObject('#sesadv_privacy_icon_edit').removeAttr('class').addClass('sesact_network');
   }else if(privacy && privacy.indexOf('member_list_') > -1){
      var exploidV =  privacy.split(',');
      for(i=0;i<exploidV.length;i++){
         var id = exploidV[i].replace('member_list_','');
         sesJqueryObject('.sesadv_list_edit[data-rel="'+id+'"]').addClass('active');
      }
      sesJqueryObject('#adv_pri_option_edit').html('<?php echo $this->translate("Multiple Lists"); ?>');
     sesJqueryObject('.sesact_privacy_btn_edit').attr('title','<?php echo $this->translate("Multiple Lists"); ?>');;
     sesJqueryObject('#sesadv_privacy_icon_edit').removeAttr('class').addClass('sesact_list');
   }
  }
  sesadvtooltip();
});

    
  function removeFromToValueEdit(id) {    
    
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
        removeToValueEdit(recipientsArray[i], toValueArray);
      }
    }
    else{
      removeToValueEdit(id, toValueArray);
    }
    $('tag_friends_input_edit').disabled = false;
    var firstElem = sesJqueryObject('#toValues-element > span').eq(0).text();
    var countElem = sesJqueryObject('#toValues-element').children().length;
    var html = '';
    
    if(!firstElem.trim()){
      sesJqueryObject('#tag_friend_cnt_edit').html('');
      sesJqueryObject('#tag_friend_cnt_edit').hide();
      if(!sesJqueryObject('#tag_location_edit').val())
      sesJqueryObject('#dash_elem_act_edit').hide();
      return;
    }else if(countElem == 1){
      html = '<a href="javascript:;" class="sestag_clk_edit">'+firstElem.replace('x','')+'</a>';
    }else if(countElem > 2){
      html = '<a href="javascript:;" class="sestag_clk_edit">'+firstElem.replace('x','')+'</a>';
      html = html + ' and <a href="javascript:;" class="sestag_clk_edit">'+(countElem-1)+' others</a>';
    }else{
      html = '<a href="javascript:;" class="sestag_clk_edit">'+firstElem.replace('x','')+'</a>';
      html = html + ' and <a href="javascript:;" class="sestag_clk_edit">'+sesJqueryObject('#toValues-element > span').eq(1).text().replace('x','')+'</a>';
    }
    sesJqueryObject('#tag_friend_cnt_edit').html('with '+html);
    sesJqueryObject('#tag_friend_cnt_edit').show();
    sesJqueryObject('#dash_elem_act_edit').show();
    
  }

  function removeToValueEdit(id, toValueArray){
    for (var i = 0; i < toValueArray.length; i++){
      if (toValueArray[i]==id) toValueIndex =i;
    }

    toValueArray.splice(toValueIndex, 1);
    $('toValues').value = toValueArray.join();
  }

  en4.core.runonce.add(function() {
      new Autocompleter.Request.JSON('tag_friends_input_edit', '<?php echo $this->url(array('module' => 'sesadvancedactivity', 'controller' => 'index', 'action' => 'suggest'), 'default', true) ?>', {
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
          if( $('toValues').value.split(',').length >= maxRecipientsEdit ){
            $('tag_friends_input_edit').disabled = true;
          }
          var firstElem = sesJqueryObject('#toValues-element > span').eq(0).text();
          var countElem = sesJqueryObject('#toValues-element  > span').children().length;
          var html = '';
          if(countElem == 1){
            html = '<a href="javascript:;" class="sestag_clk_edit">'+firstElem.replace('x','')+'</a>';
          }else if(countElem > 2){
            html = '<a href="javascript:;" class="sestag_clk_edit">'+firstElem.replace('x','')+'</a>';
            html = html + ' and <a href="javascript:;"  class="sestag_clk_edit">'+(countElem-1)+' others</a>';
          }else{
            html = '<a href="javascript:;" class="sestag_clk_edit">'+firstElem.replace('x','')+'</a>';
            html = html + ' and <a href="javascript:;" class="sestag_clk_edit">'+sesJqueryObject('#toValues-element > span').eq(1).text().replace('x','')+'</a>';
          }
          sesJqueryObject('#tag_friend_cnt_edit').html('with '+html);
          sesJqueryObject('#tag_friend_cnt_edit').show();
          sesJqueryObject('#dash_elem_act_edit').show();
        }
      });
      
      new Composer.OverText($('tag_friends_input_edit'), {
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
    

    <script type="text/javascript">
      en4.core.runonce.add(function() {
       sesJqueryObject('#edit_activity_body').show();
       tagLocationWorkEdit();
       autosize(sesJqueryObject('#edit_activity_body'));
       sesJqueryObject('#edit_activity_body').trigger('keyup');
       <?php if(count($this->members)){ ?>
        var firstElem = sesJqueryObject('#toValues-element > span').eq(0).text();
        var countElem = sesJqueryObject('#toValues-element').children().length;
        var html = '';
        
        if(!firstElem.trim()){
          sesJqueryObject('#tag_friend_cnt_edit').html('');
          sesJqueryObject('#tag_friend_cnt_edit').hide();
          if(!sesJqueryObject('#tag_location_edit').val())
          sesJqueryObject('#dash_elem_act_edit').hide();
          return;
        }else if(countElem == 1){
          html = '<a href="javascript:;" class="sestag_clk_edit">'+firstElem.replace('x','')+'</a>';
        }else if(countElem > 2){
          html = '<a href="javascript:;" class="sestag_clk_edit">'+firstElem.replace('x','')+'</a>';
          html = html + ' and <a href="javascript:;" class="sestag_clk_edit">'+(countElem-1)+' others</a>';
        }else{
          html = '<a href="javascript:;" class="sestag_clk_edit">'+firstElem.replace('x','')+'</a>';
          html = html + ' and <a href="javascript:;" class="sestag_clk_edit">'+sesJqueryObject('#toValues-element > span').eq(1).text().replace('x','')+'</a>';
        }
        sesJqueryObject('#tag_friend_cnt_edit').html('with '+html);
        sesJqueryObject('#tag_friend_cnt_edit').show();
        sesJqueryObject('#dash_elem_act_edit').show();
       
       <?php } ?>
       var input = document.getElementById('tag_location_edit');
        var autocomplete = new google.maps.places.Autocomplete(input);
        google.maps.event.addListener(autocomplete, 'place_changed', function () {
          var place = autocomplete.getPlace();
          if (!place.geometry) {
            return;
          }
          tagLocationWorkEdit();
          document.getElementById('activitylngEdit').value = place.geometry.location.lng();
          document.getElementById('activitylatEdit').value = place.geometry.location.lat();
        });
      });
     function openTargetPostPopupEdit(){
        if(!sesJqueryObject('#location_send_edit').length)
        sesJqueryObject('.composer_targetpost_edit_toggle').append('<input type="hidden" id="country_name_edit"  name="targetpost[country_name]" value=""><input type="hidden" id="city_name_edit"  name="targetpost[city_name]" value=""><input type="hidden" id="location_send_edit"  name="targetpost[location_send]" value=""><input type="hidden" id="location_city_edit" name="targetpost[location_city]" value=""><input type="hidden" id="location_country_edit"  name="targetpost[location_country]"value=""><input type="hidden" id="gender_send_edit" name="targetpost[gender_send]" value=""><input type="hidden" id="age_min_send_edit" name="targetpost[age_min_send]" value=""><input type="hidden" id="age_max_send_edit" name="targetpost[age_max_send]" value=""><input type="hidden" id="targetpostlat_edit" name="targetpost[targetpostlat]" value=""><input type="hidden" id="targetpostlng_edit" name="targetpost[targetpostlng]" value=""><input type="hidden" id="targetpostlatcity_edit" name="targetpost[targetpostlatcity]" value=""><input type="hidden" id="targetpostlngcity_edit" name="targetpost[targetpostlngcity]" value="">');
        
        <?php 
        $optionHTML = '';
        for($i=14;$i<99;$i++){ 
            $optionHTML = $optionHTML.'<option value="'.$i.'">'.$i.'</option>';		
         } ?>
        var htmlOptions = '<?php echo $optionHTML; ?>';
        msg = "<div class='sesact_target_popup sesbasic_bxs clearfix'><div class='sesact_target_post_popup_header'><?php echo $this->translate('Choose Preferred Audience'); ?></div><div class='sesact_target_post_popup_cont'><p><?php echo $this->translate('Choose preferred audience for your post.'); ?></p>";
        var memberenable = '<?php echo Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled("sesmember"); ?>';
        
        if(memberenable){
          msg += "<div class='sesact_target_popup_field clearfix'><div class='sesact_target_popup_field_label'><?php echo $this->translate('Location');?> <i class='sesadv_tooltip fa fa-info-circle sesbasic_text_light' title='Enter one or more countries, states or cities to show your post only to the people in those locations.'></i></div><div class='sesact_target_popup_field_element sesact_target_popup_field_element_edit'><span><input type='radio' checked='checked' class='selected_coun_val selected_coun_val_edit' name='country_type_sel_edit' value='world'> <?php echo $this->translate('World');?></span><span><input type='radio' name='country_type_sel_edit' class='selected_coun_val selected_coun_val_edit' value='country'> <?php echo $this->translate('Country');?></span><span><input class='selected_coun_val selected_coun_val_edit' type='radio' name='country_type_sel_edit' value='city'> <?php echo $this->translate('By City');?></span><div class='sesact_target_popup_field_input'><input type='text' name='country_sel' id='country_sel_edit' placeholder='<?php echo $this->translate("Select Country");?>' style='display:none;'><input type='text' name='city_sel' id='city_sel_edit' placeholder='<?php echo $this->translate("Select City");?>' style='display:none;'><p class='sesact_target_popup_error' style='display:none;' id='location_error_sel_edit'><?php echo $this->translate("Please select value.");?></p></div></div></div>";  
        }
        msg += "<div class='sesact_target_popup_field clearfix'>"+"<div class='sesact_target_popup_field_label'><?php echo $this->translate('Gender');?> <i class='sesadv_tooltip fa fa-info-circle sesbasic_text_light' title='<?php echo ("Choose to share your post with &quot;All&quot; or specific gender.");?>'></i></div><div class='sesact_target_popup_field_element'><span><input type='radio' checked='checked'  name='gender_type_sel_edit' value='all'> <?php echo $this->translate('All');?></span><span><input type='radio' name='gender_type_sel_edit'  value='male'><?php echo $this->translate('Men');?></span><span><input type='radio' name='gender_type_sel_edit' value='women'> <?php echo $this->translate('Women');?></span></div></div>"+"<div class='sesact_target_popup_field'><div class='sesact_target_popup_field_label'><?php echo $this->translate('Age');?> <i class='sesadv_tooltip fa fa-info-circle sesbasic_text_light' title='<?php echo $this->translate("Select the minimum and maximum age of the people who will find your ad relevant.");?>'></i></div><div class='sesact_target_popup_field_element'><span><select name='age_sel_min' id='age_sel_min_edit'><option value='13'>13</option>"+htmlOptions+"</select> - <select name='age_sel_max' id='age_sel_max_edit'>"+htmlOptions+"<option value='99'>99+</option></select><p class='sesact_target_popup_error' style='display:none;' id='age_error_sel'><?php echo $this->translate("Age max field is greater than Age min field.");?></p></div></div>"+"</div><div class='sesact_target_post_popup_btm'><button href=\"javascript:void(0);\" class='savevaluessel notclose'><?php echo $this->translate("Save"); ?></button><button href=\"javascript:void(0);\" class='removevaluessel removevaluessel_edit notclose' style='display:none;'><?php echo $this->translate("Remove"); ?></button><button href=\"javascript:void(0);\" onclick=\"javascript:parent.Smoothbox.close()\" class='secondary notclose'><?php echo $this->translate("Close"); ?></button></div></div>";
        Smoothbox.open(msg);
        //change values
        var location_send = sesJqueryObject('#location_send_edit');
        var location_city = sesJqueryObject('#location_city_edit');
        var location_country = sesJqueryObject('#location_country_edit');
        var gender_send = sesJqueryObject('#gender_send_edit');
        var age_min_send = sesJqueryObject('#age_min_send_edit');
        var age_max_send = sesJqueryObject('#age_max_send_edit');
        if(location_send.val()  == 'country'){
          sesJqueryObject('#country_sel_edit').show();
          sesJqueryObject('#city_sel_edit').hide();
        }else if(location_send.val() == 'city'){
          sesJqueryObject('#country_sel_edit').hide();
          sesJqueryObject('#city_sel_edit').show();
        }else{
          sesJqueryObject('#country_sel_edit').hide();
          sesJqueryObject('#city_sel_edit').hide();
        }
        sesJqueryObject('input:radio[name="country_type_sel_edit"][value="'+location_send.val()+'"]').attr('checked',true);
        sesJqueryObject('#country_sel_edit').val(location_country.val());
        sesJqueryObject('#city_sel_edit').val(location_city.val());
        sesJqueryObject('input:radio[name="gender_type_sel_edit"][value="'+gender_send.val()+'"]').attr('checked',true);
        sesJqueryObject('#age_sel_min_edit').val(age_min_send.val());
        sesJqueryObject('#age_sel_max_edit').val(age_max_send.val());
        if(sesJqueryObject('#compose-targetpost-edit-form-input').is(':checked'))
          sesJqueryObject('.removevaluessel_edit').show();
        sesJqueryObject('#TB_ajaxContent').addClass('sesact_target_post_popup_wrapper sesbasic_bxs');
        sesadvtooltip();
        if(memberenable)
          makeGoogleMapSelect();
        if(sesJqueryObject('#location_send_edit').length)
        sesJqueryObject(".sesact_target_popup_field_element_edit input:radio[value='"+sesJqueryObject('#location_send_edit').val()+"']").attr("checked", true).trigger('click');
     }
     
    </script>
  </div>
</div>  
