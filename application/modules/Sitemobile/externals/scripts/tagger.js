/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: tagger.js 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
var Tagger={
};

Tagger.tagger = {
  
  // Local options
  options:{
    'existingTags' : [],
    'tagListElement' : false,
    'guid' : false,
    'enableCreate' : false,
    'enableDelete' : false
  },
  initialize : function(options) {
    this.options = $.merge(this.options,options);
  },
  //Onclick of Save button ,Save tags & display new tag list.
  saveTag : function(url,subject_guid,canRemove, user_guid) {
		$.mobile.activePage.find('#tagit_'+subject_guid).show(); 
//    $.mobile.showPageLoadingMsg();
    $.mobile.loading().loader("show");
    //Check for blank tag,it cannot be saved.
    if(($.mobile.activePage.find('.tag').length) || ($.mobile.activePage.find('#tags_'+subject_guid).val().length)){ 
      
      var toValues = $.mobile.activePage.find('#toValues_'+subject_guid).val();

      if($.mobile.activePage.find('#tags_'+subject_guid).val()!='') {
        var text = $.mobile.activePage.find('#tags_'+subject_guid).val();
      }
      var self = this;

      $.ajax({
        url:url, //Request send to url on same  page
        type:'POST',       //Method of request send is post
        data:{
          'subject' : subject_guid,       
          'format' : 'json',
          'guid': toValues,
          'text' : text
        },
        success:function()  //On the success of ajax request
        { 
            $.mobile.loading().loader("hide");
//					$.mobile.hidePageLoadingMsg();
          self.getTagList(subject_guid, canRemove, user_guid);    
          self.cancelTag(subject_guid);
					$.mobile.activePage.find('#tagit_'+subject_guid).hide();
        } 
      });
       
    }
  },

  //Onclick of Add Tag link ,Displays tag box and hide other content.
  addTag : function(subject_guid){

    //On click of add tag show tag box. 
    $.mobile.activePage.find('#tagit_'+subject_guid).show(); 

    //To hide all content on the page (photo , comment ,menu navigation ,next-prev navigation)
    $.mobile.activePage.find('.sm-ui-photo-view-nav, .sm-ui-photo-view, .ui-navbar, .comments, .sm-ui-photo-view-info, .albums_viewmedia_info_actions').hide();
    //hide taglist if tag exist
    if($.mobile.activePage.find('.tag_span').length){            
      $.mobile.activePage.find('.sm-ui-photo-view-info').hide();
    }

    $.mobile.activePage.find('#tags_'+subject_guid).focus();   
  },
  
  //Onclick of cancel button displays the whole content & hide tag box.
  cancelTag : function(subject_guid){
    
    $.mobile.activePage.find('#tagit_'+subject_guid).hide();
    //To show content of page (photo , comment ,menu navigation ,next-prev navigation)
    $.mobile.activePage.find('.sm-ui-photo-view-nav, .sm-ui-photo-view, .ui-navbar, .comments, .sm-ui-photo-view-info, .albums_viewmedia_info_actions').show();  
    //show taglist if tag exists
    if($.mobile.activePage.find('.tag_span').length){            
      $.mobile.activePage.find('.sm-ui-photo-view-info-tags').show();
    }
    
    $.mobile.activePage.find('#toValues_'+subject_guid).val('');
    $.mobile.activePage.find('#tags_'+subject_guid).val('');
    $.mobile.activePage.find('#toValues-wrapper_'+subject_guid).find('.tag').remove();    
  },
  
  //Get complete list of tags and display it.
  getTagList : function(subject_guid,canRemove, user_guid) { 
    var self=this;
		var comma = '';
    $.ajax({
      url:sm4.core.baseUrl+ 'core/tag/get-tags', //Request send to url on same  page
      type:'POST',   
      dataType: 'json',
      data:{
        'subject' : subject_guid,       
        'format' : 'json'
      },
      success:function(result)  //On the success of ajax request
      { 
        $.mobile.activePage.find('#media_tags_'+subject_guid).css('display','none');
        $.mobile.activePage.find('#media_tags_'+subject_guid).find('span').remove();
        $.each(result.tags, function(i, item) { 
       
          //Add span for each tag.
          $.mobile.activePage.find('#media_tags_'+subject_guid).append("<span id='tag_info_"+item.id+"' class='tag_span'></span>");
           if(i > 0) {
						comma = ',  ';
					}      
          //Append tag. 
          $.mobile.activePage.find('#tag_info_'+item.id).append(comma+"<a href= "+item.href+" class='ui-link'>"+item.text+"</a>");

          //Delete tag 
          if(canRemove || self.checkCanRemove(item,subject_guid, user_guid) )
          {  
            bracket = $('#tag_info_'+item.id).append(' (');         
            //Append (X) delete mark
            var remove=  $("<a>").attr({
              href: "javascript:",
              title: sm4.core.language.translate('delete'),
              id:'tag_destroyer_' + item.id,
              'class' : 'tag_destroyer albums_tag_delete ui-link'
            }).text("X").appendTo(bracket);
              remove.on("click", function(e){
              self.removeTag(item, subject_guid);
            });
            
            $.mobile.activePage.find('#tag_info_'+item.id).append(')');       
          } 

          $.mobile.activePage.find('#media_tags_'+subject_guid).css('display','block');
        });
       
      } 
    } );
  },
 
  //Onclick of  (X) delete the tag. 
  removeTag : function(item,subject_guid) {
    var self = this;

    // Remove from frontend
    $.mobile.activePage.find('#tag_info_' +item.id).remove();

    $.ajax({
      url:sm4.core.baseUrl+ 'core/tag/remove', //Request send to url on same  page
      type:'POST',   
      dataType: 'json',
      data:{
        'subject' : subject_guid,
        'tagmap_id':item.id,
        'format' : 'json'
      },
      success:function()  //On the success of ajax request
      { 
        //check if it is last tag then hide Tagged text
        if(!($.mobile.activePage.find('.tag_span').length)){ 
          $.mobile.activePage.find('#media_tags_'+subject_guid).css('display','none');
        }
      } 
    } );
  },

  //Check for tag can be removed or not. 
  checkCanRemove : function(tagData, subject_guid, user_guid) { 
    if( tagData && user_guid && subject_guid) { 
      if( tagData.tag_type + '_' + tagData.tag_id == user_guid) return true;
      if( tagData.tagger_type + '_' + tagData.tagger_id == user_guid ) return true;
    }
    return false;
  }

}

