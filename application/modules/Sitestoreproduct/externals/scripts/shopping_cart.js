/* $Id: shopping_cart.js 2013-09-02 00:00:00Z SocialEngineAddOns Copyright 2012-2013 BigStep Technologies Pvt. Ltd. $ */

var ShoppingCartHandler = new Class({ 
	
	Implements : [Events, Options],
	
  options : {
    debug : false,
    baseUrl : '/',
		shoppingCart_text : 'Shopping Cart',
    enableShoppingCart : true,
    stylecolor : '#0267cc',
    mouseovercolor : '#ff0000',
    classname : 'smoothbox shoppingCart-button-left'
    
  },

  initialize : function(options) {
    this.setOptions(options);
    this.rooms = new Hash();
  },
  
  
  start : function() {
  	var mouseovercolortemp = this.options.stylecolor;
  	var mouseoutcolortemp = this.options.mouseovercolor;
  	var baseurl = this.options.baseUrl;
    
    new Element('div',{ 
      'id' : 'sitestoreproduct_shopping_cart_container'
    }).inject($('global_content') || document.body);
    
    
    new Element('a', {
      'id' : 'sitestoreproduct_shopping_cart_link',
      'class' : this.options.classname, 'html' : this.options.shoppingCart_text,   
      'styles': {
                  'background': this.options.stylecolor
                },    
      'events': {
                  'mouseout': function()
                   {
                     this.style.backgroundColor = mouseovercolortemp;
                   },
                  'mouseover': function()
                   {
                     this.style.backgroundColor = mouseoutcolortemp;
                   },
                  'click': function()
                  {
                    if( myHorizontalSlide.open == true )
                    {
                      document.getElementById('sitestoreproduct_shopping_horizontal_cart_content').innerHTML = '';
                      myVerticalSlide.toggle().chain(function(){myHorizontalSlide.toggle();});
//                      myHorizontalSlide.toggle();
                    }
                    else
                    {
                      document.getElementById('sitestoreproduct_shopping_horizontal_cart_content').innerHTML = 'Please Wait<br /><img style="float:left; margin-right: 5px;" src="'+en4.core.staticBaseUrl+'application/modules/Seaocore/externals/images/core/loading.gif" title="Loading ...">';
                      myHorizontalSlide.toggle().chain(function(){
                      
                      var request = new Request.HTML({
                            url : en4.core.baseUrl + 'sitestoreproduct/product/get-cart-products',
                            method: 'GET',
                            data : {
                              format : 'html'
                            },    
                            onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
                              document.getElementById('sitestoreproduct_shopping_horizontal_cart_content').innerHTML = '';
                              document.getElementById('sitestoreproduct_shopping_vertical_cart_content').innerHTML = responseHTML;
                                myVerticalSlide.toggle();
                            }.bind(this)
                            });
                            request.send(); 
                        });
                    }
                    
                  }
               }
    }).inject($('sitestoreproduct_shopping_cart_container'));

    new Element('div', { 
      'id' : 'sitestoreproduct_shopping_horizontal_cart_content'
    }).inject($('sitestoreproduct_shopping_cart_container'));
    
    new Element('div', { 
      'id' : 'sitestoreproduct_shopping_vertical_cart_content'
    }).inject($('sitestoreproduct_shopping_cart_container'));
    
    var myHorizontalSlide = new Fx.Slide('sitestoreproduct_shopping_horizontal_cart_content',{mode: 'horizontal'}).toggle();
    var myVerticalSlide = new Fx.Slide('sitestoreproduct_shopping_vertical_cart_content').toggle();
    
  }
  
});

window.addEvent('domready', function() 
{
  function shoppingCartToggle() {
    alert('hhhhhi'); alert('visible = ' + myHorizontalSlide.open); 
  }
});
