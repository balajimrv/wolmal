<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manage-address.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<?php if (!empty($this->noCountryEnable)) : ?>
<div id="no_country_tip" class="tip">
      <span>
    <?php echo $this->translate("There are no locations enabled by site administrator.") ?>
      </span>
    </div>
<?php return; endif; ?> 

<div id="show_form_errors"></div>
<script type="text/javascript">
  var tempBillAddressErrorMsg = 0;
  var tempShipAddressErrorMsg = 0;
  en4.core.runonce.add(function() {    
    
    <?php if (empty($this->flag_same_address)): ?>
      document.getElementById('common').checked = false;
      $('common').set("value", 2);      
    <?php else: ?>
      document.getElementById('common').checked = true;
      $('common').set("value", 1);
    <?php endif; ?>
      
    <?php if (empty($this->is_populate)): ?>
      document.getElementById('state_billing-wrapper').style.display = 'none';   
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
     }
     if( $('shippingregion_backgroundimage') )  
       document.getElementById('shippingregion_backgroundimage').inject(document.getElementById('country_shipping'), 'after');
     
     if(!document.getElementById('country_billing').value) {
       document.getElementById('state_billing-wrapper').style.display = 'none';
     }
     if(!document.getElementById('country_shipping').value) {
       document.getElementById('state_shipping-wrapper').style.display = 'none';
     }
     
    onSameAddress();
    
    $('store_address').addEvent('submit', function(e) {
     e.stop();
     $('show_form_errors').innerHTML = '';
     $('spiner-image').innerHTML = '<center><img src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitestoreproduct/externals/images/spinner.gif" /></center>';
     en4.core.request.send(new Request.JSON({
        url : en4.core.baseUrl + 'sitestoreproduct/index/saveaddress',
        method : 'POST',
        data : {
          format : 'json',
          address : $('store_address').toQueryString(),
          show_shipping : 1,
          billing_add_id : <?php echo $this->billingAddId ?>,
          shipping_add_id : <?php echo $this->shippingAddId ?>
          
        },
        onSuccess : function(responseJSON) {
          $('spiner-image').innerHTML = '';
          if( responseJSON.errorFlag === '0') {
            var subcatss = responseJSON.errorStr . split("::");
            for (var i=0; i < subcatss.length;++i){
              var subcatsss = subcatss[i].split("=");
                $(subcatsss[0]).innerHTML = ''; 
            }
            $('show_form_errors').innerHTML = '<ul class="form-notices"><li><?php echo $this->translate("Your changes have been saved.") ?></li></ul>';
            
          }else{
            $('show_form_errors').innerHTML = '';
             var subcatss = responseJSON.errorStr . split("::");
            for (var i=0; i < subcatss.length;++i){
              var subcatsss = subcatss[i].split("=");
              if(subcatsss[1] != '0'){       
                 if( ( subcatsss[0] == tempBillAddressErrorMsg) || (subcatsss[0] == tempShipAddressErrorMsg) ) {
                   continue;
                 }

                 if( subcatsss[0] == 'country_billing_error' && country_billing.length != 0 ) 
                   subcatsss[1] = '<?php echo $this->translate('There are no region available for selected country.') ?>';
                 
                 if( subcatsss[0] == 'country_shipping_error' && country_shipping.length != 0 ) 
                   subcatsss[1] = '<?php echo $this->translate('There are no region available for selected country.') ?>';
                 
                 $(subcatsss[0]).innerHTML = subcatsss[1];
              }
              else
                $(subcatsss[0]).innerHTML = ''; 
            }
          }
        }

      })
    ); 
   });
  });

  function showRegions(flag){
    var country='country_billing';
    var state='state_billing';
    if(flag){
      country='country_shipping';
      state='state_shipping';
    }
    
    // IF THERER IS NO COUNTRY SELECT THEN HIDE STATE WRAPPER
    if($(country).value == 0){
      document.getElementById(state+'-wrapper').style.display = 'none';
      return;
    }
      
    document.getElementById(state+'-wrapper').style.display = 'none'; 
    var addressType = state.split('_');
		
		if( addressType[1] == 'billing' ) {
		$(addressType[1]+'region_backgroundimage').inject(document.getElementById('country_billing'), 'after');
		$(addressType[1]+'region_backgroundimage').style.display='inline-block';
    $(addressType[1]+'region_backgroundimage').innerHTML='<div class="pleft10"><img src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitestoreproduct/externals/images/loading.gif" /><div>';
		}
		else
		{
			$(addressType[1]+'region_backgroundimage').innerHTML='<div class="pleft10"><centre><img src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitestoreproduct/externals/images/loading.gif" /></center><div>';
		}
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
         document.getElementById(country + '_error').innerHTML = "<span id='" + state + "_error' class='seaocore_txt_red'><?php echo $this->translate('There are no region available for selected country.') ?></span>";
        }
        
    <?php if (!empty($this->is_populate)): ?>
          if(flag == 0){
            $('state_billing').value = '<?php echo $this->billingRegionId ?>';
          }
    <?php endif; ?>
    <?php if (!empty($this->is_populate)): ?>
          if(flag == 1)
            $('state_shipping').value = '<?php echo $this->shippingRegionId ?>';
    <?php endif; ?>
  
      }

    })
  );
  }

  function onSameAddress(){
    if($('common').value == 1){
      new Fx.Slide('sitestoreproduct_shipping_address_form').slideOut().toggle();
      document.getElementById('common').checked=true;
      $('common').set("value", 2);
    }else{
      document.getElementById('common').checked=false;
      new Fx.Slide('sitestoreproduct_shipping_address_form', {resetHeight : true}).slideIn().toggle();
      <?php if (empty($this->tempShippingRegionFlag) && !empty($this->is_populate)): ?>
          if($('country_shipping').value != 0)
            document.getElementById('state_shipping-wrapper').style.display = 'block';   
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
      $('common').set("value", 1);
    }
  }
  
  function addOption( selectbox, text, value ,state)
  {
    var optn = document.createElement("OPTION");
    optn.text = text;
    optn.value = value;

    if(optn.text != '' && optn.value != '') {
      $(state).style.display = 'block';
      $(state+'-wrapper').style.display = 'block';
      selectbox.options.add(optn);
    } else {
      $(state).style.display = 'none';
      $(state+'-wrapper').style.display = 'none';
      selectbox.options.add(optn);
    }
  }
  
</script>

<div class="sitestoreproduct_manage_address" id="dynamic_address_content">
  <?php echo $this->form->render($this) ?>
</div>