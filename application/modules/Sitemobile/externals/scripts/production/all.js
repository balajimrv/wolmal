/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: all.js 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

/* JS Name 
 *   tagger.js start here
 *   Use for Tag the JS
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
					$.mobile.hidePageLoadingMsg();
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

};
//---------------------------------------------------------------------------------
/* JS Name 
 *   iscroll.js start here
 */

/*! iScroll v5.1.1 ~ (c) 2008-2014 Matteo Spinelli ~ http://cubiq.org/license */
(function (window, document, Math) {
var rAF = window.requestAnimationFrame	||
	window.webkitRequestAnimationFrame	||
	window.mozRequestAnimationFrame		||
	window.oRequestAnimationFrame		||
	window.msRequestAnimationFrame		||
	function (callback) { window.setTimeout(callback, 1000 / 60); };

var utils = (function () {
	var me = {};

	var _elementStyle = document.createElement('div').style;
	var _vendor = (function () {
		var vendors = ['t', 'webkitT', 'MozT', 'msT', 'OT'],
			transform,
			i = 0,
			l = vendors.length;

		for ( ; i < l; i++ ) {
			transform = vendors[i] + 'ransform';
			if ( transform in _elementStyle ) return vendors[i].substr(0, vendors[i].length-1);
		}

		return false;
	})();

	function _prefixStyle (style) {
		if ( _vendor === false ) return false;
		if ( _vendor === '' ) return style;
		return _vendor + style.charAt(0).toUpperCase() + style.substr(1);
	}

	me.getTime = Date.now || function getTime () { return new Date().getTime(); };

	me.extend = function (target, obj) {
		for ( var i in obj ) {
			target[i] = obj[i];
		}
	};

	me.addEvent = function (el, type, fn, capture) {
		el.addEventListener(type, fn, !!capture);
	};

	me.removeEvent = function (el, type, fn, capture) {
		el.removeEventListener(type, fn, !!capture);
	};

	me.momentum = function (current, start, time, lowerMargin, wrapperSize, deceleration) {
		var distance = current - start,
			speed = Math.abs(distance) / time,
			destination,
			duration;

		deceleration = deceleration === undefined ? 0.0006 : deceleration;

		destination = current + ( speed * speed ) / ( 2 * deceleration ) * ( distance < 0 ? -1 : 1 );
		duration = speed / deceleration;

		if ( destination < lowerMargin ) {
			destination = wrapperSize ? lowerMargin - ( wrapperSize / 2.5 * ( speed / 8 ) ) : lowerMargin;
			distance = Math.abs(destination - current);
			duration = distance / speed;
		} else if ( destination > 0 ) {
			destination = wrapperSize ? wrapperSize / 2.5 * ( speed / 8 ) : 0;
			distance = Math.abs(current) + destination;
			duration = distance / speed;
		}

		return {
			destination: Math.round(destination),
			duration: duration
		};
	};

	var _transform = _prefixStyle('transform');

	me.extend(me, {
		hasTransform: _transform !== false,
		hasPerspective: _prefixStyle('perspective') in _elementStyle,
		hasTouch: 'ontouchstart' in window,
		hasPointer: navigator.msPointerEnabled,
		hasTransition: _prefixStyle('transition') in _elementStyle
	});

	// This should find all Android browsers lower than build 535.19 (both stock browser and webview)
	me.isBadAndroid = /Android /.test(window.navigator.appVersion) && !(/Chrome\/\d/.test(window.navigator.appVersion));

	me.extend(me.style = {}, {
		transform: _transform,
		transitionTimingFunction: _prefixStyle('transitionTimingFunction'),
		transitionDuration: _prefixStyle('transitionDuration'),
		transitionDelay: _prefixStyle('transitionDelay'),
		transformOrigin: _prefixStyle('transformOrigin')
	});

	me.hasClass = function (e, c) {
		var re = new RegExp("(^|\\s)" + c + "(\\s|$)");
		return re.test(e.className);
	};

	me.addClass = function (e, c) {
		if ( me.hasClass(e, c) ) {
			return;
		}

		var newclass = e.className.split(' ');
		newclass.push(c);
		e.className = newclass.join(' ');
	};

	me.removeClass = function (e, c) {
		if ( !me.hasClass(e, c) ) {
			return;
		}

		var re = new RegExp("(^|\\s)" + c + "(\\s|$)", 'g');
		e.className = e.className.replace(re, ' ');
	};

	me.offset = function (el) {
		var left = -el.offsetLeft,
			top = -el.offsetTop;

		// jshint -W084
		while (el = el.offsetParent) {
			left -= el.offsetLeft;
			top -= el.offsetTop;
		}
		// jshint +W084

		return {
			left: left,
			top: top
		};
	};

	me.preventDefaultException = function (el, exceptions) {
		for ( var i in exceptions ) {
			if ( exceptions[i].test(el[i]) ) {
				return true;
			}
		}

		return false;
	};

	me.extend(me.eventType = {}, {
		touchstart: 1,
		touchmove: 1,
		touchend: 1,

		mousedown: 2,
		mousemove: 2,
		mouseup: 2,

		MSPointerDown: 3,
		MSPointerMove: 3,
		MSPointerUp: 3
	});

	me.extend(me.ease = {}, {
		quadratic: {
			style: 'cubic-bezier(0.25, 0.46, 0.45, 0.94)',
			fn: function (k) {
				return k * ( 2 - k );
			}
		},
		circular: {
			style: 'cubic-bezier(0.1, 0.57, 0.1, 1)',	// Not properly "circular" but this looks better, it should be (0.075, 0.82, 0.165, 1)
			fn: function (k) {
				return Math.sqrt( 1 - ( --k * k ) );
			}
		},
		back: {
			style: 'cubic-bezier(0.175, 0.885, 0.32, 1.275)',
			fn: function (k) {
				var b = 4;
				return ( k = k - 1 ) * k * ( ( b + 1 ) * k + b ) + 1;
			}
		},
		bounce: {
			style: '',
			fn: function (k) {
				if ( ( k /= 1 ) < ( 1 / 2.75 ) ) {
					return 7.5625 * k * k;
				} else if ( k < ( 2 / 2.75 ) ) {
					return 7.5625 * ( k -= ( 1.5 / 2.75 ) ) * k + 0.75;
				} else if ( k < ( 2.5 / 2.75 ) ) {
					return 7.5625 * ( k -= ( 2.25 / 2.75 ) ) * k + 0.9375;
				} else {
					return 7.5625 * ( k -= ( 2.625 / 2.75 ) ) * k + 0.984375;
				}
			}
		},
		elastic: {
			style: '',
			fn: function (k) {
				var f = 0.22,
					e = 0.4;

				if ( k === 0 ) { return 0; }
				if ( k == 1 ) { return 1; }

				return ( e * Math.pow( 2, - 10 * k ) * Math.sin( ( k - f / 4 ) * ( 2 * Math.PI ) / f ) + 1 );
			}
		}
	});

	me.tap = function (e, eventName) {
		var ev = document.createEvent('Event');
		ev.initEvent(eventName, true, true);
		ev.pageX = e.pageX;
		ev.pageY = e.pageY;
		e.target.dispatchEvent(ev);
	};

	me.click = function (e) {
		var target = e.target,
			ev;

		if ( !(/(SELECT|INPUT|TEXTAREA)/i).test(target.tagName) ) {
			ev = document.createEvent('MouseEvents');
			ev.initMouseEvent('click', true, true, e.view, 1,
				target.screenX, target.screenY, target.clientX, target.clientY,
				e.ctrlKey, e.altKey, e.shiftKey, e.metaKey,
				0, null);

			ev._constructed = true;
			target.dispatchEvent(ev);
		}
	};

	return me;
})();

function IScroll (el, options) {
	this.wrapper = typeof el == 'string' ? document.querySelector(el) : el;
	this.scroller = this.wrapper.children[0];
	this.scrollerStyle = this.scroller.style;		// cache style for better performance

	this.options = {

		resizeScrollbars: true,

		mouseWheelSpeed: 20,

		snapThreshold: 0.334,

// INSERT POINT: OPTIONS 

		startX: 0,
		startY: 0,
		scrollY: true,
		directionLockThreshold: 5,
		momentum: true,

		bounce: true,
		bounceTime: 600,
		bounceEasing: '',

		preventDefault: true,
		preventDefaultException: { tagName: /^(INPUT|TEXTAREA|BUTTON|SELECT)$/ },

		HWCompositing: true,
		useTransition: true,
		useTransform: true
	};

	for ( var i in options ) {
		this.options[i] = options[i];
	}

	// Normalize options
	this.translateZ = this.options.HWCompositing && utils.hasPerspective ? ' translateZ(0)' : '';

	this.options.useTransition = utils.hasTransition && this.options.useTransition;
	this.options.useTransform = utils.hasTransform && this.options.useTransform;

	this.options.eventPassthrough = this.options.eventPassthrough === true ? 'vertical' : this.options.eventPassthrough;
	this.options.preventDefault = !this.options.eventPassthrough && this.options.preventDefault;

	// If you want eventPassthrough I have to lock one of the axes
	this.options.scrollY = this.options.eventPassthrough == 'vertical' ? false : this.options.scrollY;
	this.options.scrollX = this.options.eventPassthrough == 'horizontal' ? false : this.options.scrollX;

	// With eventPassthrough we also need lockDirection mechanism
	this.options.freeScroll = this.options.freeScroll && !this.options.eventPassthrough;
	this.options.directionLockThreshold = this.options.eventPassthrough ? 0 : this.options.directionLockThreshold;

	this.options.bounceEasing = typeof this.options.bounceEasing == 'string' ? utils.ease[this.options.bounceEasing] || utils.ease.circular : this.options.bounceEasing;

	this.options.resizePolling = this.options.resizePolling === undefined ? 60 : this.options.resizePolling;

	if ( this.options.tap === true ) {
		this.options.tap = 'tap';
	}

	if ( this.options.shrinkScrollbars == 'scale' ) {
		this.options.useTransition = false;
	}

	this.options.invertWheelDirection = this.options.invertWheelDirection ? -1 : 1;

	if ( this.options.probeType == 3 ) {
		this.options.useTransition = false;	}

// INSERT POINT: NORMALIZATION

	// Some defaults	
	this.x = 0;
	this.y = 0;
	this.directionX = 0;
	this.directionY = 0;
	this._events = {};

// INSERT POINT: DEFAULTS

	this._init();
	this.refresh();

	this.scrollTo(this.options.startX, this.options.startY);
	this.enable();
}

IScroll.prototype = {
	version: '5.1.1',

	_init: function () {
		this._initEvents();

		if ( this.options.scrollbars || this.options.indicators ) {
			this._initIndicators();
		}

		if ( this.options.mouseWheel ) {
			this._initWheel();
		}

		if ( this.options.snap ) {
			this._initSnap();
		}

		if ( this.options.keyBindings ) {
			this._initKeys();
		}

// INSERT POINT: _init

	},

	destroy: function () {
		this._initEvents(true);

		this._execEvent('destroy');
	},

	_transitionEnd: function (e) {
		if ( e.target != this.scroller || !this.isInTransition ) {
			return;
		}

		this._transitionTime();
		if ( !this.resetPosition(this.options.bounceTime) ) {
			this.isInTransition = false;
			this._execEvent('scrollEnd');
		}
	},

	_start: function (e) {
		// React to left mouse button only
		if ( utils.eventType[e.type] != 1 ) {
			if ( e.button !== 0 ) {
				return;
			}
		}

		if ( !this.enabled || (this.initiated && utils.eventType[e.type] !== this.initiated) ) {
			return;
		}

		if ( this.options.preventDefault && !utils.isBadAndroid && !utils.preventDefaultException(e.target, this.options.preventDefaultException) ) {
			e.preventDefault();
		}

		var point = e.touches ? e.touches[0] : e,
			pos;

		this.initiated	= utils.eventType[e.type];
		this.moved		= false;
		this.distX		= 0;
		this.distY		= 0;
		this.directionX = 0;
		this.directionY = 0;
		this.directionLocked = 0;

		this._transitionTime();

		this.startTime = utils.getTime();

		if ( this.options.useTransition && this.isInTransition ) {
			this.isInTransition = false;
			pos = this.getComputedPosition();
			this._translate(Math.round(pos.x), Math.round(pos.y));
			this._execEvent('scrollEnd');
		} else if ( !this.options.useTransition && this.isAnimating ) {
			this.isAnimating = false;
			this._execEvent('scrollEnd');
		}

		this.startX    = this.x;
		this.startY    = this.y;
		this.absStartX = this.x;
		this.absStartY = this.y;
		this.pointX    = point.pageX;
		this.pointY    = point.pageY;

		this._execEvent('beforeScrollStart');
	},

	_move: function (e) {
		if ( !this.enabled || utils.eventType[e.type] !== this.initiated ) {
			return;
		}

		if ( this.options.preventDefault ) {	// increases performance on Android? TODO: check!
			e.preventDefault();
		}

		var point		= e.touches ? e.touches[0] : e,
			deltaX		= point.pageX - this.pointX,
			deltaY		= point.pageY - this.pointY,
			timestamp	= utils.getTime(),
			newX, newY,
			absDistX, absDistY;

		this.pointX		= point.pageX;
		this.pointY		= point.pageY;

		this.distX		+= deltaX;
		this.distY		+= deltaY;
		absDistX		= Math.abs(this.distX);
		absDistY		= Math.abs(this.distY);

		// We need to move at least 10 pixels for the scrolling to initiate
		if ( timestamp - this.endTime > 300 && (absDistX < 10 && absDistY < 10) ) {
			return;
		}

		// If you are scrolling in one direction lock the other
		if ( !this.directionLocked && !this.options.freeScroll ) {
			if ( absDistX > absDistY + this.options.directionLockThreshold ) {
				this.directionLocked = 'h';		// lock horizontally
			} else if ( absDistY >= absDistX + this.options.directionLockThreshold ) {
				this.directionLocked = 'v';		// lock vertically
			} else {
				this.directionLocked = 'n';		// no lock
			}
		}

		if ( this.directionLocked == 'h' ) {
			if ( this.options.eventPassthrough == 'vertical' ) {
				e.preventDefault();
			} else if ( this.options.eventPassthrough == 'horizontal' ) {
				this.initiated = false;
				return;
			}

			deltaY = 0;
		} else if ( this.directionLocked == 'v' ) {
			if ( this.options.eventPassthrough == 'horizontal' ) {
				e.preventDefault();
			} else if ( this.options.eventPassthrough == 'vertical' ) {
				this.initiated = false;
				return;
			}

			deltaX = 0;
		}

		deltaX = this.hasHorizontalScroll ? deltaX : 0;
		deltaY = this.hasVerticalScroll ? deltaY : 0;

		newX = this.x + deltaX;
		newY = this.y + deltaY;

		// Slow down if outside of the boundaries
		if ( newX > 0 || newX < this.maxScrollX ) {
			newX = this.options.bounce ? this.x + deltaX / 3 : newX > 0 ? 0 : this.maxScrollX;
		}
		if ( newY > 0 || newY < this.maxScrollY ) {
			newY = this.options.bounce ? this.y + deltaY / 3 : newY > 0 ? 0 : this.maxScrollY;
		}

		this.directionX = deltaX > 0 ? -1 : deltaX < 0 ? 1 : 0;
		this.directionY = deltaY > 0 ? -1 : deltaY < 0 ? 1 : 0;

		if ( !this.moved ) {
			this._execEvent('scrollStart');
		}

		this.moved = true;

		this._translate(newX, newY);

/* REPLACE START: _move */
		if ( timestamp - this.startTime > 300 ) {
			this.startTime = timestamp;
			this.startX = this.x;
			this.startY = this.y;

			if ( this.options.probeType == 1 ) {
				this._execEvent('scroll');
			}
		}

		if ( this.options.probeType > 1 ) {
			this._execEvent('scroll');
		}
/* REPLACE END: _move */

	},

	_end: function (e) {
		if ( !this.enabled || utils.eventType[e.type] !== this.initiated ) {
			return;
		}

		if ( this.options.preventDefault && !utils.preventDefaultException(e.target, this.options.preventDefaultException) ) {
			e.preventDefault();
		}

		var point = e.changedTouches ? e.changedTouches[0] : e,
			momentumX,
			momentumY,
			duration = utils.getTime() - this.startTime,
			newX = Math.round(this.x),
			newY = Math.round(this.y),
			distanceX = Math.abs(newX - this.startX),
			distanceY = Math.abs(newY - this.startY),
			time = 0,
			easing = '';

		this.isInTransition = 0;
		this.initiated = 0;
		this.endTime = utils.getTime();

		// reset if we are outside of the boundaries
		if ( this.resetPosition(this.options.bounceTime) ) {
			return;
		}

		this.scrollTo(newX, newY);	// ensures that the last position is rounded

		// we scrolled less than 10 pixels
		if ( !this.moved ) {
			if ( this.options.tap ) {
				utils.tap(e, this.options.tap);
			}

			if ( this.options.click ) {
				utils.click(e);
			}

			this._execEvent('scrollCancel');
			return;
		}

		if ( this._events.flick && duration < 200 && distanceX < 100 && distanceY < 100 ) {
			this._execEvent('flick');
			return;
		}

		// start momentum animation if needed
		if ( this.options.momentum && duration < 300 ) {
			momentumX = this.hasHorizontalScroll ? utils.momentum(this.x, this.startX, duration, this.maxScrollX, this.options.bounce ? this.wrapperWidth : 0, this.options.deceleration) : { destination: newX, duration: 0 };
			momentumY = this.hasVerticalScroll ? utils.momentum(this.y, this.startY, duration, this.maxScrollY, this.options.bounce ? this.wrapperHeight : 0, this.options.deceleration) : { destination: newY, duration: 0 };
			newX = momentumX.destination;
			newY = momentumY.destination;
			time = Math.max(momentumX.duration, momentumY.duration);
			this.isInTransition = 1;
		}


		if ( this.options.snap ) {
			var snap = this._nearestSnap(newX, newY);
			this.currentPage = snap;
			time = this.options.snapSpeed || Math.max(
					Math.max(
						Math.min(Math.abs(newX - snap.x), 1000),
						Math.min(Math.abs(newY - snap.y), 1000)
					), 300);
			newX = snap.x;
			newY = snap.y;

			this.directionX = 0;
			this.directionY = 0;
			easing = this.options.bounceEasing;
		}

// INSERT POINT: _end

		if ( newX != this.x || newY != this.y ) {
			// change easing function when scroller goes out of the boundaries
			if ( newX > 0 || newX < this.maxScrollX || newY > 0 || newY < this.maxScrollY ) {
				easing = utils.ease.quadratic;
			}

			this.scrollTo(newX, newY, time, easing);
			return;
		}

		this._execEvent('scrollEnd');
	},

	_resize: function () {
		var that = this;

		clearTimeout(this.resizeTimeout);

		this.resizeTimeout = setTimeout(function () {
			that.refresh();
		}, this.options.resizePolling);
	},

	resetPosition: function (time) {
		var x = this.x,
			y = this.y;

		time = time || 0;

		if ( !this.hasHorizontalScroll || this.x > 0 ) {
			x = 0;
		} else if ( this.x < this.maxScrollX ) {
			x = this.maxScrollX;
		}

		if ( !this.hasVerticalScroll || this.y > 0 ) {
			y = 0;
		} else if ( this.y < this.maxScrollY ) {
			y = this.maxScrollY;
		}

		if ( x == this.x && y == this.y ) {
			return false;
		}

		this.scrollTo(x, y, time, this.options.bounceEasing);

		return true;
	},

	disable: function () {
		this.enabled = false;
	},

	enable: function () {
		this.enabled = true;
	},

	refresh: function () {
		var rf = this.wrapper.offsetHeight;		// Force reflow

		this.wrapperWidth	= this.wrapper.clientWidth;
		this.wrapperHeight	= this.wrapper.clientHeight;

/* REPLACE START: refresh */

		this.scrollerWidth	= this.scroller.offsetWidth;
		this.scrollerHeight	= this.scroller.offsetHeight;

		this.maxScrollX		= this.wrapperWidth - this.scrollerWidth;
		this.maxScrollY		= this.wrapperHeight - this.scrollerHeight;

/* REPLACE END: refresh */

		this.hasHorizontalScroll	= this.options.scrollX && this.maxScrollX < 0;
		this.hasVerticalScroll		= this.options.scrollY && this.maxScrollY < 0;

		if ( !this.hasHorizontalScroll ) {
			this.maxScrollX = 0;
			this.scrollerWidth = this.wrapperWidth;
		}

		if ( !this.hasVerticalScroll ) {
			this.maxScrollY = 0;
			this.scrollerHeight = this.wrapperHeight;
		}

		this.endTime = 0;
		this.directionX = 0;
		this.directionY = 0;

		this.wrapperOffset = utils.offset(this.wrapper);

		this._execEvent('refresh');

		this.resetPosition();

// INSERT POINT: _refresh

	},

	on: function (type, fn) {
		if ( !this._events[type] ) {
			this._events[type] = [];
		}

		this._events[type].push(fn);
	},

	off: function (type, fn) {
		if ( !this._events[type] ) {
			return;
		}

		var index = this._events[type].indexOf(fn);

		if ( index > -1 ) {
			this._events[type].splice(index, 1);
		}
	},

	_execEvent: function (type) {
		if ( !this._events[type] ) {
			return;
		}

		var i = 0,
			l = this._events[type].length;

		if ( !l ) {
			return;
		}

		for ( ; i < l; i++ ) {
			this._events[type][i].apply(this, [].slice.call(arguments, 1));
		}
	},

	scrollBy: function (x, y, time, easing) {
		x = this.x + x;
		y = this.y + y;
		time = time || 0;

		this.scrollTo(x, y, time, easing);
	},

	scrollTo: function (x, y, time, easing) {
		easing = easing || utils.ease.circular;

		this.isInTransition = this.options.useTransition && time > 0;

		if ( !time || (this.options.useTransition && easing.style) ) {
			this._transitionTimingFunction(easing.style);
			this._transitionTime(time);
			this._translate(x, y);
		} else {
			this._animate(x, y, time, easing.fn);
		}
	},

	scrollToElement: function (el, time, offsetX, offsetY, easing) {
		el = el.nodeType ? el : this.scroller.querySelector(el);

		if ( !el ) {
			return;
		}

		var pos = utils.offset(el);

		pos.left -= this.wrapperOffset.left;
		pos.top  -= this.wrapperOffset.top;

		// if offsetX/Y are true we center the element to the screen
		if ( offsetX === true ) {
			offsetX = Math.round(el.offsetWidth / 2 - this.wrapper.offsetWidth / 2);
		}
		if ( offsetY === true ) {
			offsetY = Math.round(el.offsetHeight / 2 - this.wrapper.offsetHeight / 2);
		}

		pos.left -= offsetX || 0;
		pos.top  -= offsetY || 0;

		pos.left = pos.left > 0 ? 0 : pos.left < this.maxScrollX ? this.maxScrollX : pos.left;
		pos.top  = pos.top  > 0 ? 0 : pos.top  < this.maxScrollY ? this.maxScrollY : pos.top;

		time = time === undefined || time === null || time === 'auto' ? Math.max(Math.abs(this.x-pos.left), Math.abs(this.y-pos.top)) : time;

		this.scrollTo(pos.left, pos.top, time, easing);
	},

	_transitionTime: function (time) {
		time = time || 0;

		this.scrollerStyle[utils.style.transitionDuration] = time + 'ms';

		if ( !time && utils.isBadAndroid ) {
			this.scrollerStyle[utils.style.transitionDuration] = '0.001s';
		}


		if ( this.indicators ) {
			for ( var i = this.indicators.length; i--; ) {
				this.indicators[i].transitionTime(time);
			}
		}


// INSERT POINT: _transitionTime

	},

	_transitionTimingFunction: function (easing) {
		this.scrollerStyle[utils.style.transitionTimingFunction] = easing;


		if ( this.indicators ) {
			for ( var i = this.indicators.length; i--; ) {
				this.indicators[i].transitionTimingFunction(easing);
			}
		}


// INSERT POINT: _transitionTimingFunction

	},

	_translate: function (x, y) {
		if ( this.options.useTransform ) {

/* REPLACE START: _translate */

			this.scrollerStyle[utils.style.transform] = 'translate(' + x + 'px,' + y + 'px)' + this.translateZ;

/* REPLACE END: _translate */

		} else {
			x = Math.round(x);
			y = Math.round(y);
			this.scrollerStyle.left = x + 'px';
			this.scrollerStyle.top = y + 'px';
		}

		this.x = x;
		this.y = y;


	if ( this.indicators ) {
		for ( var i = this.indicators.length; i--; ) {
			this.indicators[i].updatePosition();
		}
	}


// INSERT POINT: _translate

	},

	_initEvents: function (remove) {
		var eventType = remove ? utils.removeEvent : utils.addEvent,
			target = this.options.bindToWrapper ? this.wrapper : window;

		eventType(window, 'orientationchange', this);
		eventType(window, 'resize', this);

		if ( this.options.click ) {
			eventType(this.wrapper, 'click', this, true);
		}

		if ( !this.options.disableMouse ) {
			eventType(this.wrapper, 'mousedown', this);
			eventType(target, 'mousemove', this);
			eventType(target, 'mousecancel', this);
			eventType(target, 'mouseup', this);
		}

		if ( utils.hasPointer && !this.options.disablePointer ) {
			eventType(this.wrapper, 'MSPointerDown', this);
			eventType(target, 'MSPointerMove', this);
			eventType(target, 'MSPointerCancel', this);
			eventType(target, 'MSPointerUp', this);
		}

		if ( utils.hasTouch && !this.options.disableTouch ) {
			eventType(this.wrapper, 'touchstart', this);
			eventType(target, 'touchmove', this);
			eventType(target, 'touchcancel', this);
			eventType(target, 'touchend', this);
		}

		eventType(this.scroller, 'transitionend', this);
		eventType(this.scroller, 'webkitTransitionEnd', this);
		eventType(this.scroller, 'oTransitionEnd', this);
		eventType(this.scroller, 'MSTransitionEnd', this);
	},

	getComputedPosition: function () {
		var matrix = window.getComputedStyle(this.scroller, null),
			x, y;

		if ( this.options.useTransform ) {
			matrix = matrix[utils.style.transform].split(')')[0].split(', ');
			x = +(matrix[12] || matrix[4]);
			y = +(matrix[13] || matrix[5]);
		} else {
			x = +matrix.left.replace(/[^-\d.]/g, '');
			y = +matrix.top.replace(/[^-\d.]/g, '');
		}

		return { x: x, y: y };
	},

	_initIndicators: function () {
		var interactive = this.options.interactiveScrollbars,
			customStyle = typeof this.options.scrollbars != 'string',
			indicators = [],
			indicator;

		var that = this;

		this.indicators = [];

		if ( this.options.scrollbars ) {
			// Vertical scrollbar
			if ( this.options.scrollY ) {
				indicator = {
					el: createDefaultScrollbar('v', interactive, this.options.scrollbars),
					interactive: interactive,
					defaultScrollbars: true,
					customStyle: customStyle,
					resize: this.options.resizeScrollbars,
					shrink: this.options.shrinkScrollbars,
					fade: this.options.fadeScrollbars,
					listenX: false
				};

				this.wrapper.appendChild(indicator.el);
				indicators.push(indicator);
			}

			// Horizontal scrollbar
			if ( this.options.scrollX ) {
				indicator = {
					el: createDefaultScrollbar('h', interactive, this.options.scrollbars),
					interactive: interactive,
					defaultScrollbars: true,
					customStyle: customStyle,
					resize: this.options.resizeScrollbars,
					shrink: this.options.shrinkScrollbars,
					fade: this.options.fadeScrollbars,
					listenY: false
				};

				this.wrapper.appendChild(indicator.el);
				indicators.push(indicator);
			}
		}

		if ( this.options.indicators ) {
			// TODO: check concat compatibility
			indicators = indicators.concat(this.options.indicators);
		}

		for ( var i = indicators.length; i--; ) {
			this.indicators.push( new Indicator(this, indicators[i]) );
		}

		// TODO: check if we can use array.map (wide compatibility and performance issues)
		function _indicatorsMap (fn) {
			for ( var i = that.indicators.length; i--; ) {
				fn.call(that.indicators[i]);
			}
		}

		if ( this.options.fadeScrollbars ) {
			this.on('scrollEnd', function () {
				_indicatorsMap(function () {
					this.fade();
				});
			});

			this.on('scrollCancel', function () {
				_indicatorsMap(function () {
					this.fade();
				});
			});

			this.on('scrollStart', function () {
				_indicatorsMap(function () {
					this.fade(1);
				});
			});

			this.on('beforeScrollStart', function () {
				_indicatorsMap(function () {
					this.fade(1, true);
				});
			});
		}


		this.on('refresh', function () {
			_indicatorsMap(function () {
				this.refresh();
			});
		});

		this.on('destroy', function () {
			_indicatorsMap(function () {
				this.destroy();
			});

			delete this.indicators;
		});
	},

	_initWheel: function () {
		utils.addEvent(this.wrapper, 'wheel', this);
		utils.addEvent(this.wrapper, 'mousewheel', this);
		utils.addEvent(this.wrapper, 'DOMMouseScroll', this);

		this.on('destroy', function () {
			utils.removeEvent(this.wrapper, 'wheel', this);
			utils.removeEvent(this.wrapper, 'mousewheel', this);
			utils.removeEvent(this.wrapper, 'DOMMouseScroll', this);
		});
	},

	_wheel: function (e) {
		if ( !this.enabled ) {
			return;
		}

		e.preventDefault();
		e.stopPropagation();

		var wheelDeltaX, wheelDeltaY,
			newX, newY,
			that = this;

		if ( this.wheelTimeout === undefined ) {
			that._execEvent('scrollStart');
		}

		// Execute the scrollEnd event after 400ms the wheel stopped scrolling
		clearTimeout(this.wheelTimeout);
		this.wheelTimeout = setTimeout(function () {
			that._execEvent('scrollEnd');
			that.wheelTimeout = undefined;
		}, 400);

		if ( 'deltaX' in e ) {
			wheelDeltaX = -e.deltaX;
			wheelDeltaY = -e.deltaY;
		} else if ( 'wheelDeltaX' in e ) {
			wheelDeltaX = e.wheelDeltaX / 120 * this.options.mouseWheelSpeed;
			wheelDeltaY = e.wheelDeltaY / 120 * this.options.mouseWheelSpeed;
		} else if ( 'wheelDelta' in e ) {
			wheelDeltaX = wheelDeltaY = e.wheelDelta / 120 * this.options.mouseWheelSpeed;
		} else if ( 'detail' in e ) {
			wheelDeltaX = wheelDeltaY = -e.detail / 3 * this.options.mouseWheelSpeed;
		} else {
			return;
		}

		wheelDeltaX *= this.options.invertWheelDirection;
		wheelDeltaY *= this.options.invertWheelDirection;

		if ( !this.hasVerticalScroll ) {
			wheelDeltaX = wheelDeltaY;
			wheelDeltaY = 0;
		}

		if ( this.options.snap ) {
			newX = this.currentPage.pageX;
			newY = this.currentPage.pageY;

			if ( wheelDeltaX > 0 ) {
				newX--;
			} else if ( wheelDeltaX < 0 ) {
				newX++;
			}

			if ( wheelDeltaY > 0 ) {
				newY--;
			} else if ( wheelDeltaY < 0 ) {
				newY++;
			}

			this.goToPage(newX, newY);

			return;
		}

		newX = this.x + Math.round(this.hasHorizontalScroll ? wheelDeltaX : 0);
		newY = this.y + Math.round(this.hasVerticalScroll ? wheelDeltaY : 0);

		if ( newX > 0 ) {
			newX = 0;
		} else if ( newX < this.maxScrollX ) {
			newX = this.maxScrollX;
		}

		if ( newY > 0 ) {
			newY = 0;
		} else if ( newY < this.maxScrollY ) {
			newY = this.maxScrollY;
		}

		this.scrollTo(newX, newY, 0);

		if ( this.options.probeType > 1 ) {
			this._execEvent('scroll');
		}

// INSERT POINT: _wheel
	},

	_initSnap: function () {
		this.currentPage = {};

		if ( typeof this.options.snap == 'string' ) {
			this.options.snap = this.scroller.querySelectorAll(this.options.snap);
		}

		this.on('refresh', function () {
			var i = 0, l,
				m = 0, n,
				cx, cy,
				x = 0, y,
				stepX = this.options.snapStepX || this.wrapperWidth,
				stepY = this.options.snapStepY || this.wrapperHeight,
				el;

			this.pages = [];

			if ( !this.wrapperWidth || !this.wrapperHeight || !this.scrollerWidth || !this.scrollerHeight ) {
				return;
			}

			if ( this.options.snap === true ) {
				cx = Math.round( stepX / 2 );
				cy = Math.round( stepY / 2 );

				while ( x > -this.scrollerWidth ) {
					this.pages[i] = [];
					l = 0;
					y = 0;

					while ( y > -this.scrollerHeight ) {
						this.pages[i][l] = {
							x: Math.max(x, this.maxScrollX),
							y: Math.max(y, this.maxScrollY),
							width: stepX,
							height: stepY,
							cx: x - cx,
							cy: y - cy
						};

						y -= stepY;
						l++;
					}

					x -= stepX;
					i++;
				}
			} else {
				el = this.options.snap;
				l = el.length;
				n = -1;

				for ( ; i < l; i++ ) {
					if ( i === 0 || el[i].offsetLeft <= el[i-1].offsetLeft ) {
						m = 0;
						n++;
					}

					if ( !this.pages[m] ) {
						this.pages[m] = [];
					}

					x = Math.max(-el[i].offsetLeft, this.maxScrollX);
					y = Math.max(-el[i].offsetTop, this.maxScrollY);
					cx = x - Math.round(el[i].offsetWidth / 2);
					cy = y - Math.round(el[i].offsetHeight / 2);

					this.pages[m][n] = {
						x: x,
						y: y,
						width: el[i].offsetWidth,
						height: el[i].offsetHeight,
						cx: cx,
						cy: cy
					};

					if ( x > this.maxScrollX ) {
						m++;
					}
				}
			}

			this.goToPage(this.currentPage.pageX || 0, this.currentPage.pageY || 0, 0);

			// Update snap threshold if needed
			if ( this.options.snapThreshold % 1 === 0 ) {
				this.snapThresholdX = this.options.snapThreshold;
				this.snapThresholdY = this.options.snapThreshold;
			} else {
				this.snapThresholdX = Math.round(this.pages[this.currentPage.pageX][this.currentPage.pageY].width * this.options.snapThreshold);
				this.snapThresholdY = Math.round(this.pages[this.currentPage.pageX][this.currentPage.pageY].height * this.options.snapThreshold);
			}
		});

		this.on('flick', function () {
			var time = this.options.snapSpeed || Math.max(
					Math.max(
						Math.min(Math.abs(this.x - this.startX), 1000),
						Math.min(Math.abs(this.y - this.startY), 1000)
					), 300);

			this.goToPage(
				this.currentPage.pageX + this.directionX,
				this.currentPage.pageY + this.directionY,
				time
			);
		});
	},

	_nearestSnap: function (x, y) {
		if ( !this.pages.length ) {
			return { x: 0, y: 0, pageX: 0, pageY: 0 };
		}

		var i = 0,
			l = this.pages.length,
			m = 0;

		// Check if we exceeded the snap threshold
		if ( Math.abs(x - this.absStartX) < this.snapThresholdX &&
			Math.abs(y - this.absStartY) < this.snapThresholdY ) {
			return this.currentPage;
		}

		if ( x > 0 ) {
			x = 0;
		} else if ( x < this.maxScrollX ) {
			x = this.maxScrollX;
		}

		if ( y > 0 ) {
			y = 0;
		} else if ( y < this.maxScrollY ) {
			y = this.maxScrollY;
		}

		for ( ; i < l; i++ ) {
			if ( x >= this.pages[i][0].cx ) {
				x = this.pages[i][0].x;
				break;
			}
		}

		l = this.pages[i].length;

		for ( ; m < l; m++ ) {
			if ( y >= this.pages[0][m].cy ) {
				y = this.pages[0][m].y;
				break;
			}
		}

		if ( i == this.currentPage.pageX ) {
			i += this.directionX;

			if ( i < 0 ) {
				i = 0;
			} else if ( i >= this.pages.length ) {
				i = this.pages.length - 1;
			}

			x = this.pages[i][0].x;
		}

		if ( m == this.currentPage.pageY ) {
			m += this.directionY;

			if ( m < 0 ) {
				m = 0;
			} else if ( m >= this.pages[0].length ) {
				m = this.pages[0].length - 1;
			}

			y = this.pages[0][m].y;
		}

		return {
			x: x,
			y: y,
			pageX: i,
			pageY: m
		};
	},

	goToPage: function (x, y, time, easing) {
		easing = easing || this.options.bounceEasing;

		if ( x >= this.pages.length ) {
			x = this.pages.length - 1;
		} else if ( x < 0 ) {
			x = 0;
		}

		if ( y >= this.pages[x].length ) {
			y = this.pages[x].length - 1;
		} else if ( y < 0 ) {
			y = 0;
		}

		var posX = this.pages[x][y].x,
			posY = this.pages[x][y].y;

		time = time === undefined ? this.options.snapSpeed || Math.max(
			Math.max(
				Math.min(Math.abs(posX - this.x), 1000),
				Math.min(Math.abs(posY - this.y), 1000)
			), 300) : time;

		this.currentPage = {
			x: posX,
			y: posY,
			pageX: x,
			pageY: y
		};

		this.scrollTo(posX, posY, time, easing);
	},

	next: function (time, easing) {
		var x = this.currentPage.pageX,
			y = this.currentPage.pageY;

		x++;

		if ( x >= this.pages.length && this.hasVerticalScroll ) {
			x = 0;
			y++;
		}

		this.goToPage(x, y, time, easing);
	},

	prev: function (time, easing) {
		var x = this.currentPage.pageX,
			y = this.currentPage.pageY;

		x--;

		if ( x < 0 && this.hasVerticalScroll ) {
			x = 0;
			y--;
		}

		this.goToPage(x, y, time, easing);
	},

	_initKeys: function (e) {
		// default key bindings
		var keys = {
			pageUp: 33,
			pageDown: 34,
			end: 35,
			home: 36,
			left: 37,
			up: 38,
			right: 39,
			down: 40
		};
		var i;

		// if you give me characters I give you keycode
		if ( typeof this.options.keyBindings == 'object' ) {
			for ( i in this.options.keyBindings ) {
				if ( typeof this.options.keyBindings[i] == 'string' ) {
					this.options.keyBindings[i] = this.options.keyBindings[i].toUpperCase().charCodeAt(0);
				}
			}
		} else {
			this.options.keyBindings = {};
		}

		for ( i in keys ) {
			this.options.keyBindings[i] = this.options.keyBindings[i] || keys[i];
		}

		utils.addEvent(window, 'keydown', this);

		this.on('destroy', function () {
			utils.removeEvent(window, 'keydown', this);
		});
	},

	_key: function (e) {
		if ( !this.enabled ) {
			return;
		}

		var snap = this.options.snap,	// we are using this alot, better to cache it
			newX = snap ? this.currentPage.pageX : this.x,
			newY = snap ? this.currentPage.pageY : this.y,
			now = utils.getTime(),
			prevTime = this.keyTime || 0,
			acceleration = 0.250,
			pos;

		if ( this.options.useTransition && this.isInTransition ) {
			pos = this.getComputedPosition();

			this._translate(Math.round(pos.x), Math.round(pos.y));
			this.isInTransition = false;
		}

		this.keyAcceleration = now - prevTime < 200 ? Math.min(this.keyAcceleration + acceleration, 50) : 0;

		switch ( e.keyCode ) {
			case this.options.keyBindings.pageUp:
				if ( this.hasHorizontalScroll && !this.hasVerticalScroll ) {
					newX += snap ? 1 : this.wrapperWidth;
				} else {
					newY += snap ? 1 : this.wrapperHeight;
				}
				break;
			case this.options.keyBindings.pageDown:
				if ( this.hasHorizontalScroll && !this.hasVerticalScroll ) {
					newX -= snap ? 1 : this.wrapperWidth;
				} else {
					newY -= snap ? 1 : this.wrapperHeight;
				}
				break;
			case this.options.keyBindings.end:
				newX = snap ? this.pages.length-1 : this.maxScrollX;
				newY = snap ? this.pages[0].length-1 : this.maxScrollY;
				break;
			case this.options.keyBindings.home:
				newX = 0;
				newY = 0;
				break;
			case this.options.keyBindings.left:
				newX += snap ? -1 : 5 + this.keyAcceleration>>0;
				break;
			case this.options.keyBindings.up:
				newY += snap ? 1 : 5 + this.keyAcceleration>>0;
				break;
			case this.options.keyBindings.right:
				newX -= snap ? -1 : 5 + this.keyAcceleration>>0;
				break;
			case this.options.keyBindings.down:
				newY -= snap ? 1 : 5 + this.keyAcceleration>>0;
				break;
			default:
				return;
		}

		if ( snap ) {
			this.goToPage(newX, newY);
			return;
		}

		if ( newX > 0 ) {
			newX = 0;
			this.keyAcceleration = 0;
		} else if ( newX < this.maxScrollX ) {
			newX = this.maxScrollX;
			this.keyAcceleration = 0;
		}

		if ( newY > 0 ) {
			newY = 0;
			this.keyAcceleration = 0;
		} else if ( newY < this.maxScrollY ) {
			newY = this.maxScrollY;
			this.keyAcceleration = 0;
		}

		this.scrollTo(newX, newY, 0);

		this.keyTime = now;
	},

	_animate: function (destX, destY, duration, easingFn) {
		var that = this,
			startX = this.x,
			startY = this.y,
			startTime = utils.getTime(),
			destTime = startTime + duration;

		function step () {
			var now = utils.getTime(),
				newX, newY,
				easing;

			if ( now >= destTime ) {
				that.isAnimating = false;
				that._translate(destX, destY);
				
				if ( !that.resetPosition(that.options.bounceTime) ) {
					that._execEvent('scrollEnd');
				}

				return;
			}

			now = ( now - startTime ) / duration;
			easing = easingFn(now);
			newX = ( destX - startX ) * easing + startX;
			newY = ( destY - startY ) * easing + startY;
			that._translate(newX, newY);

			if ( that.isAnimating ) {
				rAF(step);
			}

			if ( that.options.probeType == 3 ) {
				that._execEvent('scroll');
			}
		}

		this.isAnimating = true;
		step();
	},

	handleEvent: function (e) {
		switch ( e.type ) {
			case 'touchstart':
			case 'MSPointerDown':
			case 'mousedown':
				this._start(e);
				break;
			case 'touchmove':
			case 'MSPointerMove':
			case 'mousemove':
				this._move(e);
				break;
			case 'touchend':
			case 'MSPointerUp':
			case 'mouseup':
			case 'touchcancel':
			case 'MSPointerCancel':
			case 'mousecancel':
				this._end(e);
				break;
			case 'orientationchange':
			case 'resize':
				this._resize();
				break;
			case 'transitionend':
			case 'webkitTransitionEnd':
			case 'oTransitionEnd':
			case 'MSTransitionEnd':
				this._transitionEnd(e);
				break;
			case 'wheel':
			case 'DOMMouseScroll':
			case 'mousewheel':
				this._wheel(e);
				break;
			case 'keydown':
				this._key(e);
				break;
			case 'click':
				if ( !e._constructed ) {
					e.preventDefault();
					e.stopPropagation();
				}
				break;
		}
	}
};
function createDefaultScrollbar (direction, interactive, type) {
	var scrollbar = document.createElement('div'),
		indicator = document.createElement('div');

	if ( type === true ) {
		scrollbar.style.cssText = 'position:absolute;z-index:9999';
		indicator.style.cssText = '-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box;position:absolute;background:rgba(0,0,0,0.5);border:1px solid rgba(255,255,255,0.9);border-radius:3px';
	}

	indicator.className = 'iScrollIndicator';

	if ( direction == 'h' ) {
		if ( type === true ) {
			scrollbar.style.cssText += ';height:7px;left:2px;right:2px;bottom:0';
			indicator.style.height = '100%';
		}
		scrollbar.className = 'iScrollHorizontalScrollbar';
	} else {
		if ( type === true ) {
			scrollbar.style.cssText += ';width:7px;bottom:2px;top:2px;right:1px';
			indicator.style.width = '100%';
		}
		scrollbar.className = 'iScrollVerticalScrollbar';
	}

	scrollbar.style.cssText += ';overflow:hidden';

	if ( !interactive ) {
		scrollbar.style.pointerEvents = 'none';
	}

	scrollbar.appendChild(indicator);

	return scrollbar;
}

function Indicator (scroller, options) {
	this.wrapper = typeof options.el == 'string' ? document.querySelector(options.el) : options.el;
	this.wrapperStyle = this.wrapper.style;
	this.indicator = this.wrapper.children[0];
	this.indicatorStyle = this.indicator.style;
	this.scroller = scroller;

	this.options = {
		listenX: true,
		listenY: true,
		interactive: false,
		resize: true,
		defaultScrollbars: false,
		shrink: false,
		fade: false,
		speedRatioX: 0,
		speedRatioY: 0
	};

	for ( var i in options ) {
		this.options[i] = options[i];
	}

	this.sizeRatioX = 1;
	this.sizeRatioY = 1;
	this.maxPosX = 0;
	this.maxPosY = 0;

	if ( this.options.interactive ) {
		if ( !this.options.disableTouch ) {
			utils.addEvent(this.indicator, 'touchstart', this);
			utils.addEvent(window, 'touchend', this);
		}
		if ( !this.options.disablePointer ) {
			utils.addEvent(this.indicator, 'MSPointerDown', this);
			utils.addEvent(window, 'MSPointerUp', this);
		}
		if ( !this.options.disableMouse ) {
			utils.addEvent(this.indicator, 'mousedown', this);
			utils.addEvent(window, 'mouseup', this);
		}
	}

	if ( this.options.fade ) {
		this.wrapperStyle[utils.style.transform] = this.scroller.translateZ;
		this.wrapperStyle[utils.style.transitionDuration] = utils.isBadAndroid ? '0.001s' : '0ms';
		this.wrapperStyle.opacity = '0';
	}
}

Indicator.prototype = {
	handleEvent: function (e) {
		switch ( e.type ) {
			case 'touchstart':
			case 'MSPointerDown':
			case 'mousedown':
				this._start(e);
				break;
			case 'touchmove':
			case 'MSPointerMove':
			case 'mousemove':
				this._move(e);
				break;
			case 'touchend':
			case 'MSPointerUp':
			case 'mouseup':
			case 'touchcancel':
			case 'MSPointerCancel':
			case 'mousecancel':
				this._end(e);
				break;
		}
	},

	destroy: function () {
		if ( this.options.interactive ) {
			utils.removeEvent(this.indicator, 'touchstart', this);
			utils.removeEvent(this.indicator, 'MSPointerDown', this);
			utils.removeEvent(this.indicator, 'mousedown', this);

			utils.removeEvent(window, 'touchmove', this);
			utils.removeEvent(window, 'MSPointerMove', this);
			utils.removeEvent(window, 'mousemove', this);

			utils.removeEvent(window, 'touchend', this);
			utils.removeEvent(window, 'MSPointerUp', this);
			utils.removeEvent(window, 'mouseup', this);
		}

		if ( this.options.defaultScrollbars ) {
			this.wrapper.parentNode.removeChild(this.wrapper);
		}
	},

	_start: function (e) {
		var point = e.touches ? e.touches[0] : e;

		e.preventDefault();
		e.stopPropagation();

		this.transitionTime();

		this.initiated = true;
		this.moved = false;
		this.lastPointX	= point.pageX;
		this.lastPointY	= point.pageY;

		this.startTime	= utils.getTime();

		if ( !this.options.disableTouch ) {
			utils.addEvent(window, 'touchmove', this);
		}
		if ( !this.options.disablePointer ) {
			utils.addEvent(window, 'MSPointerMove', this);
		}
		if ( !this.options.disableMouse ) {
			utils.addEvent(window, 'mousemove', this);
		}

		this.scroller._execEvent('beforeScrollStart');
	},

	_move: function (e) {
		var point = e.touches ? e.touches[0] : e,
			deltaX, deltaY,
			newX, newY,
			timestamp = utils.getTime();

		if ( !this.moved ) {
			this.scroller._execEvent('scrollStart');
		}

		this.moved = true;

		deltaX = point.pageX - this.lastPointX;
		this.lastPointX = point.pageX;

		deltaY = point.pageY - this.lastPointY;
		this.lastPointY = point.pageY;

		newX = this.x + deltaX;
		newY = this.y + deltaY;

		this._pos(newX, newY);


		if ( this.scroller.options.probeType == 1 && timestamp - this.startTime > 300 ) {
			this.startTime = timestamp;
			this.scroller._execEvent('scroll');
		} else if ( this.scroller.options.probeType > 1 ) {
			this.scroller._execEvent('scroll');
		}


// INSERT POINT: indicator._move

		e.preventDefault();
		e.stopPropagation();
	},

	_end: function (e) {
		if ( !this.initiated ) {
			return;
		}

		this.initiated = false;

		e.preventDefault();
		e.stopPropagation();

		utils.removeEvent(window, 'touchmove', this);
		utils.removeEvent(window, 'MSPointerMove', this);
		utils.removeEvent(window, 'mousemove', this);

		if ( this.scroller.options.snap ) {
			var snap = this.scroller._nearestSnap(this.scroller.x, this.scroller.y);

			var time = this.options.snapSpeed || Math.max(
					Math.max(
						Math.min(Math.abs(this.scroller.x - snap.x), 1000),
						Math.min(Math.abs(this.scroller.y - snap.y), 1000)
					), 300);

			if ( this.scroller.x != snap.x || this.scroller.y != snap.y ) {
				this.scroller.directionX = 0;
				this.scroller.directionY = 0;
				this.scroller.currentPage = snap;
				this.scroller.scrollTo(snap.x, snap.y, time, this.scroller.options.bounceEasing);
			}
		}

		if ( this.moved ) {
			this.scroller._execEvent('scrollEnd');
		}
	},

	transitionTime: function (time) {
		time = time || 0;
		this.indicatorStyle[utils.style.transitionDuration] = time + 'ms';

		if ( !time && utils.isBadAndroid ) {
			this.indicatorStyle[utils.style.transitionDuration] = '0.001s';
		}
	},

	transitionTimingFunction: function (easing) {
		this.indicatorStyle[utils.style.transitionTimingFunction] = easing;
	},

	refresh: function () {
		this.transitionTime();

		if ( this.options.listenX && !this.options.listenY ) {
			this.indicatorStyle.display = this.scroller.hasHorizontalScroll ? 'block' : 'none';
		} else if ( this.options.listenY && !this.options.listenX ) {
			this.indicatorStyle.display = this.scroller.hasVerticalScroll ? 'block' : 'none';
		} else {
			this.indicatorStyle.display = this.scroller.hasHorizontalScroll || this.scroller.hasVerticalScroll ? 'block' : 'none';
		}

		if ( this.scroller.hasHorizontalScroll && this.scroller.hasVerticalScroll ) {
			utils.addClass(this.wrapper, 'iScrollBothScrollbars');
			utils.removeClass(this.wrapper, 'iScrollLoneScrollbar');

			if ( this.options.defaultScrollbars && this.options.customStyle ) {
				if ( this.options.listenX ) {
					this.wrapper.style.right = '8px';
				} else {
					this.wrapper.style.bottom = '8px';
				}
			}
		} else {
			utils.removeClass(this.wrapper, 'iScrollBothScrollbars');
			utils.addClass(this.wrapper, 'iScrollLoneScrollbar');

			if ( this.options.defaultScrollbars && this.options.customStyle ) {
				if ( this.options.listenX ) {
					this.wrapper.style.right = '2px';
				} else {
					this.wrapper.style.bottom = '2px';
				}
			}
		}

		var r = this.wrapper.offsetHeight;	// force refresh

		if ( this.options.listenX ) {
			this.wrapperWidth = this.wrapper.clientWidth;
			if ( this.options.resize ) {
				this.indicatorWidth = Math.max(Math.round(this.wrapperWidth * this.wrapperWidth / (this.scroller.scrollerWidth || this.wrapperWidth || 1)), 8);
				this.indicatorStyle.width = this.indicatorWidth + 'px';
			} else {
				this.indicatorWidth = this.indicator.clientWidth;
			}

			this.maxPosX = this.wrapperWidth - this.indicatorWidth;

			if ( this.options.shrink == 'clip' ) {
				this.minBoundaryX = -this.indicatorWidth + 8;
				this.maxBoundaryX = this.wrapperWidth - 8;
			} else {
				this.minBoundaryX = 0;
				this.maxBoundaryX = this.maxPosX;
			}

			this.sizeRatioX = this.options.speedRatioX || (this.scroller.maxScrollX && (this.maxPosX / this.scroller.maxScrollX));	
		}

		if ( this.options.listenY ) {
			this.wrapperHeight = this.wrapper.clientHeight;
			if ( this.options.resize ) {
				this.indicatorHeight = Math.max(Math.round(this.wrapperHeight * this.wrapperHeight / (this.scroller.scrollerHeight || this.wrapperHeight || 1)), 8);
				this.indicatorStyle.height = this.indicatorHeight + 'px';
			} else {
				this.indicatorHeight = this.indicator.clientHeight;
			}

			this.maxPosY = this.wrapperHeight - this.indicatorHeight;

			if ( this.options.shrink == 'clip' ) {
				this.minBoundaryY = -this.indicatorHeight + 8;
				this.maxBoundaryY = this.wrapperHeight - 8;
			} else {
				this.minBoundaryY = 0;
				this.maxBoundaryY = this.maxPosY;
			}

			this.maxPosY = this.wrapperHeight - this.indicatorHeight;
			this.sizeRatioY = this.options.speedRatioY || (this.scroller.maxScrollY && (this.maxPosY / this.scroller.maxScrollY));
		}

		this.updatePosition();
	},

	updatePosition: function () {
		var x = this.options.listenX && Math.round(this.sizeRatioX * this.scroller.x) || 0,
			y = this.options.listenY && Math.round(this.sizeRatioY * this.scroller.y) || 0;

		if ( !this.options.ignoreBoundaries ) {
			if ( x < this.minBoundaryX ) {
				if ( this.options.shrink == 'scale' ) {
					this.width = Math.max(this.indicatorWidth + x, 8);
					this.indicatorStyle.width = this.width + 'px';
				}
				x = this.minBoundaryX;
			} else if ( x > this.maxBoundaryX ) {
				if ( this.options.shrink == 'scale' ) {
					this.width = Math.max(this.indicatorWidth - (x - this.maxPosX), 8);
					this.indicatorStyle.width = this.width + 'px';
					x = this.maxPosX + this.indicatorWidth - this.width;
				} else {
					x = this.maxBoundaryX;
				}
			} else if ( this.options.shrink == 'scale' && this.width != this.indicatorWidth ) {
				this.width = this.indicatorWidth;
				this.indicatorStyle.width = this.width + 'px';
			}

			if ( y < this.minBoundaryY ) {
				if ( this.options.shrink == 'scale' ) {
					this.height = Math.max(this.indicatorHeight + y * 3, 8);
					this.indicatorStyle.height = this.height + 'px';
				}
				y = this.minBoundaryY;
			} else if ( y > this.maxBoundaryY ) {
				if ( this.options.shrink == 'scale' ) {
					this.height = Math.max(this.indicatorHeight - (y - this.maxPosY) * 3, 8);
					this.indicatorStyle.height = this.height + 'px';
					y = this.maxPosY + this.indicatorHeight - this.height;
				} else {
					y = this.maxBoundaryY;
				}
			} else if ( this.options.shrink == 'scale' && this.height != this.indicatorHeight ) {
				this.height = this.indicatorHeight;
				this.indicatorStyle.height = this.height + 'px';
			}
		}

		this.x = x;
		this.y = y;

		if ( this.scroller.options.useTransform ) {
			this.indicatorStyle[utils.style.transform] = 'translate(' + x + 'px,' + y + 'px)' + this.scroller.translateZ;
		} else {
			this.indicatorStyle.left = x + 'px';
			this.indicatorStyle.top = y + 'px';
		}
	},

	_pos: function (x, y) {
		if ( x < 0 ) {
			x = 0;
		} else if ( x > this.maxPosX ) {
			x = this.maxPosX;
		}

		if ( y < 0 ) {
			y = 0;
		} else if ( y > this.maxPosY ) {
			y = this.maxPosY;
		}

		x = this.options.listenX ? Math.round(x / this.sizeRatioX) : this.scroller.x;
		y = this.options.listenY ? Math.round(y / this.sizeRatioY) : this.scroller.y;

		this.scroller.scrollTo(x, y);
	},

	fade: function (val, hold) {
		if ( hold && !this.visible ) {
			return;
		}

		clearTimeout(this.fadeTimeout);
		this.fadeTimeout = null;

		var time = val ? 250 : 500,
			delay = val ? 0 : 300;

		val = val ? '1' : '0';

		this.wrapperStyle[utils.style.transitionDuration] = time + 'ms';

		this.fadeTimeout = setTimeout((function (val) {
			this.wrapperStyle.opacity = val;
			this.visible = +val;
		}).bind(this, val), delay);
	}
};

IScroll.utils = utils;

if ( typeof module != 'undefined' && module.exports ) {
	module.exports = IScroll;
} else {
	window.IScroll = IScroll;
}

})(window, document, Math);

//--------------------------------------------------------
/* JS Name 
 *   jquery.jplayer.js start here
 * /
/*
 * jPlayer Plugin for jQuery JavaScript Library
 * http://www.jplayer.org
 *
 * Copyright (c) 2009 - 2011 Happyworm Ltd
 * Dual licensed under the MIT and GPL licenses.
 *  - http://www.opensource.org/licenses/mit-license.php
 *  - http://www.gnu.org/copyleft/gpl.html
 *
 * Author: Mark J Panaghiston
 * Version: 2.0.22
 * Date: 13th July 2011
 */

/* Code verified using http://www.jshint.com/ */
/*jshint asi:false, bitwise:false, boss:false, browser:true, curly:true, debug:false, eqeqeq:true, eqnull:false, evil:false, forin:false, immed:false, jquery:true, laxbreak:false, newcap:true, noarg:true, noempty:true, nonew:true, nomem:false, onevar:false, passfail:false, plusplus:false, regexp:false, undef:true, sub:false, strict:false, white:false */
/*global jQuery:false, ActiveXObject:false, alert:false */

(function($, undefined) {

  // Adapted from jquery.ui.widget.js (1.8.7): $.widget.bridge
  $.fn.jPlayer = function( options ) {
    var name = "jPlayer";
    var isMethodCall = typeof options === "string",
    args = Array.prototype.slice.call( arguments, 1 ),
    returnValue = this;

    // allow multiple hashes to be passed on init
    options = !isMethodCall && args.length ?
    $.extend.apply( null, [ true, options ].concat(args) ) :
    options;

    // prevent calls to internal methods
    if ( isMethodCall && options.charAt( 0 ) === "_" ) {
      return returnValue;
    }

    if ( isMethodCall ) {
      this.each(function() {
        var instance = $.data( this, name ),
        methodValue = instance && $.isFunction( instance[options] ) ?
        instance[ options ].apply( instance, args ) :
        instance;
        if ( methodValue !== instance && methodValue !== undefined ) {
          returnValue = methodValue;
          return false;
        }
      });
    } else {
      this.each(function() {
        var instance = $.data( this, name );
        if ( instance ) {
          // instance.option( options || {} )._init(); // Orig jquery.ui.widget.js code: Not recommend for jPlayer. ie., Applying new options to an existing instance (via the jPlayer constructor) and performing the _init(). The _init() is what concerns me. It would leave a lot of event handlers acting on jPlayer instance and the interface.
          instance.option( options || {} ); // The new constructor only changes the options. Changing options only has basic support atm.
        } else {
          $.data( this, name, new $.jPlayer( options, this ) );
        }
      });
    }

    return returnValue;
  };

  $.jPlayer = function( options, element ) { 
    // allow instantiation without initializing for simple inheritance
    if ( arguments.length ) {
      this.element = $(element);
      this.options = $.extend(true, {},
        this.options,
        options
        );
      var self = this;
      this.element.bind( "remove.jPlayer", function() {
        self.destroy();
      });
      this._init();
    }
  };
  // End of: (Adapted from jquery.ui.widget.js (1.8.7))

  // Emulated HTML5 methods and properties
  $.jPlayer.emulateMethods = "load play pause";
  $.jPlayer.emulateStatus = "src readyState networkState currentTime duration paused ended playbackRate";
  $.jPlayer.emulateOptions = "muted volume";

  // Reserved event names generated by jPlayer that are not part of the HTML5 Media element spec
  $.jPlayer.reservedEvent = "ready flashreset resize repeat error warning";

  // Events generated by jPlayer
  $.jPlayer.event = {
    ready: "jPlayer_ready",
    flashreset: "jPlayer_flashreset", // Similar to the ready event if the Flash solution is set to display:none and then shown again or if it's reloaded for another reason by the browser. For example, using CSS position:fixed on Firefox for the full screen feature.
    resize: "jPlayer_resize", // Occurs when the size changes through a full/restore screen operation or if the size/sizeFull options are changed.
    repeat: "jPlayer_repeat", // Occurs when the repeat status changes. Usually through clicks on the repeat button of the interface.
    error: "jPlayer_error", // Event error code in event.jPlayer.error.type. See $.jPlayer.error
    warning: "jPlayer_warning", // Event warning code in event.jPlayer.warning.type. See $.jPlayer.warning

    // Other events match HTML5 spec.
    loadstart: "jPlayer_loadstart",
    progress: "jPlayer_progress",
    suspend: "jPlayer_suspend",
    abort: "jPlayer_abort",
    emptied: "jPlayer_emptied",
    stalled: "jPlayer_stalled",
    play: "jPlayer_play",
    pause: "jPlayer_pause",
    loadedmetadata: "jPlayer_loadedmetadata",
    loadeddata: "jPlayer_loadeddata",
    waiting: "jPlayer_waiting",
    playing: "jPlayer_playing",
    canplay: "jPlayer_canplay",
    canplaythrough: "jPlayer_canplaythrough",
    seeking: "jPlayer_seeking",
    seeked: "jPlayer_seeked",
    timeupdate: "jPlayer_timeupdate",
    ended: "jPlayer_ended",
    ratechange: "jPlayer_ratechange",
    durationchange: "jPlayer_durationchange",
    volumechange: "jPlayer_volumechange"
  };

  $.jPlayer.htmlEvent = [ // These HTML events are bubbled through to the jPlayer event, without any internal action.
  "loadstart",
  // "progress", // jPlayer uses internally before bubbling.
  // "suspend", // jPlayer uses internally before bubbling.
  "abort",
  // "error", // jPlayer uses internally before bubbling.
  "emptied",
  "stalled",
  // "play", // jPlayer uses internally before bubbling.
  // "pause", // jPlayer uses internally before bubbling.
  "loadedmetadata",
  "loadeddata",
  // "waiting", // jPlayer uses internally before bubbling.
  // "playing", // jPlayer uses internally before bubbling.
  "canplay",
  "canplaythrough",
  // "seeking", // jPlayer uses internally before bubbling.
  // "seeked", // jPlayer uses internally before bubbling.
  // "timeupdate", // jPlayer uses internally before bubbling.
  // "ended", // jPlayer uses internally before bubbling.
  "ratechange"
  // "durationchange" // jPlayer uses internally before bubbling.
  // "volumechange" // jPlayer uses internally before bubbling.
  ];

  $.jPlayer.pause = function() {
    // $.each($.jPlayer.instances, function(i, element) {
    $.each($.jPlayer.prototype.instances, function(i, element) {
      if(element.data("jPlayer").status.srcSet) { // Check that media is set otherwise would cause error event.
        element.jPlayer("pause");
      }
    });
  };
	
  $.jPlayer.timeFormat = {
    showHour: false,
    showMin: true,
    showSec: true,
    padHour: false,
    padMin: true,
    padSec: true,
    sepHour: ":",
    sepMin: ":",
    sepSec: ""
  };

  $.jPlayer.convertTime = function(s) {
    var myTime = new Date(s * 1000);
    var hour = myTime.getUTCHours();
    var min = myTime.getUTCMinutes();
    var sec = myTime.getUTCSeconds();
    var strHour = ($.jPlayer.timeFormat.padHour && hour < 10) ? "0" + hour : hour;
    var strMin = ($.jPlayer.timeFormat.padMin && min < 10) ? "0" + min : min;
    var strSec = ($.jPlayer.timeFormat.padSec && sec < 10) ? "0" + sec : sec;
    return (($.jPlayer.timeFormat.showHour) ? strHour + $.jPlayer.timeFormat.sepHour : "") + (($.jPlayer.timeFormat.showMin) ? strMin + $.jPlayer.timeFormat.sepMin : "") + (($.jPlayer.timeFormat.showSec) ? strSec + $.jPlayer.timeFormat.sepSec : "");
  };

  // Adapting jQuery 1.4.4 code for jQuery.browser. Required since jQuery 1.3.2 does not detect Chrome as webkit.
  $.jPlayer.uaBrowser = function( userAgent ) {
    var ua = userAgent.toLowerCase();

    // Useragent RegExp
    var rwebkit = /(webkit)[ \/]([\w.]+)/;
    var ropera = /(opera)(?:.*version)?[ \/]([\w.]+)/;
    var rmsie = /(msie) ([\w.]+)/;
    var rmozilla = /(mozilla)(?:.*? rv:([\w.]+))?/;

    var match = rwebkit.exec( ua ) ||
    ropera.exec( ua ) ||
    rmsie.exec( ua ) ||
    ua.indexOf("compatible") < 0 && rmozilla.exec( ua ) ||
    [];

    return {
      browser: match[1] || "", 
      version: match[2] || "0"
    };
  };

  // Platform sniffer for detecting mobile devices
  $.jPlayer.uaPlatform = function( userAgent ) {
    var ua = userAgent.toLowerCase();

    // Useragent RegExp
    var rplatform = /(ipad|iphone|ipod|android|blackberry|playbook|windows ce|webos)/;
    var rtablet = /(ipad|playbook)/;
    var randroid = /(android)/;
    var rmobile = /(mobile)/;

    var platform = rplatform.exec( ua ) || [];
    var tablet = rtablet.exec( ua ) ||
    !rmobile.exec( ua ) && randroid.exec( ua ) ||
    [];

    return {
      platform: platform[1] || "", 
      tablet: tablet[1] || ""
    };
  };

  $.jPlayer.browser = {
  };
  $.jPlayer.platform = {
  };

  var browserMatch = $.jPlayer.uaBrowser(navigator.userAgent);
  if ( browserMatch.browser ) {
    $.jPlayer.browser[ browserMatch.browser ] = true;
    $.jPlayer.browser.version = browserMatch.version;
  }
  var platformMatch = $.jPlayer.uaPlatform(navigator.userAgent);
  if ( platformMatch.platform ) {
    $.jPlayer.platform[ platformMatch.platform ] = true;
    $.jPlayer.platform.mobile = !platformMatch.tablet;
    $.jPlayer.platform.tablet = !!platformMatch.tablet;
  }

  $.jPlayer.prototype = {
    count: 0, // Static Variable: Change it via prototype.
    version: { // Static Object
      script: "2.0.22",
      needFlash: "2.0.9",
      flash: "unknown"
    },
    options: { // Instanced in $.jPlayer() constructor
      swfPath: "js", // Path to Jplayer.swf. Can be relative, absolute or server root relative.
      solution: "html, flash", // Valid solutions: html, flash. Order defines priority. 1st is highest,
      supplied: "mp3", // Defines which formats jPlayer will try and support and the priority by the order. 1st is highest,
      preload: 'metadata',  // HTML5 Spec values: none, metadata, auto.
      volume: 0.8, // The volume. Number 0 to 1.
      muted: false,
      wmode: "opaque", // Valid wmode: window, transparent, opaque, direct, gpu. 
      backgroundColor: "#000000", // To define the jPlayer div and Flash background color.
      cssSelectorAncestor: "#jp_container_1",
      cssSelector: { // * denotes properties that should only be required when video media type required. _cssSelector() would require changes to enable splitting these into Audio and Video defaults.
        videoPlay: ".jp-video-play", // *
        play: ".jp-play",
        pause: ".jp-pause",
        stop: ".jp-stop",
        seekBar: ".jp-seek-bar",
        playBar: ".jp-play-bar",
        mute: ".jp-mute",
        unmute: ".jp-unmute",
        volumeBar: ".jp-volume-bar",
        volumeBarValue: ".jp-volume-bar-value",
        volumeMax: ".jp-volume-max",
        currentTime: ".jp-current-time",
        duration: ".jp-duration",
        fullScreen: ".jp-full-screen", // *
        restoreScreen: ".jp-restore-screen", // *
        repeat: ".jp-repeat",
        repeatOff: ".jp-repeat-off",
        gui: ".jp-gui" // The interface used with autohide feature.
      },
      fullScreen: false,
      autohide: {
        restored: false, // Controls the interface autohide feature.
        full: true, // Controls the interface autohide feature.
        fadeIn: 200, // Milliseconds. The period of the fadeIn anim.
        fadeOut: 600, // Milliseconds. The period of the fadeOut anim.
        hold: 1000 // Milliseconds. The period of the pause before autohide beings.
      },
      loop: false,
      repeat: function(event) { // The default jPlayer repeat event handler
        if(event.jPlayer.options.loop) {
          $(this).unbind(".jPlayerRepeat").bind($.jPlayer.event.ended + ".jPlayer.jPlayerRepeat", function() {
            $(this).jPlayer("play");
          });
        } else {
          $(this).unbind(".jPlayerRepeat");
        }
      },
      // globalVolume: false, // Not implemented: Set to make volume changes affect all jPlayer instances
      // globalMute: false, // Not implemented: Set to make mute changes affect all jPlayer instances
      idPrefix: "jp", // Prefix for the ids of html elements created by jPlayer. For flash, this must not include characters: . - + * / \
      noConflict: "jQuery",
      emulateHtml: false, // Emulates the HTML5 Media element on the jPlayer element.
      errorAlerts: false,
      warningAlerts: false
    },
    optionsAudio: {
      size: {
        width: "0px",
        height: "0px",
        cssClass: ""
      },
      sizeFull: {
        width: "0px",
        height: "0px",
        cssClass: ""
      }
    },
    optionsVideo: {
      size: {
        width: "480px",
        height: "270px",
        cssClass: "jp-video-270p"
      },
      sizeFull: {
        width: "100%",
        height: "100%",
        cssClass: "jp-video-full"
      }
    },
    instances: {}, // Static Object
    status: { // Instanced in _init()
      src: "",
      media: {},
      paused: true,
      format: {},
      formatType: "",
      waitForPlay: true, // Same as waitForLoad except in case where preloading.
      waitForLoad: true,
      srcSet: false,
      video: false, // True if playing a video
      seekPercent: 0,
      currentPercentRelative: 0,
      currentPercentAbsolute: 0,
      currentTime: 0,
      duration: 0,
      readyState: 0,
      networkState: 0,
      playbackRate: 1,
      ended: 0

    /*		Persistant status properties created dynamically at _init():
			width
			height
			cssClass
*/
    },

    internal: { // Instanced in _init()
      ready: false
    // instance: undefined,
    // domNode: undefined,
    // htmlDlyCmdId: undefined
    // autohideId: undefined
    },
    solution: { // Static Object: Defines the solutions built in jPlayer.
      html: true,
      flash: true
    },
    // 'MPEG-4 support' : canPlayType('video/mp4; codecs="mp4v.20.8"')
    format: { // Static Object
      mp3: {
        codec: 'audio/mpeg; codecs="mp3"',
        flashCanPlay: true,
        media: 'audio'
      },
      m4a: { // AAC / MP4
        codec: 'audio/mp4; codecs="mp4a.40.2"',
        flashCanPlay: true,
        media: 'audio'
      },
      oga: { // OGG
        codec: 'audio/ogg; codecs="vorbis"',
        flashCanPlay: false,
        media: 'audio'
      },
      wav: { // PCM
        codec: 'audio/wav; codecs="1"',
        flashCanPlay: false,
        media: 'audio'
      },
      webma: { // WEBM
        codec: 'audio/webm; codecs="vorbis"',
        flashCanPlay: false,
        media: 'audio'
      },
      fla: { // FLV / F4A
        codec: 'audio/x-flv',
        flashCanPlay: true,
        media: 'audio'
      },
      m4v: { // H.264 / MP4
        codec: 'video/mp4; codecs="avc1.42E01E, mp4a.40.2"',
        flashCanPlay: true,
        media: 'video'
      },
      ogv: { // OGG
        codec: 'video/ogg; codecs="theora, vorbis"',
        flashCanPlay: false,
        media: 'video'
      },
      webmv: { // WEBM
        codec: 'video/webm; codecs="vorbis, vp8"',
        flashCanPlay: false,
        media: 'video'
      },
      flv: { // FLV / F4V
        codec: 'video/x-flv',
        flashCanPlay: true,
        media: 'video'
      }
    },
    _init: function() {
      var self = this;
			
      this.element.empty();
			
      this.status = $.extend({}, this.status); // Copy static to unique instance.
      this.internal = $.extend({}, this.internal); // Copy static to unique instance.

      this.internal.domNode = this.element.get(0);

      this.formats = []; // Array based on supplied string option. Order defines priority.
      this.solutions = []; // Array based on solution string option. Order defines priority.
      this.require = {}; // Which media types are required: video, audio.
			
      this.htmlElement = {}; // DOM elements created by jPlayer
      this.html = {}; // In _init()'s this.desired code and setmedia(): Accessed via this[solution], where solution from this.solutions array.
      this.html.audio = {};
      this.html.video = {};
      this.flash = {}; // In _init()'s this.desired code and setmedia(): Accessed via this[solution], where solution from this.solutions array.
			
      this.css = {};
      this.css.cs = {}; // Holds the css selector strings
      this.css.jq = {}; // Holds jQuery selectors. ie., $(css.cs.method)

      this.ancestorJq = []; // Holds jQuery selector of cssSelectorAncestor. Init would use $() instead of [], but it is only 1.4+

      this.options.volume = this._limitValue(this.options.volume, 0, 1); // Limit volume value's bounds.

      // Create the formats array, with prority based on the order of the supplied formats string
      $.each(this.options.supplied.toLowerCase().split(","), function(index1, value1) {
        var format = value1.replace(/^\s+|\s+$/g, ""); //trim
        if(self.format[format]) { // Check format is valid.
          var dupFound = false;
          $.each(self.formats, function(index2, value2) { // Check for duplicates
            if(format === value2) {
              dupFound = true;
              return false;
            }
          });
          if(!dupFound) {
            self.formats.push(format);
          }
        }
      });

      // Create the solutions array, with prority based on the order of the solution string
      $.each(this.options.solution.toLowerCase().split(","), function(index1, value1) {
        var solution = value1.replace(/^\s+|\s+$/g, ""); //trim
        if(self.solution[solution]) { // Check solution is valid.
          var dupFound = false;
          $.each(self.solutions, function(index2, value2) { // Check for duplicates
            if(solution === value2) {
              dupFound = true;
              return false;
            }
          });
          if(!dupFound) {
            self.solutions.push(solution);
          }
        }
      });

      this.internal.instance = "jp_" + this.count;
      this.instances[this.internal.instance] = this.element;

      // Check the jPlayer div has an id and create one if required. Important for Flash to know the unique id for comms.
      if(!this.element.attr("id")) {
        this.element.attr("id", this.options.idPrefix + "_jplayer_" + this.count);
      }

      this.internal.self = $.extend({}, {
        id: this.element.attr("id"),
        jq: this.element
      });
      this.internal.audio = $.extend({}, {
        id: this.options.idPrefix + "_audio_" + this.count,
        jq: undefined
      });
      this.internal.video = $.extend({}, {
        id: this.options.idPrefix + "_video_" + this.count,
        jq: undefined
      });
      this.internal.flash = $.extend({}, {
        id: this.options.idPrefix + "_flash_" + this.count,
        jq: undefined,
        swf: this.options.swfPath + ((this.options.swfPath !== "" && this.options.swfPath.slice(-1) !== "/") ? "/" : "") + "Jplayer.swf"
      });
      this.internal.poster = $.extend({}, {
        id: this.options.idPrefix + "_poster_" + this.count,
        jq: undefined
      });

      // Register listeners defined in the constructor
      $.each($.jPlayer.event, function(eventName,eventType) {
        if(self.options[eventName] !== undefined) {
          self.element.bind(eventType + ".jPlayer", self.options[eventName]); // With .jPlayer namespace.
          self.options[eventName] = undefined; // Destroy the handler pointer copy on the options. Reason, events can be added/removed in other ways so this could be obsolete and misleading.
        }
      });

      // Determine if we require solutions for audio, video or both media types.
      this.require.audio = false;
      this.require.video = false;
      $.each(this.formats, function(priority, format) {
        self.require[self.format[format].media] = true;
      });

      // Now required types are known, finish the options default settings.
      if(this.require.video) {
        this.options = $.extend(true, {},
          this.optionsVideo,
          this.options
          );
      } else {
        this.options = $.extend(true, {},
          this.optionsAudio,
          this.options
          );
      }
      this._setSize(); // update status and jPlayer element size

      // Create the poster image.
      this.htmlElement.poster = document.createElement('img');
      this.htmlElement.poster.id = this.internal.poster.id;
      this.htmlElement.poster.onload = function() { // Note that this did not work on Firefox 3.6: poster.addEventListener("onload", function() {}, false); Did not investigate x-browser.
        if(!self.status.video || self.status.waitForPlay) {
          self.internal.poster.jq.show();
        }
      };
      this.element.append(this.htmlElement.poster);
      this.internal.poster.jq = $("#" + this.internal.poster.id);
      this.internal.poster.jq.css({
        'width': this.status.width, 
        'height': this.status.height
      });
      this.internal.poster.jq.hide();
			
      // Generate the required media elements
      this.html.audio.available = false;
      if(this.require.audio) { // If a supplied format is audio
        this.htmlElement.audio = document.createElement('audio');
        this.htmlElement.audio.id = this.internal.audio.id;
        this.html.audio.available = !!this.htmlElement.audio.canPlayType;
      }
      this.html.video.available = false;
      if(this.require.video) { // If a supplied format is video
        this.htmlElement.video = document.createElement('video');
        this.htmlElement.video.id = this.internal.video.id;
        this.html.video.available = !!this.htmlElement.video.canPlayType;
      }

      this.flash.available = this._checkForFlash(10);

      this.html.canPlay = {};
      this.flash.canPlay = {};
      $.each(this.formats, function(priority, format) {
        self.html.canPlay[format] = self.html[self.format[format].media].available && "" !== self.htmlElement[self.format[format].media].canPlayType(self.format[format].codec);
        self.flash.canPlay[format] = self.format[format].flashCanPlay && self.flash.available;
      });
      this.html.desired = false;
      this.flash.desired = false;
      $.each(this.solutions, function(solutionPriority, solution) {
        if(solutionPriority === 0) {
          self[solution].desired = true;
        } else {
          var audioCanPlay = false;
          var videoCanPlay = false;
          $.each(self.formats, function(formatPriority, format) {
            if(self[self.solutions[0]].canPlay[format]) { // The other solution can play
              if(self.format[format].media === 'video') {
                videoCanPlay = true;
              } else {
                audioCanPlay = true;
              }
            }
          });
          self[solution].desired = (self.require.audio && !audioCanPlay) || (self.require.video && !videoCanPlay);
        }
      });
      // This is what jPlayer will support, based on solution and supplied.
      this.html.support = {};
      this.flash.support = {};
      $.each(this.formats, function(priority, format) {
        self.html.support[format] = self.html.canPlay[format] && self.html.desired;
        self.flash.support[format] = self.flash.canPlay[format] && self.flash.desired;
      });
      // If jPlayer is supporting any format in a solution, then the solution is used.
      this.html.used = false;
      this.flash.used = false;
      $.each(this.solutions, function(solutionPriority, solution) {
        $.each(self.formats, function(formatPriority, format) {
          if(self[solution].support[format]) {
            self[solution].used = true;
            return false;
          }
        });
      });

      // Init solution active state and the event gates to false.
      this.html.active = false;
      this.html.audio.gate = false;
      this.html.video.gate = false;
      this.flash.active = false;
      this.flash.gate = false;

      // Set up the css selectors for the control and feedback entities.
      this._cssSelectorAncestor(this.options.cssSelectorAncestor);
			
      // If neither html nor flash are being used by this browser, then media playback is not possible. Trigger an error event.
      if(!(this.html.used || this.flash.used)) {
        this._error( {
          type: $.jPlayer.error.NO_SOLUTION, 
          context: "{solution:'" + this.options.solution + "', supplied:'" + this.options.supplied + "'}",
          message: $.jPlayer.errorMsg.NO_SOLUTION,
          hint: $.jPlayer.errorHint.NO_SOLUTION
        });
      }

      // Add the flash solution if it is being used.
      if(this.flash.used) {
        var htmlObj,
        flashVars = 'jQuery=' + encodeURI(this.options.noConflict) + '&id=' + encodeURI(this.internal.self.id) + '&vol=' + this.options.volume + '&muted=' + this.options.muted;

        // Code influenced by SWFObject 2.2: http://code.google.com/p/swfobject/
        // Non IE browsers have an initial Flash size of 1 by 1 otherwise the wmode affected the Flash ready event. 

        if($.browser.msie && Number($.browser.version) <= 8) {
          var objStr = '<object id="' + this.internal.flash.id + '" classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" width="0" height="0"></object>';

          var paramStr = [
          '<param name="movie" value="' + this.internal.flash.swf + '" />',
          '<param name="FlashVars" value="' + flashVars + '" />',
          '<param name="allowScriptAccess" value="always" />',
          '<param name="bgcolor" value="' + this.options.backgroundColor + '" />',
          '<param name="wmode" value="' + this.options.wmode + '" />'
          ];

          htmlObj = document.createElement(objStr);
          for(var i=0; i < paramStr.length; i++) {
            htmlObj.appendChild(document.createElement(paramStr[i]));
          }
        } else {
          var createParam = function(el, n, v) {
            var p = document.createElement("param");
            p.setAttribute("name", n);	
            p.setAttribute("value", v);
            el.appendChild(p);
          };

          htmlObj = document.createElement("object");
          htmlObj.setAttribute("id", this.internal.flash.id);
          htmlObj.setAttribute("data", this.internal.flash.swf);
          htmlObj.setAttribute("type", "application/x-shockwave-flash");
          htmlObj.setAttribute("width", "1"); // Non-zero
          htmlObj.setAttribute("height", "1"); // Non-zero
          createParam(htmlObj, "flashvars", flashVars);
          createParam(htmlObj, "allowscriptaccess", "always");
          createParam(htmlObj, "bgcolor", this.options.backgroundColor);
          createParam(htmlObj, "wmode", this.options.wmode);
        }

        this.element.append(htmlObj);
        this.internal.flash.jq = $(htmlObj);
      }
			
      // Add the HTML solution if being used.
      if(this.html.used) {

        // The HTML Audio handlers
        if(this.html.audio.available) {
          this._addHtmlEventListeners(this.htmlElement.audio, this.html.audio);
          this.element.append(this.htmlElement.audio);
          this.internal.audio.jq = $("#" + this.internal.audio.id);
        }

        // The HTML Video handlers
        if(this.html.video.available) {
          this._addHtmlEventListeners(this.htmlElement.video, this.html.video);
          this.element.append(this.htmlElement.video);
          this.internal.video.jq = $("#" + this.internal.video.id);
          this.internal.video.jq.css({
            'width':'0px', 
            'height':'0px'
          }); // Using size 0x0 since a .hide() causes issues in iOS
        }
      }

      // Create the bridge that emulates the HTML Media element on the jPlayer DIV
      if( this.options.emulateHtml ) {
        this._emulateHtmlBridge();
      }

      if(this.html.used && !this.flash.used) { // If only HTML, then emulate flash ready() call after 100ms.
        setTimeout( function() {
          self.internal.ready = true;
          self.version.flash = "n/a";
          self._trigger($.jPlayer.event.repeat); // Trigger the repeat event so its handler can initialize itself with the loop option.
          self._trigger($.jPlayer.event.ready);
        }, 100);
      }

      // Initialize the interface components with the options.
      this._updateInterface();
      this._updateButtons(false);
      this._updateAutohide();
      this._updateVolume(this.options.volume);
      this._updateMute(this.options.muted);
      if(this.css.jq.videoPlay.length) {
        this.css.jq.videoPlay.hide();
      }

      $.jPlayer.prototype.count++; // Change static variable via prototype.
    },
    destroy: function() {
      // MJP: The background change remains. Would need to store the original to restore it correctly.

      // Reset the interface, remove seeking effect and times.
      this._resetStatus();
      this._updateInterface();
      this._seeked();
      if(this.css.jq.currentTime.length) {
        this.css.jq.currentTime.text("");
      }
      if(this.css.jq.duration.length) {
        this.css.jq.duration.text("");
      }

      if(this.status.srcSet) { // Or you get a bogus error event
        this.pause(); // Pauses the media and clears any delayed commands used in the HTML solution.
      }
      $.each(this.css.jq, function(fn, jq) { // Remove any bindings from the interface controls.
        // Check selector is valid before trying to execute method.
        if(jq.length) {
          jq.unbind(".jPlayer");
        }
      });
      if( this.options.emulateHtml ) {
        this._destroyHtmlBridge();
      }
      this.element.removeData("jPlayer"); // Remove jPlayer data
      this.element.unbind(".jPlayer"); // Remove all event handlers created by the jPlayer constructor
      this.element.empty(); // Remove the inserted child elements
			
      this.instances[this.internal.instance] = undefined; // Clear the instance on the static instance object
    },
    enable: function() { // Plan to implement
    // options.disabled = false
    },
    disable: function () { // Plan to implement
    // options.disabled = true
    },
    _addHtmlEventListeners: function(mediaElement, entity) {
      var self = this;
      mediaElement.preload = this.options.preload;
      mediaElement.muted = this.options.muted;
      mediaElement.volume = this.options.volume;

      // Create the event listeners
      // Only want the active entity to affect jPlayer and bubble events.
      // Using entity.gate so that object is referenced and gate property always current
			
      mediaElement.addEventListener("progress", function() {
        if(entity.gate && !self.status.waitForLoad) {
          self._getHtmlStatus(mediaElement);
          self._updateInterface();
          self._trigger($.jPlayer.event.progress);
        }
      }, false);
      mediaElement.addEventListener("timeupdate", function() {
        if(entity.gate && !self.status.waitForLoad) {
          self._getHtmlStatus(mediaElement);
          self._updateInterface();
          self._trigger($.jPlayer.event.timeupdate);
        }
      }, false);
      mediaElement.addEventListener("durationchange", function() {
        if(entity.gate && !self.status.waitForLoad) {
          self.status.duration = this.duration;
          self._getHtmlStatus(mediaElement);
          self._updateInterface();
          self._trigger($.jPlayer.event.durationchange);
        }
      }, false);
      mediaElement.addEventListener("play", function() {
        if(entity.gate && !self.status.waitForLoad) {
          self._updateButtons(true);
          self._trigger($.jPlayer.event.play);
        }
      }, false);
      mediaElement.addEventListener("playing", function() {
        if(entity.gate && !self.status.waitForLoad) {
          self._updateButtons(true);
          self._seeked();
          self._trigger($.jPlayer.event.playing);
        }
      }, false);
      mediaElement.addEventListener("pause", function() {
        if(entity.gate && !self.status.waitForLoad) {
          self._updateButtons(false);
          self._trigger($.jPlayer.event.pause);
        }
      }, false);
      mediaElement.addEventListener("waiting", function() {
        if(entity.gate && !self.status.waitForLoad) {
          self._seeking();
          self._trigger($.jPlayer.event.waiting);
        }
      }, false);
      mediaElement.addEventListener("seeking", function() {
        if(entity.gate && !self.status.waitForLoad) {
          self._seeking();
          self._trigger($.jPlayer.event.seeking);
        }
      }, false);
      mediaElement.addEventListener("seeked", function() {
        if(entity.gate && !self.status.waitForLoad) {
          self._seeked();
          self._trigger($.jPlayer.event.seeked);
        }
      }, false);
      mediaElement.addEventListener("volumechange", function() {
        if(entity.gate && !self.status.waitForLoad) {
          // Read the values back from the element as the Blackberry PlayBook shares the volume with the physical buttons master volume control.
          // However, when tested 6th July 2011, those buttons do not generate an event. The physical play/pause button does though.
          self.options.volume = mediaElement.volume;
          self.options.muted = mediaElement.muted;
          self._updateMute();
          self._updateVolume();
          self._trigger($.jPlayer.event.volumechange);
        }
      }, false);
      mediaElement.addEventListener("suspend", function() { // Seems to be the only way of capturing that the iOS4 browser did not actually play the media from the page code. ie., It needs a user gesture.
        if(entity.gate && !self.status.waitForLoad) {
          self._seeked();
          self._trigger($.jPlayer.event.suspend);
        }
      }, false);
      mediaElement.addEventListener("ended", function() {
        if(entity.gate && !self.status.waitForLoad) {
          // Order of the next few commands are important. Change the time and then pause.
          // Solves a bug in Firefox, where issuing pause 1st causes the media to play from the start. ie., The pause is ignored.
          if(!$.jPlayer.browser.webkit) { // Chrome crashes if you do this in conjunction with a setMedia command in an ended event handler. ie., The playlist demo.
            self.htmlElement.media.currentTime = 0; // Safari does not care about this command. ie., It works with or without this line. (Both Safari and Chrome are Webkit.)
          }
          self.htmlElement.media.pause(); // Pause otherwise a click on the progress bar will play from that point, when it shouldn't, since it stopped playback.
          self._updateButtons(false);
          self._getHtmlStatus(mediaElement, true); // With override true. Otherwise Chrome leaves progress at full.
          self._updateInterface();
          self._trigger($.jPlayer.event.ended);
        }
      }, false);
      mediaElement.addEventListener("error", function() {
        if(entity.gate && !self.status.waitForLoad) {
          self._updateButtons(false);
          self._seeked();
          if(self.status.srcSet) { // Deals with case of clearMedia() causing an error event.
            clearTimeout(self.internal.htmlDlyCmdId); // Clears any delayed commands used in the HTML solution.
            self.status.waitForLoad = true; // Allows the load operation to try again.
            self.status.waitForPlay = true; // Reset since a play was captured.
            if(self.status.video) {
              self.internal.video.jq.css({
                'width':'0px', 
                'height':'0px'
              });
            }
            if(self._validString(self.status.media.poster)) {
              self.internal.poster.jq.show();
            }
            if(self.css.jq.videoPlay.length) {
              self.css.jq.videoPlay.show();
            }
            self._error( {
              type: $.jPlayer.error.URL,
              context: self.status.src, // this.src shows absolute urls. Want context to show the url given.
              message: $.jPlayer.errorMsg.URL,
              hint: $.jPlayer.errorHint.URL
            });
          }
        }
      }, false);
      // Create all the other event listeners that bubble up to a jPlayer event from html, without being used by jPlayer.
      $.each($.jPlayer.htmlEvent, function(i, eventType) {
        mediaElement.addEventListener(this, function() {
          if(entity.gate && !self.status.waitForLoad) {
            self._trigger($.jPlayer.event[eventType]);
          }
        }, false);
      });
    },
    _getHtmlStatus: function(media, override) {
      var ct = 0, d = 0, cpa = 0, sp = 0, cpr = 0;

      if(media.duration) { // Fixes the duration bug in iOS, where the durationchange event occurs when media.duration is not always correct.
        this.status.duration = media.duration;
      }
      ct = media.currentTime;
      cpa = (this.status.duration > 0) ? 100 * ct / this.status.duration : 0;
      if((typeof media.seekable === "object") && (media.seekable.length > 0)) {
        sp = (this.status.duration > 0) ? 100 * media.seekable.end(media.seekable.length-1) / this.status.duration : 100;
        cpr = 100 * media.currentTime / media.seekable.end(media.seekable.length-1);
      } else {
        sp = 100;
        cpr = cpa;
      }
			
      if(override) {
        ct = 0;
        cpr = 0;
        cpa = 0;
      }

      this.status.seekPercent = sp;
      this.status.currentPercentRelative = cpr;
      this.status.currentPercentAbsolute = cpa;
      this.status.currentTime = ct;

      this.status.readyState = media.readyState;
      this.status.networkState = media.networkState;
      this.status.playbackRate = media.playbackRate;
      this.status.ended = media.ended;
    },
    _resetStatus: function() {
      this.status = $.extend({}, this.status, $.jPlayer.prototype.status); // Maintains the status properties that persist through a reset.
    },
    _trigger: function(eventType, error, warning) { // eventType always valid as called using $.jPlayer.event.eventType
      var event = $.Event(eventType);
      event.jPlayer = {};
      event.jPlayer.version = $.extend({}, this.version);
      event.jPlayer.options = $.extend(true, {}, this.options); // Deep copy
      event.jPlayer.status = $.extend(true, {}, this.status); // Deep copy
      event.jPlayer.html = $.extend(true, {}, this.html); // Deep copy
      event.jPlayer.flash = $.extend(true, {}, this.flash); // Deep copy
      if(error) {
        event.jPlayer.error = $.extend({}, error);
      }
      if(warning) {
        event.jPlayer.warning = $.extend({}, warning);
      }
      this.element.trigger(event);
    },
    jPlayerFlashEvent: function(eventType, status) { // Called from Flash
      if(eventType === $.jPlayer.event.ready) {
        if(!this.internal.ready) {
          this.internal.ready = true;
          this.internal.flash.jq.css({
            'width':'0px', 
            'height':'0px'
          }); // Once Flash generates the ready event, minimise to zero as it is not affected by wmode anymore.

          this.version.flash = status.version;
          if(this.version.needFlash !== this.version.flash) {
            this._error( {
              type: $.jPlayer.error.VERSION,
              context: this.version.flash,
              message: $.jPlayer.errorMsg.VERSION + this.version.flash,
              hint: $.jPlayer.errorHint.VERSION
            });
          }
          this._trigger($.jPlayer.event.repeat); // Trigger the repeat event so its handler can initialize itself with the loop option.
          this._trigger(eventType);
        } else {
          // This condition occurs if the Flash is hidden and then shown again.
          // Firefox also reloads the Flash if the CSS position changes. position:fixed is used for full screen.

          // Only do this if the Flash is the solution being used at the moment. Affects Media players where both solution may be being used.
          if(this.flash.gate) {

            // Send the current status to the Flash now that it is ready (available) again.
            if(this.status.srcSet) {

              // Need to read original status before issuing the setMedia command.
              var	currentTime = this.status.currentTime,
              paused = this.status.paused; 

              this.setMedia(this.status.media);
              if(currentTime > 0) {
                if(paused) {
                  this.pause(currentTime);
                } else {
                  this.play(currentTime);
                }
              }
            }
            this._trigger($.jPlayer.event.flashreset);
          }
        }
      }
      if(this.flash.gate) {
        switch(eventType) {
          case $.jPlayer.event.progress:
            this._getFlashStatus(status);
            this._updateInterface();
            this._trigger(eventType);
            break;
          case $.jPlayer.event.timeupdate:
            this._getFlashStatus(status);
            this._updateInterface();
            this._trigger(eventType);
            break;
          case $.jPlayer.event.play:
            this._seeked();
            this._updateButtons(true);
            this._trigger(eventType);
            break;
          case $.jPlayer.event.pause:
            this._updateButtons(false);
            this._trigger(eventType);
            break;
          case $.jPlayer.event.ended:
            this._updateButtons(false);
            this._trigger(eventType);
            break;
          case $.jPlayer.event.error:
            this.status.waitForLoad = true; // Allows the load operation to try again.
            this.status.waitForPlay = true; // Reset since a play was captured.
            if(this.status.video) {
              this.internal.flash.jq.css({
                'width':'0px', 
                'height':'0px'
              });
            }
            if(this._validString(this.status.media.poster)) {
              this.internal.poster.jq.show();
            }
            if(this.css.jq.videoPlay.length) {
              this.css.jq.videoPlay.show();
            }
            if(this.status.video) { // Set up for another try. Execute before error event.
              this._flash_setVideo(this.status.media);
            } else {
              this._flash_setAudio(this.status.media);
            }
            this._error( {
              type: $.jPlayer.error.URL,
              context:status.src,
              message: $.jPlayer.errorMsg.URL,
              hint: $.jPlayer.errorHint.URL
            });
            break;
          case $.jPlayer.event.seeking:
            this._seeking();
            this._trigger(eventType);
            break;
          case $.jPlayer.event.seeked:
            this._seeked();
            this._trigger(eventType);
            break;
          case $.jPlayer.event.ready:
            // The ready event is handled outside the switch statement.
            // Captured here otherwise 2 ready events would be generated if the ready event handler used setMedia.
            break;
          default:
            this._trigger(eventType);
        }
      }
      return false;
    },
    _getFlashStatus: function(status) {
      this.status.seekPercent = status.seekPercent;
      this.status.currentPercentRelative = status.currentPercentRelative;
      this.status.currentPercentAbsolute = status.currentPercentAbsolute;
      this.status.currentTime = status.currentTime;
      this.status.duration = status.duration;

      // The Flash does not generate this information in this release
      this.status.readyState = 4; // status.readyState;
      this.status.networkState = 0; // status.networkState;
      this.status.playbackRate = 1; // status.playbackRate;
      this.status.ended = false; // status.ended;
    },
    _updateButtons: function(playing) {
      if(playing !== undefined) {
        this.status.paused = !playing;
        if(this.css.jq.play.length && this.css.jq.pause.length) {
          if(playing) {
            this.css.jq.play.hide();
            this.css.jq.pause.show();
          } else {
            this.css.jq.play.show();
            this.css.jq.pause.hide();
          }
        }
      }
      if(this.css.jq.restoreScreen.length && this.css.jq.fullScreen.length) {
        if(this.options.fullScreen) {
          this.css.jq.fullScreen.hide();
          this.css.jq.restoreScreen.show();
        } else {
          this.css.jq.fullScreen.show();
          this.css.jq.restoreScreen.hide();
        }
      }
      if(this.css.jq.repeat.length && this.css.jq.repeatOff.length) {
        if(this.options.loop) {
          this.css.jq.repeat.hide();
          this.css.jq.repeatOff.show();
        } else {
          this.css.jq.repeat.show();
          this.css.jq.repeatOff.hide();
        }
      }
    },
    _updateInterface: function() {
      if(this.css.jq.seekBar.length) {
        this.css.jq.seekBar.width(this.status.seekPercent+"%");
      }
      if(this.css.jq.playBar.length) {
        this.css.jq.playBar.width(this.status.currentPercentRelative+"%");
      }
      if(this.css.jq.currentTime.length) {
        this.css.jq.currentTime.text($.jPlayer.convertTime(this.status.currentTime));
      }
      if(this.css.jq.duration.length) {
        this.css.jq.duration.text($.jPlayer.convertTime(this.status.duration));
      }
    },
    _seeking: function() {
      if(this.css.jq.seekBar.length) {
        this.css.jq.seekBar.addClass("jp-seeking-bg");
      }
    },
    _seeked: function() {
      if(this.css.jq.seekBar.length) {
        this.css.jq.seekBar.removeClass("jp-seeking-bg");
      }
    },
    setMedia: function(media) {
		
      /*	media[format] = String: URL of format. Must contain all of the supplied option's video or audio formats.
			 *	media.poster = String: Video poster URL.
			 *	media.subtitles = String: * NOT IMPLEMENTED * URL of subtitles SRT file
			 *	media.chapters = String: * NOT IMPLEMENTED * URL of chapters SRT file
			 *	media.stream = Boolean: * NOT IMPLEMENTED * Designating actual media streams. ie., "false/undefined" for files. Plan to refresh the flash every so often.
			 */
			
      var self = this;
			
      this._seeked();
      clearTimeout(this.internal.htmlDlyCmdId); // Clears any delayed commands used in the HTML solution.

      // Store the current html gates, since we need for clearMedia() conditions.
      var audioGate = this.html.audio.gate;
      var videoGate = this.html.video.gate;

      var supported = false;
      $.each(this.formats, function(formatPriority, format) {
        var isVideo = self.format[format].media === 'video';
        $.each(self.solutions, function(solutionPriority, solution) {
          if(self[solution].support[format] && self._validString(media[format])) { // Format supported in solution and url given for format.
            var isHtml = solution === 'html';
						
            if(isVideo) {
              if(isHtml) {
                self.html.audio.gate = false;
                self.html.video.gate = true;
                self.flash.gate = false;
              } else {
                self.html.audio.gate = false;
                self.html.video.gate = false;
                self.flash.gate = true;
              }
            } else {
              if(isHtml) {
                self.html.audio.gate = true;
                self.html.video.gate = false;
                self.flash.gate = false;
              } else {
                self.html.audio.gate = false;
                self.html.video.gate = false;
                self.flash.gate = true;
              }
            }

            // Clear media of the previous solution if:
            //  - it was Flash
            //  - changing from HTML to Flash
            //  - the HTML solution media type (audio or video) remained the same.
            // Note that, we must be careful with clearMedia() on iPhone, otherwise clearing the video when changing to audio corrupts the built in video player.
            if(self.flash.active || (self.html.active && self.flash.gate) || (audioGate === self.html.audio.gate && videoGate === self.html.video.gate)) {
              self.clearMedia();
            } else if(audioGate !== self.html.audio.gate && videoGate !== self.html.video.gate) { // If switching between html elements
              self._html_pause();
              // Hide the video if it was being used.
              if(self.status.video) {
                self.internal.video.jq.css({
                  'width':'0px', 
                  'height':'0px'
                });
              }
              self._resetStatus(); // Since clearMedia usually does this. Execute after status.video useage.
            }

            if(isVideo) {
              if(isHtml) {
                self._html_setVideo(media);
                self.html.active = true;
                self.flash.active = false;
              } else {
                self._flash_setVideo(media);
                self.html.active = false;
                self.flash.active = true;
              }
              if(self.css.jq.videoPlay.length) {
                self.css.jq.videoPlay.show();
              }
              self.status.video = true;
            } else {
              if(isHtml) {
                self._html_setAudio(media);
                self.html.active = true;
                self.flash.active = false;
              } else {
                self._flash_setAudio(media);
                self.html.active = false;
                self.flash.active = true;
              }
              if(self.css.jq.videoPlay.length) {
                self.css.jq.videoPlay.hide();
              }
              self.status.video = false;
            }
						
            supported = true;
            return false; // Exit $.each
          }
        });
        if(supported) {
          return false; // Exit $.each
        }
      });

      if(supported) {
        // Set poster after the possible clearMedia() command above. IE had issues since the IMG onload event occurred immediately when cached. ie., The clearMedia() hide the poster.
        if(this._validString(media.poster)) {
          if(this.htmlElement.poster.src !== media.poster) { // Since some browsers do not generate img onload event.
            this.htmlElement.poster.src = media.poster;
          } else {
            this.internal.poster.jq.show();
          }
        } else {
          this.internal.poster.jq.hide(); // Hide if not used, since clearMedia() does not always occur above. ie., HTML audio <-> video switching.
        }
        this.status.srcSet = true;
        this.status.media = $.extend({}, media);
        this._updateButtons(false);
        this._updateInterface();
      } else { // jPlayer cannot support any formats provided in this browser
        // Pause here if old media could be playing. Otherwise, playing media being changed to bad media would leave the old media playing.
        if(this.status.srcSet && !this.status.waitForPlay) {
          this.pause();
        }
        // Reset all the control flags
        this.html.audio.gate = false;
        this.html.video.gate = false;
        this.flash.gate = false;
        this.html.active = false;
        this.flash.active = false;
        // Reset status and interface.
        this._resetStatus();
        this._updateInterface();
        this._updateButtons(false);
        // Hide the any old media
        this.internal.poster.jq.hide();
        if(this.html.used && this.require.video) {
          this.internal.video.jq.css({
            'width':'0px', 
            'height':'0px'
          });
        }
        if(this.flash.used) {
          this.internal.flash.jq.css({
            'width':'0px', 
            'height':'0px'
          });
        }
        // Send an error event
        this._error( {
          type: $.jPlayer.error.NO_SUPPORT,
          context: "{supplied:'" + this.options.supplied + "'}",
          message: $.jPlayer.errorMsg.NO_SUPPORT,
          hint: $.jPlayer.errorHint.NO_SUPPORT
        });
      }
    },
    clearMedia: function() {
      this._resetStatus();
      this._updateButtons(false);

      this.internal.poster.jq.hide();

      clearTimeout(this.internal.htmlDlyCmdId);

      if(this.html.active) {
        this._html_clearMedia();
      } else if(this.flash.active) {
        this._flash_clearMedia();
      }
    },
    load: function() {
      if(this.status.srcSet) {
        if(this.html.active) {
          this._html_load();
        } else if(this.flash.active) {
          this._flash_load();
        }
      } else {
        this._urlNotSetError("load");
      }
    },
    play: function(time) {
      time = (typeof time === "number") ? time : NaN; // Remove jQuery event from click handler
      if(this.status.srcSet) {
        if(this.html.active) {
          this._html_play(time);
        } else if(this.flash.active) {
          this._flash_play(time);
        }
      } else {
        this._urlNotSetError("play");
      }
    },
    videoPlay: function(e) { // Handles clicks on the play button over the video poster
      this.play();
    },
    pause: function(time) {
      time = (typeof time === "number") ? time : NaN; // Remove jQuery event from click handler
      if(this.status.srcSet) {
        if(this.html.active) {
          this._html_pause(time);
        } else if(this.flash.active) {
          this._flash_pause(time);
        }
      } else {
        this._urlNotSetError("pause");
      }
    },
    pauseOthers: function() {
      var self = this;
      $.each(this.instances, function(i, element) {
        if(self.element !== element) { // Do not this instance.
          if(element.data("jPlayer").status.srcSet) { // Check that media is set otherwise would cause error event.
            element.jPlayer("pause");
          }
        }
      });
    },
    stop: function() {
      if(this.status.srcSet) {
        if(this.html.active) {
          this._html_pause(0);
        } else if(this.flash.active) {
          this._flash_pause(0);
        }
      } else {
        this._urlNotSetError("stop");
      }
    },
    playHead: function(p) {
      p = this._limitValue(p, 0, 100);
      if(this.status.srcSet) {
        if(this.html.active) {
          this._html_playHead(p);
        } else if(this.flash.active) {
          this._flash_playHead(p);
        }
      } else {
        this._urlNotSetError("playHead");
      }
    },
    _muted: function(muted) {
      this.options.muted = muted;
      if(this.html.used) {
        this._html_mute(muted);
      }
      if(this.flash.used) {
        this._flash_mute(muted);
      }

      // The HTML solution generates this event from the media element itself.
      if(this.flash.gate) {
        this._updateMute(muted);
        this._updateVolume(this.options.volume);
        this._trigger($.jPlayer.event.volumechange);
      }
    },
    mute: function(mute) { // mute is either: undefined (true), an event object (true) or a boolean (muted).
      mute = mute === undefined ? true : !!mute;
      this._muted(mute);
    },
    unmute: function(unmute) { // unmute is either: undefined (true), an event object (true) or a boolean (!muted).
      unmute = unmute === undefined ? true : !!unmute;
      this._muted(!unmute);
    },
    _updateMute: function(mute) {
      if(mute === undefined) {
        mute = this.options.muted;
      }
      if(this.css.jq.mute.length && this.css.jq.unmute.length) {
        if(mute) {
          this.css.jq.mute.hide();
          this.css.jq.unmute.show();
        } else {
          this.css.jq.mute.show();
          this.css.jq.unmute.hide();
        }
      }
    },
    volume: function(v) {
      v = this._limitValue(v, 0, 1);
      this.options.volume = v;

      if(this.html.used) {
        this._html_volume(v);
      }
      if(this.flash.used) {
        this._flash_volume(v);
      }

      // The HTML solution generates this event from the media element itself.
      if(this.flash.gate) {
        this._updateVolume(v);
        this._trigger($.jPlayer.event.volumechange);
      }
    },
    volumeBar: function(e) { // Handles clicks on the volumeBar
      if(this.css.jq.volumeBar.length) {
        var offset = this.css.jq.volumeBar.offset();
        var x = e.pageX - offset.left;
        var w = this.css.jq.volumeBar.width();
        var v = x/w;
        this.volume(v);
      }
      if(this.options.muted) {
        this._muted(false);
      }
    },
    volumeBarValue: function(e) { // Handles clicks on the volumeBarValue
      this.volumeBar(e);
    },
    _updateVolume: function(v) {
      if(v === undefined) {
        v = this.options.volume;
      }
      v = this.options.muted ? 0 : v;

      if(this.css.jq.volumeBarValue.length) {
        this.css.jq.volumeBarValue.width((v*100)+"%");
      }
    },
    volumeMax: function() { // Handles clicks on the volume max
      this.volume(1);
      if(this.options.muted) {
        this._muted(false);
      }
    },
    _cssSelectorAncestor: function(ancestor) {
      var self = this;
      this.options.cssSelectorAncestor = ancestor;
      this._removeUiClass();
      this.ancestorJq = ancestor ? $(ancestor) : []; // Would use $() instead of [], but it is only 1.4+
      if(ancestor && this.ancestorJq.length !== 1) { // So empty strings do not generate the warning.
        this._warning( {
          type: $.jPlayer.warning.CSS_SELECTOR_COUNT,
          context: ancestor,
          message: $.jPlayer.warningMsg.CSS_SELECTOR_COUNT + this.ancestorJq.length + " found for cssSelectorAncestor.",
          hint: $.jPlayer.warningHint.CSS_SELECTOR_COUNT
        });
      }
      this._addUiClass();
      $.each(this.options.cssSelector, function(fn, cssSel) {
        self._cssSelector(fn, cssSel);
      });
    },
    _cssSelector: function(fn, cssSel) {
      var self = this;
      if(typeof cssSel === 'string') {
        if($.jPlayer.prototype.options.cssSelector[fn]) {
          if(this.css.jq[fn] && this.css.jq[fn].length) {
            this.css.jq[fn].unbind(".jPlayer");
          }
          this.options.cssSelector[fn] = cssSel;
          this.css.cs[fn] = this.options.cssSelectorAncestor + " " + cssSel;

          if(cssSel) { // Checks for empty string
            this.css.jq[fn] = $(this.css.cs[fn]);
          } else {
            this.css.jq[fn] = []; // To comply with the css.jq[fn].length check before its use. As of jQuery 1.4 could have used $() for an empty set. 
          }

          if(this.css.jq[fn].length) {
            var handler = function(e) {
              self[fn](e);
              $(this).blur();
              return false;
            };
            this.css.jq[fn].bind("click.jPlayer", handler); // Using jPlayer namespace
          }

          if(cssSel && this.css.jq[fn].length !== 1) { // So empty strings do not generate the warning. ie., they just remove the old one.
            this._warning( {
              type: $.jPlayer.warning.CSS_SELECTOR_COUNT,
              context: this.css.cs[fn],
              message: $.jPlayer.warningMsg.CSS_SELECTOR_COUNT + this.css.jq[fn].length + " found for " + fn + " method.",
              hint: $.jPlayer.warningHint.CSS_SELECTOR_COUNT
            });
          }
        } else {
          this._warning( {
            type: $.jPlayer.warning.CSS_SELECTOR_METHOD,
            context: fn,
            message: $.jPlayer.warningMsg.CSS_SELECTOR_METHOD,
            hint: $.jPlayer.warningHint.CSS_SELECTOR_METHOD
          });
        }
      } else {
        this._warning( {
          type: $.jPlayer.warning.CSS_SELECTOR_STRING,
          context: cssSel,
          message: $.jPlayer.warningMsg.CSS_SELECTOR_STRING,
          hint: $.jPlayer.warningHint.CSS_SELECTOR_STRING
        });
      }
    },
    seekBar: function(e) { // Handles clicks on the seekBar
      if(this.css.jq.seekBar) {
        var offset = this.css.jq.seekBar.offset();
        var x = e.pageX - offset.left;
        var w = this.css.jq.seekBar.width();
        var p = 100*x/w;
        this.playHead(p);
      }
    },
    playBar: function(e) { // Handles clicks on the playBar
      this.seekBar(e);
    },
    repeat: function() { // Handle clicks on the repeat button
      this._loop(true);
    },
    repeatOff: function() { // Handle clicks on the repeatOff button
      this._loop(false);
    },
    _loop: function(loop) {
      if(this.options.loop !== loop) {
        this.options.loop = loop;
        this._updateButtons();
        this._trigger($.jPlayer.event.repeat);
      }
    },

    // Plan to review the cssSelector method to cope with missing associated functions accordingly.

    currentTime: function(e) { // Handles clicks on the text
    // Added to avoid errors using cssSelector system for the text
    },
    duration: function(e) { // Handles clicks on the text
    // Added to avoid errors using cssSelector system for the text
    },
    gui: function(e) { // Handles clicks on the gui
    // Added to avoid errors using cssSelector system for the gui
    },

    // Options code adapted from ui.widget.js (1.8.7).  Made changes so the key can use dot notation. To match previous getData solution in jPlayer 1.
    option: function(key, value) {
      var options = key;

      // Enables use: options().  Returns a copy of options object
      if ( arguments.length === 0 ) {
        return $.extend( true, {}, this.options );
      }

      if(typeof key === "string") {
        var keys = key.split(".");

        // Enables use: options("someOption")  Returns a copy of the option. Supports dot notation.
        if(value === undefined) {

          var opt = $.extend(true, {}, this.options);
          for(var i = 0; i < keys.length; i++) {
            if(opt[keys[i]] !== undefined) {
              opt = opt[keys[i]];
            } else {
              this._warning( {
                type: $.jPlayer.warning.OPTION_KEY,
                context: key,
                message: $.jPlayer.warningMsg.OPTION_KEY,
                hint: $.jPlayer.warningHint.OPTION_KEY
              });
              return undefined;
            }
          }
          return opt;
        }

        // Enables use: options("someOptionObject", someObject}).  Creates: {someOptionObject:someObject}
        // Enables use: options("someOption", someValue).  Creates: {someOption:someValue}
        // Enables use: options("someOptionObject.someOption", someValue).  Creates: {someOptionObject:{someOption:someValue}}

        options = {};
        var opts = options;

        for(var j = 0; j < keys.length; j++) {
          if(j < keys.length - 1) {
            opts[keys[j]] = {};
            opts = opts[keys[j]];
          } else {
            opts[keys[j]] = value;
          }
        }
      }

      // Otherwise enables use: options(optionObject).  Uses original object (the key)

      this._setOptions(options);

      return this;
    },
    _setOptions: function(options) {
      var self = this;
      $.each(options, function(key, value) { // This supports the 2 level depth that the options of jPlayer has. Would review if we ever need more depth.
        self._setOption(key, value);
      });

      return this;
    },
    _setOption: function(key, value) {
      var self = this;

      // The ability to set options is limited at this time.

      switch(key) {
        case "volume" :
          this.volume(value);
          break;
        case "muted" :
          this._muted(value);
          break;
        case "cssSelectorAncestor" :
          this._cssSelectorAncestor(value); // Set and refresh all associations for the new ancestor.
          break;
        case "cssSelector" :
          $.each(value, function(fn, cssSel) {
            self._cssSelector(fn, cssSel); // NB: The option is set inside this function, after further validity checks.
          });
          break;
        case "fullScreen" :
          if(this.options[key] !== value) { // if changed
            this._removeUiClass();
            this.options[key] = value;
            this._refreshSize();
          }
          break;
        case "size" :
          if(!this.options.fullScreen && this.options[key].cssClass !== value.cssClass) {
            this._removeUiClass();
          }
          this.options[key] = $.extend({}, this.options[key], value); // store a merged copy of it, incase not all properties changed.
          this._refreshSize();
          break;
        case "sizeFull" :
          if(this.options.fullScreen && this.options[key].cssClass !== value.cssClass) {
            this._removeUiClass();
          }
          this.options[key] = $.extend({}, this.options[key], value); // store a merged copy of it, incase not all properties changed.
          this._refreshSize();
          break;
        case "autohide" :
          this.options[key] = $.extend({}, this.options[key], value); // store a merged copy of it, incase not all properties changed.
          this._updateAutohide();
          break;
        case "loop" :
          this._loop(value);
          break;
        case "emulateHtml" :
          if(this.options[key] !== value) { // To avoid multiple event handlers being created, if true already.
            this.options[key] = value;
            if(value) {
              this._emulateHtmlBridge();
            } else {
              this._destroyHtmlBridge();
            }
          }
          break;
      }

      return this;
    },
    // End of: (Options code adapted from ui.widget.js)

    _refreshSize: function() {
      this._setSize(); // update status and jPlayer element size
      this._addUiClass(); // update the ui class
      this._updateSize(); // update internal sizes
      this._updateButtons();
      this._updateAutohide();
      this._trigger($.jPlayer.event.resize);
    },
    _setSize: function() {
      // Determine the current size from the options
      if(this.options.fullScreen) {
        this.status.width = this.options.sizeFull.width;
        this.status.height = this.options.sizeFull.height;
        this.status.cssClass = this.options.sizeFull.cssClass;
      } else {
        this.status.width = this.options.size.width;
        this.status.height = this.options.size.height;
        this.status.cssClass = this.options.size.cssClass;
      }

      // Set the size of the jPlayer area.
      this.element.css({
        'width': this.status.width, 
        'height': this.status.height
      });
    },
    _addUiClass: function() {
      if(this.ancestorJq.length) {
        this.ancestorJq.addClass(this.status.cssClass);
      }
    },
    _removeUiClass: function() {
      if(this.ancestorJq.length) {
        this.ancestorJq.removeClass(this.status.cssClass);
      }
    },
    _updateSize: function() {
      // The poster uses show/hide so can simply resize it.
      this.internal.poster.jq.css({
        'width': this.status.width, 
        'height': this.status.height
      });

      // Video html or flash resized if necessary at this time.
      if(!this.status.waitForPlay) {
        if(this.html.active && this.status.video) { // Only if video media
          this.internal.video.jq.css({
            'width': this.status.width, 
            'height': this.status.height
          });
        }
        else if(this.flash.active) {
          this.internal.flash.jq.css({
            'width': this.status.width, 
            'height': this.status.height
          });
        }
      }
    },
    _updateAutohide: function() {
      var	self = this,
      event = "mousemove.jPlayer",
      namespace = ".jPlayerAutohide",
      eventType = event + namespace,
      handler = function() {
        self.css.jq.gui.fadeIn(self.options.autohide.fadeIn, function() {
          clearTimeout(self.internal.autohideId);
          self.internal.autohideId = setTimeout( function() {
            self.css.jq.gui.fadeOut(self.options.autohide.fadeOut);
          }, self.options.autohide.hold);
        });
      };

      clearTimeout(this.internal.autohideId);
      this.element.unbind(namespace);
      if(this.css.jq.gui.length) {
        this.css.jq.gui.unbind(namespace);
        if(this.options.fullScreen && this.options.autohide.full || !this.options.fullScreen && this.options.autohide.restored) {
          this.element.bind(eventType, handler);
          this.css.jq.gui.bind(eventType, handler);
          this.css.jq.gui.hide();
        } else {
          this.css.jq.gui.stop(true, true).show(); // Need the stop() otherwise a change screen mode during the GUI fade out hides the GUI in the other mode.
        }
      }
    },
    fullScreen: function() {
      this._setOption("fullScreen", true);
    },
    restoreScreen: function() {
      this._setOption("fullScreen", false);
    },
    _html_initMedia: function() {
      if(this.status.srcSet  && !this.status.waitForPlay) {
        this.htmlElement.media.pause();
      }
      if(this.options.preload !== 'none') {
        this._html_load();
      }
      this._trigger($.jPlayer.event.timeupdate); // The flash generates this event for its solution.
    },
    _html_setAudio: function(media) {
      var self = this;
      // Always finds a format due to checks in setMedia()
      $.each(this.formats, function(priority, format) {
        if(self.html.support[format] && media[format]) {
          self.status.src = media[format];
          self.status.format[format] = true;
          self.status.formatType = format;
          return false;
        }
      });
      this.htmlElement.media = this.htmlElement.audio;
      this._html_initMedia();
    },
    _html_setVideo: function(media) {
      var self = this;
      // Always finds a format due to checks in setMedia()
      $.each(this.formats, function(priority, format) {
        if(self.html.support[format] && media[format]) {
          self.status.src = media[format];
          self.status.format[format] = true;
          self.status.formatType = format;
          return false;
        }
      });
      this.htmlElement.media = this.htmlElement.video;
      this._html_initMedia();
    },
    _html_clearMedia: function() {
      if(this.htmlElement.media) {
        if(this.htmlElement.media.id === this.internal.video.id) {
          this.internal.video.jq.css({
            'width':'0px', 
            'height':'0px'
          });
        }
        this.htmlElement.media.pause();
        this.htmlElement.media.src = "";
        this.htmlElement.media.load(); // Stops an old, "in progress" download from continuing the download. Triggers the loadstart, error and emptied events, due to the empty src. Also an abort event if a download was in progress.
      }
    },
    _html_load: function() {
      if(this.status.waitForLoad) {
        this.status.waitForLoad = false;
        this.htmlElement.media.src = this.status.src;
        this.htmlElement.media.load();
      }
      clearTimeout(this.internal.htmlDlyCmdId);
    },
    _html_play: function(time) {
      var self = this;
      this._html_load(); // Loads if required and clears any delayed commands.

      this.htmlElement.media.play(); // Before currentTime attempt otherwise Firefox 4 Beta never loads.

      if(!isNaN(time)) {
        try {
          this.htmlElement.media.currentTime = time;
        } catch(err) {
          this.internal.htmlDlyCmdId = setTimeout(function() {
            self.play(time);
          }, 100);
          return; // Cancel execution and wait for the delayed command.
        }
      }
      this._html_checkWaitForPlay();
    },
    _html_pause: function(time) {
      var self = this;
			
      if(time > 0) { // We do not want the stop() command, which does pause(0), causing a load operation.
        this._html_load(); // Loads if required and clears any delayed commands.
      } else {
        clearTimeout(this.internal.htmlDlyCmdId);
      }

      // Order of these commands is important for Safari (Win) and IE9. Pause then change currentTime.
      this.htmlElement.media.pause();

      if(!isNaN(time)) {
        try {
          this.htmlElement.media.currentTime = time;
        } catch(err) {
          this.internal.htmlDlyCmdId = setTimeout(function() {
            self.pause(time);
          }, 100);
          return; // Cancel execution and wait for the delayed command.
        }
      }
      if(time > 0) { // Avoids a setMedia() followed by stop() or pause(0) hiding the video play button.
        this._html_checkWaitForPlay();
      }
    },
    _html_playHead: function(percent) {
      var self = this;
      this._html_load(); // Loads if required and clears any delayed commands.
      try {
        if((typeof this.htmlElement.media.seekable === "object") && (this.htmlElement.media.seekable.length > 0)) {
          this.htmlElement.media.currentTime = percent * this.htmlElement.media.seekable.end(this.htmlElement.media.seekable.length-1) / 100;
        } else if(this.htmlElement.media.duration > 0 && !isNaN(this.htmlElement.media.duration)) {
          this.htmlElement.media.currentTime = percent * this.htmlElement.media.duration / 100;
        } else {
          throw "e";
        }
      } catch(err) {
        this.internal.htmlDlyCmdId = setTimeout(function() {
          self.playHead(percent);
        }, 100);
        return; // Cancel execution and wait for the delayed command.
      }
      if(!this.status.waitForLoad) {
        this._html_checkWaitForPlay();
      }
    },
    _html_checkWaitForPlay: function() {
      if(this.status.waitForPlay) {
        this.status.waitForPlay = false;
        if(this.css.jq.videoPlay.length) {
          this.css.jq.videoPlay.hide();
        }
        if(this.status.video) {
          this.internal.poster.jq.hide();
          this.internal.video.jq.css({
            'width': this.status.width, 
            'height': this.status.height
          });
        }
      }
    },
    _html_volume: function(v) {
      if(this.html.audio.available) {
        this.htmlElement.audio.volume = v;
      }
      if(this.html.video.available) {
        this.htmlElement.video.volume = v;
      }
    },
    _html_mute: function(m) {
      if(this.html.audio.available) {
        this.htmlElement.audio.muted = m;
      }
      if(this.html.video.available) {
        this.htmlElement.video.muted = m;
      }
    },
    _flash_setAudio: function(media) {
      var self = this;
      try {
        // Always finds a format due to checks in setMedia()
        $.each(this.formats, function(priority, format) {
          if(self.flash.support[format] && media[format]) {
            switch (format) {
              case "m4a" :
              case "fla" :
                self._getMovie().fl_setAudio_m4a(media[format]);
                break;
              case "mp3" :
                self._getMovie().fl_setAudio_mp3(media[format]);
                break;
            }
            self.status.src = media[format];
            self.status.format[format] = true;
            self.status.formatType = format;
            return false;
          }
        });

        if(this.options.preload === 'auto') {
          this._flash_load();
          this.status.waitForLoad = false;
        }
      } catch(err) {
        this._flashError(err);
      }
    },
    _flash_setVideo: function(media) {
      var self = this;
      try {
        // Always finds a format due to checks in setMedia()
        $.each(this.formats, function(priority, format) {
          if(self.flash.support[format] && media[format]) {
            switch (format) {
              case "m4v" :
              case "flv" :
                self._getMovie().fl_setVideo_m4v(media[format]);
                break;
            }
            self.status.src = media[format];
            self.status.format[format] = true;
            self.status.formatType = format;
            return false;
          }
        });

        if(this.options.preload === 'auto') {
          this._flash_load();
          this.status.waitForLoad = false;
        }
      } catch(err) {
        this._flashError(err);
      }
    },
    _flash_clearMedia: function() {
      this.internal.flash.jq.css({
        'width':'0px', 
        'height':'0px'
      }); // Must do via CSS as setting attr() to zero causes a jQuery error in IE.
      try {
        this._getMovie().fl_clearMedia();
      } catch(err) {
        this._flashError(err);
      }
    },
    _flash_load: function() {
      try {
        this._getMovie().fl_load();
      } catch(err) {
        this._flashError(err);
      }
      this.status.waitForLoad = false;
    },
    _flash_play: function(time) {
      try {
        this._getMovie().fl_play(time);
      } catch(err) {
        this._flashError(err);
      }
      this.status.waitForLoad = false;
      this._flash_checkWaitForPlay();
    },
    _flash_pause: function(time) {
      try {
        this._getMovie().fl_pause(time);
      } catch(err) {
        this._flashError(err);
      }
      if(time > 0) { // Avoids a setMedia() followed by stop() or pause(0) hiding the video play button.
        this.status.waitForLoad = false;
        this._flash_checkWaitForPlay();
      }
    },
    _flash_playHead: function(p) {
      try {
        this._getMovie().fl_play_head(p);
      } catch(err) {
        this._flashError(err);
      }
      if(!this.status.waitForLoad) {
        this._flash_checkWaitForPlay();
      }
    },
    _flash_checkWaitForPlay: function() {
      if(this.status.waitForPlay) {
        this.status.waitForPlay = false;
        if(this.css.jq.videoPlay.length) {
          this.css.jq.videoPlay.hide();
        }
        if(this.status.video) {
          this.internal.poster.jq.hide();
          this.internal.flash.jq.css({
            'width': this.status.width, 
            'height': this.status.height
          });
        }
      }
    },
    _flash_volume: function(v) {
      try {
        this._getMovie().fl_volume(v);
      } catch(err) {
        this._flashError(err);
      }
    },
    _flash_mute: function(m) {
      try {
        this._getMovie().fl_mute(m);
      } catch(err) {
        this._flashError(err);
      }
    },
    _getMovie: function() {
      return document[this.internal.flash.id];
    },
    _checkForFlash: function (version) {
      // Function checkForFlash adapted from FlashReplace by Robert Nyman
      // http://code.google.com/p/flashreplace/
      var flashIsInstalled = false;
      var flash;
      if(window.ActiveXObject){
        try{
          flash = new ActiveXObject(("ShockwaveFlash.ShockwaveFlash." + version));
          flashIsInstalled = true;
        }
        catch(e){
        // Throws an error if the version isn't available			
        }
      }
      else if(navigator.plugins && navigator.mimeTypes.length > 0){
        flash = navigator.plugins["Shockwave Flash"];
        if(flash){
          var flashVersion = navigator.plugins["Shockwave Flash"].description.replace(/.*\s(\d+\.\d+).*/, "$1");
          if(flashVersion >= version){
            flashIsInstalled = true;
          }
        }
      }
      return flashIsInstalled;
    },
    _validString: function(url) {
      return (url && typeof url === "string"); // Empty strings return false
    },
    _limitValue: function(value, min, max) {
      return (value < min) ? min : ((value > max) ? max : value);
    },
    _urlNotSetError: function(context) {
      this._error( {
        type: $.jPlayer.error.URL_NOT_SET,
        context: context,
        message: $.jPlayer.errorMsg.URL_NOT_SET,
        hint: $.jPlayer.errorHint.URL_NOT_SET
      });
    },
    _flashError: function(error) {
      var errorType;
      if(!this.internal.ready) {
        errorType = "FLASH";
      } else {
        errorType = "FLASH_DISABLED";
      }
      this._error( {
        type: $.jPlayer.error[errorType],
        context: this.internal.flash.swf,
        message: $.jPlayer.errorMsg[errorType] + error.message,
        hint: $.jPlayer.errorHint[errorType]
      });

    },
    _error: function(error) {
      this._trigger($.jPlayer.event.error, error);
      if(this.options.errorAlerts) {
        this._alert("Error!" + (error.message ? "\n\n" + error.message : "") + (error.hint ? "\n\n" + error.hint : "") + "\n\nContext: " + error.context);
      }
    },
    _warning: function(warning) {
      this._trigger($.jPlayer.event.warning, undefined, warning);
      if(this.options.warningAlerts) {
        this._alert("Warning!" + (warning.message ? "\n\n" + warning.message : "") + (warning.hint ? "\n\n" + warning.hint : "") + "\n\nContext: " + warning.context);
      }
    },
    _alert: function(message) {
      alert("jPlayer " + this.version.script + " : id='" + this.internal.self.id +"' : " + message);
    },
    _emulateHtmlBridge: function() {
      var self = this,
      methods = $.jPlayer.emulateMethods;

      // Emulate methods on jPlayer's DOM element.
      $.each( $.jPlayer.emulateMethods.split(/\s+/g), function(i, name) {
        self.internal.domNode[name] = function(arg) {
          self[name](arg);
        };

      });

      // Bubble jPlayer events to its DOM element.
      $.each($.jPlayer.event, function(eventName,eventType) {
        var nativeEvent = true;
        $.each( $.jPlayer.reservedEvent.split(/\s+/g), function(i, name) {
          if(name === eventName) {
            nativeEvent = false;
            return false;
          }
        });
        if(nativeEvent) {
          self.element.bind(eventType + ".jPlayer.jPlayerHtml", function() { // With .jPlayer & .jPlayerHtml namespaces.
            self._emulateHtmlUpdate();
            var domEvent = document.createEvent("Event");
            domEvent.initEvent(eventName, false, true);
            self.internal.domNode.dispatchEvent(domEvent);
          });
        }
      // The error event would require a special case
      });

    // IE9 has a readyState property on all elements. The document should have it, but all (except media) elements inherit it in IE9. This conflicts with Popcorn, which polls the readyState.
    },
    _emulateHtmlUpdate: function() {
      var self = this;

      $.each( $.jPlayer.emulateStatus.split(/\s+/g), function(i, name) {
        self.internal.domNode[name] = self.status[name];
      });
      $.each( $.jPlayer.emulateOptions.split(/\s+/g), function(i, name) {
        self.internal.domNode[name] = self.options[name];
      });
    },
    _destroyHtmlBridge: function() {
      var self = this;

      // Bridge event handlers are also removed by destroy() through .jPlayer namespace.
      this.element.unbind(".jPlayerHtml"); // Remove all event handlers created by the jPlayer bridge. So you can change the emulateHtml option.

      // Remove the methods and properties
      var emulated = $.jPlayer.emulateMethods + " " + $.jPlayer.emulateStatus + " " + $.jPlayer.emulateOptions;
      $.each( emulated.split(/\s+/g), function(i, name) {
        delete self.internal.domNode[name];
      });
    }
  };

  $.jPlayer.error = {
    FLASH: "e_flash",
    FLASH_DISABLED: "e_flash_disabled",
    NO_SOLUTION: "e_no_solution",
    NO_SUPPORT: "e_no_support",
    URL: "e_url",
    URL_NOT_SET: "e_url_not_set",
    VERSION: "e_version"
  };

  $.jPlayer.errorMsg = {
    FLASH: "jPlayer's Flash fallback is not configured correctly, or a command was issued before the jPlayer Ready event. Details: ", // Used in: _flashError()
    FLASH_DISABLED: "jPlayer's Flash fallback has been disabled by the browser due to the CSS rules you have used. Details: ", // Used in: _flashError()
    NO_SOLUTION: "No solution can be found by jPlayer in this browser. Neither HTML nor Flash can be used.", // Used in: _init()
    NO_SUPPORT: "It is not possible to play any media format provided in setMedia() on this browser using your current options.", // Used in: setMedia()
    URL: "Media URL could not be loaded.", // Used in: jPlayerFlashEvent() and _addHtmlEventListeners()
    URL_NOT_SET: "Attempt to issue media playback commands, while no media url is set.", // Used in: load(), play(), pause(), stop() and playHead()
    VERSION: "jPlayer " + $.jPlayer.prototype.version.script + " needs Jplayer.swf version " + $.jPlayer.prototype.version.needFlash + " but found " // Used in: jPlayerReady()
  };

  $.jPlayer.errorHint = {
    FLASH: "Check your swfPath option and that Jplayer.swf is there.",
    FLASH_DISABLED: "Check that you have not display:none; the jPlayer entity or any ancestor.",
    NO_SOLUTION: "Review the jPlayer options: support and supplied.",
    NO_SUPPORT: "Video or audio formats defined in the supplied option are missing.",
    URL: "Check media URL is valid.",
    URL_NOT_SET: "Use setMedia() to set the media URL.",
    VERSION: "Update jPlayer files."
  };

  $.jPlayer.warning = {
    CSS_SELECTOR_COUNT: "e_css_selector_count",
    CSS_SELECTOR_METHOD: "e_css_selector_method",
    CSS_SELECTOR_STRING: "e_css_selector_string",
    OPTION_KEY: "e_option_key"
  };

  $.jPlayer.warningMsg = {
    CSS_SELECTOR_COUNT: "The number of css selectors found did not equal one: ",
    CSS_SELECTOR_METHOD: "The methodName given in jPlayer('cssSelector') is not a valid jPlayer method.",
    CSS_SELECTOR_STRING: "The methodCssSelector given in jPlayer('cssSelector') is not a String or is empty.",
    OPTION_KEY: "The option requested in jPlayer('option') is undefined."
  };

  $.jPlayer.warningHint = {
    CSS_SELECTOR_COUNT: "Check your css selector and the ancestor.",
    CSS_SELECTOR_METHOD: "Check your method name.",
    CSS_SELECTOR_STRING: "Check your css selector is a string.",
    OPTION_KEY: "Check your option name."
  };
})(jQuery);

// Sitemobile Compatible Js 

(function($) {
  $.fn.smMusicPlayer = function(playlist, userOptions) {
    var $self = this, defaultOptions, options, cssSelector, init, playlistMgr, interfaceMgr, playlist,
    layout, myPlaylist, current;

    cssSelector = {
      jPlayer: "#jquery_jplayer",
      jPlayerInterface: '.jp-interface',
      playerPrevious: ".jp-interface .jp-previous",
      playerNext: ".jp-interface .jp-next",
      trackList:'.tracklist',
      tracks:'.tracks',
      track:'.track',
      trackInfo:'.track-info',
      title: '.title',
      playing:'.playing',
      moreButton:'.more',
      player:'.music-player-top'
    };

    defaultOptions = {
      onPlayCallback:null,
      tracksToShow:5,
      autoPlay:false
    };

    options = $.extend(true, {}, defaultOptions, userOptions);

    myPlaylist = playlist;

    current = 0;

    init = function() {
      playlist = new playlistMgr();
      layout = new interfaceMgr();
      playlist.init(options.jPlayer);
      $self.bind('smPlaylistLoaded', function() {
        layout.init();

      });
    };

    playlistMgr = function() {

      var playing = false, markup, $myJplayer = {},$tracks,showHeight = 0,remainingHeight = 0,$tracksWrapper, $more;

      markup = {
      };

      function init(playlistOptions) {

        $myJplayer = $($self.find('.music-player .jPlayer-container'));


        var jPlayerDefaults, jPlayerOptions;

        jPlayerDefaults = {
          swfPath: "jquery.jplayer",
          supplied: "mp3, oga",
          cssSelectorAncestor:  cssSelector.jPlayerInterface,
          preload: 'auto',
          errorAlerts: false,
          warningAlerts: false,
          fullScreen: false
        };

        //apply any user defined jPlayer options
        jPlayerOptions = $.extend(true, {}, jPlayerDefaults, playlistOptions);
        $myJplayer.bind($.jPlayer.event.play, function() {
          $(this).jPlayer("pauseOthers");
 
        });
        $myJplayer.bind($.jPlayer.event.ready, function() {
          //Bind jPlayer events. Do not want to pass in options object to prevent them from being overridden by the user
          $myJplayer.bind($.jPlayer.event.ended, function(event) {
            playlistNext();
          });

          $myJplayer.bind($.jPlayer.event.play, function(event) {
            $myJplayer.jPlayer("pauseOthers");
            $tracks.eq(current).addClass(attr(cssSelector.playing)).siblings().removeClass(attr(cssSelector.playing));
            runCallback(options.onPlayCallback, current, myPlaylist[current]);
          });


          $myJplayer.bind($.jPlayer.event.playing, function(event) {
            playing = true;
          });

          $myJplayer.bind($.jPlayer.event.pause, function(event) {
            playing = false;
          });

          //Bind next/prev click events
          $($self.find(cssSelector.playerPrevious)).click(function() {
            playlistPrev();
            $(this).blur();
            return false;
          });

          $($self.find(cssSelector.playerNext)).click(function() {
            playlistNext();
            $(this).blur();
            return false;
          });

          $self.bind('smPlaylist', function(e) {
            var changeTo = this.getData('smPlaylist');

            if (changeTo != current) {
              current = changeTo;
              playlistContent(current);
            }
            else {
              if (!$myJplayer.data('jPlayer').status.srcSet) {
                playlistContent(0);
              }
              else {
                togglePlay();
              }
            }
          });

          buildPlaylist();
          //If the user doesn't want to wait for widget loads, start playlist now
          $self.trigger('smPlaylistLoaded');

          playlistInit(options.autoplay);
        });

        //Initialize jPlayer
        $myJplayer.jPlayer(jPlayerOptions);

      }

      function playlistInit(autoplay) {
        current = 0;

        if (autoplay) {
          playlistContent(current);
        }
        else {
          playlistConfig(current);
          $self.trigger('smPlaylistInit');
        }
      }

      function playlistConfig(index) {
        current = index;
        $myJplayer.jPlayer("setMedia", myPlaylist[current]);
      }

      function playlistContent(index) {
        playlistConfig(index);

        if (index >= options.tracksToShow)
          showMore();

        $self.trigger('smPlaylistContent');
        $myJplayer.jPlayer("play");
      }

      function playlistNext() {
        var index = (current + 1 < myPlaylist.length) ? current + 1 : 0;
        playlistContent(index);
      }

      function playlistPrev() {
        var index = (current - 1 >= 0) ? current - 1 : myPlaylist.length - 1;
        playlistContent(index);
      }

      function togglePlay() {
        if (!playing)
          $myJplayer.jPlayer("play");
        else $myJplayer.jPlayer("pause");
      }

      function buildPlaylist() {

        $tracksWrapper = $self.find(cssSelector.tracks);
        var j=0;
        $tracksWrapper.find('.track').each(function() {
          $(this).data('index', j);
          j++;
        });


        $tracks = $($self.find(cssSelector.track));

        $tracks.slice(0, options.tracksToShow).each(function() {
          showHeight += $(this).outerHeight();
        });

        $tracks.slice(options.tracksToShow, myPlaylist.length).each(function() {
          remainingHeight += $(this).outerHeight();
        });

        if (remainingHeight > 0) {
          var $trackList = $(cssSelector.trackList);

          $tracksWrapper.height(showHeight);
          $trackList.addClass('show-more-button');

          $trackList.find(cssSelector.moreButton).click(function() {
            $more = $(this);
            showMore();

          });
        }

        $tracks.find('.title').click(function() {
          playlistContent($(this).parents('li').data('index'));
        });
      }

      function showMore() {

        if (isUndefined($more))
          $more = $self.find(cssSelector.moreButton);

        $tracksWrapper.animate({
          height: showHeight + remainingHeight
        }, function() {
          $more.animate({
            opacity:0
          }, function() {
            $more.slideUp(function() {
              $more.parents(cssSelector.trackList).removeClass('show-more-button');
              $more.remove();

            });
          });
        });
      }

      return{
        init:init,
        playlistInit:playlistInit,
        playlistContent:playlistContent,
        playlistNext:playlistNext,
        playlistPrev:playlistPrev,
        togglePlay:togglePlay,
        $myJplayer:$myJplayer
      };

    };

       
    interfaceMgr = function() {

      var $player, $title, $artist;


      function init() {
        $player = $($self.find(cssSelector.player)),
        $title = $player.find(cssSelector.title),
        $artist = $player.find(cssSelector.artist);
        $self.bind('smPlaylistContent smPlaylistInit', function() {
          setTitle();
        });
      }
      function setTitle() {
        $title.html(trackName(current));
      }

      return{
        init:init
      }

    };

    /** Common Functions **/
    function trackName(index) {
      if (!isUndefined(myPlaylist[index].title))
        return myPlaylist[index].title;
      else if (!isUndefined(myPlaylist[index].mp3))
        return fileName(myPlaylist[index].mp3);
      else if (!isUndefined(myPlaylist[index].oga))
        return fileName(myPlaylist[index].oga);
      else return '';
    }

    function fileName(path) {
      path = path.split('/');
      return path[path.length - 1];
    }





    /** Utility Functions **/
    function attr(selector) {
      return selector.substr(1);
    }

    function runCallback(callback) {
      var functionArgs = Array.prototype.slice.call(arguments, 1);

      if ($.isFunction(callback)) {
        callback.apply(this, functionArgs);
      }
    }

    function isUndefined(value) {
      return typeof value == 'undefined';
    }

    init();
  };
})(jQuery);

(function($) {
  $.fn.smMusicShortPlayer = function(playlist, options) {
    var $self = this;
    $self.find('.jPlayer-container').jPlayer({
        ready: function () {
          $(this).jPlayer("setMedia", playlist);
        },
        play: function() { // To avoid multiple jPlayers playing together.
          $(this).jPlayer("pauseOthers");
          if($.isFunction(options.onPlayCallback))
          options.onPlayCallback();
        },
        swfPath: "jquery.jplayer",
        supplied: "mp3, oga",
        cssSelectorAncestor: options.cssSelectorAncestor,
        preload: 'auto',
        errorAlerts: false,
        warningAlerts: false,
        fullScreen: false
      });
  }; 
})(jQuery);

//------------------------------------------------------------------------------
/* JS Name 
 *   smSocialActivity.js start here
 */
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: smSocialActivity.js 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
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
//      $.mobile.showPageLoadingMsg();
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
          $.mobile.hidePageLoadingMsg();          
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
    
//     $.mobile.showPageLoadingMsg();
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
//          $.mobile.hidePageLoadingMsg();
				  $.mobile.showPageLoadingMsg('a', 'Your Tweet to @' + $('#screen_name').val() + 'has been sent!', true);
          //$('#feedshare').html('Your Tweet to @' + $('#screen_name').val() + 'has been sent!' );
          $(this).delay(800).queue(function(){            
            $('.ui-page-active').removeClass('pop_back_max_height');
            $('#feedsharepopup').remove();
            $.mobile.loading().loader("hide");
//            $.mobile.hidePageLoadingMsg();
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
//      $.mobile.showPageLoadingMsg();
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
//              $.mobile.hidePageLoadingMsg();
				      $.mobile.showPageLoadingMsg('a', 'Your message was successfully sent.', true);
              $(this).delay(700).queue(function(){
                  $.mobile.loading().loader("hide");
//                $.mobile.hidePageLoadingMsg();
                $(".ui-page-active").removeClass("pop_back_max_height");
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
			
		
//      $.mobile.showPageLoadingMsg();
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
//          $.mobile.hidePageLoadingMsg();
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

/* JS Name 
 *   smActivity.js start here
 */
//----------------------------------------------------------------------------------
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
        proceed_request = false,
        proceed_request_temp = false,
        currentactive_panel = 'undefined',
        photoUpload = false,
        oldCommentLikeID = 0,
        parentScrollTop = 0,
        deleteCommentActive = 0,
        currentpageid = '';
(function() { // START NAMESPACE
  var $ = 'id' in document ? document.id : window.$;

  $(document).on('afterSMCoreInit', function(event, data) {
    sm4.activity.feedTabURL = sm4.core.baseUrl + sm4.activity.feedTabURL;
    sm4.activity.feedURL = sm4.core.baseUrl + sm4.activity.feedURL;
  });
  sm4.activity = {
    options: {
      allowEmptyWithoutAttachment: false,
      allowEmptyWithAttachment: true,
      hideSubmitOnBlur: true,
      submitElement: false,
      useContentEditable: true
    },
    advfeed_array: {},
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
          e.preventDefault();

          $(this).trigger('editorSubmit');

          if (!$this.options.allowEmptyWithAttachment && $this.getContent() == '') {
            e.preventDefault();
            return;
          } else {
            if (!$this.options.allowEmptyWithoutAttachment && $this.getContent() == '') {
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
        $('#activitypost-container-temp').find('textarea').focus();
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
    activityremove: function(e, comment_id, action_id) {
      deleteCommentActive = true;
      if ($.type(e) != 'undefined' && $.type($(e) == 'object')) {
        feedElement = $(e);
        var commentinfo = feedElement.data('message').split('-');
        if (commentinfo[0] == 0) {
          $.mobile.activePage.find('#popupDialog').popup("open");
          $.mobile.activePage.find('#popupDialog').parent().css('z-index', '11000')
          $.mobile.activePage.find('#popupDialog').popup("open");
        }
        else {
          $.mobile.activePage.find('#popupDialog-Comment').parent().css('z-index', '11000')
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
          'format': 'html'
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
    like_unlikeFeed: function(action, action_id, comment_id, self) {

      if (action == 'like') {

        //MAKE LIKE CHANGE TO UNLIKE FIRST AND THEN SEND AJAX REQUEST:
        if ($.type($.mobile.activePage.find('#activity-item-' + action_id).find('.feed_item_btm a.feed_likes').get(0)) != 'undefined') {
          var likespan = $.trim($.mobile.activePage.find('#activity-item-' + action_id + ' .feed_item_btm a.feed_likes span').html()).split(' ');
          $.mobile.activePage.find('#activity-item-' + action_id + ' .feed_item_btm a.feed_likes span').html(sm4.core.language.translate('% like', parseInt(likespan[0]) + parseInt(1)));

        }
        else {

          if (typeof like_commentURL == 'undefined') {
            var likeCountHtml = '<span class="sep">-</span><a href="javascript:void(0);" onclick="sm4.activity.openPopup(\'' + self.data('message') + '\', \'feedsharepopup\')" class="feed_likes"><span>' + sm4.core.language.translate('% like', 1) + '</span></a>'
            self.attr('onclick', "javascript:sm4.activity.unlike(\'" + action_id + "\', \'\',$(this) )");
          }
          else {
            var likeCountHtml = '<span class="sep">-</span><a href="javascript:void(0);" onclick="sm4.activity.openPopup(\'' + like_commentURL + '/action_id/' + action_id + '\', \'feedsharepopup\')" class="feed_likes"><span>' + sm4.core.language.translate('% like', 1) + '</span></a>';

            $.mobile.activePage.find('#main-feed-' + action_id + ' .feed_item_option .ui-block-a a').attr('onclick', 'javascript:sm4.activity.unlike(' + action_id + ');');

          }

          $.mobile.activePage.find('#activity-item-' + action_id).find('.feed_item_btm .feed_item_date').after($(likeCountHtml));

        }

        $.mobile.activePage.find('#main-feed-' + action_id + ' .feed_item_option .ui-block-a a').html('<i class="ui-icon ui-icon-thumbs-down"></i> <span>' + sm4.core.language.translate('Unlike') + '</span>');

      }
      else {
        var likespan = $.trim($.mobile.activePage.find('#activity-item-' + action_id + ' .feed_item_btm a.feed_likes span').html()).split(' ');
        if ((parseInt(likespan[0]) - parseInt(1)) > 0)
          $.mobile.activePage.find('#activity-item-' + action_id + ' .feed_item_btm a.feed_likes span').html((parseInt(likespan[0]) - parseInt(1)) + ' likes');

        else {
          $.mobile.activePage.find('#activity-item-' + action_id + ' .feed_item_btm a.feed_likes').prev().remove();
          $.mobile.activePage.find('#activity-item-' + action_id + ' .feed_item_btm a.feed_likes').remove();
        }

        $.mobile.activePage.find('#main-feed-' + action_id + ' .feed_item_option .ui-block-a a').attr('onclick', "javascript:sm4.activity.like(\'" + action_id + "\', \'\',$(this))");

        $.mobile.activePage.find('#main-feed-' + action_id + ' .feed_item_option .ui-block-a a').html('<i class="ui-icon ui-icon-thumbs-up"></i>&nbsp;<span>' + sm4.core.language.translate('Like') + '</span>');

      }

      sm4.core.dloader.refreshPage();

    },
    like_unlikeComment: function(action, action_id, comment_id) {

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

        $('#comment-' + comment_id + ' .comment_likes').html(sm4.core.language.translate('unlike'));

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

        $('#comment-' + comment_id + ' .comment_likes').html(sm4.core.language.translate('like'));

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
    comment: function(action_id, body) {
      if (body.trim() == '') {
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
          'subject': $.mobile.activePage.advfeed_array.subject_guid,
          'format': 'json'
        },
        success: function(responseJSON, textStatus, xhr) {
          var li = $('<li />', {
            'id': 'comment-' + responseJSON.comment_id,
            'html': sm4.core.mobiPageHTML(responseJSON.body)

          }).inject($('#showhide-comments-' + action_id).find('ul'));
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
        }
      }
      );

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
    attachComment: function(formElement) {
      var bind = this;

      formElement.attr('data-ajax', 'false');
      formElement.css('display', 'block');
      bind.comment($("[name='action_id']", formElement).val(), $("[name='body']", formElement).val());
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
          'format': 'json'
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
          'page': page
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
    doOnScrollLoadActivity: function(feedtype) {
      feedtype = this.feedType;
      if ($.type($.mobile.activePage.advfeed_array) == 'undefined')
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
    addFriend: function(self) {
      var container = $(self).parent();
      container.html('<i class="ui-icon ui-icon-spinner icon-spin ui-icon"></i>');
      $.ajax({
        type: "POST",
        dataType: "json",
        url: self.href,
        data: {
          'format': 'json',
          'type': 'json'
        },
        success: function(responseJSON, textStatus, xhr) {
          container.html(sm4.core.mobiPageHTML(responseJSON.body));
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
      $.ajax({
        type: "POST",
        dataType: "json",
        url: this.feedURL,
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
          //$('#activitypost-container-temp').remove();

          $.mobile.activePage.find('#activitypost-container-temp').remove();
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


      if ($.type($.mobile.activePage.advfeed_array) != 'undefined') {

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
    openPopup: function(Url, popupid) {
      parentScrollTop = $(window).scrollTop();
      if (!document.location.search.length) {
        //window.location.hash='#&ui-state=dialogcomment';
      }
      $.mobile.activePage.popupid = popupid;
      $('.ui-page-active').addClass('pop_back_max_height');
      if ($.type($('#' + popupid).get(0)) != 'undefined')
        $('#' + popupid).remove();
      var $popup = $('<div id= "' + popupid + '" class="sm-ui-popup ui-body-c"  style="display:block">' + "<div class='ps-close-popup'></div><div class='ps-carousel-comments sm-ui-popup' ><div class='ps_loading sm-ui-popup-loading'></div> </div></div>" + '</div>');
      $('body').append($popup);

      $.ajax({
        type: "GET",
        dataType: "html",
        url: Url,
        data: {
          'format': 'html',
          'popupid': ''

        },
        success: function(responseHTML, textStatus, xhr) {
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
      $('.ui-page-active').removeClass('pop_back_max_height');
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
            setTimeout(function() {
              new IScroll($this, {
                scrollX: true,
                scrollY: false,
                momentum: false,
                snap: true,
                snapSpeed: 400,
                keyBindings: true
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
    activityViewMore: function(feedTaburl, feedtype) {
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
            if (feedtype == 'sitefeed')
              sm4.activity.activityUpdateHandler.scrollRefresh();
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
      $('.checkin-label').on('click', function(e) {
        e.preventDefault();
        if ($(this).html() == sm4.core.language.translate('Cancel')) {
          proceed_request = false;
          $(this).html(sm4.core.language.translate('Search'));
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
          //noResults: '',
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


          var termss = sm4.core.Module.autoCompleter.split(request.term);
          // remove the current input
          request.term = termss[termss.length - 1];
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
    $('#activitypost-container-temp').find('.composer_status_share_options').toggle();
    hidePrivacyIconClickEnable = true;
  },
  toggleEmotions: function(self) {
    $('#activitypost-container-temp').find('#emoticons-board').toggle();
    hideEmotionIconClickEnable = true;
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
  if ($('.ui-page-active').hasClass('pop_back_max_height')) {
    $('.ui-page-active').removeClass('pop_back_max_height');
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
        self.pageOptions[self.getIndexId()].scroll = self.iScrollEvent();
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
    if (!this.hasCheckUpdates())
      return;
    if (this.pageOptions[this.getIndexId()].scroll) {
      this.pageOptions[this.getIndexId()].scroll.refresh();
    }
  },
  scrollToElement: function(el, duration) {
    var options = this.pageOptions[this.getIndexId()];
    if (!options)
      return;
    var scroll = options.scroll;
    if (!scroll)
      return;
    if (!el && scroll.siblingpullDownEl) {
      el = scroll.siblingpullDownEl;
    }
    el = el.nodeType ? el : $.mobile.activePage.find(el)[0];
    duration = duration ? duration : 10;
    if (!el)
      return;
    scroll.isScrollToElement = true;
    scroll.scrollToElement(el, duration);
  },
  iScrollEvent: function() {
    var self = this, scroll,
            wrapper = $.mobile.activePage.find('[data-role="wrapper"]'),
            scroller = $.mobile.activePage.find('[data-role="scroller"]'),
            pullDownEl = $.mobile.activePage.find('.sm_aaf_pullDown'),
            //  pullUpEl = $.mobile.activePage.find('.feed_viewmore'),
            composerWrapper = $.mobile.activePage.find('.layout_sitemobile_sitemobile_advfeed [data-role="composer-wrapper"]'),
            headerHeight = 0, footerHeight = 0, composerWrapperHeight = 0, pullDownOffset = 0, scrollStart = 0,
            header = $.mobile.activePage.find('[data-role="header"]'),
            footer = $.mobile.activePage.find('[data-role="footer"]');
    if (wrapper.find('.layout_page_user_index_home').length <= 0) {
      composerWrapper = {};
    }
    if (header.length > 0) {
      headerHeight = header.outerHeight();
    }
    if (footer.length > 0) {
      footerHeight = footer.outerHeight();
    }
    if (composerWrapper.length > 0) {
      wrapper.before(composerWrapper);
      composerWrapperHeight = composerWrapper.outerHeight()+10;
      headerHeight = composerWrapperHeight + headerHeight;
      composerWrapper.css({
        position: 'relative',
        zIndex: '0',
        top: 0 + 'px'
      });
    }
    scroller.prepend(pullDownEl);
    pullDownEl.removeClass('dnone');
    pullDownOffset = pullDownEl.outerHeight();
    wrapper.css({
      position: 'absolute',
      zIndex: '1',
      top: (headerHeight) + 'px',
      bottom: footerHeight + 'px',
      left: '5px',
      right: '5px',
      overflow: 'hidden'
    });
    var pullDownAction = function() {
      self.getFeedUpdate(scroll);

    };
    var startScrollBack = 0, previosPos = 0;
    scroll = new IScroll(wrapper[0], {
      probeType: 2,
      bounceEasing: 'elastic', bounceTime: 1000,
      preventDefaultException: {
        tagName: /^(INPUT|TEXTAREA|BUTTON|SELECT|X-WIDGET)$/,
        accessKey: /.+/
      }
    });
    scroll.siblingpullDownEl = pullDownEl.next()[0];
    scroll.isScrollToElement = false;
    scroll.on('refresh', function() {
      previosPos = scroll.y;
      if (!this.hasVerticalScroll) {
        pullDownEl.addClass('dnone');
      } else {
        pullDownEl.removeClass('dnone');
      }
      setTimeout(function() {
        if (scroll.y + pullDownOffset > 0 && !scroll.isScrollToElement)
          scroll.scrollToElement(scroll.siblingpullDownEl, 1000);
      }, 500);
      if (pullDownEl.hasClass('loading')) {
        pullDownEl.removeClass('loading');
        pullDownEl.find('.pullDownLabel').removeClass('dnone');
        pullDownEl.find('.pullDownLabelRelease').addClass('dnone');
        pullDownEl.find('.pullDownLabelLoading').addClass('dnone');
      }
    });
    scroll.on('scroll', function() {
      if (this.y > 5 && !pullDownEl.hasClass('flip')) {
        pullDownEl.addClass('flip');
        pullDownEl.find('.pullDownLabel').addClass('dnone');
        pullDownEl.find('.pullDownLabelRelease').removeClass('dnone');
        pullDownEl.find('.pullDownLabelLoading').addClass('dnone');
        this.minScrollY = 0;
      } else if (this.y < 5 && pullDownEl.hasClass('flip')) {
        pullDownEl.removeClass('flip');
        pullDownEl.find('.pullDownLabel').removeClass('dnone');
        pullDownEl.find('.pullDownLabelRelease').addClass('dnone');
        pullDownEl.find('.pullDownLabelLoading').addClass('dnone');
        // pullDownEl.querySelector('.pullDownLabel').innerHTML = 'Pull down to refresh...';
        this.minScrollY = -pullDownOffset;
      } else if (this.y < (this.maxScrollY + 400)) {
        if ($.mobile.activePage.advfeed_array.next_id > 0 && $.mobile.activePage.advfeed_array.endOfFeed == false) {
          sm4.activity.activityViewMore(sm4.core.baseUrl + 'widget/index/name/sitemobile.sitemobile-advfeed', 'sitefeed');

        }	// Execute custom function (ajax call?)

        setTimeout(function() {
          scroll.refresh();
        }, 5000);
      }
      if (this.y < 0 && composerWrapperHeight > 0) {
        var y = 0, zindex = 0;
        if ((composerWrapperHeight + 100) > -(this.y)) {
          y = 0;
          zindex = 0;
          composerWrapper.css({
            top: 0 + 'px'
          });
        } else if (previosPos < this.y) {
          y = -(composerWrapperHeight);
          zindex = 6;
          composerWrapper.css({
            top: 0 + 'px'
          });
        } else {
          y = -(composerWrapperHeight);
          composerWrapper.css({
            top: y + 'px'
          });
        }
        composerWrapper.css({
          zIndex: zindex
        });
        wrapper.css({
          top: (headerHeight + y) + 'px'
        });
      }
      previosPos = this.y;
    });
    scroll.on('scrollEnd', function() {
      if (pullDownEl.hasClass('flip')) {
        pullDownEl.find('.pullDownLabel').addClass('dnone');
        pullDownEl.find('.pullDownLabelRelease').addClass('dnone');
        pullDownEl.find('.pullDownLabelLoading').removeClass('dnone');
        pullDownEl.addClass('loading');
        pullDownAction();// Execute custom function (ajax call?)
      } else if (this.y < (this.maxScrollY + 400)) {
        // pullUpEl.className = 'loading';
        // pullUpEl.querySelector('.pullUpLabel').innerHTML = 'Loading...';				
        if ($.mobile.activePage.advfeed_array.next_id > 0 && $.mobile.activePage.advfeed_array.endOfFeed == false) {
          sm4.activity.activityViewMore(sm4.core.baseUrl + 'widget/index/name/sitemobile.sitemobile-advfeed', 'sitefeed');

        }	// Execute custom function (ajax call?)

        setTimeout(function() {
          scroll.refresh();
        }, 5000);
      }
//      if (this.y < 0 && composerWrapperHeight > 0) {
//      var y = 0, zindex = 0;
//
//        if (composerWrapperHeight > -(this.y) && previosPos > this.y) {
//          y = 0;
//          zindex = 6;
//        } else {
//          y = -(composerWrapperHeight);
//          zindex = 0;
//        }
//
//        composerWrapper.css({
//          zIndex: zindex
//        });
//        wrapper.css({
//          top: (headerHeight + composerWrapperHeight + y) + 'px'
//        });
//      }
      if (this.y + pullDownOffset > 0 && !this.isScrollToElement) {
        setTimeout(function() {
          if (scroll.y + pullDownOffset > 0 && !scroll.isScrollToElement)
            scroll.scrollToElement(scroll.siblingpullDownEl, 1000);
        }, 500);
      } else {
        this.isScrollToElement = false;
      }

      this.isScrollToElement = false;
      previosPos = this.y;
    });
    scroll.scrollToElement(scroll.siblingpullDownEl);
    return scroll;
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
  getFeedUpdate: function(scroll) {
    if (this.getUpdateActive)
      return;
    scroll = this.pageOptions[this.getIndexId()].scroll;
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
        if (scroll) {
          scroll.refresh();
          setTimeout(function() {
            // scroll.scrollToElement($.mobile.activePage.find("#aaf_feed_update_loading")[0],1500);
            self.scrollToElement();
          }, 1500);
        }
        try {
          if (responseHTML) {
            $.mobile.activePage.find('#activity-feed-sitefeed').prepend(responseHTML);
            $.mobile.activePage.find('#activity-feed-sitefeed').trigger('create');
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
        setEmoticonsBoard = function() {
  //   if(composeInstance)
  //    composeInstance.focus();
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
        //hide on body click
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
  if (!hidePrivacyIconClickEnable && $('.composer_status_share_options') && $('.composer_status_share_options').css('display') === 'block') {
    $('.composer_status_share_options').css('display', 'none');
  }
  hidePrivacyIconClickEnable = false;
};

//----------------------------------------------------------------------------------------
/* JS Name 
 *   composer.js start here
 */

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
$(document).on('afterSMCoreInit', function(event, data) {
  sm4.activity.composer.checkin.options.suggestOptions.url = sm4.core.baseUrl + sm4.activity.composer.checkin.options.suggestOptions.url

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
    if (plugin != 'checkin' && plugin != 'addpeople')
      $('#activitypost-container-temp', sm4.activity.composer.content).find('#composer-options').hide();

    if (plugin) {
      this.activate(plugin);
    }
  },
  activate: function(plugin) {
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

    this.composePlugin.activate();
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
      else
        $('#activitypost-container-temp', sm4.activity.composer.content).find('.compose_buttons').before(this.getTray());
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

      var image = $this.elements.loadingImage = $this.elements.loadingImage || ($('<img />', {
        'id': 'compose-' + this.getName() + '-loading-image',
        'class': 'compose-loading-image'
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
      var form = sm4.activity.getForm();

      plugin.elements.inputarea = $('<div />', {
        'css': {
          'display': 'none'
        }
      });
      form.append(plugin.elements.inputarea);
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
        'url': 'sitetagcheckin/checkin/suggest',
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
      this.elements = {};
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
      this.elements.checkinbutton = $('<button />', {
        'class': 'checkin-label ui-shadow ui-input-button ui-corner-all',
        'data-role': 'none',
        'html': sm4.core.language.translate('Search')
      }).inject(element_5);

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
      if (location.latitude == undefined) {
        $('#activitypost-container-temp').find('#compose-submit').parent('div').css('display', 'none');
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
            $('#activitypost-container-temp', sm4.activity.composer.content).find('#compose-submit').parent('div').css('display', 'block');
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
    activate: function() {

      var addLinkBefore = $('#adv_post_container_tagging', sm4.activity.composer.content);
      addLinkBefore.prevAll().css('display', 'none');
      addLinkBefore.css('display', 'block');
      addLinkBefore.next().css('display', 'block');
      $('#ui-header', sm4.activity.composer.content).css('display', 'none');
      $('#ui-header-addpeople', sm4.activity.composer.content).css('display', 'block');

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
        'search': 'search'
      }, 'toValues-temp');



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
        'html': ('To upload a video from your computer, please use our <a href="/videos/create/type/3">full uploader</a>.'),
        'style': 'display:none;'
      });
      this.elements.body.append(this.elements.previewDescription);

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
          if ($.type($('#activitypost-container-temp', sm4.activity.composer.content).find('#compose-photo-body').children('#advfeed-photo').get(0)) == 'undefined') {
            $('#activitypost-container-temp', sm4.activity.composer.content).find('#compose-photo-body').html(sm4.core.language.translate('Invalid Upload'));
          }
          else {
            sm4.activity.getForm().append(sm4.activity.composer.getInputArea($this).html($('#activitypost-container-temp', sm4.activity.composer.content).find('#compose-photo-body').children('#advfeed-photo')));
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

      $.mobile.showPageLoadingMsg($.mobile.pageLoadErrorMessageTheme, message, true);
      setTimeout(function() {
          $.mobile.loading().loader("hide");
//        $.mobile.hidePageLoadingMsg();
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
        $.mobile.showPageLoadingMsg($.mobile.pageLoadErrorMessageTheme, message, true);
        setTimeout(function() {
            $.mobile.loading().loader("hide");
//          $.mobile.hidePageLoadingMsg();
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
      url = appconfig.siteInfo.baseHref + url.replace(appconfig.siteInfo.baseUrl, '');
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
      $.mobile.showPageLoadingMsg($.mobile.pageLoadErrorMessageTheme, message, true);
      setTimeout(function() {
          $.mobile.loading().loader("hide");
//        $.mobile.hidePageLoadingMsg();
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
        var pagesubject = sm4.activity.composer.content('#subject').val();
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

//----------------------------------------------------------------------------------------
/* JS Name 
 *   jquery.form.js start here
 */
/*!
 * jQuery Form Plugin
 * version: 3.40.0-2013.08.13
 * @requires jQuery v1.5 or later
 * Copyright (c) 2013 M. Alsup
 * Examples and documentation at: http://malsup.com/jquery/form/
 * Project repository: https://github.com/malsup/form
 * Dual licensed under the MIT and GPL licenses.
 * https://github.com/malsup/form#copyright-and-license
 */
/*global ActiveXObject */
;(function($) {
"use strict";

/*
    Usage Note:
    -----------
    Do not use both ajaxSubmit and ajaxForm on the same form.  These
    functions are mutually exclusive.  Use ajaxSubmit if you want
    to bind your own submit handler to the form.  For example,

    $(document).ready(function() {
        $('#myForm').on('submit', function(e) {
            e.preventDefault(); // <-- important
            $(this).ajaxSubmit({
                target: '#output'
            });
        });
    });

    Use ajaxForm when you want the plugin to manage all the event binding
    for you.  For example,

    $(document).ready(function() {
        $('#myForm').ajaxForm({
            target: '#output'
        });
    });

    You can also use ajaxForm with delegation (requires jQuery v1.7+), so the
    form does not have to exist when you invoke ajaxForm:

    $('#myForm').ajaxForm({
        delegation: true,
        target: '#output'
    });

    When using ajaxForm, the ajaxSubmit function will be invoked for you
    at the appropriate time.
*/

/**
 * Feature detection
 */
var feature = {};
feature.fileapi = $("<input type='file'/>").get(0).files !== undefined;
feature.formdata = window.FormData !== undefined;

var hasProp = !!$.fn.prop;

// attr2 uses prop when it can but checks the return type for
// an expected string.  this accounts for the case where a form 
// contains inputs with names like "action" or "method"; in those
// cases "prop" returns the element
$.fn.attr2 = function() {
    if ( ! hasProp )
        return this.attr.apply(this, arguments);
    var val = this.prop.apply(this, arguments);
    if ( ( val && val.jquery ) || typeof val === 'string' )
        return val;
    return this.attr.apply(this, arguments);
};

/**
 * ajaxSubmit() provides a mechanism for immediately submitting
 * an HTML form using AJAX.
 */
$.fn.ajaxSubmit = function(options) {
    /*jshint scripturl:true */

    // fast fail if nothing selected (http://dev.jquery.com/ticket/2752)
    if (!this.length) {
        log('ajaxSubmit: skipping submit process - no element selected');
        return this;
    }

    var method, action, url, $form = this;

    if (typeof options == 'function') {
        options = { success: options };
    }
    else if ( options === undefined ) {
        options = {};
    }

    method = options.type || this.attr2('method');
    action = options.url  || this.attr2('action');

    url = (typeof action === 'string') ? $.trim(action) : '';
    url = url || window.location.href || '';
    if (url) {
        // clean url (don't include hash vaue)
        url = (url.match(/^([^#]+)/)||[])[1];
    }

    options = $.extend(true, {
        url:  url,
        success: $.ajaxSettings.success,
        type: method || $.ajaxSettings.type,
        iframeSrc: /^https/i.test(window.location.href || '') ? 'javascript:false' : 'about:blank'
    }, options);

    // hook for manipulating the form data before it is extracted;
    // convenient for use with rich editors like tinyMCE or FCKEditor
    var veto = {};
    this.trigger('form-pre-serialize', [this, options, veto]);
    if (veto.veto) {
        log('ajaxSubmit: submit vetoed via form-pre-serialize trigger');
        return this;
    }

    // provide opportunity to alter form data before it is serialized
    if (options.beforeSerialize && options.beforeSerialize(this, options) === false) {
        log('ajaxSubmit: submit aborted via beforeSerialize callback');
        return this;
    }

    var traditional = options.traditional;
    if ( traditional === undefined ) {
        traditional = $.ajaxSettings.traditional;
    }

    var elements = [];
    var qx, a = this.formToArray(options.semantic, elements);
    if (options.data) {
        options.extraData = options.data;
        qx = $.param(options.data, traditional);
    }

    // give pre-submit callback an opportunity to abort the submit
    if (options.beforeSubmit && options.beforeSubmit(a, this, options) === false) {
        log('ajaxSubmit: submit aborted via beforeSubmit callback');
        return this;
    }

    // fire vetoable 'validate' event
    this.trigger('form-submit-validate', [a, this, options, veto]);
    if (veto.veto) {
        log('ajaxSubmit: submit vetoed via form-submit-validate trigger');
        return this;
    }

    var q = $.param(a, traditional);
    if (qx) {
        q = ( q ? (q + '&' + qx) : qx );
    }
    if (options.type.toUpperCase() == 'GET') {
        options.url += (options.url.indexOf('?') >= 0 ? '&' : '?') + q;
        options.data = null;  // data is null for 'get'
    }
    else {
        options.data = q; // data is the query string for 'post'
    }

    var callbacks = [];
    if (options.resetForm) {
        callbacks.push(function() { $form.resetForm(); });
    }
    if (options.clearForm) {
        callbacks.push(function() { $form.clearForm(options.includeHidden); });
    }

    // perform a load on the target only if dataType is not provided
    if (!options.dataType && options.target) {
        var oldSuccess = options.success || function(){};
        callbacks.push(function(data) {
            var fn = options.replaceTarget ? 'replaceWith' : 'html';
            $(options.target)[fn](data).each(oldSuccess, arguments);
        });
    }
    else if (options.success) {
        callbacks.push(options.success);
    }

    options.success = function(data, status, xhr) { // jQuery 1.4+ passes xhr as 3rd arg
        var context = options.context || this ;    // jQuery 1.4+ supports scope context
        for (var i=0, max=callbacks.length; i < max; i++) {
            callbacks[i].apply(context, [data, status, xhr || $form, $form]);
        }
    };

    if (options.error) {
        var oldError = options.error;
        options.error = function(xhr, status, error) {
            var context = options.context || this;
            oldError.apply(context, [xhr, status, error, $form]);
        };
    }

     if (options.complete) {
        var oldComplete = options.complete;
        options.complete = function(xhr, status) {
            var context = options.context || this;
            oldComplete.apply(context, [xhr, status, $form]);
        };
    }

    // are there files to upload?

    // [value] (issue #113), also see comment:
    // https://github.com/malsup/form/commit/588306aedba1de01388032d5f42a60159eea9228#commitcomment-2180219
    var fileInputs = $('input[type=file]:enabled:not([value=""])', this);

    var hasFileInputs = fileInputs.length > 0;
    var mp = 'multipart/form-data';
    var multipart = ($form.attr('enctype') == mp || $form.attr('encoding') == mp);

    var fileAPI = feature.fileapi && feature.formdata;
    log("fileAPI :" + fileAPI);
    var shouldUseFrame = (hasFileInputs || multipart) && !fileAPI;

    var jqxhr;

    // options.iframe allows user to force iframe mode
    // 06-NOV-09: now defaulting to iframe mode if file input is detected
    if (options.iframe !== false && (options.iframe || shouldUseFrame)) {
        // hack to fix Safari hang (thanks to Tim Molendijk for this)
        // see:  http://groups.google.com/group/jquery-dev/browse_thread/thread/36395b7ab510dd5d
        if (options.closeKeepAlive) {
            $.get(options.closeKeepAlive, function() {
                jqxhr = fileUploadIframe(a);
            });
        }
        else {
            jqxhr = fileUploadIframe(a);
        }
    }
    else if ((hasFileInputs || multipart) && fileAPI) {
        jqxhr = fileUploadXhr(a);
    }
    else {
        jqxhr = $.ajax(options);
    }

    $form.removeData('jqxhr').data('jqxhr', jqxhr);

    // clear element array
    for (var k=0; k < elements.length; k++)
        elements[k] = null;

    // fire 'notify' event
    this.trigger('form-submit-notify', [this, options]);
    return this;

    // utility fn for deep serialization
    function deepSerialize(extraData){
        var serialized = $.param(extraData, options.traditional).split('&');
        var len = serialized.length;
        var result = [];
        var i, part;
        for (i=0; i < len; i++) {
            // #252; undo param space replacement
            serialized[i] = serialized[i].replace(/\+/g,' ');
            part = serialized[i].split('=');
            // #278; use array instead of object storage, favoring array serializations
            result.push([decodeURIComponent(part[0]), decodeURIComponent(part[1])]);
        }
        return result;
    }

     // XMLHttpRequest Level 2 file uploads (big hat tip to francois2metz)
    function fileUploadXhr(a) {
        var formdata = new FormData();

        for (var i=0; i < a.length; i++) {
            formdata.append(a[i].name, a[i].value);
        }

        if (options.extraData) {
            var serializedData = deepSerialize(options.extraData);
            for (i=0; i < serializedData.length; i++)
                if (serializedData[i])
                    formdata.append(serializedData[i][0], serializedData[i][1]);
        }

        options.data = null;

        var s = $.extend(true, {}, $.ajaxSettings, options, {
            contentType: false,
            processData: false,
            cache: false,
            type: method || 'POST'
        });

        if (options.uploadProgress) {
            // workaround because jqXHR does not expose upload property
            s.xhr = function() {
                var xhr = $.ajaxSettings.xhr();
                if (xhr.upload) {
                    xhr.upload.addEventListener('progress', function(event) {
                        var percent = 0;
                        var position = event.loaded || event.position; /*event.position is deprecated*/
                        var total = event.total;
                        if (event.lengthComputable) {
                            percent = Math.ceil(position / total * 100);
                        }
                        options.uploadProgress(event, position, total, percent);
                    }, false);
                }
                return xhr;
            };
        }

        s.data = null;
            var beforeSend = s.beforeSend;
            s.beforeSend = function(xhr, o) {
                o.data = formdata;
                if(beforeSend)
                    beforeSend.call(this, xhr, o);
        };
        return $.ajax(s);
    }

    // private function for handling file uploads (hat tip to YAHOO!)
    function fileUploadIframe(a) {
        var form = $form[0], el, i, s, g, id, $io, io, xhr, sub, n, timedOut, timeoutHandle;
        var deferred = $.Deferred();

        // #341
        deferred.abort = function(status) {
            xhr.abort(status);
        };

        if (a) {
            // ensure that every serialized input is still enabled
            for (i=0; i < elements.length; i++) {
                el = $(elements[i]);
                if ( hasProp )
                    el.prop('disabled', false);
                else
                    el.removeAttr('disabled');
            }
        }

        s = $.extend(true, {}, $.ajaxSettings, options);
        s.context = s.context || s;
        id = 'jqFormIO' + (new Date().getTime());
        if (s.iframeTarget) {
            $io = $(s.iframeTarget);
            n = $io.attr2('name');
            if (!n)
                 $io.attr2('name', id);
            else
                id = n;
        }
        else {
            $io = $('<iframe name="' + id + '" src="'+ s.iframeSrc +'" />');
            $io.css({ position: 'absolute', top: '-1000px', left: '-1000px' });
        }
        io = $io[0];


        xhr = { // mock object
            aborted: 0,
            responseText: null,
            responseXML: null,
            status: 0,
            statusText: 'n/a',
            getAllResponseHeaders: function() {},
            getResponseHeader: function() {},
            setRequestHeader: function() {},
            abort: function(status) {
                var e = (status === 'timeout' ? 'timeout' : 'aborted');
                log('aborting upload... ' + e);
                this.aborted = 1;

                try { // #214, #257
                    if (io.contentWindow.document.execCommand) {
                        io.contentWindow.document.execCommand('Stop');
                    }
                }
                catch(ignore) {}

                $io.attr('src', s.iframeSrc); // abort op in progress
                xhr.error = e;
                if (s.error)
                    s.error.call(s.context, xhr, e, status);
                if (g)
                    $.event.trigger("ajaxError", [xhr, s, e]);
                if (s.complete)
                    s.complete.call(s.context, xhr, e);
            }
        };

        g = s.global;
        // trigger ajax global events so that activity/block indicators work like normal
        if (g && 0 === $.active++) {
            $.event.trigger("ajaxStart");
        }
        if (g) {
            $.event.trigger("ajaxSend", [xhr, s]);
        }

        if (s.beforeSend && s.beforeSend.call(s.context, xhr, s) === false) {
            if (s.global) {
                $.active--;
            }
            deferred.reject();
            return deferred;
        }
        if (xhr.aborted) {
            deferred.reject();
            return deferred;
        }

        // add submitting element to data if we know it
        sub = form.clk;
        if (sub) {
            n = sub.name;
            if (n && !sub.disabled) {
                s.extraData = s.extraData || {};
                s.extraData[n] = sub.value;
                if (sub.type == "image") {
                    s.extraData[n+'.x'] = form.clk_x;
                    s.extraData[n+'.y'] = form.clk_y;
                }
            }
        }

        var CLIENT_TIMEOUT_ABORT = 1;
        var SERVER_ABORT = 2;
                
        function getDoc(frame) {
            /* it looks like contentWindow or contentDocument do not
             * carry the protocol property in ie8, when running under ssl
             * frame.document is the only valid response document, since
             * the protocol is know but not on the other two objects. strange?
             * "Same origin policy" http://en.wikipedia.org/wiki/Same_origin_policy
             */
            
            var doc = null;
            
            // IE8 cascading access check
            try {
                if (frame.contentWindow) {
                    doc = frame.contentWindow.document;
                }
            } catch(err) {
                // IE8 access denied under ssl & missing protocol
                log('cannot get iframe.contentWindow document: ' + err);
            }

            if (doc) { // successful getting content
                return doc;
            }

            try { // simply checking may throw in ie8 under ssl or mismatched protocol
                doc = frame.contentDocument ? frame.contentDocument : frame.document;
            } catch(err) {
                // last attempt
                log('cannot get iframe.contentDocument: ' + err);
                doc = frame.document;
            }
            return doc;
        }

        // Rails CSRF hack (thanks to Yvan Barthelemy)
        var csrf_token = $('meta[name=csrf-token]').attr('content');
        var csrf_param = $('meta[name=csrf-param]').attr('content');
        if (csrf_param && csrf_token) {
            s.extraData = s.extraData || {};
            s.extraData[csrf_param] = csrf_token;
        }

        // take a breath so that pending repaints get some cpu time before the upload starts
        function doSubmit() {
            // make sure form attrs are set
            var t = $form.attr2('target'), a = $form.attr2('action');

            // update form attrs in IE friendly way
            form.setAttribute('target',id);
            if (!method) {
                form.setAttribute('method', 'POST');
            }
            if (a != s.url) {
                form.setAttribute('action', s.url);
            }

            // ie borks in some cases when setting encoding
            if (! s.skipEncodingOverride && (!method || /post/i.test(method))) {
                $form.attr({
                    encoding: 'multipart/form-data',
                    enctype:  'multipart/form-data'
                });
            }

            // support timout
            if (s.timeout) {
                timeoutHandle = setTimeout(function() { timedOut = true; cb(CLIENT_TIMEOUT_ABORT); }, s.timeout);
            }

            // look for server aborts
            function checkState() {
                try {
                    var state = getDoc(io).readyState;
                    log('state = ' + state);
                    if (state && state.toLowerCase() == 'uninitialized')
                        setTimeout(checkState,50);
                }
                catch(e) {
                    log('Server abort: ' , e, ' (', e.name, ')');
                    cb(SERVER_ABORT);
                    if (timeoutHandle)
                        clearTimeout(timeoutHandle);
                    timeoutHandle = undefined;
                }
            }

            // add "extra" data to form if provided in options
            var extraInputs = [];
            try {
                if (s.extraData) {
                    for (var n in s.extraData) {
                        if (s.extraData.hasOwnProperty(n)) {
                           // if using the $.param format that allows for multiple values with the same name
                           if($.isPlainObject(s.extraData[n]) && s.extraData[n].hasOwnProperty('name') && s.extraData[n].hasOwnProperty('value')) {
                               extraInputs.push(
                               $('<input type="hidden" name="'+s.extraData[n].name+'">').val(s.extraData[n].value)
                                   .appendTo(form)[0]);
                           } else {
                               extraInputs.push(
                               $('<input type="hidden" name="'+n+'">').val(s.extraData[n])
                                   .appendTo(form)[0]);
                           }
                        }
                    }
                }

                if (!s.iframeTarget) {
                    // add iframe to doc and submit the form
                    $io.appendTo('body');
                    if (io.attachEvent)
                        io.attachEvent('onload', cb);
                    else
                        io.addEventListener('load', cb, false);
                }
                setTimeout(checkState,15);

                try {
                    form.submit();
                } catch(err) {
                    // just in case form has element with name/id of 'submit'
                    var submitFn = document.createElement('form').submit;
                    submitFn.apply(form);
                }
            }
            finally {
                // reset attrs and remove "extra" input elements
                form.setAttribute('action',a);
                if(t) {
                    form.setAttribute('target', t);
                } else {
                    $form.removeAttr('target');
                }
                $(extraInputs).remove();
            }
        }

        if (s.forceSync) {
            doSubmit();
        }
        else {
            setTimeout(doSubmit, 10); // this lets dom updates render
        }

        var data, doc, domCheckCount = 50, callbackProcessed;

        function cb(e) {
            if (xhr.aborted || callbackProcessed) {
                return;
            }
            
            doc = getDoc(io);
            if(!doc) {
                log('cannot access response document');
                e = SERVER_ABORT;
            }
            if (e === CLIENT_TIMEOUT_ABORT && xhr) {
                xhr.abort('timeout');
                deferred.reject(xhr, 'timeout');
                return;
            }
            else if (e == SERVER_ABORT && xhr) {
                xhr.abort('server abort');
                deferred.reject(xhr, 'error', 'server abort');
                return;
            }

            if (!doc || doc.location.href == s.iframeSrc) {
                // response not received yet
                if (!timedOut)
                    return;
            }
            if (io.detachEvent)
                io.detachEvent('onload', cb);
            else
                io.removeEventListener('load', cb, false);

            var status = 'success', errMsg;
            try {
                if (timedOut) {
                    throw 'timeout';
                }

                var isXml = s.dataType == 'xml' || doc.XMLDocument || $.isXMLDoc(doc);
                log('isXml='+isXml);
                if (!isXml && window.opera && (doc.body === null || !doc.body.innerHTML)) {
                    if (--domCheckCount) {
                        // in some browsers (Opera) the iframe DOM is not always traversable when
                        // the onload callback fires, so we loop a bit to accommodate
                        log('requeing onLoad callback, DOM not available');
                        setTimeout(cb, 250);
                        return;
                    }
                    // let this fall through because server response could be an empty document
                    //log('Could not access iframe DOM after mutiple tries.');
                    //throw 'DOMException: not available';
                }

                //log('response detected');
                var docRoot = doc.body ? doc.body : doc.documentElement;
                xhr.responseText = docRoot ? docRoot.innerHTML : null;
                xhr.responseXML = doc.XMLDocument ? doc.XMLDocument : doc;
                if (isXml)
                    s.dataType = 'xml';
                xhr.getResponseHeader = function(header){
                    var headers = {'content-type': s.dataType};
                    return headers[header.toLowerCase()];
                };
                // support for XHR 'status' & 'statusText' emulation :
                if (docRoot) {
                    xhr.status = Number( docRoot.getAttribute('status') ) || xhr.status;
                    xhr.statusText = docRoot.getAttribute('statusText') || xhr.statusText;
                }

                var dt = (s.dataType || '').toLowerCase();
                var scr = /(json|script|text)/.test(dt);
                if (scr || s.textarea) {
                    // see if user embedded response in textarea
                    var ta = doc.getElementsByTagName('textarea')[0];
                    if (ta) {
                        xhr.responseText = ta.value;
                        // support for XHR 'status' & 'statusText' emulation :
                        xhr.status = Number( ta.getAttribute('status') ) || xhr.status;
                        xhr.statusText = ta.getAttribute('statusText') || xhr.statusText;
                    }
                    else if (scr) {
                        // account for browsers injecting pre around json response
                        var pre = doc.getElementsByTagName('pre')[0];
                        var b = doc.getElementsByTagName('body')[0];
                        if (pre) {
                            xhr.responseText = pre.textContent ? pre.textContent : pre.innerText;
                        }
                        else if (b) {
                            xhr.responseText = b.textContent ? b.textContent : b.innerText;
                        }
                    }
                }
                else if (dt == 'xml' && !xhr.responseXML && xhr.responseText) {
                    xhr.responseXML = toXml(xhr.responseText);
                }

                try {
                    data = httpData(xhr, dt, s);
                }
                catch (err) {
                    status = 'parsererror';
                    xhr.error = errMsg = (err || status);
                }
            }
            catch (err) {
                log('error caught: ',err);
                status = 'error';
                xhr.error = errMsg = (err || status);
            }

            if (xhr.aborted) {
                log('upload aborted');
                status = null;
            }

            if (xhr.status) { // we've set xhr.status
                status = (xhr.status >= 200 && xhr.status < 300 || xhr.status === 304) ? 'success' : 'error';
            }

            // ordering of these callbacks/triggers is odd, but that's how $.ajax does it
            if (status === 'success') {
                if (s.success)
                    s.success.call(s.context, data, 'success', xhr);
                deferred.resolve(xhr.responseText, 'success', xhr);
                if (g)
                    $.event.trigger("ajaxSuccess", [xhr, s]);
            }
            else if (status) {
                if (errMsg === undefined)
                    errMsg = xhr.statusText;
                if (s.error)
                    s.error.call(s.context, xhr, status, errMsg);
                deferred.reject(xhr, 'error', errMsg);
                if (g)
                    $.event.trigger("ajaxError", [xhr, s, errMsg]);
            }

            if (g)
                $.event.trigger("ajaxComplete", [xhr, s]);

            if (g && ! --$.active) {
                $.event.trigger("ajaxStop");
            }

            if (s.complete)
                s.complete.call(s.context, xhr, status);

            callbackProcessed = true;
            if (s.timeout)
                clearTimeout(timeoutHandle);

            // clean up
            setTimeout(function() {
                if (!s.iframeTarget)
                    $io.remove();
                xhr.responseXML = null;
            }, 100);
        }

        var toXml = $.parseXML || function(s, doc) { // use parseXML if available (jQuery 1.5+)
            if (window.ActiveXObject) {
                doc = new ActiveXObject('Microsoft.XMLDOM');
                doc.async = 'false';
                doc.loadXML(s);
            }
            else {
                doc = (new DOMParser()).parseFromString(s, 'text/xml');
            }
            return (doc && doc.documentElement && doc.documentElement.nodeName != 'parsererror') ? doc : null;
        };
        var parseJSON = $.parseJSON || function(s) {
            /*jslint evil:true */
            return window['eval']('(' + s + ')');
        };

        var httpData = function( xhr, type, s ) { // mostly lifted from jq1.4.4

            var ct = xhr.getResponseHeader('content-type') || '',
                xml = type === 'xml' || !type && ct.indexOf('xml') >= 0,
                data = xml ? xhr.responseXML : xhr.responseText;

            if (xml && data.documentElement.nodeName === 'parsererror') {
                if ($.error)
                    $.error('parsererror');
            }
            if (s && s.dataFilter) {
                data = s.dataFilter(data, type);
            }
            if (typeof data === 'string') {
                if (type === 'json' || !type && ct.indexOf('json') >= 0) {
                    data = parseJSON(data);
                } else if (type === "script" || !type && ct.indexOf("javascript") >= 0) {
                    $.globalEval(data);
                }
            }
            return data;
        };

        return deferred;
    }
};

/**
 * ajaxForm() provides a mechanism for fully automating form submission.
 *
 * The advantages of using this method instead of ajaxSubmit() are:
 *
 * 1: This method will include coordinates for <input type="image" /> elements (if the element
 *    is used to submit the form).
 * 2. This method will include the submit element's name/value data (for the element that was
 *    used to submit the form).
 * 3. This method binds the submit() method to the form for you.
 *
 * The options argument for ajaxForm works exactly as it does for ajaxSubmit.  ajaxForm merely
 * passes the options argument along after properly binding events for submit elements and
 * the form itself.
 */
$.fn.ajaxForm = function(options) {
    options = options || {};
    options.delegation = options.delegation && $.isFunction($.fn.on);

    // in jQuery 1.3+ we can fix mistakes with the ready state
    if (!options.delegation && this.length === 0) {
        var o = { s: this.selector, c: this.context };
        if (!$.isReady && o.s) {
            log('DOM not ready, queuing ajaxForm');
            $(function() {
                $(o.s,o.c).ajaxForm(options);
            });
            return this;
        }
        // is your DOM ready?  http://docs.jquery.com/Tutorials:Introducing_$(document).ready()
        log('terminating; zero elements found by selector' + ($.isReady ? '' : ' (DOM not ready)'));
        return this;
    }

    if ( options.delegation ) {
        $(document)
            .off('submit.form-plugin', this.selector, doAjaxSubmit)
            .off('click.form-plugin', this.selector, captureSubmittingElement)
            .on('submit.form-plugin', this.selector, options, doAjaxSubmit)
            .on('click.form-plugin', this.selector, options, captureSubmittingElement);
        return this;
    }

    return this.ajaxFormUnbind()
        .bind('submit.form-plugin', options, doAjaxSubmit)
        .bind('click.form-plugin', options, captureSubmittingElement);
};

// private event handlers
function doAjaxSubmit(e) {
    /*jshint validthis:true */
    var options = e.data;
    if (!e.isDefaultPrevented()) { // if event has been canceled, don't proceed
        e.preventDefault();
        $(this).ajaxSubmit(options);
    }
}

function captureSubmittingElement(e) {
    /*jshint validthis:true */
    var target = e.target;
    var $el = $(target);
    if (!($el.is("[type=submit],[type=image]"))) {
        // is this a child element of the submit el?  (ex: a span within a button)
        var t = $el.closest('[type=submit]');
        if (t.length === 0) {
            return;
        }
        target = t[0];
    }
    var form = this;
    form.clk = target;
    if (target.type == 'image') {
        if (e.offsetX !== undefined) {
            form.clk_x = e.offsetX;
            form.clk_y = e.offsetY;
        } else if (typeof $.fn.offset == 'function') {
            var offset = $el.offset();
            form.clk_x = e.pageX - offset.left;
            form.clk_y = e.pageY - offset.top;
        } else {
            form.clk_x = e.pageX - target.offsetLeft;
            form.clk_y = e.pageY - target.offsetTop;
        }
    }
    // clear form vars
    setTimeout(function() { form.clk = form.clk_x = form.clk_y = null; }, 100);
}


// ajaxFormUnbind unbinds the event handlers that were bound by ajaxForm
$.fn.ajaxFormUnbind = function() {
    return this.unbind('submit.form-plugin click.form-plugin');
};

/**
 * formToArray() gathers form element data into an array of objects that can
 * be passed to any of the following ajax functions: $.get, $.post, or load.
 * Each object in the array has both a 'name' and 'value' property.  An example of
 * an array for a simple login form might be:
 *
 * [ { name: 'username', value: 'jresig' }, { name: 'password', value: 'secret' } ]
 *
 * It is this array that is passed to pre-submit callback functions provided to the
 * ajaxSubmit() and ajaxForm() methods.
 */
$.fn.formToArray = function(semantic, elements) {
    var a = [];
    if (this.length === 0) {
        return a;
    }

    var form = this[0];
    var els = semantic ? form.getElementsByTagName('*') : form.elements;
    if (!els) {
        return a;
    }

    var i,j,n,v,el,max,jmax;
    for(i=0, max=els.length; i < max; i++) {
        el = els[i];
        n = el.name;
        if (!n || el.disabled) {
            continue;
        }

        if (semantic && form.clk && el.type == "image") {
            // handle image inputs on the fly when semantic == true
            if(form.clk == el) {
                a.push({name: n, value: $(el).val(), type: el.type });
                a.push({name: n+'.x', value: form.clk_x}, {name: n+'.y', value: form.clk_y});
            }
            continue;
        }

        v = $.fieldValue(el, true);
        if (v && v.constructor == Array) {
            if (elements)
                elements.push(el);
            for(j=0, jmax=v.length; j < jmax; j++) {
                a.push({name: n, value: v[j]});
            }
        }
        else if (feature.fileapi && el.type == 'file') {
            if (elements)
                elements.push(el);
            var files = el.files;
            if (files.length) {
                for (j=0; j < files.length; j++) {
                    a.push({name: n, value: files[j], type: el.type});
                }
            }
            else {
                // #180
                a.push({ name: n, value: '', type: el.type });
            }
        }
        else if (v !== null && typeof v != 'undefined') {
            if (elements)
                elements.push(el);
            a.push({name: n, value: v, type: el.type, required: el.required});
        }
    }

    if (!semantic && form.clk) {
        // input type=='image' are not found in elements array! handle it here
        var $input = $(form.clk), input = $input[0];
        n = input.name;
        if (n && !input.disabled && input.type == 'image') {
            a.push({name: n, value: $input.val()});
            a.push({name: n+'.x', value: form.clk_x}, {name: n+'.y', value: form.clk_y});
        }
    }
    return a;
};

/**
 * Serializes form data into a 'submittable' string. This method will return a string
 * in the format: name1=value1&amp;name2=value2
 */
$.fn.formSerialize = function(semantic) {
    //hand off to jQuery.param for proper encoding
    return $.param(this.formToArray(semantic));
};

/**
 * Serializes all field elements in the jQuery object into a query string.
 * This method will return a string in the format: name1=value1&amp;name2=value2
 */
$.fn.fieldSerialize = function(successful) {
    var a = [];
    this.each(function() {
        var n = this.name;
        if (!n) {
            return;
        }
        var v = $.fieldValue(this, successful);
        if (v && v.constructor == Array) {
            for (var i=0,max=v.length; i < max; i++) {
                a.push({name: n, value: v[i]});
            }
        }
        else if (v !== null && typeof v != 'undefined') {
            a.push({name: this.name, value: v});
        }
    });
    //hand off to jQuery.param for proper encoding
    return $.param(a);
};

/**
 * Returns the value(s) of the element in the matched set.  For example, consider the following form:
 *
 *  <form><fieldset>
 *      <input name="A" type="text" />
 *      <input name="A" type="text" />
 *      <input name="B" type="checkbox" value="B1" />
 *      <input name="B" type="checkbox" value="B2"/>
 *      <input name="C" type="radio" value="C1" />
 *      <input name="C" type="radio" value="C2" />
 *  </fieldset></form>
 *
 *  var v = $('input[type=text]').fieldValue();
 *  // if no values are entered into the text inputs
 *  v == ['','']
 *  // if values entered into the text inputs are 'foo' and 'bar'
 *  v == ['foo','bar']
 *
 *  var v = $('input[type=checkbox]').fieldValue();
 *  // if neither checkbox is checked
 *  v === undefined
 *  // if both checkboxes are checked
 *  v == ['B1', 'B2']
 *
 *  var v = $('input[type=radio]').fieldValue();
 *  // if neither radio is checked
 *  v === undefined
 *  // if first radio is checked
 *  v == ['C1']
 *
 * The successful argument controls whether or not the field element must be 'successful'
 * (per http://www.w3.org/TR/html4/interact/forms.html#successful-controls).
 * The default value of the successful argument is true.  If this value is false the value(s)
 * for each element is returned.
 *
 * Note: This method *always* returns an array.  If no valid value can be determined the
 *    array will be empty, otherwise it will contain one or more values.
 */
$.fn.fieldValue = function(successful) {
    for (var val=[], i=0, max=this.length; i < max; i++) {
        var el = this[i];
        var v = $.fieldValue(el, successful);
        if (v === null || typeof v == 'undefined' || (v.constructor == Array && !v.length)) {
            continue;
        }
        if (v.constructor == Array)
            $.merge(val, v);
        else
            val.push(v);
    }
    return val;
};

/**
 * Returns the value of the field element.
 */
$.fieldValue = function(el, successful) {
    var n = el.name, t = el.type, tag = el.tagName.toLowerCase();
    if (successful === undefined) {
        successful = true;
    }

    if (successful && (!n || el.disabled || t == 'reset' || t == 'button' ||
        (t == 'checkbox' || t == 'radio') && !el.checked ||
        (t == 'submit' || t == 'image') && el.form && el.form.clk != el ||
        tag == 'select' && el.selectedIndex == -1)) {
            return null;
    }

    if (tag == 'select') {
        var index = el.selectedIndex;
        if (index < 0) {
            return null;
        }
        var a = [], ops = el.options;
        var one = (t == 'select-one');
        var max = (one ? index+1 : ops.length);
        for(var i=(one ? index : 0); i < max; i++) {
            var op = ops[i];
            if (op.selected) {
                var v = op.value;
                if (!v) { // extra pain for IE...
                    v = (op.attributes && op.attributes['value'] && !(op.attributes['value'].specified)) ? op.text : op.value;
                }
                if (one) {
                    return v;
                }
                a.push(v);
            }
        }
        return a;
    }
    return $(el).val();
};

/**
 * Clears the form data.  Takes the following actions on the form's input fields:
 *  - input text fields will have their 'value' property set to the empty string
 *  - select elements will have their 'selectedIndex' property set to -1
 *  - checkbox and radio inputs will have their 'checked' property set to false
 *  - inputs of type submit, button, reset, and hidden will *not* be effected
 *  - button elements will *not* be effected
 */
$.fn.clearForm = function(includeHidden) {
    return this.each(function() {
        $('input,select,textarea', this).clearFields(includeHidden);
    });
};

/**
 * Clears the selected form elements.
 */
$.fn.clearFields = $.fn.clearInputs = function(includeHidden) {
    var re = /^(?:color|date|datetime|email|month|number|password|range|search|tel|text|time|url|week)$/i; // 'hidden' is not in this list
    return this.each(function() {
        var t = this.type, tag = this.tagName.toLowerCase();
        if (re.test(t) || tag == 'textarea') {
            this.value = '';
        }
        else if (t == 'checkbox' || t == 'radio') {
            this.checked = false;
        }
        else if (tag == 'select') {
            this.selectedIndex = -1;
        }
		else if (t == "file") {
			if (/MSIE/.test(navigator.userAgent)) {
				$(this).replaceWith($(this).clone(true));
			} else {
				$(this).val('');
			}
		}
        else if (includeHidden) {
            // includeHidden can be the value true, or it can be a selector string
            // indicating a special test; for example:
            //  $('#myForm').clearForm('.special:hidden')
            // the above would clean hidden inputs that have the class of 'special'
            if ( (includeHidden === true && /hidden/.test(t)) ||
                 (typeof includeHidden == 'string' && $(this).is(includeHidden)) )
                this.value = '';
        }
    });
};

/**
 * Resets the form data.  Causes all form elements to be reset to their original value.
 */
$.fn.resetForm = function() {
    return this.each(function() {
        // guard against an input with the name of 'reset'
        // note that IE reports the reset function as an 'object'
        if (typeof this.reset == 'function' || (typeof this.reset == 'object' && !this.reset.nodeType)) {
            this.reset();
        }
    });
};

/**
 * Enables or disables any matching elements.
 */
$.fn.enable = function(b) {
    if (b === undefined) {
        b = true;
    }
    return this.each(function() {
        this.disabled = !b;
    });
};

/**
 * Checks/unchecks any matching checkboxes or radio buttons and
 * selects/deselects and matching option elements.
 */
$.fn.selected = function(select) {
    if (select === undefined) {
        select = true;
    }
    return this.each(function() {
        var t = this.type;
        if (t == 'checkbox' || t == 'radio') {
            this.checked = select;
        }
        else if (this.tagName.toLowerCase() == 'option') {
            var $sel = $(this).parent('select');
            if (select && $sel[0] && $sel[0].type == 'select-one') {
                // deselect all other options
                $sel.find('option').selected(false);
            }
            this.selected = select;
        }
    });
};

// expose debug var
$.fn.ajaxSubmit.debug = false;

// helper fn for console logging
function log() {
    if (!$.fn.ajaxSubmit.debug)
        return;
    var msg = '[jquery.form] ' + Array.prototype.join.call(arguments,'');
    if (window.console && window.console.log) {
        window.console.log(msg);
    }
    else if (window.opera && window.opera.postError) {
        window.opera.postError(msg);
    }
}

})( (typeof(jQuery) != 'undefined') ? jQuery : window.Zepto );


//----------------------------------------------------------------------------------------
/* JS Name 
 *   composer_socialservices.js start here
 */
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: composer_socialservices.js 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

(function() { // START NAMESPACE
  var $ = 'id' in document ? document.id : window.$;
  
  sm4.socialService = {
    
    initialize : function(element) {  
      
      if ($.type(element) != 'undefined') {
        this[element].toggle(this);
        if (element == 'facebook')
          fb_loginURL = '';
        else if (element == 'twitter')
          twitter_loginURL = '';
        else 
          linkedin_loginURL = '';
      }
      else {
       //Adding evet to Facebook icon
        $('.composer_facebook_toggle').on('click',function(){ 
          this['facebook'].toggle(this);
        }.bind(this));

        //Adding event to Twiiter icon
        $('.composer_twitter_toggle').on('click',function(){
          this['twitter'].toggle(this);
        }.bind(this));

        //Adding event to Linkedin icon
        $('.composer_linkedin_toggle').on('click',function(){
          this['linkedin'].toggle(this);
        }.bind(this));
      }
    },
    
    facebook:  {
      
      name : 'facebook',

      options : {
        title : '',
        lang : {
          'Publish this on Facebook': '',
          'Do not publish this on Facebook': ''
        },
        requestOptions : false
      },

      initialize : function(options) { 
        this.elements = {};
        this.params = {};
        
		
      },
      
      toggle : function(event) { 
        
        if (fb_loginURL == '') {   
          var checkBox = $('.compose-form-input-facebook');
           checkBox.each(function(key, el){
              if ($(el).attr("checked") == 'checked') { 
                $(el).removeAttr('Checked');
              }
              else {
                $(el).attr("checked", "checked");
              }
           });         
          
          $('.composer_facebook_toggle').toggleClass('composer_facebook_toggle_active'); 		 
          if (!$('.composer_facebook_toggle').hasClass('composer_facebook_toggle_active')) { 
            
           // $('#composer_facebook_toggle').children('span').html(this.options.lang['Publish this on Facebook'] + '<img alt="" src="application/modules/Advancedactivity/externals/images/tooltip-arrow-down.png" />');
          }
          else {
          //  $('#composer_facebook_toggle').children('span').html(this.options.lang['Do not publish this on Facebook'] + '<img alt="" src="application/modules/Advancedactivity/externals/images/tooltip-arrow-down.png" />');
          }
        }
        else { 
           window.open(fb_loginURL, '_blank');
        }
      }
      
    },
    
    twitter: {
      name : 'twitter',

      options : {
        title : '',
        lang : {
          'Publish this on Twitter': '',
          'Do not publish this on Twitter': ''
        },
        requestOptions : false
      },

      initialize : function(options) { 
        this.elements = {};
        this.params = {};
        
		
      },
      
      toggle : function(event) { 
        
        if (twitter_loginURL == '') {  
          var checkBox = $('.compose-form-input-twitter');
           checkBox.each(function(key, el){
              if ($(el).attr("checked") == 'checked') { 
                $(el).removeAttr('Checked');
              }
              else {
                $(el).attr("checked", "checked");
              }
           }); 
            
          $('#composer_twitter_toggle').toggleClass('composer_twitter_toggle_active'); 		 
          if (!$('#composer_twitter_toggle').hasClass('composer_twitter_toggle_active')) { 
            
           // $('#composer_twitter_toggle').children('span').html(this.options.lang['Publish this on Twitter'] + '<img alt="" src="application/modules/Advancedactivity/externals/images/tooltip-arrow-down.png" />');
          }
          else {
          //  $('#composer_twitter_toggle').children('span').html(this.options.lang['Do not publish this on Twitter'] + '<img alt="" src="application/modules/Advancedactivity/externals/images/tooltip-arrow-down.png" />');
          }
        }
        else { 
           window.open(twitter_loginURL, '_blank');
        }
      }
      
      
    },
    
    linkedin: {     
      
      name : 'linkedin',

      options : {
        title : '',
        lang : {
          'Publish this on Linkedin': '',
          'Do not publish this on Linkedin': ''
        },
        requestOptions : false
      },

      initialize : function(options) { 
        this.elements = {};
        this.params = {};
        
		
      },
      
      toggle : function(event) { 
        
        if (linkedin_loginURL == '') {  
          var checkBox = $('.compose-form-input-linkedin');
           checkBox.each(function(key, el){
              if ($(el).attr("checked") == 'checked') { 
                $(el).removeAttr('Checked');
              }
              else {
                $(el).attr("checked", "checked");
              }
           }); 
              
         $('#composer_linkedin_toggle').toggleClass('composer_linkedin_toggle_active'); 		 
          if (!$('#composer_linkedin_toggle').hasClass('composer_linkedin_toggle_active')) { 
            
           // $('#composer_linkedin_toggle').children('span').html(this.options.lang['Publish this on Linkedin'] + '<img alt="" src="application/modules/Advancedactivity/externals/images/tooltip-arrow-down.png" />');
          }
          else {
         //   $('#composer_linkedin_toggle').children('span').html(this.options.lang['Do not publish this on Linkedin'] + '<img alt="" src="application/modules/Advancedactivity/externals/images/tooltip-arrow-down.png" />');
          }
        }
        else { 
           window.open(linkedin_loginURL, '_blank');
        }
      }
    }
    
    
  } 
  

})(); // END NAMESPACE

sm4.core.runonce.add(function() { 
  if (typeof sm4 != 'undefined')
   sm4.socialService.initialize();
 
}); 