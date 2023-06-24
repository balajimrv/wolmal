
/* $Id: core.js 9572 2011-12-27 23:41:06Z john $ */



(function() { // START NAMESPACE
var $ = 'id' in document ? document.id : window.$;



en4.activity = {

  load : function(next_id, subject_guid){
    if( en4.core.request.isRequestActive() ) return;

    $('feed_viewmore').style.display = 'none';
    $('feed_loading').style.display = '';

    en4.core.request.send(new Request.HTML({
      url : en4.core.baseUrl + 'activity/widget/feed',
      data : {
        //format : 'json',
        'maxid' : next_id,
        'feedOnly' : true,
        'nolayout' : true,
        'subject' : subject_guid
      }
      /*
      onSuccess : function(){
        $('feed_viewmore').style.display = '';
        $('feed_loading').style.display = 'none';
      }*/
    }), {
      'element' : $('activity-feed'),
      'updateHtmlMode' : 'append'
    });
  },

  like : function(action_id, comment_id) {
    en4.core.request.send(new Request.JSON({
      url : en4.core.baseUrl + 'timeline/index/like',
      data : {
        format : 'json',
        action_id : action_id,
        comment_id : comment_id,
        subject : en4.core.subject.guid
      }
    }), {
      'element' : $('activity-item-'+action_id),
      'updateHtmlMode': 'comments'
    });
  },

  unlike : function(action_id, comment_id) {
    en4.core.request.send(new Request.JSON({
      url : en4.core.baseUrl + 'timeline/index/unlike',
      data : {
        format : 'json',
        action_id : action_id,
        comment_id : comment_id,
        subject : en4.core.subject.guid
      }
    }), {
      'element' : $('activity-item-'+action_id),
      'updateHtmlMode': 'comments'
    });
  },

  comment : function(action_id, body) {
    if( body.trim() == '' )
    {
      return;
    }
    
    en4.core.request.send(new Request.JSON({
      url : en4.core.baseUrl + 'timeline/index/comment',
      data : {
        format : 'json',
        action_id : action_id,
        body : body,
        subject : en4.core.subject.guid
      }
    }), {
      'element' : $('activity-item-'+action_id),
      'updateHtmlMode': 'comments'
    });
  },

  attachComment : function(formElement){
    var bind = this;
    formElement.addEvent('submit', function(event){
      event.stop();
      bind.comment(formElement.action_id.value, formElement.body.value);
    });
  },

  viewComments : function(action_id){
    en4.core.request.send(new Request.JSON({
      url : en4.core.baseUrl + 'timeline/index/viewComment',
      data : {
        format : 'json',
        action_id : action_id,
        nolist : true
      }
    }), {
      'element' : $('activity-item-'+action_id)
    });
  },

  viewLikes : function(action_id){
    en4.core.request.send(new Request.JSON({
      url : en4.core.baseUrl + 'timeline/index/viewLike',
      data : {
        format : 'json',
        action_id : action_id,
        nolist : true
      }
    }), {
      'element' : $('activity-item-'+action_id),
      'updateHtmlMode': 'comments'
    });
  },

  hideNotifications : function(reset_text) {
    en4.core.request.send(new Request.JSON({
      'url' : en4.core.baseUrl + 'activity/notifications/hide'
    }));
    $('updates_toggle').set('html', reset_text).removeClass('new_updates');

    /*
    var notify_link = $('core_menu_mini_menu_updates_count').clone();
    $('new_notification').destroy();
    notify_link.setAttribute('id', 'core_menu_mini_menu_updates_count');
    notify_link.innerHTML = "0 updates";
    notify_link.inject($('core_menu_mini_menu_updates'));
    $('core_menu_mini_menu_updates').setAttribute('id', '');
    */
    if($('notifications_main')){
      var notification_children = $('notifications_main').getChildren('li');
      notification_children.each(function(el){
          el.setAttribute('class', '');
      });
    }

    if($('notifications_menu')){
      var notification_children = $('notifications_menu').getChildren('li');
      notification_children.each(function(el){
          el.setAttribute('class', '');
      });
    }
    //$('core_menu_mini_menu_updates').setStyle('display', 'none');
  },

  updateNotifications : function() {
    if( en4.core.request.isRequestActive() ) return;
    en4.core.request.send(new Request.JSON({
      url : en4.core.baseUrl + 'activity/notifications/update',
      data : {
        format : 'json'
      },
      onSuccess : this.showNotifications.bind(this)
    }));
  },

  showNotifications : function(responseJSON){
    if (responseJSON.notificationCount>0){
      $('updates_toggle').set('html', responseJSON.text).addClass('new_updates');
    }
  },

  markRead : function (action_id){
    en4.core.request.send(new Request.JSON({
      url : en4.core.baseUrl + 'activity/notifications/test',
      data : {
        format     : 'json',
        'actionid' : action_id
      }
    }));
  },

  cometNotify : function(responseObject){
    //for( var x in responseObject ) alert(responseObject[x]);
    //if( $type(responseObject.text) ){
      $('core_menu_mini_menu_updates').style.display = '';
      $('core_menu_mini_menu_updates_count').innerHTML = responseObject.text;
    //}
  }

};

NotificationUpdateHandler = new Class({

  Implements : [Events, Options],
  options : {
      debug : false,
      baseUrl : '/',
      identity : false,
      delay : 5000,
      admin : false,
      idleTimeout : 600000,
      last_id : 0,
      subject_guid : null
    },

  state : true,

  activestate : 1,

  fresh : true,

  lastEventTime : false,

  title: document.title,

  initialize : function(options) {
    this.setOptions(options);
  },

  start : function() {
    this.state = true;

    // Do idle checking
    this.idleWatcher = new IdleWatcher(this, {timeout : this.options.idleTimeout});
    this.idleWatcher.register();
    this.addEvents({
      'onStateActive' : function() {
        this.activestate = 1;
        this.state= true;
      }.bind(this),
      'onStateIdle' : function() {
        this.activestate = 0;
        this.state = false;
      }.bind(this)
    });

    this.loop();
  },

  stop : function() {
    this.state = false;
  },

  updateNotifications : function() {
    if( en4.core.request.isRequestActive() ) return;
    en4.core.request.send(new Request.JSON({
      url : en4.core.baseUrl + 'activity/notifications/update',
      data : {
        format : 'json'
      },
      onSuccess : this.showNotifications.bind(this)
    }));
  },

  showNotifications : function(responseJSON){
    if (responseJSON.notificationCount>0){
      $('updates_toggle').set('html', responseJSON.text).addClass('new_updates');
    }
  },
  
  loop : function() {
    if( !this.state) {
      this.loop.delay(this.options.delay, this);
      return;
    }

    try {
      this.updateNotifications().addEvent('complete', function() {
        this.loop.delay(this.options.delay, this);
      }.bind(this));
    } catch( e ) {
      this.loop.delay(this.options.delay, this);
      this._log(e);
    }
  },

  // Utility

  _log : function(object) {
    if( !this.options.debug ) {
      return;
    }

    // Firefox is dumb and causes problems sometimes with console
    try {
      if( typeof(console) && $type(console) ) {
        console.log(object);
      }
    } catch( e ) {
      // Silence
    }
  }
});

//(function(){

  en4.activity.compose = {

    composers : {},

    register : function(object){
      name = object.getName();
      this.composers[name] = object;
    },

    deactivate : function(){
      for( var x in this.composers ){
        this.composers[x].deactivate();
      }
      return this;
    }

  };


  en4.activity.compose.icompose = new Class({

    Implements: [Events, Options],

    name : false,

    element : false,

    options : {},

    initialize : function(element, options){
      this.element = $(element);
      this.setOptions(options);
    },

    getName : function(){
      return this.name;
    },

    activate : function(){
      en4.activity.compose.deactivate();
    },

    deactivate : function(){

    }
  });

//})();

ActivityUpdateHandler = new Class({

  Implements : [Events, Options],
  options : {
      debug : true,
      baseUrl : '/',
      identity : false,
      delay : 5000,
      admin : false,
      idleTimeout : 600000,
      last_id : 0,
      next_id : null,
      subject_guid : null,
      showImmediately : false
    },

  state : true,

  activestate : 1,

  fresh : true,

  lastEventTime : false,

  title: document.title,
  
  //loopId : false,
  
  initialize : function(options) {
    this.setOptions(options);
  },

  start : function() {
    this.state = true;

    // Do idle checking
    this.idleWatcher = new IdleWatcher(this, {timeout : this.options.idleTimeout});
    this.idleWatcher.register();
    this.addEvents({
      'onStateActive' : function() {
        this._log('activity loop onStateActive');
        this.activestate = 1;
        this.state = true;
      }.bind(this),
      'onStateIdle' : function() {
        this._log('activity loop onStateIdle');
        this.activestate = 0;
        this.state = false;
      }.bind(this)
    });
    this.loop();
    //this.loopId = this.loop.periodical(this.options.delay, this);
  },

  stop : function() {
    this.state = false;
  },

  checkFeedUpdate : function(action_id, subject_guid){
    if( en4.core.request.isRequestActive() ) return;
    var req = new Request.HTML({
      url : en4.core.baseUrl + 'widget/index/name/activity.feed',
      data : {
        'format' : 'html',
        'minid' : this.options.last_id+1,
        'feedOnly' : true,
        'nolayout' : true,
        'subject' : this.options.subject_guid,
        'checkUpdate' : true
      }
    });
    en4.core.request.send(req, {
      'element' : $('feed-update')
    });
    req.addEvent('complete', function() {
      (function() {
        if( this.options.showImmediately && $('feed-update').getChildren().length > 0 ) {
          $('feed-update').setStyle('display', 'none');
          $('feed-update').empty();
          this.getFeedUpdate(this.options.next_id);
        }
      }).delay(50, this);
    }.bind(this));
    return req;
  },

  getFeedUpdate : function(last_id){
    if( en4.core.request.isRequestActive() ) return;
    var min_id = this.options.last_id + 1;
    this.options.last_id = last_id;
    document.title = this.title;
    var request = new Request.HTML({
      url : en4.core.baseUrl + 'widget/index/name/timeline.feed',
      data : {
        'format' : 'html',
        'minid' : min_id,
        'feedOnly' : true,
        'nolayout' : true,
        'getUpdate' : true,
        'subject' : this.options.subject_guid
      },
      evalScripts : true,
      onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
        Elements.from(responseHTML).inject($('activity-feed'), 'top');
        en4.core.runonce.trigger();
        Smoothbox.bind($('activity-feed'));
        start_side();
      }
    });
    request.send();
    return request;
  },

  loop : function() {
    this._log('activity update loop start');
    
    if( !this.state ) {
      this.loop.delay(this.options.delay, this);
      return;
    }

    try {
      this.checkFeedUpdate().addEvent('complete', function() {
        try {
          this._log('activity loop req complete');
          this.loop.delay(this.options.delay, this);
        } catch( e ) {
          this.loop.delay(this.options.delay, this);
          this._log(e);
        }
      }.bind(this));
    } catch( e ) {
      this.loop.delay(this.options.delay, this);
      this._log(e);
    }
    
    this._log('activity update loop stop');
  },
  toggleFeatured : function(action_id) {
    new Request.JSON({
                 'url' : en4.core.baseUrl + 'timeline/ajax/toggle-featured',
                 'data': {
                            'isajax' : true,
                            'action_id' : action_id
                          },
                 'onSuccess' : function(responseObject) {
                    if( $type(responseObject)!="object" ) {
                        alert('ERROR occurred. Please try againe.');
                        return false;
                    }
                    if( !responseObject.status || responseObject.status !=true ) {
                        if (responseObject.reload == true)
                            window.location.reload(true);
                        if( responseObject.error && $type(responseObject.error) == 'string' ) {
                            alert(responseObject.error);
                        }
                        return false;
                    }
                    if (responseObject.status == true) {
                      delete responseObject.status;
                      if (responseObject.toggle == 'added') {
                          $('activity-item-' + action_id).set('data-side', 'featured');
                          $('feature_toggle_' + action_id).set('class', 'tl_featured');
                          start_side();
                      }
                      else {
                          $('activity-item-' + action_id).set('data-side', '');
                          $('feature_toggle_' + action_id).set('class', 'tl_default');
                          start_side();
                      }
                      return false;
                    }
                }.bind(this)
              }).send();
  },
  // Utility
  _log : function(object) {
    if( !this.options.debug ) {
      return;
    }

    try {
      if( 'console' in window && typeof(console) && 'log' in console ) {
        console.log(object);
      }
    } catch( e ) {
      // Silence
    }
  }
});



})(); // END NAMESPACE

function start_side() {
    var activity_feed = $('activity-feed');
    activity_feed.setStyle('opacity', 0);
    var data_side = 'left';
    var height_left = 0;
    var height_right = 0;
    activity_feed.getChildren('li').each(function(item, index){
                                                                if (item.get('data-side') == 'featured') {
                                                                    height_left = 0;
                                                                    height_right = 0;
                                                                    return;
                                                                }
                                                                if (height_left == 0 && height_right == 0) {
                                                                    data_side = 'left';
                                                                }
                                                                else if (height_left != 0 && height_right == 0) {
                                                                    data_side = 'right';
                                                                }
                                                                else if (data_side == 'right') {
                                                                    if ( height_left > height_right) {
                                                                        height_right = height_right + item.getSize().y;
                                                                    }
                                                                    else {
                                                                        data_side = 'left';
                                                                    }
                                                                }
                                                                else {
                                                                    if ( height_left < height_right) {
                                                                    }
                                                                    else {
                                                                        data_side = 'right';
                                                                    }
                                                                }
                                                                item.set('data-side', data_side);
                                                                if (data_side == 'left') {
                                                                    height_left = item.getCoordinates().bottom;
                                                                }
                                                                if (data_side == 'right') {
                                                                    height_right = item.getCoordinates().bottom;
                                                                }
                                                              

                                                            });
    activity_feed.setStyle('opacity', 1);
    }

function startCrop(input_data) {
    var loading = window.$('loading');
    loading.setStyle('display', 'block');
    window.$('form_upload_box').dispose();
    var preview = Asset.image(input_data.src, {
                                   onLoad : function(img){
                                                        loading.setStyle('display', 'none');
                                                        img.inject(window.$('imglayer'), 'top');
                                                        window.$('cropframe').setStyle('background-image', 'url("' + input_data.src + '")');
                                                        window.$('crop_div').setStyle('display', 'block');
                                      
                                                        if (img.get('width') < input_data.cover_width) {
                                                            var maxsize_width = img.get('width');
                                                        }
                                                        else {
                                                            var maxsize_width = input_data.cover_width;
                                                        }
                                                        if (img.get('height') < input_data.cover_height) {
                                                            var maxsize_height = img.get('height');
                                                        }
                                                        else {
                                                            var maxsize_height = input_data.cover_height;
                                                        }
                                                        window.ch = new CwCrop({
                                                                             onCrop: function(values) {
                                                                                 new Request.JSON({
                                                                                                     'url' : en4.core.baseUrl + 'timeline/ajax/crop',
                                                                                                     'data': {
                                                                                                                'isajax' : true,
                                                                                                                'original_src' : input_data.original_src,
                                                                                                                x : values.x,
                                                                                                                y : values.y,
                                                                                                                w : values.w,
                                                                                                                h : values.h
                                                                                                              },
                                                                                                     'onSuccess' : function(responseObject) {
                                                                                                        if( $type(responseObject)!="object" ) {
                                                                                                            alert('ERROR occurred. Please try againe.');
                                                                                                            return false;
                                                                                                        }
                                                                                                        if( !responseObject.status || responseObject.status !=true ) {
                                                                                                            if (responseObject.reload == true)
                                                                                                                window.location.reload(true);
                                                                                                            if( responseObject.error && $type(responseObject.error) == 'string' ) {
                                                                                                                alert(responseObject.error);
                                                                                                            }
                                                                                                            return false;
                                                                                                        }
                                                                                                        if (responseObject.status == true) {
                                                                                                            parent.$('timeline_cover').set('src', responseObject.src);
                                                                                                            parent.$('timeline_delete_cover').setStyle('display', '');
                                                                                                            window.$('crop_div').setStyle('display', 'none');
                                                                                                            window.$('button_cancel').setStyle('display', 'none');
                                                                                                            window.$('success_message_box').setStyle('display', 'block');
                                                                                                            setTimeout(function() {
                                                                                                                parent.Smoothbox.close();
                                                                                                            }, 1000 );
                                                                                                        }
                                                                                          
                                                                                                     }
                                                                                                  }).send();
                                                                             },
                                                                             fixedratio: input_data.cover_width/input_data.cover_height,
                                                                             maxsize: {x: maxsize_width, y: maxsize_height},
                                                                             initialmax : true,
                                                                             minsize: {x: input_data.width_min, y: input_data.height_min}
                                                                        });
                                                       parent.Smoothbox.instance.doAutoResize(); 
                                        }
                                  });
}


(function(){

this.ScrollLoader = new Class({

	Implements: [Options, Events],

	options: {
		/*onScroll: $empty,*/
		area: 50,
		mode: 'vertical',
		container: null
	},

	initialize: function(options){
		this.setOptions(options);
                if (options.area == null) {
                    this.options.area = this.getViewportSize().height+100;
                }
		this.bound = {scroll: this.scroll.bind(this)};
		this.container = document.id(this.options.container) || window;
		this.attach();
	},

	attach: function(){
		this.container.addEvent('scroll', this.bound.scroll);
		return this;
	},

	detach: function(){
		this.container.removeEvent('scroll', this.bound.scroll);
		return this;
	},

	scroll: function(){
		var z = this.options.mode == 'vertical' ? 'y' : 'x';

		var size = this.container.getSize()[z],
			scroll = this.container.getScroll()[z],
			scrollSize = this.container.getScrollSize()[z];
                
		if (scroll + size < scrollSize - this.options.area) return;

		this.fireEvent('scroll');
	},

        getViewportSize: function() {
            var size = {};

            if (typeof window.innerWidth != 'undefined') {
                size.width  = window.innerWidth,
                size.height = window.innerHeight
            }
            else if (typeof document.documentElement != 'undefined'
                && typeof document.documentElement.clientWidth !=
                'undefined' && document.documentElement.clientWidth != 0) {
                    size.width  = document.documentElement.clientWidth,
                    size.height = document.documentElement.clientHeight
            } else {
                size.width  = document.getElementsByTagName('body')[0].clientWidth,
                size.height = document.getElementsByTagName('body')[0].clientHeight
            }

            return size;
        }

});

})();

function set_icon_collection(tab, icon) {
    new Request.JSON({
      url : en4.core.baseUrl + 'admin/timeline/settings/set-icon-collection',
      method : 'post',
      data : {
        format : 'json',
        tab : tab,
        icon : icon
      },
      onSuccess : function(responseObject) {
        if( $type(responseObject) =="object" && responseObject.status) {
            $('tab_img_' + tab).set('src', responseObject.file_icon);
        }
      }
    }).send();
}

Smoothbox.Modal.TimeLineImage = new Class({

  Extends : Smoothbox.Modal,

  element : false,

  load : function()
  {
    if( this.content )
    {
      return;
    }

    this.parent();

    this.content = new Element('div', {
      id : 'TB_ajaxContent'
    });
    this.content.inject(this.window);
    var clone_element = this.element.clone();
    clone_element.setStyle('display', 'block').inject(this.content);
        
    this.hideLoading();
    this.showWindow();
    this.doAutoResize(clone_element);
    this.onLoad();
  },

  setOptions : function(options)
  {
    this.element = $(options.element);
    this.parent(options);
  }

});

TimeLineScroller = new Class({

    element:null,
    scroll_element:null,
    show_stickly:false,
    
    initialize:function(el, sc_el){
        if (typeOf(el) == 'element') {
            this.element = el;
        }
        else if (typeOf(el) == 'string') {
            this.element = $(el);
        }
        else return;
        if (typeOf(sc_el) == 'element') {
            this.scroll_element = sc_el;
        }
        else if (typeOf(sc_el) == 'string') {
            this.scroll_element = $(sc_el);
        }
        else return;
        window.addEvent('scroll', function(e) {
                                                this.check(e);
                                              }.bind(this));
        new Element('a', {'href' : 'javascript:void(0);',
                          //'text' : en4.core.language.translate('To Top'),
						  'class' : 'back-to-top',
                          'events': {
                                      'click': function() { 
                                                            new Fx.Scroll(window).toTop();
                                        }
                                    }
                          }).inject(this.scroll_element);
    },
    check: function(e) {
        var h_start = this.element.getSize().y + this.element.getPosition().y;
                
	if (h_start < window.getScroll().y && !this.show_stickly)  {
            this.show_stickly = true;
            this.showBox();
            return;
        }
        if (h_start > window.getScroll().y && this.show_stickly) {
            this.show_stickly = false;
            this.hideBox();
            return;
        }
    },
    showBox: function() {
        this.scroll_element.setStyle('display', '');
    },
    hideBox: function() {
        this.scroll_element.setStyle('display', 'none');
    }
});