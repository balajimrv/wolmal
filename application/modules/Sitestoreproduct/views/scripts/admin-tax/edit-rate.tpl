<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: edit-rate.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<script type="text/javascript">
  
  var setRegion = null;
  window.addEvent('domready', function() {
    document.getElementById('tax_rate-wrapper').style.display = 'none';
<?php if ($this->flag_region != 0): ?>
          setRegion = <?php echo $this->flag_region ?>;
<?php endif; ?>
        
         <?php  if (!empty($this->flagAllCountries)): ?>
      document.getElementById('state-wrapper').style.display = 'none';
        <?php  endif; ?>
          
//        showRegions();
        showPriceType();
      });
  
function showPriceType(){
    if(document.getElementById('handling_type')){
      if(document.getElementById('handling_type').value == 1) {
        document.getElementById('tax_price-wrapper').style.display = 'none';
        document.getElementById('tax_rate-wrapper').style.display = 'block';

      } else{
        document.getElementById('tax_price-wrapper').style.display = 'block';
        document.getElementById('tax_rate-wrapper').style.display = 'none';
      }
    }
  }
 
//    function showRegions(){    
//    // IF THERER IS NO COUNTRY SELECT THEN HIDE STATE WRAPPER
//    if($('country').value == 0 || $('country').value == 'ALL'){
//      document.getElementById('state-wrapper').style.display = 'none';
//      return;
//    }
//      
//    document.getElementById('state-wrapper').style.display = 'none'; 
//
//    $('region_backgroundimage').innerHTML='<center><img src="application/modules/Sitestoreproduct/externals/images/spinner.gif" /></center>';
//      
//    en4.core.request.send(new Request.JSON({
//      url : en4.core.baseUrl + 'sitestoreproduct/index/changestate',
//      method : 'GET',
//      data : {
//        format : 'json',
//        country : $('country').value,
//        tax_id : <?php // echo $this->tax_id ?>,
//        flag_add_tax_rate : 1
//      },
//      onSuccess : function(responseJSON) {//alert(responseJSON);
//        $('region_backgroundimage').innerHTML="";
//      document.getElementById('state-wrapper').style.display = 'block'; 
//      var states = 0;
//      states = JSON.decode(responseJSON.length);
//      $('state').innerHTML = "";
//      if(states != 0){
//        addOption($('state'), 'All Regions', 0,'state');
//        for (var i=0; i < states.length;++i){       
//          var statess = states[i].split('_');
//          addOption($('state'), statess[0], statess[1],'state');
//        }
//        
//        if(setRegion) {
//          $('state').value = setRegion;
//        }
//      }else{
//        if(type == 1 && country == $('country').value){
//          addOptionTableRate($('state'), region, regionId,'state');
//        }else{    
//          $('region_backgroundimage').innerHTML = "There is no region available for this country now. ";
//          $('region_backgroundimage').style.display = 'block';
//          $('state-wrapper').style.display = 'none';         
//        }        
//      }
//      }
//
//    })
//  );
//  }
  
//      function addOption( selectbox, text, value)
//      {
//        var optn = document.createElement("OPTION");
//        optn.text = text;
//        optn.value = value;
//
//        if(optn.text != '' && optn.value != '') {
//          $('state').style.display = 'block';
//          $('state-wrapper').style.display = 'block';
//          selectbox.options.add(optn);
//        } else {
//          $('state').style.display = 'none';
//          $('state-wrapper').style.display = 'none';
//          selectbox.options.add(optn);
//        }
//      }
 
</script>

<div class="global_form_popup">
  <?php echo $this->form->render($this) ?>
</div>