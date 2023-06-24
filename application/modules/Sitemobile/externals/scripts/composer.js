/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: composer.js 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
var self = false;
var uploadDevice = false;
var uploadUrl = "";
var comment_reply_id='';
$(document).on('afterSMCoreInit', function(event, data) {
  var checkinUrl =sm4.activity.composer.checkin.options.suggestOptions.url;
  if (sm4.core.baseUrl && checkinUrl.indexOf(sm4.core.baseUrl) === 0) {
    sm4.activity.composer.checkin.options.suggestOptions.url = sm4.activity.composer.checkin.options.suggestOptions.url.substring(sm4.core.baseUrl.length,checkinUrl.length);
  }
  sm4.activity.composer.checkin.options.suggestOptions.url = sm4.core.baseUrl + sm4.activity.composer.checkin.options.suggestOptions.url;
});
sm4.activity.composer = {
  active: false,
  content: $,
  composePlugin: false,
  $this: false,
  elements: {},
  options: {
    requestOptions: false,
    allowEmptyWithoutAttachment: false,
    allowEmptyWithAttachment: true,
    hideSubmitOnBlur: true,
    submitElement: false,
    useContentEditable: true
  },
  init: function(options) {
    if (typeof options != 'undefined')
      this.options.requestOptions = options;
  },
   showPluginForm: function(e, plugin, insidePage) {
    if (insidePage) {
      sm4.activity.composer.content = $($.mobile.activePage);
    } else {
      sm4.activity.composer.content = $(document);
    }
    
    if ($.type($('#activitypost-container-temp', sm4.activity.composer.content).find('#activity_body').get(0)) != 'undefined')
      this.elements.textarea = $('#activitypost-container-temp', sm4.activity.composer.content).find('#activity_body');
  
  
   // this.elements.textarea = $('#activity_body');
    if (plugin != 'checkin' && plugin != 'addpeople')
      $('#activitypost-container-temp', sm4.activity.composer.content).find('#composer-options').hide();

    uploadDevice = $(e).data('allowed');
    uploadUrl = $(e).data('uploadurl') ;
    if (plugin) {
      this.activate(plugin);
    }
  },
  showCommentPluginForm: function(e, plugin, body_id) {
    
    sm4.activity.composer.content = $(document);
    this.elements.textarea = $(body_id);
    if (plugin != 'checkin' && plugin != 'addpeople')
      $('#activitypost-container-temp', sm4.activity.composer.content).find('#composer-options').hide();

    if (plugin) {
      this.activate(plugin, 'comment');
    }
   
  },
  showReplyPluginForm: function(e, plugin, body_id, id) {
    
    sm4.activity.composer.content = $(document);
    this.elements.textarea = $(body_id);
    if (plugin != 'checkin' && plugin != 'addpeople')
      $('#activitypost-container-temp', sm4.activity.composer.content).find('#composer-options').hide();

    if (plugin) {
        if($.type($('#reply_options_box_'+ id, sm4.activity.composer.content).get(0)) != 'undefined')
        {
           $('#reply_options_box_'+id, sm4.activity.composer.content).parent().addClass('reply'); 
           comment_reply_id = id;
        }
        this.activate(plugin, 'reply');
    }
  },  
  showEditPluginForm: function(e, plugin, body_id, id) { 
    
    sm4.activity.composer.content = $(document);
    this.elements.textarea = $(body_id);
    if (plugin != 'checkin' && plugin != 'addpeople')
      $('#activitypost-container-temp', sm4.activity.composer.content).find('#composer-options').hide();

    if (plugin) {
        if($.type($('#edit_options_box', sm4.activity.composer.content).get(0)) != 'undefined' && ($('#edit_options_box', sm4.activity.composer.content).parent().attr('action-id') == 'edit-form' || $('#edit_options_box', sm4.activity.composer.content).parent().attr('action-id') == 'activity-edit-form-' + id))
        {
           $('#edit_options_box', sm4.activity.composer.content).parent().addClass('edit'); 
        }
        this.activate(plugin, 'edit');
      
    }
  }, 
  activate: function(plugin, mode) {
    $.mobile.activePage.find('#activitypost-container-temp').removeClass('dnone');
    self = this;
    if (plugin != 'checkin' && plugin != 'addpeople') {
      $this = this[plugin];
      if (this.active) {
        return;
      }

      this.active = true;
    }
    this.composePlugin = this[plugin];

    if (!this.composePlugin) {
      return;
    }

    //  if (!this.composePlugin.is_init) {
    this.composePlugin.init();
    this.composePlugin.is_init = true;
    //  }

    this.composePlugin.activate(mode);
     
  },
  getName: function() {
    return this.composePlugin.name;
  },
  getMenu: function() {
    if ($.type(this.composePlugin.elements.menu) == 'undefined') {
      if ($.type($('#compose-menu', sm4.activity.composer.content).get(0)) == 'undefined') {
        this.composePlugin.elements.menu = $('<div />', {
          'id': 'compose-menu',
          'class': 'compose-menu'
        }).inject(this.elements.textarea.parent('form'), 'after');
      }
      else {
        this.composePlugin.elements.menu = $('#compose-menu', sm4.activity.composer.content)
      }

    }
    return this.composePlugin.elements.menu;
  },
  getActivatorContent: function() {
    if ($.type(this.elements.activatorContent) == 'undefined') {

      if ($.type($('#compose-activator-content', sm4.activity.composer.content)[0]) == 'undefined') {
        this.composePlugin.elements.activatorContent = $('<div />', {
          'id': 'compose-activator-content',
          'class': 'adv_post_compose_menu'
        }).inject(this.elements.textarea.parent('form'), 'after');
      }
      else {
        this.composePlugin.elements.activatorContent = $('#compose-activator-content', sm4.activity.composer.content);
      }

    }
    return this.elements.activatorContent;
  },
  getTray: function() {
    if (!this.composePlugin.elements.tray) {
      this.composePlugin.elements.tray = $('<div />', {
        'id': 'compose-tray',
        'class': 'compose-tray ui-shadow-inset',
        'css': {
          'display': 'block'
        }
      });
    }
    return this.composePlugin.elements.tray;

  },
  makeMenu: function() {
    if (!this.composePlugin.elements.menu) {
      if ($.type($('.compose_buttons', sm4.activity.composer.content).get(0)) == 'undefined') {
        $('#activitypost-container-temp', sm4.activity.composer.content).find('#composer-options').before(this.getTray());
      }
      else {
        if($.type($('.compose_buttons', sm4.activity.composer.content).get(0)) != 'undefined') {
//            if($.type($('#reply_options_box_'+ comment_reply_id, sm4.activity.composer.content).get(0)) != 'undefined' && $('#reply_options_box_'+ comment_reply_id, sm4.activity.composer.content).parent().attr('action-id') == 'reply-form_'+comment_reply_id && $('#reply_options_box_'+comment_reply_id, sm4.activity.composer.content).parent().attr('class') == 'reply') {
//                $('#reply_options_box_'+comment_reply_id, sm4.activity.composer.content).append(this.getTray());
//            } 
            
            if($.type($('#reply_options_box_'+ comment_reply_id, sm4.activity.composer.content).get(0)) != 'undefined' ) {
                $('#reply_options_box_'+comment_reply_id, sm4.activity.composer.content).append(this.getTray());
            }
            else if($.type($('#comment_options_box', sm4.activity.composer.content).get(0)) != 'undefined' && $('#comment_options_box', sm4.activity.composer.content).parent().attr('action-id') == 'comment-form') {
                $('#comment_options_box', sm4.activity.composer.content).append(this.getTray());
            } else {
                $('#activitypost-container-temp', sm4.activity.composer.content).find('.compose_buttons').before(this.getTray());
            }
        } 
      }
      
      this.composePlugin.elements.menu = $('<div />', {
        'id': 'compose-' + this.getName() + '-menu',
        'class': 'compose-menu'
      });
      
      this.getTray().append(this.composePlugin.elements.menu);
      
      this.composePlugin.elements.menuTitle = $('<span />', {
        'html': sm4.core.language.translate('Add ' + (this.getName()).capitalize()) + ' ('
      }).inject(this.composePlugin.elements.menu);

      this.composePlugin.elements.menuClose = $('<a />', {
        'href': 'javascript:void(0);',
        'class': 'ui-link',
        'style': 'display:inline-block;',
        'html': sm4.core.language.translate('cancel'),
        'click': function(e) {
          e.preventDefault();
          this.deactivate();
        }.bind(this)

      }).inject(this.composePlugin.elements.menuTitle);

      this.composePlugin.elements.menuTitle.append(')');
    }
  },
  makeBody: function() {
    if (!$this.elements.body) {
      $this.elements.body = $('<div />', {
        'id': 'compose-' + this.getName() + '-body',
        'class': 'compose-body'
      }).inject(this.getTray());
    }
  },
  deactivate: function() {
    // clean video out if not attached      
    sm4.activity.options.allowEmptyWithoutAttachment = false;
    this.active = false;
    this.getTray().remove();
    $('#composer-options', sm4.activity.composer.content).show();
    this.reset();
  },
  reset: function() {
    if (typeof $this == 'undefined')
      return;
    $.each($this.elements, function(key, element) {
      if ($.type(element) == 'object' && key != 'loading' && key != 'activator' && key != 'menu') {
        $(element).remove();

      }
    }.bind(this));
    $this.params = {};
    $this.elements = {};
    photoUpload = false;
  },
  makeLoading: function(action) {
    if (!$this.elements.loading) {
      if (action == 'empty') {
        $this.elements.body.empty();
      } else if (action == 'hide') {
        $this.elements.body.children().each(function(element) {
          element.css('display', 'none')
        });
      } else if (action == 'invisible') {
        $this.elements.body.children().each(function(key, element) {
          element.css('height', '0px').css('visibility', 'hidden')
        });
      }

      $this.elements.loading = $('<div />', {
        'id': 'compose-' + this.getName() + '-loading',
        'class': 'compose-loading'
      });
      $this.elements.body.append($this.elements.loading);
      
      var loadingSRc = 'application/modules/Sitemobile/modules/Core/externals/images/loading.gif';
      if (sm4.core.isApp()) {
        loadingSRc = sm4.core.staticBaseUrl + loadingSRc;
      }
      var image = $this.elements.loadingImage = $this.elements.loadingImage || ($('<img />', {
        'id': 'compose-' + this.getName() + '-loading-image',
        'class': 'compose-loading-image',
        'src': loadingSRc
      }));

      $this.elements.loading.append(image);

      $('<span />', {
        'html': sm4.core.language.translate('Loading...')
      });
      $this.elements.loading.append($('<span />', {
        'html': sm4.core.language.translate('Loading...')
      }));
    }
  },
  makeError: function(message, action) {
    if ($.type(action) == 'undefined')
      action = 'empty';
    message = message || sm4.core.language.translate('An error has occurred');
    //message = this._lang(message);

    $this.elements.error = $('<div />', {
      'id': 'compose-' + this.getName() + '-error',
      'class': 'compose-error',
      'html': sm4.core.language.translate(message)
    });

    if (!$this.elements.body)
      $this.elements.error.inject(this.getTray());
    else
      $this.elements.error.inject($this.elements.body);

  },
  makeFormInputs: function(data) {
    //this.ready();

    this.getInputArea($this).text('');
    if ($.type(data.type) == 'undefined')
      data.type = this.getName();
    $.each(data, function(key, value) {
      this.setFormInputValue(key, value);
    }.bind(this));

  },
  setFormInputValue: function(key, value) {
    var elName = 'attachmentForm' + key.capitalize();
    var newelem = true;
    $this.elements.inputarea.children().each(function(index, element) {
      if (element.name == 'attachment[' + key + ']') {
        newelem = false;
        element.value = value;
      }

    });
    if (newelem) {
      $this.elements.elName = $('<input />', {
        'type': 'hidden',
        'name': 'attachment[' + key + ']',
        'value': value || ''
      });

      $this.elements.inputarea.append($this.elements.elName);
    }

  },
  getInputArea: function(plugin) {
    if ($.type(plugin.elements.inputarea) == 'undefined') {
        if($('#activitypost-container-temp')) {
            var containerTemp= $('#activitypost-container-temp').get(0);
            var action_id = $(containerTemp).attr('action-id');
            if($.type($('#reply_options_box_'+ comment_reply_id, sm4.activity.composer.content).get(0)) != 'undefined' && $('#activitypost-container-temp').find('.reply')) { 
                containerTemp = $('#reply_options_box_'+ comment_reply_id, sm4.activity.composer.content).parent('#activitypost-container-temp').get(0);
                 action_id = $(containerTemp).attr('action-id');
                 var formEle =  $('#' + action_id);
            } else if($.type($('#comment_options_box', sm4.activity.composer.content).get(0)) != 'undefined') {
                containerTemp = $('#comment_options_box', sm4.activity.composer.content).parent('#activitypost-container-temp').get(0);
                 action_id = $(containerTemp).attr('action-id');
                 var formEle =  $('#' + action_id);
            }
            else {
                var formEle =  sm4.activity.getForm();
            }
        } else {
            var formEle =  sm4.activity.getForm();
        }
      plugin.elements.inputarea = $('<div>', {
        'css': {
          'display': 'none'
        }
      });
      formEle.append(plugin.elements.inputarea);
    }

    return plugin.elements.inputarea;
  },
  _lang: function() {
    try {
      if (arguments.length < 1) {
        return '';
      }

      var string = arguments[0];
      if ($.type($this.options.lang) && $.type($this.options.lang[string]) != 'undefined') {
        string = $this.options.lang[string];
      }

      if (arguments.length <= 1) {
        return string;
      }

      var args = new Array();
      for (var i = 1, l = arguments.length; i < l; i++) {
        args.push(arguments[i]);
      }

      return string.vsprintf(args);
    } catch (e) {
      alert(e);

    }
  },
  checkin: {
    name: 'checkin',
    active: false,
    aboartReq: false,
    self: '',
    persistentElements: ['activator', 'loadingImage'],
    options: {
      title: 'Share Location',
      lang: {},
      suggestOptions: {
        'url': sm4.core.baseUrl + 'sitetagcheckin/checkin/suggest',
        'data': {
          'format': 'json'
        }
      }
    },
    location: '',
    call_empty_suggest: false,
    add_location: false,
    navigator_location_shared: false,
    add_location:false,
            init: function(options) {
      if(!this.active) { 
      this.elements = {};      
      }
      this.params = {};
      this.self = this;

    },
    activate: function() {
      var addLinkBefore = $('#sitetagchecking_mob', sm4.activity.composer.content);
      this.call_empty_suggest = false;
      addLinkBefore.prevAll().css('display', 'none');
      $('#ui-header', sm4.activity.composer.content).css('display', 'none');
      $('#ui-header-checkin', sm4.activity.composer.content).css('display', 'block');
      if (this.active && this.elements.stchekinsuggestContainer) {
        this.elements.stchekinsuggestContainer.toggle();

        return;
      }
      this.active = true;

      var width = self.elements.textarea.outerWidth();
      this.elements.stchekinsuggestContainer = $('<div />', {
        'class': 'sm-post-search-container  ui-page-content',
        'id': 'stchekin_suggest_container',
        'css': {
          'display': 'block'
        }
      }).inject(addLinkBefore, 'after');

      this.elements.stchekinsuggestContainerSearchDiv = $('<div />', {
      }).inject(this.elements.stchekinsuggestContainer);

      var element_1 = $('<div />', {
        'class': 'sm-post-search-fields'

      }).inject(this.elements.stchekinsuggestContainerSearchDiv);

      var element_2 = $('<table />', {
      }).inject(element_1);

      var element_3 = $('<tr />', {
      }).inject(element_2);

      var element_4 = $('<td />', {
        'class': 'sm-post-search-fields-left'

      }).inject(element_3);


      this.elements.stchekinsearchText = $('<input />', {
        'type': 'search',
        'id': 'aff_mobile_aft_search_stch',
        'class': 'ui-input-field ui-autocomplete-input',
        'placeholder': sm4.core.language.translate('Search..')

      }).inject(element_4);

      var element_5 = $('<td />', {
        'class': 'sm-post-search-fields-right'
      }).inject(element_3);


      this.elements.stchekinsearchText.attr('autocomplete', 'off');
      if (!sm4.core.isApp()) {
        this.elements.checkinbutton = $('<button />', {
          'class': 'checkin-label ui-input-button',
          'data-role': 'none',
          'html': sm4.core.language.translate('Search')
        }).inject(element_5);
    }

      this.elements.crosslocation = $('<span />', {
        'id': 'cross_location',
        'css': 'display:none'
      });


      this.elements.crosslocation.inject(this.elements.stchekinsuggestContainerSearchDiv);

      this.elements.stchekinsuggestContainerSearchListDiv = $('<div />', {
      }).inject(this.elements.stchekinsuggestContainer);

      var loadingSRc = 'application/modules/Sitemobile/modules/Core/externals/images/loading.gif';
      if (sm4.core.isApp()) {
        loadingSRc = sm4.core.staticBaseUrl + loadingSRc;
      }
      this.elements.stchekinloading = $('<div />', {
        'class': 'sm-post-search-loading',
        'html': '<img src="' + loadingSRc + '" alt="' + sm4.core.language.translate('Loading...') + '" />',
        'id': 'place-loading',
        'css': {
          'display': 'none'
        }

      }).inject(this.elements.stchekinsuggestContainer);

      this.elements.stchekinerrorlocatoin = $('<div />', {
        'class': 'tip',
        'html': sm4.core.language.translate('There was an error detecting your current location.<br />Please make sure location services are enabled in your browser,and this site has permission to use them. You can still search for a place, but the search will not be as accurate.'),
        'id': 'place-errorlocation',
        'css': {
          'display': 'none'
        }

      }).inject(this.elements.stchekinloading, 'after');
      sm4.core.dloader.refreshPage();

      // Submit
      sm4.activity.getForm().on('editorSubmit', function() {
        this.submit();

      }.bind(this));

      //}

      this.suggest = this.getSuggest();
      this.self = this;
      this.getCurrentLocation();

    },
    detach: function() {

      return this;
    },
    toggleEvent: function() {

      if (this.elements.stchekinsearchText)
        this.elements.stchekinsearchText.value = '';

      if (this.elements.stchekinsuggestContainer.css("display") == 'block') {
        this.elements.stchekinsuggestContainer.css("display", "none");
      }
      else {
        this.elements.stchekinsuggestContainer.css("display", "block");
      }
    },
    loading: function() {
      this.elements.stchekinsuggestContainerSearchListDiv.text('');
      this.elements.stchekinloading.inject(this.elements.stchekinsuggestContainerSearchListDiv);
    },
    search: function() {
      if (this.elements.stchekinsearchText.val() == '')
        return;
      this.getLocation({
        'suggest': this.elements.stchekinsearchText.val()
      });
    },
    getCurrentLocation: function() {

      if (!sm4.core.isApp() && $.type(this.watchID) != 'undefined')
        return;
      this.elements.stchekinloading.css('display', 'block');
      var locationTimeLimit = 10000;

      //var self = this;
      var locationTimeout = window.setTimeout(function() {
        try {
          this.navigator_location_shared = false;
          if (this.watchID) {
            navigator.geolocation.clearWatch(this.watchID);
          } else {
            this.elements.stchekinloading.css('display', 'none');

            if (typeof proceed_request_temp == 'undefined' || (typeof proceed_request_temp != 'undefined' && !proceed_request_temp))
              this.elements.stchekinerrorlocatoin.css('display', 'block');


          }
        } catch (e) {
        }

        var data = {
          'accuracy': 0,
          'latitude': 0,
          'longitude': 0,
          'label': '',
          'vicinity': ''
        };

        this.location = data;
      }.bind(this), locationTimeLimit);
      var self = this;
      if (navigator.geolocation) {
        try {

          this.watchID = navigator.geolocation.watchPosition(function(position) {
//            if (this.watchID)
//              navigator.geolocation.clearWatch(this.watchID);
            this.navigator_location_shared = true;
            window.clearTimeout(locationTimeout);
            this.navigator_location_shared = true;
            var delimiter = (position.address && position.address.street != '' && position.address.city != '') ? ', ' : '';
            var data = {
              'accuracy': position.coords.accuracy,
              'latitude': position.coords.latitude,
              'longitude': position.coords.longitude,
              'label': (position.address) ? (position.address.street + delimiter + position.address.city) : '',
              'vicinity': (position.address) ? (position.address.street + delimiter + position.address.city) : ''
            };
            if (!position.address) {
              data.vicinity = this.getAddress(position.coords);
              self.location = data;
              self.suggest._setOptions({
                'extraParams': this.getLocation()
              });
            } else {
              if (!self.add_location)
                self.location = data;
              self.suggest._setOptions({
                'extraParams': this.getLocation()
              });
            }
          }.bind(this), function() {
            self.getEmptySuggest();
          }.bind(this), {
            maximumAge: 60000,
            timeout: 5000,
            enableHighAccuracy: !sm4.core.isApp()
          });
          //}
        } catch (e) {
          this.getEmptySuggest();
        }
      }
      else {
        this.elements.stchekinloading.css('display', 'none');
        this.elements.stchekinerrorlocatoin.css('display', 'block');
      }
    },
    getEmptySuggest: function() {
      if (this.call_empty_suggest)
        return;
      if (typeof this.elements.stchekinsearchText != 'undefined')
        this.elements.stchekinsearchText.focus();
      if (this.suggest && this.suggest.element.val() == '') {
        this.suggest.queryValue = ' ';
        this.suggest.source({
          term: ''
        }, this.suggest._response());
      }

      this.call_empty_suggest = true;
    },
    getAddress: function(location) {
      if (sm4.core.isApp()) {
        var geoAPI = 'https://maps.googleapis.com/maps/api/geocode/json?sensor=false&latlng=' + location.latitude + ',' + location.longitude;

        var realThis = this;
        $.getJSON(geoAPI, function(r) {

          if (r.results.length > 0) {
            var results = r.results;
            var index = 0;
            var radian = 3.141592653589793 / 180;
            var my_distance = 1000;
            var R = 6371; // km
            for (var i = 0; i < results.length; i++) {
              var lat2 = results[i].geometry.location.lat;
              var lon2 = results[i].geometry.location.lng;
              var dLat = (lat2 - location.latitude) * radian;
              var dLon = (lon2 - location.longitude) * radian;
              var lat1 = location.latitude * radian;
              lat2 = lat2 * radian;
              var a = Math.sin(dLat / 2) * Math.sin(dLat / 2) + Math.sin(dLon / 2) * Math.sin(dLon / 2) * Math.cos(lat1) * Math.cos(lat2);
              var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
              var d = R * c;

              if (d < my_distance) {
                index = i;
                my_distance = d;
              }
            }

            var address = results[index].formatted_address;
            realThis.location.latitude = location.latitude;
            realThis.location.longitude = location.longitude;
            realThis.location.vicinity = address;
            realThis.suggest._setOptions({
              'extraParams': realThis.getLocation()
            });
            realThis.getEmptySuggest();
            setTimeout(function() {
              realThis.location = {};
            }, 3000);
            return  address;
          }
        });
      } else {
        var realThis = this;
        //var self=this;
        var map = new google.maps.Map($('<div />').get(0), {
          mapTypeId: google.maps.MapTypeId.ROADMAP,
          center: new google.maps.LatLng(location.latitude, location.longitude),
          zoom: 15
        });
        var service = new google.maps.places.PlacesService(map);
        var request = {
          location: new google.maps.LatLng(location.latitude, location.longitude),
          radius: 500
        };

        service.search(request, function(results, status) {
          if (status == 'OK') {
            realThis.location.vicinity = results[0].vicinity;
            realThis.suggest._setOptions({
              'extraParams': realThis.getLocation()
            });
            //realThis.elements.stchekinsearchText.focus();
            var index = 0;
            var radian = 3.141592653589793 / 180;
            var my_distance = 1000;
            var R = 6371; // km
            for (var i = 0; i < results.length; i++) {
              var lat2 = results[i].geometry.location.lat();
              var lon2 = results[i].geometry.location.lng();
              var dLat = (lat2 - location.latitude) * radian;
              var dLon = (lon2 - location.longitude) * radian;
              var lat1 = location.latitude * radian;
              lat2 = lat2 * radian;
              var a = Math.sin(dLat / 2) * Math.sin(dLat / 2) + Math.sin(dLon / 2) * Math.sin(dLon / 2) * Math.cos(lat1) * Math.cos(lat2);
              var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
              var d = R * c;

              if (d < my_distance) {
                index = i;
                my_distance = d;
              }
            }
            realThis.getEmptySuggest();
            return results[index].vicinity;
          }
        });
      }
    },
    getSuggest: function() {

      // if( !this.suggest ) { 
      var width = this.elements.stchekinsearchText.outerWidth();
      this.suggestContener = $('<div />', {
        'class': 'sitecheckin-autosuggest-contener',
        'css': {
          'width': width + 'px',
          'display': 'none'
        }
      });

      this.choicesSliderArea = $('<div />', {
        'class': 'sitecheckin-autosuggest'
      });


      this.choices = $('<ul />', {
        'class': 'tag-autosuggest seaocore-autosuggest sitetagcheckin-autosuggestlist-feed',
        'css': {
          'width': width + 'px'
        }
      }).inject(this.choicesSliderArea);

      this.choicesSliderArea.inject(this.suggestContener);
      $('<div />', {
        'class': 'clr'
      }).inject(this.suggestContener);
      this.suggestMap = $('<div />', {
        'class': 'sitecheckin-autosuggest-map',
        'css': {
          'position': 'relative'
        }
      }).inject(this.suggestContener);
      this.suggestContener.inject(this.elements.stchekinsearchText, 'after');
      //this.scroller=new SEAOMooVerticalScroll(this.choicesSliderArea, this.choices,{});
      var self = this;
      var options = {
        'cache': false,
        'selectMode': 'pick',
        'postVar': 'suggest',
        'callback': this,
        'minLength': 0,
        'className': 'searchbox_autosuggest',
        'filterSubset': true,
        'tokenValueKey': 'label',
        'tokenFormat': 'object',
        'customChoices': this.choices,
        'extraParams': this.getLocation(),
        'indicatorClass': 'checkin-loading',
        'maxChoices': 25,
        'url': this.options.suggestOptions.url,
        'data': {
          'format': 'json'
        }
      };

      sm4.activity.autoCompleter.attach('aff_mobile_aft_search_stch', this.options.suggestOptions.url, options);
      this.suggest = sm4.activity.autoCompleter.autocomplete_checkin;
      //}
      return this.suggest;
    },
    getLocation: function() {
      var location = {
        'latitude': 0,
        'longitude': 0,
        'location_detected': ''
      };

      if (this.isValidLocation(false, true)) {
        location.latitude = this.location.latitude;
        location.longitude = this.location.longitude;
        location.location_detected = this.location.vicinity;
      }

      return location;
    },
    isValidLocation: function(location, checkin_params) {
      var location = (location) ? location : this.location;
      return  (checkin_params)
              ? (location && location.latitude && this.location.longitude)
              : (location && location.label != undefined && location.label != '');
    },
    getLocation_old: function(params) {
      //var =this;      
      this.loading();
      $.ajax({
        dataType: "json",
        url: this.options.suggestOptions.url,
        data: $.merge(params, {
          'format': 'json'
        }),
        success: function(responseJSON, textStatus, xhr) {
          this.queryResponse(responseJSON);
        }.bind(this)

      });

    },
    queryResponse: function(response) {

      this.elements.stchekinsuggestContainerSearchListDiv.text('');
      this.choices = $('<ul />', {
        'class': 'aaf-mobile-aad-tag-autosuggest'

      }).inject(this.elements.stchekinsuggestContainerSearchListDiv);

      $.each(response, this.injectChoice.bind(this));
      //      $.each(response, function(this.injectChoice ,this) { 
      //      
      //    });
    },
    injectChoice: function(key, token) {

      //  if(token.type != "just_use"){
      var choice = $('<li />', {
        'class': 'autocompleter-choices',
        'value': this.markQueryValue(token.label),
        'id': token.id
      });
      if (token.type != "just_use") {
        var divEl = $('<div />', {
          'html': this.markQueryValue(token.label),
          'class': 'autocompleter-choice'
        });
      } else {
        var divEl = $('<div />', {
          'html': this.markQueryValue(token.li_html),
          'class': 'autocompleter-choice chekin_autosuggest_just_use'
        });
      }
      if (token.type != 'place' && token.type != "just_use") {
        $('<div />', {
          'html': this.markQueryValue(token.category) + ' &#8226; ' + this.markQueryValue(token.vicinity)
        }).inject(divEl);
      }
      divEl.inject(choice);
      this.addChoiceEvents(choice).inject(this.choices);
      choice.data('autocompleteChoice', token);
      //  }      
    },
    /**
     * markQueryValue
     *
     * Marks the queried word in the given string with <span class="autocompleter-queried">*</span>
     * Call this i.e. from your custom parseChoices, same for addChoiceEvents
     *
     * @param		{String} Text
     * @return		{String} Text
     */
    markQueryValue: function(str) {
      return (!this.options.markQuery || !this.queryValue) ? str
              : str.replace(new RegExp('(' + ((this.options.filterSubset) ? '' : '^') + this.queryValue.escapeRegExp() + ')', (this.options.filterCase) ? '' : 'i'), '<span class="autocompleter-queried">$1</span>');
    },
    /**
     * addChoiceEvents
     *
     * Appends the needed event handlers for a choice-entry to the given element.
     *
     * @param		{Element} Choice entry
     * @return		{Element} Choice entry
     */
    addChoiceEvents: function(el) {
      return el.click(function() {
        this.choiceSelect(el);

      }.bind(this));

    },
    choiceSelect: function(choice) {
      var data = choice.data('autocompleteChoice');
      this.setLocation(data);
      this.elements.stchekinsuggestContainerSearchListDiv.html('');
      this.toggleEvent();
    },
    setLocation: function(location) {

      //if (sm4.activity.composer.checkin.aboartReq)return;
      var realThis = this;
      this.add_location = true;
      this.location = location;

      this.elements.stchekinlocationdiv = $('<div />', {
        'class': 'aaf-add-friend-tagcontainer'
      });
      $('<span />', {
        'class': 'aff-tag-with',
        'html': sm4.core.language.translate('at') + ': '
      }).inject(this.elements.stchekinlocationdiv);
      this.elements.stchekinlocationspan = $('<span />', {
        'class': 'tag',
        'html': (location.type == 'place' && location.vicinity) ? ((location.name && location.name != location.vicinity) ? location.name + ', ' + location.vicinity : location.vicinity) : location.label
      });

      this.elements.stchekinremovelink = $('<a />', {
        'html': '<span class="ui-icon ui-icon-delete ui-icon-shadow"></span>',
        'href': 'javascript:void(0);',
        'click': this.removeLocation.bind(this)
      }).inject(this.elements.stchekinlocationspan);

      this.elements.stchekinlocationspan.inject(this.elements.stchekinlocationdiv);
      $('#composer-checkin-tag').css('display', 'block');
      this.elements.stchekinlocationdiv.inject($('#toValuesdone-wrapper'), 'after');
      $('#activitypost-container-temp').find('.cm-icon-map-marker').addClass('active');
      if (location.type == 'place') {
        $('#activitypost-container-temp').find('#compose-submit').css('display', 'none');
        window.setTimeout(function() {
          var map = new google.maps.Map($('<div />').get(0), {
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            center: new google.maps.LatLng(0, 0),
            zoom: 15
          });
          var service = new google.maps.places.PlacesService(map);

          service.getDetails({
           'placeId': location.place_id
          }, function(place, status) {
            if (status == 'OK') {

              location.google_id = place.id;
              location.name = place.name;
              location.vicinity = (place.vicinity) ? place.vicinity : place.formatted_address;
              location.latitude = place.geometry.location.lat();
              location.longitude = place.geometry.location.lng();
              location.icon = place.icon;
              location.types = place.types.join(',');
              location.prefixadd = location.types.indexOf('establishment') > -1 ? sm4.core.language.translate('at') : sm4.core.language.translate('in');

              realThis.location = location;
              if ($.type($('#checkinstr_status').get(0)) != 'undefined') {
                $('#checkinstr_status', sm4.activity.composer.content).val(jQuery.param(location));
              }

            }
            $('#activitypost-container-temp', sm4.activity.composer.content).find('#compose-submit').css('display', 'block');
          });
        }, 1000);
      }

    },
    setMarker: function(checkin, choice) {


      var myLatlng = new google.maps.LatLng(checkin.latitude, checkin.longitude);
      var new_map = false;
      if (this.map == undefined || !this.suggestMap.get(0)) {
        new_map = true;
        this.map = new google.maps.Map(this.suggestMap.get(0), {
          navigationControl: false,
          mapTypeControl: false,
          scaleControl: false,
          draggable: false,
          streetViewControl: false,
          zoomControl: false,
          mapTypeId: google.maps.MapTypeId.ROADMAP,
          center: myLatlng,
          zoom: 15
        });
      }

      if (new_map) {
        this.marker = new google.maps.Marker({
          position: myLatlng,
          map: this.map
        });
        this.map.setCenter(myLatlng);
      } else {
        this.marker = (this.marker == undefined) ? new google.maps.Marker({
          position: myLatlng,
          map: this.map
        }) : this.marker;
        this.marker.setPosition(myLatlng);
        this.map.panTo(myLatlng);
      }
      this.elements.stchekinsearchText.val('');
    },
    removeLocation: function() {
      this.elements.stchekinlocationdiv.remove();
      this.add_location = false;
      this.location = "";
      this.call_empty_suggest = false;
      sm4.activity.options.allowEmptyWithoutAttachment = false;
      $('.cm-icon-map-marker', sm4.activity.composer.content).removeClass('active');
      //      if(this.elements.stchekinlink.hasClass("aaf_st_disable")){
      //        this.elements.stchekinlink.removeClass("aaf_st_disable").addClass("aaf_st_enable"); 
      //      }
    },
    submit: function() {
      var checkinStr = '';
      if (this.add_location) {
        var checkinHash = this.location;
        checkinStr = $.param(checkinHash);
        if (this.options.allowEmpty)
          self.options.allowEmptyWithoutAttachment = true;
      }

      //IF THE Checkinstr does not have latitued and longitude then we will not submit the status form.
      if(this.location.latitude == undefined) {
        sm4.activity.options.submitElement = false;
        alert(sm4.core.language.translate('There were some problem in getting the location info. Please try again.'));
      }
      this.makeFormInputs({
        checkin: checkinStr
      });
    },
    makeFormInputs: function(data) {
      self.getInputArea(this);
      $.each(data, function(key, value) {
        this.setFormInputValue(key, value);
      }.bind(this));
    },
    // make tag hidden input and set value into composer form
    setFormInputValue: function(key, value) {

      var elName = 'aafComposerForm' + key.capitalize();
      var newelem = true;
      this.elements.inputarea.children().each(function(index, element) {
        if (element.name == 'composer[' + key + ']') {
          newelem = false;
          element.value = value;
        }

      });
      if (newelem) {
        this.elements.elName = $('<input />', {
          'type': 'hidden',
          'name': 'composer[' + key + ']',
          'value': value || ''
        });

        this.elements.inputarea.append(this.elements.elName);
      }


    },
    reset: function() {

      $.each(this.elements, function(key, element) {
        if ($.type(element) == 'object' && key != 'loading' && key != 'activator' && key != 'menu') {
          $(element).remove();

        }
      }.bind(this));
      this.params = {};
      this.elements = {};

    }


  },
  addpeople: {
    name: 'addfriendtag',
    active: false,
    self: '',
    persistentElements: ['activator', 'loadingImage'],
    options: {
      title: sm4.core.language.translate('Add People'),
      lang: {}
    },
    add_friend_suggest: false,
    add_friend: false,
    tag_ids: '',
    init: function(options) {
      this.elements = {};
      this.params = {};

      //this.parent(options);
    },
    activate: function(mode) {

      if($.type(mode) == 'undefined') {
            var addLinkBefore = $('#adv_post_container_tagging', sm4.activity.composer.content);
      addLinkBefore.prevAll().css('display', 'none');
      addLinkBefore.css('display', 'block');
      addLinkBefore.next().css('display', 'block');
      $('#ui-header', sm4.activity.composer.content).css('display', 'none');
      $('#ui-header-addpeople', sm4.activity.composer.content).css('display', 'block');
      //SET FOCUS
      $('#aff_mobile_aft_search').focus(); 

      if (this.active)
        return;
      this.active = true;
      this.self = this;
      var url = sm4.core.baseUrl + 'advancedactivity/friends/suggest';
      sm4.core.Module.autoCompleter.attach("aff_mobile_aft_search", url, {
        'singletextbox': false,
        'limit': 10,
        'minLength': 1,
        'showPhoto': true,
        'search': 'search',
        showloading: true,
        'useActivePage': false,
      }, 'toValues-temp');
      } else {
				
	if(mode=='comment'){
	    var addLinkBefore = $('#adv_comment_post_container_tagging', sm4.activity.composer.content);
            
            $('#toCommentValues-temp-wrapper').css('display', 'none');
            
             addLinkBefore.css('display', 'block');
             addLinkBefore.next().css('display', 'block');
            // $('#ui-header', sm4.activity.composer.content).css('display', 'none');
             $('#ui-post-comment-header-addpeople', sm4.activity.composer.content).css('display', 'block');
             //SET FOCUS
             $('#aff_comment_mobile_aft_search').focus(); 

             if (this.active)
               return;
             this.active = true;
             this.self = this;
             var url = sm4.core.baseUrl + 'advancedactivity/friends/suggest';
             sm4.core.Module.autoCompleter.attach("aff_comment_mobile_aft_search", url, {
               'singletextbox': false,
               'limit': 10,
               'minLength': 1,
               'showPhoto': true,
               'search': 'search',
               showloading: true,
               'useActivePage': false,
             }, 'toCommentValues-temp');
      } else if(mode=='edit') {
          
        var addLinkBefore = $('#adv_edit_post_container_tagging', sm4.activity.composer.content);
        // addLinkBefore.prevAll().css('display', 'none');
         addLinkBefore.css('display', 'block');
         $('#toEditValues-temp-wrapper').css('display', 'none');
         addLinkBefore.next().css('display', 'block');
        // $('#ui-header', sm4.activity.composer.content).css('display', 'none');
         $('#ui-post-edit-header-addpeople', sm4.activity.composer.content).css('display', 'block');
         //SET FOCUS
         $('#aff_edit_mobile_aft_search').focus(); 

         if (this.active)
           return;
         this.active = true;
         this.self = this;
         var url = sm4.core.baseUrl + 'advancedactivity/friends/suggest';
         sm4.core.Module.autoCompleter.attach("aff_edit_mobile_aft_search", url, {
           'singletextbox': false,
           'limit': 10,
           'minLength': 1,
           'showPhoto': true,
           'search': 'search',
            showloading: true,
           'useActivePage': false,
         }, 'toEditValues-temp');
      }
      else {
        var addLinkBefore = $('#adv_reply_post_container_tagging_'+comment_reply_id, sm4.activity.composer.content);
        // addLinkBefore.prevAll().css('display', 'none');
         addLinkBefore.css('display', 'block');
         $('#toReplyValues-temp_'+comment_reply_id+'-wrapper').css('display', 'none');
         addLinkBefore.next().css('display', 'block');
        // $('#ui-header', sm4.activity.composer.content).css('display', 'none');
         $('#ui-post-reply-header-addpeople_'+comment_reply_id, sm4.activity.composer.content).css('display', 'block');
         //SET FOCUS
         $('#aff_reply_mobile_aft_search_'+comment_reply_id).focus(); 

         if (this.active)
           return;
         this.active = true;
         this.self = this;
         var url = sm4.core.baseUrl + 'advancedactivity/friends/suggest';
         sm4.core.Module.autoCompleter.attach("aff_reply_mobile_aft_search_"+comment_reply_id, url, {
           'singletextbox': false,
           'limit': 10,
           'minLength': 1,
           'showPhoto': true,
           'search': 'search',
           showloading: true,
           'useActivePage': false,
         }, 'toReplyValues-temp_'+comment_reply_id);
      }
        }
      return this;
    },
    detach: function() {
      //   this.parent();
      return this;
    },
    toggleEvent: function() {

      if ($('#adv_post_container_tagging', sm4.activity.composer.content).css('display') == 'block')
        $('#adv_post_container_tagging', sm4.activity.composer.content).css('display', 'none');
      else
        $('#adv_post_container_tagging', sm4.activity.composer.content).css('display', 'block');

    },
    loading: function() {
      this.elements.suggestContainerSearchListDiv.val('');
      this.elements.loading.inject(this.elements.suggestContainerSearchListDiv);
    },
    getFriends: function(params) {
      var selfparent = this;
      this.loading();


      $.ajax({
        type: "POST",
        dataType: "html",
        url: sm4.core.baseUrl + 'advancedactivity/friends/suggest-mobile',
        data: $.merge(params, {
          'format': 'html',
          'subject': sm4.core.subject.guid

        }),
        success: function(responseHTML, textStatus, xhr) {

          selfparent.elements.suggestContainerSearchListDiv.innerHTML = responseHTML;
          $(".aaf_mobile_add_tag", sm4.activity.composer.content).each(function(key, el) {
            el.click(selfparent.addTag.bind(selfparent));
          });
          $(".aff_list_pagination", sm4.activity.composer.content).each(function(key, el) {
            el.click(selfparent.searchLink.bind(selfparent));
          });
          $(".aff_list_pagination_select", sm4.activity.composer.content).each(function(key, el) {
            el.change(selfparent.searchSelect.bind(selfparent));
          });

        }
      });
    },
    addFriends: function() {

      $('#toValues', sm4.activity.composer.content).val($('#toValues-temp', sm4.activity.composer.content).val());
      $('#toValuesdone-wrapper', sm4.activity.composer.content).html($('#toValues-temp-wrapper', sm4.activity.composer.content).html()).find('div').remove();
      if ($('#toValues-temp', sm4.activity.composer.content).val() != '') {
        var tagspan = $('<span />', {
          'class': 'aff-tag-with',
          'html': sm4.core.language.translate('with:')
        });
        $('#toValuesdone-wrapper', sm4.activity.composer.content).prepend(tagspan);
        $('#toValuesdone-wrapper', sm4.activity.composer.content).css('display', 'block');
      }
      else {
        $('#toValuesdone-wrapper', sm4.activity.composer.content).css('display', 'none');
      }

      $('#toValuesdone-wrapper', sm4.activity.composer.content).find('.remove').off('click').on("click", function(e) {
        var id = this.id
        sm4.core.Module.autoCompleter.removeTagResults($(this), 'toValues');
        sm4.core.Module.autoCompleter.removeTagResults($('#' + id, sm4.activity.composer.content), 'toValues-temp');
        if ($('#toValues', sm4.activity.composer.content).val() != '')
          sm4.activity.options.allowEmptyWithoutAttachment = true;
        else {
          sm4.activity.options.allowEmptyWithoutAttachment = false;
          $('#toValuesdone-wrapper', sm4.activity.composer.content).html('');
          $('#toValuesdone-wrapper', sm4.activity.composer.content).css('display', 'none');
          $('.cm-icon-user', sm4.activity.composer.content).removeClass('active');
        }
      });

      sm4.activity.toggleFeedArea('', false, 'addpeople');
    },
    addCommentFriends: function(form_id) {

      $('#toCommentValues', sm4.activity.composer.content).val($('#toCommentValues-temp', sm4.activity.composer.content).val());
      $('#toCommentValuesdone-wrapper', sm4.activity.composer.content).html($('#toCommentValues-temp-wrapper', sm4.activity.composer.content).html()).find('div').remove();
      if ($('#toCommentValues-temp', sm4.activity.composer.content).val() != '') {
        
        $('#toCommentValuesdone-wrapper', sm4.activity.composer.content).css('display', 'block');
      }
      else {
        $('#toCommentValuesdone-wrapper', sm4.activity.composer.content).css('display', 'none');
      }

      $('#toCommentValuesdone-wrapper', sm4.activity.composer.content).find('.remove').off('click').on("click", function(e) {
        var id = this.id;
        sm4.core.Module.autoCompleter.removeTagResults($(this), 'toCommentValues');
        sm4.core.Module.autoCompleter.removeTagResults($('#' + id, sm4.activity.composer.content), 'toCommentValues-temp');
        if ($('#toCommentValues', sm4.activity.composer.content).val() != '')
          sm4.activity.options.allowEmptyWithoutAttachment = true;
        else {
          sm4.activity.options.allowEmptyWithoutAttachment = false;
          $('#toCommentValuesdone-wrapper', sm4.activity.composer.content).html('');
          $('#toCommentValuesdone-wrapper', sm4.activity.composer.content).css('display', 'none');
          $('.cm-icon-user', sm4.activity.composer.content).removeClass('active');
        }
      });     
      var addLinkBefore = $('#adv_comment_post_container_tagging', sm4.activity.composer.content);
      $('#composer-nested-comment-options').css('display', 'block');
      $('#comment-emoticons-button').css('display', 'block');
      $('#comment-add-people').css('display', 'block');
      addLinkBefore.css('display', 'none');
      //addLinkBefore.next().css('display', 'none');
      //$('#ui-header', sm4.activity.composer.content).css('display', 'block');
      $('#ui-post-comment-header-addpeople', sm4.activity.composer.content).css('display', 'none');
      
      //sm4.activity.toggleFeedArea('', false, 'addpeople');
    },
    addReplyFriends: function(form_id, id) {
      $('#toReplyValues_'+id, sm4.activity.composer.content).val($('#toReplyValues-temp_'+id, sm4.activity.composer.content).val());
          
      $('#toReplyValuesdone-wrapper_'+id, sm4.activity.composer.content).html($('#toReplyValues-temp_'+id+'-wrapper', sm4.activity.composer.content).html()).find('div').remove();
      if ($('#toReplyValues-temp_'+id, sm4.activity.composer.content).val() != '') {
        
        $('#toReplyValuesdone-wrapper_'+id, sm4.activity.composer.content).css('display', 'block');
      }
      else {
        $('#toReplyValuesdone-wrapper_'+id, sm4.activity.composer.content).css('display', 'none');
      }

      $('#toReplyValuesdone-wrapper_'+id, sm4.activity.composer.content).find('.remove').off('click').on("click", function(e) {
        var id = this.id;
        sm4.core.Module.autoCompleter.removeTagResults($(this), 'toReplyValues_'+id);
        sm4.core.Module.autoCompleter.removeTagResults($('#' + id, sm4.activity.composer.content), 'toReplyValues-temp_'+id);
        if ($('#toReplyValues_'+id, sm4.activity.composer.content).val() != '')
          sm4.activity.options.allowEmptyWithoutAttachment = true;
        else {
          sm4.activity.options.allowEmptyWithoutAttachment = false;
          $('#toReplyValuesdone-wrapper_'+id, sm4.activity.composer.content).html('');
          $('#toReplyValuesdone-wrapper_'+id, sm4.activity.composer.content).css('display', 'none');
          $('.cm-icon-user', sm4.activity.composer.content).removeClass('active');
        }
      });
     
      var addLinkBefore = $('#adv_reply_post_container_tagging_'+id, sm4.activity.composer.content);
      $('#composer-nested-reply-options_'+id).css('display', 'block');
      $('#reply-emoticons-button_'+id).css('display', 'block');
      $('#reply-add-people_'+id).css('display', 'block');
      addLinkBefore.css('display', 'none');
      //addLinkBefore.next().css('display', 'none');
      //$('#ui-header', sm4.activity.composer.content).css('display', 'block');
      $('#ui-post-reply-header-addpeople_'+id, sm4.activity.composer.content).css('display', 'none');
      //sm4.activity.toggleFeedArea('', false, 'addpeople');
    },
    addEditFriends: function(form_id) {
      $('#toEditValues', sm4.activity.composer.content).val($('#toEditValues-temp', sm4.activity.composer.content).val());
          
      $('#toEditValuesdone-wrapper', sm4.activity.composer.content).html($('#toEditValues-temp-wrapper', sm4.activity.composer.content).html()).find('div').remove();
      if ($('#toEditValues-temp', sm4.activity.composer.content).val() != '') {
        
        $('#toEditValuesdone-wrapper', sm4.activity.composer.content).css('display', 'block');
      }
      else {
        $('#toEditValuesdone-wrapper', sm4.activity.composer.content).css('display', 'none');
      }

      $('#toEditValuesdone-wrapper', sm4.activity.composer.content).find('.remove').off('click').on("click", function(e) {
        var id = this.id;
        sm4.core.Module.autoCompleter.removeTagResults($(this), 'toEditValues');
        sm4.core.Module.autoCompleter.removeTagResults($('#' + id, sm4.activity.composer.content), 'toEditValues-temp');
        if ($('#toEditValues', sm4.activity.composer.content).val() != '')
          sm4.activity.options.allowEmptyWithoutAttachment = true;
        else {
          sm4.activity.options.allowEmptyWithoutAttachment = false;
          $('#toEditValuesdone-wrapper', sm4.activity.composer.content).html('');
          $('#toEditValuesdone-wrapper', sm4.activity.composer.content).css('display', 'none');
          $('.cm-icon-user', sm4.activity.composer.content).removeClass('active');
        }
      });
     
     // $('#toCommentValuesdone-wrapper', sm4.activity.composer.content).inject($(form_id));

      var addLinkBefore = $('#adv_edit_post_container_tagging', sm4.activity.composer.content);
      //addLinkBefore.prevAll().css('display', 'block');
      $('#composer-nested-edit-options').css('display', 'block');
      $('#edit-emoticons-button').css('display', 'block');
      $('#edit-add-people').css('display', 'block');
      addLinkBefore.css('display', 'none');
      //addLinkBefore.next().css('display', 'none');
      //$('#ui-header', sm4.activity.composer.content).css('display', 'block');
      $('#ui-post-edit-header-addpeople', sm4.activity.composer.content).css('display', 'none');
      
      //sm4.activity.toggleFeedArea('', false, 'addpeople');
    }, 
    search: function() {
      this.getFriends({
        'page': 1,
        'search': this.elements.searchText.val()
      });
    },
    searchLink: function(event) {
      var el = event.target;
      this.getFriends({
        'page': el.get("rev"),
        'search': this.elements.searchText.val()
      });
    },
    searchSelect: function(event) {
      var el = event.target;
      this.getFriends({
        'page': el.value,
        'search': this.elements.searchText.val()
      });
    },
    addTag: function(event) {
      var el = event.target;
      var id = el.get("rel");
      var label = el.get("rev");
      var self = this;

      if (this.tag_ids == "") {
        this.elements.tagcontainer = $('<div />', {
          'class': 'aaf-add-friend-tagcontainer'
        });
        var tagspan = $('<span />', {
          'class': 'aff-tag-with',
          'html': sm4.core.language.translate('with:')
        }).inject(this.elements.tagcontainer);
        this.elements.tagcontainer.inject(this.getComposer().getMenu(), 'before');
        this.tag_ids = id;
      } else {
        if (this.hasTagged(id))
          return;
        this.tag_ids = this.tag_ids + ',' + id;
      }

      var tagspan = $('<span />', {
        'class': 'tag',
        'html': label
      });

      $('<a />', {
        'html': 'X',
        'rel': id,
        'href': 'javascript:void(0);',
        'click': self.removeTag.bind(this)
      }).inject(tagspan);

      tagspan.inject(this.elements.tagcontainer);
    },
    removeTag: function(event) {
      var el = event.target;
      var id = el.get("rel");
      if (this.hasTagged(id)) {
        el.getParent().destroy();
        var toValueArray = this.tag_ids.split(",");
        var toValueIndex = 0;
        for (var i = 0; i < toValueArray.length; i++) {
          if (toValueArray[i] == id)
            toValueIndex = i;
        }

        toValueArray.splice(toValueIndex, 1);

        if (toValueArray.length > 0) {
          this.tag_ids = toValueArray.join();
        } else {
          this.tag_ids = '';
          this.elements.tagcontainer.destroy();
        }
      }
    },
    hasTagged: function(id) {

      var toValueArray = this.tag_ids.split(",");
      var hasTagged = false;
      for (var i = 0; i < toValueArray.length; i++) {
        if (toValueArray[i] == id) {
          hasTagged = true;
          break;
        }
      }
      return hasTagged;
    },
    submit: function() {

      this.makeFormInputs({
        toValues: this.tag_ids

      });
    },
    makeFormInputs: function(data) {
      $.each(data, function(key, value) {
        this.setFormInputValue(key, value);
      }.bind(this));
    },
    // make tag hidden input and set value into composer form
    setFormInputValue: function(key, value) {

      var elName = 'aafComposerForm' + key.capitalize();
      var composerObj = this.getComposer();
      if (!composerObj.elements.has(elName)) {
        composerObj.elements.attr(elName, $('<input />', {
          'type': 'hidden',
          'name': key,
          'value': value || ''
        }).inject(self.getInputArea(this)));
      }
      composerObj.elements.get(elName).value = value;
    },
    reset: function() {

      $.each(this.elements, function(key, element) {
        if ($.type(element) == 'object' && key != 'loading' && key != 'activator' && key != 'menu') {
          $(element).remove();

        }
      }.bind(this));
      this.params = {};
      this.elements = {};

    }
  },
  link: {
    name: 'Link',
    options: {
      title: sm4.core.language.translate('Add Link'),
      lang: {},
      // Options for the link preview request
      requestOptions: {},
      persistentElements: ['activator', 'loadingImage'],
      // Various image filtering options
      imageMaxAspect: (10 / 3),
      imageMinAspect: (3 / 10),
      imageMinSize: 48,
      imageMaxSize: 5000,
      imageMinPixels: 2304,
      imageMaxPixels: 1000000,
      imageTimeout: 5000,
      // Delay to detect links in input
      monitorDelay: 600,
      debug: false
    },
    init: function() {

      this.elements = {};
      this.params = {};
    },
    activate: function() {
      self.makeMenu();
      self.makeBody();
      // Generate body contents
      // Generate form

      this.elements.formInput = $('<input />', {
        'id': 'compose-link-form-submit',
        'class': 'compose-form-submit',
        'html': sm4.core.language.translate('Attach'),
        'click': function(e) {
          e.preventDefault();
          this.doAttach();
        }.bind(this)
      }).inject(this.elements.body);


      this.elements.formSubmit = $('<button />', {
        'id': 'compose-link-form-submit',
        'class': 'compose-form-submit',
        'html': sm4.core.language.translate('Attach'),
        'click': function(e) {
          e.preventDefault();
          this.doAttach();
        }.bind(this)
      }).inject(this.elements.body);

      this.elements.formInput.focus();
    },
    // Getting into the core stuff now

    doAttach: function() {
      var val = this.elements.formInput.val();
      if (!val) {
        return;
      }
      if (!val.match(/^[a-zA-Z]{1,5}:\/\//))
      {
        val = 'http://' + val;
      }
      this.params.uri = val;
      // Input is empty, ignore attachment
      if (val == '') {
        e.preventDefault();
        return;
      }

      var options = $.merge({
        type: 'POST',
        url: sm4.core.baseUrl + 'core/link/preview',
        dataType: "json",
        'data': {
          'format': 'json',
          'uri': val
        },
        'success': this.doProcessResponse.bind(this)
      }, this.options.requestOptions);


      // Inject loading
      self.makeLoading('empty');
      $.ajax(options);
    },
    doProcessResponse: function(responseJSON, responseText) {

      // Handle error
      if ($.type(responseJSON) != 'object') {
        responseJSON = {
          'status': false
        };
      }
      this.params.uri = responseJSON.url;

      // If google docs then just output Google Document for title and descripton
      var uristr = responseJSON.url;
      if (uristr.substr(0, 23) == 'https://docs.google.com') {
        var title = uristr;
        var description = sm4.core.language.translate('Google Document');
      } else {
        var title = responseJSON.title || responseJSON.url;
        var description = responseJSON.description || responseJSON.title || responseJSON.url;
      }

      var images = responseJSON.images || [];

      this.params.title = title;
      this.params.description = description;
      this.params.images = images;
      this.params.loadedImages = [];
      this.params.thumb = '';

      if (images.length > 0) {
        this.doLoadImages();
      } else {
        this.doShowPreview();
      }
      sm4.activity.options.allowEmptyWithoutAttachment = true;
    },
    // Image loading

    doLoadImages: function() {

      var imagetimeout = this.options.imageTimeout;
      var interval = setTimeout(function() {
        this.doShowPreview();
      }.bind(this), imagetimeout);
      // Load them images
      this.params.loadedImages = [];
      this.params.assets = [];

      $(this.params.images, sm4.activity.composer.content).each(function(index, value) {
        $this.params.assets[index] = $('<img />', {
          'src': value
        })
                .load(function() {
          this.params.loadedImages[index] = this.params.images[index];
          if (index == this.params.images.length) {
            window.clearTimeout(interval);
            this.doShowPreview();
          }
        }.bind(this))
                .error(function() {
          delete this.params.images[index];
        }.bind(this));
      }.bind(this));

    },
    doShowPreview: function() {

      this.elements.body.val('');
      this.makeFormInputs();
      $this.elements.loading.css('display', 'none')
      // Generate image thingy
      if (this.params.loadedImages.length > 0) {
        var tmp = [];
        this.elements.previewImages = $('<div />', {
          'id': 'compose-link-preview-images',
          'class': 'compose-preview-images'
        });
        this.elements.body.append(this.elements.previewImages);

        $.each(this.params.assets, function(index, element) {
          if (!$.type($this.params.loadedImages[index]))
            return;
          $this.elements.previewImages.append(element.addClass('compose-preview-image-invisible'));
          if (!this.checkImageValid(element)) {
            delete this.params.images[index];
            delete this.params.loadedImages[index];
            element.remove();
          } else {
            element.removeClass('compose-preview-image-invisible').addClass('compose-preview-image-hidden');
            tmp.push($this.params.loadedImages[index]);
            element.removeAttr('height');
            element.removeAttr('width');
          }
        }.bind(this));

        $this.params.loadedImages = tmp;

        if ($this.params.loadedImages.length <= 0) {
          $this.elements.previewImages.remove();
        }
      }

      this.elements.previewInfo = $('<div />', {
        'id': 'compose-link-preview-info',
        'class': 'compose-preview-info'
      });
      this.elements.body.append(this.elements.previewInfo);

      // Generate title and description
      this.elements.previewTitle = $('<div />', {
        'id': 'compose-link-preview-title',
        'class': 'compose-preview-title'
      });
      this.elements.previewInfo.append(this.elements.previewTitle);

      this.elements.previewTitleLink = $('<a />', {
        'href': this.params.uri,
        'html': this.params.title,
        'class': 'ui-link',
        'click': function(e) {
          e.preventDefault();
          $this.handleEditTitle(this);
        }
      });
      this.elements.previewTitle.append(this.elements.previewTitleLink);


      this.elements.previewDescription = $('<div />', {
        'id': 'compose-link-preview-description',
        'class': 'compose-preview-description',
        'html': this.params.description,
        'click': function(e) {
          e.preventDefault();
          $this.handleEditDescription(this);
        }
      }).inject(this.elements.previewInfo);
      this.elements.previewInfo.append(this.elements.previewDescription);


      // Generate image selector thingy
      if (this.params.loadedImages.length > 0) {
        this.elements.previewOptions = $('<div />', {
          'id': 'compose-link-preview-options',
          'class': 'compose-preview-options'
        }).inject(this.elements.previewInfo);

        if (this.params.loadedImages.length > 1) {
          this.elements.previewChoose = $('<div />', {
            'id': 'compose-link-preview-options-choose',
            'class': 'compose-preview-options-choose',
            'html': '<span>' + sm4.core.language.translate('Choose Image:') + '</span>'
          }).inject(this.elements.previewOptions);

          this.elements.previewPrevious = $('<a />', {
            'id': 'compose-link-preview-options-previous',
            'class': 'compose-preview-options-previous',
            'href': 'javascript:void(0);',
            'html': '&#171; ' + sm4.core.language.translate('Last'),
            'click': this.doSelectImagePrevious.bind(this)
          }).inject(this.elements.previewChoose);

          this.elements.previewCount = $('<span />', {
            'id': 'compose-link-preview-options-count',
            'class': 'compose-preview-options-count'
          }).inject(this.elements.previewChoose);


          this.elements.previewPrevious = $('<a />', {
            'id': 'compose-link-preview-options-next',
            'class': 'compose-preview-options-next',
            'href': 'javascript:void(0);',
            'html': sm4.core.language.translate('Next') + ' &#187;',
            'click': this.doSelectImageNext.bind(this)
          }).inject(this.elements.previewChoose);
        }

        this.elements.previewNoImage = $('<div />', {
          'id': 'compose-link-preview-options-none',
          'class': 'compose-preview-options-none'
        }).inject(this.elements.previewOptions);

        this.elements.previewNoImageInput = $('<input />', {
          'id': 'compose-link-preview-options-none-input',
          'class': 'compose-preview-options-none-input',
          'type': 'checkbox',
          'click': this.doToggleNoImage.bind(this)
        }).inject(this.elements.previewNoImage);

        this.elements.previewNoImageLabel = $('<label />', {
          'for': 'compose-link-preview-options-none-input',
          'html': sm4.core.language.translate('Don\'t show an image')

        }).inject(this.elements.previewNoImage);

        // Show first image
        this.setImageThumb($(this.elements.previewImages.children()[0]));
      }
    },
    makeFormInputs: function() {

      var data = {
        'uri': $this.params.uri,
        'title': $this.params.title,
        'description': $this.params.description,
        'thumb': $this.params.thumb
      };
      self.makeFormInputs(data);
    },
    checkImageValid: function(element) {
      var size = {
        'x': element.outerWidth(),
        'y': element.outerHeight()
      };

      var sizeAlt = {
        x: element.innerWidth(),
        y: element.height()
      };
      var width = sizeAlt.x || size.x;
      var height = sizeAlt.y || size.y;
      var pixels = width * height;
      var aspect = width / height;
      // Debugging
      if (this.options.debug) {
        console.log(element.get('src'), sizeAlt, size, width, height, pixels, aspect);
      }

      // Check aspect
      if (aspect > this.options.imageMaxAspect) {
        // Debugging
        if (this.options.debug) {
          console.log('Aspect greater than max - ', element.get('src'), aspect, this.options.imageMaxAspect);
        }
        return false;
      } else if (aspect < this.options.imageMinAspect) {
        // Debugging
        if (this.options.debug) {
          console.log('Aspect less than min - ', element.get('src'), aspect, this.options.imageMinAspect);
        }
        return false;
      }
      // Check min size
      if (width < this.options.imageMinSize) {
        // Debugging
        if (this.options.debug) {
          console.log('Width less than min - ', element.get('src'), width, this.options.imageMinSize);
        }
        return false;
      } else if (height < this.options.imageMinSize) {
        // Debugging
        if (this.options.debug) {
          console.log('Height less than min - ', element.get('src'), height, this.options.imageMinSize);
        }
        return false;
      }
      // Check max size
      if (width > this.options.imageMaxSize) {
        // Debugging
        if (this.options.debug) {
          console.log('Width greater than max - ', element.get('src'), width, this.options.imageMaxSize);
        }
        return false;
      } else if (height > this.options.imageMaxSize) {
        // Debugging
        if (this.options.debug) {
          console.log('Height greater than max - ', element.get('src'), height, this.options.imageMaxSize);
        }
        return false;
      }
      // Check  pixels
      if (pixels < this.options.imageMinPixels) {
        // Debugging
        if (this.options.debug) {
          console.log('Pixel count less than min - ', element.get('src'), pixels, this.options.imageMinPixels);
        }
        return false;
      } else if (pixels > this.options.imageMaxPixels) {
        // Debugging
        if (this.options.debug) {
          console.log('Pixel count greater than max - ', element.get('src'), pixels, this.options.imageMaxPixels);
        }
        return false;
      }

      return true;
    },
    doSelectImagePrevious: function() {
      if ($.type(this.elements.imageThumb) != 'undefined' && $(this.elements.imageThumb).prev() && $.type($(this.elements.imageThumb).prev().get(0) != 'undefined')) {
        this.setImageThumb($(this.elements.imageThumb).prev());
      }
    },
    doSelectImageNext: function() {
      if ($.type(this.elements.imageThumb) != 'undefined' && $(this.elements.imageThumb).next() && $.type($(this.elements.imageThumb).next().get(0) != 'undefined')) {
        this.setImageThumb($(this.elements.imageThumb).next());
      }
    },
    doToggleNoImage: function() {

      if ($.type(this.params.thumb) == 'undefined') {
        this.params.thumb = this.elements.imageThumb.src;
        self.setFormInputValue('thumb', this.params.thumb);
        this.elements.previewImages.css('display', 'block');
        if (this.elements.previewChoose)
          this.elements.previewChoose.css('display', 'block');
      } else {
        delete this.params.thumb;
        self.setFormInputValue('thumb', '');
        this.elements.previewImages.css('display', 'none');
        if (this.elements.previewChoose)
          this.elements.previewChoose.css('display', 'none');
      }
    },
    setImageThumb: function(element) {
      // Hide old thumb
      if (this.elements.imageThumb) {
        $(this.elements.imageThumb).addClass('compose-preview-image-hidden');
      }

      if (element) {
        element.removeClass('compose-preview-image-hidden');
        if (typeof element.get(0) == 'undefined')
          return;
        this.elements.imageThumb = element.get(0);
        this.params.thumb = element.get(0).src;
        self.setFormInputValue('thumb', element.get(0).src);
        if (this.elements.previewCount) {
          var index = this.params.loadedImages.indexOf(element.get(0).src);
          //this.elements.previewCount.set('html', ' | ' + (index + 1) + ' of ' + this.params.loadedImages.length + ' | ');
          var count = parseInt(index) + 1;
          this.elements.previewCount.html(' | ' + count + ' of ' + this.params.loadedImages.length + ' | ');
        }

      } else {
        this.elements.imageThumb = false;
        delete this.params.thumb;
      }
    },
    handleEditTitle: function(element) {
      $(element).css('display', 'none');
      var input = $('<input />', {
        'type': 'text',
        'value': $(element).text().trim(),
        'blur': function() {
          if (input.val().trim() != '') {
            this.params.title = input.val();
            $(element).text(this.params.title)
            self.setFormInputValue('title', this.params.title);
          }
          $(element).css('display', '');
          input.remove();
        }.bind(this)
      }).inject($(element), 'after');
      input.get(0).focus();
    },
    handleEditDescription: function(element) {
      $(element).css('display', 'none');
      var input = $('<textarea />', {
        'html': $(element).text().trim(),
        'blur': function() {
          if (input.val().trim() != '') {
            this.params.description = input.val();
            $(element).text(this.params.description);
            self.setFormInputValue('description', this.params.description);
          }
          $(element).css('display', '');
          input.remove();
        }.bind(this)
      }).inject($(element), 'after');
      input.get(0).focus();
    }

  },
  video: {
    name: 'Video',
    options: {
      title: sm4.core.language.translate('Add Video'),
      lang: {},
      // Options for the link preview request
      requestOptions: {},
      // Various image filtering options
      imageMaxAspect: (10 / 3),
      imageMinAspect: (3 / 10),
      imageMinSize: 48,
      imageMaxSize: 5000,
      imageMinPixels: 2304,
      imageMaxPixels: 1000000,
      imageTimeout: 5000,
      // Delay to detect links in input
      monitorDelay: 250
    },
    persistentElements: ['activator', 'loadingImage'],
    init: function() {
      this.options.requestOptions = {
        'url': sm4.activity.advfeed_array[$.mobile.activePage.attr('id') + '_attachmentURL'].videourl,
        'deleteurl': sm4.activity.advfeed_array[$.mobile.activePage.attr('id') + '_attachmentURL'].videodeleturl
      };

      this.elements = {};
      this.params = {};
      //this.activate();
    },
    deactivate: function() {
      this.options.requestOptions.deleteurl = sm4.activity.advfeed_array[$.mobile.activePage.attr('id') + '_attachmentURL'].videodeleturl;
      // clean video out if not attached
      sm4.activity.composer.active = false;
      this.getTray().remove();
      $('#composer-options', sm4.activity.composer.content).show();
      this.reset();
      if (this.params.video_id)
        $.ajax({
          url: this.options.requestOptions.deleteurl,
          dataType: "json",
          data: {
            format: 'json',
            video_id: this.params.video_id
          }
        });

    },
    activate: function() {
      this.options.requestOptions.url = sm4.activity.advfeed_array[$.mobile.activePage.attr('id') + '_attachmentURL'].videourl;
      this.options.requestOptions.deleteurl = sm4.activity.advfeed_array[$.mobile.activePage.attr('id') + '_attachmentURL'].videodeleturl;
      self.makeMenu();
      self.makeBody();

      // Generate body contents
      // Generate form

      this.elements.formInput = $('<select />', {
        'id': 'compose-video-form-type',
        'class': 'compose-form-input',
        'option': 'test',
        'change': this.updateVideoFields.bind(this)
      });
      this.elements.body.append(this.elements.formInput);
      $('<option />', {
        value: '0',
        text: sm4.core.language.translate('Choose Source')
      }).appendTo(this.elements.formInput);
      $('<option />', {
        value: '1',
        text: sm4.core.language.translate('YouTube')
      }).appendTo(this.elements.formInput);
      $('<option />', {
        value: '2',
        text: sm4.core.language.translate('Vimeo')
      }).appendTo(this.elements.formInput);
      if(uploadDevice){
      $('<option />', {
        value: '3',
        text: sm4.core.language.translate('My Device')
      }).appendTo(this.elements.formInput);
       }
      this.elements.formInput = $('<input />', {
        'id': 'compose-video-form-input',
        'class': 'compose-form-input',
        'type': 'text',
        'style': 'display:none;'
      });
     
      this.elements.body.append(this.elements.formInput);
      
      this.elements.previewDescription = $('<div />', {
        'id': 'compose-video-upload',
        'class': 'compose-video-upload',
        'html': ('To upload a video from your computer, please use our <a  href="javascript:void(0)" onclick="myDeviceUpload();">full uploader</a>.'),
        'style': 'display:none;'
      });
      this.elements.body.append(this.elements.previewDescription);
      myDeviceUpload = function(){
          $.mobile.changePage(uploadUrl);
      }
      
      this.elements.formSubmit = $('<button />', {
        'id': 'compose-video-form-submit',
        'class': 'compose-form-submit',
        'style': 'display:none;',
        'html': sm4.core.language.translate('Attach'),
        'click': function(e) {
          e.preventDefault();
          this.doAttach();
        }.bind(this)
      });
      this.elements.body.append(this.elements.formSubmit);
      this.elements.formInput.focus();
    },
    doAttach: function(e) {
      var val = this.elements.formInput.val();
      if (!val)
      {
        return;
      }
      if (!val.match(/^[a-zA-Z]{1,5}:\/\//))
      {
        val = 'http://' + val;
      }
      this.params.uri = val;
      // Input is empty, ignore attachment
      if (val == '') {
        e.preventDefault();
        return;
      }

      var video_element = $("#compose-video-form-type", sm4.activity.composer.content);
      var type = video_element.val();
      // Send request to get attachment
      var options = $.merge({
        type: 'POST',
        url: this.options.requestOptions.url,
        dataType: "json",
        'data': {
          'format': 'json',
          'uri': val,
          'type': type
        },
        'success': this.doProcessResponse.bind(this)
      }, this.options.requestOptions);

      // Inject loading
      self.makeLoading('empty');
      $.ajax(options);

    },
    doImageLoaded: function() {

      if (this.elements.loading)
        this.elements.loading.remove();
      this.elements.preview.removeAttr('width');
      this.elements.preview.removeAttr('height');
      this.elements.body.append(this.elements.preview);


      this.elements.previewInfo = $('<div />', {
        'id': 'compose-video-preview-info',
        'class': 'compose-preview-info'
      });
      this.elements.body.append(this.elements.previewInfo);
      this.elements.previewTitle = $('<div />', {
        'id': 'compose-video-preview-title',
        'class': 'compose-preview-title'
      });
      this.elements.previewInfo.append(this.elements.previewTitle);

      this.elements.previewTitleLink = $('<a />', {
        'href': this.params.uri,
        'html': this.params.title,
        'class': 'ui-link',
        'click': function(e) {
          e.preventDefault();
          this.handleEditTitle(this);
        }.bind(this)
      });
      this.elements.previewTitle.append(this.elements.previewTitleLink);


      this.elements.previewDescription = $('<div />', {
        'id': 'compose-video-preview-description',
        'class': 'compose-preview-description',
        'html': this.params.description,
        'click': function(e) {
          e.preventDefault();
          this.handleEditDescription(this);
        }.bind(this)
      });
      this.elements.previewInfo.append(this.elements.previewDescription);

      this.makeFormInputs();
    },
    makeFormInputs: function() {

      var data = {
        'photo_id': this.params.photo_id,
        'video_id': this.params.video_id,
        'title': this.params.title,
        'description': this.params.description,
        'type': this.params.type
      };

      self.makeFormInputs(data);
    },
    doProcessResponse: function(responseJSON, responseText) {

      // Handle error
      if (($.type(responseJSON) != 'hash' && $.type(responseJSON) != 'object') || $.type(responseJSON.src) != 'string' || $.type(parseInt(responseJSON.video_id)) != 'number') {
        //this.elements.body.empty();
        if (this.elements.loading)
          this.elements.loading.remove();

        self.makeError(responseJSON.message, 'empty');

        return;
        //throw "unable to upload image";
      }

      var title = responseJSON.title || this.params.get('uri').replace('http://', '');


      this.params.title = responseJSON.title;
      this.params.description = responseJSON.description;
      this.params.photo_id = responseJSON.photo_id;
      this.params.video_id = responseJSON.video_id;
      this.params.type = responseJSON.type;
      this.elements.preview = $('<img />');
      this.elements.preview.attr({
        'src': responseJSON.src,
        'id': 'compose-video-preview-image',
        'class': 'compose-preview-image',
        'onload': this.doImageLoaded.bind(this)
      });
      sm4.activity.options.allowEmptyWithoutAttachment = true;

    },
    updateVideoFields: function(element) {
      var video_element = document.getElementById("compose-video-form-type");
      var url_element = document.getElementById("compose-video-form-input");
      var post_element = document.getElementById("compose-video-form-submit");
      var upload_element = document.getElementById("compose-video-upload");
      // clear url if input field on change
      $('#compose-video-form-input', sm4.activity.composer.content).value = "";

      // If video source is empty
      if (video_element.value == 0)
      {
        upload_element.style.display = "none";
        post_element.style.display = "none";
        url_element.style.display = "none";
      }

      // If video source is youtube or vimeo
      if (video_element.value == 1 || video_element.value == 2)
      {
        upload_element.style.display = "none";
        post_element.style.display = "block";
        url_element.style.display = "block";
        url_element.focus();
      }

      // if video source is upload
      if (video_element.value == 3)
      {
        upload_element.style.display = "block";
        post_element.style.display = "none";
        url_element.style.display = "none";
      }
    }

  },
  photo: {
    name: 'photo',
    parent: false,
    options: {
      title: sm4.core.language.translate('Add Photo'),
      lang: {},
      requestOptions: false,
      fancyUploadEnabled: true,
      fancyUploadOptions: {}
    },
    persistentElements: ['activator', 'loadingImage'],
    init: function() {

      this.options.requestOptions = {
        'url': sm4.activity.advfeed_array[$.mobile.activePage.attr('id') + '_attachmentURL'].photourl
      };
      this.elements = {};
      this.params = {};

    },
    activate: function() {
      this.options.requestOptions.url = sm4.activity.advfeed_array[$.mobile.activePage.attr('id') + '_attachmentURL'].photourl;
      self.makeMenu();
      self.makeBody();
      if ($.type($('#subject', sm4.activity.composer.content).get(0)) != 'undefined')
        var pagesubject = $('#subject', sm4.activity.composer.content).val();
      else
        var pagesubject = false;
      if (pagesubject) {

        pagesubject = pagesubject.split('_');
        if (pagesubject.length >= 3) {
          var page_id = pagesubject[2];
          var page = pagesubject[1] + '_id';
        }
        else {
          var page_id = pagesubject[1];
          var page = pagesubject[0] + '_id';
        }
      }
      else
        var page_id = '';
      // Generate form
      if (page_id != '') {
        var fullUrl = this.options.requestOptions.url + page + '/' + page_id
      }
      else
        var fullUrl = this.options.requestOptions.url;

      this.elements.form = $('<form />', {
        'id': 'compose-photo-form',
        'class': 'compose-form',
        'method': 'post',
        'action': fullUrl,
        'enctype': 'multipart/form-data',
        'data-ajax': 'false'
      });
      this.elements.body.append(this.elements.form);

      //CREATING A FILE TYPE INPUT
      var spanWrapperParent = $('<span />', {
        'id': 'photobutton',
        'data-role': 'button',
        'data-corners': 'true',
        'data-shadow': 'true',
        'data-iconshadow': 'true',
        'class': 'file-input-button ui-btn ui-shadow ui-btn-corner-all ui-btn-up-c'
      });

      var spanWrapperChild1 = $('<span />', {
        'class': 'ui-btn-inner ui-btn-corner-all'
      });

      var spanWrapperChild2 = $('<span />', {
        'class': 'ui-btn-text',
        'html': sm4.core.language.translate('Add Photo')
      });


      if (sm4.core.isApp()) {
        this.elements.formInput = $('<div />', {
          'class': "photo-compose-buttons"
        });

        var $button1 = $('<button />', {
          'class': 'photo-button-camera',
          'type': 'button',
          'html': sm4.core.language.translate('Capture')
        });
        var $button2 = $('<button />', {
          'class': 'photo-button-gallery',
          'type': 'button',
          'html': sm4.core.language.translate('Choose From Gallery')
        });

        this.elements.formInput.append($button1);
        this.elements.formInput.append($button2);
        $button1.on('vclick', function(e) {
          e.preventDefault();
          sm4.activity.composer.photo.capturePhoto();
        });
        $button2.on('vclick', function(e) {
          e.preventDefault();
          sm4.activity.composer.photo.getPhoto(smappcore.pictureSource.PHOTOLIBRARY)
        });
      } else if (DetectAllWindowsMobile()) { //SPECIAL CASE => IF THE MOBILE IS WINDOWS MOBILE THEN WE WILL SHOW USERS AN ERROR MESSAGE.
        this.elements.formInput = $('<div />', {
          'id': 'photo',
          'html': sm4.core.language.translate('Sorry, the browser you are using does not support Photo uploading. You can upload the Photo from your Desktop.')

        });

      }
      else {
        this.elements.formInput = $('<input />', {
          'id': 'photo',
          //'class' : 'ui-input-text ui-body-c fileInput',
          'type': 'file',
          'name': 'Filedata',
          'accept': 'image/*',
          'change': this.doRequest.bind(this)
        });
      }

      this.elements.formInput_temp = $('<input />', {
        'type': 'hidden',
        'name': 'feedphoto',
        'value': '1'
      });

      spanWrapperChild1.append(spanWrapperChild2);
      spanWrapperParent.append(spanWrapperChild1);
      spanWrapperParent.append(this.elements.formInput);
      this.elements.form.append(this.elements.formInput_temp);
      this.elements.form.append(this.elements.formInput);
      if (!(sm4.core.isApp() && DetectAllWindowsMobile())) {
        var fileInput = this.elements.formInput;
        var fileInputName = 'Filedata';
        var fileInputLabel = sm4.core.language.translate('Choose Photo');

        var fileInputButton = $('<span id="'
                + fileInputName + 'button" data-role="button" data-corners="true" data-shadow="true" data-iconshadow="true" class="file-input-button ui-btn ui-shadow ui-btn-corner-all ui-btn-up-c">'
                + '<span class="ui-btn-inner ui-btn-corner-all">'
                + '<span class="ui-btn-text">'
                + fileInputLabel
                + '</span>'
                + '</span>'
                + '</span>');

        fileInputButton.insertBefore(fileInput);
        fileInput.insertAfter(fileInputButton.find('.ui-btn-inner'));
        var parentDiv = fileInputButton.parent();
        if (parentDiv.hasClass('ui-input-text')) {
          parentDiv.attr('class', 'file-input-button-wrapper');
        }

        fileInputButton.unbind();
        fileInput.addClass('fileInput');
        fileInput.bind('change', function() {
          $('.ui-page-active').find('form #' + $(this).attr('name') + 'button .ui-btn-text').html($(this).val());
        });
      }
    },
    deactivate: function() {
      self.deactivate();
    },
    doRequest: function() {
      photoUpload = true;
      $("#compose-photo-form", sm4.activity.composer.content).ajaxForm({
        target: '#compose-photo-body',
        data: {
          'feedphoto': true,
          'format': 'html'
        },
        success: function(responseJSON, textStatus, xhr) {
          photoUpload = false;
          if ($.type($('#activitypost-container-temp', sm4.activity.composer.content).find('#compose-photo-body').children('#advfeed-photo').get(0)) == 'undefined' && $.type($('#reply_options_box_'+ comment_reply_id, sm4.activity.composer.content).get(0)) == 'undefined') {
            $('#activitypost-container-temp', sm4.activity.composer.content).find('#compose-photo-body').html(sm4.core.language.translate('Invalid Upload'));
          }
          else {
            if($('#activitypost-container-temp')) {
                var containerTemp= $('#activitypost-container-temp').get(0);
                var action_id = $(containerTemp).attr('action-id');
                if($.type($('#reply_options_box_'+ comment_reply_id, sm4.activity.composer.content).get(0)) != 'undefined' && $('#activitypost-container-temp').find('.reply')) { 
                    containerTemp = $('#reply_options_box_'+ comment_reply_id, sm4.activity.composer.content).parent('#activitypost-container-temp').get(0);
                     action_id = $(containerTemp).attr('action-id');
                     var formEle =  $('#' + action_id);
                    formEle.append(sm4.activity.composer.getInputArea($this).html($('#reply_options_box_'+ comment_reply_id, sm4.activity.composer.content).parent('#activitypost-container-temp').find('#compose-photo-body').children('#advfeed-photo')));
                } else if($.type($('#comment_options_box', sm4.activity.composer.content).get(0)) != 'undefined') {
                    containerTemp = $('#comment_options_box', sm4.activity.composer.content).parent('#activitypost-container-temp').get(0);
                     action_id = $(containerTemp).attr('action-id');
                     var formEle =  $('#' + action_id);
                     formEle.append(sm4.activity.composer.getInputArea($this).html($('#comment_options_box', sm4.activity.composer.content).parent('#activitypost-container-temp').find('#compose-photo-body').children('#advfeed-photo')));
                }
                else {
                    var formEle =  sm4.activity.getForm();
                    formEle.append(sm4.activity.composer.getInputArea($this).html($('#activitypost-container-temp', sm4.activity.composer.content).find('#compose-photo-body').children('#advfeed-photo')));
                }
            } else {
                var formEle =  sm4.activity.getForm();
                formEle.append(sm4.activity.composer.getInputArea($this).html($('#activitypost-container-temp', sm4.activity.composer.content).find('#compose-photo-body').children('#advfeed-photo')));
            }
            
            $('#activitypost-container-temp', sm4.activity.composer.content).find('#compose-photo-body').children('#advfeed-photo').remove();
            sm4.activity.options.allowEmptyWithoutAttachment = true;
          }
        }
      }).submit();


      this.elements.form.attr('style', 'display:none;');
      //
      // Start loading screen
      self.makeLoading();
    },
    doProcessResponse: function(responseJSON) {
      this.elements.form.remove();
      // An error occurred
      if (($.type(responseJSON) != 'hash' && $.type(responseJSON) != 'object') || $.type(responseJSON.src) != 'string' || $.type(parseInt(responseJSON.photo_id)) != 'number') {
        this.elements.body.empty();
        //this.makeError('Unable to upload photo. Please click cancel and try again', 'empty');
        return;
        //throw "unable to upload image";
      }

      // Success
      this.params.rawParams = responseJSON;
      this.params.photo_id = responseJSON.photo_id;
      this.params.type = responseJSON.type;

      this.elements.preview = $('<img />');
      this.elements.preview.attr({
        'src': responseJSON.src,
        'id': 'compose-photo-preview-image',
        'class': 'compose-preview-image',
        'onload': this.doImageLoaded.bind(this)
      });
      sm4.activity.options.allowEmptyWithoutAttachment = true;
    },
    doImageLoaded: function() {
      if (this.elements.loading)
        this.elements.loading.remove();
      //if( this.elements.formFancyContainer ) this.elements.formFancyContainer.destroy();
      this.elements.preview.removeAttr('width');
      this.elements.preview.removeAttr('height');
      this.elements.body.append(this.elements.preview);
      this.makeFormInputs();
    },
    makeFormInputs: function() {
      var data = {
        'photo_id': this.params.photo_id,
        'type': this.params.type
      };
      self.makeFormInputs(data);
    },
    capturePhoto: function(options) {
      var phtoself = this;
      options = {limit: 1};

      // allowing user to capture only one image by {limit: 1}
      navigator.device.capture.captureImage(phtoself.onCaptureSuccess, phtoself.onCaptureError, options);

    },
    onCaptureSuccess: function(mediaFiles) {
      var mediaFile = mediaFiles[0];
      var options = new FileUploadOptions();
      options.fileKey = "Filedata";
      options.fileName = mediaFile.name;
      options.mimeType = "image/jpeg";

      var params = new Object();
      params.fullpath = mediaFile.fullPath;
      params.name = mediaFile.name;
      params.feedphoto = true;
      options.params = params;
      options.chunkedMode = false;
      sm4.activity.composer.photo.uploadReqCount = 0;
      sm4.activity.composer.photo.uploadPhoto(mediaFile.fullPath, options);
    },
    onCaptureError: function(error) {
      if (error.code === 3)
        return;
      var message = 'An error occurred during capture: ' + error.code;

      $.mobile.loading().loader('show',$.mobile.pageLoadErrorMessageTheme, message, true);
      setTimeout(function() {
        $.mobile.loading().loader('hide');
      }, 500);
    },
    getPhoto: function(source, options) {
      options = {
        quality: 50,
        destinationType: smappcore.destinationType.FILE_URI,
        sourceType: smappcore.pictureSource.PHOTOLIBRARY
      };
      // Retrieve image file location from specified source
      sm4.activity.composer.photo.uploadReqCount = 0;
      navigator.camera.getPicture(sm4.activity.composer.photo.onPhotoURISuccess, sm4.activity.composer.photo.onFail, options);
    },
    // Called when a photo is successfully retrieved By getPhoto
    onPhotoURISuccess: function(imageURI) {
      var options = new FileUploadOptions();
      options.chunkedMode = false;
      options.fileKey = "Filedata";
      options.fileName = imageURI.substr(imageURI.lastIndexOf("/") + 1) + ".jpg";
      options.mimeType = "image/jpeg";
      var params = new Object();
      params.fullpath = imageURI;
      params.name = options.fileName;
      params.feedphoto = true;
      options.params = params;
      sm4.activity.composer.photo.uploadPhoto(imageURI, options);
    },
    onFail: function(message) {  // Called if something bad happens.
      if (message !== 'Camera cancelled.') {
        $.mobile.loading().loader('show',$.mobile.pageLoadErrorMessageTheme, message, true);
        setTimeout(function() {
          $.mobile.loading().loader('hide');
        }, 2000);
      }
    },
    uploadReqCount: 0,
    uploadContent: null,
    uploadPhoto: function(imageURI, options)
    {
      sm4.activity.composer.photo.uploadContent = {
        imageURI: imageURI,
        options: options
      };
      options.params.useragentApp = navigator.userAgent;
      var url = sm4.activity.composer.photo.elements.form.attr('action');
      var ft = new FileTransfer();
      url = appconfig.siteInfo.baseHref + url; //.replace(appconfig.siteInfo.baseUrl, '');
      ft.upload(imageURI, url, sm4.activity.composer.photo.onUploadSuccess, sm4.activity.composer.photo.onUploadFail, options);
      this.elements.form.attr('style', 'display:none;');
      // Start loading screen
      self.makeLoading();
    },
    onUploadSuccess: function(r)
    {
      $('#activitypost-container-temp', sm4.activity.composer.content).find('#compose-photo-body').html(r.response);
      if ($.type($('#activitypost-container-temp', sm4.activity.composer.content).find('#compose-photo-body').children('#advfeed-photo').get(0)) === 'undefined') {
        $('#activitypost-container-temp', sm4.activity.composer.content).find('#compose-photo-body').html(sm4.core.language.translate('Invalid Upload'));
      }
      else {
        sm4.activity.getForm().append(sm4.activity.composer.getInputArea($this).html($('#activitypost-container-temp', sm4.activity.composer.content).find('#compose-photo-body').children('#advfeed-photo')));
        $('#activitypost-container-temp', sm4.activity.composer.content).find('#compose-photo-body').children('#advfeed-photo').remove();

        sm4.activity.options.allowEmptyWithoutAttachment = true;
      }

      // alert("Sent = " + r.bytesSent);
    },
    onUploadFail: function(error)
    {
      if (sm4.activity.composer.photo.uploadReqCount < 3 && error.code === FileTransferError.CONNECTION_ERR) {
        sm4.activity.composer.photo.uploadReqCount++;
        sm4.activity.composer.photo.uploadPhoto(sm4.activity.composer.photo.uploadContent.imageURI, sm4.activity.composer.photo.uploadContent.options);
        return;
      }
      var message = '';
      switch (error.code)
      {
        case FileTransferError.FILE_NOT_FOUND_ERR:
          message = "Photo file not found";
          break;
        case FileTransferError.INVALID_URL_ERR:
          message = "Bad Photo URL";
          break;
        case FileTransferError.CONNECTION_ERR:
          message = "Connection error";
          break;
      }
      $.mobile.loading().loader('show',$.mobile.pageLoadErrorMessageTheme, message, true);
      setTimeout(function() {
        $.mobile.loading().loader('hide');
      }, 2000);
      // alert("An error has occurred: Code = " + error.code);
    }
  },
  //music 
  music: {
    name: 'music',
    parent: false,
    options: {
      title: sm4.core.language.translate('Add Music'),
      lang: {},
      requestOptions: false,
      fancyUploadEnabled: true,
      fancyUploadOptions: {}
    },
    persistentElements: ['activator', 'loadingImage'],
    init: function() {

      this.options.requestOptions = {
        'url': sm4.activity.advfeed_array[$.mobile.activePage.attr('id') + '_attachmentURL'].musicurl
      };
      this.elements = {};
      this.params = {};

    },
    activate: function() {
      this.options.requestOptions.url = sm4.activity.advfeed_array[$.mobile.activePage.attr('id') + '_attachmentURL'].musicurl;
      self.makeMenu();
      self.makeBody();

      if ($.type($('#subject', sm4.activity.composer.content).get(0)) != 'undefined')
        var pagesubject = $.mobile.activePage.attr("data-subject");
      else
        var pagesubject = false;
      if (pagesubject) {

        pagesubject = pagesubject.split('_');
        if (pagesubject.length >= 3) {
          var page_id = pagesubject[2];
          var page = pagesubject[1] + '_id';
        }
        else {
          var page_id = pagesubject[1];
          var page = pagesubject[0] + '_id';
        }
      }
      else
        var page_id = '';
      // Generate form
      if (page_id != '') {
        var fullUrl = this.options.requestOptions.url + '&' + page + '=' + page_id
      }
      else
        var fullUrl = this.options.requestOptions.url;
      this.elements.form = $('<form />', {
        'id': 'compose-music-form',
        'class': 'compose-form',
        'method': 'post',
        'action': fullUrl,
        'enctype': 'multipart/form-data',
        'data-ajax': 'false'
      });
      this.elements.body.append(this.elements.form);

      //CREATING A FILE TYPE INPUT
      var spanWrapperParent = $('<span />', {
        'id': 'musicbutton',
        'data-role': 'button',
        'data-corners': 'true',
        'data-shadow': 'true',
        'data-iconshadow': 'true',
        'class': 'file-input-button ui-btn ui-shadow ui-btn-corner-all ui-btn-up-c'
      });

      var spanWrapperChild1 = $('<span />', {
        'class': 'ui-btn-inner ui-btn-corner-all'
      });

      var spanWrapperChild2 = $('<span />', {
        'class': 'ui-btn-text',
        'html': sm4.core.language.translate('Add Music')
      });

      //SPECIAL CASE => IF THE MOBILE IS WINDOWS MOBILE THEN WE WILL SHOW USERS AN ERROR MESSAGE.

      if (DetectAllWindowsMobile()) {
        this.elements.formInput = $('<div />', {
          'id': 'music',
          'html': sm4.core.language.translate('Sorry, the browser you are using does not support Music uploading. You can upload the Music from your Desktop.')

        });

      }
      else {
        this.elements.formInput = $('<input />', {
          'id': 'music',
          //'class' : 'ui-input-text ui-body-c fileInput',
          'type': 'file',
          'name': 'Filedata',
          'accept': 'audio/*',
          'change': this.doRequest.bind(this)
        });
      }


      this.elements.formInput_temp = $('<input />', {
        'type': 'hidden',
        'name': 'feedmusic',
        'value': '1'
      });

      spanWrapperChild1.append(spanWrapperChild2);
      spanWrapperParent.append(spanWrapperChild1);
      spanWrapperParent.append(this.elements.formInput);
      this.elements.form.append(this.elements.formInput_temp);
      this.elements.form.append(this.elements.formInput);

      if (!(sm4.core.isApp())) {
        var fileInput = this.elements.formInput;
        var fileInputName = 'Filedata';
        var fileInputLabel = sm4.core.language.translate('Choose Music');

        var fileInputButton = $('<span id="'
                + fileInputName + 'button" data-role="button" data-corners="true" data-shadow="true" data-iconshadow="true" class="file-input-button ui-btn ui-shadow ui-btn-corner-all ui-btn-up-c">'
                + '<span class="ui-btn-inner ui-btn-corner-all">'
                + '<span class="ui-btn-text">'
                + fileInputLabel
                + '</span>'
                + '</span>'
                + '</span>');

        fileInputButton.insertBefore(fileInput);
        fileInput.insertAfter(fileInputButton.find('.ui-btn-inner'));
        var parentDiv = fileInputButton.parent();
        if (parentDiv.hasClass('ui-input-text')) {
          parentDiv.attr('class', 'file-input-button-wrapper');
        }

        fileInputButton.unbind();
        fileInput.addClass('fileInput');
        fileInput.bind('change', function() {
          $('.ui-page-active').find('form #' + $(this).attr('name') + 'button .ui-btn-text').html($(this).val());
        });
      }
    },
    deactivate: function() {
      self.deactivate();
    },
    doRequest: function() {

      musicUpload = true;
      $("#compose-music-form", sm4.activity.composer.content).ajaxForm({
        target: '#compose-music-body',
        data: {
          'feedmusic': true
        },
        success: function(responseJSON, textStatus, xhr) {
          musicUpload = false;
          if ($.type($('#activitypost-container-temp', sm4.activity.composer.content).find('#compose-music-body').children('#advfeed-music').get(0)) == 'undefined') {
            sm4.activity.composer.content.find('#activitypost-container-temp').find('#compose-music-body').html(sm4.core.language.translate('Invalid Upload'));
          }
          else {
            sm4.activity.getForm().append(sm4.activity.composer.getInputArea($this).html(sm4.activity.composer.content.find('#activitypost-container-temp').find('#compose-music-body').children('#advfeed-music')));
            sm4.activity.composer.content.find('#activitypost-container-temp').find('#compose-music-body').children('#advfeed-music').remove();

            sm4.activity.options.allowEmptyWithoutAttachment = true;
          }
        }
      }).submit();


      this.elements.form.attr('style', 'display:none;');

      // Start loading screen
      self.makeLoading();
    }


  }
};