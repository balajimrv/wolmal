<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _billing_address.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>
   
<?php $showShippingAddress = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.virtual.product.shipping', 1); ?>
<?php if( (!empty($this->sitestoreproduct_virtual_product) && !empty($showShippingAddress)) || !empty($this->sitestoreproduct_other_product_type)) :
        $showShippingAddredss = 1;
      else:
        $showShippingAddredss = 0;
      endif;
?>
<input type="hidden" id ="sitestoreproduct_address" name="sitestoreproduct_address" />
<div id="show_form_errors"></div>
<script type="text/javascript">
  var tempBillAddressErrorMsg = 0;
  var tempShipAddressErrorMsg = 0;
  en4.core.runonce.add(function() {    
  
   <?php if(!empty($showShippingAddredss)) : ?>
    <?php if (empty($this->flag_same_address)): ?>
      document.getElementById('common').checked = false;
      document.getElementById('common').set("value", 2);      
    <?php else: ?>
      document.getElementById('common').checked = true;
      document.getElementById('common').set("value", 1);
    <?php endif; ?>
   <?php endif; ?>
     if( $('common').value )  
     {
       new Element('div', {'id' : 'sitestoreproduct_shipping_address_form' ,'class' : 'fleft'}).inject($('common-wrapper'), 'after');

       document.getElementById('dummy_shipping_address_title-wrapper').inject($('sitestoreproduct_shipping_address_form'));
       document.getElementById('f_name_shipping-wrapper').inject($('sitestoreproduct_shipping_address_form'));
       document.getElementById('l_name_shipping-wrapper').inject($('sitestoreproduct_shipping_address_form'));
       document.getElementById('phone_shipping-wrapper').inject($('sitestoreproduct_shipping_address_form'));
       document.getElementById('country_shipping-wrapper').inject($('sitestoreproduct_shipping_address_form'));
       document.getElementById('state_shipping-wrapper').style.display = 'none'; 
        document.getElementById('shippingregion_backgroundimage').style.display = 'inline-block'; 
       document.getElementById('state_shipping-wrapper').inject($('sitestoreproduct_shipping_address_form'));
       document.getElementById('city_shipping-wrapper').inject($('sitestoreproduct_shipping_address_form'));
       document.getElementById('locality_shipping-wrapper').inject($('sitestoreproduct_shipping_address_form'));
       document.getElementById('zip_shipping-wrapper').inject($('sitestoreproduct_shipping_address_form'));
       document.getElementById('address_shipping-wrapper').inject($('sitestoreproduct_shipping_address_form'));
       
        if($('common').value == 1)
          new Fx.Slide('sitestoreproduct_shipping_address_form', {resetHeight : true}).hide();
     }
    <?php if (empty($this->is_populate)): ?>
      document.getElementById('state_billing-wrapper').style.display = 'none';   
    <?php endif; ?>
      
   <?php if(!empty($showShippingAddredss)) : ?>
    onSameAddress();
   <?php endif; ?>
  });

  function onSameAddress(){
    if($('common').value == 1){
      new Fx.Slide('sitestoreproduct_shipping_address_form', {resetHeight : true}).slideOut().toggle();
      document.getElementById('common').checked=true;
      document.getElementById('common').set("value", 2);
    }else{
      if(!document.getElementById('common').checked){
              // new Fx.Slide('sitestoreproduct_shipping_address_form', {resetHeight : true}).toggle();
              new Fx.Slide('sitestoreproduct_shipping_address_form', {resetHeight : true}).slideIn().toggle();
      }

    document.getElementById('common').checked=false;
      
    <?php if (empty($this->tempShippingRegionFlag) && !empty($this->is_populate)): ?>
        if($('country_shipping').value != 0)
          document.getElementById('state_shipping-wrapper').style.display = 'inline-block'; 
    <?php endif; ?>
    <?php if (empty($this->is_populate)): ?>
        $('f_name_shipping').set("value", '');
        $('l_name_shipping').set("value", '');
        $('phone_shipping').set("value", '');
        $('country_shipping').set("value", 0);
        $('city_shipping').set("value", '');
        $('locality_shipping').set("value", '');
        $('zip_shipping').set("value", '');
        $('address_shipping').set("value", '');
    <?php endif; ?>
      document.getElementById('common').set("value", 1);
    }
  }
  
  function addOption( selectbox, text, value ,state)
  { 
    var optn = document.createElement("OPTION");
    optn.text = text;
    optn.value = value;
    if(optn.text != '' && optn.value != '') {
      $(state).style.display = 'inline-block';
      $(state+'-wrapper').style.display = 'inline-block';
      selectbox.options.add(optn);
    } else {
      $(state).style.display = 'none';
      $(state+'-wrapper').style.display = 'none';
      selectbox.options.add(optn);
    }
  }
  
</script>

<div  id="dynamic_address_content">
  <?php echo $this->form->setAttrib('class', 'sitestoreproduct_checkout_form_address')->render($this) ?>
</div>
  
<script type="text/javascript">
en4.core.runonce.add(function() {    
document.getElementById('billingregion_backgroundimage').inject(document.getElementById('country_billing'), 'after');
document.getElementById('billingregion_backgroundimage').style.display = 'inline-block';
if( $('shippingregion_backgroundimage') )  
  document.getElementById('shippingregion_backgroundimage').inject(document.getElementById('country_shipping'), 'after');
});
  </script>

<div class='buttons'>
  <?php if( empty($this->sitestoreproduct_logged_in_viewer) ): ?>
  	<div class="m10 fleft"><a href="javascript:void(0)" onclick="checkoutProcess(1)">&laquo; Back</a></div>
  <?php endif; ?>
  <button type='button' name="continue" onclick="billingAddress()" class="m10 fright"><?php echo $this->translate("Continue") ?></button>
  <div id="loading_image_2" class="fright mtop10 ptop10" style="display: inline-block;"></div>
</div>

<script type="text/javascript">
  
	function showRegions(flag){

    var country='country_billing';
    var state='state_billing';
      
    if(flag){      
      tempShipAddressErrorMsg = 0;
      country='country_shipping';
      state='state_shipping';
    }else {
      tempBillAddressErrorMsg = 0;
    }
    
    
    document.getElementById(country + '_error').innerHTML = "";    
    
    // IF THERER IS NO COUNTRY SELECT THEN HIDE STATE WRAPPER
    if($(country).value == 0){
      document.getElementById(state+'-wrapper').style.display = 'none';
      return;
    }
      
    document.getElementById(state+'-wrapper').style.display = 'none'; 
    var addressType = state.split('_');
    $(addressType[1]+'region_backgroundimage').innerHTML='<img class="pleft10" src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitestoreproduct/externals/images/loading.gif" />';

    en4.core.request.send(new Request.JSON({
      url : en4.core.baseUrl + 'sitestoreproduct/index/changestate',
      method : 'GET',
      data : {
        format : 'json',
        country : $(country).value
      },
      onSuccess : function(responseJSON) {
        $(addressType[1]+'region_backgroundimage').innerHTML="";
        var subcatss = JSON.decode(responseJSON.length);
        if(subcatss.length > 0){
        document.getElementById(state+'-wrapper').style.display = 'block';
        $(state).innerHTML = "";
        
        for (var i=0; i < subcatss.length;++i){
          var subcatsss = subcatss[i].split('_'); 
          if((responseJSON.tempFlag > 0)) {
            if(subcatsss[0]) {
              addOption($(state), subcatsss[0], subcatsss[1],state);
            }
          }else {
            addOption($(state), subcatsss[0], subcatsss[1],state);
            document.getElementById(state+'-wrapper').style.display = 'none';
          }
        }
        
        }else{

        if(flag){      
          tempShipAddressErrorMsg = country + '_error';
        }else{
          tempBillAddressErrorMsg = country + '_error';
        }
         document.getElementById(country + '_error').innerHTML = "<span id='" + state + "_error' class='seaocore_txt_red'><?php echo $this->translate("There are no region available for selected country.") ?></span>";
        }

        
    <?php if (!empty($this->is_populate)): ?>
          if(flag == 0){
            $('state_billing').value = '<?php echo $this->billingRegionId ?>';
          }
    <?php endif; ?>
    <?php if (!empty($this->is_populate)): ?>
        <?php if (!empty($showShippingAddredss)): ?>
          if(flag == 1)
            $('state_shipping').value = '<?php echo $this->shippingRegionId ?>';
          <?php endif; ?>
    <?php endif; ?>
    }
  })
);
  }
	
  function billingAddress()
  {
    sitestoreproduct_address = $('store_address').toQueryString();
    if(sitestoreproduct_address)
        $("sitestoreproduct_address").set('value', sitestoreproduct_address);
    
    en4.core.request.send(new Request.JSON({
      url : en4.core.baseUrl + 'sitestoreproduct/index/saveaddress',
      method : 'POST',
      onRequest: function(){
        $('loading_image_2').innerHTML = '<img src='+en4.core.staticBaseUrl+'application/modules/Sitestoreproduct/externals/images/loading.gif height=15 width=15>';

      },
      data : {
               format : 'json',
               address : sitestoreproduct_address,
               show_shipping : <?php echo $showShippingAddredss ?>,
               billing_add_id : <?php echo $this->billingAddId ?>,
               shipping_add_id : <?php echo $this->shippingAddId ?>
             },
      onSuccess : function(responseJSON) 
      {
        if( responseJSON.errorFlag === '0' ) 
        {
          var subcatss = responseJSON.errorStr . split("::");
          for (var i=0; i < subcatss.length;++i)
          {
            var subcatsss = subcatss[i].split("=");          
            $(subcatsss[0]).innerHTML = ''; 
          }
            
          var address = new Array(4);
          address[0] = responseJSON.shipping_region_id;
          address[1] = responseJSON.shipping_country_id;
          address[2] = responseJSON.billing_region_id;
          address[3] = responseJSON.billing_country_id;

          var addresses_id = address.toString();
           if(document.getElementById('state_billing').options[document.getElementById('state_billing').selectedIndex].text){
            checkout_process_billing_address = $('f_name_billing').value + ' ' +$('l_name_billing').value + '<br/>' + $('address_billing').value + '<br/>' + $('city_billing').value + '<br/>' + document.getElementById('country_billing').options[document.getElementById('country_billing').selectedIndex].text + '<br/>' + document.getElementById('state_billing').options[document.getElementById('state_billing').selectedIndex].text + '&nbsp;&nbsp;&nbsp;&nbsp;' + $('zip_billing').value + '<br/>';
           }else {
             checkout_process_billing_address = $('f_name_billing').value + ' ' +$('l_name_billing').value + '<br/>' + $('address_billing').value + '<br/>' + $('city_billing').value + '<br/>' + document.getElementById('country_billing').options[document.getElementById('country_billing').selectedIndex].text + '<br/>' + $('zip_billing').value + '<br/>';
           }
          
<?php if( !empty($showShippingAddredss) ) : ?>
      // IF BILLING AND SHIPPING ADDRESSES ARE SAME
      if( document.getElementById('common').checked == true )
      {
        checkout_process_shipping_address = checkout_process_billing_address + $('phone_billing').value;
      }
      else
      {
        if(document.getElementById('state_shipping').options[document.getElementById('state_shipping').selectedIndex].text){
         checkout_process_shipping_address = $('f_name_shipping').value + ' ' +$('l_name_shipping').value + '<br/>' + $('address_shipping').value + '<br/>' + $('city_shipping').value + '<br/>' + document.getElementById('country_shipping').options[document.getElementById('country_shipping').selectedIndex].text + '<br/>' + document.getElementById('state_shipping').options[document.getElementById('state_shipping').selectedIndex].text + '&nbsp;&nbsp;&nbsp;&nbsp;' + $('zip_shipping').value + '<br/>' + $('phone_shipping').value; 
        }else {
         checkout_process_shipping_address = $('f_name_shipping').value + ' ' +$('l_name_shipping').value + '<br/>' + $('address_shipping').value + '<br/>' + $('city_shipping').value + '<br/>' + document.getElementById('country_shipping').options[document.getElementById('country_shipping').selectedIndex].text + '<br/>' + $('zip_shipping').value + '<br/>' + $('phone_shipping').value; 
        }        
      }
<?php endif; ?>
      
<?php if( empty($this->sitestoreproduct_logged_in_viewer) ): ?>
  checkout_process_billing_address += $('email_billing').value + '<br />';
<?php endif; ?>
  
  checkout_process_billing_address += $('phone_billing').value;
  setTimeout("checkout(3, '"+addresses_id+"')", 100);
        }
        else
        {
          var subcatss = responseJSON.errorStr . split("::");
          for (var i=0; i < subcatss.length;++i)
          {
            var subcatsss = subcatss[i].split("=");
						
            if(subcatsss[1] != '0')
            {
              if( ( subcatsss[0] == tempBillAddressErrorMsg) || (subcatsss[0] == tempShipAddressErrorMsg) ) 
              {
                continue;
              }
              
              var country_billing = document.getElementById('country_billing').value;
              var country_shipping= '';
              
              <?php if( !empty($showShippingAddredss) ) : ?>
                   country_shipping = document.getElementById('country_shipping').value;     
               <?php endif; ?>
                    
              if( subcatsss[0] == 'country_billing_error' && country_billing.length != 0 ) 
                subcatsss[1] = '<?php echo $this->translate("There are no region available for selected country.") ?>';

              if( subcatsss[0] == 'country_shipping_error' && country_shipping.length != 0 ) 
                subcatsss[1] = '<?php echo $this->translate("There are no region available for selected country.") ?>';

              $(subcatsss[0]).innerHTML = subcatsss[1];
            }
            else
              $(subcatsss[0]).innerHTML = ''; 
          }
          $('loading_image_2').innerHTML = '';
        }
      }
    })
  );
}
</script>

<?php if ( Zend_Controller_Front::getInstance()->getRequest()->getParam("singleAddressFlag", 0) ): ?>
  <script type="text/javascript">
    window.addEvent('domready', function() {
      setTimeout("showState()", 1000);
    });
    
    function showState() {
      $("state_billing-wrapper").style.display = 'block';
      $("state_shipping-wrapper").style.display = 'block';
    }
  </script>
<?php endif; ?>