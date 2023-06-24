/*------------------------------------------ */
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: core.js 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
var sm4 = {
};

sm4.user = {
  /* changes remaining (to check)*/
  viewer: {
    type: false,
    id: false
  },
  //    clearStatus : function() {
  //    var request = new Request.JSON({
  //      url : sm4.core.baseUrl + 'user/edit/clear-status',
  //      method : 'post',
  //      data : {
  //        format : 'json'
  //      }
  //    });
  //    request.send();
  //    if( $('user_profile_status_container') ) {
  //      $('user_profile_status_container').empty();
  //    }
  //    return request;
  //  },
  clearStatus: function() {
    sm4.core.request.send({
      type: "POST",
      dataType: "json",
      url: sm4.core.baseUrl + 'user/edit/clear-status',
      method: 'post',
      data: {
        format: 'json'
      }
    });
    if ($('#user_profile_status_container')) {
      $('#user_profile_status_container').empty();
    }
    //return request;
  },
  buildFieldPrivacySelector: function(elements) {
    var idEx = {};

    // Clear when body click, if not inside selector
    $.mobile.activePage.on('click', function(event) {
      if ($(event.target).hasClass('field-privacy-selector')) {
        return;
      } else if ($(event.target).closest('.field-privacy-selector').length) {
        return;
      } else {
        $('.field-privacy-selector').removeClass('active');
      }
    });
    // Register selectors
    elements.each(function() {
      var el = $(this);
      if (this.tagName == 'SPAN') {
        return;
      }
      var fuid = el.attr('id');
      var tmp;
      if ((tmp = fuid.match(/^\d+_\d+_\d+/))) {
        fuid = tmp[0];
      }
      var id = el.attr('data-field-id');
      if (id in idEx) {
        return;
      }
      idEx[id] = true;
      var wrapperEl = el.closest('.form-wrapper');
      var privacyValue = el.jqmData('privacy');
      var selector = $('<div />')
              .attr('class', 'field-privacy-selector')
              .attr('data-privacy', privacyValue || 'everyone')
              .html('\
                  <span class="icon"></span>\n\
                  <span class="caret"></span>\n\
                  <ul>\n\
                    <li data-value="everyone" class="field-privacy-option-everyone"><span class="icon"></span><span class="text">'
              + sm4.core.language.translate('Everyone') + '</span></li>\n\
                    <li data-value="registered" class="field-privacy-option-registered"><span class="icon"></span><span class="text">'
              + sm4.core.language.translate('All Members') + '</span></li>\n\
                    <li data-value="friends" class="field-privacy-option-friends"><span class="icon"></span><span class="text">'
              + sm4.core.language.translate('Friends') + '</span></li>\n\
                    <li data-value="self" class="field-privacy-option-self"><span class="icon"></span><span class="text">'
              + sm4.core.language.translate('Only Me') + '</span></li>\n\
                  </ul>\n\
                  <input type="hidden" name="privacy[' + fuid + ']" />');

      selector.inject(wrapperEl);
      selector.on('click', function(event) {
        var prevState = selector.hasClass('active');
        $.mobile.activePage.find('.field-privacy-selector').removeClass('active');
        if (!prevState) {
          selector.addClass('active');
        }
      });
      selector.find('li').on('click', function(event) {
        var el = $(event.target);
        if (event.target.tagName != 'LI') {
          el = el.parent();
        }
        var value = el.attr('data-value');
        selector.find('input').attr('value', value);
        selector.find('.active').removeClass('active');
        el.addClass('active');
        selector.attr('data-privacy', value);
      })
      selector.find('*[data-value="' + (privacyValue || 'everyone') + '"]').addClass('active');
      selector.find('input').attr('value', privacyValue || 'everyone');
    });
  }
}
sm4.core = {
  baseUrl: '',
  basePath: '',
  location: {},
  environment: 'production',
  init: function(options) {
    //sm4.core.language.init(options.languageData);
    sm4.core.reset(options);
    sm4.core.setConfig(options);
    sm4.core.libraries.init();
    sm4.core.setBaseUrl(options.baseUrl);
    sm4.core.dloader.init();
    if ($("meta[name=environment]"))
      sm4.core.environment = $("meta[name=environment]").attr("content");
    $(document).trigger('afterSMCoreInit', options);
  },
  reset: function(data) {
    if (data.languageData) {
      sm4.core.language.init(data.languageData);
      // $.mobile.loadingMessage = sm4.core.language.translate('SITEMOBILE_LOADING_PAGE_MESSAGE');
      $.mobile.loader.prototype.options.textVisible = true;
//       $.mobile.loader.prototype.defaultHtml= "<div class='" + loaderClass + "'>" +
//			"<span class='ui-icon-loading'></span>" +
//			"<h1></h1>" +
//			"</div>";
      $.mobile.loader.prototype.options.text = sm4.core.language.translate('SITEMOBILE_LOADING_PAGE_MESSAGE');

      $.mobile.loadingPhotoGalleryMessage = sm4.core.language.translate('SITEMOBILE_LOADING_PHOTOGALLERY_MESSAGE');
      $.mobile.pageLoadErrorMessage = sm4.core.language.translate('SITEMOBILE_ERROR_LOADING_PAGE_MESSAGE');
    }
    if (data.viewerDetails)
      sm4.user.viewer = data.viewerDetails;
    if (data.mlocale)
      sm4.core.language.options.locale = data.mlocale;
    if (data.defaultLocale)
      sm4.core.language.options.defaultLocale = data.defaultLocale;
  },
  setBaseUrl: function(url)
  {
    this.baseUrl = url;
    var m = this.baseUrl.match(/^(.+?)index[.]php/i);
    this.basePath = (m ? m[1] : this.baseUrl);
  },
  isDevelopmentMode: function() {
    return  sm4.core.environment == 'development';
  },
  subject: {
    type: '',
    id: 0,
    guid: ''
  },
  isApp: function() {
    return !!window.smappcore;
  },
isIos: function(){
    var iOS = ( navigator.userAgent.match(/(iPad|iPhone|iPod)/g) ? true : false );
    return iOS;//(navigator.userAgent.match(/(iPad|iPhone|iPod)/g) ? true : false );
},
  isPhoneGap: function() {
    return window.cordova || window.phonegap;
  },
  setConfig: function(options) {
    // $(document).on("mobileinit", function(){
    $.mobile.defaultPageTransition = 'none';
    $.mobile.defaultDialogTransition = 'none';
    $.mobile.loadingMessageTextVisible = true;
    $.mobile.dynamicBaseEnabled = false;
    //$.mobile.page.prototype.options.headerTheme  = "a";
    //$.mobile.page.prototype.options.footerTheme  = "a";
    //$.mobile.page.prototype.options.contentTheme = null;
    //  });
    this.location = window.location;
    $(document).bind('pagebeforeshow', function(event, data) {
      var $page = $(event.target);
      var dashboardPanelId = $page.jqmData('dashboardpanelid') ? '#' + $page.jqmData('dashboardpanelid') : '#dashboardPanelMenu';
      if ($page.find(dashboardPanelId) && $page.find(".dashboard-panel-mini-menu").length) {
        $page.find(".ui-panel-content-wrap").addClass('dashboard-panel-mini-menu-hide');
        //
        $page.find(".ui-panel-content-fixed-toolbar").addClass('dashboard-panel-mini-menu-fixed-toolbar-hide');
      }

      //ON ACTIVE LINK OF MORE POPUP - ACTIVE MORE TAB ALSO
      var moreMenuButton = $.mobile.activePage.find('#navigationBarMore');
      if (moreMenuButton.length && $.mobile.activePage.find(moreMenuButton.attr('href')).find("a[active='1']").length) {
        moreMenuButton.addClass('ui-btn-active');
      }
      //END

      if ($page.data('pageShowTime') && (event.timeStamp - $page.data('pageShowTime')) > (900000)) {
        sm4.core.reloadPage();
      }
      if (!(sm4.core.isApp() && !window.phonegap.checkOnlineMode()))
        $page.data('pageShowTime', event.timeStamp);

      //IF DASHBOARD ICON IS NOT IN HEADER BUT NOTIFICATION COUNT IS  THERE THEN REMOVE THAT.
      if ($.type($page.find('#header-dashboard-menuButton').get(0)) == 'undefined' && $page.find('.total-count-left-fix-bubble').length)
        $page.find('.total-count-left-fix-bubble').css('display', 'none');
    });
    var resetThePageHeigth, focusinpage = false;
    $(document).bind('pageshow', function(event, data) {
      $.mobile.activePage.fileUploader = [];
      sm4.core.closefunctions.trigger();
      var $page = $(event.target);
      $page.find('.smapp_download_photo').attr('data-ajax', 'false').attr('data-download', 'true');
      if ($page.find('.sm-mini-menu').length || $page.find('.main-navigation').length && $page.find('.main-navigation').find('.core_main_update').length) {
        sm4.activity.notificationCountUpdate($page);
        //  setTimeout(function() {
        sm4.activity.messageCountUpdate($page);
        //  }, 2000);
        //  setTimeout(function() {
        sm4.activity.requestCountUpdate($page);
        //  }, 3000);
      }
      var $page = $(event.target);
      var dashboardPanelId = $page.jqmData('dashboardpanelid') ? '#' + $page.jqmData('dashboardpanelid') : '#dashboardPanelMenu';
      var viewData = options.viewData;
      if (!sm4.core.isApp()) {
        // 			sm4.core.runonce.add(function() {
        // @todo integrate this into the composer
        if (DetectMobileQuick() && DetectIpad()) {

          // Set a timeout...
          setTimeout(function() {
            // Hide the address bar!
            window.scroll(0, 1);
          }, 0);
        }

        // 	});

        if ($('.sm_startup_screen').length > 0) {
          var delay = (!data.prevPage.attr('id') && !(viewData.smoothboxClose || viewData.parentRefresh || viewData.refresh || viewData.redirect || viewData.parentRedirect)) ? 1000 : 10;
          setTimeout((function() {
            $('.sm_startup_screen').remove();
            $page.removeClass('p_fixed');
          }), delay);
        } else {
          if ($page.hasClass('p_fixed'))
            setTimeout((function() {
              if ($page.hasClass('p_fixed'))
                $page.removeClass('p_fixed');
            }), 1000);
        }
      }

      if ($page.find(dashboardPanelId)) {
        var minPanelEnabled = $page.find(".dashboard-panel-mini-menu").length;
        if (minPanelEnabled) {
          $page.addClass('ui-panel-iconbar-page');

          var iscroll = new IScroll($page.find(".dashboard-panel-mini-menu")[0], {
            probeType: 3, mouseWheel: true, preventDefault: false,
            onBeforeScrollStart: function(e) {
              e.preventDefault();
            }
          });
          if ($page.find(".dashboard-panel-mini-menu li.ui-btn-active").length)
            iscroll.scrollToElement($page.find(".dashboard-panel-mini-menu li.ui-btn-active")[0]);
          iscroll.on('scroll', function() {
            iscroll.options.preventDefault = true;
          });
          iscroll.on('scrollEnd', function() {
            iscroll.options.preventDefault = false;
          });

          $page.find(".dashboard-panel-mini-menu").on("swiperight", function(event) {
            if ($.mobile.activePage.jqmData("panel") !== "open") {
              event.preventDefault();
              $page.find(dashboardPanelId).panel("open");
            }
          });
        }

        $(dashboardPanelId).off('panelbeforeopen').on("panelbeforeopen", function(event, ui) {
          $.mobile.activePage.pscrollTop = $(document).scrollTop();
          $page.find('.sm_aaf_pullDown').addClass('sm_ui_panel_dnone');
          if (minPanelEnabled) {
            $page.find(".dashboard-panel-mini-menu").css('display', 'none');
            $page.removeClass('ui-panel-iconbar-page');
          }
          if (!sm4.core.isApp())
            $page.addClass('ui-responsive-panel-open');
        });

        $(dashboardPanelId).off('panelbeforeclose').on("panelbeforeclose", function(event, ui) {
          if (minPanelEnabled) {
            $page.find(".dashboard-panel-mini-menu").css('display', 'block');
            $page.addClass('ui-panel-iconbar-page');
          }

          if (!sm4.core.isApp()) {
            $page.removeClass('ui-responsive-panel-open');
            $.mobile.silentScroll();
          }
        }).off('panelclose').on('panelclose', function() {
          $page.find('.sm_aaf_pullDown').removeClass('sm_ui_panel_dnone');
          $.mobile.silentScroll($.mobile.activePage.pscrollTop);
          $.mobile.activePage.pscrollTop = 0;
        });

        /// problem of swaping the incase of tab and photos
        //        $page.on( "swipeleft swiperight",  function( e ) {
//
        //          if ( $.mobile.activePage.jqmData( "panel" ) !== "open" ) {
        //            if ( e.type === "swipeleft"  ) {
        //              $page.find( dashboardPanelId ).panel( "close" );
        //            } else if ( e.type === "swiperight" ) {
        //              $page.find( dashboardPanelId ).panel( "open" );
//            }
        //          }
        //        }); 
      }
      if ($page.jqmData('role') == 'page') {
        if (!!resetThePageHeigth) {
          $(window).off('resize', resetThePageHeigth);
          if ($page.attr('data-layout') === 'fixed') {
            $(document).off('focus', 'input, textarea', resetThePageHeigth);
            $(document).off('blur', 'input, textarea', resetThePageHeigth);
          }
        }
        resetThePageHeigth = function(event) {
          var minPageHeight = $.mobile.getScreenHeight(), difHeight = $page.find('[data-role="content"]').outerHeight() - $page.find('[data-role="content"]').height();

          if ($page.find('.ui-page-header-layout').length > 0) {
            minPageHeight = minPageHeight - $page.find('.ui-page-header-layout').outerHeight();
          }

          if ($page.find('.ui-page-footer-layout').length > 0) {
            minPageHeight = minPageHeight - $page.find('.ui-page-footer-layout').outerHeight();
          }

          minPageHeight = minPageHeight - difHeight;
          if ($page.attr('data-layout') === 'fixed') {
            var $wrapper = $page.find('[data-role="wrapper"]');
            if ($wrapper.find('.layout_top').length || $wrapper.find('.layout_bottom').length) {
              var $top = $wrapper.find('.layout_top').length ? $wrapper.find('.layout_top') : $wrapper.find('.layout_bottom'), $maincontent = $wrapper.find('.layout_main');
              var padding = $top.outerHeight(true) + 2, top = 0;
              if ($wrapper.find('.layout_top').length) {
                if ($wrapper.find('.layout_top .layout_middle').length) {
                  if ($page.find('.ui-page-header-layout').length > 0) {
                    padding = padding + $page.find('.ui-page-header-layout').outerHeight() + 10;
                    top = $page.find('.ui-page-header-layout').outerHeight() - 2;
                  }
                  $top.css('top', top + 'px');
                  $page.css("paddingTop", padding + 'px');
                }
              } else {
                if ($page.find('.ui-page-footer-layout').length > 0) {
                  padding = padding + $page.find('.ui-page-footer-layout').outerHeight() + 10;
                  top = $page.find('.ui-page-footer-layout').outerHeight() - 2;
                }
                $page.css("paddingBottom", padding + 'px');
                $top.css('bottom', top + 'px');
              }
              $top.addClass('layout_top_fixed');

              var diffH = 20;
//              var contentHeight = minPageHeight - $top.outerHeight(true) - diffH;
//              if (contentHeight > 100) {
//                $maincontent.css("height", contentHeight);
//                $maincontent.css('overflowY', 'auto');
//                $maincontent.css('overflowX', 'hidden');
//              }
            }
            if ((!!event && event.type === 'focusout')) {
              focusinpage = false;
            }

            if (!!event && event.type === 'focusin') {
              focusinpage = true;
              $page.find('[data-content="main"]').css("height", minPageHeight);
            } else if (focusinpage === true && !!event && event.type === 'resize') {
              $page.find('[data-content="main"]').css("height", minPageHeight);
            } else {
              $page.find('[data-content="main"]').css("height", 'auto');
            }

          } else {
            $page.find('[data-content="main"]').css("min-height", minPageHeight);
          }
          var $dashboardPanel = $(dashboardPanelId);
          if ($dashboardPanel.length > 0 && $dashboardPanel.find('.layout_top').length > 0) {
            var $top = $dashboardPanel.find('.layout_top'), panelmiddle = $dashboardPanel.find('.layout_main');
            var diffH = $dashboardPanel.hasClass('ui-panel-display-overlay') ? 47 : 5;
            var panelHieght = $.mobile.getScreenHeight(true) - $top.outerHeight(true) - diffH;
            if (panelHieght > 100) {
              panelmiddle.css("height", panelHieght);
              panelmiddle.css('overflowY', 'auto');
              panelmiddle.css('overflowX', 'hidden');
            }
          }
          if ($page.attr("id") === 'jqm_page_sitemobile-browse-browse') {
            var pageWidth = $page.width(), widthOfIcon = 80, noOfIconInRow = parseInt(pageWidth / widthOfIcon);
            if (noOfIconInRow > 8)
              noOfIconInRow = 8;
            $page.find('.navigation_dashboard_grid').css("max-width", noOfIconInRow * widthOfIcon);
          }
          $.mobile.activePage.find('.iscroll_wapper').css('width', $('body').width() - 25 + 'px');

          sm4.activity.activityUpdateHandler.scrollRefresh();
        };
        resetThePageHeigth();
        $(window).on('resize', resetThePageHeigth);
        if ($page.attr('data-layout') === 'fixed') {
          $(document).on('focus', 'input, textarea', resetThePageHeigth);
          $(document).on('blur', 'input, textarea', resetThePageHeigth);
        }
      }

      $.mobile.activePage.find('.iscroll_container').not('.iscroller').each(function() {
        $(this).closest('.iscroll_wapper').css('width', $('body').width() - 25 + 'px');
        var width = 0, height = 0, margin = 10;

        $(this).find('.iscroll_item').each(function() {
          if ($(this).jqmData('margin'))
            margin = $(this).jqmData('margin');
          width = width + ($(this).outerWidth() + margin);
          if (height < ($(this).outerWidth()))
            height = $(this).outerHeight();
        });
        $(this).css('width', width + 'px');

        if ($(this).find('.iscroll_item').length > 1) {
          var $this = $(this).closest('.iscroll_wapper')[0];
          setTimeout(function() {
            new IScroll($this, {
              scrollX: true,
              scrollY: false,
              preventDefault: false,
              momentum: false,
              snap: true,
              snapSpeed: 100,
              keyBindings: true
            });
          }, 500);

        }
        $(this).addClass('iscroller');
      });
    });

    if (!this.isApp()) {
      $('body').ready(function() {
        sm4.core.Module.init(options.requestInfo, $(document).find('#jqm_' + options.requestInfo.contentType + '_' + options.requestInfo.id), options.viewData);
        sm4.core.runonce.trigger();
      });
    }
    //    $(document).bind( "pageinit", function( event, data ) {     
    //       
    //    });

    $(document).bind("pagecreate", function(event, data) {
//      var $page = $( event.target );
      sm4.core.Module.setContent($(event.target));
//      if(sm4.core.isApp() && device.platform === "Android" && $page.find('.ui-header').length && $page.find('.ui-header').find('[data-rel="back"]').length){
//        $page.find('.ui-header').find('[data-rel="back"]').addClass('dnone');
//      }
    });
    $(document).on('refreshcontent', function(event, data) {
      sm4.core.reset(data);
      sm4.core.cache.clear(true);
    });

    $(document).on('afteruserlogin', function(event, data) {
      sm4.core.cache.clear(true);
    });

    $(document).on('afteruserlogout', function(event, data) {
      sm4.core.cache.clear(true);
    });

    $(document).scroll(function(event) {
      if (sm4.core.isApp()) {
        var $scrollbutton = $.mobile.activePage.find('[data-role="scroll_top"]');
        if ($scrollbutton) {
          var player_pause_button = $.mobile.activePage.find('[data-role="player_pause"]');
          if (player_pause_button.css('display') != 'block' && $(document).scrollTop() > ($.mobile.getScreenHeight() * 3) && $scrollbutton.hasClass('dnone')) {
            $scrollbutton.removeClass('dnone');
            $scrollbutton.off('vclick').on('vclick', function() {
              $.mobile.silentScroll();
              $scrollbutton.addClass('dnone');
              $scrollbutton.off('vclick');
            });

          } else if (!$scrollbutton.hasClass('dnone') && $(document).scrollTop() < ($.mobile.getScreenHeight() * 3)) {
            $scrollbutton.addClass('dnone');
          }
        }
        if ($.mobile.activePage.length) {
          var $page = $.mobile.activePage, height = $page.outerHeight() - $.mobile.getScreenHeight() - 10;
          if ($page.css('paddingBottom')) {
            height = height - parseInt($page.css('paddingBottom').replace('px', ''));
          }
          var top = 0, maxTop = 20;
          if ((height > 0) && height <= $(document).scrollTop()) {
            if ($.mobile.activePage.find(".paginationViewmore #view_more").length == 1) {
              $.mobile.activePage.find(".paginationViewmore #view_more a").click();
            }
            $.mobile.activePage.trigger('upscroll', event);
          }
          if (($(document).scrollTop() < maxTop) && (top < $(document).scrollTop())) {
            $.mobile.activePage.trigger('downscroll', event);
          }

          if ($.mobile.activePage.find('.layout_top_fixed').length) {
            var fixContent = $.mobile.activePage.find('.layout_top_fixed');
            var previousScrollTop = fixContent.attr('scrollTop');
            previousScrollTop = previousScrollTop ? previousScrollTop : 0;
            var isRevers = previousScrollTop > $(document).scrollTop();
            if (fixContent.hasClass('layout_top')) {
              if (!isRevers && (parseInt(fixContent.css('top').replace('px', '')) + fixContent.outerHeight(true)) < $(document).scrollTop()) {
                if (fixContent.attr('fade') !== 'out') {
                  fixContent.fadeOut();
                  fixContent.attr('fade', 'out');
                }
              } else {
                if (fixContent.attr('fade') !== 'in') {
                  fixContent.fadeIn();
                  fixContent.attr('fade', 'in');
                }

              }
            }
            fixContent.attr('scrollTop', $(document).scrollTop());
          }
        }
      }

    });
  },
  /* */
  language: {
    options: {
      locale: 'en',
      defaultLocale: 'en'
    },
    data: {},
    init: function(data) {
      this.data = {};
      this.addData(data);
    },
    addData: function(data) {
      if (typeof data == 'string') {
        try {
          data = $.parseJSON(data);
        } catch (e) {
          return;
        }
      }
      if (typeof data != 'object') {
        return;
      }
      for (var key in data) {
        if (typeof key == 'string')
          this.data[key] = data[key];
      }
    },
    translate: function(key, count) {
      try {
        if (arguments.length < 1) {
          return '';
        }
        var convert = null;
        if (typeof key == 'string' && 'string' == typeof this.data[key] && arguments.length == 1) { // For Simple String Translate
          convert = this.data[key];
        } else if (typeof key == 'string' && arguments.length == 2) { // For Plural Type String Translate
          convert = this.data[key];
          if (typeof convert == 'object' || typeof convert == 'array') {
            convert = (count == 1) ? convert[0] : convert[1];
          } else if (convert == null) {
            convert = key;
          }
          convert = convert.replace(/%1\$s/g, count).replace(/%s/g, count).replace(/%%/g, 'SM_PER').replace(/%/g, count).replace('SM_PER', '%');
        } else if (arguments.length >= 2) { // For More then one arguments in String Translate
          convert = this.data[key];
          for (var i = 1; i < arguments.length; i++) {
            var reg = new RegExp('%' + i + '\\$s', 'gi');
            convert = convert.replace(reg, arguments[i]);
          }
        } else {
          convert = key;
        }
        return convert;
      } catch (e) {
        if (sm4.core.isDevelopmentMode())
          console.log(key + ":-" + e);
      }
    }
  },
  /* */
  locale: {
    format: function(name) {
      return  $('locale').attr(name);
    }
  },
  cache: {
    clear: function(force) {
      // if($(document).data('hasCache')){
      var $page = $.mobile.activePage;
      setTimeout(function() {
        if ($page != $.mobile.activePage && !$page.hasClass('ui-page-active')) {
          $page.remove();
        }
      }, 2000);
      $('.ui-page').not('.ui-page-active').each(function() {
        if (!$(this).jqmData('dom-cache') || force) {
          $(this).remove();
        }
      });
      $(document).data('hasCache', 0);
      if ($.mobile.firstPage && $.mobile.firstPage[0]) {
        $.mobile.firstPage = $('.ui-page').not('.ui-page-active');
      }
      //  }
    }
  },
  refreshPage: function() {
    sm4.core.dloader.refreshPage();
  },
  reloadPage: function() {
//    if (sm4.core.isApp() && !window.phonegap.checkOnlineMode())
//      return;
    $.mobile.changePage($.mobile.navigate.history.getActive().url, {
      reloadPage: true,
      showLoadMsg: false
    });
  },
  showError: function(error) {
    var $popup = $(error);
    $.mobile.activePage.append($popup);
    $popup.popup();
    $popup.popup('open');
    $popup.on("popupafterclose", function() {
      $popup.remove();
    });
  },
  showFlashMessage: function(msg, delay) {
    $.mobile.loading().loader('show', 'a', msg, true);
    setTimeout(function() {
      $.mobile.loading().loader('hide');
    }, delay);
  },
  tab: {
    setScroll: function(id) {
      var width = 0;
      var $this = $.mobile.activePage.find('#' + id);
      if ($this.length < 1) {
        setTimeout(function() {
          sm4.core.tab.setScroll(id);
        }, 250);
        return;
      }
      setTimeout(function() {
        var liActiveEl = '';
        $this.find('li').each(function() {
          if ($(this).hasClass('active'))
            liActiveEl = this;
          width = width + ($(this).outerWidth());
        });
        width = width + 1;
        $this.find('ul').first().css('width', width + 'px');
        var iscroll = new IScroll($this[0], {
          eventPassthrough: true,
          scrollX: true,
          scrollY: false,
          preventDefault: false,
        });
        if (liActiveEl)
          iscroll.scrollToElement(liActiveEl);
      }, 1500);
      //      $this.on('touchmove', function (e) {
      //        e.preventDefault();
      //      }, false);
    }
  },
  map: {
    initialize: function(id, options) {
      var latitude = options.latitude, longitude = options.longitude;
      var mapLatlng = new google.maps.LatLng(latitude, longitude);

      var mapOptions = {
        zoom: options.zoom ? options.zoom : 10,
        center: mapLatlng,
        //  navigationControl: true,
        mapTypeId: google.maps.MapTypeId.ROADMAP
      }
      var map = new google.maps.Map(document.getElementById(id), mapOptions);
      google.maps.event.addListener(map, 'click', function() {
        google.maps.event.trigger(map, 'resize');
        map.setZoom(options.zoom ? options.zoom : 10);
        map.setCenter(mapLatlng);
      });
      if (options.marker) {
        var marker = new google.maps.Marker({
          position: mapLatlng,
          map: map,
          title: options.title ? options.title : ''
        });
      }
    }
  },
  mobiPageHTML: function(responseHTML) {

    var temp = $('<div style="display:none"/>').html(responseHTML)
    $.mobile.activePage.append(temp);
    temp.trigger('create');
    responseHTML = temp.html();
    temp.remove();
    return responseHTML;
  }
};
/**
 * Run Once scripts
 */
sm4.core.runonce = {
  executing: false,
  fns: [],
  add: function(fn) {
    this.fns.push(fn);
  },
  trigger: function() {
    if (this.executing)
      return;
    this.executing = true;
    sm4.core.loadImageLazy();
    var fn;
    while ((fn = this.fns.shift())) {
      try {
        fn();
      } catch (e) {
      }
    }
    this.fns = [];
    this.executing = false;
  }

};
/**
 * Run Once scripts for closing popup or window
 */
sm4.core.closefunctions = {
  executing: false,
  fns: [],
  add: function(fn) {
    this.fns.push(fn);
  },
  trigger: function() {
    if (this.executing)
      return;
    this.executing = true;
    //sm4.core.loadImageLazy();
    var fn;
    while ((fn = this.fns.shift())) {
      try {
        fn();
      } catch (e) {
      }
    }
    this.fns = [];
    this.executing = false;
  }

};
/**
 * shutdown scripts
 */
sm4.core.shutdown = {
  executing: false,
  fns: [],
  add: function(fn) {
    this.fns.push(fn);
  },
  trigger: function() {
    if (this.executing)
      return;
    this.executing = true;
    var fn;
    while ((fn = this.fns.shift())) {
      try {
        fn();
      } catch (e) {
      }
    }
    this.fns = [];
    this.executing = false;
  }

};
sm4.core.libraries = {
  init: function() {
    this.addCapitalize();
    this.stripScripts();
    this.removeValueFromArray();
  },
  addCapitalize: function() {
    String.prototype.capitalize = function() {
      return this.charAt(0).toUpperCase() + this.slice(1);
    }
  },
  stripScripts: function() {
    String.prototype.stripScripts = function() {
      var div = $('<div></div>');
      div.get(0).innerHTML = this;
      div.find('script').remove();
      return div.get(0).innerHTML;
    }
  },
  removeValueFromArray: function() {
    Array.prototype.erase = function(v) {
      for (var i in this) {
        if (this[i].toString() == v.toString()) {
          this.splice(i, 1)
        }
      }
    }
  },
  strToDate: function(str, seprator) {
    var value = str.split(seprator);
    if (sm4.core.isApp())
      var dateFormate = ('mm/dd/yy').split(seprator);
    else
      var dateFormate = sm4.core.locale.format('date').split(seprator);
    var dateArray = {};
    for (var i = 0; i < dateFormate.length; i++) {
      dateArray[dateFormate[i].toLowerCase()] = value[i];
    }
    return new Date([dateArray['mm'], dateArray['dd'], dateArray['yy']].join(seprator));
  },
  autocomplete: function() {

  }
};

sm4.core.loadImageLazy = function(container) {
  $(".lazy").lazyload({
    //  container: container ? container : $.mobile.activePage,
    skip_invisible: false
  });
  $(".lazy").addClass('lazy_default').removeClass('lazy');
};

/**
 * Dynamic load scripts
 */
sm4.core.dloader = {
  enabled: false,
  init: function(options) {
    $(document).data('loaded', true);
    this.attachEvents();
  },
  refreshPage: function() {
    $.mobile.activePage.trigger('create');
  },
  attachEvents: function() {

    var self = this;
    if (sm4.core.isApp()) {
      $.mobile.document.off('click').on("click", function(event) {
        var path = $.mobile.path, findClosestLink = function(ele)
        {
          while (ele) {
            if ((typeof ele.nodeName === "string") && ele.nodeName.toLowerCase() === "a") {
              break;
            }
            ele = ele.parentNode;
          }
          return ele;
        }, getClosestBaseUrl = function(ele)
        {
          // Find the closest page and extract out its url.
          var url = $(ele).closest(".ui-page").jqmData("url"),
                  base = path.getDocumentBase().hrefNoHash;

          if (!url && $.mobile.activePage) {
            url = $.mobile.activePage.jqmData("url");
          }
          if (!url || !path.isPath(url)) {
            url = base;
          }
          return path.makeUrlAbsolute(url, base);
        };
        var link = findClosestLink(event.target), $link = $(link), httpCleanup;

        if (!link || event.which > 1 || !$link.jqmHijackable().length) {
          return;
        }
        var dashboardPanelId = $.mobile.activePage.jqmData('dashboardpanelid') ? '#' + $.mobile.activePage.jqmData('dashboardpanelid') : '#dashboardPanelMenu';
        if ($.mobile.activePage.jqmData("panel") === "open" && $.mobile.activePage.find(dashboardPanelId)) {
          $.mobile.activePage.find(dashboardPanelId).panel("close");

        }
        //if there's a data-rel=back attr, go back in history
        if ($link.is(":jqmData(rel='back')")) {
          $.mobile.back();
          return false;
        }
        var baseUrl = getClosestBaseUrl($link), documentUrl = $.mobile.path.documentUrl,
                href = path.makeUrlAbsolute($link.attr("href") || "#", baseUrl);

        if (href.search("#") !== -1) {
          href = href.replace(/[^#]*#/, "");
          if (!href) {
            event.preventDefault();
            return;
          } else if (path.isPath(href)) {
            href = path.makeUrlAbsolute(href, baseUrl);
          } else {
            href = path.makeUrlAbsolute("#" + href, documentUrl.hrefNoHash);
          }
        }
        if (href.search("javascript") !== -1 || href.search("mailto:") !== -1) {
          return;
        }

        var isSameDomainUrl = function(url) {
          var documentUrl = path.parseUrl(window.phonegap.smappcore.host.baseHref);
          var u = path.parseUrl(url);
          if (u.protocol === "file:")
            return true;
          return u.protocol && u.domain === documentUrl.domain ? true : false;
        };
        var useDefaultUrlHandling = $link.is("[rel='external']") || $link.is(":jqmData(ajax='false')") || $link.is("[target]"),
                isExternal = useDefaultUrlHandling || !isSameDomainUrl(href) || (path.isExternal(href) && !path.isPermittedCrossDomainRequest(documentUrl, href));
        if (!isExternal) {
          var templatename, templateKey;
          $.each(AppTemplates.routes, function(routeSample, templateName) {
            if (href.search(routeSample) !== -1) {
              templatename = templateName;
              templateKey = routeSample;
              return false;
            }
          });
          //use ajax
          var transition = $link.jqmData("transition"),
                  reverse = $link.jqmData("direction") === "reverse" ||
                  // deprecated - remove by 1.0
                  $link.jqmData("back"),
                  //this may need to be more specific as we use data-rel more
                  role = $link.attr("data-" + $.mobile.ns + "rel") || undefined;

          if (!!templatename && !!templateKey) {
            AppTemplates.setTemplateContent(templatename, templateKey, {href: href, transition: transition, reverse: reverse, role: role, link: $link});
          } else {
            $.mobile.changePage(href, {transition: transition, reverse: reverse, role: role, link: $link});
          }
        } else {

          window.setTimeout(function() {
            $link.removeClass($.mobile.activeBtnClass);
          }, 200);
          if ($link.is(":jqmData(download='true')")) {
            smappcore.savePhoto(href);
          } else if (href.indexOf("/auth/facebook") >= 0 || href.indexOf("/auth/twitter") >= 0) {
            SoicalLoginApp.open(href);
          } else {
            var target = 'blank';
            if ($link.is("[target]") && $link.attr("target") === '_system') {
              target = 'system';
            }
            sm4.core.AppBrowser.open(href, {type: target});
          }
        }
        event.preventDefault();
      });

    }
    //  $(document).bind('ready',function(){
    // Listen for any attempts to call changePage().
    $(document).on("pagebeforechange", function(event, data) {
      $.mobile.loading().loader("hide");
    });

    $(document).on("pagecontainerbeforeload", function(event, data) {
      var dashboardPanelId = $.mobile.activePage.jqmData('dashboardpanelid') ? '#' + $.mobile.activePage.jqmData('dashboardpanelid') : '#dashboardPanelMenu';
      if ($.mobile.activePage.jqmData("panel") === "open" && $.mobile.activePage.find(dashboardPanelId)) {
        $.mobile.activePage.find(dashboardPanelId).panel("close");
      }
      if (sm4.core.isApp() && !window.phonegap.checkOnlineMode()) {
        event.preventDefault();
        navigator.notification.alert(sm4.core.language.translate('Please try later'), function() {
        }, sm4.core.language.translate('Connection Problems'));
        data.deferred.reject($.mobile.path.getFilePath(data.absUrl));
        return;
      }
      //sm4.core.cache.clear();
      if (!($.mobile.allowCrossDomainPages || $.mobile.path.isSameDomain($.mobile.path.documentUrl, data.absUrl))) {
        return;
      }
      if (data.options.link && data.options.link.jqmData('linktype') === 'photo-gallery') {
        data.deferred.reject(data.absUrl, data.options);
        event.preventDefault();
        if ($(document).data('loaded') || data.options.prefetch) {
          $(document).data('loaded', false);
          sm4.core.photoGallery.process(data.absUrl);
        }
        return;
      }

      event.preventDefault();
      if ($(document).data('loaded') || data.options.prefetch) {
        $(document).data('loaded', false);
        self.request.send($.mobile.path.getFilePath(data.absUrl), data);
      }
      return data.deferred.promise();

    });

    $(document).on("pagechange", function(e, data) {
    });
    $(document).on("pagechangefailed", function(e, data) {

    });
    //   });
  },
  request: {
    send: function(url, reqData) {
      var reqOptions = reqData.options;
      if (reqOptions.link && reqOptions.link.hasClass('smoothbox')) {
        reqData.options.role = 'dialog';
      }

      if (reqOptions.showLoadMsg) {
        if (reqOptions.link && !!!reqOptions.link.jqmData('loadersmall') && !!$.mobile.loader.prototype.showpage && reqData.options.role !== 'dialog' && $.mobile.activePage.jqmData('role') != 'dialog') {
          var heading = sm4.core.language.translate('SITEMOBILE_LOADING_PAGE_MESSAGE');
          if (reqOptions.link && reqOptions.link.text()) {
            if (reqOptions.link.text() === reqOptions.link.html())
              heading = reqOptions.link.text();
            else if (!!reqOptions.link.jqmData('title'))
              heading = reqOptions.link.jqmData('title');
            else if (!!reqOptions.link.attr('title'))
              heading = reqOptions.link.attr('title');
            else if (reqOptions.link.find('h3').length)
              heading = reqOptions.link.find('h3').text();
            else if (reqOptions.link.find('.content').length)
              heading = reqOptions.link.find('.content').text();
            else if (reqOptions.link.find('.tab-title').length)
              heading = reqOptions.link.find('.tab-title').text();
            else if (reqOptions.link.find('.forum-topic-listing-title').length)
              heading = reqOptions.link.find('.forum-topic-listing-title').text();
            else if (reqOptions.link.find('.p_list_grid_title').length)
              heading = reqOptions.link.find('.p_list_grid_title').text();
          }
          $.mobile.loading().loader("showpage", heading);
        } else {
          $.mobile.loading().loader("show");
        }
      }
      if (reqOptions.showLoadMsg) {

        // This configurable timeout allows cached pages a brief delay to load without showing a message
//        var loadMsgDelay = setTimeout(function() {
//          $.mobile.loading().loader("showpage");
//        }, 0);
        reqData.hideMsg = function() {

          // Stop message show timer
          //   clearTimeout(loadMsgDelay);

          // Hide loading message
          $.mobile.loading().loader("hide");
        };
      }


      // Reset base to the default document base.
      if ($.mobile.base) {
        $.mobile.base.reset();
      }
      //var $page = false;
      var req_params = false;

      var contentType = 'page';


      if (reqData.options.role) {
        contentType = reqOptions.role;
      }

      if (reqOptions && reqOptions.data) {
        if ($.type(reqOptions.data) === 'string') {
          req_params = reqOptions.data + '&formatType=smjson&contentType=' + contentType;
          if (reqOptions.link && reqOptions.link.jqmData('linktype') == 'photo-gallery') {
            req_params = req_params + '&photoGallery=1';
          }
          req_params = req_params + '&clear_cache=' + reqOptions.clear_cache;
        } else {
          req_params.formatType = 'smjson';
          req_params.contentType = contentType;
          req_params.clear_cache = reqOptions.clear_cache;
          if (reqOptions.link && reqOptions.link.jqmData('linktype') == 'photo-gallery') {
            req_params.photoGallery = 1;
          }
        }
      } else {
        req_params = {
          'formatType': 'smjson',
          'contentType': contentType,
          'clear_cache': reqOptions.clear_cache
        };
        if (reqOptions.link && reqOptions.link.jqmData('linktype') == 'photo-gallery') {
          req_params.photoGallery = 1;
        }
      }
      var self = this;
      var fileUrl = $.mobile.path.getFilePath(reqData.absUrl);
      if(fileUrl.indexOf("cometchat") > -1){
          window.open(encodeURI(fileUrl), '_blank', 'location=no');
      }
      $.ajax({
        type: reqOptions.type,
        url: fileUrl,
        data: req_params,
        dataType: "html",
        success: function(responseJSON, textStatus, xhr) {
          self.setOnSuccess(responseJSON, reqData, reqOptions, fileUrl, textStatus, xhr);
        },
        error: function(xhr, textStatus, errorThrown) {
          self.setError(xhr, textStatus, errorThrown, reqData, reqOptions);
        },
        statusCode: {
          404: function(response) {
            $(document).data('loaded', true);
          }
        }
      });
    },
    setOnSuccess: function(responseJSON, reqData, reqOptions, fileUrl, textStatus, xhr) {
      var self = this;

      $(document).data('loaded', true);
      try {
        var responseSplit = responseJSON.split('RESPONSE_HTML_SM');
        var responseHTML = responseSplit[responseSplit.length -1];
        responseJSON = responseJSON.replace( 'RESPONSE_HTML_SM' + responseHTML, '' );
        responseJSON = $.parseJSON(responseJSON);
        responseJSON.responseHTML = responseHTML;
        if (responseJSON.clear_cache) {
          sm4.core.cache.clear();
        }
        // Set the content 
        if (responseJSON.requestInfo.id == "core-utility-success") {
          if (reqOptions.showLoadMsg && !responseJSON.notSuccessMessage) {
            // Remove loading message.
            reqData.hideMsg();
          }
          reqData.deferred.reject(reqData.absUrl, reqOptions);
          if (!responseJSON.redirectTime)
            responseJSON.redirectTime = 1000;
          if (responseJSON && responseJSON.triggerEventsOnContentLoad) {
            $.each(responseJSON.triggerEventsOnContentLoad, function(key, value) {
              $(document).trigger(value, responseJSON);
            });
          }
          responseJSON.requestInfo.showLoadMsg = reqOptions.showLoadMsg;
          if (reqOptions.splashscreen)
            responseJSON.requestInfo.splashscreen = reqOptions.splashscreen;

          sm4.core.Module.core.utility.success(responseJSON.requestInfo, $.mobile.activePage, responseJSON);
        } else {
          if (reqOptions.splashscreen) {
            setTimeout(function() {
              navigator.splashscreen.hide();
            }, 1000);
          }
          var page = self.setData(responseJSON, reqData, fileUrl, textStatus, xhr);
          if (page) {
            //                if(page.attr('id')){
            //                  if($('.'+page.attr('id')).length > 1)
            //                    $('.'+page.attr('id')).not('.ui-page-active').remove();
            //                }
            sm4.core.Module.init(responseJSON.requestInfo, page, responseJSON);
            sm4.core.language.addData(responseJSON.responseLanguageData);
            self.evalScripts(responseJSON.responseScripts);
            sm4.core.runonce.trigger();
          }
        }
      } catch (errorThrown) {
        self.setError(xhr, textStatus, errorThrown, reqData, reqOptions);
        $(document).data('loaded', true);
        throw errorThrown;

      }
    },
    setError: function(xhr, textStatus, errorThrown, reqData, reqOptions) {
      //set base back to current path
      if ($.mobile.base) {
        $.mobile.base.set($.mobile.path.get());
      }

      // Add error info to our triggerData.
      reqOptions.xhr = xhr;
      reqOptions.textStatus = textStatus;
      reqOptions.errorThrown = errorThrown;

      if (sm4.core.isDevelopmentMode())
        console.log(reqOptions.errorThrown);

      var plfEvent = new $.Event("pageloadfailed");

      // Let listeners know the page load failed.
      $.mobile.pageContainer.trigger(plfEvent, reqData);

      // If the default behavior is prevented, stop here!
      // Note that it is the responsibility of the listener/handler
      // that called preventDefault(), to resolve/reject the
      // deferred object within the triggerData.
      if (plfEvent.isDefaultPrevented()) {
        return;
      }

      // Remove loading message.
      if (reqOptions.showLoadMsg) {
        // Remove loading message.
        $.mobile.loading().loader('hide');
      }

      // show error message 
      $.mobile.loading().loader('show', $.mobile.pageLoadErrorMessageTheme, $.mobile.pageLoadErrorMessage, true);
      setTimeout(function() {
        $.mobile.loading().loader('hide');
      }, 1500);
      reqData.deferred.reject(reqData.absUrl, reqOptions);
    },
    setData: function(data, reqData, fileUrl, textStatus, xhr) {
      if (typeof data != undefined) {
        reqData.options.pageContainer = (reqData.options.pageContainer || $.mobile.pageContainer);
        var html = data.responseHTML,
                path = $.mobile.path,
                page = reqData.options.pageContainer ? reqData.options.pageContainer.children("[data-" + $.mobile.ns + "url='" + reqData.dataUrl + "']") : null,
                dupCachedPage = null;
        if (page && page.length) {
          dupCachedPage = page;
        }
        //var page=this.
        //pre-parse html to check for a data-url,
        //use it as the new fileUrl, base path, etc
        var all = $("<div></div>"),
                //page title regexp
                newPageTitle = html.match(/<title[^>]*>([^<]*)/) && RegExp.$1,
                // TODO handle dialogs again
                pageElemRegex = new RegExp("(<[^>]+\\bdata-" + $.mobile.ns + "role=[\"']?page[\"']?[^>]*>)"),
                dataUrlRegex = new RegExp("\\bdata-" + $.mobile.ns + "url=[\"']?([^\"'>]*)[\"']?");


        // data-url must be provided for the base tag so resource requests can be directed to the
        // correct url. loading into a temprorary element makes these requests immediately
        if (pageElemRegex.test(html) &&
                RegExp.$1 &&
                dataUrlRegex.test(RegExp.$1) &&
                RegExp.$1) {
          fileUrl = path.getFilePath($("<div>" + RegExp.$1 + "</div>").text());
        }

        if (reqData.options.prefetch === undefined) {
          $.mobile.base.set(fileUrl);
        }


        //workaround to allow scripts to execute when included in page divs
        all.get(0).innerHTML = html;
        page = all.find(":jqmData(role='page'), :jqmData(role='dialog')").first();

        //if page elem couldn't be found, create one and insert the body element's contents
        if (!page.length) {
          page = $("<div data-" + $.mobile.ns + "role='page'>" + (html.split(/<\/?body[^>]*>/gmi)[1] || "") + "</div>");
        }
        page.attr("data-" + $.mobile.ns + "url", $.mobile.path.convertUrlToDataUrl(fileUrl))
                .attr("data-" + $.mobile.ns + "external-page", true);

        if (newPageTitle && !page.jqmData("title")) {
          if (~newPageTitle.indexOf("&")) {
            newPageTitle = $("<div>" + newPageTitle + "</div>").text();
          }
          page.jqmData("title", newPageTitle);
        }
        //  sm4.core.Module.setContent(page);
        //rewrite src and href attrs to use a base url
        if (!$.support.dynamicBaseTag && $.mobile.dynamicBaseEnabled) {
          var newPath = path.get(fileUrl);
          page.find("[src], link[href], a[rel='external'], :jqmData(ajax='false'), a[target]").each(function() {
            var thisAttr = $(this).is('[href]') ? 'href' :
                    $(this).is('[src]') ? 'src' : 'action',
                    thisUrl = $(this).attr(thisAttr);

            // XXX_jblas: We need to fix this so that it removes the document
            //            base URL, and then prepends with the new page URL.
            //if full path exists and is same, chop it - helps IE out
            thisUrl = thisUrl.replace(location.protocol + '//' + location.host + location.pathname, '');

            if (!/^(\w+:|#|\/)/.test(thisUrl)) {
              $(this).attr(thisAttr, newPath + thisUrl);
            }
          });
        }

        //append to page and enhance
        // TODO taging a page with external to make sure that embedded pages aren't removed
        //      by the various page handling code is bad. Having page handling code in many
        //      places is bad. Solutions post 1.0
        page
                .appendTo(reqData.options.pageContainer);
        //run page plugin
        page.page();
        // wait for page creation to leverage options defined on widget
        // page.one('pagecreate', $.mobile._bindPageRemove);
        // enhancePage( page, reqData.options.role );
        // If a role was specified, make sure the data-role attribute
        // on the page element is in sync.

        if (reqData.options.role) {
          page.page({role: reqData.options.role});
          page.attr("data-" + $.mobile.ns + "role", reqData.options.role);
        }

        page.page('bindRemove');
        // Enhancing the page may result in new dialogs/sub pages being inserted
        // into the DOM. If the original absUrl refers to a sub-page, that is the
        // real page we are interested in.
        if (reqData.absUrl.indexOf("&" + $.mobile.subPageUrlKey) > -1) {
          page = reqData.options.pageContainer.children("[data-" + $.mobile.ns + "url='" + dataUrl + "']");
        }

        // Remove loading message.
        if (reqData.options.showLoadMsg) {
          reqData.hideMsg();
        }

        // Add the page reference and xhr to our triggerData.
        reqData.xhr = xhr;
        reqData.textStatus = textStatus;
        reqData.page = page;
        reqData.content = page;

        // Let listeners know the page loaded successfully.
        reqData.options.pageContainer.trigger("pageload", reqData);
        reqData.deferred.resolve(reqData.absUrl, reqData.options, page, dupCachedPage);
        if (dupCachedPage) {
          $.mobile.changePage(page, {
            dataUrl: reqData.absUrl
          });
          dupCachedPage.remove();
        }
        return page;
      }
      return '';
    },
    evalScripts: function(scripts) {
      if (scripts) {
        var self = this;
        scripts = $('<div>' + scripts + '</div>');
        $.each(scripts.find("script"), function(key, script) {
          if (script.src) {
            self.loadScript(script.src, script.type, script.onload);
          } else {
            eval(script.innerHTML);
          }
        });

      }
    },
    loadScript: function(src, type, onload) {
      var script = document.createElement('script');
      script.type = type;
      if ('function' == typeof onload) {
        script.onreadystatechange = function() {
          if (this.readyState == 'complete')
            onload();
        }
        script.onload = onload;
      }
      script.src = src;
      document.getElementsByTagName('head')[0].appendChild(script);
    }

  }
};
/**
 * Request pipeline
 */
sm4.core.request = {
  activeRequests: [],
  isRequestActive: function() {
    return (this.activeRequests.length > 0);
  },
  send: function(req, options) {
    options = options || {};
    if (!$.type(options.force))
      options.force = false;

    // If there are currently active requests, ignore
    if (this.activeRequests.length > 0 && !options.force) {
      return this;
    }
    var dataType = req.dataType;
    if (options.showLoading) {
      $.mobile.loading().loader("show");
    }
    req = $.ajax(req);
    this.activeRequests.push(req);
    // OnComplete
    var bind = this;
    // Process options
    if ($.type(options.htmlJsonKey) == 'undefined')
      options.htmlJsonKey = 'body';

    if ($.type(options.element) !== 'undefined') {
      options.updateHtmlElement = options.element;
      options.evalsScriptsElement = options.element;
    }
    req.success(function(response, textStatus, xhr) {
      if (options.showLoading) {
        $.mobile.loading().loader("hide");
      }
      bind.activeRequests.erase(req);
      var htmlBody;
      var jsBody;

      // Get response
      if ($.type(response) == 'object') { // JSON response 
        htmlBody = response[options.htmlJsonKey];
      } else if ($.type(response) == 'string') { // HTML response
        htmlBody = response;

      }

      // An error probably occurred
      if (!response && $.type(options.updateHtmlElement)) {
        sm4.core.showError('An error has occurred processing the request. The target may no longer exist.');
        return;
      }
      //
      if ($.type(response) == 'object' && $.type(response.status) !== 'undefined' && response.status == false /* && $.type(response.error) */)
      {
        sm4.core.showError('An error has occurred processing the request. The target may no longer exist.' + '<br /><br /><button data-rel="back">Close</button>');
        return;
      }
      if ($.type(options.updateHtmlElement) !== 'undefined' && htmlBody) {
        var $htmlBody = $("<div></div>").html(htmlBody).children();
        if ($.type(options.updateHtmlMode) !== 'undefined' && options.updateHtmlMode == 'append') {
          $(options.updateHtmlElement).append($htmlBody);
        } else if ($.type(options.updateHtmlMode !== 'undefined') && options.updateHtmlMode == 'prepend') {
          $(options.updateHtmlElement).prepend($htmlBody.reverse());
        } else if ($.type(options.updateHtmlMode) !== 'undefined' && options.updateHtmlMode == 'comments' && $htmlBody && $htmlBody[1] && $htmlBody[1].find('.comments')) {
          $(options.updateHtmlElement).find('.comments').remove();
          $(options.updateHtmlElement).find('.feed_item_date').remove();
          if ($htmlBody[1].find('.feed_item_date'))
            $(options.updateHtmlElement.find('.feed_item_body')).append($htmlBody[1].find('.feed_item_date'));
          $(options.updateHtmlElement.find('.feed_item_body')).append($htmlBody[1].find('.comments'));
        } else {
          $(options.updateHtmlElement).empty();
          $(options.updateHtmlElement).append($htmlBody);
        }
        $(options.updateHtmlElement).trigger("create");
        if ($.type(options.doRefreshPage) == 'undefined' || !options.doRefreshPage) {
          sm4.core.refreshPage();
        }
      }
      if ($('.ps-popup-wapper').find('#photo-comment-form').length > 0) {
        sm4.core.photocomments.attachCreateComment($('.ps-popup-wapper').find('#photo-comment-form'));
      }
      if ($.type(options.doRunOnce) == 'undefined' || !options.doRunOnce) {
        sm4.core.runonce.trigger();
      }
    });
    req.error(function(jqXHR, textStatus, errorThrown) {

    });

    return this;
  }
};

/**
 * likes
 */
sm4.core.applikes = {
  like: function(type, id, show_bottom_post) {
    this.resetLikeUnlike('like', type, id);
    sm4.core.request.send({
      type: "POST",
      dataType: "json",
      url: sm4.core.baseUrl + 'core/comment/like',
      data: {
        format: 'json',
        type: type,
        id: id
      },
      success: function(responseJSON) {
        if ($.type(responseJSON) != 'object' || !responseJSON.status) {
          //this.resetLikeUnlike('unlike', type, id);

        }
      }.bind(this)
    }, {
      'force': true
    });
  },
  unlike: function(type, id) {
    this.resetLikeUnlike('unlike', type, id);
    sm4.core.request.send({
      type: "POST",
      dataType: "json",
      url: sm4.core.baseUrl + 'core/comment/unlike',
      data: {
        format: 'json',
        type: type,
        id: id
      },
      success: function(responseJSON) {
        if ($.type(responseJSON) != 'object' || !responseJSON.status) {
          // this.resetLikeUnlike('like', type, id);
        }
      }.bind(this)
    });
  },
  resetLikeUnlike: function(action, type, id) {
    if (action == 'like') {
      $.mobile.activePage.find(".ui-icon-thumbs-up-alt").removeClass("feed-like-icon feed-liked-icon");
      $.mobile.activePage.find(".ui-icon-thumbs-up-alt").addClass("feed-unlike-icon feed-unliked-icon");
      $.mobile.activePage.find(".ui-btn-likeunlike").attr('onclick', 'sm4.core.applikes.unlike("' + type + '",' + id + ' )');
      var likecount = 1;
    } else {
      $.mobile.activePage.find('#likecomment-' + id).find(".ui-icon-thumbs-up-alt").addClass("feed-like-icon feed-liked-icon");
      $.mobile.activePage.find('#likecomment-' + id).find(".ui-icon-thumbs-up-alt").removeClass("feed-unlike-icon feed-unliked-icon");
      $.mobile.activePage.find('#likecomment-' + id).find(".ui-btn-likeunlike").attr('onclick', 'sm4.core.applikes.like("' + type + '",' + id + ' )');
      var likecount = -1;
    }
    try {
      var likeCount = $.mobile.activePage.find('#likecomment-' + id).find('span.profileLikes').html();
      var m = likeCount.match(/\d+/);
      var newCount = (parseInt(m[0]) != 'NaN' ? parseInt(m[0]) + (likecount) : 0);

      likeCount = likeCount.replace(m[0], newCount);
      $.mobile.activePage.find('#likecomment-' + id).find('span.profileLikes').html(likeCount);
    } catch (e) {
    }
  }
}

sm4.core.likes = {
  like: function(type, id, show_bottom_post) {
    this.resetLikeUnlike('like', type, id);
    sm4.core.request.send({
      type: "POST",
      dataType: "json",
      url: sm4.core.baseUrl + 'core/comment/like',
      data: {
        format: 'json',
        type: type,
        id: id
      },
      success: function(responseJSON) {
        if ($.type(responseJSON) != 'object' || !responseJSON.status) {
          //this.resetLikeUnlike('unlike', type, id);
        }
      }.bind(this)
    });
  },
  unlike: function(type, id) {
    this.resetLikeUnlike('unlike', type, id);
    sm4.core.request.send({
      type: "POST",
      dataType: "json",
      url: sm4.core.baseUrl + 'core/comment/unlike',
      data: {
        format: 'json',
        type: type,
        id: id
      },
      success: function(responseJSON) {
        if ($.type(responseJSON) != 'object' || !responseJSON.status) {
          //this.resetLikeUnlike('like', type, id);
        }
      }.bind(this)
    });
  },
  resetLikeUnlike: function(action, type, id) {
    if ($("#" + type + '_' + id + 'unlike_link'))
      $("#" + type + '_' + id + 'unlike_link').css('display', action == 'unlike' ? 'none' : 'block');
    if ($("#" + type + '_' + id + 'like_link'))
      $("#" + type + '_' + id + 'like_link').css('display', action == 'like' ? 'none' : 'block');
  }

};


/**
 * Comments
 */
sm4.core.comments = {
  preloadCommentsString: '',
  PreloadCommentRequest: false,
  options: {
    self: false
  },
  loadComments: function(type, id, page) {
    sm4.core.request.send({
      type: "POST",
      dataType: "html",
      url: sm4.core.baseUrl + 'core/comment/list',
      data: {
        format: 'html',
        type: type,
        id: id,
        page: page
      }
    }, {
      element: $.mobile.activePage.find('#comments')
    });
  },
  loadReplies: function(type, id, page, comment_id) {
    sm4.core.request.send({
      type: "POST",
      dataType: "html",
      url: sm4.core.baseUrl + 'nestedcomment/reply/view',
      data: {
        format: 'html',
        type: type,
        id: id,
        page: page,
        comment_id: comment_id
      }
    }, {
      element: $('#replies_'+comment_id)
    });
  },
  preloadComments: function(type, id) {
    sm4.core.comments.preloadCommentsString = "<div class='ps_loading sm-ui-popup-loading'></div>";
    sm4.core.request.send({
      type: "POST",
      dataType: "html",
      url: sm4.core.baseUrl + 'core/comment/list',
      data: {
        format: 'html',
        type: type,
        id: id,
        'getLikeUsers': 1
      },
      success: function(responseHTML, textStatus, xhr) {
        sm4.core.comments.preloadCommentsString = responseHTML;
        sm4.core.comments.PreloadCommentRequest = true;
        sm4.core.runonce.trigger();
      }
    }, {
      'force': true
    });
  },
  attachCreateComment: function(formElement) {

      var bind = this;
      if($('.cm-icon-emoticons'))
      $('.cm-icon-emoticons').removeClass('active');
      formElement.attr('data-ajax', 'false');
      formElement.css('display', 'block');
      var photo_id, type;
      
      var form_values = formElement.serialize();
      form_values += '&format=json';
      form_values += '&id=' + $("[name='identity']", formElement).val();
     
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
      
      if ($("[name='body']", formElement).val() == '' && photo_id == 0 && tagfriends == 0)
        return;
      if(tagfriends != 0)
      form_values += '&toValues=' + tagfriends;
      $.mobile.loading().loader("show");
      sm4.core.request.send({
        type: "POST",
        dataType: "json",
        url: sm4.core.baseUrl + 'core/comment/create',
        data: form_values,
        success: function(responseJSON, textStatus, xhr) {
          $.mobile.loading().loader("hide");
          sm4.core.dloader.refreshPage();
          sm4.core.runonce.trigger();
          sm4.activity.resetAdvFeed();
          if($.type('#adv_comment_post_container_tagging') != 'undefined')
          $('#adv_comment_post_container_tagging').css('display', 'none');
          if($.type('#ui-post-comment-header-addpeople') != 'undefined')
          $('#ui-post-comment-header-addpeople').css('display', 'none');
        }.bind(this) 
      }, {
        element: $.mobile.activePage.find('#comments')
      });

   // });
  },
  attachCreateReply: function(formElement) {
      var bind = this;
      if($('.cm-icon-emoticons'))
      $('.cm-icon-emoticons').removeClass('active');
      formElement.attr('data-ajax', 'false');
      formElement.css('display', 'block');
      var photo_id, type;
      
      var form_values = formElement.serialize();
      form_values += '&format=json';
      form_values += '&id=' + $("[name='identity']", formElement).val();
     
      var photo_id, type;
      
      if($.type($("[name='attachment[photo_id]']", formElement).val()) == 'undefined') {
          photo_id = 0;
      } else {
          photo_id = $("[name='attachment[photo_id]']", formElement).val();
      }
      if($.type($("[name='attachment[type]']", formElement).val()) == 'undefined') {
          photo_type = 0;
      } else {
          photo_type = $("[name='attachment[type]']", formElement).val();
      }
      
      var tagfriends = 0;
      if($.type($('#toReplyValues_'+$("[name='comment_id']", formElement).val())) != 'undefined' && $('#toReplyValues_'+$("[name='comment_id']", formElement).val()).val()) {
          tagfriends = $('#toReplyValues_'+$("[name='comment_id']", formElement).val()).val();
      }
      if ($("[name='body']", formElement).val() == '' && photo_id == 0 && tagfriends == 0)
        return;
    
      if(tagfriends != 0)
      form_values += '&toValues=' + tagfriends;
      
      $.mobile.loading().loader("show");
      sm4.core.request.send({
        type: "POST",
        dataType: "json",
        url: sm4.core.baseUrl + 'nestedcomment/reply/create',
        data: form_values,
        success: function(responseJSON, textStatus, xhr) {
          $.mobile.loading().loader("hide");
          $('#reply_list_' + responseJSON.comment_id).find('#reply_link').find('.feed_replies').find('span').html(responseJSON.count);
          $('#reply_list_' + responseJSON.comment_id).css('display','');
          $('#activitypost-container-temp').remove();
          sm4.core.dloader.refreshPage();
          sm4.core.runonce.trigger();
          sm4.activity.resetAdvFeed();
          if($.type('#adv_reply_post_container_tagging_'+responseJSON.comment_id) != 'undefined')
          $('#adv_reply_post_container_tagging_'+responseJSON.comment_id).css('display', 'none');
          if($.type('#ui-post-reply-header-addpeople_'+responseJSON.comment_id) != 'undefined')
          $('#ui-post-reply-header-addpeople_'+responseJSON.comment_id).css('display', 'none');
        }.bind(this) 
      }, {
        element: $('#replies_'+ $("[name='comment_id']", formElement).val())
      });
      
  },
  attachEdit: function(formElement) {
      var bind = this;
      if($('.cm-icon-emoticons'))
      $('.cm-icon-emoticons').removeClass('active');
      formElement.attr('data-ajax', 'false');
      formElement.css('display', 'block');
      var photo_id, type;
      
      var form_values = formElement.serialize();
      form_values += '&format=json';
      form_values += '&id=' + $("[name='identity']", formElement).val();
     
      var photo_id, type;
      
      if($.type($("[name='attachment[photo_id]']", formElement).val()) == 'undefined') {
          photo_id = 0;
      } else {
          photo_id = $("[name='attachment[photo_id]']", formElement).val();
      }
      if($.type($("[name='attachment[type]']", formElement).val()) == 'undefined') {
          photo_type = 0;
      } else {
          photo_type = $("[name='attachment[type]']", formElement).val();
      }
      
      var tagfriends = 0;
      if($.type($('#toEditValues').val()) != 'undefined' && $('#toEditValues').val()) {
          tagfriends = $('#toEditValues').val();
      }
      
      if ($("[name='body']", formElement).val() == '' && photo_id == 0 && tagfriends == 0)
        return;
      if(tagfriends != 0)
      form_values += '&toValues=' + tagfriends;
      $.mobile.loading().loader("show");
      sm4.core.request.send({
        type: "POST",
        dataType: "json",
        url: sm4.core.baseUrl + 'nestedcomment/reply/edit',
        data: form_values,
        success: function(responseJSON, textStatus, xhr) {
          $.mobile.loading().loader("hide");
          $('#'+$.mobile.activePage.attr('id')).removeClass('dnone');
          if(responseJSON.perform == 'comment-edit') {
            $('#comment-'+responseJSON.comment_id).find('.comments_info').find('.comments_body').html(responseJSON.body);
          } else {
            $('#reply-'+responseJSON.comment_id).find('.comments_info').find('.comments_body').html(responseJSON.body);
          } 
          $('#editpopup_'+ responseJSON.comment_id).remove();
          $('#comment-option-'+ responseJSON.comment_id).css('display', 'none');
          $('#comment_information-'+ responseJSON.comment_id).css('display', 'block');
          sm4.core.dloader.refreshPage();
          sm4.core.runonce.trigger();
          sm4.activity.resetAdvFeed();
        }.bind(this)
      });
  }, 
  like_unlikeFeed: function(action, action_id, comment_id, type) {
    if(enabledModuleForMobile && (showAsLike == 0)) {
        if (action == 'like') {
            //MAKE LIKE CHANGE TO UNLIKE FIRST AND THEN SEND AJAX REQUEST:
            
            if ($.type($.mobile.activePage.find('.comments_likes').find('.comments_likes_count')[0]) != 'undefined') {
              var likespan = $.trim($.mobile.activePage.find('.comments_likes a:first-child').html()).split(' ');
              if(showLikeWithoutIcon != 3) {
                $.mobile.activePage.find('.comments_likes a:first-child').html(sm4.core.language.translate('% like', parseInt(likespan[0]) + parseInt(1)));
              } else {
                $.mobile.activePage.find('.comments_likes').find('.comments_likes_count').html(sm4.core.language.translate(parseInt(likespan[0]) + parseInt(1)));
              }
            }
            else {
              contentviewpage_URL = contentviewpage_URL + '/type/' + type + '/id/' + action_id;
              if(showLikeWithoutIcon != 3) {
                var likeCountHtml = '<a class="f_normal" href="javascript:void(0);" onclick="sm4.activity.openPopup(\'' + contentviewpage_URL + '\', \'feedsharepopup\')"> ' + sm4.core.language.translate('% likes', 1) + '</a> <b class="sep">&nbsp;-&nbsp;</b>';
                $.mobile.activePage.find('.comments_likes').prepend($(likeCountHtml));
              } else {
                  var likeCountHtml = '<a href="javascript:void(0);" class="comments_likes_count f_normal" onclick="sm4.activity.openPopup(\'' + contentviewpage_URL + '\', \'feedsharepopup\')"> ' + sm4.core.language.translate(1) + '</a>';
                $.mobile.activePage.find('.comments_likes').prepend($(likeCountHtml));
              }
            }

            $.mobile.activePage.find('.comments_options').find('.feed_likes').attr('onclick', 'sm4.activity.changeLikeDislikeColor()');

            $.mobile.activePage.find('.comments_options').find('.feed_dislikes').attr('onclick', 'sm4.core.comments.dislike(\'' + type + '\',' + action_id + ');');
        }
        else {
          var likespan = $.trim($.mobile.activePage.find('.comments_likes').children('a').html()).split(' ');
          if ((parseInt(likespan[0]) - parseInt(1)) > 0)
            $.mobile.activePage.find('.comments_likes').children('a').html(sm4.core.language.translate('% like', parseInt(likespan[0]) - parseInt(1)));

          else {
            $.mobile.activePage.find('.comments_likes').children('a').next().remove();
            $.mobile.activePage.find('.comments_likes').children('a').remove();
          }

          $.mobile.activePage.find('.comments_options a:last-child').attr('onclick', 'javascript:sm4.core.comments.like(\'' + type + '\',' + action_id + ');');

        }
    } else {
        if (action == 'like') {

      //MAKE LIKE CHANGE TO UNLIKE FIRST AND THEN SEND AJAX REQUEST:
      if ($.type($.mobile.activePage.find('.comments_likes a:first-child')[0]) != 'undefined') {
        var likespan = $.trim($.mobile.activePage.find('.comments_likes a:first-child').html()).split(' ');
        $.mobile.activePage.find('.comments_likes a:first-child').html(sm4.core.language.translate('% like', parseInt(likespan[0]) + parseInt(1)));

      }
      else {
        contentviewpage_URL = contentviewpage_URL + '/type/' + type + '/id/' + action_id;
        var likeCountHtml = '<a href="javascript:void(0);" onclick="sm4.activity.openPopup(\'' + contentviewpage_URL + '\', \'feedsharepopup\')"> ' + sm4.core.language.translate('% like', 1) + '</a> <b class="sep">&nbsp;-&nbsp;</b>';
        $.mobile.activePage.find('.comments_likes').prepend($(likeCountHtml));

      }

      $.mobile.activePage.find('.comments_options a:last-child').attr('onclick', 'javascript:sm4.core.comments.unlike(\'' + type + '\',' + action_id + ');');

      $.mobile.activePage.find('.comments_options a:last-child').html(' <span>' + sm4.core.language.translate('Unlike This') + '</span>');

    }
    else {
      var likespan = $.trim($.mobile.activePage.find('.comments_likes').children('a').html()).split(' ');
      if ((parseInt(likespan[0]) - parseInt(1)) > 0)
        $.mobile.activePage.find('.comments_likes').children('a').html(sm4.core.language.translate('% like', parseInt(likespan[0]) - parseInt(1)));

      else {
        $.mobile.activePage.find('.comments_likes').children('a').next().remove();
        $.mobile.activePage.find('.comments_likes').children('a').remove();
      }

      $.mobile.activePage.find('.comments_options a:last-child').attr('onclick', 'javascript:sm4.core.comments.like(\'' + type + '\',' + action_id + ');');

      $.mobile.activePage.find('.comments_options a:last-child').html(sm4.core.language.translate('Like This'));

      }
    }
    sm4.core.dloader.refreshPage();
  },
  like_unlikeComment: function(action, action_id, comment_id, type) {
      if(enabledModuleForMobile && (showAsLike == 0)) {
        var url = "'" +sm4.core.baseUrl + 'advancedactivity/index/get-all-like-user/type/core_comment' + '/id/' + comment_id + '/showLikeWithoutIconInReplies/' + showLikeWithoutIconInReplies +"'";
        var popup = "'feedsharepopup'";
        if (action == 'like') { 

        //MAKE LIKE CHANGE TO UNLIKE FIRST AND THEN SEND AJAX REQUEST:
        if ($.type($('#comments_comment_likes_' + comment_id).get(0)) != 'undefined') {
          //var likespan = $.trim($('#comments_comment_likes_' + comment_id).html()).split(' ');
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
            $('#comment-' + comment_id + ' .comment_dislikes').attr('onclick', 'javascript:sm4.core.comments.dislike(\'' + type + '\',' + action_id + ',' + comment_id + ');');
            
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
             var likeCountHtml = '<span class="sep"> - </span><a href="javascript:void(0);" id="comments_comment_likes_' + comment_id + '" class="comments_comment_likes" onclick="sm4.activity.openPopup('+url+','+popup+')"><span class="ui-icon ui-icon-thumbs-up-alt">' + sm4.core.language.translate(1) + '</span></a>';
          } else {
               var likeCountHtml = '<a href="javascript:void(0);" id="comments_comment_likes_' + comment_id + '" class="comments_comment_likes" onclick="sm4.activity.openPopup('+url+','+popup+')"><span>' + sm4.core.language.translate(1) + '</span></a>';
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
            $('#comment-' + comment_id + ' .comment_dislikes').attr('onclick', 'javascript:sm4.core.comments.dislike(\'' + type + '\',' + action_id + ',' + comment_id + ');');
            
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

        $('#comment-' + comment_id + ' .comment_likes').attr('onclick', 'javascript:sm4.core.comments.like(\'' + type + '\',' + action_id + ',' + comment_id + ');');

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
          var likeCountHtml = '<span class="sep"> - </span><a href="javascript:void(0);" id="comments_comment_likes_' + comment_id + '" class="comments_comment_likes" onclick="sm4.core.comments.comment_likes(' + comment_id + ')"><span>' + sm4.core.language.translate('% likes this', 1) + '</span></a>';
          likeCountHtml = sm4.core.mobiPageHTML(likeCountHtml);

          $('#comment-' + comment_id + ' .comment_likes').after($(likeCountHtml));

        }

        $('#comment-' + comment_id + ' .comment_likes').attr('onclick', 'javascript:sm4.core.comments.unlike(\'' + type + '\',' + action_id + ',' + comment_id + ');');

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

        $('#comment-' + comment_id + ' .comment_likes').attr('onclick', 'javascript:sm4.core.comments.like(\'' + type + '\',' + action_id + ',' + comment_id + ');');
        $('#comment-' + comment_id + ' .comment_likes').html(sm4.core.language.translate('Like'));

        }
      }
      sm4.core.dloader.refreshPage();

  },
  like: function(type, id, comment_id) {

    if ($.type(comment_id) == 'undefined') {
      this.like_unlikeFeed('like', id, comment_id, type);
    } else {
      this.like_unlikeComment('like', id, comment_id, type);
    }
    sm4.core.request.send({
      type: "POST",
      dataType: "json",
      url: sm4.core.baseUrl + 'core/comment/like',
      data: {
        format: 'json',
        type: type,
        id: id,
        comment_id: comment_id
      },
      success: function(responseJSON, textStatus, xhr) {
        if(enabledModuleForMobile && (showAsLike == 0)) {
          if ($.type(comment_id) == 'undefined' || comment_id == '') {
             if(responseJSON.hasViewerDisLike && responseJSON.dislikeCount > 1) {
                  
                  //var likespan = $.mobile.activePage.find('.comments_dislikes').children('a').html(sm4.core.language.translate('% like', parseInt(likespan[0]) - parseInt(1)));
                  
                  var likespan = $.trim($.mobile.activePage.find('.comments_dislikes').children('a').html()).split(' ');
                  if(responseJSON.dislikeCount > 1) {
                      //$.mobile.activePage.find('#activity-item-' + action_id + ' .feed_item_btm a.feed_dislikes span').html(sm4.core.mobiPageHTML(sm4.core.language.translate('% dislikes', parseInt(responseJSON.dislikeCount) - parseInt(1))));
                      if(showLikeWithoutIcon != 3) {
                        $.mobile.activePage.find('.comments_dislikes').children('a').html(sm4.core.mobiPageHTML(sm4.core.language.translate('% dislikes', parseInt(responseJSON.dislikeCount) - parseInt(1))));
                       } else {
                           $.mobile.activePage.find('.comments_dislikes').children('.comments_dislikes_count').html(sm4.core.mobiPageHTML(parseInt(responseJSON.dislikeCount) - parseInt(1)));
                       }
                  } else {
                    if(showLikeWithoutIcon != 3) {
                      $.mobile.activePage.find('.comments_dislikes').children('a').next().remove();
                      $.mobile.activePage.find('.comments_dislikes').children('a').remove();
                    } else {
                      $.mobile.activePage.find('.comments_dislikes').find('.comments_dislikes_count').remove();
                    } 
                  }
              }  else if(responseJSON.hasViewerDisLike && responseJSON.dislikeCount == 1) {
                   // $.mobile.activePage.find('#activity-item-' + action_id + ' .feed_item_btm a.feed_dislikes').prev().remove();
                  // $.mobile.activePage.find('#activity-item-' + action_id + ' .feed_item_btm a.feed_dislikes').remove();
                   
                     if(showLikeWithoutIcon != 3) {
                      $.mobile.activePage.find('.comments_dislikes').children('a').next().remove();
                      $.mobile.activePage.find('.comments_dislikes').children('a').remove();
                    } else {
                      $.mobile.activePage.find('.comments_dislikes').find('.comments_dislikes_count').remove();
                    } 
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
          this.like_unlikeFeed('unlike', id, comment_id, type);
        }
        else {
          this.like_unlikeComment('unlike', id, comment_id, type);

        }
      },
      statusCode: {
        404: function(response) {
          if ($.type(comment_id) == 'undefined') {
            this.like_unlikeFeed('unlike', id, comment_id, type);
          }
          else {
            this.like_unlikeComment('unlike', id, comment_id, type);

          }
        }
      }
    }, {
      //element : $.mobile.activePage.find('#comments')
    });
  },
  likeReply: function(type, id, comment_id) {

    if ($.type(comment_id) == 'undefined') {
      this.like_unlikeFeed('like', id, comment_id, type);
    } else {
      this.like_unlikeReply('like', id, comment_id, type);
    }
    sm4.core.request.send({
      type: "POST",
      dataType: "json",
      url: sm4.core.baseUrl + 'nestedcomment/reply/like',
      data: {
        format: 'json',
        type: type,
        id: id,
        comment_id: comment_id
      },
      success: function(responseJSON, textStatus, xhr) {
        if(enabledModuleForMobile && (showAsLike == 0)) {
          if ($.type(comment_id) == 'undefined' || comment_id == '') {
             if(responseJSON.hasViewerDisLike && responseJSON.dislikeCount > 1) {
                  
                  //var likespan = $.mobile.activePage.find('.reply_dislikes').children('a').html(sm4.core.language.translate('% like', parseInt(likespan[0]) - parseInt(1)));
                  
                  var likespan = $.trim($.mobile.activePage.find('.reply_dislikes').children('a').html()).split(' ');
                  if(responseJSON.dislikeCount > 1) {
                      //$.mobile.activePage.find('#activity-item-' + action_id + ' .feed_item_btm a.feed_dislikes span').html(sm4.core.mobiPageHTML(sm4.core.language.translate('% dislikes', parseInt(responseJSON.dislikeCount) - parseInt(1))));
                      if(showLikeWithoutIcon != 3) {
                        $.mobile.activePage.find('.reply_dislikes').children('a').html(sm4.core.mobiPageHTML(sm4.core.language.translate('% dislikes', parseInt(responseJSON.dislikeCount) - parseInt(1))));
                       } else {
                           $.mobile.activePage.find('.reply_dislikes').children('.replies_dislikes_count').html(sm4.core.mobiPageHTML(parseInt(responseJSON.dislikeCount) - parseInt(1)));
                       }
                  } else {
                    if(showLikeWithoutIcon != 3) {
                      $.mobile.activePage.find('.reply_dislikes').children('a').next().remove();
                      $.mobile.activePage.find('.reply_dislikes').children('a').remove();
                    } else {
                      $.mobile.activePage.find('.reply_dislikes').find('.replies_dislikes_count').remove();
                    } 
                  }
              }  else if(responseJSON.hasViewerDisLike && responseJSON.dislikeCount == 1) {
                   // $.mobile.activePage.find('#activity-item-' + action_id + ' .feed_item_btm a.feed_dislikes').prev().remove();
                  // $.mobile.activePage.find('#activity-item-' + action_id + ' .feed_item_btm a.feed_dislikes').remove();
                   
                     if(showLikeWithoutIcon != 3) {
                      $.mobile.activePage.find('.reply_dislikes').children('a').next().remove();
                      $.mobile.activePage.find('.reply_dislikes').children('a').remove();
                    } else {
                      $.mobile.activePage.find('.reply_dislikes').find('.replies_dislikes_count').remove();
                    } 
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
          this.like_unlikeFeed('unlike', id, comment_id, type);
        }
        else {
          this.like_unlikeReply('unlike', id, comment_id, type);

        }
      },
      statusCode: {
        404: function(response) {
          if ($.type(comment_id) == 'undefined') {
            this.like_unlikeFeed('unlike', id, comment_id, type);
          }
          else {
            this.like_unlikeReply('unlike', id, comment_id, type);

          }
        }
      }
    }, {
      //element : $.mobile.activePage.find('#comments')
    });
  },
  like_unlikeReply: function(action, action_id, comment_id, type) {
     
      if(enabledModuleForMobile && (showAsLike == 0)) {
        var url = "'" +sm4.core.baseUrl + 'advancedactivity/index/get-all-like-user/type/core_comment' + '/id/' + comment_id + '/showLikeWithoutIconInReplies/' + showLikeWithoutIconInReplies +"'";
        var popup = "'feedsharepopup'";
        if (action == 'like') { 

        //MAKE LIKE CHANGE TO UNLIKE FIRST AND THEN SEND AJAX REQUEST:
        if ($.type($('#replies_reply_likes_' + comment_id).get(0)) != 'undefined') {
          //var likespan = $.trim($('#replies_reply_likes_' + comment_id).html()).split(' ');
          if(showLikeWithoutIconInReplies != 3) {
              var likespan = "<span class='ui-icon ui-icon-thumbs-up-alt'>" + (parseInt($('#replies_reply_likes_' + comment_id).find('span').html()) + parseInt(1)); + "</span>";
              $('#replies_reply_likes_' + comment_id).html(sm4.core.mobiPageHTML(likespan));
            } else {
                var likespan = "<span>" + (parseInt($('#replies_reply_likes_' + comment_id).find('span').html()) + parseInt(1)); + "</span>";
                $('#replies_reply_likes_' + comment_id).html(sm4.core.mobiPageHTML(parseInt(likespan) + parseInt(1)));
            }

            $('#comment-' + comment_id + ' .reply_likes').attr('href', 'javascript:void(0);');
            $('#comment-' + comment_id + ' .reply_likes').attr('onclick', 'sm4.activity.changeLikeDislikeColor()');
            
            if(showLikeWithoutIconInReplies != 3) {
                $('#comment-' + comment_id + ' .reply_likes').html(sm4.core.language.translate('Like'));
            } else {
                $('#comment-' + comment_id + ' .reply_likes').addClass("ui-icon ui-icon-angle-up");   
            }
            $('#comment-' + comment_id + ' .reply_dislikes').attr('href', 'javascript:void(0);');
            $('#comment-' + comment_id + ' .reply_dislikes').attr('onclick', 'javascript:sm4.core.comments.dislikeReply(\'' + type + '\',' + action_id + ',' + comment_id + ');');
            
            if(showLikeWithoutIconInReplies != 3) {
              $('#comment-' + comment_id + ' .reply_dislikes').html(sm4.core.language.translate('Dislike'));
            } else {
                $('#comment-' + comment_id + ' .reply_dislikes').addClass("ui-icon ui-icon-angle-down");
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
             var likeCountHtml = '<span class="sep"> - </span><a href="javascript:void(0);" id="replies_reply_likes_' + comment_id + '" class="replies_reply_likes" onclick="sm4.activity.openPopup('+url+','+popup+')"><span class="ui-icon ui-icon-thumbs-up-alt">' + sm4.core.language.translate(1) + '</span></a>';
          } else {
               var likeCountHtml = '<a href="javascript:void(0);" id="replies_reply_likes_' + comment_id + '" class="replies_reply_likes" onclick="sm4.activity.openPopup('+url+','+popup+')"><span>' + sm4.core.language.translate(1) + '</span></a>';
          }
          likeCountHtml = sm4.core.mobiPageHTML(likeCountHtml);

            if(showLikeWithoutIconInReplies == 3) {    
              $('#comment-' + comment_id + ' .reply_likes').before($(likeCountHtml));
            } else {
              $('#comment-' + comment_id + ' .reply_dislikes').after($(likeCountHtml));
            }
            
            $('#comment-' + comment_id + ' .reply_likes').attr('href', 'javascript:void(0);');
            $('#comment-' + comment_id + ' .reply_likes').attr('onclick', 'sm4.activity.changeLikeDislikeColor()');
            
            if(showLikeWithoutIconInReplies != 3) {
                $('#comment-' + comment_id + ' .reply_likes').html(sm4.core.language.translate('Like'));
            } else {
                $('#comment-' + comment_id + ' .reply_likes').addClass("ui-icon ui-icon-angle-up");
            }

            $('#comment-' + comment_id + ' .reply_dislikes').attr('href', 'javascript:void(0);');
            $('#comment-' + comment_id + ' .reply_dislikes').attr('onclick', 'javascript:sm4.core.comments.dislikeReply(\'' + type + '\',' + action_id + ',' + comment_id + ');');
            
            if(showLikeWithoutIconInReplies != 3) {
                $('#comment-' + comment_id + ' .reply_dislikes').html(sm4.core.language.translate('Dislike'));
            } else {
                $('#comment-' + comment_id + ' .reply_dislikes').addClass("ui-icon ui-icon-angle-down");
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

        $('#comment-' + comment_id + ' .reply_likes').attr('onclick', 'javascript:sm4.core.comments.likeReply(\'' + type + '\',' + action_id + ',' + comment_id + ');');

        $('#comment-' + comment_id + ' .reply_likes').html(sm4.core.language.translate('Like'));

        } 
      } else {
      if (action == 'like') {

        //MAKE LIKE CHANGE TO UNLIKE FIRST AND THEN SEND AJAX REQUEST:
        if ($.type($('#replies_reply_likes_' + comment_id).get(0)) != 'undefined') {
          var likespan = $.trim($('#replies_reply_likes_' + comment_id).html()).split(' ');
          $('#replies_reply_likes_' + comment_id).html(sm4.core.mobiPageHTML(sm4.core.language.translate('% likes this', parseInt(likespan[0]) + parseInt(1))));

        }
        else {
          var likeCountHtml = '<span class="sep"> - </span><a href="javascript:void(0);" id="replies_reply_likes_' + comment_id + '" class="replies_reply_likes" onclick="sm4.core.comments.replies_likes(' + comment_id + ')"><span>' + sm4.core.language.translate('% likes this', 1) + '</span></a>';
          likeCountHtml = sm4.core.mobiPageHTML(likeCountHtml);

          $('#comment-' + comment_id + ' .reply_likes').after($(likeCountHtml));

        }
        $('#comment-' + comment_id + ' .reply_likes').attr('onclick', 'javascript:sm4.core.comments.unlikeReply(\'' + type + '\',' + action_id + ',' + comment_id + ');');
       
        $('#comment-' + comment_id + ' .reply_likes').html(sm4.core.language.translate('Unlike'));

      }
      else {
        var likespan = $.trim($('#replies_reply_likes_' + comment_id).html()).split(' ');

        if ((parseInt(likespan[0]) - parseInt(1)) > 0)
          $('#replies_reply_likes_' + comment_id).html(sm4.core.mobiPageHTML(sm4.core.language.translate('% likes this', parseInt(likespan[0]) - parseInt(1))));

        else {
          $('#replies_reply_likes_' + comment_id).prev().remove();
          $('#replies_reply_likes_' + comment_id).remove();
        }

        $('#comment-' + comment_id + ' .reply_likes').attr('onclick', 'javascript:sm4.core.comments.likeReply(\'' + type + '\',' + action_id + ',' + comment_id + ');');
        $('#comment-' + comment_id + ' .reply_likes').html(sm4.core.language.translate('Like'));

        }
      }
      sm4.core.dloader.refreshPage();

  },  
  dislike_unlikeComment: function(action, action_id, comment_id, type) {
      var url = "'" +sm4.core.baseUrl + 'advancedactivity/index/get-all-dislike-user/type/core_comment' + '/id/' + comment_id + '/showLikeWithoutIconInReplies/' + showLikeWithoutIconInReplies +"'";
      var popup = "'feedsharepopup'";
      if (action == 'like') {

        //MAKE LIKE CHANGE TO UNLIKE FIRST AND THEN SEND AJAX REQUEST:
        if ($.type($('#comments_comment_likes_' + comment_id).get(0)) != 'undefined') {
          var likespan = $.trim($('#comments_comment_likes_' + comment_id).html()).split(' ');
          $('#comments_comment_likes_' + comment_id).html(sm4.core.mobiPageHTML(sm4.core.language.translate('% likes this', parseInt(likespan[0]) + parseInt(1))));

        }
        else {
          var likeCountHtml = '<span class="sep"> - </span><a href="javascript:void(0);" id="comments_comment_likes_' + comment_id + '" class="comments_comment_likes" onclick="sm4.core.comments.comment_likes(\'' + comment_id + '\')"><span>' + sm4.core.language.translate('% likes this', 1) + '</span></a>';
          likeCountHtml = sm4.core.mobiPageHTML(likeCountHtml);
          $('#comment-' + comment_id + ' .comment_likes').after($(likeCountHtml));
        }

        $('#comment-' + comment_id + ' .comment_likes').attr('onclick', 'javascript:sm4.core.comments.unlike(\'' + type + '\',' + action_id + ',' + comment_id + ');');

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
            $('#comment-' + comment_id + ' .comment_likes').attr('onclick', 'javascript:sm4.core.comments.like(\'' + type + '\',' + action_id + ',' + comment_id + ');');

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
        $('#comment-' + comment_id + ' .comment_likes').attr('onclick', 'javascript:sm4.core.comments.like(\'' + type + '\',' + action_id + ',' + comment_id + ');');
        
        $('#comment-' + comment_id + ' .comment_dislikes').attr('onclick', 'sm4.activity.changeLikeDislikeColor()');

        if(showLikeWithoutIconInReplies != 3) {  
            $('#comment-' + comment_id + ' .comment_dislikes').html(sm4.core.language.translate('Dislike'));
        } else {
            $('#comment-' + comment_id + ' .comment_dislikes').addClass("ui-icon ui-icon-angle-down");
        }
          if(showLikeWithoutIconInReplies != 3) {  
            if(showDislikeUsers == 1) {  
                var likeCountHtml = '<span class="sep"> - </span><a href="javascript:void(0);" id="comments_comment_dislikes_' + comment_id + '" class="comments_comment_dislikes" onclick="sm4.activity.openPopup('+url+','+popup+')"><span class="ui-icon ui-icon-thumbs-down-alt">' + sm4.core.language.translate(1) + '</span></a>';
            } else {
                var likeCountHtml = '<span class="sep"> - </span><a href="javascript:void(0);" id="comments_comment_dislikes_' + comment_id + '" class="comments_comment_dislikes" onclick=""><span class="ui-icon ui-icon-thumbs-down-alt">' + sm4.core.language.translate(1) + '</span></a>';
            }
            
          } else {
              if(showDislikeUsers == 1) {  
                var likeCountHtml = '<a href="javascript:void(0);" id="comments_comment_dislikes_' + comment_id + '" class="comments_comment_dislikes" onclick="sm4.activity.openPopup('+url+','+popup+')"><span>' + sm4.core.language.translate(1) + '</span></a>';
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
  dislike_unlikeReply: function(action, action_id, comment_id, type) {
      var url = "'" +sm4.core.baseUrl + 'advancedactivity/index/get-all-dislike-user/type/core_comment' + '/id/' + comment_id + '/showLikeWithoutIconInReplies/' + showLikeWithoutIconInReplies +"'";
      var popup = "'feedsharepopup'";
      if (action == 'like') {

        //MAKE LIKE CHANGE TO UNLIKE FIRST AND THEN SEND AJAX REQUEST:
        if ($.type($('#replies_reply_likes_' + comment_id).get(0)) != 'undefined') {
          var likespan = $.trim($('#replies_reply_likes_' + comment_id).html()).split(' ');
          $('#replies_reply_likes_' + comment_id).html(sm4.core.mobiPageHTML(sm4.core.language.translate('% likes this', parseInt(likespan[0]) + parseInt(1))));

        }
        else {
          var likeCountHtml = '<span class="sep"> - </span><a href="javascript:void(0);" id="replies_reply_likes_' + comment_id + '" class="replies_reply_likes" onclick="sm4.core.comments.replies_likes(\'' + comment_id + '\')"><span>' + sm4.core.language.translate('% likes this', 1) + '</span></a>';
          likeCountHtml = sm4.core.mobiPageHTML(likeCountHtml);
          $('#comment-' + comment_id + ' .reply_likes').after($(likeCountHtml));
        }

        $('#comment-' + comment_id + ' .reply_likes').attr('onclick', 'javascript:sm4.core.comments.unlikeReply(\'' + type + '\',' + action_id + ',' + comment_id + ');');

        $('#comment-' + comment_id + ' .reply_likes').html(sm4.core.language.translate('Unlike'));

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
                $('#comment-' + comment_id + ' .reply_likes').html(sm4.core.language.translate('Like'));
            } else {
                $('#comment-' + comment_id + ' .reply_likes').addClass("ui-icon ui-icon-angle-up");
            }
            $('#comment-' + comment_id + ' .reply_likes').attr('onclick', 'javascript:sm4.core.comments.likeReply(\'' + type + '\',' + action_id + ',' + comment_id + ');');

            $('#comment-' + comment_id + ' .reply_dislikes').attr('onclick', 'sm4.activity.changeLikeDislikeColor()');

            if(showLikeWithoutIconInReplies != 3) { 
                $('#comment-' + comment_id + ' .reply_dislikes').html(sm4.core.language.translate('Dislike'));
            } else {
                $('#comment-' + comment_id + ' .reply_dislikes').addClass("ui-icon ui-icon-angle-down");
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
            $('#comment-' + comment_id + ' .reply_likes').html(sm4.core.language.translate('Like'));
        } else {
            $('#comment-' + comment_id + ' .reply_likes').addClass("ui-icon ui-icon-angle-up");
        }
        $('#comment-' + comment_id + ' .reply_likes').attr('onclick', 'javascript:sm4.core.comments.likeReply(\'' + type + '\',' + action_id + ',' + comment_id + ');');
        
        $('#comment-' + comment_id + ' .reply_dislikes').attr('onclick', 'sm4.activity.changeLikeDislikeColor()');

        if(showLikeWithoutIconInReplies != 3) {  
            $('#comment-' + comment_id + ' .reply_dislikes').html(sm4.core.language.translate('Dislike'));
        } else {
            $('#comment-' + comment_id + ' .reply_dislikes').addClass("ui-icon ui-icon-angle-down");
        }
          if(showLikeWithoutIconInReplies != 3) {  
            if(showDislikeUsers == 1) {  
                var likeCountHtml = '<span class="sep"> - </span><a href="javascript:void(0);" id="replies_reply_dislikes_' + comment_id + '" class="replies_reply_dislikes" onclick="sm4.activity.openPopup('+url+','+popup+')"><span class="ui-icon ui-icon-thumbs-down-alt">' + sm4.core.language.translate(1) + '</span></a>';
            } else {
                var likeCountHtml = '<span class="sep"> - </span><a href="javascript:void(0);" id="replies_reply_dislikes_' + comment_id + '" class="replies_reply_dislikes" onclick=""><span class="ui-icon ui-icon-thumbs-down-alt">' + sm4.core.language.translate(1) + '</span></a>';
            }
            
          } else {
              if(showDislikeUsers == 1) {  
                var likeCountHtml = '<a href="javascript:void(0);" id="replies_reply_dislikes_' + comment_id + '" class="replies_reply_dislikes" onclick="sm4.activity.openPopup('+url+','+popup+')"><span>' + sm4.core.language.translate(1) + '</span></a>';
              } else {
                  var likeCountHtml = '<a href="javascript:void(0);" id="replies_reply_dislikes_' + comment_id + '" class="replies_reply_dislikes"><span>' + sm4.core.language.translate(1) + '</span></a>';
              }
          }
          
          likeCountHtml = sm4.core.mobiPageHTML(likeCountHtml);
          if($.type($('#comment-' + comment_id + ' .replies_reply_likes').get(0)) != 'undefined') {
            if(showLikeWithoutIconInReplies != 3) {       
               $('#comment-' + comment_id + ' .replies_reply_likes').after($(likeCountHtml));
            } else {
               $('#comment-' + comment_id + ' .reply_dislikes').before($(likeCountHtml)); 
            }
          } else { 
            if(showLikeWithoutIconInReplies == 3) {    
              $('#comment-' + comment_id + ' .reply_dislikes').before($(likeCountHtml));
            } else {
              $('#comment-' + comment_id + ' .reply_dislikes').after($(likeCountHtml)); 
            }
          }
        }
      }
      sm4.core.dloader.refreshPage();
  },
  dislike_unlikeFeed: function(action, action_id, comment_id, type) {

    if (action == 'like') {

      //MAKE LIKE CHANGE TO UNLIKE FIRST AND THEN SEND AJAX REQUEST:
      if ($.type($.mobile.activePage.find('.comments_likes a:first-child')[0]) != 'undefined') {
        var likespan = $.trim($.mobile.activePage.find('.comments_likes a:first-child').html()).split(' ');
        $.mobile.activePage.find('.comments_likes a:first-child').html(sm4.core.language.translate('% like', parseInt(likespan[0]) + parseInt(1)));

      }
      else {
        contentviewdislikepage_URL = contentviewdislikepage_URL + '/type/' + type + '/id/' + action_id;
        var likeCountHtml = '<a href="javascript:void(0);" onclick="sm4.activity.openPopup(\'' + contentviewdislikepage_URL + '\', \'feedsharepopup\')"> ' + sm4.core.language.translate('% like', 1) + '</a> <b class="sep">&nbsp;-&nbsp;</b>';
        $.mobile.activePage.find('.comments_likes').prepend($(likeCountHtml));
      }

      $.mobile.activePage.find('.comments_options a:last-child').attr('onclick', 'javascript:sm4.core.comments.unlike(\'' + type + '\',' + action_id + ');');
      $.mobile.activePage.find('.comments_options a:last-child').html(' <span>' + sm4.core.language.translate('Dislike') + '</span>');
    }
    else { 
      if(enabledModuleForMobile ==1 & showLikeWithoutIcon != 3) {
        if ($.type($.mobile.activePage.find('.comments_dislikes a:first-child')[0]) != 'undefined') {
          var likespan = $.trim($.mobile.activePage.find('.comments_dislikes a:first-child').html()).split(' ');
          $.mobile.activePage.find('.comments_dislikes a:first-child').html(sm4.core.language.translate('% dislike', parseInt(likespan[0]) + parseInt(1)));
        }
        else {
          contentviewdislikepage_URL = contentviewdislikepage_URL + '/type/' + type + '/id/' + action_id;
          var likeCountHtml = '<a href="javascript:void(0);" onclick="sm4.activity.openPopup(\'' + contentviewdislikepage_URL + '\', \'feedsharepopup\')"> ' + sm4.core.language.translate('% dislikes', 1) + '</a> <b class="sep">&nbsp;-&nbsp;</b>';
          $.mobile.activePage.find('.comments_dislikes').prepend($(likeCountHtml));
        }
        $.mobile.activePage.find('.comments_options a:last-child').html(sm4.core.language.translate('Dislike'));
      } else {
          if ($.type($.mobile.activePage.find('.comments_dislikes').find('.comments_dislikes_count')[0]) != 'undefined') {
          var likespan = $.trim($.mobile.activePage.find('.comments_dislikes a:first-child').html()).split(' ');
          $.mobile.activePage.find('.comments_dislikes').find('.comments_dislikes_count').html(parseInt(likespan[0]) - parseInt(1));
        }
        else {
          contentviewdislikepage_URL = contentviewdislikepage_URL + '/type/' + type + '/id/' + action_id;
          var likeCountHtml = '<a href="javascript:void(0);" onclick="sm4.activity.openPopup(\'' + contentviewdislikepage_URL + '\', \'feedsharepopup\')" class="comments_dislikes_count"> ' + sm4.core.language.translate(1) + '</a>';
          $.mobile.activePage.find('.comments_dislikes').prepend($(likeCountHtml));
        }
      }
      
      $.mobile.activePage.find('.comments_options').find('.feed_dislikes').attr('onclick', 'sm4.activity.changeLikeDislikeColor()');
      
      $.mobile.activePage.find('.comments_options').find('.feed_likes').attr('onclick', 'sm4.core.comments.like(\'' + type + '\',' + action_id + ');');

    }
    sm4.core.dloader.refreshPage();
  },  
  dislike: function(type, id, comment_id) {

    if ($.type(comment_id) == 'undefined') {
      this.dislike_unlikeFeed('unlike', id, comment_id, type);
    } else {
      this.dislike_unlikeComment('unlike', id, comment_id, type);
    }
    sm4.core.request.send({
      type: "POST",
      dataType: "json",
      url: sm4.core.baseUrl + 'core/comment/unlike',
      data: {
        format: 'json',
        type: type,
        id: id,
        comment_id: comment_id
      },
      success: function(responseJSON, textStatus, xhr) {
          sm4.core.dloader.refreshPage();
          sm4.core.runonce.trigger();
          
          if ($.type(comment_id) == 'undefined' || comment_id == '') {
             if(responseJSON.hasViewerLike && responseJSON.likeCount > 1) {
                  
//                  var likespan = $.trim($.mobile.activePage.find('#activity-item-' + action_id + ' .feed_item_btm a.feed_likes span').html()).split(' ');
//                  if(responseJSON.likeCount > 1) {
//                      $.mobile.activePage.find('#activity-item-' + action_id + ' .feed_item_btm a.feed_likes span').html(sm4.core.mobiPageHTML(sm4.core.language.translate('% likes', parseInt(responseJSON.likeCount) - parseInt(1))));
//                  } else {
//                    $.mobile.activePage.find('#activity-item-' + action_id + ' .feed_item_btm a.feed_likes').next().remove();
//                      $.mobile.activePage.find('#activity-item-' + action_id + ' .feed_item_btm a.feed_likes').remove();
//                  }


//var likespan = $.mobile.activePage.find('.comments_dislikes').children('a').html(sm4.core.language.translate('% like', parseInt(likespan[0]) - parseInt(1)));
                  
                  var likespan = $.trim($.mobile.activePage.find('.comments_likes').children('a').html()).split(' ');
                  
                 
                  if(responseJSON.likeCount > 1) {
                      //$.mobile.activePage.find('#activity-item-' + action_id + ' .feed_item_btm a.feed_dislikes span').html(sm4.core.mobiPageHTML(sm4.core.language.translate('% dislikes', parseInt(responseJSON.dislikeCount) - parseInt(1))));
                      if(showLikeWithoutIcon != 3) {
                        $.mobile.activePage.find('.comments_likes').children('a').html(sm4.core.mobiPageHTML(sm4.core.language.translate('% likes', parseInt(responseJSON.likeCount) - parseInt(1))));
                    } else {
                        $.mobile.activePage.find('.comments_likes').find('.comments_likes_count').html(sm4.core.mobiPageHTML(parseInt(responseJSON.likeCount) - parseInt(1)));
                    }
                  } else {
                    if(showLikeWithoutIcon != 3) {
                      $.mobile.activePage.find('.comments_likes').children('a').next().remove();
                      $.mobile.activePage.find('.comments_likes').children('a').remove();
                    } else {
                      $.mobile.activePage.find('.comments_likes').find('.comments_likes_count').remove();
                    }  
                  }

              }  else if(responseJSON.hasViewerLike && responseJSON.likeCount == 1) {
                   // $.mobile.activePage.find('#activity-item-' + action_id + ' .feed_item_btm a.feed_likes').next().remove();
                  // $.mobile.activePage.find('#activity-item-' + action_id + ' .feed_item_btm a.feed_likes').remove();
                    if(showLikeWithoutIcon != 3) {
                      $.mobile.activePage.find('.comments_likes').children('a').next().remove();
                      $.mobile.activePage.find('.comments_likes').children('a').remove();
                    } else {
                      $.mobile.activePage.find('.comments_likes').find('.comments_likes_count').remove();
                    }
                  
              }
              
              
              
//              if(responseJSON.hasViewerDisLike && responseJSON.dislikeCount > 1) {
//                  
//                  //var likespan = $.mobile.activePage.find('.comments_dislikes').children('a').html(sm4.core.language.translate('% like', parseInt(likespan[0]) - parseInt(1)));
//                  
//                  var likespan = $.trim($.mobile.activePage.find('.comments_dislikes').children('a').html()).split(' ');
//                  if(responseJSON.dislikeCount > 1) {
//                      //$.mobile.activePage.find('#activity-item-' + action_id + ' .feed_item_btm a.feed_dislikes span').html(sm4.core.mobiPageHTML(sm4.core.language.translate('% dislikes', parseInt(responseJSON.dislikeCount) - parseInt(1))));
//                      $.mobile.activePage.find('.comments_dislikes').children('a').html(sm4.core.mobiPageHTML(sm4.core.language.translate('% dislikes', parseInt(responseJSON.dislikeCount) - parseInt(1))));
//                      
//                  } else {
//                      $.mobile.activePage.find('.comments_dislikes').children('a').next().remove();
//                      $.mobile.activePage.find('.comments_dislikes').children('a').remove();
//                  }
//              }  else if(responseJSON.hasViewerDisLike && responseJSON.dislikeCount == 1) {
//                   // $.mobile.activePage.find('#activity-item-' + action_id + ' .feed_item_btm a.feed_dislikes').prev().remove();
//                  // $.mobile.activePage.find('#activity-item-' + action_id + ' .feed_item_btm a.feed_dislikes').remove();
//                   
//                    $.mobile.activePage.find('.comments_dislikes').children('a').next().remove();
//                    $.mobile.activePage.find('.comments_dislikes').children('a').remove();
//              }
              
              
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
          this.like_unlikeFeed('unlike', id, comment_id, type);
        }
        else {
          this.like_unlikeComment('unlike', id, comment_id, type);

        }
      },
      statusCode: {
        404: function(response) {
          if ($.type(comment_id) == 'undefined') {
            this.like_unlikeFeed('unlike', id, comment_id, type);
          }
          else {
            this.like_unlikeComment('unlike', id, comment_id, type);

          }
        }
      }
    }, {
      //element : $.mobile.activePage.find('#comments')
    });
  },
  dislikeReply: function(type, id, comment_id) {

    if ($.type(comment_id) == 'undefined') {
      this.dislike_unlikeFeed('unlike', id, comment_id, type);
    } else {
      this.dislike_unlikeReply('unlike', id, comment_id, type);
    }
    sm4.core.request.send({
      type: "POST",
      dataType: "json",
      url: sm4.core.baseUrl + 'nestedcomment/reply/unlike',
      data: {
        format: 'json',
        type: type,
        id: id,
        comment_id: comment_id
      },
      success: function(responseJSON, textStatus, xhr) {
          sm4.core.dloader.refreshPage();
          sm4.core.runonce.trigger();
          
          if ($.type(comment_id) == 'undefined' || comment_id == '') {
             if(responseJSON.hasViewerLike && responseJSON.likeCount > 1) {
                  
//                  var likespan = $.trim($.mobile.activePage.find('#activity-item-' + action_id + ' .feed_item_btm a.feed_likes span').html()).split(' ');
//                  if(responseJSON.likeCount > 1) {
//                      $.mobile.activePage.find('#activity-item-' + action_id + ' .feed_item_btm a.feed_likes span').html(sm4.core.mobiPageHTML(sm4.core.language.translate('% likes', parseInt(responseJSON.likeCount) - parseInt(1))));
//                  } else {
//                    $.mobile.activePage.find('#activity-item-' + action_id + ' .feed_item_btm a.feed_likes').next().remove();
//                      $.mobile.activePage.find('#activity-item-' + action_id + ' .feed_item_btm a.feed_likes').remove();
//                  }


//var likespan = $.mobile.activePage.find('.reply_dislikes').children('a').html(sm4.core.language.translate('% like', parseInt(likespan[0]) - parseInt(1)));
                  
                  var likespan = $.trim($.mobile.activePage.find('.reply_likes').children('a').html()).split(' ');
                  
                 
                  if(responseJSON.likeCount > 1) {
                      //$.mobile.activePage.find('#activity-item-' + action_id + ' .feed_item_btm a.feed_dislikes span').html(sm4.core.mobiPageHTML(sm4.core.language.translate('% dislikes', parseInt(responseJSON.dislikeCount) - parseInt(1))));
                      if(showLikeWithoutIcon != 3) {
                        $.mobile.activePage.find('.reply_likes').children('a').html(sm4.core.mobiPageHTML(sm4.core.language.translate('% likes', parseInt(responseJSON.likeCount) - parseInt(1))));
                    } else {
                        $.mobile.activePage.find('.reply_likes').find('.replies_likes_count').html(sm4.core.mobiPageHTML(parseInt(responseJSON.likeCount) - parseInt(1)));
                    }
                  } else {
                    if(showLikeWithoutIcon != 3) {
                      $.mobile.activePage.find('.reply_likes').children('a').next().remove();
                      $.mobile.activePage.find('.reply_likes').children('a').remove();
                    } else {
                      $.mobile.activePage.find('.reply_likes').find('.replies_likes_count').remove();
                    }  
                  }

              }  else if(responseJSON.hasViewerLike && responseJSON.likeCount == 1) {
                   // $.mobile.activePage.find('#activity-item-' + action_id + ' .feed_item_btm a.feed_likes').next().remove();
                  // $.mobile.activePage.find('#activity-item-' + action_id + ' .feed_item_btm a.feed_likes').remove();
                    if(showLikeWithoutIcon != 3) {
                      $.mobile.activePage.find('.reply_likes').children('a').next().remove();
                      $.mobile.activePage.find('.reply_likes').children('a').remove();
                    } else {
                      $.mobile.activePage.find('.reply_likes').find('.replies_likes_count').remove();
                    }
                  
              }
              
              
              
//              if(responseJSON.hasViewerDisLike && responseJSON.dislikeCount > 1) {
//                  
//                  //var likespan = $.mobile.activePage.find('.reply_dislikes').children('a').html(sm4.core.language.translate('% like', parseInt(likespan[0]) - parseInt(1)));
//                  
//                  var likespan = $.trim($.mobile.activePage.find('.reply_dislikes').children('a').html()).split(' ');
//                  if(responseJSON.dislikeCount > 1) {
//                      //$.mobile.activePage.find('#activity-item-' + action_id + ' .feed_item_btm a.feed_dislikes span').html(sm4.core.mobiPageHTML(sm4.core.language.translate('% dislikes', parseInt(responseJSON.dislikeCount) - parseInt(1))));
//                      $.mobile.activePage.find('.reply_dislikes').children('a').html(sm4.core.mobiPageHTML(sm4.core.language.translate('% dislikes', parseInt(responseJSON.dislikeCount) - parseInt(1))));
//                      
//                  } else {
//                      $.mobile.activePage.find('.reply_dislikes').children('a').next().remove();
//                      $.mobile.activePage.find('.reply_dislikes').children('a').remove();
//                  }
//              }  else if(responseJSON.hasViewerDisLike && responseJSON.dislikeCount == 1) {
//                   // $.mobile.activePage.find('#activity-item-' + action_id + ' .feed_item_btm a.feed_dislikes').prev().remove();
//                  // $.mobile.activePage.find('#activity-item-' + action_id + ' .feed_item_btm a.feed_dislikes').remove();
//                   
//                    $.mobile.activePage.find('.reply_dislikes').children('a').next().remove();
//                    $.mobile.activePage.find('.reply_dislikes').children('a').remove();
//              }
              
              
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
          this.like_unlikeFeed('unlike', id, comment_id, type);
        }
        else {
          this.like_unlikeReply('unlike', id, comment_id, type);

        }
      },
      statusCode: {
        404: function(response) {
          if ($.type(comment_id) == 'undefined') {
            this.like_unlikeFeed('unlike', id, comment_id, type);
          }
          else {
            this.like_unlikeReply('unlike', id, comment_id, type);

          }
        }
      }
    }, {
      //element : $.mobile.activePage.find('#comments')
    });
  }, 
  unlike: function(type, id, comment_id) {
    if ($.type(comment_id) == 'undefined') {
      this.like_unlikeFeed('unlike', id, comment_id, type);
    } else {
      this.like_unlikeComment('unlike', id, comment_id, type);

    }
    sm4.core.request.send({
      type: "POST",
      dataType: "json",
      url: sm4.core.baseUrl + 'core/comment/unlike',
      data: {
        format: 'json',
        type: type,
        id: id,
        comment_id: comment_id
      },
      error: function(xhr, textStatus, errorThrown) {
        if ($.type(comment_id) == 'undefined') {
          this.like_unlikeFeed('like', id, comment_id, type);
        }
        else {
          this.like_unlikeComment('like', id, comment_id, type);

        }
      },
      statusCode: {
        404: function(response) {
          if ($.type(comment_id) == 'undefined') {
            this.like_unlikeFeed('like', id, comment_id, type);
          }
          else {
            this.like_unlikeComment('like', id, comment_id, type);

          }
        }
      }
    }, {
      //element : $.mobile.activePage.find('#comments')
    });
  },
  unlikeReply: function(type, id, comment_id) {
    if ($.type(comment_id) == 'undefined') {
      this.like_unlikeFeed('unlike', id, comment_id, type);
    } else {
      this.like_unlikeReply('unlike', id, comment_id, type);

    }
    sm4.core.request.send({
      type: "POST",
      dataType: "json",
      url: sm4.core.baseUrl + 'nestedcomment/reply/unlike',
      data: {
        format: 'json',
        type: type,
        id: id,
        comment_id: comment_id
      },
      error: function(xhr, textStatus, errorThrown) {
        if ($.type(comment_id) == 'undefined') {
          this.like_unlikeFeed('like', id, comment_id, type);
        }
        else {
          this.like_unlikeReply('like', id, comment_id, type);

        }
      },
      statusCode: {
        404: function(response) {
          if ($.type(comment_id) == 'undefined') {
            this.like_unlikeFeed('like', id, comment_id, type);
          }
          else {
            this.like_unlikeReply('like', id, comment_id, type);

          }
        }
      }
    }, {
      //element : $.mobile.activePage.find('#comments')
    });
  },  
  showLikes: function(type, id) {
    $.mobile.loading().loader("show");
    sm4.core.request.send({
      type: "POST",
      dataType: "html",
      url: sm4.core.baseUrl + 'core/comment/list',
      data: {
        format: 'html',
        type: type,
        id: id,
        viewAllLikes: true
      },
      success: function(responseHTML, textStatus, xhr) {
        $.mobile.loading().loader("hide");
      }
    }, {
      element: $.mobile.activePage.find('#comments')
    });
  },
  deleteComment: function(type, id, comment_id) {
    if (this.options.self == false) {
      this.options.self = type + '-' + id + '-' + comment_id;
    }

    if (type != '') {
      $.mobile.activePage.find('#popupDialog-Post').parent().css('z-index', '11000');
      $.mobile.activePage.find('#popupDialog-Post').popup("open");

    }
    else {
      var commentinfo = this.options.self.split('-');
      this.options.self = false;
      sm4.core.request.send({
        type: "POST",
        dataType: "json",
        url: sm4.core.baseUrl + 'core/comment/delete',
        data: {
          format: 'json',
          type: commentinfo[0],
          id: commentinfo[1],
          comment_id: commentinfo[2]
        },
        success: function() {
          if ($.mobile.activePage.find('#comment-' + commentinfo[2])) {
            $.mobile.activePage.find('#comment-' + commentinfo[2]).remove();
          }
          try {
            var commentCount = $.mobile.activePage.find('#comments_count')[0].innerHTML;
            var m = commentCount.match(/\d+/);
            var newCount = (parseInt(m[0]) != 'NaN' && parseInt(m[0]) > 1 ? parseInt(m[0]) - 1 : 0);
            commentCount = commentCount.replace(m[0], newCount);
            
            $.mobile.activePage.find('#comments_count')[0].innerHTML = commentCount;
          } catch (e) {
          }
        }
      });
    }
  },
  deleteReply: function(type, id, comment_id) {
    if (this.options.self == false) {
      this.options.self = type + '-' + id + '-' + comment_id;
    }
//var currentpageid = $.mobile.activePage.attr('id');
    if (type != '') {
      //$('#popupDialog-Reply').parent().css('z-index', '11000');
      $('#popupDialog-Reply_'+id).css('display', 'block' );
      
      //$('#'+currentpageid).removeClass('dnone');
    }
    else {
      var commentinfo = this.options.self.split('-');
      $('#popupDialog-Reply_'+ commentinfo[1]).css('display', 'none');  
      var commentinfo = this.options.self.split('-');
      this.options.self = false;
      sm4.core.request.send({
        type: "POST",
        dataType: "json",
        url: sm4.core.baseUrl + 'nestedcomment/reply/delete',
        data: {
          format: 'json',
          type: commentinfo[0],
          id: commentinfo[1],
          comment_id: commentinfo[2]
        },
        success: function(responseJSON, textStatus, xhr) {
          $('#reply_list_' + responseJSON.comment_id).css('display','');
          if ($('#comment-' + responseJSON.comment_id)) {
            $('#comment-' + commentinfo[2]).remove();
          }
          try {
              if(responseJSON.count > 0) {
              $('#reply_list_' + responseJSON.comment_id).find('#reply_link').find('.feed_replies').find('span').html(responseJSON.countWithText);
              
             } else 
             {
               var html = '<li id="no-comments"><div class="no-comments"><i class="ui-icon ui-icon-comment-alt"></i><span>'+ sm4.core.language.translate('No Replies') + '</span></div></li>'
                var li = $('#replies_'+ responseJSON.comment_id).find('.viewcomment').append(html);
                $('#reply_list_' + responseJSON.comment_id).css('display','none'); 
             }
             $('#count-feedcomments_' + responseJSON.comment_id).html(responseJSON.countWithText);
//            var commentCount = $.mobile.activePage.find('.comments_likes span')[0].innerHTML;
//            var m = commentCount.match(/\d+/);
//            var newCount = (parseInt(m[0]) != 'NaN' && parseInt(m[0]) > 1 ? parseInt(m[0]) - 1 : 0);
//            commentCount = commentCount.replace(m[0], newCount);
//            $.mobile.activePage.find('.comments_likes span')[0].innerHTML = commentCount;
          } catch (e) {
          }
        }
      });
    }
  },
  comment_likes: function(id) {
    $.mobile.loading().loader("show");
    $.ajax({
      type: "POST",
      dataType: "json",
      url: sm4.core.baseUrl + 'core/comment/get-likes',
      data: {
        'id': id,
        'type': 'core_comment',
        'format': 'json'
      },
      success: function(responseJSON, textStatus, xhr) {
        var $popup = $('<div data-role="popup" data-theme="e" style="max-width:350px;" aria-disabled="false" data-disabled="false" data-shadow="true" data-corners="true" data-transition="none" data-position-to="#comments_comment_likes_' + id + '" data-dismissible="true" >' + "<p>" + responseJSON.body + "</p>" + '</div>');
        $.mobile.activePage.append($popup);
        $popup.popup();
        $popup.popup('open');
        $popup.on("popupafterclose", function() {
          $popup.remove();
        });
        $.mobile.loading().loader("hide");
      }
    });
  },
  comments_likes_popup: function(type, id, url, preloaded, commentHeading) {
    if ($('.ps-popup-wapper').length > 0) {
      return;
    }

    //IF THIS IS THE APP THEN WE WILL SHOW THE PRELOADED COMMENTS.
    if (preloaded) {

      var $popup = $('<div class="ps-popup-wapper ui-body-c"  style="display:block">' + "<div class='ps-carousel-comments sm-ui-popup'>" + sm4.core.comments.preloadCommentsString + "</div></div>" + '</div>');
      $('body').append($popup);
      sm4.core.runonce.trigger();
      if ($popup.find('#photo-comment-form').length > 0) {
        sm4.core.photocomments.attachCreateComment($popup.find('#photo-comment-form'));
      }
      //REMOVE THE ui-btn-active CLASS FROM THE CROSS BUTTON.
       if ($popup.find('#photo-comments').length > 0) {
          $('#photo-comments').find('a.ps-close-popup').removeClass('ui-btn-active'); 
      }
      if (sm4.core.comments.PreloadCommentRequest)
        return;

    }
    else {
      var commentHeadingStr = '';
      if (commentHeading) {
        commentHeadingStr = '<div class="sm-ui-popup-top ui-header ui-bar-a"><h2 class="ui-title photo_comments_options">' + commentHeading + '</h2></div>';
      }
      var $popup = $('<div class="ps-popup-wapper ui-body-c"  style="display:block">' + "<div class='ps-carousel-comments sm-ui-popup'>" + commentHeadingStr + "<div class='ps_loading sm-ui-popup-loading'></div> </div></div>" + '</div>');
      $('body').append($popup);
    }


    sm4.core.request.send({
      type: "POST",
      dataType: "html",
      url: url,
      data: {
        format: 'html',
        type: type,
        id: id

      },
      success: function(response) {
//        if(preloaded) {
//           if($popup.find('#photo-comment-form-input').length > 0) { console.log($(response).find('#photo-comment-form-input').find('.ui-input-field'))
//             var currenttext = $popup.find('#photo-comment-form-input').find('.ui-input-field').val();
//             
//           }
//        }

        $popup.find('.ps-carousel-comments').html(response).trigger("create");
        if ($popup.find('#photo-comment-form').length > 0) {
          sm4.core.photocomments.attachCreateComment($popup.find('#photo-comment-form'));
        }

        //    sm4.core.photocomments.showLikesUsers(type, id, 1);
        sm4.core.runonce.trigger();
      }
    }, {
      force: true
    });

  }

};

/**
 * Comments
 */
sm4.core.photocomments = {
  options: {
    self: true

  },
  setPhotoRating: function(canRateFlag,rating,resourceId){
if(canRateFlag){
    if(rating == 5)
           return "<div id='rating' class='sr_us_rating fivestar'><span id=1 onclick='sm4.core.photocomments.photoRating(1,"+resourceId+")'></span><span id=2 onclick='sm4.core.photocomments.photoRating(2,"+resourceId+")'></span><span id=3 onclick='sm4.core.photocomments.photoRating(3,"+resourceId+")'></span><span id=4 onclick='sm4.core.photocomments.photoRating(4,"+resourceId+")'></span><span id=5 onclick='sm4.core.photocomments.photoRating(5,"+resourceId+")'></span></div>";
    else if(rating == 4.5)
                   return "<div id='rating' class='sr_us_rating fourhalfstar'><span id=1 onclick='sm4.core.photocomments.photoRating(1,"+resourceId+")'></span><span id=2 onclick='sm4.core.photocomments.photoRating(2,"+resourceId+")'></span><span id=3 onclick='sm4.core.photocomments.photoRating(3,"+resourceId+")'></span><span id=4 onclick='sm4.core.photocomments.photoRating(4,"+resourceId+")'></span><span id=5 onclick='sm4.core.photocomments.photoRating(5,"+resourceId+")'></span></div>";
    else if(rating == 4)
                   return "<div id='rating' class='sr_us_rating fourstar'><span id=1 onclick='sm4.core.photocomments.photoRating(1,"+resourceId+")'></span><span id=2 onclick='sm4.core.photocomments.photoRating(2,"+resourceId+")'></span><span id=3 onclick='sm4.core.photocomments.photoRating(3,"+resourceId+")'></span><span id=4 onclick='sm4.core.photocomments.photoRating(4,"+resourceId+")'></span><span id=5 onclick='sm4.core.photocomments.photoRating(5,"+resourceId+")'></span></div>";
    else if(rating == 3.5)
                   return "<div id='rating' class='sr_us_rating threehalfstar'><span id=1 onclick='sm4.core.photocomments.photoRating(1,"+resourceId+")'></span><span id=2 onclick='sm4.core.photocomments.photoRating(2,"+resourceId+")'></span><span id=3 onclick='sm4.core.photocomments.photoRating(3,"+resourceId+")'></span><span id=4 onclick='sm4.core.photocomments.photoRating(4,"+resourceId+")'></span><span id=5 onclick='sm4.core.photocomments.photoRating(5,"+resourceId+")'></span></div>";
    else if(rating == 3)
                   return "<div id='rating' class='sr_us_rating threestar'><span id=1 onclick='sm4.core.photocomments.photoRating(1,"+resourceId+")'></span><span id=2 onclick='sm4.core.photocomments.photoRating(2,"+resourceId+")'></span><span id=3 onclick='sm4.core.photocomments.photoRating(3,"+resourceId+")'></span><span id=4 onclick='sm4.core.photocomments.photoRating(4,"+resourceId+")'></span><span id=5 onclick='sm4.core.photocomments.photoRating(5,"+resourceId+")'></span></div>";
    else if(rating == 2.5)
                   return "<div id='rating' class='sr_us_rating twohalfstar'><span id=1 onclick='sm4.core.photocomments.photoRating(1,"+resourceId+")'></span><span id=2 onclick='sm4.core.photocomments.photoRating(2,"+resourceId+")'></span><span id=3 onclick='sm4.core.photocomments.photoRating(3,"+resourceId+")'></span><span id=4 onclick='sm4.core.photocomments.photoRating(4,"+resourceId+")'></span><span id=5 onclick='sm4.core.photocomments.photoRating(5,"+resourceId+")'></span></div>";
    else if(rating == 2)
       return "<div id='rating' class='sr_us_rating twostar'><span id=1 onclick='sm4.core.photocomments.photoRating(1,"+resourceId+")'></span><span id=2 onclick='sm4.core.photocomments.photoRating(2,"+resourceId+")'></span><span id=3 onclick='sm4.core.photocomments.photoRating(3,"+resourceId+")'></span><span id=4 onclick='sm4.core.photocomments.photoRating(4,"+resourceId+")'></span><span id=5 onclick='sm4.core.photocomments.photoRating(5,"+resourceId+")'></span></div>";
    else if(rating == 1.5)
        return "<div id='rating' class='sr_us_rating onehalfstar'><span id=1 onclick='sm4.core.photocomments.photoRating(1,"+resourceId+")'></span><span id=2 onclick='sm4.core.photocomments.photoRating(2,"+resourceId+")'></span><span id=3 onclick='sm4.core.photocomments.photoRating(3,"+resourceId+")'></span><span id=4 onclick='sm4.core.photocomments.photoRating(4,"+resourceId+")'></span><span id=5 onclick='sm4.core.photocomments.photoRating(5,"+resourceId+")'></span></div>";
    else if(rating == 1)
        return "<div id='rating' class='sr_us_rating onestar'><span id=1 onclick='sm4.core.photocomments.photoRating(1,"+resourceId+")'></span><span id=2 onclick='sm4.core.photocomments.photoRating(2,"+resourceId+")'></span><span id=3 onclick='sm4.core.photocomments.photoRating(3,"+resourceId+")'></span><span id=4 onclick='sm4.core.photocomments.photoRating(4,"+resourceId+")'></span><span id=5 onclick='sm4.core.photocomments.photoRating(5,"+resourceId+")'></span></div>";
    else if(rating == 0.5)
        return "<div id='rating' class='sr_us_rating halfstar'><span id=1 onclick='sm4.core.photocomments.photoRating(1,"+resourceId+")'></span><span id=2 onclick='sm4.core.photocomments.photoRating(2,"+resourceId+")'></span><span id=3 onclick='sm4.core.photocomments.photoRating(3,"+resourceId+")'></span><span id=4 onclick='sm4.core.photocomments.photoRating(4,"+resourceId+")'></span><span id=5 onclick='sm4.core.photocomments.photoRating(5,"+resourceId+")'></span></div>";
    else if(rating == 0)
        return "<div id='rating' class='sr_us_rating nostar'><span id=1 onclick='sm4.core.photocomments.photoRating(1,"+resourceId+")'></span><span id=2 onclick='sm4.core.photocomments.photoRating(2,"+resourceId+")'></span><span id=3 onclick='sm4.core.photocomments.photoRating(3,"+resourceId+")'></span><span id=4 onclick='sm4.core.photocomments.photoRating(4,"+resourceId+")'></span><span id=5 onclick='sm4.core.photocomments.photoRating(5,"+resourceId+")'></span></div>";
}else{
        if(rating == 5)
           return "<div id='rating' class='sr_us_rating fivestar'><span id=1></span><span id=2></span><span id=3></span><span id=4></span><span id=5></span></div>";
    else if(rating == 4.5)
                   return "<div id='rating' class='sr_us_rating fourhalfstar'><span id=1></span><span id=2></span><span id=3></span><span id=4></span><span id=5></span></div>";
    else if(rating == 4)
                   return "<div id='rating' class='sr_us_rating fourstar'><span id=1></span><span id=2></span><span id=3></span><span id=4></span><span id=5></span></div>";
    else if(rating == 3.5)
                   return "<div id='rating' class='sr_us_rating threehalfstar'><span id=1<span id=1></span><span id=2></span><span id=3></span><span id=4></span><span id=5></span></div>";
    else if(rating == 3)
                   return "<div id='rating' class='sr_us_rating threestar'><span id=1></span><span id=2></span><span id=3></span><span id=4></span><span id=5></span></div>";
    else if(rating == 2.5)
                   return "<div id='rating' class='sr_us_rating twohalfstar'><span id=1></span><span id=2></span><span id=3></span><span id=4></span><span id=5></span></div>";
    else if(rating == 2)
       return "<div id='rating' class='sr_us_rating twostar'><span id=1></span><span id=2></span><span id=3></span><span id=4></span><span id=5></span></div>";
    else if(rating == 1.5)
        return "<div id='rating' class='sr_us_rating onehalfstar'><span id=1></span><span id=2></span><span id=3></span><span id=4></span><span id=5></span></div>";
    else if(rating == 1)
        return "<div id='rating' class='sr_us_rating onestar'><span id=1></span><span id=2></span><span id=3></span><span id=4></span><span id=5></span></div>";
    else if(rating == 0.5)
        return "<div id='rating' class='sr_us_rating halfstar'><span id=1></span><span id=2></span><span id=3></span><span id=4></span><span id=5></span></div>";
    else if(rating == 0)
        return "<div id='rating' class='sr_us_rating nostar'><span id=1></span><span id=2></span><span id=3></span><span id=4></span><span id=5></span></div>";
}
  },
 getRatingClass: function(rating){
     var temp_new_class;
      if(rating == "0")
    temp_new_class="sr_us_rating nostar";
else if(rating == "0.5")
    temp_new_class = "sr_us_rating halfstar";
else if(rating == "1")
    temp_new_class = "sr_us_rating onestar";
else if(rating == "1.5")
    temp_new_class = "sr_us_rating onehalfstar";
else if(rating == "2")
    temp_new_class = "sr_us_rating twostar";
else if(rating == "2.5")
    temp_new_class = "sr_us_rating twohalfstar";
else if(rating == "3")
    temp_new_class = "sr_us_rating threestar";
else if(rating == "3.5")
    temp_new_class = "sr_us_rating threehalfstar";
else if(rating == "4")
    temp_new_class = "sr_us_rating fourstar";
else if(rating == "4.5")
    temp_new_class = "sr_us_rating fourhalfstar";
else if(rating == "5")
    temp_new_class = "sr_us_rating fivestar";
return temp_new_class;
          },
  photoRating: function(rating,resourceId){

     var url = sm4.core.baseUrl + 'sitemobile/photo-gallery/rate';
     sm4.core.request.send({
      type: "POST",
      dataType: "json",
      'url': url,
      'data': {
        'format': 'json',
            'rating': rating,
            'subject_id': resourceId,
            'subject_type': "album_photo"
      },
      success: function(response)
      {
        var temp_new_class,temp_old_class;
        $('#rating').removeAttr('class');
        temp_new_class = sm4.core.photocomments.getRatingClass(rating);
        $("#rating").addClass(temp_new_class);
      }
    });
  },
  loadComments: function(type, id, page) {
    sm4.core.request.send({
      type: "POST",
      dataType: "html",
      url: sm4.core.baseUrl + 'core/photo-comment/list',
      data: {
        format: 'html',
        type: type,
        id: id,
        page: page
      }
    }, {
      element: $('#photo-comments')
    });
  },
  attachCreateComment: function(formElement) {
    var noBlur = false;
    formElement.find("#body").on('blur', function() {
      if (noBlur && !formElement.find("#body").val()) {
        if ($('#photo-comment-form-body'))
          $('#photo-comment-form-body').css('display', 'none');
        if ($('#photo-comment-form-input'))
          $('#photo-comment-form-input').css('display', 'block');
      }
    });
    $('#photo-comment-form-input').on('vclick', function() {
      noBlur = false;
      $(this).css('display', 'none');
      $('#photo-comment-form-body').css('display', 'block');
      $('#photo-comment-form').find('#body').focus();
      setTimeout(function() {
        noBlur = true;
      }, 200);
    });
    formElement.attr('data-ajax', 'false');
    formElement.submit(function(event) {
      event.preventDefault();
      var form_values = formElement.serialize();
      form_values += '&format=json';
      form_values += '&id=' + $("[name='identity']", formElement).val();
      
      if(photoUpload)
          return;
      
      var photo_id, type;
            
      if($.type($("[name='attachment[photo_id]']", formElement).val()) == 'undefined') {
          photo_id = 0;
      } else {
          photo_id = $("[name='attachment[photo_id]']", formElement).val();
      }
      if($.type($("[name='attachment[type]']", formElement).val()) == 'undefined') {
          photo_type = 0;
      } else {
          photo_type = $("[name='attachment[type]']", formElement).val();
      }
      
      var tagfriends = 0;
      if($.type($('#toCommentValues').val()) != 'undefined' && $('#toCommentValues').val()) {
          tagfriends = $('#toCommentValues').val();
      }
      
      if ($("[name='body']", formElement).val() == '' && photo_id == 0 && tagfriends == 0)
        return;
      if(tagfriends != 0)
      form_values += '&toValues=' + tagfriends;
  
      sm4.core.request.send({
        type: "POST",
        dataType: "json",
        url: sm4.core.baseUrl + 'core/photo-comment/create',
        data: form_values,
        success: function(responseJSON) {
          try {
            var id = $("[name='identity']", formElement).val();
            if (sm4.core.isApp() && $.type($.mobile.activePage.find('#likecomment-' + id).get(0)) != 'undefined') {
              var commentCount = $.mobile.activePage.find('#likecomment-' + id).find('span.profileComments').html();
              var m = commentCount.match(/\d+/);
              var newCount = (parseInt(m[0]) != 'NaN' ? parseInt(m[0]) + 1 : 0);
              commentCount = commentCount.replace(m[0], newCount);
              $.mobile.activePage.find('#likecomment-' + id).find('span.profileComments').html(commentCount);
            }
            sm4.activity.resetAdvFeed();
          } catch (e) {
          }

        }
      }, {
        element: $('#photo-comments'),
        showLoading: true
      });

    });
  },
  like_unlikeComment: function(action, action_id, comment_id, type) {
    if (action == 'like') {

      //MAKE LIKE CHANGE TO UNLIKE FIRST AND THEN SEND AJAX REQUEST:
      if ($.type($('#comments_comment_likes_' + comment_id).get(0)) != 'undefined') {
        var likespan = $.trim($('#comments_comment_likes_' + comment_id).html()).split(' ');
        $('#comments_comment_likes_' + comment_id).html(sm4.core.language.translate('% likes this', parseInt(likespan[0]) + parseInt(1)));

      }
      else {
        var likeCountHtml = '<span class="sep">&nbsp;-&nbsp;</span><a href="javascript:void(0);" id="comments_comment_likes_' + comment_id + '" class="comments_comment_likes ui-link" onclick="sm4.core.comments.comment_likes(' + comment_id + ')">' + sm4.core.language.translate('% likes this', 1) + '</a>';

        $('#comment-' + comment_id + ' .comment_likes').after($(likeCountHtml));

      }

      $('#comment-' + comment_id + ' .comment_likes').attr('onclick', 'javascript:sm4.core.photocomments.unlike(\'' + type + '\',' + action_id + ',' + comment_id + ');');

      $('#comment-' + comment_id + ' .comment_likes').html(sm4.core.language.translate('Unlike'));

    }
    else {
      var likespan = $.trim($('#comments_comment_likes_' + comment_id).html()).split(' ');

      if ((parseInt(likespan[0]) - parseInt(1)) > 0)
        $('#comments_comment_likes_' + comment_id).html(sm4.core.language.translate('% likes this', parseInt(likespan[0]) - parseInt(1)));

      else {
        $('#comments_comment_likes_' + comment_id).prev().remove();
        $('#comments_comment_likes_' + comment_id).remove();
      }



      $('#comment-' + comment_id + ' .comment_likes').attr('onclick', 'javascript:sm4.core.photocomments.like(\'' + type + '\',' + action_id + ',' + comment_id + ');');

      $('#comment-' + comment_id + ' .comment_likes').html(sm4.core.language.translate('Like'));

    }

    sm4.core.dloader.refreshPage();

  },
 
  like: function(type, id, comment_id) {
    if ($.type(comment_id) != 'undefined') {
      this.like_unlikeComment('like', id, comment_id, type);
    }
    sm4.core.request.send({
      type: "POST",
      dataType: "json",
      url: sm4.core.baseUrl + 'core/photo-comment/like',
      data: {
        format: 'json',
        type: type,
        id: id,
        comment_id: comment_id
      },
      error: function(xhr, textStatus, errorThrown) {
        if ($.type(comment_id) != 'undefined') {
          this.like_unlikeComment('unlike', id, comment_id, type);
        }

      },
      statusCode: {
        404: function(response) {
          if ($.type(comment_id) != 'undefined') {
            this.like_unlikeComment('unlike', id, comment_id, type);
          }

        }
      }
    }, {
      // element : $('#photo-comments')
    });
  },
  unlike: function(type, id, comment_id) {
    if ($.type(comment_id) != 'undefined') {
      this.like_unlikeComment('unlike', id, comment_id, type);
    }
    sm4.core.request.send({
      type: "POST",
      dataType: "json",
      url: sm4.core.baseUrl + 'core/photo-comment/unlike',
      data: {
        format: 'json',
        type: type,
        id: id,
        comment_id: comment_id
      },
      error: function(xhr, textStatus, errorThrown) {
        if ($.type(comment_id) != 'undefined') {
          this.like_unlikeComment('like', id, comment_id, type);
        }
      },
      statusCode: {
        404: function(response) {
          if ($.type(comment_id) != 'undefined') {
            this.like_unlikeComment('like', id, comment_id, type);
          }
        }
      }
    }, {
      //element : $('#photo-comments')
    });
  },
  toggleCommentLikeList: function(type, id) {
    $('#comments-wrp-' + type + '_' + id).toggle();
    $('#likes-wrp-' + type + '_' + id).toggle();
  },
  toggleCommentDisLikeList: function(type, id) {
    $('#comments-wrp-' + type + '_' + id).toggle();
    $('#dislikes-wrp-' + type + '_' + id).toggle();
  }, 
  showLikesUsers: function(type, id, page) {
    var idEl = 'likes-wrp-' + type + '_' + id, wrpr = idEl;
    if (page > 1) {
      $('#' + wrpr).find('.like_viewmore').remove();
      $('#' + wrpr).find('#likes_viewmore_loading').removeClass('dnone');
      idEl = 'likes-wrp-ul';
    }
    sm4.core.request.send({
      type: "POST",
      dataType: "html",
      url: sm4.core.baseUrl + 'core/photo-comment/get-like-users',
      data: {
        format: 'html',
        type: type,
        id: id,
        page: page
      },
      success: function(response) {

        if (page > 1) {
          $('#' + idEl).append(response);
        } else {
          $('#' + idEl).html(response);
        }
        $('#' + wrpr).find('#likes_viewmore_loading').addClass('dnone');
        if (page > 1) {
          if (page > 1 && $('#' + idEl).find('.like_viewmore').length) {
            $('#' + idEl).after($('#' + idEl).find('.like_viewmore').css('display', 'block'));
          }
          $('#' + idEl).listview().listview('refresh');
        } else {
          $('#' + idEl).trigger("create");
        }

        sm4.core.runonce.trigger();
      }
    }, {
      force: true
    });
  },
  showDisLikesUsers: function(type, id, page) {
    var idEl = 'dislikes-wrp-' + type + '_' + id, wrpr = idEl;
    if (page > 1) {
      $('#' + wrpr).find('.dislike_viewmore').remove();
      $('#' + wrpr).find('#dislikes_viewmore_loading').removeClass('dnone');
      idEl = 'dislikes-wrp-ul';
    }
    sm4.core.request.send({
      type: "POST",
      dataType: "html",
      url: sm4.core.baseUrl + 'core/photo-comment/get-dislike-users',
      data: {
        format: 'html',
        type: type,
        id: id,
        page: page
      },
      success: function(response) {

        if (page > 1) {
          $('#' + idEl).append(response);
        } else {
          $('#' + idEl).html(response);
        }
        $('#' + wrpr).find('#dislikes_viewmore_loading').addClass('dnone');
        if (page > 1) {
          if (page > 1 && $('#' + idEl).find('.dislike_viewmore').length) {
            $('#' + idEl).after($('#' + idEl).find('.dislike_viewmore').css('display', 'block'));
          }
          $('#' + idEl).listview().listview('refresh');
        } else {
          $('#' + idEl).trigger("create");
        }

        sm4.core.runonce.trigger();
      }
    }, {
      force: true
    });
  },  
  showLikes: function(type, id) {
    sm4.core.request.send({
      type: "POST",
      dataType: "html",
      url: sm4.core.baseUrl + 'core/photo-comment/list',
      data: {
        format: 'html',
        type: type,
        id: id,
        viewAllLikes: true
      }
    }, {
      element: $('#photo-comments')
    });
  },
  deleteComment: function(type, id, comment_id) {
    if ($('#comment-' + comment_id)) {
      $('#comment-' + comment_id).remove();
    }
    sm4.core.request.send({
      type: "POST",
      dataType: "json",
      url: sm4.core.baseUrl + 'core/photo-comment/delete',
      data: {
        format: 'json',
        type: type,
        id: id,
        comment_id: comment_id
      },
      success: function() {
        try {
          var commentCount = $('.photo_comments_options').html();
          var m = commentCount.match(/\d+/);
          var newCount = (parseInt(m[0]) != 'NaN' && parseInt(m[0]) > 1 ? parseInt(m[0]) - 1 : 0);
          commentCount = commentCount.replace(m[0], newCount);
          $('.photo_comments_options').html(commentCount);
        } catch (e) {
        }

        //IN CASE OF APP
        if (sm4.core.isApp() && $.type($.mobile.activePage.find('#likecomment-' + id).get(0)) != 'undefined') {
          var commentCount = $.mobile.activePage.find('#likecomment-' + id).find('span.profileComments').html();
          var m = commentCount.match(/\d+/);
          var newCount = (parseInt(m[0]) != 'NaN' && parseInt(m[0]) > 1 ? parseInt(m[0]) - 1 : 0);
          commentCount = commentCount.replace(m[0], newCount);
          $.mobile.activePage.find('#likecomment-' + id).find('span.profileComments').html(commentCount);
        }
      }
    });
  },
  comment_likes: function(id) {
    $.ajax({
      type: "POST",
      dataType: "json",
      url: sm4.core.baseUrl + 'core/photo-comment/get-likes',
      data: {
        'id': id,
        'type': 'core_comment',
        'format': 'json'
      },
      success: function(responseJSON, textStatus, xhr) {
        var $popup = $('<div data-role="popup" data-theme="e" style="max-width:350px;" aria-disabled="false" data-disabled="false" data-shadow="true" data-corners="true" data-transition="none" data-position-to="#comments_comment_likes_' + id + '" data-dismissible="true" >' + "<p>" + responseJSON.body + "</p>" + '</div>');
        $('body').append($popup);
        $popup.popup();
        $popup.popup('open');
        setTimeout(function() {
          $popup.parent('.ui-popup-container').addClass('ps-poup-like-content');
          $(".ui-popup-screen").addClass('ps-poup-like-content');
        }, 100);
        $popup.on("popupafterclose", function() {
          $popup.parent('.ui-popup-container').removeClass('ps-poup-like-content');
          $(".ui-popup-screen").removeClass('ps-poup-like-content');
          $popup.remove();
        });
      }
    });
  },
  album_comments_likes: function(id, type) {
    if ($('.ps-popup-wapper').length > 0) {
      return;
    }
    var $popup = $('<div class="ps-popup-wapper ui-body-c"  style="display:block">' + "<div class='ps-carousel-comments sm-ui-popup'><div class='ps_loading sm-ui-popup-loading'></div> </div></div>" + '</div>');
    $('body').append($popup);

    sm4.core.request.send({
      type: "POST",
      dataType: "html",
      url: sm4.core.baseUrl + 'core/photo-comment/list',
      data: {
        format: 'html',
        type: type,
        id: id
      },
      success: function(response) {
        $popup.find('.ps-carousel-comments').html(response).trigger("create");
        if ($popup.find('#photo-comment-form').length > 0) {
          sm4.core.photocomments.attachCreateComment($popup.find('#photo-comment-form'));
        }
        sm4.core.runonce.trigger();
      }
    });

  },
};



sm4.core.photoGallery = {
  gallery: null,
  set: function(page) {
    
    var fieldToggleGroup = ['.thumbs_photo', '.feed_attachment_group_photo a', '.feed_attachment_event_photo a', '.feed_attachment_sitepage_photo a', '.feed_attachment_list_photo a', '.feed_attachment_recipe_photo a', '.feed_attachment_sitepagenote_photo a', '.feed_attachment_album_photo a', '.feed_attachment_sitebusiness_photo a', '.feed_attachment_sitebusinessnote_photo a', '.feed_attachment_sitegroup_photo a', '.feed_attachment_sitegroupnote_photo a', '.feed_attachment_sitereview_photo a', '.feed_attachment_sitestore_photo a', '.feed_attachment_sitestoreproduct_photo a', '.feed_attachment_siteevent_photo a', '.feed_attachment_sitealbum_photo a'];
    fieldToggleGroup = page.find(fieldToggleGroup.join(',')).attr('data-linktype', 'photo-gallery');
  },
  process: function(photoUrl) {
    if (this.gallery)
      this.gallery.remove();
    this.close();
    $('.sm-ui-popup').remove();
    // $.mobile.showPageLoadingMsg('a', $('<p/>').html($.mobile.loadingPhotoGalleryMessage).text(), true);
    $('body').append('<div id="tempPhotoGallery" style="display:block;"><div class="ps-document-overlay" style="display: block; position: fixed; left: 0px; top: 0px; z-index: 1000; width: 100%; height: 100%; opacity: 1;" data-ccl-disp="block"></div><div class="ps-carousel" style="display: block; position: fixed; left: 0px; top: 0px; overflow: hidden; z-index: 1000; width: 100%; height: 100%;" data-ccl-disp="block"><div class="ps-carousel-content" style="display: table; position: absolute; left: 0px; top: 0px; width: 100%; height: 100%;"><div class="ps-carousel-item ps-carousel-item-loading" style="display: table-cell; position: relative; left: 0px; top: 0px; overflow: hidden;width: 100%; height: 100%;vertical-align:middle;text-align:center;"></div></div></div></div>');
   var self = this;
    $.ajax({
      type: 'GET',
      url: photoUrl,
      data: {
        formatType: 'smjson',
        contentType: 'page',
        photoGallery: true
      },
      dataType: "html",
      success: function(responseJSON, textStatus, xhr) {
        $(document).data('loaded', true);
        try {
          // Hide loading message
          $.mobile.loading().loader("hide");
          
                var responseSplit = responseJSON.split('RESPONSE_HTML_SM');
        var responseHTML = responseSplit[responseSplit.length -1];
        responseJSON = responseJSON.replace( 'RESPONSE_HTML_SM' + responseHTML, '' );
        responseJSON = $.parseJSON(responseJSON);
        responseJSON.responseHTML = responseHTML;
        
          var $htmlBody = $("<div id='photoGalleryContener'></div>").html(responseJSON.responseHTML);
          self.gallery = $htmlBody;
          $('body').append($htmlBody);

          self.show({
            preventHide: false,
            preventSlideshow: !responseJSON.slideshow,
            preventComments: !responseJSON.canComment,
            preventLike: !responseJSON.canComment,
            preventTags: !responseJSON.canTag,
            preventFullview: !responseJSON.fullView,
            preventShare: responseJSON.canShare,
            preventSocialShare: responseJSON.socialShare,
            preventSavePhoto: responseJSON.savePhoto,
            preventViewAlbum: responseJSON.viewAlbum,
            preventReportPhoto: responseJSON.reportPhoto,
            preventPhotoDescription: responseJSON.photoDescription,
            preventDefaultWindowHashChange: true,
            totalImageCount: responseJSON.totalImageCount,
            getImageSource: function(el) {
              return el.href;
            },
            getImageCaption: function(el) {
              var imageCaption = document.createElement("div");
              var captionTop = $(el).data('caption');
              if ($(el).data('count-caption')) {
                 var rating = $(el).data('count-rating');
                 var resourceId = $(el).data('resource-id');
                 var canRateFlag = $(el).data('can-rate');
                if($(el).data('can-see-rating'))
                    captionTop += "<div class='ps-caption-content-count'>" + $(el).data('count-caption') + "</div>"+"<div class='ps-rating'>"+sm4.core.photocomments.setPhotoRating(canRateFlag,rating,resourceId)+"</div>";
                else
                    captionTop += "<div class='ps-caption-content-count'>" + $(el).data('count-caption') + "</div>"+"<div class='ps-rating'>"+"</div>";
              }
              imageCaption.innerHTML = captionTop;
              return imageCaption;
              // return $(el).data('caption');
            },
            getImageDescription: function(el) {
              if ($(el).data('photo-title') != "" || $(el).data('photo-caption')) {
                var imageDescription = document.createElement("div");
                imageDescription.innerHTML = "<div><b>" + $(el).data('photo-title') + "</b></div>" + $(el).data('photo-caption');
                return imageDescription;
                // return $(el).data('caption');
              }
            }
          });
   setTimeout(function() {
          $('#tempPhotoGallery').remove();
}, 1000);
        } catch (errorThrown) {

          throw errorThrown;

        }
      },
      error: function(xhr, textStatus, errorThrown) {
        // self.setError(xhr, textStatus, errorThrown , reqData, reqOptions );
        $(document).data('loaded', true);
      },
      statusCode: {
        404: function(response) {
          // $(document).data('loaded', true);
        }
      }
    });

  },
  show: function(options) {

    var
            photoSwipeInstance = $("ul.gallery a", this.gallery).photoSwipe(options, 'photo_gellary'),
            activePhoto = this.gallery.find("ul.gallery").find('a.active_photo');
    if (activePhoto.length > 0) {
      for (var i = 0; i < photoSwipeInstance.cache.images.length; i++) {
        if (photoSwipeInstance.cache.images[i].refObj == activePhoto[0]) {
          photoSwipeInstance.show(i);
          break;
        }
      }
    } else {
      photoSwipeInstance.show(0);
    }
    var photohideOnShow = function(event, data) {
      if ($('body').hasClass('ps-active') && photoSwipeInstance.settings) {
        if (photoSwipeInstance.isBackEventSupported && photoSwipeInstance.settings.backButtonHideEnabled && data.options.direction == 'back') {
          event.preventDefault();
        }
        $('.ps-popup-wapper').remove();
        photoSwipeInstance.backButtonClicked = true;
        photoSwipeInstance.hide();
      }
      photohideEventRemove();
    }.bind(this);

    $(document).on('pagebeforechange', photohideOnShow);

    var photohideEventRemove = function() {
      $(document).off('pagebeforechange', photohideOnShow);
    }

  },
  close: function() {
    (function(window, $, PhotoSwipe) {
      var photoSwipeInstance = PhotoSwipe.getInstance('photo_gellary');
      if (typeof photoSwipeInstance != "undefined" && photoSwipeInstance != null) {
        PhotoSwipe.detatch(photoSwipeInstance);
      }
    }(window, window.jQuery, window.Code.PhotoSwipe));
  }
};
sm4.core.Module = {
  init: function(params, pageContent, data) {
    var moduleName = params.module, controllerName, actionName, arr, i;
    if (!pageContent) {
      pageContent = $(document).find('#jqm_' + params.contentType + '_' + params.id);
    }

    this.attachEvents();

    if (typeof this[moduleName] == 'object') {
      arr = params.controller.split('-');
      for (i = 1; i < arr.length; i++) {
        arr[i] = arr[i].capitalize();
      }
      controllerName = arr.join('');
      if (typeof this[moduleName][controllerName] == 'object') {
        arr = params.action.split('-');
        for (i = 1; i < arr.length; i++) {
          arr[i] = arr[i].capitalize();
        }
        actionName = arr.join('');
        if (typeof this[moduleName][controllerName][actionName] == 'function') {
          this[moduleName][controllerName][actionName](params, pageContent, data);
        }
      }
    }

    if (data && data.triggerEventsOnContentLoad) {
      $.each(data.triggerEventsOnContentLoad, function(key, value) {
        $(document).trigger(value, data);
      });
    }
  },
  attachEvents: function() {
    $.mobile.activePage.find('.collapsible_icon').on('vclick', function(event) {
      var el = event.target;
      if (el.tagName == 'SPAN') {
        el = el.parentNode;
      }
      var collapsibleEL = $(el.parentNode).find('.collapsible').first();
      if (collapsibleEL.hasClass('collapsible_active')) {
        collapsibleEL.removeClass('collapsible_active');
        $(el).find('.ui-icon').removeClass('ui-icon-minus');
      } else {
        collapsibleEL.addClass('collapsible_active');
        $(el).find('.ui-icon').addClass('ui-icon-minus');
      }
    });
  },
  core: {
    activeParams: {},
    utility: {
      success: function(params, pageContent, data) {
        var redirectTime = (typeof data.redirectTime !== "undefined" && data.redirectTime) ? data.redirectTime : 100;
        // var redirectDelay = 0;
        if (data.responseHTML && !data.notSuccessMessage) {
          $.mobile.loading().loader('show', 'a', $('<p/>').html(data.responseHTML).find('.sucess_message_content').text(), true);
          setTimeout(function() {
            $.mobile.loading().loader('hide');
          }, redirectTime);
        }
        if (data.parentRedirect === 'backToPage') {
          $.mobile.back();
          return;
        }
        if (data.smoothboxClose || data.parentRefresh || data.refresh || data.redirect || data.parentRedirect) {

          setTimeout(function() {
            var poptions = {
              reloadPage: true,
              clear_cache: data.clear_cache,
              showLoadMsg: params.showLoadMsg
            };
            if (params.splashscreen) {
              poptions.splashscreen = true;
            }
            var url = sm4.core.location.pathname;

            if (data.parentRedirect) {
              url = data.parentRedirect;
            } else if (data.redirect) {
              url = data.redirect;
            }
            if (sm4.core.isApp()) {
              url = appconfig.settings.location.origin + url;
            }
            $.mobile.changePage(url, poptions);
            if (data.onloadFirstPage == 1 && $.mobile.firstPage && $.mobile.firstPage[0]) {
              $.mobile.firstPage = {};
            }
            setTimeout(function() {
              if (data.clear_cache) {
                sm4.core.cache.clear();
              }
            }, 2000);
          }, redirectTime);
          return false;
        }

      }
    },
    search: {
      activeRequest: false,
      currentPage: 1,
      totalPages: 1,
      index: function(params, pageContent) {
        $(".iscroll-wrapper", $(pageContent)).bind({
          //iscroll_onpulldown : onPullDown,
          iscroll_onpullup: sm4.core.Module.core.search.getSearchContent
        });

      },
      getSearchContent: function() {
        $.mobile.activePage.find('.scroll-pullup').addClass('dnone');
        var self = sm4.core.Module.core.search;

        if (self.currentPage >= self.totalPages)
          return;
        if (self.activeRequest == true)
          return;
        self.activeRequest = true;
        $.mobile.activePage.find('.scroll-pullup').removeClass('dnone');
        $.ajax({
          type: "POST",
          dataType: "html",
          url: sm4.core.baseUrl + 'search',
          data: {
            'format': 'html',
            query: $('#query').val(),
            'type': $('#type').val(),
            isappajax: true,
            'page': parseInt(self.currentPage) + parseInt(1)
          },
          success: function(responseHTML, textStatus, xhr) {
            $.mobile.activePage.find('.scroll-pullup').addClass('dnone');
            self.activeRequest = false;
            $.mobile.activePage.find('#search_viewmore').css('display', 'none');
            $(document).data('loaded', true);
            $.mobile.activePage.find('#searchmembers_ul').append(responseHTML);
            sm4.core.dloader.refreshPage();
            sm4.core.runonce.trigger();
            $.mobile.activePage.find('#searchmembers_ul').listview('refresh');
          }
        });

      }

    },
    getPaginatorContent: function() {
      // $.mobile.activePage.find('.scroll-pullup').addClass('dnone');
      var self = sm4.core.Module.core;

      var activePageParams = self.activeParams[sm4.activity.activityUpdateHandler.getIndexId()];
      if (parseInt(activePageParams.currentPage) >= parseInt(activePageParams.totalPages))
        return;
      if (activePageParams.activeRequest == true)
        return;
      self.activeParams[sm4.activity.activityUpdateHandler.getIndexId()].activeRequest = true;
      $.mobile.activePage.find('.scroll-pullup').removeClass('dnone');

      $.ajax({
        type: "POST",
        dataType: "html",
        url: activePageParams.contentUrl,
        data: $.extend((activePageParams.formValues && activePageParams.formValues != '') ? activePageParams.formValues : {}, {
          'format': 'html',
          isappajax: true,
          ajax: true,
          'page': parseInt(activePageParams.currentPage) + parseInt(1),
          'subject': sm4.core.subject.guid != '' ? sm4.core.subject.guid : $.mobile.activePage.attr("data-subject"),
        }),
        success: function(responseHTML, textStatus, xhr) {
          $.mobile.activePage.find('.scroll-pullup').addClass('dnone');
          self.activeParams[sm4.activity.activityUpdateHandler.getIndexId()].activeRequest = false;
          //$.mobile.activePage.find('#browsemembers_viewmore').css('display', 'none');
          $(document).data('loaded', true);
          $.mobile.activePage.find('#' + activePageParams.container).append(responseHTML);
          sm4.core.dloader.refreshPage();
          sm4.core.refreshPage();
          if ($.mobile.activePage.find('#' + activePageParams.container).jqmData('role') == 'listview')
            $.mobile.activePage.find('#' + activePageParams.container).listview().listview('refresh');
          sm4.core.runonce.trigger();
        }
      });
    },
    bindAutoScroll: function(params, pageContent) {
      $(pageContent).bind({
        //iscroll_onpulldown : onPullDown,
        upscroll: sm4.core.Module.core.getPaginatorContent
      });

    },
//    showLoadingBottom: function(show_hide, container) {
//      if(show_hide == 'show') {
//        var temp = $('<div />', {          
//            'class': 'feeds_loading',
//            'html': '<i class="icon_loading"></i>'
//          });
//          $(container).after(temp);
//      } else {
//         $.mobile.activePage.find('.feeds_loading').remove();
//      }
//    }
  },
  music: {
    index: {
      browse: function(params, pageContent) {
        sm4.core.Module.core.bindAutoScroll(params, pageContent);
      },
      manage: function(params, pageContent) {
        sm4.core.Module.core.bindAutoScroll(params, pageContent);
      },
    }
  },
  user: {
    index: {
      activeParams: {},
      browse: function(params, pageContent) {
        sm4.core.Module.core.bindAutoScroll(params, pageContent);
      },
      friends: function(params, pageContent) {
        sm4.core.Module.core.bindAutoScroll(params, pageContent);
      }
    },
    auth: {
      loginMobile: function(params, pageContent) {
        if (pageContent.find('#facebook-element').length)
          pageContent.find('#facebook-element').find('a').attr('data-ajax', 'false');
        if (pageContent.find('#twitter-element').length)
          pageContent.find('#twitter-element').find('a').attr('data-ajax', 'false');
      }
    }
  },
  album: {
    index: {
      upload: function(params, pageContent) {
        var fieldToggleGroup = ['#title-wrapper', '#category_id-wrapper', '#description-wrapper', '#search-wrapper',
          '#auth_view-wrapper', '#auth_comment-wrapper', '#auth_tag-wrapper'];
        fieldToggleGroup = pageContent.find('form').find(fieldToggleGroup.join(','));
        var elAlbum = pageContent.find('form').find('#album');
        if (elAlbum.val() == 0) {
          fieldToggleGroup.show();
        } else {
          fieldToggleGroup.hide();
        }
        elAlbum.removeAttr('onchange').bind('change', function(event) {
          if ($(this).val() == 0)
            fieldToggleGroup.show();
          else
            fieldToggleGroup.hide();
        });
      },
      browse: function(params, pageContent) {
        sm4.core.Module.core.bindAutoScroll(params, pageContent);

      },
      manage: function(params, pageContent) {

        sm4.core.Module.core.bindAutoScroll(params, pageContent);
      },
      photos: function(params, pageContent) {

        sm4.core.Module.core.bindAutoScroll(params, pageContent);

        var getAjaxFilterTabContent = function() {
          $.mobile.loading().loader("show");
          var activepage_id = sm4.activity.activityUpdateHandler.getIndexId();

          $.ajax({
            type: "POST",
            dataType: "html",
            url: sm4.core.Module.core.activeParams[activepage_id].contentUrl,
            data: {
              'format': 'html',
              isappajax: 0,
              ajax: true,
              tabName: $(this).val()
            },
            success: function(responseHTML, textStatus, xhr) {
              $.mobile.loading().loader("hide");
              $(document).data('loaded', true);
              $.mobile.activePage.find('.layout_sitemobile_album_browse_photos').html(responseHTML);
              sm4.core.dloader.refreshPage();
              sm4.core.refreshPage();
              sm4.core.runonce.trigger();
              pageContent.find('#filter_type').bind('change', getAjaxFilterTabContent);
            }
          });
        }
        //ADD ONCHANGE EVENT TO FILTER DROP-DOWN
        pageContent.find('#filter_type').bind('change', getAjaxFilterTabContent);
      },
    },
    album: {
      view: function(params, pageContent) {
        sm4.core.Module.core.bindAutoScroll(params, pageContent);
      }
    }
  },
  sitealbum:{
    index:{
      browse: function(params, pageContent) {
          sm4.core.Module.core.bindAutoScroll(params, pageContent);
        },
      manage: function(params, pageContent) {
          sm4.core.Module.core.bindAutoScroll(params, pageContent);
        }
    },
    album: { 
      view: function(params, pageContent) {
        $.mobile.activePage.find('.scroll-pullup').appendTo($.mobile.activePage.find('.layout_sitealbum_album_view'));
        sm4.core.Module.core.bindAutoScroll(params, pageContent);       
      }
    }
  },
  event: {
    index: {
      browse: function(params, pageContent) {
        sm4.core.Module.core.bindAutoScroll(params, pageContent);
      },
      manage: function(params, pageContent) {
        sm4.core.Module.core.bindAutoScroll(params, pageContent);
      }
    },
    profile: {
      index: function(params, pageContent) {
        sm4.core.Module.core.bindAutoScroll(params, pageContent);
      }
    }
  },
  blog: {
    index: {
      index: function(params, pageContent) {
        sm4.core.Module.core.bindAutoScroll(params, pageContent);
      },
      manage: function(params, pageContent) {
        sm4.core.Module.core.bindAutoScroll(params, pageContent);
      }
    }
  },
  video: {
    index: {
      current_code: null,
      ignoreValidation: function() {
        var pageContent = $.mobile.activePage;
        pageContent.find('#upload-wrapper').show();
        pageContent.find('#validation').hide();
        pageContent.find('#code').val(this.current_code);
        pageContent.find('#ignore').val(true);
      },
      create: function(params, pageContent) {
        sm4.core.Module.videoAttach(params, pageContent);
        //  sm4.core.Module.core.bindAutoScroll(params, pageContent);
      },
      browse: function(params, pageContent) {
        sm4.core.Module.core.bindAutoScroll(params, pageContent);
      },
      manage: function(params, pageContent) {
        sm4.core.Module.core.bindAutoScroll(params, pageContent);
      }


    }
  },
  group: {
    index: {
      browse: function(params, pageContent) {
        sm4.core.Module.core.bindAutoScroll(params, pageContent);
      },
      manage: function(params, pageContent) {
        sm4.core.Module.core.bindAutoScroll(params, pageContent);
      },
    }
  },
  sitepage: {
    index: {
      index: function(params, pageContent) {
        sm4.core.Module.core.bindAutoScroll(params, pageContent);
      },
      manage: function(params, pageContent) {
        sm4.core.Module.core.bindAutoScroll(params, pageContent);
      }
    },
    album: {
      browse: function(params, pageContent) {
        sm4.core.Module.core.bindAutoScroll(params, pageContent);
      }
    },
    like: {
      mylikes: function(params, pageContent) {
        sm4.core.Module.core.bindAutoScroll(params, pageContent);
      },
      myJoined: function(params, pageContent) {
        sm4.core.Module.core.bindAutoScroll(params, pageContent);
      }
    },
    manageadmin: {
      myPages: function(params, pageContent) {
        sm4.core.Module.core.bindAutoScroll(params, pageContent);
      }
    }
  },
  sitepagemember: {
    index: {
      index: function(params, pageContent) {
        sm4.core.Module.core.bindAutoScroll(params, pageContent);
      }
    }
  },
  sitepagereview: {
    index: {
      browse: function(params, pageContent) {
        sm4.core.Module.core.bindAutoScroll(params, pageContent);
      }
    }
  },
  sitepageoffer: {
    index: {
      browse: function(params, pageContent) {
        sm4.core.Module.core.bindAutoScroll(params, pageContent);
      }
    }
  },
  sitegroup: {
    index: {
      index: function(params, pageContent) {
        sm4.core.Module.core.bindAutoScroll(params, pageContent);
      },
      manage: function(params, pageContent) {
        sm4.core.Module.core.bindAutoScroll(params, pageContent);
      }
    },
    album: {
      browse: function(params, pageContent) {
        sm4.core.Module.core.bindAutoScroll(params, pageContent);
      }
    },
    like: {
      mylikes: function(params, pageContent) {
        sm4.core.Module.core.bindAutoScroll(params, pageContent);
      },
      myJoined: function(params, pageContent) {
        sm4.core.Module.core.bindAutoScroll(params, pageContent);
      }
    },
    manageadmin: {
      myPages: function(params, pageContent) {
        sm4.core.Module.core.bindAutoScroll(params, pageContent);
      }
    }
  },
  sitegroupmember: {
    index: {
      index: function(params, pageContent) {
        sm4.core.Module.core.bindAutoScroll(params, pageContent);
      }
    }
  },
  sitegroupreview: {
    index: {
      browse: function(params, pageContent) {
        sm4.core.Module.core.bindAutoScroll(params, pageContent);
      }
    }
  },
  sitegroupoffer: {
    index: {
      browse: function(params, pageContent) {
        sm4.core.Module.core.bindAutoScroll(params, pageContent);
      }
    }
  },
  sitebusiness: {
    index: {
      index: function(params, pageContent) {
        sm4.core.Module.core.bindAutoScroll(params, pageContent);
      },
      manage: function(params, pageContent) {
        sm4.core.Module.core.bindAutoScroll(params, pageContent);
      }
    },
    album: {
      browse: function(params, pageContent) {
        sm4.core.Module.core.bindAutoScroll(params, pageContent);
      }
    },
    like: {
      mylikes: function(params, pageContent) {
        sm4.core.Module.core.bindAutoScroll(params, pageContent);
      },
      myJoined: function(params, pageContent) {
        sm4.core.Module.core.bindAutoScroll(params, pageContent);
      }
    },
    manageadmin: {
      myPages: function(params, pageContent) {
        sm4.core.Module.core.bindAutoScroll(params, pageContent);
      }
    }
  },
  sitebusinessmember: {
    index: {
      index: function(params, pageContent) {
        sm4.core.Module.core.bindAutoScroll(params, pageContent);
      }
    }
  },
  sitebusinessreview: {
    index: {
      browse: function(params, pageContent) {
        sm4.core.Module.core.bindAutoScroll(params, pageContent);
      }
    }
  },
  sitebusinessoffer: {
    index: {
      browse: function(params, pageContent) {
        sm4.core.Module.core.bindAutoScroll(params, pageContent);
      }
    }
  },
  sitestorevideo: {
    index: {
      create: function(params, pageContent) {
        sm4.core.Module.videoAttach(params, pageContent);
      }
    }
  },
  siteevent: {
    index: {
      index: function(params, pageContent) {
        sm4.core.Module.core.bindAutoScroll(params, pageContent);
      },
      manage: function(params, pageContent) {
        sm4.core.Module.core.bindAutoScroll(params, pageContent);
      }
    },
    video: {
      create: function(params, pageContent) {
        sm4.core.Module.videoAttach(params, pageContent);
      }
    }, diary: {
      browse: function(params, pageContent) {
        sm4.core.Module.core.bindAutoScroll(params, pageContent);
      }
    }, review: {
      browse: function(params, pageContent) {
        sm4.core.Module.core.bindAutoScroll(params, pageContent);
      }
    }
  },
  sitepagevideo: {
    index: {
      create: function(params, pageContent) {
        sm4.core.Module.videoAttach(params, pageContent);
      },
      browse: function(params, pageContent) {
        sm4.core.Module.core.bindAutoScroll(params, pageContent);
      }
    }
  },
  sitebusinessvideo: {
    index: {
      create: function(params, pageContent) {
        sm4.core.Module.videoAttach(params, pageContent);
      },
      browse: function(params, pageContent) {
        sm4.core.Module.core.bindAutoScroll(params, pageContent);
      }
    }
  },
  sitegroupvideo: {
    index: {
      create: function(params, pageContent) {
        sm4.core.Module.videoAttach(params, pageContent);
      },
      browse: function(params, pageContent) {
        sm4.core.Module.core.bindAutoScroll(params, pageContent);
      }
    }
  },
  sitemember: {
    location: {
      userbyLocations: function(params, pageContent) {
        sm4.core.Module.core.bindAutoScroll(params, pageContent);
      }
    }
  },
  sitereview: {
    video: {
      current_code: null,
      ignoreValidation: function() {
        var pageContent = $.mobile.activePage;
        pageContent.find('#upload-wrapper').show();
        pageContent.find('#validation').hide();
        pageContent.find('#code').val(this.current_code);
        pageContent.find('#ignore').val(true);
      },
      index: function(params, pageContent) {
        sm4.core.Module.videoAttach(params, pageContent);
      },
      create: function(params, pageContent) {
        sm4.core.Module.videoAttach(params, pageContent);
      }
    },
    index: {
      index: function(params, pageContent) {
        sm4.core.Module.core.bindAutoScroll(params, pageContent);
      },
      manage: function(params, pageContent) {
        sm4.core.Module.core.bindAutoScroll(params, pageContent);
      },
      topRated: function(params, pageContent) {
        sm4.core.Module.core.bindAutoScroll(params, pageContent);
      }
    },
    review: {
      browse: function(params, pageContent) {
        sm4.core.Module.core.bindAutoScroll(params, pageContent);
      }
    },
    wishlist: {
      browse: function(params, pageContent) {
        sm4.core.Module.core.bindAutoScroll(params, pageContent);
      },
      profile: function(params, pageContent) {
        sm4.core.Module.core.bindAutoScroll(params, pageContent);
      }
    },
  },
  videoAttach: function(params, pageContent) {
    var self = this;
    self.current_code = null;

    var updateTextFields = function() {
      var video_element = pageContent.find("#type");
      var url_element = pageContent.find("#url-wrapper");
      var file_element = pageContent.find("#file-wrapper");
      var submit_element = pageContent.find("#upload-wrapper");

//      url_element.show();
//      file_element.show();
      submit_element.show();
//      return;
      //     pageContent.find('#upload-wrapper').hide();
      var $urlElement = pageContent.find('#url');
      if (video_element.val() == 0) {
        $urlElement.val('');
        file_element.hide();
        url_element.hide();
        return;
      } else if (pageContent.find('#code').val() && $urlElement.val()) {
        pageContent.find('#type-wrapper').hide();
        file_element.hide();
        return;
      } else if (video_element.val() == 1 || video_element.val() == 2) {
        $urlElement.val('');
        pageContent.find('#code').val('');
        file_element.hide();
        url_element.show();
        return;
      } else if (video_element.val() == 3) {
        $urlElement.val('');
        pageContent.find('#code').val('');
        file_element.show();
        url_element.hide();
        return;
      } else if (pageContent.find('#id').val()) {
        pageContent.find('#type-wrapper').hide();
        file_element.hide();
        return;
      }
    }

    pageContent.find('form').find('#type').removeAttr('onchange').bind('change', updateTextFields);
    var video = {
      active: false,
      debug: false,
      currentUrl: null,
      currentTitle: null,
      currentDescription: null,
      currentImage: 0,
      currentImageSrc: null,
      imagesLoading: 0,
      images: [],
      maxAspect: (10 / 3), //(5 / 2), //3.1,

      minAspect: (3 / 10), //(2 / 5), //(1 / 3.1),

      minSize: 50,
      maxPixels: 500000,
      monitorInterval: null,
      monitorLastActivity: false,
      monitorDelay: 500,
      maxImageLoading: 5000,
      attach: function() {
        var selfbind = this;
        var $urlElement = pageContent.find('#url');
        var submitElement = pageContent.find('#upload');
        $urlElement.bind('keyup', function() {
          selfbind.monitorLastActivity = (new Date).valueOf();
        });

//          pageContent.find('#type').on('change',function(){
//            alert("ghsshhdfhfd");
//            alert($(this).val())
//          });
        var url_element = pageContent.find("#url-element");
        var myElement = $("<p />");
        //  myElement.html(checkingUrlMessage);
        myElement.addClass("description");
        myElement.attr({
          'id': "validation"
        });
        myElement.hide();
        url_element.append(myElement);
        var body = $urlElement;
        var lastBody = '';
        var lastMatch = '';
        var video_element = pageContent.find('#type');
        var submitButton = sm4.core.isApp() ? pageContent.find(".header_submit_button") : pageContent.find("#upload");
        submitButton.off('vclick').on('vclick', function(e) {
          // Ignore if no change or url matches
          e.preventDefault();

          if (video_element.val() != 3 && (body.val() == lastBody || selfbind.currentUrl || video_element.val() == 0)) {

            alert('Please choose video type and enter video Url.');
            return;
          }
          if (video_element.val() == 3) {
            if (sm4.core.isApp()) {
              pageContent.find(pageContent.find(".header_submit_button").jqmData('rel')).submit();
            } else {
              pageContent.find('#upload-wrapper').parents('form').submit();
            }
            return;
          }
          //               // Ignore if delay not met yet
          //               if( (new Date).valueOf() < bind.monitorLastActivity + bind.monitorDelay ) {
          //                 return;
          //               }

          // Check for link
          var m = body.val().match(/https?:\/\/([-\w\.]+)+(:\d+)?(\/([-#:\w/_\.]*(\?\S+)?)?)?/);
          if ($.type(m) == 'array' && $.type(m[0]) && lastMatch != m[0]) {

            if (video_element.val() == 1) {
              video.youtube(body.val());
            } else {
              video.vimeo(body.val());
            }
          }
          lastBody = body.val();
        });
      },
      youtube: function(url) {

        var youtube_code = video.videoParameter(url, 'v');

        if (youtube_code === undefined || youtube_code == 0) {
          youtube_code = $.mobile.path.parseUrl(url).filename;
        }

        if (youtube_code) {
          pageContent.find('#validation').show();
          //pageContent.find('#validation').html(checkingUrlMessage);
          pageContent.find('#upload-wrapper').hide();
          $.mobile.loading().loader("show");
          //$.mobile.showPageLoadingMsg('a', checkingUrlMessage);
          $.ajax({
            type: 'post',
            url: sm4.core.baseUrl + 'video/index/validation', //validationUrl,
            data: {
              'ajax': true,
              'code': youtube_code,
              'type': 'youtube',
              'format': 'json'
            },
            success: function(response) {
              $.mobile.loading().loader("hide");
              if (response.valid) {
                pageContent.find('#upload-wrapper').show();
                pageContent.find('#validation').hide();
                pageContent.find('#code').val(youtube_code);
                if (sm4.core.isApp()) {
                  pageContent.find(pageContent.find(".header_submit_button").jqmData('rel')).submit();
                } else {
                  pageContent.find('#upload-wrapper').parents('form').submit();
                }
              } else {
                pageContent.find('#upload-wrapper').hide();
                self.current_code = youtube_code;
                pageContent.find('#validation').html(validationErrorMessage);
              }
            },
            dataType: 'json'
          });
        }
      },
      vimeo: function(url) {
        var vimeo_code = $.mobile.path.parseUrl(url).filename;
        if (vimeo_code.length > 0) {
          pageContent.find('#validation').show();
          //pageContent.find('#validation').html(checkingUrlMessage);
          pageContent.find('#upload-wrapper').hide();
          $.mobile.loading().loader("show");
          $.ajax({
            type: 'post',
            url: sm4.core.baseUrl + 'video/index/validation', //validationUrl,
            data: {
              'ajax': true,
              'code': vimeo_code,
              'type': 'vimeo',
              'format': 'json'
            },
            success: function(response) {
              $.mobile.loading().loader("hide");
              if (response.valid) {

                pageContent.find('#validation').hide();
                pageContent.find('#code').val(vimeo_code);
                if (sm4.core.isApp()) {
                  pageContent.find(pageContent.find(".header_submit_button").jqmData('rel')).submit();
                } else {
                  pageContent.find('#upload-wrapper').parents('form').submit();
                }
              } else {
                pageContent.find('#upload-wrapper').hide();
                self.current_code = vimeo_code;
                pageContent.find('#validation').html(validationErrorMessage);
              }
            },
            dataType: 'json'
          });
        }
      },
      videoParameter: function(arg1, arg2) {
        var href;
        var name = null;

        if ($.type(arg2) == 'string' && $.type(arg1) == 'string') {
          href = arg1;
          name = arg2;
        }
        else if ($.type(arg1) == 'string') {
          href = window.location.href;
          name = arg1;
        } else if (!arguments || arguments.length == 0) {
          return;
        }
        var results = new RegExp('[\\?&]' + name + '=([^&#]*)').exec(href);
        return results ? results[1] : 0;
      }
    }

    updateTextFields();
    video.attach();
  },
  setContent: function(page) {
    var beforeCloseOverride = false;
    var currentPopupClose = function() {
      $('.popup_notification_active').each(function() {
        $('.popup_attach_notification').each(function(el) {
          var $l = $(this);
          if ($l.attr('active') == 'true') {
            setTimeout(function() {
              $l.removeAttr('active');
            }, 70);
          } else {
            $l.removeAttr('active');
          }
        });

        $(this).addClass('ui-popup-hidden ui-popup-truncate').removeClass('ui-popup-active').removeClass('popup_notification_active');
        page.find('#' + $(this).find('[data-role="popup"]').attr('id') + '-screen').removeClass('in').addClass('ui-screen-hidden');
      });
      if (beforeCloseOverride) {
        sm4.core.closefunctions.fns = beforeCloseOverride;
        beforeCloseOverride = false;
      }
    };
    page.find('.popup_attach_notification').on('click', function(event) {
      var $link = $(event.target), popup;
      if (!$link.hasClass('popup_attach_notification')) {
        $link = $link.parent('.popup_attach_notification');
      }

      var popup = page.find($link.jqmData('rel') + '-popup'), options = {
        maxwidth: 2000,
        maxheight: 1200,
      }, offset = $link.offset(), screen = page.find($link.jqmData('rel') + '-screen');

      if ($link.attr('active') !== 'true') {
        currentPopupClose();

        beforeCloseOverride = sm4.core.closefunctions.fns;
        sm4.core.closefunctions.fns = [];
        sm4.core.closefunctions.add(currentPopupClose);
        screen.addClass('in').removeClass('ui-screen-hidden');
        screen.off('vclick');
        screen.on('vclick', currentPopupClose);
        if ($(window).width() > options.maxwidth)
          var width = options.maxwidth;
        else
          var width = $(window).width();

        var heigth = options.maxheight;

        if ($(window).height() - 30 < options.maxheight)
          heigth = $(window).height() - 30;

        //width = width - 20;
        if (page.hasClass('ui-page-header-fixed')) {
          offset.top - $(document).scrollTop();
          heigth = heigth - (offset.top - $(document).scrollTop() - 11);
          popup.css({
            'width': '100%', // width,
            //  'max-height': heigth - (offset.top - $(document).scrollTop() + 30),
            'top': offset.top - $(document).scrollTop() + 20,
            'left': 0, //(($(window).width() / 2) - (width / 2))
            'position': 'fixed'
          });
        } else {
          popup.css({
            'width': '100%', // width,
            'max-height': heigth - (offset.top + 30),
            'top': offset.top + 20,
            'left': 0 //(($(window).width() / 2) - (width / 2))
          });
        }
        //ui-popup-hidden
        popup.find('.sm-ui-popup-container').css({
          'height': (heigth - popup.find('.sm-ui-popup-top').outerHeight() - popup.find('.sm-ui-popup-notification-footer').outerHeight() - 25)
        });
        popup.find('.sm-ui-popup-container').find('ul').css('display', 'block');
//        popup.css({
//          'display': 'block'
//        });
        popup.removeClass('ui-popup-hidden ui-popup-truncate').addClass('ui-popup-active');
        var leftarrow = offset.left - popup.offset().left + 9;
        popup.find('.popup_notification_arrow').removeAttr("style");
        popup.find('.popup_notification_arrow').css('left', leftarrow);
        popup.addClass('popup_notification_active');
        $('.popup_attach_notification').removeAttr('active');
        $link.attr('active', 'true');

      } else {
        $('.popup_attach_notification').removeAttr('active');
      }

    });
    page.find('.iscroll_carousal').each(function() {
      var self = $(this), wrapper = self.find('.iscroll_carousal_wrapper'), height = 0, width = page.width();
      if (self.jqmData('width')) {
        width = self.jqmData('width');
      }
      if ($('#MainContentWelcomeSlideShow').length < 1) {
        if (width > 500)
          width = 500;

        height = width;
      }
      width - 20;
      if (!$('#MainContentWelcomeSlideShow')) {
        height = width;
      }
      if (self.jqmData('height')) {
        height = self.jqmData('height');
      }
      wrapper.css('width', width);
      wrapper.css('height', height);
      wrapper.find('.iscroll_carousal_scroller ul> li').css('width', width);
      wrapper.find('.iscroll_carousal_scroller ul> li').css('height', height);
      wrapper.find('.iscroll_carousal_scroller').css('width', width * wrapper.jqmData('itemcount'));
      self.find('.iscroll_carousal_nav').css('width', width);
      setTimeout(function() {
        var icarousal = new IScroll(self.find('.iscroll_carousal_wrapper')[0], {
          scrollX: true,
          scrollY: false,
          momentum: false,
          snap: true,
          snapSpeed: 400,
          keyBindings: true,
          //  onScrollEnd: 
        });
        icarousal.on('scrollEnd', function() {
          self.find('.iscroll_carousal_indicator > li.active').removeClass('active');
          self.find('.iscroll_carousal_indicator > li:nth-child(' + (this.currentPage.pageX + 1) + ')').addClass('active');
        });
        self.find('.iscroll_carousal_prev').on('vclick', function() {
          icarousal.prev();
        });
        self.find('.iscroll_carousal_next').on('vclick', function() {
          icarousal.next();
        });
      }, 30);
    });
    this.setSubPage(page);
    this.setFormContent(page);
    sm4.core.photoGallery.set(page);
    this.setCalenderDateTime(page);
    this.setHeaderLogo(page);
    this.setTouchEventForPanelOpen(page);
  },
  setTouchEventForPanelOpen: function(page) {
    if (sm4.core.isApp()) {
      var dashboardPanelId = page.jqmData('dashboardpanelid') ? '#' + page.jqmData('dashboardpanelid') : '#dashboardPanelMenu';
      if (page.find(dashboardPanelId).length) {
        var panel = page.find(dashboardPanelId);
        //   panel.insertAfter(page);
        page.find('#header-dashboard-menuButton').off('vclick').on('vclick', function(e) {
          e.preventDefault();
          if (page.jqmData('panel') === 'open') {
            panel.panel("close");
          } else {
            panel.panel("open");
          }

        });
      }
    }
  },
  setHeaderLogo: function(page) {
    if (sm4.core.isApp() && appconfig && appconfig.siteInfo.logoIconSrc) {
      page.find('.ui-header [data-logo="true"]').each(function() {
        $(this).append($('<img />').attr({'src': appconfig.siteInfo.logoIconSrc}));
        $(this).removeClass('ui-btn-icon-notext').addClass('ui-btn-icon-left header_logo_link');
        page.find('.ui-header').removeClass('ui-header-logo').addClass('ui-header-logo');
      });
    }
  },
  setSubPage: function(page) {
    $("div:jqmData(role='subpage')").each(function() {
      var $this = $(this);
      $this.attr('data-role', "page");
      $("div:jqmData(role='subheader')", $this).each(function() {
        $(this).attr('data-role', "header").addClass('ui-page-header-layout');
      });
      $("button[type='submit']", $this).each(function() {
        $(this).attr('data-theme', "a");
        $(this).attr('data-inline', "true");
        $(this).attr('class', 'fixed-header-button');
        $(this).html(sm4.core.language.translate('Submit'));
        var wrapper = $(this).closest('.form-wrapper');
        wrapper.before($(this));
        wrapper.remove();
      });
      $this.appendTo($.mobile.pageContainer);
      setTimeout(function() {
        $this.page();
      }, 1000);

    });
  },
  widgetAjaxPaginationContent: function(thisObject, widgetIdentity, anchor, datas) {
    url = '';
    if (datas.url) {
      url = datas.url;
    } else {
      if ($.mobile.activePage.attr('id') == 'jqm_page_sitepage-index-view-' + widgetIdentity) {
        url = sm4.core.baseUrl + 'sitepage/mobile-widget/index/content_id/' + widgetIdentity;
      } else if ($.mobile.activePage.attr('id') == 'jqm_page_sitebusiness-index-view-' + widgetIdentity) {
        url = sm4.core.baseUrl + 'sitebusiness/mobile-widget/index/content_id/' + widgetIdentity;
      } else if ($.mobile.activePage.attr('id') == 'jqm_page_sitegroup-index-view-' + widgetIdentity) {
        url = sm4.core.baseUrl + 'sitegroup/mobile-widget/index/content_id/' + widgetIdentity;
      } else {
        url = sm4.core.baseUrl + 'core/widget/index/content_id/' + widgetIdentity;
      }
    }
    var wrpr = $.mobile.activePage.find("#" + anchor).parent(), content = wrpr.find('ul'), hasViewmore = !!$(thisObject).jqmData('viewmore'), options = {};
    if (hasViewmore) {
      wrpr.find('.paginationViewmore #view_more').addClass('dnone');
      wrpr.find('.paginationViewmore #loading').removeClass('dnone');
    } else {
      options = {
        'element': $.mobile.activePage.find("#" + anchor).parent(),
        showLoading: true
      };
    }

    sm4.core.request.send({
      type: "GET",
      dataType: "html",
      url: url,
      data: $.extend(datas, {
        subject: $(thisObject).attr("data-subject"),
        page: $(thisObject).attr("data-pagination"),
        pages: $(thisObject).attr("data-pagination"),
        group: $(thisObject).attr("data-pagination"),
        groups: $(thisObject).attr("data-pagination"),
        business: $(thisObject).attr("data-pagination"),
        businesses: $(thisObject).attr("data-pagination"),
        'format': 'html'
      }),
      success: function(response) {
        if (hasViewmore) {
          wrpr.find('.paginationViewmore').remove();
          var responseEl = $('<div />').html(response);
          content.append(responseEl.find("#" + anchor + ' li'));
          if (responseEl.find('.paginationViewmore').length)
            wrpr.append(responseEl.find('.paginationViewmore'));
          if (content.jqmData('role') === 'listview') {
            sm4.core.refreshPage();
            content.listview().listview('refresh');
          }
        }
        sm4.core.runonce.trigger();
      }
    }, options);
  },
  setCalenderDateTime: function(page) {
    $("input[type='date'], input:jqmData(type='date')", page).each(function() {
      var $this = $(this);
      var inputValue = $this.val();
      //	$this.val(inputValue ? sm4.core.libraries.strToDate(inputValue,'/').toLocaleDateString() : new Date().toLocaleDateString());
      var $aLink = $("<a class='ui-btn ui-btn-icon-left ui-icon-calendar ui-btn-d ui-shadow ui-corner-all'/>").text(inputValue ? sm4.core.libraries.strToDate(inputValue, '/').toLocaleDateString() : sm4.core.language.translate('Select a date')).attr({
        'href': 'javascript://;',
        'data-icon': 'calendar'
      }).button();
      $this.after($aLink);
      var $popup = $('<div data-role="popup" data-tolerance="15"  data-overlay-theme="a" data-theme="none" aria-disabled="false" data-position-to="window" />');
      $this.after($popup);
      $aLink.on('click', function() {
        $popup.empty();
        $popup.popup();
        $popup.append($("<div />").datepicker({
          dateFormat: sm4.core.locale.format('date'),
          altField: "#" + $this.attr("id"),
          showOtherMonths: true,
          defaultDate: $this.val()
        }));
        $popup.popup('open');
      });
      $this.on('change', function() {
        setTimeout(function() {
          $aLink.html(sm4.core.libraries.strToDate($this.val(), '/').toLocaleDateString());
        }, 50);
      });
    });
  },
  setFormContent: function(page) {
    var forms = page.find('form');
    page.find(".header_submit_button").on('vclick', function() {
      if (!$(this).jqmData('rel'))
        return;
      page.find($(this).jqmData('rel')).submit();
    });
    $.each(forms, function(key, form) {
      form = $(form);
      if (form) {
        //       if(typeof form.attr('data-ajax') =='undefined' && $(page).data('role') == 'page'){
        //       //  form.attr('data-ajax', 'false');
        // 			}
        var action = form.attr('action');
        if (action) {
          action = action.replace('&formatType=smjson&contentType=page&', '&').replace('?formatType=smjson&contentType=page&', '?').replace('?formatType=smjson&contentType=page', '');
          if (sm4.core.isApp() && action.indexOf("/") === 0) {
            action = $.mobile.path.parseUrl(window.phonegap.smappcore.host.baseHref).domain + action;
          }
          action = form.attr('action', action);
        }

        var fileInputs = form.find('input:file');
        if (fileInputs.length > 0 && (typeof form.attr('data-ajax') == 'undefined' || form.attr('data-ajax') == 'false')) {
          form.attr('data-ajax', 'false');

          $(form).ajaxForm({
            reqData: null,
            data: {
              formatType: 'smjson'
            },
            beforeSend: function(formData, jqForm, options) {
              var settings = $.extend({}, $.mobile.loadPage.defaults, options);
              settings.pageContainer = settings.pageContainer || $.mobile.pageContainer;
              var $form = $(this);

              this.reqData = !!jqForm.reqData ? jqForm.reqData : {};
              this.reqData.deferred = $.Deferred();
              this.reqData.url = jqForm.url;
              this.reqData.absUrl = jqForm.url;
              this.reqData.dataUrl = $.mobile.path.convertUrlToDataUrl(jqForm.url);
              var reqOptions = {
                type: jqForm.type,
                data: jqForm.data,
                transition: $form.jqmData("transition"),
                reverse: $form.jqmData("direction") === "reverse",
                reloadPage: true,
                showLoadMsg: true,
                loadMsgDelay: 100
              };

              reqOptions = $.extend({}, settings, reqOptions);
              this.reqData.options = reqOptions;

              // This configurable timeout allows cached pages a brief delay to load without showing a message
              var loadMsgDelay = setTimeout(function() {
                $.mobile.loading().loader("show");
              }, reqOptions.loadMsgDelay);

              this.reqData.hideMsg = function() {
                // Stop message show timer
                clearTimeout(loadMsgDelay);
                // Hide loading message
                $.mobile.loading().loader("hide");
              };
            },
            uploadProgress: function(event, position, total, percentComplete) {

            },
            success: function(responseJSON, textStatus, xhr) {
              sm4.core.dloader.request.setOnSuccess(responseJSON, this.reqData, this.reqData.options, $.mobile.path.getFilePath(this.reqData.url), textStatus, xhr);
            },
            complete: function(xhr) {
            }
          });


          if (form.attr('action')) {
            form.attr('action', form.attr('action').replace('formatType=smjson', 'formatType=').replace('&format=json', '').replace('?format=json&', '?').replace('?format=json', ''));
          }

          for (var i = 0; i < fileInputs.length; i++) {
            var fileInput = $(fileInputs[i]);
            var fileInputNameOrg = fileInput.attr('name');
            var fileInputName = fileInput.attr('name').replace('[]', '');
            var fileInputLabel = form.find('label[for="' + fileInputName + '"]');

            var fileInputButton = $('<span id="'
                    + fileInputName + 'button" data-role="button" data-corners="true" data-shadow="true" data-iconshadow="true" class="file-input-button ui-btn ui-shadow ui-btn-corner-all ui-btn-up-c">'
                    + '<span class="ui-btn-inner ui-btn-corner-all">'
                    + '<span class="ui-btn-text">'
                    + fileInputLabel.text()
                    + '</span>'
                    + '</span>'
                    + '</span>');

            fileInputLabel.remove();
            fileInputButton.insertBefore(fileInput);
            fileInput.insertAfter(fileInputButton.find('.ui-btn-inner'));
            var parentDiv = fileInputButton.parent();
            if (parentDiv.hasClass('ui-input-text')) {
              parentDiv.attr('class', 'file-input-button-wrapper');
            }

            fileInputButton.unbind();
            fileInput.addClass('fileInput');
            var fileInputOrgClone = fileInput.clone();
            if (sm4.core.isApp() && (device.platform === "Android" && parseFloat(device.version) >= parseFloat(4.4))) {
              var filechoserUpload = function(self) {
                filechooser.open({}, function(data) {
                  var filepath = data.filepath;
                  // decode file from base64 (remove traling = first and whitespaces)
                  var content = atob(data.content.replace(/\s/g, "").replace(/=+$/, ""));

                  // convert string of bytes into actual byte array
                  var byteNumbers = new Array(content.length);
                  for (var i = 0; i < content.length; i++) {
                    byteNumbers[i] = content.charCodeAt(i);
                  }
                  var byteContent = new Uint8Array(byteNumbers);

                  var blob = new Blob([byteContent], {type: data.type, name: data.name});

                  if (!!!$.mobile.activePage.fileUploader)
                    $.mobile.activePage.fileUploader = [];

                  $.mobile.activePage.fileUploader.push({name: fileInputNameOrg, content: blob, filename: data.name, path: filepath});

                  if (fileInputNameOrg.indexOf('[]') > -1)
                    multiUpload(self, fileInputOrgClone, fileInputName);
                  else
                    $('.ui-page-active').find('form #' + self.attr('name') + 'button .ui-btn-text').html(filepath);
                  self.trigger('change');
                }, function() {

                });
              };
              fileInput.click(function(e) {
                filechoserUpload($(this));
              });

              var multiUpload = function(fileInput, fileInputOrgClone, fileInputName) {
                var listOfFiles = fileInput.closest('form').find('#' + fileInputName + 'ListOfFiles');
                if (listOfFiles.length == 0) {
                  listOfFiles = $('<ul data-role="listview" data-inset="true" data-corners="false" style="overflow:hidden;" >'
                          + '<li data-role="list-divider" class="headingUpload">'
                          + sm4.core.language.translate('Files To Upload')
                          + '<span class="ui-li-count">0</span>'
                          + '</li>'
                          + '<li data-icon="false" class="fileDummy" style="display:none; overflow:hidden;">'
                          + ' <a class="uploadedFile">'
                          + '</a>'
                          + '<a class="removeFile" data-icon="delete" data-rel="dialog" href="javascript://"></a></li>'

                          + '</ul>');
                  listOfFiles.attr('id', fileInputName + 'ListOfFiles');

                  listOfFiles.insertAfter($('.ui-page-active').find('form #' + fileInputName + 'button'));
                  listOfFiles.listview().listview('refresh');
                }

                var files = [];
                for (var i = 0; i < $.mobile.activePage.fileUploader.length; i++) {
                  if ($.mobile.activePage.fileUploader[i].name === fileInputNameOrg)
                    files.push($.mobile.activePage.fileUploader[i].path);
                }
                var header = listOfFiles.find('.headingUpload');
                listOfFiles.find('.file').remove();
                for (var i = 0; i < files.length; i++) {
                  var listFile = listOfFiles.find('li.fileDummy').clone().show();
                  listFile.find('a.uploadedFile .ui-btn-text').append(fileInput);
                  listFile.find('.uploadedFile').html(files[i]);

                  listFile.removeClass('fileDummy').addClass('file').insertAfter(header);
                  header.find('span.ui-li-count').html(listOfFiles.find('li.file').length);
                  listOfFiles.show();
                  if (files.length == 1 && 0) {
                    listFile.find('.removeFile').unbind().bind('click', function() {
                      var self = $(this);
                      if (listOfFiles.find('.file').length == 1) {
                        self.closest('li').closest('ul').hide();
                      } else {
                        header.find('span.ui-li-count').html(listOfFiles.find('.file').length - 1);
                      }
                      delete self.closest('li.file').remove();
                      delete fileInput.remove();
                    });
                  } else {
                    delete listFile.find('.removeFile').remove();
                  }
                }

                var fileInputClone = fileInputOrgClone.clone();
                fileInputClone.removeAttr('value');
                fileInputClone.attr('id', fileInputName + '-' + (listOfFiles.find('li.file').length));
                fileInputClone.insertAfter(fileInput);

                fileInputClone.click(function(e) {
                  filechoserUpload($(this));
                });
              }

            } else {
              fileInput.bind('change', function() {
                var self = $(this);
                if (self.attr('name').indexOf('[]') > -1)
                  multiUpload(self, fileInputOrgClone, fileInputName);
                else
                  $('.ui-page-active').find('form #' + self.attr('name') + 'button .ui-btn-text').html(self.val());
              });



              var multiUpload = function(fileInput, fileInputOrgClone, fileInputName) {
                var listOfFiles = fileInput.closest('form').find('#' + fileInputName + 'ListOfFiles');
                if (listOfFiles.length == 0) {
                  listOfFiles = $('<ul data-role="listview" data-inset="true" data-corners="false" style="overflow:hidden;" >'
                          + '<li data-role="list-divider" class="headingUpload">'
                          + sm4.core.language.translate('Files To Upload')
                          + '<span class="ui-li-count">0</span>'
                          + '</li>'
                          + '<li data-icon="false" class="fileDummy" style="display:none; overflow:hidden;">'
                          + ' <a class="uploadedFile">'
                          + '</a>'
                          + '<a class="removeFile" data-icon="delete" data-rel="dialog" href="javascript://"></a></li>'

                          + '</ul>');
                  listOfFiles.attr('id', fileInputName + 'ListOfFiles');

                  listOfFiles.insertAfter($('.ui-page-active').find('form #' + fileInputName + 'button'));
                  listOfFiles.listview().listview('refresh');
                }

                var files = $.map(fileInput.prop("files"), function(val) {
                  return val;
                });
                for (var i = 0; i < files.length; i++) {
                  var listFile = listOfFiles.find('li.fileDummy').clone().show();
                  listFile.find('a.uploadedFile .ui-btn-text').append(fileInput);
                  listFile.find('.uploadedFile').html(files[i].name);
                  var header = listOfFiles.find('.headingUpload');
                  listFile.removeClass('fileDummy').addClass('file').insertAfter(header);
                  header.find('span.ui-li-count').html(listOfFiles.find('li.file').length);
                  listOfFiles.show();
                  if (files.length == 1) {
                    listFile.find('.removeFile').unbind().bind('click', function() {
                      var self = $(this);
                      if (listOfFiles.find('.file').length == 1) {
                        self.closest('li').closest('ul').hide();
                      } else {
                        header.find('span.ui-li-count').html(listOfFiles.find('.file').length - 1);
                      }
                      delete self.closest('li.file').remove();
                      delete fileInput.remove();
                    });
                  } else {
                    delete listFile.find('.removeFile').remove();
                  }
                }

                var fileInputClone = fileInputOrgClone.clone();
                fileInputClone.removeAttr('value');
                fileInputClone.attr('id', fileInputName + '-' + (listOfFiles.find('li.file').length));
                fileInputClone.insertAfter(fileInput);
                fileInputClone.unbind().bind('change', function() {
                  var self = $(this);
                  multiUpload(self, fileInputOrgClone, fileInputName);
                });
              }
            }
          }
        }

        /* Form Notices */
        var formNotices = form.find('ul.form-notices');
        if (formNotices) {
          formNotices.attr({
            'data-role': 'listview',
            'data-inset': true,
            'data-theme': 'b',
            'data-mini': true
          });
          formNotices.listview().listview('refresh');
        }
        /* Form Errors */
        var formErrors = form.find('ul.form-errors');
        if (formErrors) {
          formErrors.attr({
            'data-role': 'listview',
            'data-inset': true,
            'data-mini': true,
            'data-theme': 'e'
          }).children('li').each(function() {
            var $this = $(this);
            $this.each(function() {
              var $li = $(this);
              $li.find('li').each(function() {
                $li.append($('<br/>'))
                        .append($('<p/>').html($(this).html()));
              });
              delete $li.find('ul').remove();
            });
          });
          formErrors.listview().listview('refresh');
        }
        /* Form Cancel */
        var cancel = form.find('#cancel');
        if (cancel) {
          cancel.addClass('ui-btn ui-btn-d ui-shadow ui-corner-all');
          cancel.attr({
            'data-rel': 'back',
            'onclick': ''
          });
        }
        form.find('.ui-link').each(function() {
          if ($(this).attr('type') === 'button') {
            $(this).addClass('ui-btn ui-btn-d ui-shadow ui-corner-all');
            if ($(this).attr('id') !== 'skiplink')
              $(this).attr({
                'data-rel': 'back',
                'onclick': ''
              });
          }
        });
        var delay = 10;
        if (form.closest(".ui-subpage").length) {
          delay = 2000;
        }
        //DISABLE INPUT TYPE TEXT LABLE AND PLACE THEM AS PLACEHOLDER
        if (sm4.core.isApp() && (form.closest(".ui-subpage").length == 0 || form.closest(".ui-subpage").jqmData('role') != 'subpage')) {
          setTimeout(function() {
                     
                     if(!sm4.core.isIos()){
                     form.find('[type="text"],textarea, [type="email"], [type="password"]').each(function() {
                        if ($(this).closest('.form-wrapper').find('.form-label').length) {
                            if (!$(this).attr('placeholder'))
                             $(this).attr({
                                          placeholder: $(this).closest('.form-wrapper').find('label').html()
                                          });
                             $(this).closest('.form-wrapper').find('.form-label').remove();
                            }
                   });
             }

            form.find('select').each(function() {
              if ($(this).closest('.form-wrapper').find('.form-label').length) {
                var $options = $('<option disabled />').html($(this).closest('.form-wrapper').find('label').html());
                $(this).prepend($options);
                //   $(this).closest('.form-wrapper').find('.form-label').remove();
              }

            });
          }, delay);

          //add icon on input type file field($.mobile
          if ($.type($('span.file-input-button').get(0)) != 'undefined') {
            $('span.file-input-button').each(function() {
              if ($(this).find('input:file').attr('accept') != 'audio/*')
                $(this).addClass('ui-icon-camera');

            });
          }
        }

      }
    });
  },
  autoCompleter: {
    searchInActivePage: true,
    attach: function(element, url, params, hiddenElement) {
      if ($.type(params.useActivePage) != 'undefined' && !params.useActivePage) {
        sm4.core.Module.autoCompleter.searchInActivePage = false;
      }
      elementTag = !!sm4.core.Module.autoCompleter.searchInActivePage ? $.mobile.activePage.find("#" + element) : $('#' + element);


      var search = params.search;
      ///element,url,type
      var autocomplete = elementTag.autocomplete({
        width: 300,
        max: params.limit,
        delay: 1,
        minLength: params.minLength,
        autoFocus: true,
        cacheLength: 1,
        scroll: true,
        highlight: true,
        messages: {
          // noResults: "No search results.",
          noResults: params.noResults ? params.noResults : "",
          results: function(amount) {
            /*  return amount + ( amount > 1 ? " results are" : " result is" ) +
             " available, use up and down arrow keys to navigate.";*/
            return "";
          }
        },
        source: function(request, response) {

          var data = {
            limit: params.limit
          };
          var termss = sm4.core.Module.autoCompleter.split(request.term);
          // remove the current input
          request.term = termss[termss.length - 1];
          if (search == 'search') {
            data.search = request.term;
          } else {
            data.text = request.term;
          }

          // New request 300ms after key stroke
          var $this = $(this);
          var $element = $(this.element);
          var previous_request = $element.data("jqXHR");
          if (previous_request) {
            // a previous request has been made.
            // though we don't know if it's concluded
            // we can try and kill it in case it hasn't
            previous_request.abort();
          }
          if (typeof params.showloading != 'undefined' && params.showloading == true)
            $('#addpeople_loading').css('display', 'block');
          // Store new AJAX request
          $element.data("jqXHR", $.ajax({
            type: "POST",
            url: url,
            data: data,
            dataType: "json",
            success: function(data, textStatus, jqXHR) {
              if (typeof params.showloading != 'undefined' && params.showloading == true)
                $('#addpeople_loading').css('display', 'none');

              response(data);
            },
            error: function(jqXHR, textStatus, errorThrown) {
              response([]);
            }
          }));
        },
        select: function(e, ui) {
          if (params.singletextbox) {
            if (params.poke) {
              elementTag.val("");
              return false;
            } else {
              // This is code for the invisible values
              var termss = sm4.core.Module.autoCompleter.split(this.value);

              // remove the current input
              termss.pop();

              termss.push(ui.item.value);

              // add placeholder to get the comma-and-space at the end
              termss.push("");

              this.value = termss.join(", ");
              return false;
            }
          } else {
            var name = ui.item.value;
            var toID = ui.item.id;

            // This is code for the invisible values
            var hiddenInputField = !!sm4.core.Module.autoCompleter.searchInActivePage ? $.mobile.activePage.find("#" + hiddenElement) : $("#" + hiddenElement);

            var previousToValues = hiddenInputField.val();

            if (!sm4.core.Module.autoCompleter.checkSpanExists(name, toID)) {
              var span = $("<span>").attr({
                id: "jquerytospan_" + name + "_" + toID,
                'class': 'tag'
              }).text(name);

              var remove = $("<a>").addClass("remove").attr({
                href: "javascript:",
                title: "Remove " + name,
                id: toID
              }).html("<span class='ui-icon ui-icon-delete ui-icon-shadow'></span>").appendTo(span);
              remove.on("click", function(e) {
                sm4.core.Module.autoCompleter.removeTagResults($(this), hiddenElement);
              });

              if (previousToValues == '') {
                hiddenInputField.attr({
                  value: toID
                });
              }
              else {
                hiddenInputField.attr({
                  value: previousToValues + "," + toID
                });

              }

              var hiddenInputElement = !!sm4.core.Module.autoCompleter.searchInActivePage ? $.mobile.activePage.find("#" + hiddenElement + "-element") : $("#" + hiddenElement + "-element");
              var hiddenInputWrapper = !!sm4.core.Module.autoCompleter.searchInActivePage ? $.mobile.activePage.find("#" + hiddenElement + "-wrapper") : $("#" + hiddenElement + "-wrapper")
              var hiddenInputLabel = !!sm4.core.Module.autoCompleter.searchInActivePage ? $.mobile.activePage.find("#" + hiddenElement + "-label") : $("#" + hiddenElement + "-label");

              span.insertAfter(hiddenInputElement);
              hiddenInputWrapper.css('display', 'block');
              if (hiddenInputLabel) {
                hiddenInputLabel.css('display', 'none');
              }
              elementTag.val('');
              return false;
            } else {
              elementTag.val('');
              return false;
            }
          }
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

      autocomplete._renderItem = function(ul, item) {
        var InputElement = !!sm4.core.Module.autoCompleter.searchInActivePage ? $.mobile.activePage.find("#" + element + "-element") : $("#" + element + "-element");
        ul.appendTo(InputElement);
        ul.attr({
          'data-role': 'listview',
          'data-inset': true,
          'class': 'ui-listview sm-ui-autosuggest'
        });


        if (params.poke) {
          var href = sm4.core.baseUrl + 'poke/pokeusers/pokeuser/pokeuser_id/' + item.id;
          var myHTML = "<a class='ui-btn ui-btn-icon-right' href=" + href + " data-rel='dialog'>";
        } else {
          var myHTML = "<a class='ui-btn ui-btn-icon-right'>";
        }

        if (params.showPhoto && item.photo) {
          myHTML = myHTML + item.photo;
        }

        myHTML = myHTML + item.label + "</a>";

        return  $('<li data-corners="false" data-shadow="false" data-iconshadow="true" data-wrapperels="div" data-iconpos="right" data-theme="c">')
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
    removeTagResults: function(removeObject, hiddenElement) {
      this.removeFromToValue(removeObject.attr('id'), hiddenElement);
      //remove current friend
      removeObject.parent().remove();
    },
    removeFromToValue: function(id, hiddenElement) {
      // code to change the values in the hidden field to have updated values
      // when recipients are removed.
      var toValues = $("#" + hiddenElement).val();
      var toValueArray = toValues.split(",");
      var toValueIndex = "";

      var checkMulti = id.search(/,/);

      // check if we are removing multiple recipients
      if (checkMulti != -1) {
        var recipientsArray = id.split(",");
        for (var i = 0; i < recipientsArray.length; i++) {
          this.removeToValue(recipientsArray[i], toValueArray, hiddenElement);
        }
      }
      else {
        this.removeToValue(id, toValueArray, hiddenElement);
      }

    },
    extractLast: function(term) {
      return this.split(term).pop();
    },
    removeToValue: function(id, toValueArray, hiddenElement) {
      var toValueIndex = 0;
      for (var i = 0; i < toValueArray.length; i++) {
        if (toValueArray[i] == id)
          toValueIndex = i;
      }

      toValueArray.splice(toValueIndex, 1);
      $("#" + hiddenElement).attr({
        value: toValueArray.join()
      });

      if ($("#" + hiddenElement + "-label")) {
        $("#" + hiddenElement + "-label").css('display', 'none');
      }
    },
    split: function(val) {
      return val.split(/,\s*/);
    }
  },
  showAdvancedSearch: function(toggleVariable, elementId) {
    if (toggleVariable == 0) {
      //ON CLICK SHOW ADVANCED SEARCH
      if(!fieldElements.length){
      $.mobile.activePage.find('#advanced_search_' + elementId).show();
      $.mobile.activePage.find('#simple_search_' + elementId).hide();
      }
      $.mobile.activePage.find('#hide_advanced_search_' + elementId).show();
      $.mobile.activePage.find('#show_advanced_search_' + elementId).hide();
      advancedSearchLists(0);
    } else {
      //ON CLICK HIDE ADVANCED SEARCH
      if(!fieldElements.length){
      $.mobile.activePage.find('#advanced_search_' + elementId).hide();
      $.mobile.activePage.find('#simple_search_' + elementId).show();
      }
      $.mobile.activePage.find('#hide_advanced_search_' + elementId).hide();
      $.mobile.activePage.find('#show_advanced_search_' + elementId).show();
      advancedSearchLists(1);
    }
  }
};

$.fn.inject = function(parent, position) {
  if (position == 'after')
    parent.after(this);
  else if (position == 'before')
    parent.before(this);
  else if (position == 'top')
    parent.prepend(this);
  else
    parent.append(this);
  return this;


};

sm4.album = {
  rotate: function(photo_id, angle, thisObject, setClass, url) {
    var request = $.ajax({
      url: url,
      type: "POST",
      dataType: "json",
      data: {
        format: 'json',
        photo_id: photo_id,
        angle: angle
      },
      complete: function(response) {
        thisObject.attr('class', setClass);
        // Check status
        if ($.type(response) == 'object' &&
                $.type(response.status) &&
                response.status == false) {
          sm4.core.showError('An error has occurred processing the request. The target may no longer exist.' + '<br /><br /><button data-rel="back">Close</button>');
          return;
        } else if ($.type(response) != 'object' ||
                !$.type(response.status)) {
          sm4.core.showError('An error has occurred processing the request. The target may no longer exist.' + '<br /><br /><button data-rel="back">Close</button>');
          return;
        }

        // Ok, let's refresh the page I guess
        // window.location.reload(true);
        sm4.core.reloadPage();
      }
    });
    return request;
  },
  flip: function(photo_id, direction, thisObject, setClass, url) {
    var request = $.ajax({
      url: url,
      type: "POST",
      dataType: "json",
      data: {
        format: 'json',
        photo_id: photo_id,
        direction: direction
      },
      complete: function(response) {
        thisObject.attr('class', setClass);
        // Check status
        if ($.type(response) == 'object' &&
                $.type(response.status) &&
                response.status == false) {
          sm4.core.showError('An error has occurred processing the request. The target may no longer exist.' + '<br /><br /><button data-rel="back">Close</button>');
          return;
        } else if ($.type(response) != 'object' ||
                !$.type(response.status)) {
          sm4.core.showError('An error has occurred processing the request. The target may no longer exist.' + '<br /><br /><button data-rel="back">Close</button>');
          return;
        }
        // Ok, let's refresh the page I guess
        // window.location.reload(true);
        sm4.core.reloadPage();
      }
    });
    return request;
  }



};

/*
 * ----------------------------------------------------------------------------
 * "THE BEER-WARE LICENSE" (Revision 42):
 * <jevin9@gmail.com> wrote this file. As long as you retain this notice you
 * can do whatever you want with this stuff. If we meet some day, and you think
 * this stuff is worth it, you can buy me a beer in return. Jevin O. Sewaruth
 * ----------------------------------------------------------------------------
 *
 * Autogrow Textarea Plugin Version v3.0
 * http://www.technoreply.com/autogrow-textarea-plugin-3-0
 *
 * THIS PLUGIN IS DELIVERD ON A PAY WHAT YOU WHANT BASIS. IF THE PLUGIN WAS USEFUL TO YOU, PLEASE CONSIDER BUYING THE PLUGIN HERE :
 * https://sites.fastspring.com/technoreply/instant/autogrowtextareaplugin
 *
 * Date: October 15, 2012
 */

jQuery.fn.autoGrow = function() {
  return this.each(function() {

    var createMirror = function(textarea) {
      jQuery(textarea).after('<div class="autogrow-textarea-mirror"></div>');
      return jQuery(textarea).next('.autogrow-textarea-mirror')[0];
    }

    var sendContentToMirror = function(textarea) {
      mirror.innerHTML = String(textarea.value).replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/'/g, '&#39;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/\n/g, '<br />') + '.<br/>.';

      if (jQuery(textarea).height() != jQuery(mirror).height())
        jQuery(textarea).height(jQuery(mirror).height());
    }

    var growTextarea = function() {
      sendContentToMirror(this);
    }

    // Create a mirror
    var mirror = createMirror(this);

    // Style the mirror
    mirror.style.display = 'none';
    mirror.style.wordWrap = 'break-word';
    mirror.style.padding = jQuery(this).css('padding');
    mirror.style.width = jQuery(this).css('width');
    mirror.style.fontFamily = jQuery(this).css('font-family');
    mirror.style.fontSize = jQuery(this).css('font-size');
    mirror.style.lineHeight = jQuery(this).css('line-height');

    // Style the textarea
    this.style.overflow = "hidden";
    this.style.minHeight = this.rows + "em";

    // Bind the textarea's event
    this.onkeyup = growTextarea;

    // Fire the event for text already present
    sendContentToMirror(this);

  });
};

sm4.core.category = {
  setDefault: function(params) {
    var subSelect = $.mobile.activePage.find('#subcategory_id-wrapper').find('select');
    if (subSelect) {
      var $span = $("<span id='subcategory_id_dnone'  class='dnone'>" + subSelect.html() + "</span>");
      $span.find('.subcategory_option').removeClass('dnone');
      $.mobile.activePage.find('#subcategory_id-wrapper').append($span);
      subSelect.empty();
      var subsubSelect = $.mobile.activePage.find('#subsubcategory_id-wrapper').find('select');
      if (subsubSelect) {
        var $span = $("<span id='subsubcategory_id_dnone'  class='dnone'>" + subsubSelect.html() + "</span>");
        $span.find('.subsubcategory_option').removeClass('dnone');
        $.mobile.activePage.find('#subsubcategory_id-wrapper').append($span);
        subsubSelect.empty();
      }
    }
    this.set($($.mobile.activePage.find('#category_id')).val(), 'subcategory');
    for (var i = 0; i < params.length; i++) {
      $.mobile.activePage.find('#' + params[i].type + '_id').val(params[i].value);
      $.mobile.activePage.find('#' + params[i].type + '_id').selectmenu('refresh');
      if (params[i].isChildSet)
        this.set(params[i].value, 'sub' + params[i].type);
    }
  },
  set: function(value, type) {
    var options = (type == 'category') ? $.mobile.activePage.find('[data-parent_listingtype="sr_lt_' + value + '"]') : $.mobile.activePage.find('[data-parent_category="sp_cat_' + value + '"]');

    var $select = $.mobile.activePage.find('#' + type + '_id-wrapper').find('select');
    var $span = $.mobile.activePage.find('#' + type + '_id-wrapper').find('#' + type + '_id_dnone');
    this.setVaule(type, 0);
    if (type == 'category' && $.mobile.activePage.find('#sub' + type + '-wrapper').find('select')) {
      this.setVaule('sub' + type, 0);
      if ($.mobile.activePage.find('#subsub' + type + '-wrapper').find('select')) {
        this.setVaule('subsub' + type, 0);
      }
    } else if (type == 'subcategory' && $.mobile.activePage.find('#sub' + type + '-wrapper').find('select')) {
      this.setVaule('sub' + type, 0);
    }
    if (options.length > 0) {
      this.show(type);
      if (type == 'category') {
        this.hide('subcategory');
      }
      if (type == 'category' || type == 'subcategory') {
        this.hide('subsubcategory');
      }
      var allOptions = $span.find('option');
      $select.empty();
      $select.append('<option value="0" />');
      $select.append($(options).clone());
      allOptions.each(function(k, el) {
        if ($(el).attr('value') == 0 || $(el).jqmData('parent_category') == "sp_cat_" + value)
          return;
      //  $select.append($(el));
      });
    } else {
      this.hide('subsubcategory');
      if (type == 'category' || type == 'subcategory') {
        this.hide('subcategory');
      }
      if (type == 'category') {
        this.hide('category');
      }
    }
     $.mobile.activePage.find('#' + type + '_id').selectmenu('refresh');
    if (type == 'category') {
      value = 0;
    }
    this.onChange(type.replace('subcategory', 'category'), value);
  },
  onChange: function(type, value) { 
    switch (type) {
      case 'category':
        this.setOthers(type, value);
        type = 'sub' + type;
        value = 0;
        $.mobile.activePage.find('#' + type + '_id').val(value);
      case 'subcategory':
        this.setOthers(type, value);
        type = 'sub' + type;
        value = 0;
        $.mobile.activePage.find('#' + type + '_id').val(value);
      case 'subsubcategory':
        this.setOthers(type, value);
    }
     $.mobile.activePage.find('#' + type + '_id').selectmenu('refresh');
  },
  setOthers: function(type, value) {
    $.mobile.activePage.find($.mobile.activePage.find('#' + type)).val(value);
    $.mobile.activePage.find($.mobile.activePage.find('#' + type + 'name')).val($.mobile.activePage.find($.mobile.activePage.find('#' + type + '_id').find('option:selected')).text());
     $.mobile.activePage.find('#' + type + '_id').selectmenu('refresh');
  },
  setVaule: function(type, value) {
    $.mobile.activePage.find('#' + type + '-wrapper').find('select').val(value);
    $.mobile.activePage.find('#' + type + '-wrapper').find('select').selectmenu('refresh');
    this.onChange(type, value);
  },
  show: function(type) {
    $.mobile.activePage.find('#' + type + '_id-wrapper').removeClass('dnone');
  },
  hide: function(type) {
    $.mobile.activePage.find('#' + type + '_id-wrapper').addClass('dnone');
  }
};

sm4.core.tinymce = {
  enabel: true,
  showTinymce: function(el) {
    if (!sm4.core.tinymce.enabel || sm4.core.isPhoneGap())
      return;
    tinyMCE.init({
      mode: el ? "exact" : "textareas",
      elements: el ? [el] : [],
      selector: "textarea",
      theme: "modern",
      menubar: false,
      plugins: [
        "table,fullscreen,media,preview,paste,image"
      ],
      toolbar1: "undo,redo,cleanup,removeformat,pasteword,|,code,media,image,link,fullscreen,preview",
      toolbar2: "fontselect,fontsizeselect,bold,italic,underline,strikethrough,forecolor,backcolor,|,justifyleft,justifycenter,justifyright,justifyfull,|,bullist,numlist,|,outdent,indent,blockquote",
      //       theme_toolbar_align: "left",
      //       theme_toolbar_location: "top",
      //element_format: "html",
      // height: "225px",
      //       convert_urls: false,
      //       media_strict: false,
      directionality: "ltr"
    });
  }
}

var serverOffset = 0;

Date.setServerOffset = function(ts) {
  var server = new Date(ts);
  var client = new Date();
  serverOffset = server - client;
};

Date.getServerOffset = function() {
  return serverOffset;
};

Date.prototype.getDOY = function() {
  var onejan = new Date(this.getFullYear(), 0, 1);
  return Math.ceil((this - onejan) / 86400000);
}
Date.prototype.clone = function() {
  return new Date(this.getTime());
}

$('document').ready(function()
{


  //  window.setInterval(function() {
  //        
  //    $('.timestamp-update').each(function(index, element){ 
  //      var newStamp = sm4.date.getDate(element.title);
  //      //var ref = new Date(element.title);
  //      //var newStamp = ref.getFluentTimeSince(now);
  //      if( $(element).html() != newStamp )
  //      { 
  //        $(element).html(newStamp);
  //      }
  //    });
  //  }, 1000);

});


sm4.date = {
  ref: '',
  getDate: function(title) {
    var now = new Date();
    this.ref = new Date(title);

    var newStamp = this.getFluentTimeSince(now);
    return newStamp;
  },
  getFluentTimeSince: function(now)
  {
    var val;
    if (!now)
      now = new Date();
    var deltaNormal = (this.ref - now - serverOffset) / 1000;
    //var deltaNormal = (now - ref + serverOffset) / 1000;
    var delta = Math.abs(deltaNormal);
    var isPlus = (deltaNormal > 0);
    if (delta < 1) {
      if (isPlus) {
        return sm4.core.language.translate('now');
      } else {
        return sm4.core.language.translate('now');
      }
    }

    // Less than a minute
    else if (delta < 60) {
      if (isPlus) {
        return sm4.core.language.translate('in few seconds');
      } else {
        return sm4.core.language.translate('a few seconds ago');
      }
    }

    // Less than an hour
    else if (delta < 60 * 60) {
      val = Math.floor(delta / 60);
      if (isPlus) {
        return sm4.core.language.translate('in %s minute', val);
      } else {
        return sm4.core.language.translate('%s minute ago', val);
      }
    }

    // less than 12 hours ago, or less than a day ago and same day
    else if (delta < (60 * 60 * 12) || (delta < 60 * 60 * 24 && this.ref.getDay() == now.getDay()))
    {
      val = Math.floor(delta / (60 * 60));
      if (isPlus) {
        return sm4.core.language.translate('in %s hour', val);
      } else {
        return sm4.core.language.translate('%s hour ago', val);
      }
    }

    // less than a week and same week
    //      else if( delta < 60 * 60 * 24 * 7 && this.getISOWeek('') == this.getISOWeek(now) )
    //      {
    //        return sm4.core.language.translate(
    //          '%s at %s',
    //          this.ref.toString('dddd')
    //          
    //        );
    //      }
    //
    //      // less than a year and same year
    //      else if( delta < 60 * 60 * 60 * 24 * 366 && now.getYear() == now.getYear() )
    //      {
    //        return this.ref.format('%B %d%o').replace(' 0', ' ');
    //      }
    //
    //      // Otherwise use the full date
    //      else
    //      {
    //        return this.ref.format('%B %d%o %Y');
    //      }
  },
  getISODay: function(compare)
  {
    var day = compare.getDay() - 1;
    if (day < 0)
      day += 7;
    return day;
  },
  getISOWeek: function(now)
  {
    var compare;
    var startOfWeekYear;
    if (now == '') {
      compare = this.ref.clone();
      compare.setMonth(1);
      compare.setDate(4);
      startOfWeekYear = compare.getDOY() - this.getISODay(compare) - 1;
      return Math.ceil((this.ref.getDOY() - startOfWeekYear) / 7);
    }
    else {
      compare = now.clone();
      compare.setMonth(1);
      compare.setDate(4);
      startOfWeekYear = compare.getDOY() - this.getISODay(compare) - 1;
      return Math.ceil((now.getDOY() - startOfWeekYear) / 7);
    }

  }

};

sm4.core.locationBased = {
  startReq: function(params) {
    window.locationsParamsSEAO = {
      latitude: 0,
      longitude: 0
    };
    window.locationsDetactSEAO = false;
    params.isExucute = false;
    var self = this;
    var callBackFunction = self.sendReq;
    if (params.callBack) {
      callBackFunction = params.callBack;
    }
    if (params.detactLocation && !window.locationsDetactSEAO && navigator.geolocation) {

      if (typeof($.cookie('seaocore_myLocationDetails')) != 'undefined' && $.cookie('seaocore_myLocationDetails') != "") {
        var readLocationsDetails = jQuery.parseJSON($.cookie('seaocore_myLocationDetails'));
      }

      if (typeof(readLocationsDetails) == 'undefined' || readLocationsDetails == null || typeof(readLocationsDetails.latitude) == 'undefined' || typeof(readLocationsDetails.longitude) == 'undefined') {

        navigator.geolocation.getCurrentPosition(function(position) {
          window.locationsParamsSEAO.latitude = position.coords.latitude;
          window.locationsParamsSEAO.longitude = position.coords.longitude;

          var myLocationDetails = {'latitude': position.coords.latitude, 'longitude': position.coords.longitude, 'location': '', 'locationmiles': params.locationmiles};
          self.setLocationCookies(myLocationDetails);

          self.setLocationField(position, params);
          params.requestParams = $.extend(params.requestParams, window.locationsParamsSEAO);
          params.isExucute = true;
          if (typeof(params.noSendReq) == 'undefined' || params.noSendReq == null) {
            callBackFunction(params);
          }

        }, function() {
          params.isExucute = true;
          if (typeof(params.noSendReq) == 'undefined' || params.noSendReq == null) {
            callBackFunction(params);
          }

        });
      }
      else {
        window.locationsParamsSEAO.latitude = readLocationsDetails.latitude;
        window.locationsParamsSEAO.longitude = readLocationsDetails.longitude;
        params.requestParams = $.extend(params.requestParams, window.locationsParamsSEAO);
        params.isExucute = true;
        if (typeof(params.noSendReq) == 'undefined' || params.noSendReq == null) {
          callBackFunction(params);
        }
      }

      window.locationsDetactSEAO = true;
      window.setTimeout(function() {
        if (params.isExucute)
          return;

        if (typeof(params.noSendReq) == 'undefined' || params.noSendReq == null) {
          callBackFunction(params);
        }

      }, 3000);
    } else {
      if (params.detactLocation && window.locationsDetactSEAO) {
        params.requestParams = $.extend(params.requestParams, window.locationsParamsSEAO);
      }

      if (typeof(params.noSendReq) == 'undefined' || params.noSendReq == null) {
        callBackFunction(params);
      }
    }

  },
  sendReq: function(params) {
    $.mobile.loading().loader("show");
    var self = this;
    var url = sm4.core.baseUrl + 'widget';

    if (params.requestUrl)
      url = params.requestUrl;
    $.ajax({
      url: url,
      data: $.extend(params.requestParams, {
        format: 'html',
        subject: sm4.core.subject.guid,
        is_ajax_load: true,
        isajax: true
      }),
      success: function(responseHTML) {
        $.mobile.loading().loader("hide");
        if ($.mobile.activePage.find('#' + params.responseContainer)) {
//          $.mobile.activePage.find('#'+params.responseContainer).html('');
          $.mobile.activePage.find('#' + params.responseContainer).html(responseHTML);
        }
        sm4.core.runonce.trigger();
        sm4.core.refreshPage();
      }
    });
  },
  setLocationCookies: function(params) {
    var myLocationDetails = {'latitude': params.latitude, 'longitude': params.longitude, 'location': params.location, 'locationmiles': params.locationmiles};
    $.cookie('seaocore_myLocationDetails', JSON.stringify(myLocationDetails), {expire: 30, path: sm4.core.baseUrl});
  },
  setLocationField: function(position, params) {
    var self = this;
    if (!position.address) {
      var mapDetect = new google.maps.Map($('<div>').get(0), {
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        center: new google.maps.LatLng(0, 0)
      });
      var service = new google.maps.places.PlacesService(mapDetect);
      var request = {
        location: new google.maps.LatLng(position.coords.latitude, position.coords.longitude),
        radius: 500
      };
      service.search(request, function(results, status) {
        if (status == 'OK') {
          var index = 0;
          var radian = 3.141592653589793 / 180;
          var my_distance = 1000;
          var R = 6371; // km
          for (var i = 0; i < results.length; i++) {
            var lat2 = results[i].geometry.location.lat();
            var lon2 = results[i].geometry.location.lng();
            var dLat = (lat2 - position.coords.latitude) * radian;
            var dLon = (lon2 - position.coords.longitude) * radian;
            var lat1 = position.coords.latitude * radian;
            lat2 = lat2 * radian;
            var a = Math.sin(dLat / 2) * Math.sin(dLat / 2) + Math.sin(dLon / 2) * Math.sin(dLon / 2) * Math.cos(lat1) * Math.cos(lat2);
            var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            var d = R * c;

            if (d < my_distance) {
              index = i;
              my_distance = d;
            }
          }

          if (typeof(params.fieldName) != 'undefined' && params.fieldName != null && document.getElementById(params.fieldName)) {
            document.getElementById(params.fieldName).value = (results[index].vicinity) ? results[index].vicinity : '';

            if (typeof(params.locationmilesFieldName) != 'undefined' && params.locationmilesFieldName != null && document.getElementById(params.locationmilesFieldName)) {
              document.getElementById(params.locationmilesFieldName).value = params.locationmiles;
            }
          }

          var cookiesLocation = (results[index].vicinity) ? results[index].vicinity : '';
          var myLocationDetails = {'latitude': position.coords.latitude, 'longitude': position.coords.longitude, 'location': cookiesLocation, 'locationmiles': params.locationmiles};
          self.setLocationCookies(myLocationDetails);

          if (typeof(params.reloadPage) != 'undefined' && params.reloadPage != null) {
            sm4.core.reloadPage();
          }

        }
      })
    } else {
      var delimiter = (position.address && position.address.street != '' && position.address.city != '') ? ', ' : '';
      var location = (position.address) ? (position.address.street + delimiter + position.address.city) : '';
      if (typeof(params.fieldName) != 'undefined' && params.fieldName != null && document.getElementById(params.fieldName)) {
        document.getElementById(params.fieldName).value = location;
      }

      var myLocationDetails = {'latitude': position.coords.latitude, 'longitude': position.coords.longitude, 'location': location, 'locationmiles': params.locationmiles};
      self.setLocationCookies(myLocationDetails);

      if (typeof(params.reloadPage) != 'undefined' && params.reloadPage != null) {
        sm4.core.reloadPage();
      }

    }

  },
}

//poll plugin
sm4.core.Module.poll = {
  sitemobileIndex: {
    view: function(params, pageContent, data) {
    }
  }
}
sm4.poll = {
  urls: {
    vote: 'polls/poll/vote',
    login: 'login'
  },
  data: {},
  addPollData: function(identity, data) {

    if ($.type(data) != 'object') {
      data = {};
    }

    $(this).data[identity] = data;
    return $(this);
  },
  getPollDatum: function(identity, key, defaultValue) {

    if (!defaultValue) {
      defaultValue = false;
    }
    if (!(identity in this.data)) {
      return defaultValue;
    }
    if (!(key in this.data[identity])) {
      return defaultValue;
    }
    return this.data[identity][key];
  },
  toggleResults: function(identity) {
    var pollContainer = $.mobile.activePage.find('#poll_form_' + identity);
    if ('none' == pollContainer.find('.poll_has_voted').css('display')) {
      pollContainer.find('.poll_has_voted').show();
      pollContainer.find('.poll_not_voted').hide();
      pollContainer.find('.poll_toggleResultsLink').text(sm4.core.language.translate('Show Questions'));
    } else {
      pollContainer.find('.poll_has_voted').hide();
      pollContainer.find('.poll_not_voted').show();
      pollContainer.find('.poll_toggleResultsLink').text(sm4.core.language.translate('Show Results'));
    }
  },
  renderResults: function(identity, answers, votes) {

    if (!answers || 'array' != $.type(answers)) {
      return;
    }
    var pollContainer = $.mobile.activePage.find('#poll_form_' + identity);

    $(answers).each(function(index, option) {
      var div = $.mobile.activePage.find('#poll-answer-' + option.poll_option_id);
      var pct = votes > 0
              ? Math.floor(100 * (option.votes / votes))
              : 1;
      if (pct < 1)
        pct = 1;
      // set width to 70% of actual width to fit text on same line
      div.width((.7 * pct) + '%');//div.width((.7*pct)+'%');//next//attr
      div.next('.poll_answer_total')
              .text(option.votesTranslated + ' (' + sm4.core.language.translate('%1$s%%', (option.votes ? pct : '0')) + ')');
      if (!this.getPollDatum(identity, 'canVote') ||
              (!this.getPollDatum(identity, 'canChangeVote') || this.getPollDatum(identity, 'hasVoted')) ||
              this.getPollDatum(identity, 'isClosed')) {
        pollContainer.find('.poll_radio').find('input').attr('disabled', true);
      }
    }.bind(this));
  },
  vote: function(identity, option) {
    option = $(option);

    var PollContainer = $.mobile.activePage.find('#poll_form_' + identity);
    var value = option.val();

    $.mobile.loading().loader("show");
    sm4.core.request.send({
      type: "POST",
      dataType: "html",
      url: this.urls.vote + '/' + identity,
      method: 'post',
      data: {
        'format': 'json',
        'poll_id': identity,
        'option_id': value
      },
      success: function(responseJSON) {
        $.mobile.loading().loader("hide");
        responseJSON = $.parseJSON(responseJSON);
        if ($.type(responseJSON) == 'object' && responseJSON.error) {
          $.mobile.loading().loader('show', $.mobile.pageLoadErrorMessageTheme, responseJSON.error, true);
          setTimeout((function() {
            $.mobile.loading().loader("hide");
          }), 500);
        } else {
          PollContainer.find('.poll_vote_total')
                  //sm4.core.language.translate(['%1$s vote', '%1$s votes', responseJSON.votes_total], responseJSON.votes_total)
                  .text(responseJSON.votes_total + ' votes');
          this.renderResults(identity, responseJSON.pollOptions, responseJSON.votes_total);
          this.toggleResults(identity);
        }
        if (!this.getPollDatum(identity, 'canChangeVote')) {
          PollContainer.find('.poll_radio input').attr('disabled', true);
        }
      }.bind(this)
    });

    //request.send()
  }
};

sm4.core.AppBrowser = {
  browser: null,
  params: null,
  open: function(url, params) {
    var MapReg = /^https?\:\/\/((www|maps)\.)?google\.[a-z]+\/maps\/?\?([^&]+&)*(s?ll=-?[0-9]{1,2}\.[0-9]+,-?[0-9]{1,2}\.[0-9]+|q=[^&+])+($|&)/,
            MapReg2 = /^https?\:\/\/(maps\.)?google\.[a-z]/;
    var mapmatch = url.match(MapReg);

    if (!mapmatch) {
      mapmatch = url.match(MapReg2);
    }
    if (mapmatch) {
                   if(sm4.core.isIos()){
                   var splittedUrl = url.split("?q=");
                   var newUrl = splittedUrl[1];
                   window.open("http://maps.apple.com?q="+newUrl,'_system','location=no');
                   return;
                   }else{
                   window.coreNative.googleMap({
                                               url: url
                                               });
                   return;
                   }
    }
    var toolbarcolor = this.hexc($.mobile.activePage.find('.ui-page-header-layout').css("background-color"));

    var type = 'blank', locationBar = 'location=yes';
    if (params.type)
      type = params.type;
    if (params.offLocation)
      locationBar = 'location=no';
    locationBar = locationBar + ',toolbarcolor=' + toolbarcolor + ',toolbarbuttoncolor=black,toolbarbuttontextcolor=white';
    this.browser = window.open(encodeURI(url), '_' + type, locationBar);
    this.params = params;
    this.attachEvent();
  },
  hexc: function(colorval) {
    var parts = colorval.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
    delete(parts[0]);
    for (var i = 1; i <= 3; ++i) {
      parts[i] = parseInt(parts[i]).toString(16);
      if (parts[i].length === 1)
        parts[i] = '0' + parts[i];
    }
    return '#' + parts.join('');

  },
  close: function() {
    this.browser.close();
  },
  loadStartEvent: function(event) {
  },
  loadStopEvent: function(event) {
  },
  loadErrorEvent: function(event) {
    this.close();
  },
  exitEvent: function(event) {
    this.removeEvent();
  },
  attachEvent: function() {
    var self = this;
    this.browser.addEventListener('loadstart', self.loadStartEvent);
    this.browser.addEventListener('loadstop', self.loadStopEvent);
    this.browser.addEventListener('loaderror', self.loadErrorEvent);
    this.browser.addEventListener('exit', self.exitEvent);
  },
  removeEvent: function() {
    var self = this;
    this.browser.removeEventListener('loadstart', self.loadStartEvent);
    this.browser.removeEventListener('loadstop', self.loadStopEvent);
    this.browser.removeEventListener('loaderror', self.loadErrorEvent);
    this.browser.removeEventListener('exit', self.exitEvent);
    this.browser = null;
  }
};


sm4.switchView = {
  pageInfo: {},
  //FUNCTIONS USED FOR SWITCH VIEWS (GRID VIEW, LIST VIEW ) AND VIEW MORE FUNCTIONALITY.
  getViewTypeEntity: function(view_selected, tabID, widgetUrl) {
    var currentpageid = $.mobile.activePage.attr('id') + '-' + tabID;
    if ($.type($.mobile.activePage.find('div.tab_' + tabID).get(0)) != 'undefined') {
      var tabcontainer = $.mobile.activePage.find('div.tab_' + tabID);
      if ($.type(tabcontainer.find($('#list_view')).get(0)) != 'undefined' && view_selected == 'listview')
        return;
      if ($.type(tabcontainer.find($('#grid_view')).get(0)) != 'undefined' && view_selected == 'gridview')
        return;

    }
    var params = $.extend({}, sm4.switchView.pageInfo[currentpageid]['params'], {
      'format': 'html',
      'isajax': '1',
      'viewType': view_selected,
      'is_ajax_load': '1',
      'page': '1',
      'viewmore': '0',
      'view_selected': view_selected
    });
    $.mobile.loading().loader("show");
    $.ajax({
      url: widgetUrl,
      type: 'GET',
      dataType: 'html',
      'data': params,
      success: function(responseHTML) {
//        $.mobile.hidePageLoadingMsg();
        $.mobile.loading().loader("hide");
//        $.mobile.activePage.find('#main_layout').html(responseHTML);
        //IF THE SWITCHING IS IN TABBED CONTAINER
        if ($.type($.mobile.activePage.find('div.tab_' + tabID).get(0)) != 'undefined')
          $.mobile.activePage.find('div.tab_' + tabID).html(responseHTML);
        else
          $(sm4.switchView.pageInfo[currentpageid]['responseContainer']).html(responseHTML);

        sm4.core.runonce.trigger();
        sm4.core.refreshPage();
      }
    });
  },
  viewMoreEntity: function(tabID, widgetUrl) {
    var currentpageid = $.mobile.activePage.attr('id') + '-' + tabID;
    var totalCount = sm4.switchView.pageInfo[currentpageid]['totalCount'];
    var viewType = sm4.switchView.pageInfo[currentpageid]['viewType'];
    $.mobile.activePage.find('.seaocore_loading').css('display', 'block');
    $.mobile.activePage.find('.feed_viewmore').css('display', 'none');
    var params = $.extend({}, sm4.switchView.pageInfo[currentpageid]['params'], {
      'format': 'html',
      'isajax': '1',
      'viewType': viewType,
      'is_ajax_load': '1',
      'page': parseInt(sm4.switchView.pageInfo[currentpageid]['params'].page) + parseInt(1),
      'viewmore': '1',
      'subject': sm4.core.subject.guid,
      'view_selected': viewType
    });

    $.ajax({
      type: "GET",
      dataType: "html",
      url: widgetUrl,
      data: params,
      success: function(responseHTML) {

        if ($.type($.mobile.activePage.find('div.tab_' + tabID)) != 'undefined') {

          $.mobile.activePage.find('#main_layout').find('ul').append(responseHTML);
          if (viewType == 'listview')
            $.mobile.activePage.find('#main_layout').find('ul').listview('refresh');
        }
        if (totalCount > (parseInt(params.page) * parseInt(params.limit))) {
          $.mobile.activePage.find('.seaocore_loading').css('display', 'none');
          $.mobile.activePage.find('.feed_viewmore').css('display', 'block');
        }
        else {
          $.mobile.activePage.find('.seaocore_loading').css('display', 'none');
          $.mobile.activePage.find('.feed_viewmore').css('display', 'none');
        }
        sm4.core.dloader.refreshPage();
        sm4.core.runonce.trigger();

      }
    });
  }
}
sm4.switchView.searchArray = {};
sm4.sitestoreproduct = {};
sm4.siteevent = {};
sm4.sitealbum = {};
//Siteevent function to get Ajax content on filters on profile members widget.
function getSiteeventAjaxContent(url, search, event_occurrence, rsvp, waiting, totalEventGuest) {
  $.mobile.activePage.find('.siteevent_profile_loading_image').css('display', 'block');
  $.mobile.activePage.find('.siteevent_profile_members_top').css('display', 'none');
  $.mobile.activePage.find('.siteevent_profile_list').css('display', 'none');
  $.ajax({
    'url': url,
    'data': $.merge({
      'format': 'html',
//      'subject': sm4.core.subject.guid,
      'subject': $.mobile.activePage.attr("data-subject"),
      'search': search,
      'rsvp': rsvp,
      'is_ajax_load': 1,
      'event_occurrence': event_occurrence,
      'occurrenceid': occurrenceid,
      'totalEventGuest': totalEventGuest,
      'occurrence_id': occurrence_id,
      'waiting': waiting,
    }, params),
    success: function(responseHTML) {
      $.mobile.activePage.find('.siteevent_profile_loading_image').css('display', 'none');
      $.mobile.activePage.find('.siteevent_profile_list').css('display', 'block');
      $.mobile.activePage.find('div.layout_siteevent_profile_members').html(responseHTML);
      sm4.core.dloader.refreshPage();
      sm4.core.runonce.trigger();
    }
  });
}
//Siteevent Filter Occurences VIA DATE FILTER - siteeventrepeat

var filterGuestByDate = function() {
  if (typeof totalMembersOccurrence != 'undefined')
    totalMembersOccurrence = 0;
  fiterbydate_active = 1;
  $.mobile.loading().loader("show");
  if ($('#pagination_container'))
    $('#pagination_container').css('display', 'none');
  $.ajax({
    'url': sm4.core.baseUrl + 'widget/index/mod/siteeventrepeat/name/occurrences',
    'data': $.extend(requestParams, {
      'format': 'html',
      'subject': sm4.core.subject.guid != '' ? sm4.core.subject.guid : $.mobile.activePage.attr("data-subject"),
      'page': 1,
      'is_ajax_load': 1,
      firstStartDate: $.mobile.activePage.find('#starttime-date').val(),
      lastStartDate: $.mobile.activePage.find('#endtime-date').val(),
      pagination: 1
    }),
    success: function(responseHTML) {
      fiterbydate_active = 0;
      $.mobile.activePage.find('#profile_occurences').html(responseHTML);
      $.mobile.loading().loader("hide");
      sm4.core.dloader.refreshPage();
      sm4.core.runonce.trigger();
    }
  });
}

//CALENDER WORK - siteevent
function getDayEvents(date_current, category_id) {
  // $.mobile.showPageLoadingMsg();
  $.mobile.loading("show");
  var data_month = {
    'date_current': date_current,
    category_id: category_id,
    'format': 'html',
    'is_ajax': true,
    viewtype: 'list'
  };

  if (typeof calendar_params != 'undefined')
    data_month = $.extend(calendar_params, data_month);

  $.ajax({
    'url': sm4.core.baseUrl + 'widget/index/mod/siteevent/name/calendarview-siteevent',
    'data': data_month,
    success: function(responseHTML) {
      $.mobile.loading().loader('hide');
      $('#event_list_content').css('display', 'block');
      $('#event_list_content').html(responseHTML);
      $('#dyanamic_code').css('display', 'none');
      sm4.core.runonce.trigger();
      sm4.core.refreshPage();
    }
  });
}
function filter_rsvp(url, container_id) {
  if ($('#pagination_container'))
    $('#pagination_container').css('display', 'none');
  $.mobile.loading().loader("show");
  $.ajax({
    url: url,
    data: {
      format: 'html',
      subject: sm4.core.subject.guid,
      isajax: true,
      pagination: 0,
      rsvp: -1,
      viewType: viewType,
      page: 1,
      evalScripts: true,
    },
    success: function(responseHTML) {
      $.mobile.loading().loader("hide");
      $('#' + container_id).html(responseHTML);
      sm4.core.dloader.refreshPage();
      sm4.core.runonce.trigger();
    }
  });
}

//MOVED FROM SEAOCORE MODULES JS - like_sitemobile.js
function seaocore_content_type_likes_sitemobile(resource_id, resource_type) {
  content_type_undefined = 0;
  var content_type = seaocore_content_type;
  if (seaocore_content_type == '') {
    content_type_undefined = 1;
    var content_type = resource_type;
  }
  if ($("#" + content_type + '_like_' + resource_id)) {
    var like_id = $("#" + content_type + '_like_' + resource_id).val();
  }
  if ($.mobile.activePage.find("#" + content_type + '_most_likes_' + resource_id).css('display') == 'block') {
    var action = 'like';
  } else {
    var action = 'unlike';
  }
  resetSeaocoreLikeUnlikeSitemobile(resource_id, content_type, action);
  $.ajax({
    url: sm4.core.baseUrl + 'seaocore/like/like',
    type: "POST",
    dataType: "json",
    data: {
      format: 'json',
      'resource_id': resource_id,
      'resource_type': resource_type,
      'like_id': like_id
    },
    success: function(response) {
      if (content_type_undefined == 0) {
        if (response.like_id) {
          if ($.mobile.activePage.find("#" + content_type + '_like_' + resource_id))
            $.mobile.activePage.find("#" + content_type + '_like_' + resource_id).val(response.like_id);
          if ($.mobile.activePage.find("#" + content_type + '_num_of_like_' + resource_id)) {
            $.mobile.activePage.find("#" + content_type + '_num_of_like_' + resource_id).html(response.num_of_like);
          }
        } else {
          if ($.mobile.activePage.find("#" + content_type + '_num_of_like_' + resource_id)) {
            $.mobile.activePage.find("#" + content_type + '_num_of_like_' + resource_id).html(response.num_of_like);
          }
        }
        if (!response.status) {
          resetSeaocoreLikeUnlikeSitemobile(resource_id, resource_type, action == 'like' ? 'unlike' : 'like');
        }
      }
      sm4.core.runonce.trigger();
      sm4.core.dloader.refreshPage();
    }
  });
}

function resetSeaocoreLikeUnlikeSitemobile(resource_id, content_type, action) {
  if (action == 'like') {
    if ($.mobile.activePage.find("#" + content_type + '_most_likes_' + resource_id))
      $.mobile.activePage.find("#" + content_type + '_most_likes_' + resource_id).css('display', 'none');
    if ($.mobile.activePage.find("#" + content_type + '_unlikes_' + resource_id))
      $.mobile.activePage.find("#" + content_type + '_unlikes_' + resource_id).css('display', 'block');
  } else {
    if ($.mobile.activePage.find("#" + content_type + '_like_' + resource_id))
      $.mobile.activePage.find("#" + content_type + '_like_' + resource_id).val(0);
    if ($.mobile.activePage.find("#" + content_type + '_most_likes_' + resource_id))
      $.mobile.activePage.find("#" + content_type + '_most_likes_' + resource_id).css('display', 'block');
    if ($.mobile.activePage.find("#" + content_type + '_unlikes_' + resource_id))
      $.mobile.activePage.find("#" + content_type + '_unlikes_' + resource_id).css('display', 'none');
  }
}
//MOVED FROM SEAOCORE MODULES JS - follow_sitemobile.js


function seaocore_resource_type_follows_sitemobile(resource_id, resource_type) {
  if ($.mobile.activePage.find("#" + resource_type + '_follow_' + resource_id)) {
    var follow_id = $.mobile.activePage.find("#" + resource_type + '_follow_' + resource_id).val()
  }
  if ($.mobile.activePage.find("#" + resource_type + '_most_follows_' + resource_id).css('display') == 'block') {
    var action = 'follow';
  } else {
    var action = 'unfollow';
  }
  resetSeaocoreFollowUnfollowSitemobile(resource_id, resource_type, action);

  $.ajax({
    url: sm4.core.baseUrl + 'seaocore/follow/global-follows',
    data: {
      format: 'json',
      'resource_id': resource_id,
      'resource_type': resource_type,
      'follow_id': follow_id
    },
    success: function(responseJSON) {
      if (responseJSON.follow_id) {
        $.mobile.activePage.find("#" + resource_type + '_follow_' + resource_id).val(responseJSON.follow_id);
        if ($.mobile.activePage.find("#" + resource_type + '_num_of_follow_' + resource_id)) {
          $.mobile.activePage.find("#" + resource_type + '_num_of_follow_' + resource_id).html(responseJSON.follow_count);
        }
      } else {
        $.mobile.activePage.find("#" + resource_type + '_follow_' + resource_id).val(0);
        if ($.mobile.activePage.find("#" + resource_type + '_num_of_follow_' + resource_id)) {
          $.mobile.activePage.find("#" + resource_type + '_num_of_follow_' + resource_id).html(responseJSON.follow_count);
        }
      }
      if (!responseJSON.status) {
        resetSeaocoreLikeUnlikeSitemobile(resource_id, resource_type, action == 'follow' ? 'unfollow' : 'follow');
      }
      sm4.core.runonce.trigger();
      sm4.core.dloader.refreshPage();
    }
  });
}

function resetSeaocoreFollowUnfollowSitemobile(resource_id, resource_type, action) {
  if (action == 'follow') {
    $.mobile.activePage.find("#" + resource_type + '_most_follows_' + resource_id).css('display', 'none');
    $.mobile.activePage.find("#" + resource_type + '_unfollows_' + resource_id).css('display', 'block');
  } else {
    $.mobile.activePage.find("#" + resource_type + '_most_follows_' + resource_id).css('display', 'block');
    $.mobile.activePage.find("#" + resource_type + '_unfollows_' + resource_id).css('display', 'none');
  }
}

 function siteeventCreateIsOnline(isonline){
    if (isonline == true) {
      $('#venue_name-wrapper').css('display', 'none');
      $('#online_events-wrapper').css('display', 'block');
      if ($('#location-wrapper'))
        $('#location-wrapper').css('display', 'none');
      if ($('#location_map-wrapper'))
        $('#location_map-wrapper').css('display', 'none');
      $('#is_online').val(1);
    } else {
      $('#venue_name-wrapper').css('display', 'block');
      $('#online_events-wrapper').css('display', 'none');
      if ($('#location-wrapper'))
        $('#location-wrapper').css('display', 'block');
      if ($('#location_map-wrapper') )
        $('#location_map-wrapper').css('display', 'block');
      $('#is_online').val(0);
   }
}