/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: smActivity.js 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
var prev_tweetstatus_id = 0;
var prev_tweetstatus_screenname = 0;
(function() { // START NAMESPACE
  var $ = 'id' in document ? document.id : window.$;

  sm4.socialactivity = {
    options : {
      
     
    },
    
    activeTab: false,
    feedURL: '',
    feedType: '',
    
    doOnScrollLoadActivityLikes : function() {
      
      if( nextlikepage == 0){
        window.onscroll = '';
        return;
      }
      if($.type($('#feed_viewmore').get(0)) != 'undefined'){ 
        if( $.type( $('#like_viewmore').get(0).offsetParent ) != 'undefined' ) {
          var elementPostionY=$('#like_viewmore').get(0).offsetTop;
        }else{
          var elementPostionY=$$('#like_viewmore').get(0).y; 
        }
        if(elementPostionY <= $(window).scrollTop()+($(window).height() -40)){ 
          $('#like_viewmore').css('display', 'block'); 
          $('#like_viewmore').html('<i class="icon-spinner icon-spin ui-icon"></i>'); 
          getLikeUsers();
        }
      }
    },
    
    getLikeUsers : function() {
      
      $('#like_viewmore').css('display', 'block');
      if ($.type(sm4.core.subject) != 'undefined') {
        var subjecttype = sm4.core.subject.type;
        var subjectid = sm4.core.subject.id;
      }
      else {
        var subjecttype = '';
        var subjectid = '';
      }

      $.ajax({
        type: "POST", 
        dataType: "html", 
        url: sm4.core.baseUrl + 'advancedactivity/socialfeed/get-all-like-user',
        data: { 
          'format': 'html',
          'type': subjecttype,
          'id' : subjectid,
          'page' : '<?php echo ($this->page + 1); ?>'          
        },
        success:function( responseHTML, textStatus, xhr ) { 
          activeRequest = false;
          $('#like_viewmore').css('display', 'none');
          $(document).data('loaded', true);             
          $('#likemembers_ul').append(responseHTML);
          sm4.core.dloader.refreshPage();
          sm4.core.runonce.trigger();
        }
      });
    },
    
    getLikeFeedUsers : function (action_id, action, page) {  
      if (activeRequest == false) activeRequest = true; 
      else {
        $('#like_viewmore').css('display', 'none');
        return;
      } 
      if ($('#like-activity-item-' + action_id).html() == '')
        $('#like-activity-item-' + action_id).html("<div class='ps_loading sm-ui-popup-loading'></div>");
      
      $.ajax({
        type: "POST", 
        dataType: "html", 
        url: sm4.core.baseUrl + 'advancedactivity/socialfeed/get-fb-feed-likes',
        data: { 
          'format': 'html',
          'action_id': action_id, 
          'page' : page          
        },
        success:function( responseHTML, textStatus, xhr ) { 
          activeRequest = false;
          $('#like_viewmore').css('display', 'none');
          $(document).data('loaded', true);
         
          if (page == 1)
            $('#like-activity-item-' + action_id).html(sm4.core.mobiPageHTML(responseHTML));
          if (page > 1)
            $('#likemembers_ul').append(sm4.core.mobiPageHTML(responseHTML));
          
          //   sm4.core.dloader.refreshPage();
          sm4.core.runonce.trigger();
        }
      });
      
    },
    
    comment_likes : function(action_id, comment_id, page) { 
      if (oldCommentLikeID != comment_id)
        $('#like-comment-item-' + action_id).html("<div class='ps_loading sm-ui-popup-loading'></div>");
      else {
        return;
      }
      oldCommentLikeID = comment_id;
      $.ajax({
        type: "POST", 
        dataType: "html", 
        url: sm4.core.baseUrl + 'advancedactivity/socialfeed/get-fb-feed-likes',
        data: {
          'action_id': action_id,
          'comment_id' : comment_id,
          'page' : page,
          'format':'html'
        },
        success:function( responseHTML, textStatus, xhr ) {
         
          $('#like_comment_viewmore_link').css('display', 'none');
          $(document).data('loaded', true);
          if (page == 1)
            $('#like-comment-item-' + action_id).html(sm4.core.mobiPageHTML(responseHTML));
          if (page > 1)
            $('#likecommentmembers_ul').append(sm4.core.mobiPageHTML(responseHTML));
          sm4.core.dloader.refreshPage();
          sm4.core.runonce.trigger();
          $('#like-comment-item-' + action_id).css('display', 'block');
        }
      });
    },
    
    socialFeedLogin : function(loginURL, feedURL, feedType) {
      this.activeTab = true;
      this.feedURL = feedURL;
      this.feedType = feedType;
      var child_window = window.open (loginURL ,'mywindow','width=800,height=700');
      
    },
    
    getTabBaseContentFeed : function() { 
                $.mobile.loading().loader("show");
      this.activeTab = false;
      var feedType = this.feedType;
      $.ajax({
        type: "POST", 
        dataType: "html", 
        url: this.feedURL,
        data: {
          'format' : 'html',
          'is_ajax' : '0',
          'tabaction' : true          
		
        },
        success:function( responseHTML, textStatus, xhr ) { 
                    $.mobile.loading().loader("hide");          
          $('#showadvfeed-' + feedType).html(responseHTML);
          sm4.core.dloader.refreshPage();
          sm4.core.runonce.trigger();         
        }
      }); 
      
    },

    setUpdateData : function(data, feedtype) {
			
			if (feedtype == 'fbfeed') {
				$('#fbmin_id').val(data);
			}
			else if (feedtype == 'linkedinfeed') {
				$('#linkedinmin_id').val(data);
			}
			
		}
    
      
  }
  
  sm4.socialactivity.twitter = {
    
    favourite_Tweet : function (tweet_id, action) { 
      
      if (action == 1) {
        var fav_unfav = 'Favorited';
        var icon_tweetfav = 'fastar';
        var actiontemp = 0;
      }
      else { 
        var fav_unfav = 'Favorite';
        var icon_tweetfav = 'star-empty';
        var actiontemp = 1;        
      }
      
        $.mobile.activePage.find('#main-feed-'+tweet_id + ' .feed_item_option .ui-block-a a .ui-btn-text').html('<i class="ui-icon ui-icon-' + icon_tweetfav + '"></i> <span>'+sm4.core.language.translate(fav_unfav)+'</span>'); 

       $.mobile.activePage.find('#main-feed-'+tweet_id + ' .feed_item_option .ui-block-a a').attr('onclick', "sm4.socialactivity.twitter.favourite_Tweet(\""+ tweet_id + '",' + actiontemp + ")");

      sm4.core.dloader.refreshPage();
      
      $.ajax({
        type: "POST", 
        dataType: "json", 
        url: sm4.core.baseUrl + 'widget/index/mod/advancedactivity/name/advancedactivitytwitter-userfeed',
        data: {
          'format' : 'json',
          'is_ajax' : '5',
          'tweetstatus_id' : tweet_id,
          'favorite_action': action
		
        },
        success:function( responseJSON, textStatus, xhr ) {

        }
      });   
    },
    
    reTweet : function (tweetstatus_id) {   
      
       $.mobile.activePage.find('#main-feed-'+tweetstatus_id + ' .feed_item_option .ui-block-b a .ui-btn-text').html('<i class="ui-icon ui-icon-retweet"></i> <span>'+sm4.core.language.translate('Retweeted')+'</span>'); 
       $.mobile.activePage.find('#main-feed-'+tweetstatus_id + ' .feed_item_option .ui-block-b a').removeAttr('onclick');

      sm4.core.dloader.refreshPage();
      $.ajax({
        type: "POST", 
        dataType: "json", 
        url: sm4.core.baseUrl + 'widget/index/mod/advancedactivity/name/advancedactivitytwitter-userfeed', 
        data: {
          'format' : 'json',
          'is_ajax' : '3',
          'tweetstatus_id' : tweetstatus_id
		
        },
        success:function( responseJSON, textStatus, xhr ) {

        }
      });    
    },
    
    
    post_status : function (self) {
    
               $.mobile.loading().loader("show");
     //self.prev().html('<div><img src="application/modules/Core/externals/images/loading.gif" /></div>');    
      $.ajax({
        type: "GET", 
        dataType: "json", 
        url: sm4.core.baseUrl + 'widget/index/mod/advancedactivity/name/advancedactivitytwitter-userfeed', 
        data: {
          'format' : 'json',
          'is_ajax' : '2',
          'post_status': self.prev().prev().val(),
          'tweetstatus_id' : self.next().val()
		
        },
        success:function( responseJSON, textStatus, xhr ) {  
                    $.mobile.loading().loader("hide");
				  $.mobile.loading().loader('show', 'a', 'Your Tweet to @' + $('#screen_name').val() + 'has been sent!', true);
          //$('#feedshare').html('Your Tweet to @' + $('#screen_name').val() + 'has been sent!' );
          $(this).delay(800).queue(function(){            
            $('.ui-page-active').removeClass('pop_back_max_height');
            $('.ui-page-active').removeClass('dnone');
            $('#feedsharepopup').remove();
                      $.mobile.loading().loader("hide");
            $(window).scrollTop(parentScrollTop)
            $(this).clearQueue();
          });          
        }
      });
    },
    
    limitText : function(limitField, limitNum) { 
      
      if (limitField.val().length <= limitNum) { 
        limitField.next().find('#show_loading').html (limitNum - limitField.val().length);
      }
      if (limitField.val().length > limitNum) {
        limitField.val(limitField.val().substring(0, limitNum));
      } else {
        //$('#show_loading').html (limitNum - limitField.val().length);
      }
    }  
  }
  
  sm4.socialactivity.linkedin = {
    
		current_timestamp : '',
    like : function(action_id, comment_id) {   
      if ($.type(comment_id) == 'undefined') {
        this.like_unlikeFeed('like', action_id, comment_id);
      }else {
        this.like_unlikeComment('like', action_id, comment_id);

      }
 
      $.ajax({
        type: "POST", 
        dataType: "json", 
        url: sm4.core.baseUrl + 'advancedactivity/index/like',
        data: {
          'action_id': action_id, 
          'comment_id' :comment_id, 
          'subject' : $.mobile.activePage.advfeed_array.subject_guid,
          'format':'json'
        },
        success:function( responseJSON, textStatus, xhr ) {        
          sm4.core.dloader.refreshPage();
          sm4.core.runonce.trigger();
        }.bind(this),
   
        error: function( xhr, textStatus, errorThrown ) { 
          if ($.type(comment_id) == 'undefined') {
            this.like_unlikeFeed('unlike', action_id, comment_id);
          }
          else {
            this.like_unlikeComment('unlike', action_id, comment_id);

          }
        },
        statusCode:{
          404:function (response) { 
            if ($.type(comment_id) == 'undefined') {
              this.like_unlikeFeed('unlike', action_id, comment_id);
            }
            else {
              this.like_unlikeComment('unlike', action_id, comment_id);

            }
          }
        }
      });
    },

    unlike : function(action_id, comment_id) {        
      //MAKE LIKE CHANGE TO UNLIKE FIRST AND THEN SEND AJAX REQUEST:
      if ($.type(comment_id) == 'undefined') {
        this.like_unlikeFeed('unlike', action_id, comment_id);
      }
      else {
        this.like_unlikeComment('unlike', action_id, comment_id);
        
      }
     
      $.ajax({
        type: "POST", 
        dataType: "json",
        url: sm4.core.baseUrl + 'advancedactivity/index/unlike',
        data: {
          'action_id': action_id, 
          'comment_id' :comment_id, 
          'subject' : $.mobile.activePage.advfeed_array.subject_guid,
          'format':'json'
        },
        success:function( responseJSON, textStatus, xhr ) {          
          sm4.core.dloader.refreshPage();
          sm4.core.runonce.trigger();
        }.bind(this),
        
        error: function( xhr, textStatus, errorThrown ) {
          if ($.type(comment_id) == 'undefined') {
            this.like_unlikeFeed('like', action_id, comment_id);
          }
          else {
            this.like_unlikeComment('like', action_id, comment_id);

          }
        },
        statusCode:{ 
          404:function (response) { 
            if ($.type(comment_id) == 'undefined') {
              this.like_unlikeFeed('like', action_id, comment_id);
            }
            else {
              this.like_unlikeComment('like', action_id, comment_id);

            }
          }
        }
        
      });
    },
    
    sendMessage : function(self) {
      
      if ($('#linkedin_compose').find('#body').val() == '' || $('#linkedin_compose').find('#title').val() == '') {
        alert('Please fill all the fields.');
        return;
      }
                $.mobile.loading().loader("show");
      params = $('#linkedin_compose').serialize() + '&is_ajax=2&format=json';
     
      $.ajax({
        type: "POST", 
        dataType: "json",
        url: sm4.core.baseUrl + 'widget/index/mod/advancedactivity/name/advancedactivitylinkedin-userfeed',
        data: params,
        success:function(responseJSON, textStatus, xhr ) { 
           if (responseJSON && responseJSON.response.success == true) { 
                        $.mobile.loading().loader("hide");
				     $.mobile.loading().loader('show','a', 'Your message was successfully sent.', true);
              $(this).delay(700).queue(function(){ 
                          $.mobile.loading().loader("hide");
                $(".ui-page-active").removeClass("pop_back_max_height");
                $(".ui-page-active").removeClass("dnone");
                $("#feedsharepopup").remove();
                $(window).scrollTop(parentScrollTop)  
                $(this).clearQueue();  
              });
              
           }
           else {
             thisobj.getParent('.form-elements').innerHTML = en4.core.language.translate('An error occured. Please try again after some time.');
           }
        }.bind(this)       
      });
    },
    attachComment : function(formElement, post_id, likecount, container_id, timestamp){ 
      this.timestamp = timestamp;
			var bind = this;      
      formElement.attr('data-ajax', 'false');
      formElement.css('display', 'block');
      bind.comment(post_id, $("[name='body']", formElement).val(), likecount, container_id);
      $("[name='body']", formElement).val('');
      $("[name='body']", formElement).attr('placeholder', sm4.core.language.translate('Write a comment...'));
      
    },
    comment : function(post_id, body, likecount, container_id) {
			
		
                $.mobile.loading().loader("show");
      params = {'format' : 'json',
								'is_ajax' : '4',
								'post_id' : post_id,
								'Linkedin_action': 'post',
								'content': body,
                'like_count' : likecount
				
			}
    
      $.ajax({
        type: "POST", 
        dataType: "json",
        url: sm4.core.baseUrl + 'widget/index/mod/advancedactivity/name/advancedactivitylinkedin-userfeed',
        data: params,
        success:function(responseJSON, textStatus, xhr ) { 
           var li = $('<li />', {            
            //'id' : 'comment-' + responseJSON.comment_id,
            'html': sm4.core.mobiPageHTML(responseJSON.body)                

          }).inject($('#showhide-comments-'+container_id).find('ul'));
          if ($('#showhide-comments-'+container_id).find('ul').find('li div.no-comments')) {
            $('#showhide-comments-'+container_id).find('ul').find('li div.no-comments').parent('li').remove();
          }
          $('#hide-commentform-'+container_id).css('display', 'none');
          $('#hide-commentform-'+container_id).next().css('display', 'block');
          $('#activity-comment-body-' + container_id).val('');
          sm4.core.runonce.trigger();
          sm4.core.dloader.refreshPage();
          $('.sm-ui-popup-container').animate({
            scrollTop: 2000
          }, 0); 
                    $.mobile.loading().loader("hide");
        }.bind(this)       
      });
			
		},
    getLikeUsers : function (action_id, action, page) {  
      if (activeRequest == false) activeRequest = true; 
      else {
        $('#like_viewmore').css('display', 'none');
        return;
      } 
      if ($('#like-activity-item-' + action_id).html() == '')
        $('#like-activity-item-' + action_id).html("<div class='ps_loading sm-ui-popup-loading'></div>");
      
      $.ajax({
        type: "POST", 
        dataType: "html", 
        url: sm4.core.baseUrl + 'advancedactivity/socialfeed/get-all-like-user',
        data: { 
          'format': 'html',
          'post_id': action_id, 
          'page' : page          
        },
        success:function( responseHTML, textStatus, xhr ) { 
          activeRequest = false;
          $('#like_viewmore').css('display', 'none');
          $(document).data('loaded', true);
         
          if (page == 1)
            $('#like-activity-item-' + action_id).html(sm4.core.mobiPageHTML(responseHTML));
          if (page > 1)
            $('#likemembers_ul').append(sm4.core.mobiPageHTML(responseHTML));
          
          //   sm4.core.dloader.refreshPage();
          sm4.core.runonce.trigger();
        }
      });
      
    }
	}
})();


sm4.core.runonce.add(function() { 
  
  //socialpageid = $.mobile.activePage.attr('id');
  //if (socialpageid == 'advancedactivity-index-socialfeed')
  if (typeof $.mobile.activePage.advfeed_array == 'undefined')
    $.mobile.activePage.advfeed_array = {};
  
});