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

var feedElement,
        activeRequest = false,
        activeRequestDislike = false,
        proceed_request = false,
        proceed_request_temp = false,
        currentactive_panel = 'undefined',
        photoUpload = false,
        musicUpload = false,
        oldCommentLikeID = 0,
        oldCommentDisLikeID=0,
        oldReplyLikeID = 0,
        oldReplyDisLikeID = 0,
        parentScrollTop = 0,
        deleteCommentActive = 0,
        deleteReplyActive = 0,
        enabledModuleForMobile =0,
        showAsLike=1,
        showLikeWithoutIcon=0,
        showLikeWithoutIconInReplies=0,
        currentpageid = '';
        showDislikeUsers =  0,
                enabledCommentClass = 0,
(function() { // START NAMESPACE
  var $ = 'id' in document ? document.id : window.$;

  $(document).on('afterSMCoreInit', function(event, data) {
    sm4.activity.feedTabURL = sm4.core.baseUrl + sm4.activity.feedTabURL;
    sm4.activity.feedURL = sm4.core.baseUrl + sm4.activity.feedURL;
  });
  sm4.activity = {
    preloadedCommentString: '',
    preloadedReplyString: '',
    preloadedCommentArray: [],
    preloadedReplyArray: [],
    options: {
      allowEmptyWithoutAttachment: false,
      allowEmptyWithAttachment: true,
      hideSubmitOnBlur: true,
      submitElement: true,
      useContentEditable: true
    },
    advfeed_array: {
        'subject_guid' : false
    },
    feedTabURL: 'widget/index/name/sitemobile.sitemobile-advfeed',
    feedURL: 'advancedactivity/index/post',
    feedType: 'sitefeed',
    initialize: function(element, submitAjax) {
      this.resetAdvFeed();
      this.elements = {},
              this.elements.textarea = element;

      var $this = this;
      if (submitAjax) {
        $this.getForm().off('submit').on('submit', function(e) {
          if (photoUpload == true) {
            photoUpload = false;
            return;
          }
          if (musicUpload == true) {
            musicUpload = false;
            return;
          }
          e.preventDefault();
          $(this).trigger('editorSubmit');
          if (!$this.options.allowEmptyWithAttachment && $this.getContent() == '') {
            e.preventDefault();
            return;
          } else {
            if (!$this.options.allowEmptyWithoutAttachment && $this.getContent() == '') {
              e.preventDefault();
              return;
            } else if(!sm4.activity.options.submitElement) { 
              sm4.activity.options.submitElement = true;
              e.preventDefault();
              return;
            }
          }

          $this.share();
          return false;

        });
      }

    },
    getActivePageID: function() {

      if ($.type(this.advfeed_array[$.mobile.activePage.attr('id')]) != 'undefined')
        var feedtype = this.advfeed_array[$.mobile.activePage.attr('id')];
      else
        var feedtype = 'sitefeed';
      var currentpageid = $.mobile.activePage.attr('id') + '_' + feedtype;
      if ($.type($.mobile.activePage.find('#subject').get(0)) != 'undefined') {
        currentpageid = currentpageid + '_' + $.mobile.activePage.find('#subject').val();
      }

      return currentpageid;


    },
    getForm: function() {
      if ($.type(this.elements) != 'undefined')
        return this.elements.textarea.parents('form');
    },
    getContent: function() {
      return this.cleanup(this.elements.textarea.val());
    },
    setContent: function(content) {
      this.elements.textarea.val(content);
    },
    cleanup: function(html) {
      // @todo
      return html
              .replace(/<(br|p|div)[^<>]*?>/ig, "\r\n")
              .replace(/<[^<>]+?>/ig, ' ')
              .replace(/(\r\n?|\n){3,}/ig, "\n\n")
              .trim();
    },
    toggleFeedArea: function(self, feedpost, type) {


      //      if(!document.location.search.length) {
      //        window.location.hash='#&ui-state=dialog';
      //      }
      var _self = $.mobile.activePage.find(self);
      statusHtml = _self.next().html();
      _self.next().find('script').remove();

      if (feedpost) {
        $.mobile.activePage.attr('data-paddingTop', $.mobile.activePage.css('paddingTop'));
        $.mobile.activePage.attr('data-minHeight', $.mobile.activePage.css('minHeight'));
        if ($.type($('#activitypost-container-temp').get(0)) != 'undefined') {
          $('#activitypost-container-temp').remove();
        }
        //CREATE DIV ELEMENT....
        var temp = $('<div />', {
          'id': 'activitypost-container-temp',
          'class': 'activity-post-container ui-body-c',
          'html': statusHtml
        });
        if (_self.next().html() != '')
          statusHtml = _self.next().html();
        $('body').prepend(temp);
        if (!type)
          $('#activitypost-container-temp').find('textarea').focus();
        if (sm4.core.isApp()) {
          $('#activitypost-container-temp').find('textarea').autoGrow();
        }
        if($.mobile.activePage.advfeed_array.enableHashTagComposer) {
          var highlighter = $('<div />', {
            'class': 'activity-post-highlighter-wapper',
            'html': '<div class="activity-post-highlighter" id="highlighter" />'
          });
          $('#activitypost-container-temp').find('textarea').before( highlighter );
          $('#activitypost-container-temp').find('textarea').css('position', 'relative');
          $('#activitypost-container-temp').find('textarea').on('keydown, keyup', function() {
            var hashRegExp = /\B#\w*[a-zA-Z]+\w*/g;
            var content = $(this).val().replace(/\<span\>\<\/span\>/ig, '');
            var matches = content.match(hashRegExp);
            var newcontent = '';
            if (matches) {
              var hashSplit = content.split(hashRegExp);

              var i = 0;
              for (i; i < matches.length; i++) {
                var subset = hashSplit[i] || '';
                if (subset === matches[i]) {
                  newcontent += subset;
                  continue;
                }
                newcontent += subset + '<span class="hashtag">'+ matches[i]+ '</span>';
              }

              for (i; i < hashSplit.length; i++) {
                newcontent += hashSplit[i] || '';
              }
            } else {
               newcontent = $(this).val();
            }
            $('#highlighter').html(newcontent);
          });
        }
        $("#activitypost-container-temp").unbind('click').bind('click', function(event) {
          hideEmotionIconClickEvent();
          hidePrivacyIconClickEvent();
        });

        //CHECK IF NO SOCIAL SERVICES IS COMING.
        if (typeof fb_loginURL != 'undefined' || typeof twitter_loginURL != 'undefined' || typeof linkedin_loginURL != 'undefined') {
          $('#activitypost-container-temp').find('#socialshare-button').addClass('dblock').removeClass('dnone');
        }
        currentactive_panel = _self.parents('.ui-responsive-panel');

        _self.parents('.ui-responsive-panel').addClass('dnone');
        if (sm4.activity.statusbox.privacy != false) {

          sm4.activity.statusbox.addPrivacy(sm4.activity.statusbox.privacy);
        }
        sm4.core.runonce.trigger();

        //SPECIAL CASE 1, 2;

        if ($.type($('#activitypost-container-temp').find('#ui-header').children('div').get(0)) != 'undefined') {
          $('#activitypost-container-temp').find('#ui-header').children('div').html($('#activitypost-container-temp').find('#ui-header').children('div').children('div').html())
        }

        if ($.type($('#activitypost-container-temp').find('#ui-header-addpeople').children('div').get(0)) != 'undefined') {
          $('#activitypost-container-temp').find('#ui-header-addpeople').children('div').html($('#activitypost-container-temp').find('#ui-header-addpeople').children('div').children('div').html())
        }

        sm4.activity.initialize($('#activitypost-container-temp').find('#activity_body'), true);
        sm4.socialService.initialize();
        this.resetAdvFeed();
        if (type == 'addphoto') {
          $('#attachment-options').css('display', 'block');
          $('#smactivityoptions-popup').css('display', 'none');
          sm4.activity.composer.showPluginForm('', 'photo');
        }
        if (type == 'checkin') {
          sm4.activity.composer.showPluginForm('', 'checkin');
        }
      }
      else {
        if (type == 'status') {
          currentactive_panel.removeClass('dnone');
          statusHtml = '';
          this.resetAdvFeed();
          $('#activitypost-container-temp').remove();
          sm4.activity.options.allowEmptyWithoutAttachment = false;
           $.mobile.activePage.css('paddingTop', $.mobile.activePage.attr('data-paddingTop'));
          $.mobile.activePage.css('minHeight', $.mobile.activePage.attr('data-minHeight'));
        }
        else if (type == 'checkin') {
          $('#ui-header').css('display', 'block');
          $('#ui-header-checkin').css('display', 'none');
          var addLinkBefore = $('#sitetagchecking_mob');
          $('.sm-post-wrap').css('display', 'block');
          addLinkBefore.next().css('display', 'none');
          if (sm4.activity.composer.checkin.location == '') {
            $('.cm-icon-map-marker').removeClass('active');
          }
          sm4.activity.composer.checkin.aboartReq = true;
        }
        else if (type == 'addpeople') {
          $('#ui-header').css('display', 'block');
          $('#ui-header-addpeople').css('display', 'none');
          var addLinkBefore = $('#adv_post_container_tagging');
          $('.sm-post-wrap').css('display', 'block');
          addLinkBefore.prev().css('display', 'block');
          addLinkBefore.css('display', 'none');
          addLinkBefore.nextAll().css('display', 'none');
          if ($('#toValues').val() != '') {
            sm4.activity.options.allowEmptyWithoutAttachment = true;
          } else {
            sm4.activity.options.allowEmptyWithoutAttachment = false;
            $('.cm-icon-user').removeClass('active');
          }
        }
      }

    },
    toggleFeedArea_Dialoge: function(self, feedpost, type) {
      var _self = $.mobile.activePage.find(self);
      if (statusHtml == '') {
        $.mobile.activePage.statusHtml = statusHtml = _self.next().html();
        _self.next().find('script').remove();
      }

      if (feedpost) {
        if ($.type($('#activitypost-container-temp').get(0)) != 'undefined') {
          // //$('#activitypost-container-temp').remove();
        }
        //CREATE DIV ELEMENT....
        var temp = $('<div />', {
          'id': 'activitypost-container-temp',
          'class': 'activity-post-container ui-body-c',
          'html': statusHtml
        });
        if (_self.next().html() != '')
          statusHtml = _self.next().html();
        //_self.next().html('');
        $('body').prepend(temp);
        $('#activitypost-container-temp').find('textarea').focus();
        if (sm4.core.isApp()) {
          $('#activitypost-container-temp').find('textarea').autoGrow();
        }
        currentactive_panel = _self.parents('.ui-responsive-panel');
        _self.parents('.ui-responsive-panel').addClass('dnone');

        if (sm4.activity.statusbox.privacy != false) {

          sm4.activity.statusbox.addPrivacy(sm4.activity.statusbox.privacy);
        }
        sm4.core.runonce.trigger();

        //SPECIAL CASE 1, 2;

        if ($.type($('#activitypost-container-temp').find('#ui-header').children('div').get(0)) != 'undefined') {
          $('#activitypost-container-temp').find('#ui-header').children('div').html($('#activitypost-container-temp').find('#ui-header').children('div').children('div').html())
        }

        if ($.type($('#activitypost-container-temp').find('#ui-header-addpeople').children('div').get(0)) != 'undefined') {
          $('#activitypost-container-temp').find('#ui-header-addpeople').children('div').html($('#activitypost-container-temp').find('#ui-header-addpeople').children('div').children('div').html())
        }

        sm4.activity.initialize($('#activitypost-container-temp').find('#activity_body'), false);
        sm4.socialService.initialize();
        this.resetAdvFeed();
      }
      else {
        if (type == 'status') {
          currentactive_panel.removeClass('dnone');
          statusHtml = '';
          this.resetAdvFeed();
          //$('#activitypost-container-temp').remove();
          sm4.activity.options.allowEmptyWithoutAttachment = false;
        }
        else if (type == 'checkin') {
          $('#ui-header').css('display', 'block');
          $('#ui-header-checkin').css('display', 'none');
          var addLinkBefore = $('#sitetagchecking_mob');
          $('.sm-post-wrap').css('display', 'block');
          addLinkBefore.next().css('display', 'none');
          if (sm4.activity.composer.checkin.location == '') {
            $('.cm-icon-map-marker').removeClass('active');
          }
        }
        else if (type == 'addpeople') {
          $('#ui-header').css('display', 'block');
          $('#ui-header-addpeople').css('display', 'none');
          var addLinkBefore = $('#adv_post_container_tagging');
          $('.sm-post-wrap').css('display', 'block');
          addLinkBefore.prev().css('display', 'block');
          addLinkBefore.css('display', 'none');
          addLinkBefore.nextAll().css('display', 'none');
          if ($('#toValues').val() != '')
            sm4.activity.options.allowEmptyWithoutAttachment = true;
          else {
            sm4.activity.options.allowEmptyWithoutAttachment = false;
            $('.cm-icon-user').removeClass('active');
          }
        }
      }

    },
    toggleCommentArea: function(self, action_id) {

      $(self).css('display', 'none');
      $(self).prev().css('display', 'block');
      var form = $(self).prev().find('form');
      form.css('display', 'block');
      form.find('textarea').attr('placeholder', sm4.core.language.translate('Write a comment...'))
      form.find('textarea').focus();

    },
    toggleCommentPostArea: function() {
       var addLinkBefore = $('#adv_comment_post_container_tagging', sm4.activity.composer.content);
      //addLinkBefore.prevAll().css('display', 'block');
      $('#composer-nested-comment-options').css('display', 'block');
      $('#comment-emoticons-button').css('display', 'block');
      $('#comment-add-people').css('display', 'block');
      addLinkBefore.css('display', 'none');
      //addLinkBefore.next().css('display', 'none');
      //$('#ui-header', sm4.activity.composer.content).css('display', 'block');
      $('#ui-post-comment-header-addpeople', sm4.activity.composer.content).css('display', 'none'); 
    },
     toggleEditPostArea: function() {
       var addLinkBefore = $('#adv_edit_post_container_tagging', sm4.activity.composer.content);
      //addLinkBefore.prevAll().css('display', 'block');
      $('#composer-nested-comment-options').css('display', 'block');
      $('#comment-emoticons-button').css('display', 'block');
      $('#comment-add-people').css('display', 'block');
      addLinkBefore.css('display', 'none');
      //addLinkBefore.next().css('display', 'none');
      //$('#ui-header', sm4.activity.composer.content).css('display', 'block');
      $('#ui-post-edit-header-addpeople', sm4.activity.composer.content).css('display', 'none'); 
    },
    toggleReplyPostArea: function(id) {
       var addLinkBefore = $('#adv_reply_post_container_tagging_'+id, sm4.activity.composer.content);
      $('#composer-nested-reply-options_'+id).css('display', 'block');
      $('#reply-emoticons-button_'+id).css('display', 'block');
      $('#reply-add-people_'+id).css('display', 'block');
      addLinkBefore.css('display', 'none');
      $('#ui-post-reply-header-addpeople_'+id, sm4.activity.composer.content).css('display', 'none'); 
    },
    activityremove: function(e, comment_id, action_id) {
      deleteCommentActive = true;
      if ($.type(e) != 'undefined' && $.type($(e) == 'object')) {
        feedElement = $(e);
        $.mobile.activePage.removeClass('dnone');
        $('.ui-popup-active').removeClass('dnone');
        var commentinfo = feedElement.data('message').split('-');
        if (commentinfo[0] == 0) {
          $.mobile.activePage.find('#popupDialog').popup("open");
          $.mobile.activePage.find('#popupDialog').parent().css('z-index', '11000')
          $.mobile.activePage.find('#popupDialog').popup("open");
        }
        else {
          $.mobile.activePage.find('#popupDialog-Comment').parent().css('z-index', '11000');
          $.mobile.activePage.find('#popupDialog-Comment').popup({history: false});
          $.mobile.activePage.find('#popupDialog-Comment').popup("open");

        }

      }
      else { 
        
        var commentinfo = feedElement.data('message').split('-');

        if (commentinfo[0] == 0) {
          $('#activity-item-' + commentinfo[1]).remove();
        } else {
          $('#comment-' + commentinfo[0]).remove();
          try {
            var commentCount = $('#count-feedcomments')[0].innerHTML;
            var m = commentCount.match(/\d+/);
            var newCount = (parseInt(m[0]) != 'NaN' && parseInt(m[0]) > 1 ? parseInt(m[0]) - 1 : 0);

            commentCount = commentCount.replace(m[0], newCount);
            $('#count-feedcomments')[0].innerHTML = commentCount;

            //DECREASE THE COMMENTS COUNT FROM THE MAIN WINDOW IF USER DELETE COMMENT FROM POPUP WINDOW. 

            if (parseInt(newCount) > parseInt(0)) {

              $.mobile.activePage.find('#activity-item-' + commentinfo[1]).find('.feed_comments span').html(commentCount)
            }
            else {
              $.mobile.activePage.find('#activity-item-' + commentinfo[1]).find('.feed_comments').prev('span').remove();
              $.mobile.activePage.find('#activity-item-' + commentinfo[1]).find('.feed_comments').remove();
            }



          } catch (e) {
          }
        }
        $.post(feedElement.data('url'));
      }
    },
    changeLikeDislikeColor :function() {
      return true;  
    },
    comment_likes: function(action_id, comment_id, page) {
      if (oldCommentLikeID != comment_id)
        $('#like-comment-item-' + action_id).html("<div class='ps_loading sm-ui-popup-loading'></div>");
      else {
        return;
      }
      oldCommentLikeID = comment_id;
      $.ajax({
        type: "POST",
        dataType: "html",
        url: sm4.core.baseUrl + 'advancedactivity/index/get-likes',
        data: {
          'action_id': action_id,
          'comment_id': comment_id,
          'page': page,
          'format': 'html',
          'showLikeWithoutIconInReplies': showLikeWithoutIconInReplies
        },
        success: function(responseHTML, textStatus, xhr) {

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
    comment_dislikes: function(action_id, comment_id, page) {
      if (oldCommentDisLikeID != comment_id)
        $('#dislike-comment-item-' + action_id).html("<div class='ps_loading sm-ui-popup-loading'></div>");
      else {
        return;
      }
      oldCommentDisLikeID = comment_id;
      $.ajax({
        type: "POST",
        dataType: "html",
        url: sm4.core.baseUrl + 'advancedactivity/index/get-dislikes',
        data: {
          'action_id': action_id,
          'comment_id': comment_id,
          'page': page,
          'format': 'html',
          'showLikeWithoutIconInReplies': showLikeWithoutIconInReplies
        },
        success: function(responseHTML, textStatus, xhr) {

          $('#dislike_comment_viewmore_link').css('display', 'none');
          $(document).data('loaded', true);
          if (page == 1)
            $('#dislike-comment-item-' + action_id).html(sm4.core.mobiPageHTML(responseHTML));
          if (page > 1)
            $('#dislikecommentmembers_ul').append(sm4.core.mobiPageHTML(responseHTML));
          sm4.core.dloader.refreshPage();
          sm4.core.runonce.trigger();
          $('#dislike-comment-item-' + action_id).css('display', 'block');
        }
      });
    },    
    doOnScrollLoadCommentLikes: function(action_id, comment_id, page) {

      if (nextlikecommentpage == 0) {
        window.onscroll = '';
        return;
      }
      if ($.type($('#feed_viewmore').get(0)) != 'undefined') {
        if ($.type($('#like_commentviewmore').get(0).offsetParent) != 'undefined') {
          var elementPostionY = $('#like_commentviewmore').get(0).offsetTop;
        } else {
          var elementPostionY = $$('#like_commentviewmore').get(0).y;
        }
        if (elementPostionY <= $(window).scrollTop() + ($(window).height() - 40)) {
          $('#like_commentviewmore').css('display', 'block');
          $('#like_commentviewmore').html('<i class="icon-spinner icon-spin ui-icon"></i>');
          this.comment_likes(action_id, comment_id, page)
        }
      }
    },
    doOnScrollLoadCommentDisLikes: function(action_id, comment_id, page) {

      if (nextdislikecommentpage == 0) {
        window.onscroll = '';
        return;
      }
      if ($.type($('#feed_viewmore').get(0)) != 'undefined') {
        if ($.type($('#dislike_commentviewmore').get(0).offsetParent) != 'undefined') {
          var elementPostionY = $('#dislike_commentviewmore').get(0).offsetTop;
        } else {
          var elementPostionY = $$('#dislike_commentviewmore').get(0).y;
        }
        if (elementPostionY <= $(window).scrollTop() + ($(window).height() - 40)) {
          $('#dislike_commentviewmore').css('display', 'block');
          $('#dislike_commentviewmore').html('<i class="icon-spinner icon-spin ui-icon"></i>');
          this.comment_dislikes(action_id, comment_id, page)
        }
      }
    },        
    doOnScrollLoadReplyLikes: function(action_id, comment_id, page) {

      if (nextlikereplypage == 0) {
        window.onscroll = '';
        return;
      }
      if ($.type($('#feed_viewmore').get(0)) != 'undefined') {
        if ($.type($('#like_replyviewmore').get(0).offsetParent) != 'undefined') {
          var elementPostionY = $('#like_replyviewmore').get(0).offsetTop;
        } else {
          var elementPostionY = $$('#like_replyviewmore').get(0).y;
        }
        if (elementPostionY <= $(window).scrollTop() + ($(window).height() - 40)) {
          $('#like_replyviewmore').css('display', 'block');
          $('#like_replyviewmore').html('<i class="icon-spinner icon-spin ui-icon"></i>');
          this.reply_likes(action_id, comment_id, page)
        }
      }
    },  
    doOnScrollLoadReplyDisLikes: function(action_id, comment_id, page) {

      if (nextdislikereplypage == 0) {
        window.onscroll = '';
        return;
      }
      if ($.type($('#feed_viewmore').get(0)) != 'undefined') {
        if ($.type($('#dislike_replyviewmore').get(0).offsetParent) != 'undefined') {
          var elementPostionY = $('#dislike_replyviewmore').get(0).offsetTop;
        } else {
          var elementPostionY = $$('#dislike_replyviewmore').get(0).y;
        }
        if (elementPostionY <= $(window).scrollTop() + ($(window).height() - 40)) {
          $('#dislike_replyviewmore').css('display', 'block');
          $('#dislike_replyviewmore').html('<i class="icon-spinner icon-spin ui-icon"></i>');
          this.reply_dislikes(action_id, comment_id, page)
        }
      }
    },           
    like_unlikeFeed: function(action, action_id, comment_id, self) {

     if(enabledModuleForMobile && (showAsLike == 0)) {
        if (action == 'like') {
            //MAKE LIKE CHANGE TO UNLIKE FIRST AND THEN SEND AJAX REQUEST:
            if ($.type($.mobile.activePage.find('#activity-item-' + action_id).find('.feed_item_btm a.feed_likes').get(0)) != 'undefined') {
              var likespan = $.trim($.mobile.activePage.find('#activity-item-' + action_id + ' .feed_item_btm a.feed_likes span').html()).split(' ');
              if(showLikeWithoutIcon != 3) {
                 $.mobile.activePage.find('#activity-item-' + action_id + ' .feed_item_btm a.feed_likes span').html(sm4.core.language.translate('% likes', parseInt(likespan[0]) + parseInt(1)));
              } else {
                $.mobile.activePage.find('#activity-item-' + action_id + ' .feed_item_btm a.feed_likes span').html(sm4.core.language.translate('% vote ups', parseInt(likespan[0]) + parseInt(1)));
              }
              if (typeof like_commentURL != 'undefined') {
                  $.mobile.activePage.find('#main-feed-' + action_id + ' .feed_item_option .ui-block-b a').attr('href', "javascript:void(0);");
                $.mobile.activePage.find('#main-feed-' + action_id + ' .feed_item_option .ui-block-b a').attr('onclick','sm4.activity.changeLikeDislikeColor()');
                 $.mobile.activePage.find('#main-feed-' + action_id + ' .feed_item_option .ui-block-a a').attr('href', 'javascript:void(0);');
                 $.mobile.activePage.find('#main-feed-' + action_id + ' .feed_item_option .ui-block-a a').attr('onclick', "javascript:sm4.activity.dislike(\'" + action_id + "\')");
              }
            }
            else {
              var likeSpanHtml = '<span class="sep">-</span>';
              
              if(showLikeWithoutIcon != 3) {
                if (typeof like_commentURL == 'undefined') {
                  var likeCountHtml = '<a href="javascript:void(0);" onclick="sm4.activity.openPopup(\'' + self.data('message') + '\', \'feedsharepopup\')" class="feed_likes"><span>' + sm4.core.language.translate('% like', 1) + '</span></a>'
                }
                else {
                  var likeCountHtml = '<a href="javascript:void(0);" onclick="sm4.activity.openPopup(\'' + like_commentURL + '/action_id/' + action_id + '\', \'feedsharepopup\')" class="feed_likes"><span>' + sm4.core.language.translate('% like', 1) + '</span></a>';
                }
              } else {
                 if (typeof like_commentURL == 'undefined') {
                  var likeCountHtml = '<a href="javascript:void(0);" onclick="sm4.activity.openPopup(\'' + self.data('message') + '\', \'feedsharepopup\')" class="feed_likes"><span>' + sm4.core.language.translate('% vote up', 1) + '</span></a>'
                }
                else {
                  var likeCountHtml = '<a href="javascript:void(0);" onclick="sm4.activity.openPopup(\'' + like_commentURL + '/action_id/' + action_id + '\', \'feedsharepopup\')" class="feed_likes"><span>' + sm4.core.language.translate('% vote up', 1) + '</span></a>';
                } 
              }
              
              if($.type($.mobile.activePage.find('#activity-item-' + action_id).find('.feed_item_btm a.feed_dislikes').get(0)) != 'undefined') {
                 likeCountHtml += likeSpanHtml; 
              } else if ($.type($.mobile.activePage.find('#activity-item-' + action_id).find('.feed_item_btm a.feed_comments').get(0)) != 'undefined') {
                likeCountHtml += '<span class="sep">-</span>';
              }
              $.mobile.activePage.find('#activity-item-' + action_id).find('.feed_item_btm').prepend($(likeCountHtml));
            }

            $.mobile.activePage.find('#main-feed-' + action_id + ' .feed_item_option .ui-block-b a').attr('href', "javascript:void(0);");
            $.mobile.activePage.find('#main-feed-' + action_id + ' .feed_item_option .ui-block-b a').attr('onclick', "javascript:sm4.activity.dislike(\'" + action_id + "\')");
            $.mobile.activePage.find('#main-feed-' + action_id + ' .feed_item_option .ui-block-a a').attr('href', "javascript:void(0);");
            $.mobile.activePage.find('#main-feed-' + action_id + ' .feed_item_option .ui-block-a a').attr('onclick', "sm.activity.changeLikeDislikeColor()");
            
            if(showLikeWithoutIcon != 3) {
                $.mobile.activePage.find('#main-feed-' + action_id + ' .feed_item_option .ui-block-a a').find(".ui-icon-thumbs-up-alt").removeClass("feed-like-icon feed-liked-icon").addClass("feed-unlike-icon feed-unliked-icon");
                $.mobile.activePage.find('#main-feed-' + action_id + ' .feed_item_option .ui-block-b a').find(".ui-icon-thumbs-down-alt").removeClass("feed-unlike-icon feed-unliked-icon").addClass("feed-unlike-icon");
            } else {
                $.mobile.activePage.find('#main-feed-' + action_id + ' .feed_item_option .ui-block-a a').find(".ui-icon-chevron-up").removeClass("feed-like-icon feed-liked-icon").addClass("feed-unlike-icon feed-unliked-icon");
                $.mobile.activePage.find('#main-feed-' + action_id + ' .feed_item_option .ui-block-b a').find(".ui-icon-chevron-down").removeClass("feed-unlike-icon feed-unliked-icon").addClass("feed-unlike-icon");
            }
            
        }
        else {
          var likespan = $.trim($.mobile.activePage.find('#activity-item-' + action_id + ' .feed_item_btm a.feed_likes span').html()).split(' ');
          if ((parseInt(likespan[0]) - parseInt(1)) > 0) {
            if(showLikeWithoutIcon != 3) {   
                var likestring = (parseInt(likespan[0]) - parseInt(1)) > 1 ? 'likes' : 'like';
            } else {
                var likestring = (parseInt(likespan[0]) - parseInt(1)) > 1 ? 'vote ups' : 'vote up';
            }
            $.mobile.activePage.find('#activity-item-' + action_id + ' .feed_item_btm a.feed_likes span').html(sm4.core.language.translate('% ' + likestring, (parseInt(likespan[0]) - parseInt(1))));
          }
          else {
            $.mobile.activePage.find('#activity-item-' + action_id + ' .feed_item_btm a.feed_likes').next().remove();
            $.mobile.activePage.find('#activity-item-' + action_id + ' .feed_item_btm a.feed_likes').remove();
          }
          
          if(showLikeWithoutIcon != 3) {
            $.mobile.activePage.find('#main-feed-' + action_id + ' .feed_item_option .ui-block-a a').find(".ui-icon-thumbs-up-alt").addClass("feed-like-icon feed-liked-icon").removeClass("feed-unlike-icon feed-unliked-icon");
            $.mobile.activePage.find('#main-feed-' + action_id + ' .feed_item_option .ui-block-b a').find(".ui-icon-thumbs-down-alt").removeClass("feed-unlike-icon feed-unliked-icon").addClass("feed-unlike-icon");
          } else {
              $.mobile.activePage.find('#main-feed-' + action_id + ' .feed_item_option .ui-block-a a').find(".ui-icon-chevron-up").addClass("feed-like-icon feed-liked-icon").removeClass("feed-unlike-icon feed-unliked-icon");
            $.mobile.activePage.find('#main-feed-' + action_id + ' .feed_item_option .ui-block-b a').find(".ui-icon-chevron-down").removeClass("feed-unlike-icon feed-unliked-icon").addClass("feed-unlike-icon");
          }
        }
      } else {
      if (action == 'like') {

        //MAKE LIKE CHANGE TO UNLIKE FIRST AND THEN SEND AJAX REQUEST:
        if ($.type($.mobile.activePage.find('#activity-item-' + action_id).find('.feed_item_btm a.feed_likes').get(0)) != 'undefined') {
          var likespan = $.trim($.mobile.activePage.find('#activity-item-' + action_id + ' .feed_item_btm a.feed_likes span').html()).split(' ');
          $.mobile.activePage.find('#activity-item-' + action_id + ' .feed_item_btm a.feed_likes span').html(sm4.core.language.translate('% likes', parseInt(likespan[0]) + parseInt(1)));
          if (typeof like_commentURL != 'undefined') {
            $.mobile.activePage.find('#main-feed-' + action_id + ' .feed_item_option .ui-block-a a').attr('onclick', 'javascript:sm4.activity.unlike(' + action_id + ');');
          }
        }
        else {

          if (typeof like_commentURL == 'undefined') {
            var likeCountHtml = '<a href="javascript:void(0);" onclick="sm4.activity.openPopup(\'' + self.data('message') + '\', \'feedsharepopup\')" class="feed_likes"><span>' + sm4.core.language.translate('% like', 1) + '</span></a>'
            self.attr('onclick', "javascript:sm4.activity.unlike(\'" + action_id + "\', \'\',$(this) )");
          }
          else {
            var likeCountHtml = '<a href="javascript:void(0);" onclick="sm4.activity.openPopup(\'' + like_commentURL + '/action_id/' + action_id + '\', \'feedsharepopup\')" class="feed_likes"><span>' + sm4.core.language.translate('% like', 1) + '</span></a>';

            $.mobile.activePage.find('#main-feed-' + action_id + ' .feed_item_option .ui-block-a a').attr('onclick', 'javascript:sm4.activity.unlike(' + action_id + ');');

          }
          if ($.type($.mobile.activePage.find('#activity-item-' + action_id).find('.feed_item_btm a.feed_comments').get(0)) != 'undefined') {
            likeCountHtml += '<span class="sep">-</span>';
          }
          $.mobile.activePage.find('#activity-item-' + action_id).find('.feed_item_btm').prepend($(likeCountHtml));

        }
         if(showLikeWithoutIcon != 3) {
            $.mobile.activePage.find('#main-feed-' + action_id + ' .feed_item_option .ui-block-a a').find(".ui-icon-thumbs-up-alt").removeClass("feed-like-icon feed-liked-icon").addClass("feed-unlike-icon feed-unliked-icon");
         } else {
            $.mobile.activePage.find('#main-feed-' + action_id + ' .feed_item_option .ui-block-a a').find(".ui-icon-chevron-up").removeClass("feed-like-icon feed-liked-icon").addClass("feed-unlike-icon feed-unliked-icon"); 
         }
      }
      else {
        var likespan = $.trim($.mobile.activePage.find('#activity-item-' + action_id + ' .feed_item_btm a.feed_likes span').html()).split(' ');
        if ((parseInt(likespan[0]) - parseInt(1)) > 0) {
          var likestring = (parseInt(likespan[0]) - parseInt(1)) > 1 ? 'likes' : 'like';
          $.mobile.activePage.find('#activity-item-' + action_id + ' .feed_item_btm a.feed_likes span').html(sm4.core.language.translate('% ' + likestring, (parseInt(likespan[0]) - parseInt(1))));
        }
        else {
          $.mobile.activePage.find('#activity-item-' + action_id + ' .feed_item_btm a.feed_likes').next().remove();
          $.mobile.activePage.find('#activity-item-' + action_id + ' .feed_item_btm a.feed_likes').remove();
        }

        $.mobile.activePage.find('#main-feed-' + action_id + ' .feed_item_option .ui-block-a a').attr('onclick', "javascript:sm4.activity.like(\'" + action_id + "\', \'\',$(this))");

        //$.mobile.activePage.find('#main-feed-' + action_id + ' .feed_item_option .ui-block-a a').html('<i class="ui-icon ui-icon-thumbs-up-alt feed-like-btn"></i>&nbsp;<span>' + sm4.core.language.translate('Like') + '</span>');

        $.mobile.activePage.find('#main-feed-' + action_id + ' .feed_item_option .ui-block-a a').find(".ui-icon-thumbs-up-alt").addClass("feed-like-icon feed-liked-icon").removeClass("feed-unlike-icon feed-unliked-icon");

      }
    }
      sm4.core.dloader.refreshPage();

    },
    like_unlikeComment: function(action, action_id, comment_id) {
      if(enabledModuleForMobile && (showAsLike == 0)) {
              if (action == 'like') {

        //MAKE LIKE CHANGE TO UNLIKE FIRST AND THEN SEND AJAX REQUEST:
        if ($.type($('#comments_comment_likes_' + comment_id).get(0)) != 'undefined') {
         
            if(showLikeWithoutIconInReplies != 3) {
              var likespan = "<span class='ui-icon ui-icon-thumbs-up-alt'>" + (parseInt($('#comments_comment_likes_' + comment_id).find('span').html()) + parseInt(1)); + "</span>";
              $('#comments_comment_likes_' + comment_id).html(sm4.core.mobiPageHTML(likespan));
            } else {
                var likespan = "<span>" + (parseInt($('#comments_comment_likes_' + comment_id).find('span').html()) + parseInt(1)); + "</span>";
                $('#comments_comment_likes_' + comment_id).html(sm4.core.mobiPageHTML(parseInt(likespan) + parseInt(1)));
            }

            $('#comment-' + comment_id + ' .comment_likes').attr('href', 'javascript:void(0);');
            $('#comment-' + comment_id + ' .comment_likes').attr('onclick', 'sm4.activity.changeLikeDislikeColor()');
            
            if(showLikeWithoutIconInReplies != 3) {
                $('#comment-' + comment_id + ' .comment_likes').html(sm4.core.language.translate('Like'));
            } else {
                $('#comment-' + comment_id + ' .comment_likes').addClass("ui-icon ui-icon-angle-up");   
            }
            $('#comment-' + comment_id + ' .comment_dislikes').attr('href', 'javascript:void(0);');
            $('#comment-' + comment_id + ' .comment_dislikes').attr('onclick', 'javascript:sm4.activity.dislike(' + action_id + ',' + comment_id + ');');
            
            if(showLikeWithoutIconInReplies != 3) {
              $('#comment-' + comment_id + ' .comment_dislikes').html(sm4.core.language.translate('Dislike'));
            } else {
                $('#comment-' + comment_id + ' .comment_dislikes').addClass("ui-icon ui-icon-angle-down");
            }
            
            var dislikecount = $('#comments_comment_dislikes_' + comment_id).find('span').html();
            
            if(showLikeWithoutIconInReplies != 3) {
                if ((parseInt(likespan[0]) - parseInt(1)) > 0)
                 //$('#comments_comment_dislikes_' + comment_id).html(sm4.core.mobiPageHTML(sm4.core.language.translate('% dislikes this', parseInt(likespan[0]) - parseInt(1))));
                 $('#comments_comment_dislikes_' + comment_id).html(sm4.core.mobiPageHTML("<span class='ui-icon ui-icon-thumbs-down-alt'>" + (parseInt(dislikecount) - parseInt(1)) + "</span>"));
                else {
                  $('#comments_comment_dislikes_' + comment_id).prev().remove();
                  $('#comments_comment_dislikes_' + comment_id).remove();
                }
            } else {
               if ((parseInt(likespan[0]) - parseInt(1)) > 0)
                 //$('#comments_comment_dislikes_' + comment_id).html(parseInt(likespan[0]) - parseInt(1));
                     $('#comments_comment_dislikes_' + comment_id).html(sm4.core.mobiPageHTML("<span class='ui-icon ui-icon-thumbs-down-alt'>" + (parseInt(dislikecount) - parseInt(1)) + "</span>"));
                else {
                 // $('#comments_comment_dislikes_' + comment_id).prev().remove();
                  $('#comments_comment_dislikes_' + comment_id).remove();
                } 
            }
        }
        else {
          if(showLikeWithoutIconInReplies != 3) {
             var likeCountHtml = '<span class="sep"> - </span><a href="javascript:void(0);" id="comments_comment_likes_' + comment_id + '" class="comments_comment_likes" onclick="$(\'#comment-activity-item-' + action_id + '\').css(\'display\', \'none\');$(\'#like-comment-item-' + action_id + '\').css(\'display\', \'block\'); sm4.activity.comment_likes(\'' + action_id + '\',' + comment_id + ', 1)"><span class="ui-icon ui-icon-thumbs-up-alt">' + sm4.core.language.translate(1) + '</span></a>';
          } else {
               var likeCountHtml = '<a href="javascript:void(0);" id="comments_comment_likes_' + comment_id + '" class="comments_comment_likes" onclick="$(\'#comment-activity-item-' + action_id + '\').css(\'display\', \'none\');$(\'#like-comment-item-' + action_id + '\').css(\'display\', \'block\'); sm4.activity.comment_likes(\'' + action_id + '\',' + comment_id + ', 1)"><span>' + sm4.core.language.translate(1) + '</span></a>';
          }
          
          likeCountHtml = sm4.core.mobiPageHTML(likeCountHtml);

            if(showLikeWithoutIconInReplies == 3) {    
              $('#comment-' + comment_id + ' .comment_likes').before($(likeCountHtml));
            } else {
              $('#comment-' + comment_id + ' .comment_dislikes').after($(likeCountHtml));
            }
            
            $('#comment-' + comment_id + ' .comment_likes').attr('href', 'javascript:void(0);');
            $('#comment-' + comment_id + ' .comment_likes').attr('onclick', 'sm4.activity.changeLikeDislikeColor()');
            
            if(showLikeWithoutIconInReplies != 3) {
                $('#comment-' + comment_id + ' .comment_likes').html(sm4.core.language.translate('Like'));
            } else {
                $('#comment-' + comment_id + ' .comment_likes').addClass("ui-icon ui-icon-angle-up");
            }

            $('#comment-' + comment_id + ' .comment_dislikes').attr('href', 'javascript:void(0);');
            $('#comment-' + comment_id + ' .comment_dislikes').attr('onclick', 'javascript:sm4.activity.dislike(' + action_id + ',' + comment_id + ');');
            
            if(showLikeWithoutIconInReplies != 3) {
                $('#comment-' + comment_id + ' .comment_dislikes').html(sm4.core.language.translate('Dislike'));
            } else {
                $('#comment-' + comment_id + ' .comment_dislikes').addClass("ui-icon ui-icon-angle-down");
            }
            
        }
      }
      else {
        var likespan = $.trim($('#comments_comment_likes_' + comment_id).html()).split(' ');

        if ((parseInt(likespan[0]) - parseInt(1)) > 0)
          $('#comments_comment_likes_' + comment_id).html(sm4.core.mobiPageHTML(sm4.core.language.translate('% likes this', parseInt(likespan[0]) - parseInt(1))));

        else {
          $('#comments_comment_likes_' + comment_id).prev().remove();
          $('#comments_comment_likes_' + comment_id).remove();
        }

        $('#comment-' + comment_id + ' .comment_likes').attr('onclick', 'javascript:sm4.activity.like(' + action_id + ',' + comment_id + ');');

        $('#comment-' + comment_id + ' .comment_likes').html(sm4.core.language.translate('Like'));

        } 
      } else {
      if (action == 'like') {

        //MAKE LIKE CHANGE TO UNLIKE FIRST AND THEN SEND AJAX REQUEST:
        if ($.type($('#comments_comment_likes_' + comment_id).get(0)) != 'undefined') {
          var likespan = $.trim($('#comments_comment_likes_' + comment_id).html()).split(' ');
          $('#comments_comment_likes_' + comment_id).html(sm4.core.mobiPageHTML(sm4.core.language.translate('% likes this', parseInt(likespan[0]) + parseInt(1))));

        }
        else {
          var likeCountHtml = '<span class="sep"> - </span><a href="javascript:void(0);" id="comments_comment_likes_' + comment_id + '" class="comments_comment_likes" onclick="$(\'#comment-activity-item-' + action_id + '\').css(\'display\', \'none\');$(\'#like-comment-item-' + action_id + '\').css(\'display\', \'block\'); sm4.activity.comment_likes(\'' + action_id + '\',' + comment_id + ', 1)"><span>' + sm4.core.language.translate('% likes this', 1) + '</span></a>';
          likeCountHtml = sm4.core.mobiPageHTML(likeCountHtml);

          $('#comment-' + comment_id + ' .comment_likes').after($(likeCountHtml));

        }

        $('#comment-' + comment_id + ' .comment_likes').attr('onclick', 'javascript:sm4.activity.unlike(' + action_id + ',' + comment_id + ');');

        $('#comment-' + comment_id + ' .comment_likes').html(sm4.core.language.translate('Unlike'));

      }
      else {
        var likespan = $.trim($('#comments_comment_likes_' + comment_id).html()).split(' ');

        if ((parseInt(likespan[0]) - parseInt(1)) > 0)
          $('#comments_comment_likes_' + comment_id).html(sm4.core.mobiPageHTML(sm4.core.language.translate('% likes this', parseInt(likespan[0]) - parseInt(1))));

        else {
          $('#comments_comment_likes_' + comment_id).prev().remove();
          $('#comments_comment_likes_' + comment_id).remove();
        }

        $('#comment-' + comment_id + ' .comment_likes').attr('onclick', 'javascript:sm4.activity.like(' + action_id + ',' + comment_id + ');');

        $('#comment-' + comment_id + ' .comment_likes').html(sm4.core.language.translate('Like'));

        }
      }
      sm4.core.dloader.refreshPage();

    },
    like: function(action_id, comment_id, self) {
      if ($.type(comment_id) == 'undefined' || comment_id == '') {
        this.like_unlikeFeed('like', action_id, comment_id, self);
      } else {
        this.like_unlikeComment('like', action_id, comment_id);
      }

      if ($.type(self) == 'undefined') {
        postVar = {
          'action_id': action_id,
          'comment_id': comment_id,
          'subject': $.mobile.activePage.advfeed_array.subject_guid,
          'format': 'json'
        }
        target = sm4.core.baseUrl + 'advancedactivity/index/like';
      }
      else {
        postVar = {
          'format': 'json',
          'Linkedin_action': 'like'
        }
        target = self.data('url');
      }

      $.ajax({
        type: "POST",
        dataType: "json",
        url: target,
        data: postVar,
        success: function(responseJSON, textStatus, xhr) {
           if(enabledModuleForMobile && (showAsLike == 0)) {
          if ($.type(comment_id) == 'undefined' || comment_id == '') {
             if(responseJSON.hasViewerDisLike && responseJSON.dislikeCount > 1) {
                  
                  var likespan = $.trim($.mobile.activePage.find('#activity-item-' + action_id + ' .feed_item_btm a.feed_dislikes span').html()).split(' ');
                  if(responseJSON.dislikeCount > 1) {
                      if(showLikeWithoutIconInReplies != 3) {
                        $.mobile.activePage.find('#activity-item-' + action_id + ' .feed_item_btm a.feed_dislikes span').html(sm4.core.mobiPageHTML(sm4.core.language.translate('% dislikes', parseInt(responseJSON.dislikeCount) - parseInt(1))));
                        } else {
                            $.mobile.activePage.find('#activity-item-' + action_id + ' .feed_item_btm a.feed_dislikes span').html(sm4.core.mobiPageHTML(sm4.core.language.translate('% vote downs', parseInt(responseJSON.dislikeCount) - parseInt(1))));
                        }
                      
                  } else {
                      $.mobile.activePage.find('#activity-item-' + action_id + ' .feed_item_btm a.feed_dislikes').prev().remove();
                      $.mobile.activePage.find('#activity-item-' + action_id + ' .feed_item_btm a.feed_dislikes').remove();
                  }
              }  else if(responseJSON.hasViewerDisLike && responseJSON.dislikeCount == 1) {
                    $.mobile.activePage.find('#activity-item-' + action_id + ' .feed_item_btm a.feed_dislikes').prev().remove();
                   $.mobile.activePage.find('#activity-item-' + action_id + ' .feed_item_btm a.feed_dislikes').remove();
              }
          } else {
                if(responseJSON.hasViewerDisLike && responseJSON.dislikeCount > 1) {

                    var dislikeCount = $('#comments_comment_dislikes_' + comment_id).find('span').html();
                    if(responseJSON.dislikeCount > 1) {
                      if(showLikeWithoutIconInReplies != 3) {
                        $('#comments_comment_dislikes_' + comment_id).html("<span class='ui-icon ui-icon-thumbs-down-alt'>" + (parseInt(responseJSON.dislikeCount) - parseInt(1)) + "</span>");
                      } else {
                          $('#comments_comment_dislikes_' + comment_id).html("<span>" + (parseInt(responseJSON.dislikeCount) - parseInt(1)) + "</span>");
                      } 
                  } else {
                      if(showLikeWithoutIconInReplies != 3) {  
                        $('#comments_comment_dislikes_' + comment_id).prev().remove();
                      }
                      $('#comments_comment_dislikes_' + comment_id).remove();
                  }
                }  else if(responseJSON.hasViewerDisLike && responseJSON.dislikeCount == 1) {
                   if(showLikeWithoutIconInReplies != 3) {  
                      $('#comments_comment_dislikes_' + comment_id).prev().remove();   
                   }
                   $('#comments_comment_dislikes_' + comment_id).remove();
                }
            }
          }  
          sm4.core.dloader.refreshPage();
          sm4.core.runonce.trigger();
        }.bind(this),
        error: function(xhr, textStatus, errorThrown) {
          if ($.type(comment_id) == 'undefined') {
            this.like_unlikeFeed('unlike', action_id, comment_id);
          }
          else {
            this.like_unlikeComment('unlike', action_id, comment_id);

          }
        }.bind(this),
        statusCode: {
          404: function(response) {
            if ($.type(comment_id) == 'undefined') {
              this.like_unlikeFeed('unlike', action_id, comment_id);
            }
            else {
              this.like_unlikeComment('unlike', action_id, comment_id);

            }
          }.bind(this)
        }
      });
    },
    unlike: function(action_id, comment_id, self) {
      //MAKE LIKE CHANGE TO UNLIKE FIRST AND THEN SEND AJAX REQUEST:
      if ($.type(comment_id) == 'undefined' || comment_id == '') {
        this.like_unlikeFeed('unlike', action_id, comment_id);
      }
      else {
        this.like_unlikeComment('unlike', action_id, comment_id);

      }

      if ($.type(self) == 'undefined') {
        postVar = {
          'action_id': action_id,
          'comment_id': comment_id,
          'subject': $.mobile.activePage.advfeed_array.subject_guid,
          'format': 'json'
        }
        target = sm4.core.baseUrl + 'advancedactivity/index/unlike';
      }
      else {
        postVar = {
          'format': 'json',
          'Linkedin_action': 'unlike'
        }
        target = self.data('url');
      }

      $.ajax({
        type: "POST",
        dataType: "json",
        url: target,
        data: postVar,
        success: function(responseJSON, textStatus, xhr) {
          sm4.core.dloader.refreshPage();
          sm4.core.runonce.trigger();
        }.bind(this),
        error: function(xhr, textStatus, errorThrown) {
          if ($.type(comment_id) == 'undefined') {
            this.like_unlikeFeed('like', action_id, comment_id);
          }
          else {
            this.like_unlikeComment('like', action_id, comment_id);

          }
        }.bind(this),
        statusCode: {
          404: function(response) {
            if ($.type(comment_id) == 'undefined') {
              this.like_unlikeFeed('like', action_id, comment_id);
            }
            else {
              this.like_unlikeComment('like', action_id, comment_id);

            }
          }.bind(this)
        }

      });
    },
    dislike: function(action_id, comment_id, self) {
      //MAKE LIKE CHANGE TO UNLIKE FIRST AND THEN SEND AJAX REQUEST:
      if ($.type(comment_id) == 'undefined' || comment_id == '') {
        this.dislike_unlikeFeed('unlike', action_id, comment_id);
      }
      else {
        this.dislike_unlikeComment('unlike', action_id, comment_id);

      }

      if ($.type(self) == 'undefined') {
        postVar = {
          'action_id': action_id,
          'comment_id': comment_id,
          'subject': $.mobile.activePage.advfeed_array.subject_guid,
          'format': 'json'
        }
        target = sm4.core.baseUrl + 'advancedactivity/index/unlike';
      }
      else {
        postVar = {
          'format': 'json',
          'Linkedin_action': 'unlike'
        }
        target = self.data('url');
      }

      $.ajax({
        type: "POST",
        dataType: "json",
        url: target,
        data: postVar,
        success: function(responseJSON, textStatus, xhr) {
          sm4.core.dloader.refreshPage();
          sm4.core.runonce.trigger();
          
          if ($.type(comment_id) == 'undefined' || comment_id == '') {
             if(responseJSON.hasViewerLike && responseJSON.likeCount > 1) {
                  
                  var likespan = $.trim($.mobile.activePage.find('#activity-item-' + action_id + ' .feed_item_btm a.feed_likes span').html()).split(' ');
                  if(responseJSON.likeCount > 1) {
                      if(showLikeWithoutIcon != 3) {
                        $.mobile.activePage.find('#activity-item-' + action_id + ' .feed_item_btm a.feed_likes span').html(sm4.core.mobiPageHTML(sm4.core.language.translate('% likes', parseInt(responseJSON.likeCount) - parseInt(1))));
                    } else {
                        $.mobile.activePage.find('#activity-item-' + action_id + ' .feed_item_btm a.feed_likes span').html(sm4.core.mobiPageHTML(sm4.core.language.translate('% vote ups', parseInt(responseJSON.likeCount) - parseInt(1))));
                    }
                  } else {
                    $.mobile.activePage.find('#activity-item-' + action_id + ' .feed_item_btm a.feed_likes').next().remove();
                      $.mobile.activePage.find('#activity-item-' + action_id + ' .feed_item_btm a.feed_likes').remove();
                  }
              }  else if(responseJSON.hasViewerLike && responseJSON.likeCount == 1) {
                    $.mobile.activePage.find('#activity-item-' + action_id + ' .feed_item_btm a.feed_likes').next().remove();
                   $.mobile.activePage.find('#activity-item-' + action_id + ' .feed_item_btm a.feed_likes').remove();
              }
          } else {
              if(responseJSON.hasViewerLike && responseJSON.likeCount > 1) {
                    //var likespan = $.trim($('#comments_comment_likes_' + comment_id).html()).split(' ');
                    var likecount = $('#comments_comment_likes_' + comment_id).find('span').html();
                    if(responseJSON.likeCount > 1) {
                      if(showLikeWithoutIconInReplies != 3) {
                        $('#comments_comment_likes_' + comment_id).html("<span class='ui-icon ui-icon-thumbs-up-alt'>" + (parseInt(responseJSON.likeCount) - parseInt(1)) + "</span>");
                      } else {
                          $('#comments_comment_likes_' + comment_id).html("<span>" + (parseInt(responseJSON.likeCount) - parseInt(1)) + "</span>");
                      } 
                  } else {
                      if(showLikeWithoutIconInReplies != 3) {  
                        $('#comments_comment_likes_' + comment_id).prev().remove();
                      }
                      $('#comments_comment_likes_' + comment_id).remove();
                  }
              }  else if(responseJSON.hasViewerLike && responseJSON.likeCount == 1) {
                    if(showLikeWithoutIconInReplies != 3) {  
                      $('#comments_comment_likes_' + comment_id).prev().remove();
                    } 
                    $('#comments_comment_likes_' + comment_id).remove();
              }
          }
            
        }.bind(this),
        error: function(xhr, textStatus, errorThrown) {
          if ($.type(comment_id) == 'undefined') {
            this.dislike_unlikeFeed('like', action_id, comment_id);
          }
          else {
            this.dislike_unlikeComment('like', action_id, comment_id);

          }
        }.bind(this),
        statusCode: {
          404: function(response) {
            if ($.type(comment_id) == 'undefined') {
              this.dislike_unlikeFeed('like', action_id, comment_id);
            }
            else {
              this.dislike_unlikeComment('like', action_id, comment_id);

            }
          }.bind(this)
        }

      });
    },   
    unlikeReply: function(action_id, comment_id, self) {
      //MAKE LIKE CHANGE TO UNLIKE FIRST AND THEN SEND AJAX REQUEST:
      if ($.type(comment_id) == 'undefined' || comment_id == '') {
        this.like_unlikeFeed('unlike', action_id, comment_id);
      }
      else {
        this.like_unlikeReply('unlike', action_id, comment_id);

      }

      if ($.type(self) == 'undefined') {
        postVar = {
          'action_id': action_id,
          'comment_id': comment_id,
          'subject': $.mobile.activePage.advfeed_array.subject_guid,
          'format': 'json'
        }
        target = sm4.core.baseUrl + 'advancedactivity/index/unlike';
      }
      else {
        postVar = {
          'format': 'json',
          'Linkedin_action': 'unlike'
        }
        target = self.data('url');
      }

      $.ajax({
        type: "POST",
        dataType: "json",
        url: target,
        data: postVar,
        success: function(responseJSON, textStatus, xhr) {
          sm4.core.dloader.refreshPage();
          sm4.core.runonce.trigger();
        }.bind(this),
        error: function(xhr, textStatus, errorThrown) {
          if ($.type(comment_id) == 'undefined') {
            this.like_unlikeFeed('like', action_id, comment_id);
          }
          else {
            this.like_unlikeReply('like', action_id, comment_id);

          }
        }.bind(this),
        statusCode: {
          404: function(response) {
            if ($.type(comment_id) == 'undefined') {
              this.like_unlikeFeed('like', action_id, comment_id);
            }
            else {
              this.like_unlikeReply('like', action_id, comment_id);

            }
          }.bind(this)
        }

      });
    },
    dislike_unlikeFeed: function(action, action_id, comment_id, self) {
      if (action == 'unlike') {
        //MAKE LIKE CHANGE TO UNLIKE FIRST AND THEN SEND AJAX REQUEST:
        if ($.type($.mobile.activePage.find('#activity-item-' + action_id).find('.feed_item_btm a.feed_dislikes').get(0)) != 'undefined') {
          var likespan = $.trim($.mobile.activePage.find('#activity-item-' + action_id + ' .feed_item_btm a.feed_dislikes span').html()).split(' ');
          if(showLikeWithoutIcon != 3) {
            $.mobile.activePage.find('#activity-item-' + action_id + ' .feed_item_btm a.feed_dislikes span').html(sm4.core.language.translate('% dislikes', parseInt(likespan[0]) + parseInt(1)));
          } else {
              $.mobile.activePage.find('#activity-item-' + action_id + ' .feed_item_btm a.feed_dislikes span').html(sm4.core.language.translate('% vote downs', parseInt(likespan[0]) + parseInt(1)));
          }
          if (typeof like_commentURL != 'undefined') {
            $.mobile.activePage.find('#main-feed-' + action_id + ' .feed_item_option .ui-block-b a').attr('href', "javascript:void(0);");
            $.mobile.activePage.find('#main-feed-' + action_id + ' .feed_item_option .ui-block-b a').attr('onclick','sm4.activity.changeLikeDislikeColor()');
            $.mobile.activePage.find('#main-feed-' + action_id + ' .feed_item_option .ui-block-a a').attr('href', 'javascript:void(0);');
            $.mobile.activePage.find('#main-feed-' + action_id + ' .feed_item_option .ui-block-a a').attr('onclick', "javascript:sm4.activity.like(\'" + action_id + "\')");
          }
        }
        else {
          var likeSpanHtml = '<span class="sep">-</span>';
          if(showLikeWithoutIcon != 3) {
            if (typeof like_commentURL == 'undefined') {
              var likeCountHtml = '<a href="javascript:void(0);" onclick="sm4.activity.openPopup(\'' + self.data('message') + '\', \'feedsharepopup\')" class="feed_dislikes"><span>' + sm4.core.language.translate('% dislike', 1) + '</span></a>'
            }
            else {
              var likeCountHtml = '<a href="javascript:void(0);" onclick="sm4.activity.openPopup(\'' + like_commentURL + '/action_id/' + action_id + '\', \'feedsharepopup\')" class="feed_dislikes"><span>' + sm4.core.language.translate('% dislike', 1) + '</span></a>';
            }
          } else {
            if (typeof like_commentURL == 'undefined') {
              var likeCountHtml = '<a href="javascript:void(0);" onclick="sm4.activity.openPopup(\'' + self.data('message') + '\', \'feedsharepopup\')" class="feed_dislikes"><span>' + sm4.core.language.translate('% vote down', 1) + '</span></a>'
            }
            else {
              var likeCountHtml = '<a href="javascript:void(0);" onclick="sm4.activity.openPopup(\'' + like_commentURL + '/action_id/' + action_id + '\', \'feedsharepopup\')" class="feed_dislikes"><span>' + sm4.core.language.translate('% vote down', 1) + '</span></a>';
            }
          }
            $.mobile.activePage.find('#main-feed-' + action_id + ' .feed_item_option .ui-block-a a').attr('href', "javascript:void(0);");
            
            $.mobile.activePage.find('#main-feed-' + action_id + ' .feed_item_option .ui-block-a a').attr('onclick', "javascript:sm4.activity.like(\'" + action_id + "\')");
        
           $.mobile.activePage.find('#main-feed-' + action_id + ' .feed_item_option .ui-block-b a').attr('href', "javascript:void(0);");
            $.mobile.activePage.find('#main-feed-' + action_id + ' .feed_item_option .ui-block-b a').attr('onclick', "sm.activity.changeLikeDislikeColor()");
          
          
          if($.type($.mobile.activePage.find('#activity-item-' + action_id).find('.feed_item_btm .feed_likes').get(0)) != 'undefined') {
          likeSpanHtml += likeCountHtml; 
       
          $.mobile.activePage.find('#activity-item-' + action_id).find('.feed_item_btm .feed_likes').after($(likeSpanHtml));
          } else {
              if ($.type($.mobile.activePage.find('#activity-item-' + action_id).find('.feed_item_btm a.feed_comments').get(0)) != 'undefined') {
                 likeCountHtml += '<span class="sep">-</span>';
              }
              $.mobile.activePage.find('#activity-item-' + action_id).find('.feed_item_btm').prepend($(likeCountHtml));
          }
        }

        if(showLikeWithoutIcon != 3) {  
            $.mobile.activePage.find('#main-feed-' + action_id + ' .feed_item_option .ui-block-b a').find(".ui-icon-thumbs-down-alt").removeClass("feed-unlike-icon feed-unliked-icon").addClass("feed-unlike-icon feed-unliked-icon");

            $.mobile.activePage.find('#main-feed-' + action_id + ' .feed_item_option .ui-block-a a').find(".ui-icon-thumbs-up-alt").removeClass("feed-unlike-icon feed-unliked-icon").addClass("ui-icon feed-like-icon feed-liked-icon");
        } else {
            $.mobile.activePage.find('#main-feed-' + action_id + ' .feed_item_option .ui-block-b a').find(".ui-icon-chevron-down").removeClass("feed-unlike-icon feed-unliked-icon").addClass("feed-unlike-icon feed-unliked-icon");

            $.mobile.activePage.find('#main-feed-' + action_id + ' .feed_item_option .ui-block-a a').find(".ui-icon-chevron-up").removeClass("feed-unlike-icon feed-unliked-icon").addClass("ui-icon feed-like-icon feed-liked-icon");
        }
      }
      else {
        var likespan = $.trim($.mobile.activePage.find('#activity-item-' + action_id + ' .feed_item_btm a.feed_likes span').html()).split(' ');
        
        if ((parseInt(likespan[0]) - parseInt(1)) > 0) {
          if(showLikeWithoutIcon != 3) {  
             var likestring = (parseInt(likespan[0]) - parseInt(1)) > 1 ? 'dislikes' : 'dislike';
          } else {
            var likestring = (parseInt(likespan[0]) - parseInt(1)) > 1 ? 'vote downs' : 'vote down';     
          }
          $.mobile.activePage.find('#activity-item-' + action_id + ' .feed_item_btm a.feed_likes span').html(sm4.core.language.translate('% ' + likestring, (parseInt(likespan[0]) - parseInt(1))));
        }
        else {
         // $.mobile.activePage.find('#activity-item-' + action_id + ' .feed_item_btm a.feed_likes').next().remove();
        //  $.mobile.activePage.find('#activity-item-' + action_id + ' .feed_item_btm a.feed_likes').remove();
        }

       if(showLikeWithoutIcon != 3) { 
        $.mobile.activePage.find('#main-feed-' + action_id + ' .feed_item_option .ui-block-a a').find(".ui-icon-thumbs-up-alt").addClass("feed-like-icon feed-liked-icon").removeClass("feed-unlike-icon feed-unliked-icon");
       } else {
           $.mobile.activePage.find('#main-feed-' + action_id + ' .feed_item_option .ui-block-a a').find(".ui-icon-chevron-up").addClass("feed-like-icon feed-liked-icon").removeClass("feed-unlike-icon feed-unliked-icon");
       }
      }
      sm4.core.dloader.refreshPage();

    }, 
    dislike_unlikeComment: function(action, action_id, comment_id) {

      if (action == 'like') {

        //MAKE LIKE CHANGE TO UNLIKE FIRST AND THEN SEND AJAX REQUEST:
        if ($.type($('#comments_comment_likes_' + comment_id).get(0)) != 'undefined') {
          var likespan = $.trim($('#comments_comment_likes_' + comment_id).html()).split(' ');
          $('#comments_comment_likes_' + comment_id).html(sm4.core.mobiPageHTML(sm4.core.language.translate('% likes this', parseInt(likespan[0]) + parseInt(1))));

        }
        else {
          var likeCountHtml = '<span class="sep"> - </span><a href="javascript:void(0);" id="comments_comment_likes_' + comment_id + '" class="comments_comment_likes" onclick="$(\'#comment-activity-item-' + action_id + '\').css(\'display\', \'none\');$(\'#like-comment-item-' + action_id + '\').css(\'display\', \'block\'); sm4.activity.comment_likes(\'' + action_id + '\',' + comment_id + ', 1)"><span>' + sm4.core.language.translate('% likes this', 1) + '</span></a>';
          likeCountHtml = sm4.core.mobiPageHTML(likeCountHtml);

          $('#comment-' + comment_id + ' .comment_likes').after($(likeCountHtml));

        }

        $('#comment-' + comment_id + ' .comment_likes').attr('onclick', 'javascript:sm4.activity.unlike(' + action_id + ',' + comment_id + ');');

        $('#comment-' + comment_id + ' .comment_likes').html(sm4.core.language.translate('Unlike'));

      }
      else {
       
        //MAKE LIKE CHANGE TO UNLIKE FIRST AND THEN SEND AJAX REQUEST:
        if ($.type($('#comments_comment_dislikes_' + comment_id).get(0)) != 'undefined') {
            var dislikecount =  $('#comments_comment_dislikes_' + comment_id).find('span').html();
            
            if(showLikeWithoutIconInReplies != 3) {
                $('#comments_comment_dislikes_' + comment_id).html("<span class='ui-icon ui-icon-thumbs-down-alt'>" + (parseInt(dislikecount) + parseInt(1)) + "</span>");
            } else {
                //$('#comments_comment_dislikes_' + comment_id).html(parseInt(likespan[0]) + parseInt(1));
                $('#comments_comment_dislikes_' + comment_id).html("<span>" + (parseInt(dislikecount) + parseInt(1)) + "</span>");
            }
            
            if(showLikeWithoutIconInReplies != 3) {
                $('#comment-' + comment_id + ' .comment_likes').html(sm4.core.language.translate('Like'));
            } else {
                $('#comment-' + comment_id + ' .comment_likes').addClass("ui-icon ui-icon-angle-up");
            }
            $('#comment-' + comment_id + ' .comment_likes').attr('onclick', 'javascript:sm4.activity.like(' + action_id + ',' + comment_id + ');');

            $('#comment-' + comment_id + ' .comment_dislikes').attr('onclick', 'sm4.activity.changeLikeDislikeColor()');

            if(showLikeWithoutIconInReplies != 3) { 
                $('#comment-' + comment_id + ' .comment_dislikes').html(sm4.core.language.translate('Dislike'));
            } else {
                $('#comment-' + comment_id + ' .comment_dislikes').addClass("ui-icon ui-icon-angle-down");
            }
            
            var likecount = $('#comments_comment_likes_' + comment_id).find('span').html();
            
            if(showLikeWithoutIconInReplies != 3) { 
                if ((parseInt(likecount) - parseInt(1)) > 0)
                  $('#comments_comment_likes_' + comment_id).html("<span class='ui-icon ui-icon-thumbs-up-alt'>" + (parseInt(likecount) - parseInt(1)) + "</span>");
                else {
                  $('#comments_comment_likes_' + comment_id).prev().remove();
                  $('#comments_comment_likes_' + comment_id).remove();
                }
            } else {
                if ((parseInt(likecount) - parseInt(1)) > 0)
                  $('#comments_comment_likes_' + comment_id).html("<span class='ui-icon ui-icon-thumbs-up-alt'>" + (parseInt(likecount) - parseInt(1)) + "</span>");
                else {
                  //$('#comments_comment_likes_' + comment_id).prev().remove();
                  $('#comments_comment_likes_' + comment_id).remove();
                }
            }
        }
        else {
            
        if(showLikeWithoutIconInReplies != 3) {     
            $('#comment-' + comment_id + ' .comment_likes').html(sm4.core.language.translate('Like'));
        } else {
            $('#comment-' + comment_id + ' .comment_likes').addClass("ui-icon ui-icon-angle-up");
        }
        $('#comment-' + comment_id + ' .comment_likes').attr('onclick', 'javascript:sm4.activity.like(' + action_id + ',' + comment_id + ');');
        
        $('#comment-' + comment_id + ' .comment_dislikes').attr('onclick', 'sm4.activity.changeLikeDislikeColor()');

        if(showLikeWithoutIconInReplies != 3) {  
            $('#comment-' + comment_id + ' .comment_dislikes').html(sm4.core.language.translate('Dislike'));
        } else {
            $('#comment-' + comment_id + ' .comment_dislikes').addClass("ui-icon ui-icon-angle-down");
        }
        if(showLikeWithoutIconInReplies != 3) {  
            if(showDislikeUsers == 1) {  
                
                var likeCountHtml = '<span class="sep"> - </span><a href="javascript:void(0);" id="comments_comment_dislikes_' + comment_id + '" class="comments_comment_dislikes" onclick="$(\'#comment-activity-item-' + action_id + '\').css(\'display\', \'none\');$(\'#dislike-comment-item-' + action_id + '\').css(\'display\', \'block\'); sm4.activity.comment_dislikes(\'' + action_id + '\',' + comment_id + ', 1)"><span class="ui-icon ui-icon-thumbs-down-alt">' + sm4.core.language.translate(1) + '</span></a>';
            } else {
                var likeCountHtml = '<span class="sep"> - </span><a href="javascript:void(0);" id="comments_comment_dislikes_' + comment_id + '" class="comments_comment_dislikes" onclick=""><span class="ui-icon ui-icon-thumbs-down-alt">' + sm4.core.language.translate(1) + '</span></a>';
            }
            
          } else {
              if(showDislikeUsers == 1) {  
                var likeCountHtml = '<a href="javascript:void(0);" id="comments_comment_dislikes_' + comment_id + '" class="comments_comment_dislikes" onclick="$(\'#comment-activity-item-' + action_id + '\').css(\'display\', \'none\');$(\'#dislike-comment-item-' + action_id + '\').css(\'display\', \'block\'); sm4.activity.comment_dislikes(\'' + action_id + '\',' + comment_id + ', 1)"><span>' + sm4.core.language.translate(1) + '</span></a>';
              } else {
                  var likeCountHtml = '<a href="javascript:void(0);" id="comments_comment_dislikes_' + comment_id + '" class="comments_comment_dislikes"><span>' + sm4.core.language.translate(1) + '</span></a>';
              }
          }
          
          likeCountHtml = sm4.core.mobiPageHTML(likeCountHtml);

          if($.type($('#comment-' + comment_id + ' .comments_comment_likes').get(0)) != 'undefined') {
            if(showLikeWithoutIconInReplies != 3) {       
               $('#comment-' + comment_id + ' .comments_comment_likes').after($(likeCountHtml));
            } else {
               $('#comment-' + comment_id + ' .comment_dislikes').before($(likeCountHtml)); 
            }
          } else { 
            if(showLikeWithoutIconInReplies == 3) {    
              $('#comment-' + comment_id + ' .comment_dislikes').before($(likeCountHtml));
            } else {
              $('#comment-' + comment_id + ' .comment_dislikes').after($(likeCountHtml));  
            }
          }
        }

      }

      sm4.core.dloader.refreshPage();

    },       
    likeReply: function(action_id, comment_id, self) {
      if ($.type(comment_id) == 'undefined' || comment_id == '') {
        this.like_unlikeFeed('like', action_id, comment_id, self);
      } else {
        this.like_unlikeReply('like', action_id, comment_id);
      }

      if ($.type(self) == 'undefined') {
        postVar = {
          'action_id': action_id,
          'comment_id': comment_id,
          'subject': $.mobile.activePage.advfeed_array.subject_guid,
          'format': 'json'
        }
        target = sm4.core.baseUrl + 'advancedactivity/index/like';
      }
      else {
        postVar = {
          'format': 'json',
          'Linkedin_action': 'like'
        }
        target = self.data('url');
      }

      $.ajax({
        type: "POST",
        dataType: "json",
        url: target,
        data: postVar,
        success: function(responseJSON, textStatus, xhr) {
           if(enabledModuleForMobile && (showAsLike == 0)) {
          if ($.type(comment_id) == 'undefined' || comment_id == '') {
             if(responseJSON.hasViewerDisLike && responseJSON.dislikeCount > 1) {
                  
                  var likespan = $.trim($.mobile.activePage.find('#activity-item-' + action_id + ' .feed_item_btm a.feed_dislikes span').html()).split(' ');
                  if(responseJSON.dislikeCount > 1) {
                      if(showLikeWithoutIconInReplies != 3) {
                        $.mobile.activePage.find('#activity-item-' + action_id + ' .feed_item_btm a.feed_dislikes span').html(sm4.core.mobiPageHTML(sm4.core.language.translate('% dislikes', parseInt(responseJSON.dislikeCount) - parseInt(1))));
                        } else {
                            $.mobile.activePage.find('#activity-item-' + action_id + ' .feed_item_btm a.feed_dislikes span').html(sm4.core.mobiPageHTML(sm4.core.language.translate('% vote downs', parseInt(responseJSON.dislikeCount) - parseInt(1))));
                        }
                      
                  } else {
                      $.mobile.activePage.find('#activity-item-' + action_id + ' .feed_item_btm a.feed_dislikes').prev().remove();
                      $.mobile.activePage.find('#activity-item-' + action_id + ' .feed_item_btm a.feed_dislikes').remove();
                  }
              }  else if(responseJSON.hasViewerDisLike && responseJSON.dislikeCount == 1) {
                    $.mobile.activePage.find('#activity-item-' + action_id + ' .feed_item_btm a.feed_dislikes').prev().remove();
                   $.mobile.activePage.find('#activity-item-' + action_id + ' .feed_item_btm a.feed_dislikes').remove();
              }
          } else {
                if(responseJSON.hasViewerDisLike && responseJSON.dislikeCount > 1) {

                    var dislikeCount = $('#replies_reply_dislikes_' + comment_id).find('span').html();
                    if(responseJSON.dislikeCount > 1) {
                      if(showLikeWithoutIconInReplies != 3) {
                        $('#replies_reply_dislikes_' + comment_id).html("<span class='ui-icon ui-icon-thumbs-down-alt'>" + (parseInt(responseJSON.dislikeCount) - parseInt(1)) + "</span>");
                      } else {
                          $('#replies_reply_dislikes_' + comment_id).html("<span>" + (parseInt(responseJSON.dislikeCount) - parseInt(1)) + "</span>");
                      } 
                  } else {
                      if(showLikeWithoutIconInReplies != 3) {  
                        $('#replies_reply_dislikes_' + comment_id).prev().remove();
                      }
                      $('#replies_reply_dislikes_' + comment_id).remove();
                  }
                }  else if(responseJSON.hasViewerDisLike && responseJSON.dislikeCount == 1) {
                   if(showLikeWithoutIconInReplies != 3) {  
                      $('#replies_reply_dislikes_' + comment_id).prev().remove();   
                   }
                   $('#replies_reply_dislikes_' + comment_id).remove();
                }
            }
          }  
          sm4.core.dloader.refreshPage();
          sm4.core.runonce.trigger();
        }.bind(this),
        error: function(xhr, textStatus, errorThrown) {
          if ($.type(comment_id) == 'undefined') {
            this.like_unlikeFeed('unlike', action_id, comment_id);
          }
          else {
            this.like_unlikeComment('unlike', action_id, comment_id);

          }
        }.bind(this),
        statusCode: {
          404: function(response) {
            if ($.type(comment_id) == 'undefined') {
              this.like_unlikeFeed('unlike', action_id, comment_id);
            }
            else {
              this.like_unlikeComment('unlike', action_id, comment_id);

            }
          }.bind(this)
        }
      });
    },
    like_unlikeReply: function(action, action_id, comment_id) {
      if(enabledModuleForMobile && (showAsLike == 0)) {
              if (action == 'like') {

        //MAKE LIKE CHANGE TO UNLIKE FIRST AND THEN SEND AJAX REQUEST:
        if ($.type($('#replies_reply_likes_' + comment_id).get(0)) != 'undefined') {
         
            if(showLikeWithoutIconInReplies != 3) {
              var likespan = "<span class='ui-icon ui-icon-thumbs-up-alt'>" + (parseInt($('#replies_reply_likes_' + comment_id).find('span').html()) + parseInt(1)); + "</span>";
              $('#replies_reply_likes_' + comment_id).html(sm4.core.mobiPageHTML(likespan));
            } else {
                var likespan = "<span>" + (parseInt($('#replies_reply_likes_' + comment_id).find('span').html()) + parseInt(1)); + "</span>";
                $('#replies_reply_likes_' + comment_id).html(sm4.core.mobiPageHTML(parseInt(likespan) + parseInt(1)));
            }

            $('#reply-' + comment_id + ' .reply_likes').attr('href', 'javascript:void(0);');
            $('#reply-' + comment_id + ' .reply_likes').attr('onclick', 'sm4.activity.changeLikeDislikeColor()');
            
            if(showLikeWithoutIconInReplies != 3) {
                $('#reply-' + comment_id + ' .reply_likes').html(sm4.core.language.translate('Like'));
            } else {
                $('#reply-' + comment_id + ' .reply_likes').addClass("ui-icon ui-icon-angle-up");   
            }
            $('#reply-' + comment_id + ' .reply_dislikes').attr('href', 'javascript:void(0);');
            $('#reply-' + comment_id + ' .reply_dislikes').attr('onclick', 'javascript:sm4.activity.dislikeReply(' + action_id + ',' + comment_id + ');');
            
            if(showLikeWithoutIconInReplies != 3) {
              $('#reply-' + comment_id + ' .reply_dislikes').html(sm4.core.language.translate('Dislike'));
            } else {
                $('#reply-' + comment_id + ' .reply_dislikes').addClass("ui-icon ui-icon-angle-down");
            }
            
            var dislikecount = $('#replies_reply_dislikes_' + comment_id).find('span').html();
            
            if(showLikeWithoutIconInReplies != 3) {
                if ((parseInt(likespan[0]) - parseInt(1)) > 0)
                 //$('#replies_reply_dislikes_' + comment_id).html(sm4.core.mobiPageHTML(sm4.core.language.translate('% dislikes this', parseInt(likespan[0]) - parseInt(1))));
                 $('#replies_reply_dislikes_' + comment_id).html(sm4.core.mobiPageHTML("<span class='ui-icon ui-icon-thumbs-down-alt'>" + (parseInt(dislikecount) - parseInt(1)) + "</span>"));
                else {
                  $('#replies_reply_dislikes_' + comment_id).prev().remove();
                  $('#replies_reply_dislikes_' + comment_id).remove();
                }
            } else {
               if ((parseInt(likespan[0]) - parseInt(1)) > 0)
                 //$('#replies_reply_dislikes_' + comment_id).html(parseInt(likespan[0]) - parseInt(1));
                     $('#replies_reply_dislikes_' + comment_id).html(sm4.core.mobiPageHTML("<span class='ui-icon ui-icon-thumbs-down-alt'>" + (parseInt(dislikecount) - parseInt(1)) + "</span>"));
                else {
                 // $('#replies_reply_dislikes_' + comment_id).prev().remove();
                  $('#replies_reply_dislikes_' + comment_id).remove();
                } 
            }
        }
        else {
          if(showLikeWithoutIconInReplies != 3) {
             var likeCountHtml = '<span class="sep"> - </span><a href="javascript:void(0);" id="replies_reply_likes_' + comment_id + '" class="replies_reply_likes" onclick="$(\'#reply-activity-item-' + action_id + '\').css(\'display\', \'none\');$(\'#like-reply-item-' + action_id + '\').css(\'display\', \'block\'); sm4.activity.reply_likes(\'' + action_id + '\',' + comment_id + ', 1)"><span class="ui-icon ui-icon-thumbs-up-alt">' + sm4.core.language.translate(1) + '</span></a>';
          } else {
               var likeCountHtml = '<a href="javascript:void(0);" id="replies_reply_likes_' + comment_id + '" class="replies_reply_likes" onclick="$(\'#reply-activity-item-' + action_id + '\').css(\'display\', \'none\');$(\'#like-reply-item-' + action_id + '\').css(\'display\', \'block\'); sm4.activity.reply_likes(\'' + action_id + '\',' + comment_id + ', 1)"><span>' + sm4.core.language.translate(1) + '</span></a>';
          }
          
          likeCountHtml = sm4.core.mobiPageHTML(likeCountHtml);

            if(showLikeWithoutIconInReplies == 3) {    
              $('#reply-' + comment_id + ' .reply_likes').before($(likeCountHtml));
            } else {
              $('#reply-' + comment_id + ' .reply_dislikes').after($(likeCountHtml));
            }
            
            $('#reply-' + comment_id + ' .reply_likes').attr('href', 'javascript:void(0);');
            $('#reply-' + comment_id + ' .reply_likes').attr('onclick', 'sm4.activity.changeLikeDislikeColor()');
            
            if(showLikeWithoutIconInReplies != 3) {
                $('#reply-' + comment_id + ' .reply_likes').html(sm4.core.language.translate('Like'));
            } else {
                $('#reply-' + comment_id + ' .reply_likes').addClass("ui-icon ui-icon-angle-up");
            }

            $('#reply-' + comment_id + ' .reply_dislikes').attr('href', 'javascript:void(0);');
            $('#reply-' + comment_id + ' .reply_dislikes').attr('onclick', 'javascript:sm4.activity.dislikeReply(' + action_id + ',' + comment_id + ');');
            
            if(showLikeWithoutIconInReplies != 3) {
                $('#reply-' + comment_id + ' .reply_dislikes').html(sm4.core.language.translate('Dislike'));
            } else {
                $('#reply-' + comment_id + ' .reply_dislikes').addClass("ui-icon ui-icon-angle-down");
            }
            
        }
      }
      else {
        var likespan = $.trim($('#replies_reply_likes_' + comment_id).html()).split(' ');

        if ((parseInt(likespan[0]) - parseInt(1)) > 0)
          $('#replies_reply_likes_' + comment_id).html(sm4.core.mobiPageHTML(sm4.core.language.translate('% likes this', parseInt(likespan[0]) - parseInt(1))));

        else {
          $('#replies_reply_likes_' + comment_id).prev().remove();
          $('#replies_reply_likes_' + comment_id).remove();
        }

        $('#reply-' + comment_id + ' .reply_likes').attr('onclick', 'javascript:sm4.activity.likeReply(' + action_id + ',' + comment_id + ');');

        $('#reply-' + comment_id + ' .reply_likes').html(sm4.core.language.translate('Like'));

        } 
      } else {
      if (action == 'like') {

        //MAKE LIKE CHANGE TO UNLIKE FIRST AND THEN SEND AJAX REQUEST:
        if ($.type($('#replies_reply_likes_' + comment_id).get(0)) != 'undefined') {
          var likespan = $.trim($('#replies_reply_likes_' + comment_id).html()).split(' ');
          $('#replies_reply_likes_' + comment_id).html(sm4.core.mobiPageHTML(sm4.core.language.translate('% likes this', parseInt(likespan[0]) + parseInt(1))));

        }
        else {
          var likeCountHtml = '<span class="sep"> - </span><a href="javascript:void(0);" id="replies_reply_likes_' + comment_id + '" class="replies_reply_likes" onclick="$(\'#reply-activity-item-' + action_id + '\').css(\'display\', \'none\');$(\'#like-reply-item-' + action_id + '\').css(\'display\', \'block\'); sm4.activity.reply_likes(\'' + action_id + '\',' + comment_id + ', 1)"><span>' + sm4.core.language.translate('% likes this', 1) + '</span></a>';
          likeCountHtml = sm4.core.mobiPageHTML(likeCountHtml);

          $('#reply-' + comment_id + ' .reply_likes').after($(likeCountHtml));

        }

        $('#reply-' + comment_id + ' .reply_likes').attr('onclick', 'javascript:sm4.activity.unlike(' + action_id + ',' + comment_id + ');');

        $('#reply-' + comment_id + ' .reply_likes').html(sm4.core.language.translate('Unlike'));

      }
      else {
        var likespan = $.trim($('#replies_reply_likes_' + comment_id).html()).split(' ');

        if ((parseInt(likespan[0]) - parseInt(1)) > 0)
          $('#replies_reply_likes_' + comment_id).html(sm4.core.mobiPageHTML(sm4.core.language.translate('% likes this', parseInt(likespan[0]) - parseInt(1))));

        else {
          $('#replies_reply_likes_' + comment_id).prev().remove();
          $('#replies_reply_likes_' + comment_id).remove();
        }

        $('#reply-' + comment_id + ' .reply_likes').attr('onclick', 'javascript:sm4.activity.likeReply(' + action_id + ',' + comment_id + ');');

        $('#reply-' + comment_id + ' .reply_likes').html(sm4.core.language.translate('Like'));

        }
      }
      sm4.core.dloader.refreshPage();

    },
    dislikeReply: function(action_id, comment_id, self) {
      //MAKE LIKE CHANGE TO UNLIKE FIRST AND THEN SEND AJAX REQUEST:
      if ($.type(comment_id) == 'undefined' || comment_id == '') {
        this.dislike_unlikeFeed('unlike', action_id, comment_id);
      }
      else {
        this.dislike_unlikeReply('unlike', action_id, comment_id);

      }

      if ($.type(self) == 'undefined') {
        postVar = {
          'action_id': action_id,
          'comment_id': comment_id,
          'subject': $.mobile.activePage.advfeed_array.subject_guid,
          'format': 'json'
        }
        target = sm4.core.baseUrl + 'advancedactivity/index/unlike';
      }
      else {
        postVar = {
          'format': 'json',
          'Linkedin_action': 'unlike'
        }
        target = self.data('url');
      }

      $.ajax({
        type: "POST",
        dataType: "json",
        url: target,
        data: postVar,
        success: function(responseJSON, textStatus, xhr) {
          sm4.core.dloader.refreshPage();
          sm4.core.runonce.trigger();
          
          if ($.type(comment_id) == 'undefined' || comment_id == '') {
             if(responseJSON.hasViewerLike && responseJSON.likeCount > 1) {
                  
                  var likespan = $.trim($.mobile.activePage.find('#activity-item-' + action_id + ' .feed_item_btm a.feed_likes span').html()).split(' ');
                  if(responseJSON.likeCount > 1) {
                      if(showLikeWithoutIcon != 3) {
                        $.mobile.activePage.find('#activity-item-' + action_id + ' .feed_item_btm a.feed_likes span').html(sm4.core.mobiPageHTML(sm4.core.language.translate('% likes', parseInt(responseJSON.likeCount) - parseInt(1))));
                    } else {
                        $.mobile.activePage.find('#activity-item-' + action_id + ' .feed_item_btm a.feed_likes span').html(sm4.core.mobiPageHTML(sm4.core.language.translate('% vote ups', parseInt(responseJSON.likeCount) - parseInt(1))));
                    }
                  } else {
                    $.mobile.activePage.find('#activity-item-' + action_id + ' .feed_item_btm a.feed_likes').next().remove();
                      $.mobile.activePage.find('#activity-item-' + action_id + ' .feed_item_btm a.feed_likes').remove();
                  }
              }  else if(responseJSON.hasViewerLike && responseJSON.likeCount == 1) {
                    $.mobile.activePage.find('#activity-item-' + action_id + ' .feed_item_btm a.feed_likes').next().remove();
                   $.mobile.activePage.find('#activity-item-' + action_id + ' .feed_item_btm a.feed_likes').remove();
              }
          } else {
              if(responseJSON.hasViewerLike && responseJSON.likeCount > 1) {
                    //var likespan = $.trim($('#replies_reply_likes_' + comment_id).html()).split(' ');
                    var likecount = $('#replies_reply_likes_' + comment_id).find('span').html();
                    if(responseJSON.likeCount > 1) {
                      if(showLikeWithoutIconInReplies != 3) {
                        $('#replies_reply_likes_' + comment_id).html("<span class='ui-icon ui-icon-thumbs-up-alt'>" + (parseInt(responseJSON.likeCount) - parseInt(1)) + "</span>");
                      } else {
                          $('#replies_reply_likes_' + comment_id).html("<span>" + (parseInt(responseJSON.likeCount) - parseInt(1)) + "</span>");
                      } 
                  } else {
                      if(showLikeWithoutIconInReplies != 3) {  
                        $('#replies_reply_likes_' + comment_id).prev().remove();
                      }
                      $('#replies_reply_likes_' + comment_id).remove();
                  }
              }  else if(responseJSON.hasViewerLike && responseJSON.likeCount == 1) {
                    if(showLikeWithoutIconInReplies != 3) {  
                      $('#replies_reply_likes_' + comment_id).prev().remove();
                    } 
                    $('#replies_reply_likes_' + comment_id).remove();
              }
          }
            
        }.bind(this),
        error: function(xhr, textStatus, errorThrown) {
          if ($.type(comment_id) == 'undefined') {
            this.dislike_unlikeFeed('like', action_id, comment_id);
          }
          else {
            this.dislike_unlikeReply('like', action_id, comment_id);

          }
        }.bind(this),
        statusCode: {
          404: function(response) {
            if ($.type(comment_id) == 'undefined') {
              this.dislike_unlikeFeed('like', action_id, comment_id);
            }
            else {
              this.dislike_unlikeReply('like', action_id, comment_id);

            }
          }.bind(this)
        }

      });
    },  
    dislike_unlikeReply: function(action, action_id, comment_id) {

      if (action == 'like') {

        //MAKE LIKE CHANGE TO UNLIKE FIRST AND THEN SEND AJAX REQUEST:
        if ($.type($('#replies_reply_likes_' + comment_id).get(0)) != 'undefined') {
          var likespan = $.trim($('#replies_reply_likes_' + comment_id).html()).split(' ');
          $('#replies_reply_likes_' + comment_id).html(sm4.core.mobiPageHTML(sm4.core.language.translate('% likes this', parseInt(likespan[0]) + parseInt(1))));

        }
        else {
          var likeCountHtml = '<span class="sep"> - </span><a href="javascript:void(0);" id="replies_reply_likes_' + comment_id + '" class="replies_reply_likes" onclick="$(\'#reply-activity-item-' + action_id + '\').css(\'display\', \'none\');$(\'#like-comment-item-' + action_id + '\').css(\'display\', \'block\'); sm4.activity.reply_likes(\'' + action_id + '\',' + comment_id + ', 1)"><span>' + sm4.core.language.translate('% likes this', 1) + '</span></a>';
          likeCountHtml = sm4.core.mobiPageHTML(likeCountHtml);

          $('#reply-' + comment_id + ' .reply_likes').after($(likeCountHtml));

        }

        $('#reply-' + comment_id + ' .reply_likes').attr('onclick', 'javascript:sm4.activity.unlike(' + action_id + ',' + comment_id + ');');

        $('#reply-' + comment_id + ' .reply_likes').html(sm4.core.language.translate('Unlike'));

      }
      else {
       
        //MAKE LIKE CHANGE TO UNLIKE FIRST AND THEN SEND AJAX REQUEST:
        if ($.type($('#replies_reply_dislikes_' + comment_id).get(0)) != 'undefined') {
            var dislikecount =  $('#replies_reply_dislikes_' + comment_id).find('span').html();
            
            if(showLikeWithoutIconInReplies != 3) {
                $('#replies_reply_dislikes_' + comment_id).html("<span class='ui-icon ui-icon-thumbs-down-alt'>" + (parseInt(dislikecount) + parseInt(1)) + "</span>");
            } else {
                //$('#replies_reply_dislikes_' + comment_id).html(parseInt(likespan[0]) + parseInt(1));
                $('#replies_reply_dislikes_' + comment_id).html("<span>" + (parseInt(dislikecount) + parseInt(1)) + "</span>");
            }
            
            if(showLikeWithoutIconInReplies != 3) {
                $('#reply-' + comment_id + ' .reply_likes').html(sm4.core.language.translate('Like'));
            } else {
                $('#reply-' + comment_id + ' .reply_likes').addClass("ui-icon ui-icon-angle-up");
            }
            $('#reply-' + comment_id + ' .reply_likes').attr('onclick', 'javascript:sm4.activity.likeReply(' + action_id + ',' + comment_id + ');');

            $('#reply-' + comment_id + ' .reply_dislikes').attr('onclick', 'sm4.activity.changeLikeDislikeColor()');

            if(showLikeWithoutIconInReplies != 3) { 
                $('#reply-' + comment_id + ' .reply_dislikes').html(sm4.core.language.translate('Dislike'));
            } else {
                $('#reply-' + comment_id + ' .reply_dislikes').addClass("ui-icon ui-icon-angle-down");
            }
            
            var likecount = $('#replies_reply_likes_' + comment_id).find('span').html();
            
            if(showLikeWithoutIconInReplies != 3) { 
                if ((parseInt(likecount) - parseInt(1)) > 0)
                  $('#replies_reply_likes_' + comment_id).html("<span class='ui-icon ui-icon-thumbs-up-alt'>" + (parseInt(likecount) - parseInt(1)) + "</span>");
                else {
                  $('#replies_reply_likes_' + comment_id).prev().remove();
                  $('#replies_reply_likes_' + comment_id).remove();
                }
            } else {
                if ((parseInt(likecount) - parseInt(1)) > 0)
                  $('#replies_reply_likes_' + comment_id).html("<span class='ui-icon ui-icon-thumbs-up-alt'>" + (parseInt(likecount) - parseInt(1)) + "</span>");
                else {
                  //$('#replies_reply_likes_' + comment_id).prev().remove();
                  $('#replies_reply_likes_' + comment_id).remove();
                }
            }
        }
        else {
            
        if(showLikeWithoutIconInReplies != 3) {     
            $('#reply-' + comment_id + ' .reply_likes').html(sm4.core.language.translate('Like'));
        } else {
            $('#reply-' + comment_id + ' .reply_likes').addClass("ui-icon ui-icon-angle-up");
        }
        $('#reply-' + comment_id + ' .reply_likes').attr('onclick', 'javascript:sm4.activity.likeReply(' + action_id + ',' + comment_id + ');');
        
        $('#reply-' + comment_id + ' .reply_dislikes').attr('onclick', 'sm4.activity.changeLikeDislikeColor()');

        if(showLikeWithoutIconInReplies != 3) {  
            $('#reply-' + comment_id + ' .reply_dislikes').html(sm4.core.language.translate('Dislike'));
        } else {
            $('#reply-' + comment_id + ' .reply_dislikes').addClass("ui-icon ui-icon-angle-down");
        }
          if(showLikeWithoutIconInReplies != 3) {  
            if(showDislikeUsers == 1) {  
                
                var likeCountHtml = '<span class="sep"> - </span><a href="javascript:void(0);" id="replies_reply_dislikes_' + comment_id + '" class="replies_reply_dislikes" onclick="$(\'#reply-activity-item-' + action_id + '\').css(\'display\', \'none\');$(\'#dislike-reply-item-' + action_id + '\').css(\'display\', \'block\'); sm4.activity.reply_dislikes(\'' + action_id + '\',' + comment_id + ', 1)"><span class="ui-icon ui-icon-thumbs-down-alt">' + sm4.core.language.translate(1) + '</span></a>';
            } else {
                var likeCountHtml = '<span class="sep"> - </span><a href="javascript:void(0);" id="replies_reply_dislikes_' + comment_id + '" class="replies_reply_dislikes" onclick=""><span class="ui-icon ui-icon-thumbs-down-alt">' + sm4.core.language.translate(1) + '</span></a>';
            }
            
          } else {
              if(showDislikeUsers == 1) {  
                var likeCountHtml = '<a href="javascript:void(0);" id="replies_reply_dislikes_' + comment_id + '" class="replies_reply_dislikes" onclick="$(\'#reply-activity-item-' + action_id + '\').css(\'display\', \'none\');$(\'#dislike-reply-item-' + action_id + '\').css(\'display\', \'block\'); sm4.activity.reply_dislikes(\'' + action_id + '\',' + comment_id + ', 1)"><span>' + sm4.core.language.translate(1) + '</span></a>';
              } else {
                  var likeCountHtml = '<a href="javascript:void(0);" id="replies_reply_dislikes_' + comment_id + '" class="replies_reply_dislikes"><span>' + sm4.core.language.translate(1) + '</span></a>';
              }
          }
          
          likeCountHtml = sm4.core.mobiPageHTML(likeCountHtml);

          if($.type($('#reply-' + comment_id + ' .replies_reply_likes').get(0)) != 'undefined') {
            if(showLikeWithoutIconInReplies != 3) {       
               $('#reply-' + comment_id + ' .replies_reply_likes').after($(likeCountHtml));
            } else {
               $('#reply-' + comment_id + ' .reply_dislikes').before($(likeCountHtml)); 
            }
          } else { 
            if(showLikeWithoutIconInReplies == 3) {    
              $('#reply-' + comment_id + ' .reply_dislikes').before($(likeCountHtml));
            } else {
              $('#reply-' + comment_id + ' .reply_dislikes').after($(likeCountHtml));  
            }
          }
        }

      }

      sm4.core.dloader.refreshPage();

    },
    comment: function(action_id, body, photo_id, type, tag) {
        
      if (body.trim() == '' && photo_id  == 0 && tag == 0) {
        return;
      }
      $.mobile.loading().loader("show");
      var ajax = sm4.core.request.send({
        type: "POST",
        dataType: "json",
        url: sm4.core.baseUrl + 'advancedactivity/index/comment',
        data: {
          'action_id': action_id,
          'body': body,
          'photo_id': photo_id,
          'toValues' : tag,
          'type': type,
          'subject': $.mobile.activePage.advfeed_array.subject_guid,
          'format': 'json'
        },
        success: function(responseJSON, textStatus, xhr) {
          var li = $('<li />', {
            'id': 'comment-' + responseJSON.comment_id,
            'html': sm4.core.mobiPageHTML(responseJSON.body)

          }).inject($('#showhide-comments-' + action_id).find('ul.viewcomment'));
          if ($('#showhide-comments-' + action_id).find('ul').find('li div.no-comments')) {
            $('#showhide-comments-' + action_id).find('ul').find('li div.no-comments').parent('li').remove();
          }
          $('#hide-commentform-' + action_id).css('display', 'none');
          $('#hide-commentform-' + action_id).next().css('display', 'block');
          $('#activity-comment-body-' + action_id).val('');
          sm4.core.runonce.trigger();
          sm4.core.dloader.refreshPage();
          $('.sm-ui-popup-container').animate({
            scrollTop: 2000
          }, 0);
          $.mobile.loading().loader("hide");
          if(sm4.activity.composer && photo_id)
          sm4.activity.composer.deactivate();
          $('.cm-icon-emoticons').removeClass('active');
          
          if($.type('#adv_comment_post_container_tagging') != 'undefined')
          $('#adv_comment_post_container_tagging').css('display', 'none');
          if($.type('#ui-post-comment-header-addpeople') != 'undefined')
          $('#ui-post-comment-header-addpeople').css('display', 'none');
          if(tag) {
                $('#toCommentValues', sm4.activity.composer.content).val('');
                $('#toCommentValues-temp', sm4.activity.composer.content).val('');
                $('#toCommentValuesdone-wrapper', sm4.activity.composer.content).find('span').remove();
                $('#toCommentValues-temp-wrapper', sm4.activity.composer.content).find('span').remove();
          }
          
          sm4.activity.resetAdvFeed();
        }
      }
      );
    },
    toggleReplyArea: function(self, action_id) {

      $(self).css('display', 'none');
      $(self).prev().css('display', 'block');
      var form = $(self).prev().find('form');
      form.css('display', 'block');
      form.find('textarea').attr('placeholder', sm4.core.language.translate('Write a reply...'))
      form.find('textarea').focus();
    },
    toggleEditArea: function(self, action_id) {
      $(self).css('display', 'none');
      $(self).prev().css('display', 'block');
      var form = $(self).prev().find('form');
      form.css('display', 'block');
      //form.find('textarea').attr('placeholder', sm4.core.language.translate('Write a reply...'))
      form.find('textarea').focus();
    },
    attachEdit: function(formElement, comment_id, action_id, perform) {
      var bind = this;
      if($('.cm-icon-emoticons'))
      $('.cm-icon-emoticons').removeClass('active');
      formElement.attr('data-ajax', 'false');
      formElement.css('display', 'block');
      var photo_id, type;
      if($.type($("[name='attachment[photo_id]']", formElement).val()) == 'undefined') {
          photo_id = 0;
      } else {
          photo_id = $("[name='attachment[photo_id]']", formElement).val();
      }
      
      if($.type($("[name='attachment[type]']", formElement).val()) == 'undefined') {
          type = 0;
      } else {
          type = $("[name='attachment[type]']", formElement).val();
      }
      var tagfriends = 0;
      if($.type($('#toEditValues').val()) != 'undefined' && $('#toEditValues').val()) {
          tagfriends = $('#toEditValues').val();
      }
      bind.edit(comment_id, $("[name='body']", formElement).val(), action_id, photo_id, type, tagfriends, perform);
      $("[name='body']", formElement).val('');
     // $("[name='body']", formElement).attr('placeholder', sm4.core.language.translate('Write a reply...'));

    },
    edit: function(comment_id, body, action_id, photo_id, type, tag, perform) {
      if (body.trim() == '' && photo_id == 0 && tag == 0)  {
        return;
      }
      $.mobile.loading().loader("show");
      var ajax = sm4.core.request.send({
        type: "POST",
        dataType: "json",
        url: sm4.core.baseUrl + 'advancedactivity/index/edit',
        data: {
          'comment_id': comment_id,
          'action_id': action_id,
          'body': body,
          'photo_id': photo_id,
	  'toValues': tag,
          'type': type,
          'subject': $.mobile.activePage.advfeed_array.subject_guid,
          'format': 'json',
          'perform': perform
        },
        success: function(responseJSON, textStatus, xhr) {
          $('#editpopup_' + comment_id).remove();
          if(responseJSON.perform == 'comment-edit') {
            $('#comment-'+comment_id).find('.comments_info').find('.comments_body').html(responseJSON.body);
          } else {
            $('#reply-'+comment_id).find('.comments_info').find('.comments_body').html(responseJSON.body);
          }
          $('#comment-option-'+ responseJSON.comment_id).css('display', 'none');
          $('#comment_information-'+ responseJSON.comment_id).css('display', 'block');
          sm4.core.runonce.trigger();
          sm4.core.dloader.refreshPage();
          $('.sm-ui-popup-container').animate({
            scrollTop: 2000
          }, 0);
          $.mobile.loading().loader("hide");
          if(sm4.activity.composer && photo_id)
          sm4.activity.composer.deactivate();
          $('.cm-icon-emoticons').removeClass('active');
          
          if(tag) {
                $('#toEditValues', sm4.activity.composer.content).val('');
                $('#toEditValues-temp', sm4.activity.composer.content).val('');
                $('#toEditValuesdone-wrapper', sm4.activity.composer.content).find('span').remove();
                $('#toEditValuesdone-temp-wrapper', sm4.activity.composer.content).find('span').remove(); 
          }
          sm4.activity.resetAdvFeed();
        }
      }
      );
    }, 
    attachReply: function(formElement, comment_id, action_id) {
      var bind = this;
      if($('.cm-icon-emoticons'))
      $('.cm-icon-emoticons').removeClass('active');
      formElement.attr('data-ajax', 'false');
      formElement.css('display', 'block');
      var photo_id, type;
      
      if($.type($("[name='attachment[photo_id]']", formElement).val()) == 'undefined') {
          photo_id = 0;
      } else {
          photo_id = $("[name='attachment[photo_id]']", formElement).val();
      }
      if($.type($("[name='attachment[type]']", formElement).val()) == 'undefined') {
          type = 0;
      } else {
          type = $("[name='attachment[type]']", formElement).val();
      }
     
      var tagfriends = 0;
      if($.type($('#toReplyValues_'+$("[name='comment_id']", formElement).val())) != 'undefined' && $('#toReplyValues_'+$("[name='comment_id']", formElement).val()).val()) {
          tagfriends = $('#toReplyValues_'+$("[name='comment_id']", formElement).val()).val();
      }
      bind.reply(comment_id, $("[name='body']", formElement).val(), action_id, photo_id, type, tagfriends);
      $("[name='body']", formElement).val('');
      $("[name='body']", formElement).attr('placeholder', sm4.core.language.translate('Write a reply...'));

    },
    reply: function(comment_id, body, action_id, photo_id, type, tag) {
      if (body.trim() == '' && photo_id == 0 && tag == 0)  {
        return;
      }
      $.mobile.loading().loader("show");
      var ajax = sm4.core.request.send({
        type: "POST",
        dataType: "json",
        url: sm4.core.baseUrl + 'advancedactivity/index/reply',
        data: {
          'comment_id': comment_id,
          'action_id': action_id,
          'body': body,
          'photo_id': photo_id,
	  'toValues': tag,
          'type': type,
          'subject': $.mobile.activePage.advfeed_array.subject_guid,
          'format': 'json'
        },
        success: function(responseJSON, textStatus, xhr) {
          var li = $('<li />', {
            'id': 'reply-' + responseJSON.comment_id,
            'html': sm4.core.mobiPageHTML(responseJSON.body)
          }).inject($('#showhide-comments-' + comment_id).find('ul.viewreply'));
          if ($('#showhide-comments-' + comment_id).find('ul').find('li div.no-comments')) {
            $('#showhide-comments-' + comment_id).find('ul').find('li div.no-comments').parent('li').remove();
          }
          $('#hide-commentform-' + comment_id).css('display', 'none');
          $('#hide-commentform-' + comment_id).next().css('display', 'block');
          $('#activity-reply-body-' + comment_id).val('');
          sm4.core.runonce.trigger();
          sm4.core.dloader.refreshPage();
          $('.sm-ui-popup-container').animate({
            scrollTop: 2000
          }, 0);
          $.mobile.loading().loader("hide");
          if(sm4.activity.composer && photo_id)
          sm4.activity.composer.deactivate();
          $('.cm-icon-emoticons').removeClass('active');
          
          if($.type('#adv_reply_post_container_tagging_'+comment_id) != 'undefined')
           $('#adv_reply_post_container_tagging_'+comment_id).css('display', 'none');
                
          if($.type('#ui-post-reply-header-addpeople_'+comment_id) != 'undefined')
          $('#ui-post-reply-header-addpeople_'+comment_id).css('display', 'none');
      
          if(tag) {
                $('#toReplyValues_' + comment_id, sm4.activity.composer.content).val('');
                $('#toReplyValues-temp_' + comment_id, sm4.activity.composer.content).val('');
                $('#toReplyValuesdone-wrapper_' + comment_id, sm4.activity.composer.content).find('span').remove();
                $('#toReplyValuesdone-temp-wrapper_' + comment_id, sm4.activity.composer.content).find('span').remove(); 
          }
          sm4.activity.resetAdvFeed();
        }
      }
      );
    },  
    replyremove: function(e, comment_id, action_id) {
      deleteReplyActive = true;
      if ($.type(e) != 'undefined' && $.type($(e) == 'object')) {
        feedElement = $(e); 
        $.mobile.activePage.removeClass('dnone');
        $('.ui-popup-active').removeClass('dnone');
        var replyinfo = feedElement.data('message').split('-');
        
        if (replyinfo[0] == 0) {
          $.mobile.activePage.find('#popupDialog').popup("open");
          $.mobile.activePage.find('#popupDialog').parent().css('z-index', '11000')
          $.mobile.activePage.find('#popupDialog').popup("open");
        }
        else {
          $.mobile.activePage.find('#popupDialog-Reply').parent().css('z-index', '11000');
          $.mobile.activePage.find('#popupDialog-Reply').popup({history: false});
          $.mobile.activePage.find('#popupDialog-Reply').popup("open");

        }

      }
      else { 
        
        var replyinfo = feedElement.data('message').split('-');

        if (replyinfo[0] == 0) {
          $('#activity-item-' + replyinfo[1]).remove();
        } else {
          $('#reply-' + replyinfo[0]).remove();
          try {
            var replyCount = $('#count-feedreplies')[0].innerHTML;
            var m = replyCount.match(/\d+/);
            var newCount = (parseInt(m[0]) != 'NaN' && parseInt(m[0]) > 1 ? parseInt(m[0]) - 1 : 0);

            replyCount = replyCount.replace(m[0], newCount);
            $('#count-feedreplies')[0].innerHTML = replyCount;

            //DECREASE THE COMMENTS COUNT FROM THE MAIN WINDOW IF USER DELETE COMMENT FROM POPUP WINDOW. 

            if (parseInt(newCount) > parseInt(0)) {

              $.mobile.activePage.find('#activity-item-' + replyinfo[1]).find('.feed_comments span').html(replyCount)
            }
            else {
              $.mobile.activePage.find('#activity-item-' + replyinfo[1]).find('.feed_comments').prev('span').remove();
              $.mobile.activePage.find('#activity-item-' + replyinfo[1]).find('.feed_comments').remove();
            }
           } catch (e) {
          }
        }
        $.post(feedElement.data('url'));
      }
    },
    reply_likes: function(action_id, comment_id, page) {
      if (oldReplyLikeID != action_id) {
        $('#like-reply-item-' + action_id).html("<div class='ps_loading sm-ui-popup-loading'></div>");
        $('#like-reply-item-' + action_id).css('display', 'block')
      } else {
        return;
      }
      oldReplyLikeID = comment_id;
      
      $.ajax({
        type: "POST",
        dataType: "html",
        url: sm4.core.baseUrl + 'advancedactivity/index/get-reply-likes',
        data: {
          'action_id': action_id,
          'comment_id': comment_id,
          'page': page,
          'format': 'html',
          'showLikeWithoutIconInReplies': showLikeWithoutIconInReplies
        },
        success: function(responseHTML, textStatus, xhr) {

          $('#like_reply_viewmore_link').css('display', 'none');
          $(document).data('loaded', true);
          if (page == 1)
            $('#like-reply-item-' + action_id).html(sm4.core.mobiPageHTML(responseHTML));
          if (page > 1)
            $('#likereplymembers_ul').append(sm4.core.mobiPageHTML(responseHTML));
          sm4.core.dloader.refreshPage();
          sm4.core.runonce.trigger();
          $('#like-reply-item-' + action_id).css('display', 'block');
        }
      });
    },
    reply_dislikes: function(action_id, comment_id, page) {
        
      if (oldReplyDisLikeID != action_id) {
        $('#dislike-reply-item-' + action_id).html("<div class='ps_loading sm-ui-popup-loading'></div>");
        $('#dislike-reply-item-' + action_id).css('display', 'block')
      } else {
        return;
      }
      oldReplyDisLikeID = comment_id;
      
      $.ajax({
        type: "POST",
        dataType: "html",
        url: sm4.core.baseUrl + 'advancedactivity/index/get-reply-dislikes',
        data: {
          'action_id': action_id,
          'comment_id': comment_id,
          'page': page,
          'format': 'html',
          'showLikeWithoutIconInReplies': showLikeWithoutIconInReplies
        },
        success: function(responseHTML, textStatus, xhr) {

          $('#dislike_reply_viewmore_link').css('display', 'none');
          $(document).data('loaded', true);
          if (page == 1)
            $('#dislike-reply-item-' + action_id).html(sm4.core.mobiPageHTML(responseHTML));
          if (page > 1)
            $('#dislikereplymembers_ul').append(sm4.core.mobiPageHTML(responseHTML));
          sm4.core.dloader.refreshPage();
          sm4.core.runonce.trigger();
          $('#dislike-reply-item-' + action_id).css('display', 'block');
        }
      });
    },
    getOlderComments: function(self, type, id, page, action_id) {
      $(self).html('<i class="icon-spinner icon-spin ui-icon"></i>');
      sm4.core.request.send({
        url: sm4.core.baseUrl + 'advancedactivity/index/list',
        type: "GET",
        dataType: "html",
        data: {
          format: 'html',
          type: type,
          id: id,
          subject: $.mobile.activePage.advfeed_array.subject_guid,
          page: page,
          action_id: action_id
        },
        success: function(responseHTML, textStatus, xhr) {
          var prev = $(self).prev();
          if ($.type(prev.get(0)) == 'undefined') {
            var next = $(self).next();
            next.before(sm4.core.mobiPageHTML(responseHTML));
          }
          else {
            prev.after(sm4.core.mobiPageHTML(responseHTML));
          }
          $(self).remove();

          sm4.core.runonce.trigger();
          sm4.core.dloader.refreshPage();
        }
      });

    },
    getOlderReplies: function(self, type, id, page, action_id, comment_id) {
      $(self).html('<i class="icon-spinner icon-spin ui-icon"></i>');
      sm4.core.request.send({
        url: sm4.core.baseUrl + 'advancedactivity/index/list-reply',
        type: "GET",
        dataType: "html",
        data: {
          format: 'html',
          type: type,
          id: id,
          subject: $.mobile.activePage.advfeed_array.subject_guid,
          page: page,
          action_id: action_id,
          comment_id: comment_id
        },
        success: function(responseHTML, textStatus, xhr) {
          var prev = $(self).prev();
          if ($.type(prev.get(0)) == 'undefined') {
            var next = $(self).next();
            next.before(sm4.core.mobiPageHTML(responseHTML));
          }
          else {
            prev.after(sm4.core.mobiPageHTML(responseHTML));
          }
          $(self).remove();

          sm4.core.runonce.trigger();
          sm4.core.dloader.refreshPage();
        }
      });

    },
    attachComment: function(formElement) {
      var bind = this;
      if($('.cm-icon-emoticons'))
      $('.cm-icon-emoticons').removeClass('active');
      formElement.attr('data-ajax', 'false');
      formElement.css('display', 'block');
      var photo_id, type;
      
      if($.type($("[name='attachment[photo_id]']", formElement).val()) == 'undefined') {
          photo_id = 0;
      } else {
          photo_id = $("[name='attachment[photo_id]']", formElement).val();
      }
      if($.type($("[name='attachment[type]']", formElement).val()) == 'undefined') {
          type = 0;
      } else {
          type = $("[name='attachment[type]']", formElement).val();
      }
      var tagfriends = 0;
      if($.type($('#toCommentValues').val()) != 'undefined' && $('#toCommentValues').val()) {
          tagfriends = $('#toCommentValues').val();
      }
     
      bind.comment($("[name='action_id']", formElement).val(), $("[name='body']", formElement).val(), photo_id, type, tagfriends);
      $("[name='body']", formElement).val('');
      $("[name='body']", formElement).attr('placeholder', sm4.core.language.translate('Write a comment...'));

    },
    viewComments: function(action_id) {
      $.ajax({
        type: "POST",
        dataType: "json",
        url: sm4.core.baseUrl + 'activity/index/viewComment',
        data: {
          'action_id': action_id,
          'nolist': true,
          'format': 'json',
          'showLikeWithoutIcon': showLikeWithoutIcon
        },
        success: function(responseJSON, textStatus, xhr) {
          $(document).data('loaded', true);
          $('#activity-item-' + action_id).html(sm4.core.mobiPageHTML(responseJSON.body));
          sm4.core.dloader.refreshPage();
          sm4.core.runonce.trigger();
        }
      });
    },
    getLikeUsers: function(action_id, action, page) {
      if (activeRequest == false)
        activeRequest = true;
      else {
        $('#like_viewmore').css('display', 'none');
        return;
      }
      if ($('#like-activity-item-' + action_id).html() == '')
        $('#like-activity-item-' + action_id).html("<div class='ps_loading sm-ui-popup-loading'></div>");

      $.ajax({
        type: "POST",
        dataType: "html",
        url: sm4.core.baseUrl + 'advancedactivity/index/get-all-like-user',
        data: {
          'format': 'html',
          'action_id': action_id,
          'page': page,
          'showLikeWithoutIcon': showLikeWithoutIcon
        },
        success: function(responseHTML, textStatus, xhr) {
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
    getDisLikeUsers: function(action_id, action, page) {
      if (activeRequestDislike == false)
        activeRequestDislike = true;
      else {
        $('#dislike_viewmore').css('display', 'none');
        return;
      }
      
      if ($('#dislike-activity-item-' + action_id).html() == '')
        $('#dislike-activity-item-' + action_id).html("<div class='ps_loading sm-ui-popup-loading'></div>");

      $.ajax({
        type: "POST",
        dataType: "html",
        url: sm4.core.baseUrl + 'advancedactivity/index/get-all-dislike-user',
        data: {
          'format': 'html',
          'action_id': action_id,
          'page': page,
          'showLikeWithoutIcon': showLikeWithoutIcon
        },
        success: function(responseHTML, textStatus, xhr) {
          activeRequestDislike = false;
          $('#dislike_viewmore').css('display', 'none');
          $(document).data('loaded', true);

          if (page == 1)
            $('#dislike-activity-item-' + action_id).html(sm4.core.mobiPageHTML(responseHTML));
          if (page > 1)
            $('#dislikemembers_ul').append(sm4.core.mobiPageHTML(responseHTML));

          //   sm4.core.dloader.refreshPage();
          sm4.core.runonce.trigger();
        }
      });

    },    
    doOnScrollLoadActivity: function(feedtype) {
      feedtype = this.feedType;
      if ($.type($.mobile.activePage.advfeed_array) == 'undefined' || (sm4.core.isApp() && feedtype === 'sitefeed'))
        return;
      if ($.mobile.activePage.advfeed_array.next_id == 0 || $.mobile.activePage.advfeed_array.endOfFeed == true)
        return;
      if (($.mobile.activePage.advfeed_array.maxAutoScrollAAF == 0 || $.mobile.activePage.advfeed_array.countScrollAAFSocial < $.mobile.activePage.advfeed_array.maxAutoScrollAAF) && $.mobile.activePage.advfeed_array.autoScrollFeedAAFEnable && $.type($.mobile.activePage.find('#feed_viewmore-' + feedtype).get(0)) != 'undefined') {
        if ($.type($.mobile.activePage.find('#feed_viewmore-' + feedtype).get(0).offsetParent) != 'undefined') {
          var elementPostionY = $.mobile.activePage.find('#feed_viewmore-' + feedtype).get(0).offsetTop;
        } else {
          var elementPostionY = $.mobile.activePage.find('#feed_viewmore-' + feedtype).get(0).y;
        }

        if (elementPostionY <= $(window).scrollTop() + ($(window).height() + 200)) {
          this.activityViewMore(this.feedTabURL, feedtype);
        }
      }

    },
    doOnScrollLoadActivityLikes: function(action_id, action, page) {

      if (nextlikepage == 0) {
        window.onscroll = '';
        return;
      }
      if ($.type($('#feed_viewmore').get(0)) != 'undefined') {
        if ($.type($('#like_viewmore').get(0).offsetParent) != 'undefined') {
          var elementPostionY = $('#like_viewmore').get(0).offsetTop;
        } else {
          var elementPostionY = $$('#like_viewmore').get(0).y;
        }
        if (elementPostionY <= $(window).scrollTop() + ($(window).height() - 40)) {
          $('#like_viewmore').css('display', 'block');
          $('#like_viewmore').html('<i class="icon-spinner icon-spin ui-icon"></i>');
          this.getLikeUsers(action_id, action, page)
        }
      }


    },
    doOnScrollLoadActivityDisLikes: function(action_id, action, page) {

      if (nextdislikepage == 0) {
        window.onscroll = '';
        return;
      }
      if ($.type($('#feed_viewmore').get(0)) != 'undefined') {
        if ($.type($('#dislike_viewmore').get(0).offsetParent) != 'undefined') {
          var elementPostionY = $('#dislike_viewmore').get(0).offsetTop;
        } else {
          var elementPostionY = $$('#dislike_viewmore').get(0).y;
        }
        if (elementPostionY <= $(window).scrollTop() + ($(window).height() - 40)) {
          $('#dislike_viewmore').css('display', 'block');
          $('#dislike_viewmore').html('<i class="icon-spinner icon-spin ui-icon"></i>');
          this.getDisLikeUsers(action_id, action, page)
        }
      }

    },        
    addFriend: function(self) {
      if (!sm4.core.isApp()) {
        if ($.type(self) != 'undefined') {
          if ($(self).hasClass('userlink-add'))
            var popupid = '#popupDialog-AddFriend';
          else if ($(self).hasClass('userlink-remove'))
            var popupid = '#popupDialog-RemoveFriend';
          else
            var popupid = '#popupDialog-CancelFriend';
          $.mobile.activePage.find(popupid).parent().css('z-index', '11000')
          $.mobile.activePage.find(popupid).popup("open");
          smFriendAction = self;
          if ($.mobile.activePage.find(popupid).length > 0)
            return;
        } else {
          self = smFriendAction;
        }
      }
      if ($.type($(self).attr('data-href') != 'undefined' && $(self).attr('data-href') != ''))
        var postURL = $(self).attr('data-href');
      else
        var postURL = $(self).href;

      var container = $(self).parent();


      container.html('<i class="icon_loading"></i>');
      $.ajax({
        type: "POST",
        dataType: "json",
        url: postURL,
        data: {
          'format': 'json',
          'type': 'json'
        },
        success: function(responseJSON, textStatus, xhr) {
          container.html(sm4.core.mobiPageHTML(responseJSON.body));
          //if(sm4.core.isApp())
          sm4.core.showFlashMessage(responseJSON.message, 1000);
          sm4.core.dloader.refreshPage();
          sm4.core.runonce.trigger();
        }
      });
    },
    showOptions: function(formElement) {
      if (!sm4.core.isApp() && DetectIpad()) {
        $.mobile.activePage.find('#main-feed-' + formElement).css('display', 'none');
        $.mobile.activePage.find('#feed-options-' + formElement).css('display', 'block');
      }
      else {
        $.mobile.activePage.find('#main-feed-' + formElement).slideUp(500);
        $.mobile.activePage.find('#feed-options-' + formElement).slideDown(500);
      }

    },
    hideOptions: function(formElement) {

      if (!sm4.core.isApp() && DetectIpad()) {
        $.mobile.activePage.find('#feed-options-' + formElement).css('display', 'none');
        $.mobile.activePage.find('#main-feed-' + formElement).css('display', 'block');
      }
      else {
        $.mobile.activePage.find('#feed-options-' + formElement).slideUp(500);
        $.mobile.activePage.find('#main-feed-' + formElement).slideDown(500);
      }

    },
    showHideComments: function(formElement) {
      $.mobile.activePage.find('#showhide-comments-' + formElement).slideToggle();
    },
    notificationCountUpdate: function($page) {
      sm4.core.request.send({
        type: "GET",
        dataType: "json",
        url: sm4.core.baseUrl + 'activity/notifications/update-count',
        data: {
          format: 'json'
        },
        success: function(responseJSON, textStatus, xhr) {

          if ($page.find('.sm-mini-menu').length && $page.find('.sm-mini-menu').find('a:last-child').find('.count-bubble').length)
            $page.find('.sm-mini-menu').find('a[data-content="recent_activity"]').find('.count-bubble').remove();
          if ($page.find('.main-navigation').length && $page.find('.main-navigation').find('.core_main_update').length && $page.find('.main-navigation').find('.core_main_update').find('.count-bubble').length)
            $page.find('.main-navigation').find('.core_main_update').find('.count-bubble').remove();



          if (responseJSON.notificationCount) {
            if ($page.find('.sm-mini-menu').length)
              $page.find('.sm-mini-menu').find('a[data-content="recent_activity"]').append($('<span class="count-bubble" ></span>').html(responseJSON.notificationCount));

            if ($page.find('.main-navigation').length && $page.find('.main-navigation').find('.core_main_update').length) {
              $page.find('.main-navigation').find('.core_main_update').find('.ui-menu-icon').append($('<span class="count-bubble" ></span>').html(responseJSON.notificationCount));
            }

          }
        }
      }, {
        force: true
      });
    },
    requestCountUpdate: function($page) {
      sm4.core.request.send({
        type: "GET",
        dataType: "json",
        url: sm4.core.baseUrl + 'activity/notifications/update-count-request',
        data: {
          format: 'json'
        },
        success: function(responseJSON, textStatus, xhr) {

          if ($page.find('.sm-mini-menu').length && $page.find('.sm-mini-menu').find('a:first-child').find('.count-bubble').length)
            $page.find('.sm-mini-menu').find('a:first-child').find('.count-bubble').remove();

          if (responseJSON.requestCount) {
            if ($page.find('.sm-mini-menu').length)
              $page.find('.sm-mini-menu').find('a:first-child').append($('<span class="count-bubble" ></span>').html(responseJSON.requestCount));

          }
        }
      }, {
        force: true
      });
    },
    messageCountUpdate: function($page) {
      sm4.core.request.send({
        type: "GET",
        dataType: "json",
        url: sm4.core.baseUrl + 'activity/notifications/message-count-update',
        data: {
          format: 'json'
        },
        success: function(responseJSON, textStatus, xhr) {

          if ($page.find('.sm-mini-menu').length && $page.find('.sm-mini-menu').find('a:nth-child(2)').find('.count-bubble').length)
            $page.find('.sm-mini-menu').find('a:nth-child(2)').find('.count-bubble').remove();

          if (responseJSON.messageCount) {
            if ($page.find('.sm-mini-menu').length)
              $page.find('.sm-mini-menu').find('a:nth-child(2)').append($('<span class="count-bubble" ></span>').html(responseJSON.messageCount));

          }
        }
      }, {
        force: true
      });
    },
    hideNotifications: function(reset_text) {

      var ajax = sm4.core.request.send({
        dataType: "json",
        url: sm4.core.baseUrl + 'activity/notifications/hide'
      });
      if ($('#updates_toggle'))
        $('#updates_toggle').attr('html', reset_text).removeClass('.new_updates');

      if ($('.sm-mini-menu').length)
        $('.sm-mini-menu').find('a:last-child').find('.count-bubble').remove();

      if ($('.main-navigation').length && $('.main-navigation').find('.core_main_update').length && $('.main-navigation').find('.core_main_update').find('.count-bubble').length)
        $('.main-navigation').find('.core_main_update').find('.count-bubble').remove();

      if ($('#notifications_main')) {
        var notification_children = $('#notifications_main').children('li');
        notification_children.each(function(key, el) {
          $(el).attr('class', '');
        });
        $('#notifications_main').listview().listview('refresh');
        sm4.core.dloader.refreshPage();
      }

      if ($('#notifications_menu')) {
        var notification_children = $('#notifications_menu').children('li');
        notification_children.each(function(key, el) {
          $(el).attr('class', '');
        });
        $('#notifications_main').listview().listview('refresh');
        sm4.core.dloader.refreshPage();
      }

    },
    updateCommentable: function(action_id) {
      $.mobile.loading().loader("show");
      sm4.core.request.send({
        type: "GET",
        dataType: "json",
        url: sm4.core.baseUrl + 'advancedactivity/index/update-commentable',
        data: {
          format: 'json',
          action_id: action_id,
          subject: $.mobile.activePage.find('#subject').val()
        },
        success: function(responseJSON, textStatus, xhr) {
          $.mobile.loading().loader("hide");
          if (responseJSON.status)
            $('#activity-item-' + action_id).html(responseJSON.body);
          sm4.core.dloader.refreshPage();
          sm4.core.runonce.trigger();
          sm4.core.photoGallery.set($('#activity-item-' + action_id));

        }
      });
    },
    updateShareable: function(action_id) {
      $.mobile.loading().loader("show");
      sm4.core.request.send({
        type: "GET",
        dataType: "json",
        url: sm4.core.baseUrl + 'advancedactivity/index/update-shareable',
        data: {
          format: 'json',
          action_id: action_id,
          subject: $.mobile.activePage.advfeed_array.subject_guid
        },
        success: function(responseJSON, textStatus, xhr) {
          $.mobile.loading().loader("hide");
          if (responseJSON.status)
            $('#activity-item-' + action_id).html(responseJSON.body);
          sm4.core.dloader.refreshPage();
          sm4.core.runonce.trigger();
          sm4.core.photoGallery.set($('#activity-item-' + action_id));
        }
      });
    },
    updateSaveFeed: function(action_id) {
      $.mobile.loading().loader("show");
      sm4.core.request.send({
        type: "GET",
        dataType: "json",
        url: sm4.core.baseUrl + 'advancedactivity/index/update-save-feed',
        data: {
          format: 'json',
          action_id: action_id,
          subject: $.mobile.activePage.advfeed_array.subject_guid
        },
        success: function(responseJSON, textStatus, xhr) {
          $.mobile.loading().loader("hide");
          if (responseJSON.status)
            $('#activity-item-' + action_id).html(responseJSON.body);
          sm4.core.dloader.refreshPage();
          sm4.core.runonce.trigger();
          sm4.core.photoGallery.set($('#activity-item-' + action_id));
        }
      });
    },
    share: function() {

      currentactive_panel.removeClass('dnone')
      $.mobile.loading().loader("show");
      $.mobile.activePage.find('#showadvfeed').addClass('dblock');
      this.getForm().parent().css('display', 'none');
      var url = this.feedURL;
      if(url.indexOf('advancedactivity/index/')==0)
        url = sm4.core.baseUrl +url;
      $.ajax({
        type: "POST",
        dataType: "json",
        url: url,
        data: this.getForm().serialize() + '&is_ajax=1',
        success: function(responseJSON, textStatus, xhr) {

          var htmlBody;
          // Get response          
          if (responseJSON.feed_stream) { // HTML response
            if (responseJSON.feedtype == 'sitefeed')
              sm4.activity.activityUpdateHandler.updateOptions({
                last_id: responseJSON.last_id
              });
            if (responseJSON.feedtype == 'fbfeed' || responseJSON.feedtype == 'tweetfeed' || responseJSON.feedtype == 'linkedinfeed')
              htmlBody = $(responseJSON.feed_stream).html()
            else
              htmlBody = responseJSON.feed_stream;

            if ($.type($.mobile.activePage.find('#activity-feed-' + responseJSON.feedtype).get(0)) == 'undefined') {

              $.mobile.activePage.find('#showadvfeed-' + responseJSON.feedtype).html('');
              //CREATE UL ELEMENT...
              $('<ul />', {
                'id': 'activity-feed-' + responseJSON.feedtype,
                'class': 'feeds',
                'html': htmlBody

              }).inject($.mobile.activePage.find('#showadvfeed-' + responseJSON.feedtype));

            }
            else {
              $.mobile.activePage.find('#activity-feed-' + responseJSON.feedtype).prepend(htmlBody);
            }


            $.mobile.activePage.find('#activity-item-' + responseJSON.last_id).find('script').remove();
          }
          // Hide loading message
          $.mobile.loading().loader("hide");

          //RESET THE STATUS UPDATE BOX:
          this.resetAdvFeed();
          $('#activitypost-container-temp').remove();
//console.log($.mobile.activePage.find('#activitypost-container-temp'));

//return false;
          //$.mobile.activePage.find('#activitypost-container-temp').remove();
          sm4.core.runonce.trigger();
          sm4.core.dloader.refreshPage();
          sm4.core.photoGallery.set($.mobile.activePage.find('#activity-feed-' + responseJSON.feedtype).first());
          sm4.activity.activityUpdateHandler.scrollRefresh();
//          setTimeout(function(){
//            sm4.activity.activityUpdateHandler.scrollToElement('#aaf_feed_update_loading');
//          },200);
        }.bind(this)
      });

      return false;
    },
    resetAdvFeed: function() {
      $('#feed-update').css('display', 'none');
      $('#activity_body').val('');
      $('#composer-options').css('display', 'block');
      var el = $('#adv_post_container_tagging').get(0);
      if ($.type(el) != 'undefined' && el.style.display == 'block') {
        $('#toValues-wrapper').children('span.tag').remove();
        $('#toValues').val('');
        el.style.display = 'none';
      }
      $('#compose-tray').remove();
      if ($.type(self) != 'undefined' && self != false) {
        self.reset();
        self.composePlugin = false;
        if ($.type(self.composePlugin.active) != 'undefined') {
          self.composePlugin.active = false;
        }
        self.active = false;
      }

      //RESET THE CHECKIN..
      if ($.type(sm4.activity.composer.checkin.self) == 'object') {
        sm4.activity.composer.checkin.self.reset();
        sm4.activity.composer.checkin.active = false;
        sm4.activity.composer.checkin.location = '';
      }

      //RESET THE ADD FRIENDS..
      if ($.type(sm4.activity.composer.addpeople.self) == 'object') {
        sm4.activity.composer.addpeople.self.reset();
        sm4.activity.composer.addpeople.active = false;
      }
      sm4.activity.options.allowEmptyWithoutAttachment = false;
    },
    getTabBaseContentFeed: function(tabinfo, feedtype) {
      if ($.type(tabinfo) == 'undefined') {
        tabinfo = 'all-0';
      }
      if ($.type(feedtype) == 'undefined') {
        feedtype = 'sitefeed';
      }
      var tabinfo = tabinfo.split('-');
      $.mobile.activePage.find('#feed_viewmore-sitefeed').css('display', 'none');
      $.mobile.activePage.find('#feed_loading-sitefeed').css('display', 'none');
      setTimeout(function() {
        $.mobile.loading().loader("show")
      }, 150);
      if($.type($.mobile.activePage.advfeed_array) == 'undefined') {
          $.mobile.activePage.advfeed_array={'subject_guid':''};
      }
      $.ajax({
        type: "GET",
        dataType: "html",
        url: sm4.core.baseUrl + 'widget/index/name/sitemobile.sitemobile-advfeed',
        data: {
          'actionFilter': tabinfo[0],
          'list_id': tabinfo[1],
          'feedOnly': true,
          'nolayout': true,
          'isFromTab': true,
          'subject': $.mobile.activePage.advfeed_array.subject_guid,
          'format': 'html',
          'sitemobileadvfeed_length': $.mobile.activePage.advfeed_array.sitemobileadvfeed_length,
          'sitemobileadvfeed_scroll_autoload': $.mobile.activePage.advfeed_array.sitemobileadvfeed_scroll_autoload
        },
        success: function(responseHTML, textStatus, xhr) {
          var $html = $("<div></div>");
          $html.get(0).innerHTML = responseHTML;
          var tempLayout = $html.find('.layout_middle');
          if (tempLayout.length) {
            responseHTML = tempLayout.html();
          }
          $.mobile.activePage.find('#feed-update').css('display', 'none');
          $(document).data('loaded', true);
          $.mobile.activePage.find('#activity-feed-' + feedtype).html(responseHTML);

          sm4.core.runonce.trigger();
          sm4.activity.setViewMore(feedtype);
          sm4.core.dloader.refreshPage();
          if (tabinfo[0] == 'photo')
            sm4.core.photoGallery.set($.mobile.activePage.find('#activity-feed-' + feedtype));
          $.mobile.activePage.find('#activity-feed-' + feedtype).children('script').first().remove();

          setTimeout(function() {
            $.mobile.loading().loader("hide")
          }, 150);
          sm4.activity.activityUpdateHandler.scrollRefresh();
          //          $(this).delay(400).queue(function(){
          //            if ($.type($.mobile.activePage.advfeed_array) != 'undefined') {              
          //              sm4.activity.advfeed_array[this.getActivePageID()] = $.mobile.activePage.advfeed_array;          
          //
          //            }
          //            sm4.activity.setOnScrollLoadActivity(); 
          //            $(this).clearQueue();
          //          });
        }
      });
    },
    refreshfeed: function() {
      //GET THE ACTIVE FILTER AND REFRESH THAT.
      var activeFilter = $.mobile.activePage.find('.aaf_tabs_feed').find('.aaf_tabs_apps_feed');
      this.getTabBaseContentFeed(activeFilter.val(), 'sitefeed');

    },
    setViewMore: function(feedtype) {

      if ($.mobile.activePage.advfeed_array.next_id > 0 && $.mobile.activePage.advfeed_array.endOfFeed == false) {
        $.mobile.activePage.find('#feed_viewmore-' + feedtype).css('display', '');
        $.mobile.activePage.find('#feed_loading-' + feedtype).css('display', 'none');
        $.mobile.activePage.find('#feed_no_more-' + feedtype).css('display', 'none');
        $.mobile.activePage.find('#feed_viewmore_link-' + feedtype).unbind('click').bind('click', function(event) {
          event.preventDefault();
          this.activityViewMore(this.feedTabURL, feedtype);
        }.bind(this));
        this.setOnScrollLoadActivity(feedtype);
      } else {
        $.mobile.activePage.find('#feed_viewmore-' + feedtype).css('display', 'none');
        $.mobile.activePage.find('#feed_loading-' + feedtype).css('display', 'none');
        if ($.mobile.activePage.advfeed_array.next_id > 0 || $.mobile.activePage.advfeed_array.endOfFeed)
          $.mobile.activePage.find('#feed_no_more-' + feedtype).css('display', 'block');
      }
    },
    setOnScrollLoadActivity: function(feedtype) {
      if ($.type(feedtype) == 'undefined' && $.type(this.advfeed_array[$.mobile.activePage.attr('id')]) != 'undefined')
        feedtype = this.advfeed_array[$.mobile.activePage.attr('id')];
      else if ($.type(feedtype) == 'undefined')
        feedtype = 'sitefeed';

      if ($.type($.mobile.activePage.advfeed_array) == 'undefined' && $.type(this.advfeed_array[this.getActivePageID()]) != 'undefined') {

        $.mobile.activePage.advfeed_array = this.advfeed_array[this.getActivePageID()];

      }


      if ($.type($.mobile.activePage.advfeed_array) != 'undefined' && !(sm4.core.isApp() && feedtype === 'sitefeed')) {

        if ($.mobile.activePage.advfeed_array.next_id > 0 && $.mobile.activePage.advfeed_array.endOfFeed == false) {
          if (parseInt($.mobile.activePage.advfeed_array.autoScrollFeedAAFEnable))
            window.onscroll = this.doOnScrollLoadActivity.bind(this);
          else
            window.onscroll = '';
          $.mobile.activePage.find('#feed_viewmore_link-' + feedtype).unbind('click').bind('click', function(event) {
            event.preventDefault();
            this.activityViewMore(this.feedTabURL, feedtype);
          }.bind(this));
        } else if ($.mobile.activePage.advfeed_array.countScrollAAFSocial > 0 && $.mobile.activePage.advfeed_array.endOfFeed == true) {
          window.onscroll = "";
          $.mobile.activePage.find('#feed_viewmore-' + feedtype).css('display', 'none');
          $.mobile.activePage.find('#feed_loading-' + feedtype).css('display', 'none');
          $.mobile.activePage.find('#feed_no_more-' + feedtype).css('display', 'block');

        }
        else {
          if ($.mobile.activePage.advfeed_array.autoScrollFeedAAFEnable)
            window.onscroll = "";
          $.mobile.activePage.find('#feed_viewmore-' + feedtype).css('display', 'none');
          $.mobile.activePage.find('#feed_loading-' + feedtype).css('display', 'none');

        }
      }

    },

    openPopup: function(Url, popupid, action_id) {

      parentScrollTop = $(window).scrollTop();
      if (!document.location.search.length) {
        //window.location.hash='#&ui-state=dialogcomment';
      }
      $.mobile.activePage.popupid = popupid;
      $('.ui-page-active').addClass('dnone');     
      if ($.type($('#' + popupid).get(0)) != 'undefined')
        $('#' + popupid).remove();

      if(enabledCommentClass == 1) {
         if (sm4.activity.preloadedCommentString != '') {
        var $popup = $('<div id= "' + popupid + '" class="ps-popup-wapper sm-ui-popup ui-body-c"  style="display:block">' + "<div class='ps-close-popup'></div><div class='ps-carousel-comments sm-ui-popup' >" + sm4.activity.preloadedCommentString + "</div></div>" + '</div>');
        $('body').append($popup);
        sm4.core.runonce.trigger();
        return;
      }
      if (!action_id || $.type($('#preloaded-comments-' + action_id).get(0)) == 'undefined')
        var $popup = $('<div id= "' + popupid + '" class="sm-ui-popup ui-body-c"  style="display:block">' + "<div class='ps-close-popup'></div><div class='ps-carousel-comments sm-ui-popup' ><div class='ps_loading sm-ui-popup-loading'></div> </div></div>" + '</div>'); 
      } else {
          if (sm4.activity.preloadedCommentString != '') {
        var $popup = $('<div id= "' + popupid + '" class="ps-popup-wapper sm-ui-popup ui-body-c"  style="display:block">' + "<div class='ps-close-popup'></div><div class='ps-carousel-comments sm-ui-popup' >" + sm4.activity.preloadedCommentString + "</div></div>" + '</div>');
        $('body').append($popup);
        sm4.core.runonce.trigger();
        return;
      }
      if (!action_id || $.type($('#preloaded-comments-' + action_id).get(0)) == 'undefined')
        var $popup = $('<div id= "' + popupid + '" class="sm-ui-popup ui-body-c"  style="display:block">' + "<div class='ps-close-popup'></div><div class='ps-carousel-comments sm-ui-popup' ><div class='ps_loading sm-ui-popup-loading'></div> </div></div>" + '</div>')
      }

      $('body').append($popup);
      $('#preloaded-comments-' + action_id).html('');
      sm4.core.runonce.trigger();
      $.ajax({
        type: "GET",
        dataType: "html",
        url: Url,
        data: {
          'format': 'html',
          'popupid': ''

        },
        success: function(responseHTML, textStatus, xhr) {

          if ($.type($('#' + popupid).find('#showhide-comments-' + action_id).get(0)) != 'undefined')
            $('#' + popupid).find('#showhide-comments-' + action_id).html(sm4.core.mobiPageHTML(responseHTML))
          else
            $('#' + popupid).html(sm4.core.mobiPageHTML(responseHTML));
          sm4.core.runonce.trigger();

        }
      });

    },
    openReplyPopup: function(Url, popupid, action_id) {

      parentScrollTop = $(window).scrollTop();
      if (!document.location.search.length) {
        //window.location.hash='#&ui-state=dialogcomment';
      }
      $.mobile.activePage.popupid = popupid;
      $('.ui-page-active').addClass('dnone');     
      if ($.type($('#' + popupid).get(0)) != 'undefined')
        $('#' + popupid).remove();

      if(enabledCommentClass == 1) { 
        if (sm4.activity.preloadedReplyString != '') {
          var $popup = $('<div id= "' + popupid + '" class="ps-popup-wapper sm-ui-popup ui-body-c"  style="display:block">' + "<div class='ps-close-popup'></div><div class='ps-carousel-comments sm-ui-popup' >" + sm4.activity.preloadedReplyString + "</div></div>" + '</div>');
          $('body').append($popup);
          sm4.core.runonce.trigger();
          return;
        }
        if (!action_id || $.type($('#preloaded-comments-' + action_id).get(0)) == 'undefined')
          var $popup = $('<div id= "' + popupid + '" class="ps-popup-wapper sm-ui-popup ui-body-c"  style="display:block">' + "<div class='ps-close-popup'></div><div class='ps-carousel-comments sm-ui-popup' ><div class='ps_loading sm-ui-popup-loading'></div> </div></div>" + '</div>');
      } else {
          if (sm4.activity.preloadedReplyString != '') {
          var $popup = $('<div id= "' + popupid + '" class="sm-ui-popup ui-body-c"  style="display:block">' + "<div class='ps-close-popup'></div><div class='ps-carousel-comments sm-ui-popup' >" + sm4.activity.preloadedReplyString + "</div></div>" + '</div>');
          $('body').append($popup);
          sm4.core.runonce.trigger();
          return;
        }
        if (!action_id || $.type($('#preloaded-comments-' + action_id).get(0)) == 'undefined')
          var $popup = $('<div id= "' + popupid + '" class="sm-ui-popup ui-body-c"  style="display:block">' + "<div class='ps-close-popup'></div><div class='ps-carousel-comments sm-ui-popup' ><div class='ps_loading sm-ui-popup-loading'></div> </div></div>" + '</div>');
      }

      $('body').append($popup);
      $('#preloaded-comments-' + action_id).html('');
      sm4.core.runonce.trigger();
      $.ajax({
        type: "GET",
        dataType: "html",
        url: Url,
        data: {
          'format': 'html',
          'popupid': ''

        },
        success: function(responseHTML, textStatus, xhr) {

          if ($.type($('#' + popupid).find('#showhide-comments-' + action_id).get(0)) != 'undefined')
            $('#' + popupid).find('#showhide-comments-' + action_id).html(sm4.core.mobiPageHTML(responseHTML))
          else
            $('#' + popupid).html(sm4.core.mobiPageHTML(responseHTML));
          sm4.core.runonce.trigger();

        }
      });

    },
    resizePopup: function() {
      if ($(window).width() > 400)
        var width = 400;
      else
        var width = $(window).width();
      var popupid = 'feedsharepopup';

      if ($.type($('#' + popupid).find('#feedshare').get(0)) != 'undefined') {
        $('#' + popupid).popup().parent().css({
          'width': (width - 20),
          'height': ($('#' + popupid).find('#feedshare').height())
        })
      }
      else {
        $('#' + popupid).popup().parent().css({
          'width': (width - 20),
          'height': ($(window).height() - 10)
        });
      }
      //NOW FIND THE HEIGHT OF COMMENT BOX.
      if ($('#' + popupid).find('.sm-comments-post-comment').css('display') == 'block') {
        var commentform_height = $('#' + popupid).find('.sm-comments-post-comment').outerHeight();
      } else {
        var commentform_height = $('#' + popupid).find('.sm-comments-post-comment-form').outerHeight();
      }
      $('#' + popupid).find('.comments').css({
        'height': ($('#' + popupid).popup().parent().height() - (parseInt($('#' + popupid).find('.sm-comments-top').height()) + parseInt(commentform_height)))
      });

    },
    closePopup: function(el) {
      $('.ui-page-active').removeClass('dnone');
      $(el).closest('.sm-ui-popup').remove();
      $(window).scrollTop(parentScrollTop);
    },
    feedShare: function(self) {
      $('#feedshare').css('display', 'none');
      $('#feedsharepopup').append($('<div class="sm-ui-popup-loading"></div>')).trigger('create');
      $.ajax({
        type: "POST",
        dataType: "json",
        url: self.attr('action') + '?' + self.serialize(),
        data: {
          'format': 'json'
        },
        success: function(responseJSON, textStatus, xhr) {
          $('#feedsharepopup').remove();
          $(window).scrollTop(parentScrollTop)

        }
      });


    },
    setPhotoScroll: function(counter) {
      //      $('body').ready(function() {
      setTimeout(function() {
        if ($('.feed_attachment_photo').length < 1) {
          if (counter < 4) {
            counter++
            sm4.activity.setPhotoScroll(counter);
          }
          return;
        }
        var photoWidth = 300, photoHeight = 200, imageWidth = photoWidth - 10;
        if (photoWidth > $('body').width()) {
          photoWidth = $('body').width();
        }
        imageWidth = photoWidth - 1;
        if (photoHeight > $('body').height()) {
          photoHeight = $('body').height();
        }

        $('.feed_attachment_photo').each(function() {
          $(this).css('width', imageWidth + 'px');
          $(this).css('height', photoHeight + 'px');
          $(this).closest('.feed_item_attachments').css('height', photoHeight + 'px');
          $(this).closest('.feed_item_attachments_wapper').addClass('feed_item_scroll_wapper');

        });
        $('.feed_item_scroll_wapper').not('.scrollerH').each(function() {
          var width = 0;
          $(this).find('.feed_attachment_photo').each(function() {
            width = width + ($(this).outerWidth() + 4);
          });
          $(this).find('.feed_item_attachments').css('width', width + 'px');
          if ($(this).find('.feed_attachment_photo').length > 1) {
            var $this = $(this)[0];
            $(this).addClass('.iscroll_wapper');
            setTimeout(function() {
              new IScroll($this, {
                scrollX: true,
                scrollY: false,
                momentum: false,
                snap: true,
                snapSpeed: 400,
                keyBindings: true,
                click: true
              });
            }, 500);

          }
          $(this).addClass('scrollerH')
        });
      }, 100);
      //   });
    },
    makeFeedOptions: function(feedtype, optionparams, attachmentparmas) {


      this.feedType = feedtype;
      if ($.type($.mobile.activePage) != 'undefined') {

        this.advfeed_array[$.mobile.activePage.attr('id') + '_attachmentURL'] = attachmentparmas;
      }

      $.mobile.activePage.advfeed_array = optionparams;
      this.advfeed_array[$.mobile.activePage.attr('id')] = feedtype;
      this.advfeed_array[this.getActivePageID()] = $.mobile.activePage.advfeed_array;

      this.setOnScrollLoadActivity(feedtype);
      statusHtml = '';
      if ($.type($.mobile.activePage) == 'undefined' || $.type($.mobile.activePage.advfeed_array) == 'undefined' || $.type(this.advfeed_array[this.getActivePageID()]) == 'undefined') {

        var currentScrollCount = 0;
        $.mobile.activePage.advfeed_array.countScrollAAFSocial = 0;

      } else {

        if ($.type($.mobile.activePage.advfeed_array) != 'undefined' && $.type($.mobile.activePage.advfeed_array.countScrollAAFSocial) != 'undefined')
          $.mobile.activePage.advfeed_array.current_id = $.mobile.activePage.attr('id');

        $.mobile.activePage.advfeed_array.countScrollAAFSocial = ++currentScrollCount;
      }

      if ($.mobile.activePage.advfeed_array.next_id > 0 && $.mobile.activePage.advfeed_array.endOfFeed == false) {
        $.mobile.activePage.find('#feed_viewmore-' + feedtype).css('display', 'block');
        $.mobile.activePage.find('#feed_loading-' + feedtype).css('display', 'none');

      } else {
        $.mobile.activePage.find('#feed_viewmore-' + feedtype).css('display', 'none');
        $.mobile.activePage.find('#feed_loading-' + feedtype).css('display', 'none');
        if ($.mobile.activePage.advfeed_array.next_id > 0)
          $.mobile.activePage.find('#feed_no_more-' + feedtype).css('display', 'block');
      }


      $(this).delay(400).queue(function() {

        $(this).clearQueue();
      });

      this.composer.init(attachmentparmas);

    },
    activityViewMore: function(feedTaburl, feedtype, callback) {
      if ($.mobile.activePage.advfeed_array.activityFeedViewMoreActive == true)
        return;
      if ($.mobile.activePage.advfeed_array.autoScrollFeedAAFEnable)
        $.mobile.activePage.advfeed_array.activityFeedViewMoreActive = true;
      $.mobile.activePage.find('#feed_viewmore-' + feedtype).css('display', 'none');
      $.mobile.activePage.find('#feed_loading-' + feedtype).css('display', '');
      //make options object for site feed
      if (feedtype == 'sitefeed') {
        $params = $.extend($.mobile.activePage.advfeed_array, {
          'feedOnly': true,
          'nolayout': true,
          'subject': $.mobile.activePage.advfeed_array.subject_guid,
          'format': 'html',
          'maxid': $.mobile.activePage.advfeed_array.next_id
        });
      } //make options object if facebook feed
      else if (feedtype == 'fbfeed' || 'tweeetfeed' || 'linkedinfeed') {
        $params = $.mobile.activePage.advfeed_array;
        feedTaburl = $.mobile.activePage.advfeed_array.url;
      }


      $.ajax({
        type: "GET",
        dataType: "html",
        url: feedTaburl,
        data: $params,
        success: function(responseHTML, textStatus, xhr) {
          var $html = $("<div></div>");
          $html.get(0).innerHTML = responseHTML;
          var tempLayout = $html.find('.layout_middle');
          if (tempLayout.length) {
            responseHTML = tempLayout.html();
          }
          $(document).data('loaded', true);
          $.mobile.activePage.find('#activity-feed-' + feedtype).append(responseHTML);
          sm4.core.photoGallery.set($.mobile.activePage.find('#activity-feed-' + feedtype));
          sm4.core.runonce.add(function() {

            $.mobile.activePage.advfeed_array.activityFeedViewMoreActive = false;

            $.mobile.activePage.advfeed_array.countScrollAAFSocial++;

            this.advfeed_array[this.getActivePageID()] = $.mobile.activePage.advfeed_array;

            this.setViewMore(feedtype);
            if (callback)
              callback();
          }.bind(this));

          sm4.core.runonce.trigger();
          sm4.core.dloader.refreshPage();
        }.bind(this)
      });
    }

  };




  sm4.activity.autoCompleter = {
    autocomplete_checkin: false,
    attach: function(element, url, params) {
      proceed_request = true;
      element = $("#" + element);
      var $element = element;
      $('.checkin-label').on('click', function(e) {
        e.preventDefault();
        if ($(this).html() == sm4.core.language.translate('Cancel')) {
          proceed_request = false;

          var previous_request = $element.data("jqXHR");
          previous_request.abort();
          $('#place-loading').css('display', 'none');
          if (!sm4.core.isApp()) {
            $(this).html(sm4.core.language.translate('Search'));
          }
          $('#stchekin_suggest_container').children('ul').css('display', 'none');
          element.val('');
          element.attr('placeholder', sm4.core.language.translate('Search..'));
        }

      });
      var search = params.search;
      ///element,url,type
      this.autocomplete_checkin = element.autocomplete({
        width: 300,
        max: params.limit,
        delay: 1,
        minLength: params.minLength,
        autoFocus: true,
        cacheLength: 1,
        scroll: true,
        highlight: true,
        messages: {
          noResults: sm4.core.language.translate('No search results.'),
          results: function(amount) {
            /*  return amount + ( amount > 1 ? " results are" : " result is" ) +
             " available, use up and down arrow keys to navigate.";*/

            return "";
          }
        },
        source: function(request, response) {
          $('.checkin-label').html(sm4.core.language.translate('Cancel'))
          var data = {
            limit: params.maxChoices
          };

          if ($.type(this.options.extraParams) != 'undefined' && element.val() == '') {
            $.extend(data, this.options.extraParams);
            if ($.type(this.options.extraParams.location_detected) != 'undefined' && this.options.extraParams.location_detected != '') {
              element.val(this.options.extraParams.location_detected);
              request.term = this.options.extraParams.location_detected;
              this.options.extraParams.location_detected = '';
            }


          }


//          var termss = sm4.core.Module.autoCompleter.split(request.term);
//          // remove the current input
//          request.term = termss[termss.length - 1];
          data.suggest = request.term;
          // New request 300ms after key stroke
          //          var $this = $(this);
          var $element = $(this.element);
          var previous_request = $element.data("jqXHR");
          if (previous_request || proceed_request == false) {
            // a previous request has been made.
            // though we don't know if it's concluded
            // we can try and kill it in case it hasn't
            previous_request.abort();
          }
          proceed_request_temp = true;
          $('#place-loading').css('display', 'block');
          element.next('span').html('');
          $('.sm-ui-autosuggest').html('');
          $('#place-errorlocation').css('display', 'none');
          // Store new AJAX request
          $element.data("jqXHR", $.ajax({
            type: "POST",
            url: url,
            data: data,
            dataType: "json",
            success: function(data, textStatus, jqXHR) {
              response(data);
              $('#place-loading').css('display', 'none');
            },
            error: function(jqXHR, textStatus, errorThrown) {
              response([]);
              $('#place-loading').css('display', 'none');
            }
          }));
        },
        select: function(e, ui) {
          var addLinkBefore = $('#sitetagchecking_mob');
          $('.sm-post-wrap').css('display', 'block');
          //$('#toValuesdone-wrapper').css('display', 'block');
          $('#ui-header').css('display', 'block');
          $('#ui-header-checkin').css('display', 'none');
          addLinkBefore.next().css('display', 'none');
          if ($.type($('.aaf-add-friend-tagcontainer').get(0)) != 'undefined')
            $('.aaf-add-friend-tagcontainer').remove();
          sm4.activity.options.allowEmptyWithoutAttachment = true;

          return params.callback.setLocation(ui.item);
        },
        open: function() {
          // autocomplete.menu.element.listview().listview('refresh');
          $(this).removeClass("ui-corner-all").addClass("ui-corner-top");
        },
        close: function() {
          $(this).removeClass("ui-corner-top").addClass("ui-corner-all");
        }
      }).data('autocomplete');
      // autocomplete.menu.element.attr('data-role','listview');
      this.autocomplete_checkin._renderItem = function(ul, item) {
        ul.appendTo($("#stchekin_suggest_container"));
        ul.attr({
          'data-role': 'listview',
          'data-inset': true,
          'class': 'ui-listview sm-ui-autosuggest'
        });
        var myHTML = "<a class='ui-btn ui-btn-icon-right'>";

        if (params.showPhoto && item.photo) {
          myHTML = myHTML + item.photo;
        }
        if (item.type == 'just_use')
          myHTML = myHTML + item.li_html + "</a>";
        else
          myHTML = myHTML + item.label + "</a>";

        return  $('<li data-corners="false" data-shadow="false" data-iconshadow="true" data-wrapperels="div" data-icon="arrow-r" data-iconpos="right" data-theme="c">')
                .attr('class', 'ui-menu-item_' + item.id + ' ui-li-has-thumb')
                .attr('role', 'menuitem')
                .data("item.autocomplete", item)
                .append(myHTML)
                .appendTo(ul);

      };
    },
    checkSpanExists: function(name, toID) {
      var span_id = "jquerytospan_" + name + "_" + toID;

      if (document.getElementById(span_id)) {
        return true;
      }
      else {
        return false;
      }
    },
    removeTagResults: function(removeObject) {
      this.removeFromToValue(removeObject.attr('id'));
      //remove current friend
      removeObject.parent().remove();
    },
    removeFromToValue: function(id) {
      // code to change the values in the hidden field to have updated values
      // when recipients are removed.
      var toValues = $('#toValues').val();
      var toValueArray = toValues.split(",");
      var toValueIndex = "";

      var checkMulti = id.search(/,/);

      // check if we are removing multiple recipients
      if (checkMulti != -1) {
        var recipientsArray = id.split(",");
        for (var i = 0; i < recipientsArray.length; i++) {
          this.removeToValue(recipientsArray[i], toValueArray);
        }
      }
      else {
        this.removeToValue(id, toValueArray);
      }

    },
    extractLast: function(term) {
      return this.split(term).pop();
    },
    removeToValue: function(id, toValueArray) {
      var toValueIndex = 0;
      for (var i = 0; i < toValueArray.length; i++) {
        if (toValueArray[i] == id)
          toValueIndex = i;
      }

      toValueArray.splice(toValueIndex, 1);
      $("#toValues").attr({
        value: toValueArray.join()
      });
    },
    split: function(val) {
      return val.split(/,\s*/);
    }
  }

})(); // END NAMESPACE
String.prototype.capitalize = function() {
  return this.charAt(0).toUpperCase() + this.slice(1);
}
;
(function($) {
  $.capitalize = function(str) {
    str = str.toLowerCase().replace(/\b[a-z]/g, function(letter) {
      return letter.toUpperCase();
    });
    return str;
  };

})(jQuery);

$.fn.inject = function(parent, position) {
  if (position == 'after')
    parent.after(this);
  else if (position == 'before')
    parent.before(this);
  else
    parent.append(this);
  return this;
};

var editPostStatusPrivacy = function(action_id, privacy) {
  $('#privacyoptions-popup-' + action_id).on('click', function() {
    $('#privacyoptions-popup-' + action_id).popup('close').delay(100);
    $('#privacyoptions-popup-' + action_id).remove();
  });

  //if( en4.core.request.isRequestActive())return;
  switch (privacy) {
    case "custom_0":
      sm4.core.showError('<div data-role="popup" data-theme="e" style="max-width:350px;" aria-disabled="false" data-disabled="false" data-shadow="true" data-corners="true" data-transition="none" class=\'aaf_show_popup\'><div class=\'tip\'><span>' + sm4.core.language.translate('You have currently not organized your friends into lists. To create new friend lists, go to the "Friends" section of your profile') + '."</span></div><div><a href="#" data-role="button" data-inline="true" data-rel="back" data-theme="c">' + sm4.core.language.translate('Cancel') + '</a></div></div>');
      break;
    case "custom_1":
      sm4.core.showError('<div data-role="popup" data-theme="e" style="max-width:350px;" aria-disabled="false" data-disabled="false" data-shadow="true" data-corners="true" data-transition="none" class=\'aaf_show_popup\'><div class=\'tip\'><span>' + sm4.core.language.translate('You have currently created only one list to organize your friends. Create more friend lists from the "Friends" section of  your profile') + '."</span></div><div><a href="#" data-role="button" data-inline="true" data-rel="back" data-theme="c">' + sm4.core.language.translate('Cancel') + '</a></div></div>');
      break;
    case "custom_2":
      break;
    case "network_custom":
      break;
    default:

      var ajax = sm4.core.request.send({
        //type: "POST", 
        dataType: "json",
        url: sm4.core.baseUrl + 'advancedactivity/feed/edit-feed-privacy',
        data: {
          format: 'json',
          privacy: privacy,
          subject: $.mobile.activePage.advfeed_array.subject_guid,
          action_id: action_id
        },
        success: function(responseJSON, textStatus, xhr) {
          $.mobile.activePage.find('#activity-item-' + action_id).html(responseJSON.body);
          //sm4.activity.showOptions(action_id);
          sm4.core.dloader.refreshPage();
          sm4.core.runonce.trigger();
          sm4.core.photoGallery.set($.mobile.activePage.find('#activity-item-' + action_id));
        }
      }
      );

  }
}


sm4.activity.statusbox = {
  privacyButton: false,
  privacy: false,
  self: false,
  reffid: '',
  togglePrivacy: function(self) {
    this.privacyButton = self;
    if (!sm4.core.isApp())
      $('#activitypost-container-temp').find('.composer_status_share_options').toggle();
    else
      $('#activitypost-container-temp').find('.composer_status_share_options').toggleClass('active');
    hidePrivacyIconClickEnable = true;
  },
  toggleEmotions: function(self) {
    $('#activitypost-container-temp').find('#emoticons-board').toggle();
    hideEmotionIconClickEnable = true;
  },
  toggleReplyEmotions: function(self, id) {
    $('#reply_options_box_'+ id).find('#emoticons-reply-board_'+id).toggle();
    hideEmotionReplyIconClickEnable = true;
  },
  addPrivacy: function(privacy) {
    this.reffid = '#cm-icon-' + privacy;
    this.privacy = privacy;
    privacy_temp = privacy.split('_');
    if (typeof privacy_temp[1] != 'undefined')
      $('#auth_view').val(privacy_temp[1]);
    else
      $('#auth_view').val(privacy_temp[0]);
    if (privacy_temp[0] == 'network')
      privacy_temp[0] = 'network-list';
    $('.ui-icon-ok').remove();
    $(this.reffid).find('td.compose_pr_op_right').html('<i class="ui-icon ui-icon-ok"></i>');
    $('#activitypost-container-temp').find('#addprivacy').children('i').remove();

    var icon = $('<i />', {
      'class': 'cm-icons cm-icon-' + privacy_temp[0]
    });
    $('#activitypost-container-temp').find('#addprivacy').prepend(icon);
    $('#activitypost-container-temp').find('#activity_body').focus();
  }
}


var deviceWinPhone8 = "windows phone 8.0";
//**************************
// Detects if the current browser is a
// Window Phone 8.0 device.
//function DetectWindowsPhone8()
//{ 
//   if (uagent.search(deviceWinPhone8) > -1) {
//      return true;}
//    else {
//      return false;}
//}

//**************************
// Detects if the current browser is a Windows Mobile device.
// Excludes Windows Phone 7  And Windows Phone 8devices.
// Focuses on Windows Mobile 6.xx and earlier.

//function DetectWindowsMobileBefore7(){
//     //Exclude new Windows Phone 8.
//   if (DetectWindowsPhone8())
//      return false;
//   //Exclude new Windows Phone 7.
//   if (DetectWindowsPhone7())
//      return false;
//    
//    return DetectWindowsMobile();
//}

function DetectAllIos()
{
  //CHECK DEVICE IS IPAD / IPHONE / IPOD.
  if (/iP(hone|od|ad)/.test(navigator.platform)) {
    return true;
  }
  return false;
}

function DetectAllWindowsMobile()
{
  if (sm4.core.isApp())
    return false;

  //Most devices use 'Windows CE', but some report 'iemobile'
  //  and some older ones report as 'PIE' for Pocket IE.

  //CHECK IF THE MOBILE IS IPAD AND THE VERSIO IS LESS THEN 6
  if (DetectIpad()) {
    ver = iOSversion();
    if (ver[0] >= 6)
      return false;
    else
      return true;
  }
  if (uagent.search(deviceWinMob) > -1 ||
          uagent.search(deviceIeMob) > -1 ||
          uagent.search(enginePie) > -1)
    return true;
  //Test for Windows Mobile PPC but not old Macintosh PowerPC.
  if ((uagent.search(devicePpc) > -1) &&
          !(uagent.search(deviceMacPpc) > -1))
    return true;
  //Test for Windwos Mobile-based HTC devices.
  if (uagent.search(manuHtc) > -1 &&
          uagent.search(deviceWindows) > -1)
    return true;
  else
    return false;
}

function iOSversion() {
  if (/iP(hone|od|ad)/.test(navigator.platform)) {
    // supports iOS 2.0 and later: <http://bit.ly/TJjs1V>
    var v = (navigator.appVersion).match(/OS (\d+)_(\d+)_?(\d+)?/);
    return [parseInt(v[1], 10), parseInt(v[2], 10), parseInt(v[3] || 0, 10)];
  }
}

//HANDLING OF BACK AND FORWARD BUTTON OF BROWSER...

var statushideOnShow = function(event, data) {
  //  if (typeof $.mobile.activePage != 'undefined') {
  //    if (currentactive_panel != 'undefined' && $.mobile.activePage.attr('id') == currentactive_panel.attr('id')) {
  //      if ($.type($('#activitypost-container-temp').find('form').get(0)) != 'undefined')
  //        $('#activitypost-container-temp').css('display', 'none');
  //    }     
  //  
  //  }
  if (deleteCommentActive) {
    deleteCommentActive = 0;
  } else if ($.type($.mobile.activePage) != 'undefined' && $.type($.mobile.activePage.popupid) != 'undefined') {
    $('#' + $.mobile.activePage.popupid).remove();
  }

  //$('#activitypost-container-temp').remove();

}.bind(this);

var statushideOnShowAfter = function(event, data) {
  //  if (currentactive_panel != 'undefined' && $.mobile.activePage.attr('id') != currentactive_panel.attr('id')) { 
  //    if ($.type($('#activitypost-container-temp').find('form').get(0)) != 'undefined')
  //      $('#activitypost-container-temp').css('display', 'none');
  if ($.mobile.activePage.hasClass('dnone'))
    $.mobile.activePage.removeClass('dnone')
  //  }
  //  else if (currentactive_panel != 'undefined' && $.mobile.activePage.attr('id') == currentactive_panel.attr('id') && currentactive_panel.hasClass('dnone')) {
  //    $('#activitypost-container-temp').css('display', 'block');
  //  }
  //$('#activitypost-container-temp').remove();
  //SPECIAL CASE IF THERE IS ACTIVITY FEED WIDGET THEN CALL THIS FUNCTION: 

  sm4.activity.setOnScrollLoadActivity();

  //CHECK IF COMMENT OR SHARE POPUP IS OPEN OR NOT: IF OPEN THEN JUST HIDE THEM.
   
  if ($('.ui-page-active').hasClass('pop_back_max_height') || $('.ui-page-active').hasClass('dnone')) {
    $('.ui-page-active').removeClass('pop_back_max_height');
    $('.ui-page-active').removeClass('dnone');
    if (typeof parentScrollTop != 'undefined') {
      $('#feedsharepopup').remove();
      $(window).scrollTop(parentScrollTop);
    }
  }


}.bind(this);

$(document).off('pagebeforechange', statushideOnShow).on('pagebeforechange', statushideOnShow);


$(document).off('pagechange', statushideOnShowAfter).on("pagechange", statushideOnShowAfter);


sm4.activity.activityUpdateHandler = {
  pageOptions: {},
  options: {
    last_id: null,
    showImmediately: false,
    delay: 5000
  },
  updateHandler: null,
  hasAttachEvent: false,
  getUpdateActive: false,
  initialize: function(options)
  {
    options.subject_guid = $.mobile.activePage.jqmData('subject');
    var self = this;
    self.pageOptions[self.getIndexId()] = options;
    self.attachEvent();
    if (sm4.core.isApp()) {
      setTimeout(function() {
        self.iScrollEvent();
      }, 10);
    }
  },
  getIndexId: function() {
    var currentpageid = $.mobile.activePage.attr('id');
    if ($.mobile.activePage.jqmData('subject')) {
      currentpageid = currentpageid + "_" + $.mobile.activePage.jqmData('subject');
    }
    return currentpageid;
  },
  start: function() {
    if (!this.hasCheckUpdates()) {
      return false;
    }

    var self = this;
    this.updateHandler = setInterval(function() {
      self.checkUpdate();
    }, this.options.delay);

  },
  stop: function() {
    if (this.updateHandler != null) {
      clearInterval(this.updateHandler);
      this.updateHandler = null;
    }
  },
  hasCheckUpdates: function() {
    if (!this.pageOptions[this.getIndexId()])
      return false;
    this.options = this.pageOptions[this.getIndexId()];
    return true;
  },
  scrollRefresh: function() {
     var index= this.getIndexId();
     if (this.pageOptions[index] && this.pageOptions[index].feedscroll) {
      this.pageOptions[index].feedscroll.scrollAfterPulldown(index);
    }
    if (!this.hasCheckUpdates())
      return; 
  },
  scrollToElement: function(el, duration) {
    if (!el)
      return;
    var options = this.pageOptions[this.getIndexId()];
    if (!options)
      return;
    var scroll = options.scroll;
    if (!scroll)
      return;
    el = el.nodeType ? el : $.mobile.activePage.find(el)[0];
    duration = duration ? duration : 10;
    if (!el)
      return;
    scroll.isScrollToElement = true;
    scroll.scrollToElement(el, duration);
  },
  iScrollEvent: function() {
    var self = this,
            wrapper = $.mobile.activePage.find('[data-role="wrapper"]'),
            pullDownEl = $.mobile.activePage.find('.sm_aaf_pullDown'),
            pullUpEl = $.mobile.activePage.find('.sm_aaf_pullUp'),
            composerWrapper = $.mobile.activePage.find('.layout_sitemobile_sitemobile_advfeed [data-role="composer-wrapper"]');

    if (wrapper.find('.layout_page_user_index_home').length > 0 && composerWrapper.length > 0) {
      composerWrapper.attr('data-iscroll-fixed', '1');
      if ($.mobile.activePage.find('.ui-page-header-layout').length) {
        $.mobile.activePage.find('.ui-page-header-layout').append(composerWrapper);
        $.mobile.activePage.css({paddingTop: $.mobile.activePage.find('.ui-page-header-layout').outerHeight(true)});
      //  composerWrapper.css({margin: '2px'});
      } else {
        wrapper.before(composerWrapper);
      }
      composerWrapper.addClass('composer-wrapper');
      wrapper.css({marginTop: '0px', paddingTop: '7px'});
    }

    wrapper.addClass('iscroll_wapper_feed');
    wrapper.prepend(pullDownEl);
    wrapper.append(pullUpEl);
    pullDownEl.removeClass('dnone').css({paddingTop: 50});

    $.mobile.activePage.find('#feed_viewmore-sitefeed').addClass('dnone');
    $.mobile.activePage.attr('data-feedscroll', 'pull');
    var pullDownLabel = pullDownEl.find('.iscroll-pull-label');
    self.pageOptions[self.getIndexId()].feedscroll = {
      loading: false,
      scrolling: false,
      pullDownEl: pullDownEl,
      pullDownOffset: pullDownEl.outerHeight(),
      pullDownRelease: pullDownEl.outerHeight() - 40,
      pullDownLabel: pullDownLabel,
      pulldown_to_release: pullDownLabel.jqmData('iscroll-pulled-text'),
      loading_to_pulldown: pullDownLabel.html(),
      release_to_pulldown_loading: pullDownLabel.jqmData('iscroll-loading-text')
    };

    var scrollAfterPulldown = function(index) {
      var fscroll =  self.pageOptions[index].feedscroll;
      if ($(document).scrollTop() < fscroll.pullDownOffset)
        window.scrollTo(0, fscroll.pullDownOffset);
      self.pageOptions[index].feedscroll.loading = false;
      fscroll.pullDownEl.removeClass('iscroll-pull-pulled');
      fscroll.pullDownEl.removeClass('iscroll-pull-loading');
      fscroll.pullDownLabel.html(fscroll.loading_to_pulldown);
    }, scrollAfterPulldownLading = function(fscroll) {
      window.scrollTo(0, fscroll.pullDownRelease);
      fscroll.pullDownEl.removeClass('iscroll-pull-pulled');
      fscroll.pullDownEl.addClass('iscroll-pull-loading');
      fscroll.pullDownLabel.html(fscroll.release_to_pulldown_loading);
    }, scrollPullToRelease = function(fscroll) {
      fscroll.pullDownEl.addClass('iscroll-pull-pulled');
      fscroll.pullDownLabel.html(fscroll.pulldown_to_release);
    }, pulldownAction = function() {
      var fscroll = !!self.pageOptions[self.getIndexId()] && self.pageOptions[self.getIndexId()].feedscroll ? self.pageOptions[self.getIndexId()].feedscroll : false;
      if (fscroll && !!fscroll.loading) {
        scrollAfterPulldownLading(fscroll);
        var index = self.getIndexId();
        self.getFeedUpdate(function() {
          scrollAfterPulldown(index);
        });
      } else if (fscroll && ($(document).scrollTop() < (fscroll.pullDownOffset))) {
        var index = self.getIndexId();
        scrollAfterPulldown(index);
      }
    };
    self.pageOptions[self.getIndexId()].feedscroll.scrollAfterPulldown = scrollAfterPulldown;
    parentScrollTop = self.pageOptions[self.getIndexId()].feedscroll.pullDownOffset;
    scrollAfterPulldown(self.getIndexId());
    $(wrapper).on('touchstart', function() {
      self.pageOptions[self.getIndexId()].scrolling = true;
    });
    $(wrapper).on('touchend', function() {
      self.pageOptions[self.getIndexId()].scrolling = false;
    });
//    $.mobile.activePage.on('refresh', function() {
//      console.log("Refresh");
//    });
    $(document).on('scrollend', pulldownAction);
    $(document).scroll(function(event) {
      var index = self.getIndexId();
      var flagVariable;
      if(!sm4.core.isIos())
      flagVariable = $.mobile.activePage.length && !!self.pageOptions[index] && self.pageOptions[index].feedscroll && self.pageOptions[self.getIndexId()].scrolling;
      else
      flagVariable = $.mobile.activePage.length && !!self.pageOptions[index] && self.pageOptions[index].feedscroll;    
      
      if (flagVariable) {
      
        var $page = $.mobile.activePage, height = $page.outerHeight() - $.mobile.getScreenHeight() - 30, fscroll = self.pageOptions[index].feedscroll;
        if ($page.css('paddingBottom')) {
          height = height - parseInt($page.css('paddingBottom').replace('px', ''));
        }
        if ((height > 0) && height <= $(document).scrollTop()) {
          if ($.mobile.activePage.advfeed_array.next_id > 0 && $.mobile.activePage.advfeed_array.endOfFeed == false) {
            sm4.activity.activityViewMore(sm4.core.baseUrl + 'widget/index/name/sitemobile.sitemobile-advfeed', 'sitefeed', function() {
            });
          }
        } else if (($(document).scrollTop() < fscroll.pullDownOffset)) {
          if ($(document).scrollTop() < fscroll.pullDownRelease) {
            self.pageOptions[index].feedscroll.loading = true;
            scrollPullToRelease(fscroll);
          } else {
            fscroll.pullDownLabel.html(fscroll.loading_to_pulldown);
            fscroll.pullDownEl.removeClass('iscroll-pull-pulled');
            fscroll.pullDownEl.removeClass('iscroll-pull-loading');
            self.pageOptions[index].feedscroll.loading = false;
          }
        }
      }
    });
    return;
    /*  wrapper.iscrollview({
     preventPageScroll: false,
     oncreateafter: function(event, data) {
     self.pageOptions[self.getIndexId()].scroll = data.iscrollview;
     // data.iscrollview.refresh();
     },
     onpullup: function(event, data) {
     
     if ($.mobile.activePage.advfeed_array.next_id > 0 && $.mobile.activePage.advfeed_array.endOfFeed == false) {
     sm4.activity.activityViewMore(sm4.core.baseUrl + 'widget/index/name/sitemobile.sitemobile-advfeed', 'sitefeed', function() {
     var iscrollview = data.iscrollview;
     iscrollview.refresh(null, null,
     $.proxy(function afterRefreshCallback(iscrollview) {
     if (!($.mobile.activePage.advfeed_array.next_id > 0 && $.mobile.activePage.advfeed_array.endOfFeed == false)) {
     data.iscrollview.$pullUp.remove();
     data.iscrollview.$pullUp = {};
     // $.mobile.activePage.find(".iscroll-pullup-spacer").remove();
     var is = this;
     setTimeout(function() {
     is.refresh();
     }, 100);
     
     
     }
     }, iscrollview));
     });
     
     } else {
     if (data.iscrollview.$pullUp) {
     data.iscrollview.$pullUp.remove();
     data.iscrollview.$pullUp = {};
     }
     data.iscrollview.refresh();
     }
     },
     onpulldown: function(event, data) {
     self.getFeedUpdate(function() {
     data.iscrollview.refresh();
     });
     }
     });*/

  },
  attachEvent: function() {
    if (!this.hasAttachEvent) {
      var self = this;
      $(document).bind('pageshow', function(event, data) {
        self.start();
      });
      $(document).bind('pagehide', function(event, data) {
        self.stop();
      });

      $(document).bind('onAppPause', function(event, data) {
        self.stop();
      });
      $(document).bind('onAppResume', function(event, data) {
        self.start();
        self.checkUpdate({
          showImmediately: true
        });
      });
      this.hasAttachEvent = true;
    }
  },
  updateOptions: function(options) {
    if (!this.pageOptions[this.getIndexId()])
      return false;
    var self = this;
    $.each(options, function(key, value) {
      self.options[key] = value;
    });
    this.pageOptions[this.getIndexId()] = this.options;

  },
  checkUpdate: function(options) {
    if (!this.hasCheckUpdates() || this.getUpdateActive) {
      return false;
    }
    var self = this;
    var ajax = $.ajax({
      type: "POST",
      dataType: "html",
      url: this.options.url,
      data: {
        'minid': this.options.last_id + 1,
        'feedOnly': true,
        'nolayout': true,
        'subject': this.options.subject_guid,
        'checkUpdate': true,
        'format': 'html'
      },
      success: function(responseHTML, textStatus, xhr) {
        try {
          if (responseHTML) {
            $.mobile.activePage.find('#feed-update').html(responseHTML);
            if (self.options.showImmediately || (typeof options == 'object' && options.showImmediately)) {
              self.getFeedUpdate();
            }
            else {
              $.mobile.activePage.find('#feed-update').trigger('create');
              $.mobile.activePage.find("#feed-update").css('display', 'block');
            }
          }
        } catch (errorThrown) {
          throw errorThrown;

        }
      }
    }
    );
  },
  getFeedUpdate: function(callback) {
    if (this.getUpdateActive)
      return;
    var scroll = this.pageOptions[this.getIndexId()].feedscroll;
    this.getUpdateActive = true;
    $.mobile.activePage.find('#feed-update').html('');
    $.mobile.activePage.find("feed-update").css('display', 'none');
    if (!this.options.showImmediately && !scroll) {
      $.mobile.activePage.find("#aaf_feed_update_loading").css('display', 'block');
    }
    var min_id = this.options.last_id + 1;
    var self = this;
    var ajax = $.ajax({
      type: "POST",
      dataType: "html",
      url: this.options.url,
      data: {
        'minid': min_id,
        'feedOnly': true,
        'nolayout': true,
        'getUpdate': true,
        'subject': this.options.subject_guid,
        'format': 'html'
      },
      success: function(responseHTML, textStatus, xhr) {
        self.getUpdateActive = false;
        $.mobile.activePage.find("#aaf_feed_update_loading").css('display', 'none');
        if (callback) {
          callback();
        }
        try {
          if (responseHTML) {
            $.mobile.activePage.find('#activity-feed-sitefeed').prepend(responseHTML);
            $.mobile.activePage.find('#activity-feed-sitefeed').trigger('create');
            sm4.core.runonce.trigger();
          }
        } catch (errorThrown) {
          throw errorThrown;

        }
      }
    }
    );
  }
};

var hideEmotionIconClickEnable = false,
        hidePrivacyIconClickEnable = false,
        hideEmotionReplyIconClickEnable = false,
setEmoticonsBoard = function() {
  $('#emotion_lable').html('');
  $('#emotion_symbol').html();
  hideEmotionIconClickEnable = true;
  var a = $('#emoticons-button');
  a.toggleClass('active');
  a.toggleClass('');
},
addEmotionIcon = function(iconCode) {
  var el = $('.compose_embox_cont');
  el.toggle();
  var content;
  content = sm4.activity.getContent();
  content = content.replace(/(<br>)$/g, "");
  content = content + ' ' + iconCode;
  sm4.activity.setContent(content);
  $('#activitypost-container-temp').find('#activity_body').focus();
},
        
addEmotionIconNestedComment = function(iconCode, body, id) {
  if($.type(id) != 'undefined'){
    var el = $('#reply_options_box_'+id).find('.compose_embox_cont');
  } else {
    var el = $('.compose_embox_cont');   
  }
  el.toggle();
  var content;
  content = $(body).val();
  content = content.replace(/(<br>)$/g, "");
  content = content + ' ' + iconCode;
  $(body).val(content);
  $('#activitypost-container-temp').find(body).focus();
},
setEmoticonsReplyBoard = function(id) {
  $('#emotion_lable').html('');
  $('#emotion_symbol').html();
  hideEmotionReplyIconClickEnable = true;
  var a = $('#emoticons-reply-button_'+id);
  a.toggleClass('active');
  a.toggleClass('');
},
addEmotionIconReply = function(iconCode, body, id) {
  var el = $('#reply-form_'+id).find('.compose_embox_cont');
  el.toggle();
  var content;
  content = $('#reply-form_'+id).find(body).val();
  content = content.replace(/(<br>)$/g, "");
  content = content + ' ' + iconCode;
  $('#reply-form_'+id).find(body).val(content);
  $('#reply-form_'+id).find(body).focus();
},
hideEmotionIconClickEvent = function() {
  if (!hideEmotionIconClickEnable && $('.compose_embox_cont') && $('.cm-icon-emoticons').hasClass('active')) {
    $('.compose_embox_cont').css('display', 'none');
    $('.cm-icon-emoticons').removeClass('active');

  }
  hideEmotionIconClickEnable = false;
},
setEmotionLabelPlate = function(lable, symbol) {
  $('#emotion_lable').html(lable);
  $('#emotion_symbol').html(symbol);
},
hidePrivacyIconClickEvent = function() {

  if (!sm4.core.isApp()) {
    if (!hidePrivacyIconClickEnable && $('.composer_status_share_options') && $('.composer_status_share_options').css('display') === 'block') {
      $('.composer_status_share_options').css('display', 'none');
    }
  } else {
    if (!hidePrivacyIconClickEnable && $('.composer_status_share_options') && $('.composer_status_share_options').hasClass('active')) {
      $('.composer_status_share_options').toggleClass('active');
    }
  }
  hidePrivacyIconClickEnable = false;
};
