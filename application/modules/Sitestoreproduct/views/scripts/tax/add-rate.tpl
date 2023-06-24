<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: add-rate.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>
   
<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/styles/style_sitestoreproduct.css'); ?>
<?php $allRegionsAdded = $this->allRegionsAdded; ?>
<?php if (!empty($allRegionsAdded['all_country']) && !empty($allRegionsAdded['all_regions'])): ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('All available regions for this tax is already added.') ?>
    </span>
  </div>
  <div class='buttons'>
    <button type='button' name="cancel" onclick="javascript:parent.Smoothbox.close();"><?php echo $this->translate("Close") ?></button>
  </div>
<?php return; endif; ?>

<script type="text/javascript">
var setRegion = null;
window.addEvent('domready', function() {
  $('all_regions-wrapper').setStyle('display', 'none');
  document.getElementById('tax_rate-wrapper').style.display = 'none';
  <?php if (!empty($this->flag_region)): ?>
    setRegion = <?php echo $this->flag_region ?>;
  <?php endif; ?>
  showRegions();     
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
 
function showRegions(){
  // IF THERER IS NO COUNTRY SELECT THEN HIDE STATE WRAPPER
  if($('country').value == 0 || $('country').value == 'ALL'){
    document.getElementById('all_regions-wrapper').setStyle('display', 'none');
    document.getElementById('state-wrapper').style.display = 'none';
    return;
  }

  document.getElementById('state-wrapper').style.display = 'none'; 
  document.getElementById('all_regions-wrapper').setStyle('display', 'none');

  $('region_backgroundimage').innerHTML='<center><img src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitestoreproduct/externals/images/spinner.gif" /></center>';

  en4.core.request.send(new Request.JSON({
    url : en4.core.baseUrl + 'sitestoreproduct/index/changestate',
    method : 'GET',
    data : {
      format : 'json',
      country : $('country').value,
      tax_id : <?php echo $this->tax_id ?>,
      flag_add_tax_rate : 1
    },
    onSuccess : function(responseJSON) {
      $('region_backgroundimage').innerHTML="";        
      var states = 0;
      states = JSON.decode(responseJSON.length);
      var all_region = JSON.decode(responseJSON.all_region);
      $('state').innerHTML = "";
      if(states && states != 0){
        document.getElementById('all_regions-wrapper').style.display = 'block';
        document.getElementById('state-wrapper').style.display = 'block'; 
        if(all_region == 1)
          document.getElementById('all_regions-wrapper').style.display = 'none'; 
        for (var i=0; i < states.length;++i){       
          var statess = states[i].split('_');
          if((responseJSON.tempFlag > 0)) {
            if(statess[0]) {
              addOption($('state'), statess[0], statess[1],'state');
            }
          }else {
            addOption($('state'), statess[0], statess[1],'state');
            document.getElementById('all_regions-yes').checked = 1;
            document.getElementById('all_regions-no').checked = 0;
            document.getElementById('all_regions-wrapper').style.display = 'none';
            document.getElementById('state-wrapper').style.display = 'none'; 
          }
        }

        if(setRegion) {
          $('state').value = setRegion;
        }
        
        if((responseJSON.tempFlag > 0)) {
          showAllRegions();
        }
      }else{
        document.getElementById('all_regions-yes').checked = "checked";
        $('region_backgroundimage').style.display = 'block';
        document.getElementById('all_regions-wrapper').style.display = 'none';
        document.getElementById('state-wrapper').style.display = 'none';     
      }
      
    }

  })
);
}
  
function addOption( selectbox, text, value)
{
  var optn = document.createElement("OPTION");
  var getTemImplodeState = '<?php echo $this->getImplodeState; ?>';
  optn.text = text;
  optn.value = value;
  if( getTemImplodeState.search("::" + value +"::") >= 0 ) {
    optn.selected = "selected";
  }

  if(optn.text != '' && optn.value != '') {
    $('state').style.display = 'block';
    $('state-wrapper').style.display = 'block';
    selectbox.options.add(optn);
  } else {
    $('state').style.display = 'none';
    $('state-wrapper').style.display = 'none';
    selectbox.options.add(optn);
  }
}

function showAllRegions()
{
  var radios = document.getElementsByName("all_regions");
  var radioValue;
  if (radios[0].checked) {
    radioValue = radios[0].value; 
  }else {
    radioValue = radios[1].value; 
  }
  if(radioValue == 'yes') {
    $('state-wrapper').style.display = 'none';
  } else{
   $('state-wrapper').style.display = 'block';

  }
}
</script>

<?php if( empty($this->form) ): ?>
  <div class="tip">
    <span>
       <?php echo $this->translate("There are no location available for applying this tax. May be you have applyied tax on all allowed locations or location not allowed by site administrator."); ?>
    </span>
  </div>
  <div class='buttons'>
    <button type='button' name="cancel" onclick="javascript:parent.Smoothbox.close();"><?php echo $this->translate("Close") ?></button>
  </div>
<?php else: ?>
  <div class="sr_sitestoreproduct_form_popup" style="min-height: 350px;">
    <?php echo $this->form->render($this) ?>
  </div>
<?php endif; ?>