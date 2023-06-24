/* $Id: core_cartbutton.js 2013-09-02 00:00:00Z SocialEngineAddOns Copyright 2012-2013 BigStep Technologies Pvt. Ltd. $ */
var CartHandler = new Class({ 
	
	Implements : [Events, Options],
	
  options : {
    debug : false,
    baseUrl : '/',
		cart_text : 'Cart',
    enableFeedback : true,
    stylecolor : '#d8dfe3',
    mouseovercolor : '#f23a3a',
    classname : 'smoothbox cart-button',
    idname: 'sitestoreproduct_cart_button_count'
  },

  initialize : function(options) {
    this.setOptions(options);
    this.rooms = new Hash();
  },
  
  
  start : function() { 	
  	var mouseovercolortemp = this.options.stylecolor;
  	var mouseoutcolortemp = this.options.mouseovercolor;
  	var baseurl = this.options.baseUrl;    
    (new Element('a', {
    'href' : baseurl,     
    'class' : this.options.classname, 'html' : this.options.cart_text,   
    'id' : this.options.idname,
	   'styles': {
	        'background': this.options.stylecolor
	    },    
    	'events': {
      'mouseout': function(){
          this.style.backgroundColor = mouseovercolortemp;
      },
      'mouseover': function(){
         this.style.backgroundColor = mouseoutcolortemp;
      }
    }     
    }).inject(this.container || $('global_content') || document.body));	  	
  }			
});