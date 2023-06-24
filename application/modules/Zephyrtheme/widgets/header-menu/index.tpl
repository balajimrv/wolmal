<?php
/**
 * Pixythemes
 *
 * @category   Application_Extensions
 * @package    ZephyrTheme
 * @copyright  Pixythemes
 * @license    http://www.pixythemes.com/
 * @author     Pixythemes
 */

// using Zend_Controller_Front
$module_name = Zend_Controller_Front::getInstance()->getRequest()->getModuleName();
$action_name = Zend_Controller_Front::getInstance()->getRequest()->getActionName();

// Set logo
$title = Engine_Api::_()->getApi('settings', 'core')->getSetting('core_general_site_title', $this->translate('_SITE_TITLE'));
$logo  = $this->header_logo;
$route = $this->viewer()->getIdentity() ? array('route'=>'user_general', 'action'=>'home') : array('route'=>'default');
$header_iconsshow = $this->header_iconsshow;
$header_iconssize = $this->header_iconssize;

// Select Header Layout
include 'header_' . $this->header_layout . '.php';

$s = 0;
?>

<!-- START - Menu Widget Script -->
<script type='text/javascript'>
Element.Events.clickout = {
  base : 'click',
  condition : function(event) { event.stopPropagation(); return false; },
  onAdd : function(fn) { this.getDocument().addEvent('click', fn); },
  onRemove : function(fn) { this.getDocument().removeEvent('click', fn); }
};

window.addEvent('domready', function() {
  <?php if(!$this->viewer->getIdentity()) { ?> 
  <?php if($this->header_loginbutton == 0 || $this->header_signupbutton == 0) { ?> 
	var TopConnectionBox = $('top_connection_box_container');
	var TopConnectionBoxDiv = $$('#top_connection_box_container > div');
	var LoginPopup = $$('#login_popup');
	var SignupPopup = $$('#signup_popup');
	var LoginButton = $$('.button_login');
	var SignupButton = $$('.button_signup')
	var LoginButton2 = $$('.button_login_2')
	var SignupButton2 = $$('.button_signup_2')
	var LoginTitle = $$('#login_titlex');
	var SignupTitle = $$('#signup_titlex');
	var loginclicked = 0;
	TopConnectionBox.hide();
	
	if($('global_page_user-signup-index')) { SignupPopup.dispose(); }
	if($('global_page_user-auth-login') || $('global_page_core-error-requireuser') ) { LoginPopup.dispose(); }
	
	<?php if($this->header_loginbutton == 0) { ?> 
	LoginButton.addEvents({
	  click: function(event, element, user_id){
		if(loginclicked == 0) { TopConnectionBox.show(); $$('html').addClass('connectbox_opened'); LoginPopup.show().addClass('visible'); LoginTitle.setStyle('display', 'inline-block'); SignupTitle.hide(); SignupPopup.hide(); loginclicked = 1; }
	  },
	});
	$$(LoginButton2).addEvents({
	  click: function(){ SignupPopup.hide().removeClass('visible'); LoginPopup.show().addClass('visible'); LoginTitle.setStyle('display', 'inline-block'); SignupTitle.hide(); }
	}); 
	<?php } ?>
	
	<?php if($this->header_signupbutton == 0) { ?>
	SignupButton.addEvents({
	  click: function(event, element, user_id){
		if(loginclicked == 0) { TopConnectionBox.show(); $$('html').addClass('connectbox_opened'); SignupPopup.show().addClass('visible'); SignupTitle.setStyle('display', 'inline-block'); LoginTitle.hide(); LoginPopup.hide(); loginclicked = 1; }
	  },
	});
	$$(SignupButton2).addEvents({
	  click: function(){ LoginPopup.hide().removeClass('visible'); SignupPopup.show().addClass('visible'); SignupTitle.setStyle('display', 'inline-block'); LoginTitle.hide(); }
	});
	<?php } ?>
  
	$$(LoginButton, SignupButton, TopConnectionBoxDiv).addEvents({
	  clickout: function(){
		if(loginclicked == 1) { TopConnectionBox.hide(); $$('html').removeClass('connectbox_opened'); loginclicked = 0; }
	  },
	});
	
	$('top_button_close').addEvent('click', function(event){
	  event.stop(); TopConnectionBox.hide(); $$('html').removeClass('connectbox_opened'); loginclicked = 0;
	});
	
	$$('#connect_login_box input, #connect_login_box button, .connect_box_form_login input, .connect_box_form_login button').set('tabindex', '0');
  <?php } ?>
  <?php } ?>
  
  <?php if( $this->header_fixed == 1 && $this->header_layout != 6  ) { ?>
  // Fix the Header on top of the page if it is enabled
  var headerHeight = $('global_header').getSize().y;
  $$('#global_header').setStyles({'position':'fixed', 'width':'100%', 'z-index':'99', 'top':'0px'});
  $$('#global_wrapper').setStyles({'padding-top': headerHeight});
  <?php } ?>

  // Search Box
  if($('global_search_field') && $('search_field_clear') && $('global_search_button')){
	var searchfield = $('global_search_field');
	var clearbut = $('search_field_clear');
	$$('.layout_theme_header_menu_bar #global_search_form').addEvent('click', function(){
	  searchfield.focus();
	});
	if(searchfield.value != '' && searchfield.value != '<?php echo $this->translate("Search for...") ?>' ) {
	  clearbut.setStyles({'visibility':'visible'});
	}
	else if(searchfield.value === '' || searchfield.value == '<?php echo $this->translate("Search for...") ?>') {
	  clearbut.setStyles({'visibility':'hidden'});
	}
	$$('.layout_theme_header_menu_bar #global_search_field').addEvents({
	  focus: function(){
		if(this.value == '<?php echo $this->translate("Search for...") ?>'){ this.value='' }
		if(this.value === ''){ clearbut.setStyles({'visibility':'hidden'}); }
		$$('.layout_theme_header_menu_bar #global_search_form').addClass('search_active');
	  },
	  blur: function(){
		if(this.value == ''){ this.value='<?php echo $this->translate("Search for...") ?>' }
		if(this.value === '<?php echo $this->translate("Search for...") ?>'){
		  $$('.layout_theme_header_menu_bar #global_search_form').removeClass('search_active');
		}
	  },
	  'keydown': function(){ if (this.value != '') { clearbut.setStyles({'visibility':'visible'}); } },
	  'keyup': function(){ if (this.value == '') { clearbut.setStyles({'visibility':'hidden'}); } }
	});
	$('search_field_clear').addEvent('click', function(event){
	  event.stop(); searchfield.value=''; searchfield.focus();
	});
  }

  // Search Box Mobile
  $$('.mobile_menu_container #global_search_field_mobile').addEvents({
	focus: function(){
	  if(this.value == '<?php echo $this->translate("Search for...") ?>'){ this.value='' }
	},
	blur: function(){
	  if(this.value == ''){ this.value='<?php echo $this->translate("Search for...") ?>' }
	},
  });

  // Mini and Main Menus - Submenus / Notifications
  var focusedtimer;
  var stop = 0;
  $$('li.submenu_explore').addEvents({
	mouseenter: function(){
	  if(stop == 1) { clearTimeout(focusedtimer); stop = 0; } $$('li.submenu_explore').addClass('focusedmenu'); },
	mouseleave: function(){
	  stop = 1; focusedtimer = setTimeout(function(){ stop = 0; $$('li.submenu_explore').removeClass('focusedmenu'); },500);
	}
  });
  
  <?php if( $this->viewer->getIdentity() ) { ?>  
	var upclicked = 0;
	$('updates_toggle').addEvents({
	  click: function(event, element, user_id){
		if(upclicked == 0)
		{
		  showNotifications();
		  $('pulldown_updates_wrapper').show();
		  $('core_menu_mini_menu_update').removeClass('updates_pulldown').addClass('mini_pulldown_active')
		  upclicked = 1;
		  if($('messages_pulldown').hasClass('mini_pulldown_active')){
			$('messages_pulldown_wrapper').hide();
			$('messages_pulldown').removeClass('mini_pulldown_active').addClass('messages_pulldown')
			messagesclicked = 0;
		  };
		  if($('minimenu_pulldown').hasClass('mini_pulldown_active')){
			$('minimenu_pulldown_wrapper').hide();
			$('minimenu_pulldown').removeClass('mini_pulldown_active').addClass('minimenu_pulldown')
			moreclicked = 0;
		  };
		  if($('requests_pulldown').hasClass('mini_pulldown_active')){
			$('requests_pulldown_wrapper').hide();
			$('requests_pulldown').removeClass('mini_pulldown_active').addClass('requests_pulldown')
			requestsclicked = 0;
		  };
		  <?php if( $s > $this->mainmenu_dropdown ) { ?>
		  $('submenu_toggle').addEvents({
			mouseenter: function(){
			$('pulldown_updates_wrapper').hide();
			$('core_menu_mini_menu_update').removeClass('mini_pulldown_active').addClass('updates_pulldown')
			upclicked = 0;
		  }});
		  <?php } ?>
		  $('mobile_menu_container').hide();
		  $$('.minimenu_mobile').removeClass('active');
		  mobileclicked = 0;
		}
		else
		{
		  $('pulldown_updates_wrapper').hide();
		  $('core_menu_mini_menu_update').removeClass('mini_pulldown_active').addClass('updates_pulldown');
		  upclicked = 0;
		}
	  },
	});
	$('core_menu_mini_menu_update').addEvents({
	  clickout: function(){
	  if(upclicked == 1)
	  {
		$('pulldown_updates_wrapper').hide();
		$('core_menu_mini_menu_update').removeClass('mini_pulldown_active').addClass('updates_pulldown');
		upclicked = 0;
	  }
	  },
	});
	var moreclicked = 0;
	$('minimenu_toggle').addEvents({
	  click: function(event, element, user_id){
		if(moreclicked == 0)
		{
		  if($('core_menu_mini_menu_update').hasClass('mini_pulldown_active')){
			$('pulldown_updates_wrapper').hide();
			$('core_menu_mini_menu_update').removeClass('mini_pulldown_active').addClass('updates_pulldown')
			upclicked = 0;
		  }
		  if($('messages_pulldown').hasClass('mini_pulldown_active')){
			$('messages_pulldown_wrapper').hide();
			$('messages_pulldown').removeClass('mini_pulldown_active').addClass('messages_pulldown')
			messagesclicked = 0;
		  };
		  if($('requests_pulldown').hasClass('mini_pulldown_active')){
			$('requests_pulldown_wrapper').hide();
			$('requests_pulldown').removeClass('mini_pulldown_active').addClass('requests_pulldown')
			requestsclicked = 0;
		  };
		  <?php if( $s > $this->mainmenu_dropdown ) { ?>
		  $('submenu_toggle').addEvents({
		  mouseenter: function(){
			$('minimenu_pulldown_wrapper').hide();
			$('minimenu_pulldown').removeClass('mini_pulldown_active').addClass('minimenu_pulldown');
			moreclicked = 0;
		  }});
		  <?php } ?>
		  $('mobile_menu_container').hide();
		  $$('.minimenu_mobile').removeClass('active');
		  mobileclicked = 0;
		  $('minimenu_pulldown_wrapper').show();
		  $('minimenu_pulldown').removeClass('minimenu_pulldown').addClass('mini_pulldown_active')
		  moreclicked = 1;
		}
		else
		{
		  $('minimenu_pulldown_wrapper').hide();
		  $('minimenu_pulldown').removeClass('mini_pulldown_active').addClass('minimenu_pulldown');
		  moreclicked = 0;
		}
	  },
	});
	$('minimenu_pulldown').addEvents({
	  clickout: function(){
		if(moreclicked == 1)
		{
		  $('minimenu_pulldown_wrapper').hide();
		  $('minimenu_pulldown').removeClass('mini_pulldown_active').addClass('minimenu_pulldown');
		  moreclicked = 0;
		}
	  },
	});
	
	var messagesclicked = 0;
	$('messages_toggle').addEvents({
	  click: function(event, element, user_id){
		if(messagesclicked == 0)
		{
		  showMessages();
		  if($('core_menu_mini_menu_update').hasClass('mini_pulldown_active')){
			$('pulldown_updates_wrapper').hide();
			$('core_menu_mini_menu_update').removeClass('mini_pulldown_active').addClass('updates_pulldown')
			upclicked = 0;
		  }
		  if($('minimenu_pulldown').hasClass('mini_pulldown_active')){
			$('minimenu_pulldown_wrapper').hide();
			$('minimenu_pulldown').removeClass('mini_pulldown_active').addClass('minimenu_pulldown')
			moreclicked = 0;
		  };
		  if($('requests_pulldown').hasClass('mini_pulldown_active')){
			$('requests_pulldown_wrapper').hide();
			$('requests_pulldown').removeClass('mini_pulldown_active').addClass('requests_pulldown')
			requestsclicked = 0;
		  };
		  <?php if( $s > $this->mainmenu_dropdown ) { ?>
		  $('submenu_toggle').addEvents({
		  mouseenter: function(){
			$('messages_pulldown_wrapper').hide();
			$('messages_pulldown').removeClass('mini_pulldown_active').addClass('messages_pulldown');
			messagesclicked = 0;
		  }});
		  <?php } ?>
		  $('mobile_menu_container').hide();
		  $$('.minimenu_mobile').removeClass('active');
		  mobileclicked = 0;
		  $('messages_pulldown_wrapper').show();
		  $('messages_pulldown').removeClass('messages_pulldown').addClass('mini_pulldown_active')
		  messagesclicked = 1;
		}
		else
		{
		  $('messages_pulldown_wrapper').hide();
		  $('messages_pulldown').removeClass('mini_pulldown_active').addClass('messages_pulldown');
		  messagesclicked = 0;
		}
	  },
	});
	$('messages_pulldown').addEvents({
	  clickout: function(){
		if(messagesclicked == 1)
		{
		  $('messages_pulldown_wrapper').hide();
		  $('messages_pulldown').removeClass('mini_pulldown_active').addClass('messages_pulldown');
		  messagesclicked = 0;
		}
	  },
	});

	var requestsclicked = 0;
	$('requests_toggle').addEvents({
	  click: function(event, element, user_id){
		if(requestsclicked == 0)
		{
		  showRequests();
		  if($('core_menu_mini_menu_update').hasClass('mini_pulldown_active')){
			$('pulldown_updates_wrapper').hide();
			$('core_menu_mini_menu_update').removeClass('mini_pulldown_active').addClass('updates_pulldown')
			upclicked = 0;
		  }
		  if($('minimenu_pulldown').hasClass('mini_pulldown_active')){
			$('minimenu_pulldown_wrapper').hide();
			$('minimenu_pulldown').removeClass('mini_pulldown_active').addClass('minimenu_pulldown')
			moreclicked = 0;
		  };
		  if($('messages_pulldown').hasClass('mini_pulldown_active')){
			$('messages_pulldown_wrapper').hide();
			$('messages_pulldown').removeClass('mini_pulldown_active').addClass('messages_pulldown')
			messagesclicked = 0;
		  };
		  <?php if( $s > $this->mainmenu_dropdown ) { ?>
		  $('submenu_toggle').addEvents({
		  mouseenter: function(){
			$('requests_pulldown_wrapper').hide();
			$('requests_pulldown').removeClass('mini_pulldown_active').addClass('requests_pulldown');
			requestsclicked = 0;
		  }});
		  <?php } ?>
		  $('mobile_menu_container').hide();
		  $$('.minimenu_mobile').removeClass('active');
		  mobileclicked = 0;
		  $('requests_pulldown_wrapper').show();
		  $('requests_pulldown').removeClass('requests_pulldown').addClass('mini_pulldown_active')
		  requestsclicked = 1;
		}
		else
		{
		  $('requests_pulldown_wrapper').hide();
		  $('requests_pulldown').removeClass('mini_pulldown_active').addClass('requests_pulldown');
		  requestsclicked = 0;
		}
	  },
	});
	$('requests_pulldown').addEvents({
	  clickout: function(){
		if(requestsclicked == 1)
		{
		  $('requests_pulldown_wrapper').hide();
		  $('requests_pulldown').removeClass('mini_pulldown_active').addClass('requests_pulldown');
		  requestsclicked = 0;
		}
	  },
	});

  <?php } ?> 
  
  <?php if($this->viewer->getIdentity() || $this->header_mainmenu == 1) { ?> 
  var mobileclicked = 0;
  $('minimenu_mobile_toggle').addEvents({
	click: function(event, element, user_id){
	  if(mobileclicked == 0)
	  {
		<?php if( $this->viewer->getIdentity() ) { ?> 
		  if($('core_menu_mini_menu_update').hasClass('mini_pulldown_active')){
			$('pulldown_updates_wrapper').hide();
			$('core_menu_mini_menu_update').removeClass('mini_pulldown_active').addClass('updates_pulldown')
			upclicked = 0;
		  }
		  if($('minimenu_pulldown').hasClass('mini_pulldown_active')){
			$('minimenu_pulldown_wrapper').hide();
			$('minimenu_pulldown').removeClass('mini_pulldown_active').addClass('minimenu_pulldown')
			moreclicked = 0;
		  };
		<?php } ?>
		$('mobile_menu_container').show();
		$$('.minimenu_mobile').addClass('active')
		mobileclicked = 1;
	  }
	  else
	  {
		$$('.mobile_menu_container').hide();
		$$('.minimenu_mobile').removeClass('active');
		mobileclicked = 0;
	  }
	},
  });
  $$('a.toggle_closer').addEvent('click', function(){
	$$('.mobile_menu_container').hide();
	$$('.minimenu_mobile').removeClass('active');
	mobileclicked = 0;
  });
  <?php } ?> 
});

<?php if( $this->viewer->getIdentity() ) { ?>  
var hideNotifications = function(reset_text) {
  en4.core.request.send(new Request.JSON({
	'url' : en4.core.baseUrl + 'activity/notifications/hide'
  }));
  $('updates_toggle').set('html', '<span class="pulldown_toggle_icon updates_toggle_icon"></span><span id="updates_span">' + reset_text + '</span>').removeClass('new_updates');
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
};

en4.core.runonce.add(function(){
  if($('notifications_markread_link')){
	$('notifications_markread_link').addEvent('click', function() {
	  hideNotifications('<?php echo $this->string()->escapeJavascript($this->translate("0"));?>');
	});
  }

  NotificationUpdateHandlers = new Class({
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
		  $('updates_toggle').set('html', '<span class="pulldown_toggle_icon updates_toggle_icon"></span><span id="updates_span">' + responseJSON.notificationCount + '</span>').addClass('new_updates');
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
	  _log : function(object) {
		if( !this.options.debug ) {
		  return;
		}
		try {
		  if( typeof(console) && $type(console) ) {
			console.log(object);
		  }
		} catch( e ) {
		  // Silence
		}
	  }
	});
});
  
var showNotifications = function() {
  new Request.HTML({
	'url' : en4.core.baseUrl + 'zephyrtheme/index/notifications',
	'data' : {
	  'format' : 'html',
	  'page' : 1
	},
	'onComplete' : function(responseTree, responseElements, responseHTML, responseJavaScript) {
	  if (responseHTML)
	  {
		if($('notifications_loading'))
			$('notifications_loading').setStyle('display', 'none');

		$('notifications_menu').innerHTML = responseHTML;
	  } else {
		$('notifications_loading').innerHTML = '<?php echo $this->string()->escapeJavascript($this->translate("You have no new updates."));?>';
	  }

		document.getElementById('updates_span').innerHTML = '';
		document.getElementById('updates_toggle').removeClass('new_updates');
	}
  }).send();
};

var abortRequest;
function showMessages() {
  abortRequest = new Request.HTML({
	url : en4.core.baseUrl + 'zephyrtheme/index/message',
	data : {
	  format : 'html'
	},
	onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript)
	{
	 document.getElementById('messages_menu').innerHTML = responseHTML;
	 document.getElementById('messages_toggle').removeClass('new_updates');
	}
  }); 
  en4.core.request.send(abortRequest, {
  'force': true
});
}

function showRequests() {
  abortRequest = new Request.HTML({
	url : en4.core.baseUrl + 'zephyrtheme/index/friend-request',
	data : {
	  format : 'html'
	},
	onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript)
	{
	 if(responseHTML) {
	   document.getElementById('requests_menu').innerHTML = responseHTML;
	   document.getElementById('requests_span').innerHTML = '';
	   document.getElementById('requests_toggle').removeClass('new_updates');
	 }
	 else {
	  $('friend_request_loading').innerHTML = '<?php echo $this->string()->escapeJavascript($this->translate("No new requests"));?>';
	 }
	}
  }); 
  en4.core.request.send(abortRequest, {
  'force': true
});
}

window.addEvent('domready', function() {

  <?php if($this->viewer->getIdentity() != 0) { ?>
		   setInterval(function() { newNotifications();  },10000);
	window.setInterval(function() { newMessages();       },20000);
	window.setInterval(function() { newFriendRequests(); },30000);
  <?php } ?>

});

function newFriendRequests() {
  en4.core.request.send(new Request.JSON({
	url : en4.core.baseUrl + 'zephyrtheme/index/new-friend-requests',
	method : 'POST',
	data : {
	  format : 'json'
	},
	onSuccess : function(responseJSON) 
	{
	  if( responseJSON.requestCount && $('requests_toggle') ) {
		$('requests_toggle').addClass('new_updates');
		$('requests_span').style.display = 'block';
		if(responseJSON.requestCount > 0 && responseJSON.requestCount != '') {
		  $('requests_toggle').addClass('new_updates'); }
		$('requests_span').innerHTML = responseJSON.requestCount;
	  }
	}
  }));
}

  function newNotifications()
  {
	  en4.core.request.send(new Request.JSON({
		  url : en4.core.baseUrl + 'zephyrtheme/index/new-notifications',
		  method : 'POST',
		  data : {
			  format : 'json'
		  },
		  onSuccess : function(responseJSON)
		  {
			  if (responseJSON.notificationCount && $('updates_toggle'))
			  {
				  $('updates_toggle').addClass('new_updates');
				  $('updates_span').style.display = 'block';
				  $('updates_span').innerHTML = responseJSON.notificationCount;
			  }
		  }
	  }));
  }

function newMessages() {
  en4.core.request.send(new Request.JSON({
	url : en4.core.baseUrl + 'zephyrtheme/index/new-messages',
	method : 'POST',
	data : {
	  format : 'json'
	},
	onSuccess : function(responseJSON) 
	{
	  if( responseJSON.messageCount && $("messages_toggle") ) {
		$('messages_toggle').addClass('new_updates');
		$('messages_span').style.display = 'block';
		if(responseJSON.messageCount > 0 && responseJSON.messageCount != '') {
		  $('messages_toggle').addClass('new_updates'); }
		$('messages_span').innerHTML = responseJSON.messageCount;
	  }
	}
  }));
}

<?php } ?>  
</script>
