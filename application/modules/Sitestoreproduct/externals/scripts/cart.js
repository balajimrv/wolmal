/* $Id: cart.js 2013-09-02 00:00:00Z SocialEngineAddOns Copyright 2012-2013 BigStep Technologies Pvt. Ltd. $ */

var SitestoreproductCartHandler = new Class({ 
	
	Implements : [Events, Options],
	hideToggleCartEnable : false,
  sitestoreproductCartFlag : true,
  sitestoreproductProductCount : 0,
  options : {
    PRODUCT_IN_CART_STR : en4.core.language.translate("0 items"),
    PRODUCT_IN_CART_INT : "",
    MANAGE_CART : "/" ,
    VIEWER_ID : 0,
    UPDATES_TAB_CLASS : 'updates_toggle',
    USER_URL : '',
    TEMPREMOVEFLAG : 0,
    TEMPCLASSFLAG : '',
    TEMPZEROITEM : 0
  },

  initialize : function(options) {
    this.setOptions(options);
    this.rooms = new Hash();     
  },
  
  start : function() {
    if( this.options.PRODUCT_IN_CART_INT != 0 ) {
      this.options.TEMPCLASSFLAG = 'new_updates';
    }else{
      this.options.TEMPZEROITEM = 1;
    }
    if( !document.getElementById('sitestoreproduct-header-cart') ) {

      this.options.USER_URL = '&nbsp;<a href="' + this.options.MANAGE_CART + '" style="margin:0;">' + en4.core.language.translate("View Cart") + '</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
      
     // SHOW CART LINK ON TOP LOGO HEADER DIV.
      appendCartButton = new Element('li', {
        'id' : 'sitestoreproduct-header-cart',
        'styles' : {
        
      }, 'html': '<span id="sitestoreproduct_updates_pulldown" class="updates_pulldown" style="display: inline-block;" onclick="sitestoreproductHandler.toggleCart(this);">\n\
<div class="pulldown_contents_wrapper">\n\
  <div class="pulldown_contents" id="sitestoreproduct_pulldown_contents">\n\
    <ul id="sitestoreproduct_cart_menu" class="notifications_menu">\n\
      <li><div id="notifications_loading" class="sitestoreproduct_mini_cart_loading"><img style="margin-right: 5px;" src="'+en4.core.staticBaseUrl+'application/modules/Seaocore/externals/images/core/loading.gif" title="Loading ...">' + en4.core.language.translate("Loading...") + '</div></li></ul>\n\
  </div>\n\
  <div id="cart_pulldown_options" class="pulldown_options pulldown_options">\n\
    ' + this.options.USER_URL + '\n\
<strong><span id="sitestoreproduct_update_total" style="color:#fff;" class="fright"></span></strong>\n\
  </div>\n\
</div>\n\
<a class="mini_cart_button ' + this.options.TEMPCLASSFLAG + '" id="new_updates" href="javascript:void(0);">' + this.options.PRODUCT_IN_CART_STR + '</a>\n\
</span>'
      });
      
      if( this.options.VIEWER_ID == 0 ) {
        appendCartButton.inject(document.getElementById('core_menu_mini_menu').getFirst('ul'), 'top');
      }else {
        appendCartButton.inject(document.getElementById('core_menu_mini_menu_update'), 'after');
      }
    }
  },
  
  toggleCart : function(element) {
    if( this.options.TEMPREMOVEFLAG == 0 ) {
      if (element.hasClass('updates_pulldown')) {
        this.hideToggleCartEnable = false;
        element.removeClass('updates_pulldown');
        element.addClass('updates_pulldown_active');
        if(this.sitestoreproductCartFlag && (this.options.TEMPZEROITEM != 1))
        {
          this.getCartItem();
          this.sitestoreproductCartFlag = false;
        }
         
        if(this.options.TEMPZEROITEM == 1){
          this.sitestoreproductCartFlag = false;
          document.getElementById('sitestoreproduct_pulldown_contents').innerHTML = '<ul class="notifications_menu"><li><div class="sitestoreproduct_mini_cart_loading">' + en4.core.language.translate("Your Shopping Cart is empty.") + '</li></ul></div>';
          document.getElementById('cart_pulldown_options').style.display = 'none';
        }
      } else {this.hideToggleCartEnable = false;
        element.removeClass('updates_pulldown_active');
        element.addClass('updates_pulldown');
      }
    }
  },
  
  getCartItem : function() {
    if( tempCartItemRequest == 0 ) {
      var url = en4.core.baseUrl + 'sitestoreproduct/product/get-cart-products';
      var request = new Request.HTML({
        url : url,
        method: 'GET',
        data : {
          format : 'html'
        },    
        onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
            
          tempCartItemRequest == 1;
          if( sitestoreproduct_product_in_cart ) {
            document.getElementById('new_updates').innerHTML = '' + sitestoreproduct_product_in_cart;            
          }
          
          if( sitestoreproduct_update_total ) {
            $('sitestoreproduct_pulldown_contents').innerHTML = responseHTML;
            this.options.TEMPZEROITEM = 0;
            this.sitestoreproductCartFlag = true;
            if(document.getElementById('sitestoreproduct_update_total'))
              document.getElementById('cart_pulldown_options').style.display = 'block';
            document.getElementById('sitestoreproduct_update_total').innerHTML = sitestoreproduct_update_total;
            document.getElementById('new_updates').set('class', 'new_updates mini_cart_button');
          }else {
            if(document.getElementById('sitestoreproduct_update_total'))
              document.getElementById('sitestoreproduct_update_total').innerHTML = '';
            document.getElementById('new_updates').set('class', '');
            this.options.TEMPZEROITEM = 1;
            this.sitestoreproductCartFlag = false;
            document.getElementById('cart_pulldown_options').style.display = 'none';
            document.getElementById('sitestoreproduct_pulldown_contents').innerHTML = '<ul class="notifications_menu"><li><div class="sitestoreproduct_mini_cart_loading">' + en4.core.language.translate("Your Shopping Cart is empty.") + '</li></ul></div>';
          }
        }.bind(this)
        });
        request.send();
    }
  },
  
  removeProduct : function(cartProductId, index_id, is_array) {
    this.hideToggleCartEnable = false; 
    this.options.TEMPREMOVEFLAG = 1;
    if( is_array == 1 )
      document.getElementById("sitestoreproduct_notification_" + cartProductId + "_" + index_id).innerHTML = '<ul class="notifications_menu"><li><div class="sitestoreproduct_mini_cart_loading"><img src="'+en4.core.staticBaseUrl+'application/modules/Sitestoreproduct/externals/images/loader.gif" width="20" height="15"  /></div></li></ul>';
    else
      document.getElementById("sitestoreproduct_notification_" + cartProductId).innerHTML = '<div class="sr_sitestoreproduct_profile_loading_image" style="height:60px;padding:0px;"></div>';
    var url = en4.core.baseUrl + 'sitestoreproduct/product/delete-cart-product';
    var request = new Request.JSON({
      url : url,
      method: 'GET',
      data : {
        format : 'json',
        'cartProductId' : cartProductId,
        'index_id' : index_id,
        'is_array' : is_array
      },    
      onSuccess : function(responseJson) {
         sitestoreproductHandler.getCartItem();
        this.options.TEMPREMOVEFLAG = 1;
      }
      });
      request.send();    
  },
  
  hideToggleCart : function() {
    if(this.hideToggleCartEnable)
    {
      if( $('sitestoreproduct_updates_pulldown') && this.options.TEMPREMOVEFLAG == 0 ) {
      this.toggleCart($('sitestoreproduct_updates_pulldown'));
      this.hideToggleCartEnable = false; 
      }
      else
        {
          this.options.TEMPREMOVEFLAG = 0;
        }
    }
    else if($('sitestoreproduct_updates_pulldown') && $('sitestoreproduct_updates_pulldown').hasClass('updates_pulldown_active'))
    {
      this.hideToggleCartEnable = true;
    }
  }
});

window.addEvent('domready', function() 
{
  $(document.body).addEvent('click', function(event){
     sitestoreproductHandler.hideToggleCart();
   });
   
   if($('sitestoreproduct_pulldown_contents')) {
    $('sitestoreproduct_pulldown_contents').addEvent('click', function(event){
      sitestoreproductHandler.hideToggleCart();
    });
   }
});
