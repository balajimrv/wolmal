/* $Id:editComposer.js  2017-01-12 00:00:00 SocialEngineSolutions $*/

sesJqueryObject(document).on('click','#sesadvancedactivity_location_edit, .seloc_clk_edit',function(e){
  that = sesJqueryObject(this);
  if(sesJqueryObject(this).hasClass('.seloc_clk_edit'))
     that = sesJqueryObject('#sesadvancedactivity_location_edit');
   if(sesJqueryObject(this).hasClass('active')){
     sesJqueryObject(this).removeClass('active');
     sesJqueryObject('.sesact_post_location_container_edit').hide();
     return;
   }
   sesJqueryObject('.sesact_post_location_container_edit').show();
   sesJqueryObject(this).addClass('active');
});
sesJqueryObject(document).on('click','#sesadvancedactivity_tag_edit, .sestag_clk_edit',function(e){
  that = sesJqueryObject(this);
  if(sesJqueryObject(this).hasClass('.sestag_clk_edit'))
     that = sesJqueryObject('#sesadvancedactivity_tag_edit');
   if(sesJqueryObject(that).hasClass('active')){
     sesJqueryObject(that).removeClass('active');
     sesJqueryObject('.sesact_post_tag_cnt_edit').hide();
     return;
   }
   sesJqueryObject('.sesact_post_tag_cnt_edit').show();
   sesJqueryObject(that).addClass('active');
});
var requestEmojiA;
sesJqueryObject(document).on('click','#sesadvancedactivityemoji-edit-a',function(){
    if(sesJqueryObject(this).hasClass('active')){
      sesJqueryObject(this).removeClass('active');
      sesJqueryObject('#sesadvancedactivityemoji_edit').hide();
      return false;
     }
      sesJqueryObject(this).addClass('active');
      sesJqueryObject('#sesadvancedactivityemoji_edit').show();
      if(sesJqueryObject(this).hasClass('complete'))
        return false;
       if(typeof requestEmojiA != 'undefined')
        requestEmojiA.cancel();
       var that = this;
       var url = en4.core.baseUrl + 'sesadvancedactivity/ajax/emoji/edit/true';
       requestEmojiA = new Request.HTML({
        url : url,
        data : {
          format : 'html',
        },
        evalScripts : true,
        onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
          sesJqueryObject('#sesadvancedactivityemoji_edit').find('.ses_emoji_container_inner').find('.ses_emoji_holder').html(responseHTML);
          sesJqueryObject(that).addClass('complete');
          sesadvtooltip();
         jqueryObjectOfSes(".sesbasic_custom_scroll").mCustomScrollbar({
            theme:"minimal-dark"
         });
        }
      });
     requestEmojiA.send();
});
sesJqueryObject(document).on('click','.select_emoji_advedit > img',function(e){
    var code = sesJqueryObject(this).parent().parent().attr('rel');
    var html = sesJqueryObject('#edit_activity_body').val();
    if(html == '<br>')
      sesJqueryObject('#edit_activity_body').val('');
    sesJqueryObject('#edit_activity_body').val( sesJqueryObject('#edit_activity_body').val()+' '+code)
    sesJqueryObject('#sesadvancedactivityemoji-edit-a').trigger('click');
  });
sesJqueryObject(document).on('click','.adv_privacy_optn_edit li a',function(e){
  e.preventDefault();
  if(!sesJqueryObject(this).parent().hasClass('multiple')){
    sesJqueryObject('.adv_privacy_optn_edit > li').removeClass('active');
    var text = sesJqueryObject(this).text();
    sesJqueryObject('.sesact_privacy_btn_edit').attr('title',text);;
    sesJqueryObject(this).parent().addClass('active');
    sesJqueryObject('#adv_pri_option_edit').html(text);
    sesJqueryObject('#sesadv_privacy_icon').remove();
    sesJqueryObject('<i id="sesadv_privacy_icon" class="'+sesJqueryObject(this).find('i').attr('class')+'"></i>').insertBefore('#adv_pri_option_edit');
    
    if(sesJqueryObject(this).parent().hasClass('sesadv_network_edit'))
      sesJqueryObject('#privacy_edit').val(sesJqueryObject(this).parent().attr('data-src')+'_'+sesJqueryObject(this).parent().attr('data-rel'));
    else if(sesJqueryObject(this).parent().hasClass('sesadv_list_edit'))
      sesJqueryObject('#privacy_edit').val(sesJqueryObject(this).parent().attr('data-src')+'_'+sesJqueryObject(this).parent().attr('data-rel'));
   else
    sesJqueryObject('#privacy_edit').val(sesJqueryObject(this).parent().attr('data-src'));
  }
  sesJqueryObject('.sesact_privacy_btn_edit').parent().removeClass('sesact_pulldown_active');
});
sesJqueryObject(document).on('click','.mutiselectedit',function(e){
  if(sesJqueryObject(this).attr('data-rel') == 'network-multi')
    var elem = 'sesadv_network_edit';
  else
    var elem = 'sesadv_list_edit';
  var elemens = sesJqueryObject('.'+elem);
  var html = '';
  for(i=0;i<elemens.length;i++){
    html += '<li><input class="checkbox" type="checkbox" value="'+sesJqueryObject(elemens[i]).attr('data-rel')+'">'+sesJqueryObject(elemens[i]).text()+'</li>';
  }
  en4.core.showError('<form id="'+elem+'_select" class="_privacyselectpopup"><p>'+en.core.language.translate("It is a long established fact that a reader will be distracted")+'</p><ul class="sesbasic_clearfix">'+html+'</ul><div class="_privacyselectpopup_btns sesbasic_clearfix"><button type="submit">'+en.core.language.translate("Select")+'</button><button class="close" onclick="Smoothbox.close();return false;">'+en.core.language.translate("Close")+'</button></div></form>');  
  //pre populate
  var valueElem = sesJqueryObject('#privacy_edit').val();
  if(valueElem && valueElem.indexOf('network_list_') > -1 && elem == 'sesadv_network_edit'){
    var exploidV =  valueElem.split(',');
    for(i=0;i<exploidV.length;i++){
       var id = exploidV[i].replace('network_list_','');
       sesJqueryObject('.checkbox[value="'+id+'"]').prop('checked', true);
    }
   }else if(valueElem && valueElem.indexOf('member_list_') > -1 && elem == 'sesadv_list_edit'){
    var exploidV =  valueElem.split(',');
    for(i=0;i<exploidV.length;i++){
       var id = exploidV[i].replace('member_list_','');
       sesJqueryObject('.checkbox[value="'+id+'"]').prop('checked', true);
    }
   }
});
sesJqueryObject(document).on('submit','#sesadv_list_edit_select',function(e){
  e.preventDefault();
  var isChecked = false;
   var sesadv_list_select = sesJqueryObject('#sesadv_list_edit_select').find('[type="checkbox"]');
   var valueL = '';
   for(i=0;i<sesadv_list_select.length;i++){
    if(!isChecked)
      sesJqueryObject('.adv_privacy_optn_edit > li').removeClass('active');
    if(sesJqueryObject(sesadv_list_select[i]).is(':checked')){
      isChecked = true;
      var el = sesJqueryObject(sesadv_list_select[i]).val();
      sesJqueryObject('.lists[data-rel="'+el+'"]').addClass('active');
      valueL = valueL+'member_list_'+el+',';
    }
   }
   if(isChecked){
     sesJqueryObject('#privacy_edit').val(valueL);
     sesJqueryObject('#adv_pri_option_edit').html(en.core.language.translate('Multiple Lists'));
     sesJqueryObject('.sesact_privacy_btn_edit').attr('title',en.core.language.translate('Multiple Lists'));;
    sesJqueryObject(this).find('.close').trigger('click');
   }
   sesJqueryObject('#sesadv_privacy_icon_edit').removeAttr('class').addClass('sesact_list');
});
sesJqueryObject(document).on('submit','#sesadv_network_edit_select',function(e){
  e.preventDefault();
  var isChecked = false;
   var sesadv_network_select = sesJqueryObject('#sesadv_network_edit_select').find('[type="checkbox"]');
   var valueL = '';
   for(i=0;i<sesadv_network_select.length;i++){
    if(!isChecked)
      sesJqueryObject('.adv_privacy_optn_edit > li').removeClass('active');
    if(sesJqueryObject(sesadv_network_select[i]).is(':checked')){
      isChecked = true;
      var el = sesJqueryObject(sesadv_network_select[i]).val();
      sesJqueryObject('.network[data-rel="'+el+'"]').addClass('active');
      valueL = valueL+'network_list_'+el+',';
    }
   }
   if(isChecked){
     sesJqueryObject('#privacy_edit').val(valueL);
     sesJqueryObject('#adv_pri_option_edit').html(en.core.language.translate('Multiple Network'));
     sesJqueryObject('.sesact_privacy_btn_edit').attr('title',en.core.language.translate('Multiple Network'));;
    sesJqueryObject(this).find('.close').trigger('click');
   }
   sesJqueryObject('#sesadv_privacy_icon_edit').removeAttr('class').addClass('sesact_network');
});
 
function tagLocationWorkEdit(){
    if(!sesJqueryObject('#tag_location_edit').val())
      return;
     sesJqueryObject('#locValuesEdit-element').html('<span class="tag">'+sesJqueryObject('#tag_location_edit').val()+' <a href="javascript:void(0);" class="loc_remove_act_edit">x</a></span>');
      sesJqueryObject('#dash_elem_act_edit').show();
      sesJqueryObject('#location_elem_act_edit').show();
      sesJqueryObject('#location_elem_act_edit').html('at <a href="javascript:;" class="seloc_clk_edit">'+sesJqueryObject('#tag_location_edit').val()+'</a>');
      sesJqueryObject('#tag_location_edit').hide();  
  }
  
    
  sesJqueryObject(document).on('click','.loc_remove_act_edit',function(e){
    sesJqueryObject('#activitylngEdit').val('');
    sesJqueryObject('#activitylatEdit').val('');
    sesJqueryObject('#tag_location_edit').val('');
    sesJqueryObject('#locValuesEdit-element').html('');
    sesJqueryObject('#tag_location_edit').show();
    sesJqueryObject('#location_elem_act_edit').hide();
    if(!sesJqueryObject('#toValuesEdit-element').children().length)
       sesJqueryObject('#dash_elem_act_edit').hide();
  })    
// Populate data
  var maxRecipientsEdit = 50;
  
 function getMentionDataEdit(that,dataBody){
    var data = sesJqueryObject('#edit_activity_body').val();
    var data_status = sesJqueryObject(that).attr('data-status');
    if(sesJqueryObject('#buysell-title-edit').length){
      if(!sesJqueryObject('#buysell-title-edit').val())
        return false;
      else if(!sesJqueryObject('#buysell-price-edit').val())
        return false;
    }else if(!data && data_status == 1 && !sesJqueryObject('#tag_location_edit').val())
      return false;
    
    data = sesJqueryObject(that).get(0).toQueryString()+'&bodyText='+dataBody;
    var url  = en4.core.baseUrl + 'sesadvancedactivity/index/edit-feed-post/userphotoalign/'+userphotoalign;
    sesJqueryObject(that).find('#compose-submit').attr('disabled',true);
    if(url.indexOf('&') <= 0)
      url = url+'?';
    url = url+'is_ajax=true';
    var that = that;
    sesJqueryObject(that).find('#compose-submit').html(savingtextActivityPost);
    //sesJqueryObject('#dots-animation-posting').show();
    //dotsAnimationWhenPostingInterval = setInterval (function() { dotsAnimationWhenPostingFn(sharingPostText)}, 600);
    sesadvancedactivityfeedactive2  = new Request.HTML({
        url : url,
        onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript){
          try{
            var parseJson = sesJqueryObject.parseJSON(responseHTML);
            if(parseJson.status){
              sesJqueryObject('#activity-item-'+parseJson.last_id).replaceWith(parseJson.feed);
              
              sesJqueryObject('#activity-item-'+parseJson.last_id).fadeOut("slow", function(){
                 sesJqueryObject('#activity-item-'+parseJson.last_id).replaceWith(parseJson.feed);
                 sesJqueryObject('#activity-item-'+parseJson.last_id).fadeIn("slow");
                 sesadvtooltip();
              });
              
              sessmoothboxclose();           
            }else{
               en4.core.showError("<p>" + en4.core.language.translate("An error occured. Please try again after some time.") + '</p><button onclick="Smoothbox.close()">Close</button>');
            }
          }catch(e){
            
          }
          sesJqueryObject(that).find('#compose-submit').html(savingtextActivityPostOriginal);
          sesJqueryObject(that).find('#compose-submit').removeAttr('disabled');
        },
        onError: function(){
          en4.core.showError("<p>" + en4.core.language.translate("An error occured. Please try again after some time.") + '</p><button onclick="Smoothbox.close()">Close</button>');
        },
      });
    sesadvancedactivityfeedactive2.send(data);
  }
  //submit form
  sesJqueryObject(document).on('submit','.edit-activity-form',function(e){
    e.preventDefault(); 
    var that = this;
    sesJqueryObject('textarea#edit_activity_body').mentionsInput('val', function(data) {
       getMentionDataEdit(that,data);
    });
  });
  sesJqueryObject(document).on('click','.composer_targetpost_edit_toggle',function(e){
     openTargetPostPopupEdit(); 
  });
  sesJqueryObject(document).on('focus','#edit_activity_body',function(){ 
if(!sesJqueryObject(this).attr('id'))
  sesJqueryObject(this).attr('id',new Date().getTime());
  
  isonCommentBox = true;
  var data = sesJqueryObject(this).val();
  if(!sesJqueryObject(this).val() || isOnEditField){
    if(!sesJqueryObject(this).val() )
      EditFieldValue = '';
    sesJqueryObject(this).mentionsInput({
        onDataRequest:function (mode, query, callback) {
         sesJqueryObject.getJSON('sesadvancedactivity/ajax/friends/query/'+query, function(responseData) {
          responseData = _.filter(responseData, function(item) { return item.name.toLowerCase().indexOf(query.toLowerCase()) > -1 });
          callback.call(this, responseData);
        });
      },
      //defaultValue: EditFieldValue,
      onCaret: true
    });
  }
  
  if(data){
     getDataMentionEdit(this,data);
  }
  
  if(!sesJqueryObject(this).parent().hasClass('typehead')){
    sesJqueryObject(this).hashtags();
    sesJqueryObject(this).focus();
  }
  autosize(sesJqueryObject(this));
});
sesJqueryObject(document).on('keyup','#edit_activity_body',function(){ 
    var data = sesJqueryObject(this).val();
     EditFieldValue = data;
});