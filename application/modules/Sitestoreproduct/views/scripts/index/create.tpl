<?php 
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: create.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php include_once APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/scripts/navigation_views.tpl'; 
$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'externals/calendar/calendar.compat.js');
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'externals/calendar/styles.css');
$this->headScript()
		->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
		->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/autocompleter/SEAOAutocompleter.js')
		->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/autocompleter/SEAOAutocompleter.Local.js')
		->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/autocompleter/SEAOAutocompleter.Request.js')
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Core/externals/scripts/composer.js');
?>

<?php
$apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
$this->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&key=$apiKey");
?>

<div class='layout_middle sitestoreproduct_create_product'>
  <?php if (!empty($this->quota) && $this->current_count >= $this->quota): ?>
    <div class="tip"> 
      <span>
        <?php echo $this->translate("Not allowed to create products because maximum number of products already created. You have <b>%s</b> products created in this store and package allowed you <b>%s</b> product's creation.", $this->current_count, $this->quota); ?>         
      </span>
    </div>
    <?php return; ?>
    <br/>
  <?php elseif(!empty($this->allowSellingProducts) && empty($this->isAnyCountryEnable) && !empty($this->sitestoreproduct_render) && ($this->sitestoreproduct_render != "downloadable") && ($this->sitestoreproduct_render != "virtual")): ?>
    <div class="tip"> 
      <span>
				<?php echo $this->translate("There are no location configured by site administrator for the shipment."); ?>
      </span>
    </div>
    <?php return; ?>
  <?php elseif(!empty($this->allowSellingProducts) && empty($this->shipping_method_exist) && !empty($this->sitestoreproduct_render) && ($this->sitestoreproduct_render != "downloadable") && ($this->sitestoreproduct_render != "virtual")): ?>
    <div class="tip"> 
      <span>
				<?php echo $this->translate("No shipping methods have been configured for this store yet. Please %1sclick here%2s to configure shipping methods for your store so that you can start selling.", '<a href="'.$this->url(array('action' => 'store','store_id' => $this->sitestore->store_id, 'type' => 'index', 'menuId' => '51', 'method' => 'shipping-methods'), 'sitestore_store_dashboard', true).'">', '</a>'); ?>
      </span>
    </div>
    <?php return; ?>
  <?php elseif($this->category_count > 0): ?>
    <?php if (!empty($this->sitestoreproduct_render)) : ?>
      <?php if(!empty($this->lessSimpleProductType)) : ?>
        <div class="tip"> 
          <span>
            <?php echo $this->translate("You can not create this type of product currently, because at least two products are required to create this product type."); ?>
          </span>
        </div>
        <?php return; ?>
      <?php endif; ?>
      <?php if( $this->countProductTypes == 1 ) : 
        $this->form->setTitle($this->translate("Create New Product"));
      else:
        $this->form->setTitle($this->translate("2. Create New Product"));
      endif;
      $this->form->setDescription("<p>Create your product by configuring the various properties below.</p>");
      $this->form->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));
      echo $this->form->setAttrib('class', 'global_form sr_sitestoreproduct_create_list_form')->render($this);
      ?>
      <?php if ($this->languageCount > 1 && $this->multiLanguage): ?>
    
      <div id="multiLanguageTitleLinkShow" class="form-wrapper">
        <div class="form-label">&nbsp;</div>
        <div class="form-element">
          <a href="javascript: void(0);" onclick="return multiLanguageTitleOption(2);" style="text-decoration: none;"><b><?php echo $this->translate("Create Title in the multiple languages supported by this website."); ?></b></a>
        </div>
      </div>

      <div id="multiLanguageTitleLinkHide" class="form-wrapper">
        <div class="form-label">&nbsp;</div>
        <div class="form-element">
          <a href="javascript: void(0);" onclick="return multiLanguageTitleOption(1);" style="text-decoration: none;"><b><?php echo $this->translate("Create Title in the primary language of this website."); ?></b></a>
        </div>
      
      </div>
          <div id="multiLanguageBodyLinkShow" class="form-wrapper">
        <div class="form-label">&nbsp;</div>
        <div class="form-element">
          <a href="javascript: void(0);" onclick="return multiLanguageBodyOption(2);" style="text-decoration: none;"><b><?php echo $this->translate("Create Short Description in the multiple languages supported by this website."); ?></b></a>
        </div>
      </div>

      <div id="multiLanguageBodyLinkHide" class="form-wrapper">
        <div class="form-label">&nbsp;</div>
        <div class="form-element">
          <a href="javascript: void(0);" onclick="return multiLanguageBodyOption(1);" style="text-decoration: none;"><b><?php echo $this->translate("Create Short Description in the primary language of this website."); ?></b></a>
        </div>
        
      </div>
          <div id="multiLanguageOverviewLinkShow" class="form-wrapper">
        <div class="form-label">&nbsp;</div>
        <div class="form-element">
          <a href="javascript: void(0);" onclick="return multiLanguageOverviewOption(2);" style="text-decoration: none;"><b><?php echo $this->translate("Create Overview in the multiple languages supported by this website."); ?></b></a>
        </div>
      </div>

      <div id="multiLanguageOverviewLinkHide" class="form-wrapper">
        <div class="form-label">&nbsp;</div>
        <div class="form-element">
          <a href="javascript: void(0);" onclick="return multiLanguageOverviewOption(1);" style="text-decoration: none;"><b><?php echo $this->translate("Create Overview in the primary language of this website."); ?></b></a>
        </div>
      </div>
    <?php endif; ?>
      
    <?php endif; ?>
  <?php endif; ?>


<?php $this->tinyMCESEAO()->addJS();?>


<!--CONDITIONS FOR TWO STEP FORM AND VARIOUS DEPENDENCIES ON PRODUCT-->
<?php if (!empty($this->withNoSingleProduct)): ?>
    <div class="tip"> 
      <span>
				 <?php echo $this->translate("You do not have created products in this store that's why you will not be permitted for creating %s products.", $this->productTypeName); ?>
      </span>
    </div>
<?php return; endif; ?>

<?php if (!empty ($this->viewType)): ?>
<?php if(empty($this->productType)) : ?>
<div class="tip"> 
      <span>
				 <?php echo $this->translate("There are no product type available for creating products."); ?>
      </span>
    </div>
<?php return; endif; ?>

		<?php if(!empty($this->page_id)) : ?>
			<form id='product_type_form' class="global_form" method="post" action="<?php echo $this->url(array('action' => 'create', 'store_id' => $this->sitestore->store_id, 'page_id' => $this->page_id), 'sitestoreproduct_general', true); ?>" >
		<?php else: ?>
			<form id='product_type_form' class="global_form" method="post" action="<?php echo $this->url(array('action' => 'create', 'store_id' => $this->sitestore->store_id, 'tab' => $this->tab_selected_id), 'sitestoreproduct_general', true); ?>" >
		<?php endif; ?>
    <div>
      <div>
        <h3>1. <?php echo $this->translate("Choose a Product Type"); ?></h3>
        <p class="form-description"><?php echo $this->translate("Select a product type that best matches your product's profile. This selection will allow you to access the appropriate set of features required to sell your product on %s. (Note: You can not change the type of your product later.)", $this->site_title); ?></p>
        <div class="form-elements">
          <div id="product_type-wrapper" class="form-wrapper">
            <div id="product_type-label" class="form-label">
              <label class="required" for="product_type"><?php echo  $this->translate("Product Type") ?></label>
            </div>
            <div id="product_type-element" class="form-element">
              <select id="product_type" name="product_type" class="mright5">
              <?php foreach ($this->productType as $type) : ?>
                <option value="<?php echo $type ?>" >
                <?php
                    switch ($type) {
                      case 'simple':
                        echo $this->translate('Simple Product');
                        break;

                       case 'grouped':
                        echo $this->translate('Grouped Product');
                        break;

                       case 'configurable':
                        echo $this->translate('Configurable Product');
                        break;

                      case 'virtual':
                        echo $this->translate('Virtual Product');
                        break;

                       case 'bundled':
                        echo $this->translate('Bundled Product');
                        break;

                       case 'downloadable':
                        echo $this->translate('Downloadable Product');
                        break;

                      default:
                        echo $this->translate('Simple Product');
                        break;
                    }?>
                </option>  
                <?php endforeach; ?>
              </select>
              <a href="javascript:void(0)" onclick="window.open ('<?php echo $this->url(array('action' => 'product-type-details'), 'sitestoreproduct_general', true); ?>', null, 'width=450, height=400 resizable=0')" ><img src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Seaocore/externals/images/help.gif" /></a>
            </div>
          </div>
          <div id="buttons-wrapper" class="form-wrapper">
            <div id="buttons-label" class="form-label">              
            </div>
            <div id="buttons-element" class="form-element">
              <button type="submit" name="select" ><?php echo $this->translate("Create Product") ?></button>
            </div>
           </div>
        </div>
      </div>
    </div>
  </form>
<?php return; ?>
<?php endif; ?>
<!--END OF PRODUCT TYPE CONDITIONS-->
</div>
<script type="text/javascript">
var seao_dateFormat = '<?php echo Engine_Api::_()->seaocore()->getLocaleDateFormat(); ?>';
  en4.core.runonce.add(function()
  {
    new SEAOAutocompleter.Request.JSON('tags', '<?php echo $this->url(array('module' => 'seaocore', 'controller' => 'index', 'action' => 'tag-suggest', 'resourceType' => 'sitestoreproduct_product'), 'default', true) ?>', {
      'postVar' : 'text',
      'minLength': 1,
      'selectMode': 'pick',
      'autocompleteType': 'tag',
      'className': 'tag-autosuggest',
      'customChoices' : true,
      'filterSubset' : true,
      'multiple' : true,
      'injectChoice': function(token){
        var choice = new Element('li', {'class': 'autocompleter-choices', 'value':token.label, 'id':token.id});
        new Element('div', {'html': this.markQueryValue(token.label),'class': 'autocompleter-choice'}).inject(choice);
        choice.inputValue = token;
        this.addChoiceEvents(choice).inject(this.choices);
        choice.store('autocompleteChoice', token);
      }
    });
    
    var locationEl = document.getElementById('location');
    if (locationEl && (('<?php echo !Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationspecific', 0);?>') || ('<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationspecific', 0);?>' && '<?php echo !Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationspecificcontent', 0); ?>'))) {
            var autocompleteSECreateLocation = new google.maps.places.Autocomplete(locationEl);
            google.maps.event.addListener(autocompleteSECreateLocation, 'place_changed', function() {
                var place = autocompleteSECreateLocation.getPlace();
                if (!place.geometry) {                     return;
                }
                var address = '', country = '', state = '', zip_code = '', city = '';
                if (place.address_components) {
                var len_add = place.address_components.length;

                    for (var i = 0; i < len_add; i++) {
                        var types_location = place.address_components[i]['types'][0];                         if (types_location === 'country') {
                        country = place.address_components[i]['long_name'];
                            } else if (types_location === 'administrative_area_level_1') {
                        state = place.address_components[i]['long_name'];
                            } else if (types_location === 'administrative_area_level_2') {
                        city = place.address_components[i]['long_name'];
                            } else if (types_location === 'zip_code') {
                        zip_code = place.address_components[i]['long_name'];
                            } else if (types_location === 'street_address') {
                                if (address === '')                                 address = place.address_components[i]['long_name'];                             else
                                address = address + ',' + place.address_components[i]['long_name'];
                        } else if (types_location === 'locality') {
                        if (address === '')
                                address = place.address_components[i]['long_name'];                             else
                                address = address + ',' + place.address_components[i]['long_name'];
                        } else if (types_location === 'route') {
                        if (address === '')
                                address = place.address_components[i]['long_name'];                             else
                                address = address + ',' + place.address_components[i]['long_name'];
                        } else if (types_location === 'sublocality') {
                                if (address === '')                                 address = place.address_components[i]['long_name'];                             else
                                address = address + ',' + place.address_components[i]['long_name'];
                        }
                    }
                }
                var locationParams = '{"location" :"' + locationEl.value + '","latitude" :"' + place.geometry.location.lat() + '","longitude":"' + place.geometry.location.lng() + '","formatted_address":"' + place.formatted_address + '","address":"' + address + '","country":"' + country + '","state":"' + state + '","zip_code":"' + zip_code + '","city":"' + city + '"}';
                document.getElementById('locationParams').value = locationParams;             });
        }

  });
  
  
    
	window.addEvent('domready', function() { 
    <?php if( !empty($this->allowProductCode) ) : ?>
    		var e4 = $('product_code_msg-wrapper');
		$('product_code_msg-wrapper').setStyle('display', 'none');
		
				var pageurlcontainer = $('product_code-element');
				var language = '<?php echo $this->string()->escapeJavascript($this->translate('Check Availability')) ?>';
				var newdiv = document.createElement('div');
				newdiv.id = 'product_code_varify';
				newdiv.innerHTML = "<a href='javascript:void(0);'  name='check_availability' id='check_availability' onclick='PageUrlBlur();return false;' class='check_availability_button'>"+language+"</a> <br />";

				pageurlcontainer.insertBefore(newdiv, pageurlcontainer.childNodes[2]);
        <?php endif; ?>
//				checkDraft();
        
		checkDraft();
    
      <?php $accordian = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.accordian', 0); ?>
      <?php if (!empty($accordian) && empty($this->form_post)) : ?>
               new Fx.Accordion($('sitestoreproducts_create'), '#sitestoreproducts_create h4', '#sitestoreproducts_create .content')
      <?php endif; ?>
	});

<?php if( !empty($this->allowProductCode) ) : ?>
function PageUrlBlur() {
			if ($('product_code_alert') == null) {
				var pageurlcontainer = $('product_code-element');
				var newdiv = document.createElement('span');
				newdiv.id = 'product_code_alert';
				newdiv.innerHTML = '<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitestore/externals/images/loading.gif" />';
				pageurlcontainer.insertBefore(newdiv, pageurlcontainer.childNodes[3]);
			}
			else {
				$('product_code_alert').innerHTML = '<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitestore/externals/images/loading.gif" />';
			}
			var url = '<?php echo $this->url(array('action' => 'product-code-validation' ), 'sitestoreproduct_general', true);?>';
			en4.core.request.send(new Request.JSON({
				url : url,
				method : 'get',
				data : {
					product_code : $('product_code').value,
					format : 'html'
				},

				onSuccess : function(responseJSON) {
					if (responseJSON.success == 0) {
						$('product_code_alert').innerHTML = responseJSON.error_msg;
						if ($('product_code_alert')) {
							$('product_code_alert').innerHTML = responseJSON.error_msg;
						}
					}
					else {
						$('product_code_alert').innerHTML = responseJSON.success_msg;
						if ($('product_code_alert')) {
							$('product_code_alert').innerHTML = responseJSON.success_msg;
						}
					}
				}
		}));
	}
  <?php endif; ?>
  
	function checkDraft(){
		if($('draft')){
			if($('draft').value==1) {
				$("search-wrapper").style.display="none";
        
        if($("allow_purchase"))
          $("allow_purchase-wrapper").style.display="none";
        
//				$("search").checked= false;
			} else{
				$("search-wrapper").style.display="block";
        
        if($("allow_purchase"))
          $("allow_purchase-wrapper").style.display="block";
//				$("search").checked= true;
			}
		}
	}
  
  function expand(el){
    new Fx.Scroll(window).start(0, $('global_wrapper').getCoordinates().top);
    for(var i = 1; i<= 7; i++){
    var previous_id = 'img_' + parseInt(i);
    if($(previous_id))
        $(previous_id).innerHTML = '<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitestoreproduct/externals/images/icons/leftarrow.png" />';
    }
    if($('img_' + el.id))
      $('img_' + el.id).innerHTML = '<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitestoreproduct/externals/images/icons/arrow.png" />';
  }

// START CALENDAR WORK FOR PRODUCT START-END DATE AND DISCOUNT START-END DATE
  en4.core.runonce.add(function()
  {
    if('<?php  echo $this->expiry_setting; ?>' !=1){
      document.getElementById("end_date_enable-wrapper").style.display = "none";
    }
    initializeCalendarDate(seao_dateFormat, cal_start_date, cal_end_date, 'start_date', 'end_date');
    initializeCalendarDate(seao_dateFormat, cal_discount_start_date, cal_discount_end_date, 'discount_start_date', 'discount_end_date');
    cal_start_date_onHideStart();
    cal_discount_start_date_onHideStart();
  });
  
  var cal_start_date_onHideStart = function(){
    cal_starttimeDate_onHideStart(seao_dateFormat, cal_start_date, cal_end_date, 'start_date', 'end_date');
  };
  
  var cal_discount_start_date_onHideStart = function(){
    cal_starttimeDate_onHideStart(seao_dateFormat, cal_discount_start_date, cal_discount_end_date, 'discount_start_date', 'discount_end_date');
  };
// END CALENDAR WORK FOR PRODUCT START-END DATE AND DISCOUNT START-END DATE
  
  if( document.getElementById('product_ids-wrapper') ) {
    document.getElementById('product_ids-wrapper').setStyle('display', 'none');
  }
</script>
<?php // endif; ?>
<?php
/* Include the common user-end field switching javascript */
echo $this->partial('_jsSwitch.tpl', 'fields', array())
?>

<?php if ($this->category_count <= 0): ?>
    <div class="tip"> 
      <span>
				<?php echo $this->translate("Oops! Sorry it looks like something went wrong and you can not post a new product right now. Please try again after sometime."); ?>
      </span>
    </div>
<?php endif; ?>

<script type="text/javascript">
  if($('subcategory_id'))
    $('subcategory_id').style.display = 'none';
</script>

<script type="text/javascript">

  var getProfileType = function(category_id) {
    var mapping = <?php echo Zend_Json_Encoder::encode(Engine_Api::_()->getDbTable('categories', 'sitestoreproduct')->getMapping('profile_type')); ?>;
    for(i = 0; i < mapping.length; i++) {
      if(mapping[i].category_id == category_id)
        return mapping[i].profile_type;
    }
    return 0;
  }

  var defaultProfileId = '<?php echo '0_0_' . $this->defaultProfileId ?>'+'-wrapper';
  if($type($(defaultProfileId)) && typeof $(defaultProfileId) != 'undefined') {
    $(defaultProfileId).setStyle('display', 'none');
  }

	if($('overview-wrapper')) {
		<?php echo $this->tinyMCESEAO()->render(array('element_id' => '"overview"',
      'language' => $this->language,
      'directionality' => $this->directionality,
      'upload_url' => $this->upload_url)); ?>
	}
  <?php
  foreach ($this->languageData as $language_code):
    if ($this->defaultLanguage == $language_code) {
      continue;
    }
    if ($language_code == 'en') {
      $language_code = '';
    } else {
      $language_code = "_$language_code";
    }
    ?>
      
      if($('overview' + '<?php echo $language_code; ?>' + '-wrapper')) {
        
    <?php
    echo $this->tinyMCESEAO()->render(array('element_id' => '"overview' . $language_code . '"',
        'language' => $this->language,
        'directionality' => $this->directionality,
        'upload_url' => $this->upload_url));
    ?>
          
        }
<?php endforeach; ?>
   window.addEvent('domready',function() {
   
   var productType = $('product_type').value;
  if(productType == 'bundled'){
    showWeightType();
  }
   
   if(productType != 'grouped'){
     showDiscount();
   <?php if( !empty($this->showProductInventory) ) : ?>
    showOutOfStock();
    showStock();
   <?php endif; ?>
   <?php if( !empty($this->directPayment) && !empty($this->isDownPaymentEnable) ) : ?>
     showDownpayment();
   <?php endif; ?>
  }
   showEndDate();
   
   <?php if(!empty($this->form_post)): ?>
   for(var i = 1; i<= 7; i++){
    var previous_id = 'img_' + parseInt(i);
    if($(previous_id))
        $(previous_id).innerHTML = '';
    i = i.toString();
    if($(i))
       $(i).removeAttribute("onclick");
    }
   <?php endif;?>
   });
   

    function showDownpayment() {
      var downpayment_radios = document.getElementsByName("downpayment");
      var downpayment_radioValue;
      if (downpayment_radios[0].checked) {
        downpayment_radioValue = downpayment_radios[0].value; 
      }else {
        downpayment_radioValue = downpayment_radios[1].value; 
      }
      if( downpayment_radioValue == 1 ) {
        document.getElementById('downpaymentvalue-wrapper').style.display = 'block';
      } else {
        document.getElementById('downpaymentvalue-wrapper').style.display = 'none';
      }
    }
   
   function showDiscount(){
        var radios = document.getElementsByName("discount");
         var radioValue;
        if (radios[0].checked) {
            radioValue = radios[0].value; 
          }else {
            radioValue = radios[1].value; 
          }
          if(radioValue == 0) {
            document.getElementById('handling_type-wrapper').style.display = 'none';
            document.getElementById('discount_rate-wrapper').style.display = 'none';
            document.getElementById('discount_price-wrapper').style.display = 'none';
            document.getElementById('discount_start_date-wrapper').style.display = 'none';
            document.getElementById('discount_end_date-wrapper').style.display = 'none';
            document.getElementById('discount_permanant-wrapper').style.display = 'none';
            document.getElementById('user_type-wrapper').style.display = 'none';
          } else{
            document.getElementById('handling_type-wrapper').style.display = 'block';
            document.getElementById('discount_start_date-wrapper').style.display = 'block';
            document.getElementById('discount_end_date-wrapper').style.display = 'block';
            document.getElementById('discount_permanant-wrapper').style.display = 'block';
            document.getElementById('user_type-wrapper').style.display = 'block';
            showDiscountType();
            showDiscountEndDate();
          }
   }
   
   
  function showOutOfStock(){
     var radios = document.getElementsByName("out_of_stock");
         var radioValue;
        if (radios[0].checked) {
            radioValue = radios[0].value; 
          }else {
            radioValue = radios[1].value; 
          }
          if(radioValue == 0) {
            document.getElementById('out_of_stock_action-wrapper').style.display="none";
          } else{
           document.getElementById('out_of_stock_action-wrapper').style.display="block";
            
          }
  }

  function showWeightType(){
    var radios = document.getElementsByName("weight_type");
         var radioValue;
        if (radios[0].checked) {
            radioValue = radios[0].value; 
          }else {
            radioValue = radios[1].value; 
          }
          if(radioValue == 1) {
            document.getElementById('weight-wrapper').style.display="none";
          } else{
           document.getElementById('weight-wrapper').style.display="block";            
          }
  }
  
  function showStock(){
    var radios = document.getElementsByName("stock_unlimited");
         var radioValue;
        if (radios[0].checked) {
            radioValue = radios[0].value; 
          }else {
            radioValue = radios[1].value; 
          }
          if(radioValue == 1) {
            document.getElementById('in_stock-wrapper').style.display="none";
            document.getElementById('out_of_stock-wrapper').style.display="none";
            document.getElementById('out_of_stock_action-wrapper').style.display="none";
          } else{
           document.getElementById('in_stock-wrapper').style.display="block";
           document.getElementById('out_of_stock-wrapper').style.display="block";
           showOutOfStock();
          }
  }
  
   function showDiscountEndDate(){
      var radios = document.getElementsByName("discount_permanant");
         var radioValue;
        if (radios[0].checked) {
            radioValue = radios[0].value; 
          }else {
            radioValue = radios[1].value; 
          }
          if(radioValue == 1) {
            document.getElementById('discount_end_date-wrapper').style.display="none";
          } else{
           document.getElementById('discount_end_date-wrapper').style.display="block";
           
          }    
  }
  
   function showEndDate(){
      var radios = document.getElementsByName("end_date_enable");
         var radioValue;
        if (radios[0].checked) {
            radioValue = radios[0].value; 
          }else {
            radioValue = radios[1].value; 
          }
          if(radioValue == 0) {
            document.getElementById('end_date-wrapper').style.display="none";
          } else{
           document.getElementById('end_date-wrapper').style.display="block";
           
          }    
  }

        function showDiscountType(){
        if($('handling_type')){
          if($('handling_type').value == 1) {
            document.getElementById('discount_price-wrapper').style.display = 'none';
            document.getElementById('discount_rate-wrapper').style.display = 'block';		
          } else{
            document.getElementById('discount_price-wrapper').style.display = 'block';
            document.getElementById('discount_rate-wrapper').style.display = 'none';
          }
        }
      }
      
  var maxRecipients = 10000;
  var packageRequest;
  var storeidsAutocomplete;
  var productidsAutocomplete;

  var is_simple;
  var is_configurable;
  var is_virtual;
  var is_downloadable;
  
  en4.core.runonce.add(function() {

    var productType = $('product_type').value;
  if((productType == 'bundled' ) || productType == 'grouped'){
		productidsAutocomplete = new SEAOAutocompleter.Request.JSON('product_name', '<?php echo $this->url(array('module' => 'sitestoreproduct', 'controller' => 'report', 'action' => 'suggestproducts', 'store_id' => $this->sitestore->store_id), 'default', true); ?>', {
			'postVar' : 'search',
         'postData' : {'store_ids': <?php echo $this->sitestore->store_id ?>, 'product_ids': $('product_ids').value, 'create': 1, 'is_simple': is_simple, 'is_configurable': is_configurable, 'is_virtual': is_virtual, 'is_downloadable': is_downloadable},
			'minLength': 1,
			'delay' : 250,
			'selectMode': 'pick',
			'elementValues': 'product_ids',
			'autocompleteType': 'message',
			'multiple': true,
			'className': 'tag-autosuggest seaocore-autosuggest',
			'filterSubset' : true,
			'tokenFormat' : 'object',
			'tokenValueKey' : 'label',
			'injectChoice': function(token) {
				var choice = new Element('li', {
					'class': 'autocompleter-choices',
					'html': token.photo,
					'id':token.label
				});

				new Element('div', {
					'html': this.markQueryValue(token.label),
					'class': 'autocompleter-choice'
				}).inject(choice);
        
				this.addChoiceEvents(choice).inject(this.choices);
				choice.store('autocompleteChoice', token);
            
			},
//          'onChoiceSelect' : function(choice) {           
//            var data = choice.retrieve('autocompleteChoice');
//            var product_type = data.product_type;
//            if( product_type == 'simple' )
//          },
			onPush : function() {
//   var test = choice.label;alert('hi = '+ test);

				if ($('product_ids-wrapper')) {
					$('product_ids-wrapper').style.display='block';
				}

				if( $(this.options.elementValues).value.split(',').length >= maxRecipients ) {
					this.element.disabled = true;
				}
        
        productidsAutocomplete.setOptions({
          'postData' : {'store_ids': <?php echo $this->sitestore->store_id ?>, 'product_ids': $('product_ids').value, 'create': 1, 'is_simple': is_simple, 'is_configurable': is_configurable, 'is_virtual': is_virtual, 'is_downloadable': is_downloadable}
        });
			}
		});
    }
    if(productType == 'bundled')
        bundleProductTypes();
    });
    
  function bundleProductTypes()
  {
    if( $("bundle_product_type-simple") )
      is_simple = $("bundle_product_type-simple").checked;
    if( $("bundle_product_type-configurable") )
      is_configurable = $("bundle_product_type-configurable").checked;
    if( $("bundle_product_type-virtual") )
      is_virtual = $("bundle_product_type-virtual").checked;
    if( $("bundle_product_type-downloadable") )
      is_downloadable = $("bundle_product_type-downloadable").checked;
    
    if( !is_simple && !is_configurable && !is_virtual && !is_downloadable )
      $("product_name").disabled = true;
    else
      $("product_name").disabled = false;
    
    productidsAutocomplete.setOptions({
          'postData' : {'store_ids': <?php echo $this->sitestore->store_id ?>, 'product_ids': $('product_ids').value, 'create': 1, 'is_simple': is_simple, 'is_configurable': is_configurable, 'is_virtual': is_virtual, 'is_downloadable': is_downloadable}
        });
  }
  
  function removeFromToValue(id, elmentValue,element) {
    // code to change the values in the hidden field to have updated values
    // when recipients are removed.
    var toValues = $(elmentValue).value;
    var toValueArray = toValues.split(",");
    var toValueIndex = "";

    var checkMulti = id.search(/,/);

    // check if we are removing multiple recipients
    if (checkMulti!=-1) {
      var recipientsArray = id.split(",");
      for (var i = 0; i < recipientsArray.length; i++){
        removeToValue(recipientsArray[i], toValueArray, elmentValue);
      }
    } else {
      removeToValue(id, toValueArray, elmentValue);
    }

    // hide the wrapper for element if it is empty
    if ($(elmentValue).value==""){
      $(elmentValue+'-wrapper').setStyle('height', '0');
      $(elmentValue+'-wrapper').setStyle('display', 'none');
    }
    $(element).disabled = false;
    productidsAutocomplete.setOptions({
          'postData' : {'store_ids': <?php echo $this->sitestore->store_id ?>, 'product_ids': $('product_ids').value, 'create': 1, 'is_simple': is_simple, 'is_configurable': is_configurable, 'is_virtual': is_virtual, 'is_downloadable': is_downloadable}
        });
  }
 
  function removeToValue(id, toValueArray, elmentValue) {
    for (var i = 0; i < toValueArray.length; i++){
      if (toValueArray[i]==id) toValueIndex =i;
    }
    toValueArray.splice(toValueIndex, 1);
    $(elmentValue).value = toValueArray.join();
  }
</script>


<?php
// SHOW DEFAULT ADDED PRODUCTS IN THE EDIT FORM.
if (!empty($this->productArray) && !empty($this->tempMappedIdsStr)):
  $productSpan = '<input type="hidden" id="product_ids" value="' . $this->tempMappedIdsStr . '" name="product_ids">';
  foreach ($this->productArray as $product) {
    $product['title'] = str_replace("'", "\'", $product['title']);
    $product['title'] = str_replace('"', '\"', $product['title']);
    $productSpan .= '<span id="tospan_' . $product['title'] . '_' . $product['id'] . '" class="tag">' . $product['title'] . '<a onclick="this.parentNode.destroy();removeFromToValue(&quot;2&quot;, &quot;product_ids&quot; , &quot;product_name&quot;, &quot;product_ids&quot;);" href="javascript:void(0);">x</a></span>';
  }
  ?>
  <script type="text/javascript">
    window.addEvent('domready', function() {
      document.getElementById("product_ids-element").innerHTML = '<?php echo $productSpan; ?>';
      document.getElementById("product_ids-wrapper").style.display = 'block';
    });
  </script>
<?php endif; ?>

<?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct_brands', 1)): ?>
  <script type="text/javascript">
    $('tags').addEvent('keypress', function (event) {
      if (event.key == ',') {
        alert('<?php echo $this->string()->escapeJavascript($this->translate("Only one brand can be associate with one product. You are not allowed to use comma.")) ?>');
        return false;
      }
    }); 

    $('tags').addEvent('paste', function (event) { 
      console.log(event);

      (function(){
        if($('tags').value.indexOf(',') != -1) {
          var tagValues = $('tags').value.split(',');
          $('tags').value = tagValues[0];      
          alert('<?php echo $this->string()->escapeJavascript($this->translate("Only one brand can be associate with one product. You are not allowed to use comma.")) ?>');
        }
      }).delay(100);

    });  

  </script>  
<?php endif;?>
  <script type="text/javascript">
    
    en4.core.runonce.add(function() {
      var multiLanguage = '<?php echo $this->multiLanguage; ?>';
      var languageCount = '<?php echo $this->languageCount; ?>';
      var titleParent = $('<?php echo $this->add_show_hide_title_link; ?>').getParent().getParent();
      var bodyParent = $('<?php echo $this->add_show_hide_body_link; ?>').getParent().getParent();
      var overviewParent = $('<?php echo $this->add_show_hide_overview_link; ?>').getParent().getParent();
      if (multiLanguage == 1 && languageCount > 1) {
        $('multiLanguageTitleLinkShow').inject(titleParent, 'after');
        $('multiLanguageTitleLinkHide').inject(titleParent, 'after');
        $('multiLanguageBodyLinkShow').inject(bodyParent, 'after');
        $('multiLanguageBodyLinkHide').inject(bodyParent, 'after');
        $('multiLanguageOverviewLinkShow').inject(overviewParent, 'after');
        $('multiLanguageOverviewLinkHide').inject(overviewParent, 'after');
        multiLanguageTitleOption(1);
        multiLanguageBodyOption(1);
        multiLanguageOverviewOption(1);
      }
      
    }); 
    
    var multiLanguageTitleOption = function(show) {
      
<?php
foreach ($this->languageData as $language_code):
  if ($this->defaultLanguage == $language_code) {
    continue;
  }
  if ($language_code == 'en') {
    $language_code = '';
  } else {
    $language_code = "_$language_code";
  
  ?>
      if (show == 1) {
          $('title' + '<?php echo $language_code; ?>' + '-wrapper').setStyle('display', 'none');
          $('multiLanguageTitleLinkShow').setStyle('display', 'block');
          $('multiLanguageTitleLinkHide').setStyle('display', 'none');
        }
        else {
          $('title' + '<?php echo $language_code; ?>' + '-wrapper').setStyle('display', 'block');
          $('multiLanguageTitleLinkShow').setStyle('display', 'none');
          $('multiLanguageTitleLinkHide').setStyle('display', 'block');
        }
<?php } endforeach; ?>
  }
  
      var multiLanguageBodyOption = function(show) {
      
<?php
foreach ($this->languageData as $language_code):
  if ($this->defaultLanguage == $language_code) {
    continue;
  }
  if ($language_code == 'en') {
    $language_code = '';
  } else {
    $language_code = "_$language_code";
  
  ?>
      if (show == 1) {
          $('body' + '<?php echo $language_code; ?>' + '-wrapper').setStyle('display', 'none');
          $('multiLanguageBodyLinkShow').setStyle('display', 'block');
          $('multiLanguageBodyLinkHide').setStyle('display', 'none');
        }
        else {
          $('body' + '<?php echo $language_code; ?>' + '-wrapper').setStyle('display', 'block');
          $('multiLanguageBodyLinkShow').setStyle('display', 'none');
          $('multiLanguageBodyLinkHide').setStyle('display', 'block');
        }
<?php } endforeach; ?>
  }
  
  
  
      var multiLanguageOverviewOption = function(show) {
      
<?php
foreach ($this->languageData as $language_code):
  if ($this->defaultLanguage == $language_code) {
    continue;
  }
  if ($language_code == 'en') {
    $language_code = '';
  } else {
    $language_code = "_$language_code";
  
  ?>
      if (show == 1) {
          $('overview' + '<?php echo $language_code; ?>' + '-wrapper').setStyle('display', 'none');
          $('multiLanguageOverviewLinkShow').setStyle('display', 'block');
          $('multiLanguageOverviewLinkHide').setStyle('display', 'none');
        }
        else {
          $('overview' + '<?php echo $language_code; ?>' + '-wrapper').setStyle('display', 'block');
          $('multiLanguageOverviewLinkShow').setStyle('display', 'none');
          $('multiLanguageOverviewLinkHide').setStyle('display', 'block');
        }
<?php } endforeach; ?>
  }
    <?php if(empty($this->isCommentsAllow)) : ?>
$('auth_comment-wrapper').style.display = "none";
<?php endif; ?>
  //FUNCTION FOR SHOWING THE SELLING PRICE
    function showSellingPrice(){
    if($('product_selling_price-wrapper')){

     var url = '<?php echo $this->url(array('action' => 'get-product-selling-price'), 'sitestoreproduct_product_general', true); ?>';
     var product_price = $('price').value;
     var special_vat = $('special_vat').value;
     var handling_type = $('handling_type').value;
     var discount_value = 0;
     var isDiscount = false;

     if($('discount-wrapper') && $('discount-1').checked){
       isDiscount = true;
       if($('handling_type').value == 0){
         discount_value = $('discount_price').value;
       }else{
         discount_value = $('discount_rate').value;
       }
     }else{
      discount_value = 0;
     }

     en4.core.request.send(new Request.JSON({
      url : url,
      data : {
        format : 'json',
        store_id : <?php echo $this->sitestore->store_id; ?>,
        price : product_price,
        special_vat : special_vat,
        discount_value : discount_value,
        discount_type : handling_type,
        is_discount : isDiscount
      },
      onRequest: function(){
          $('sellingPriceLoading').style.display = 'block';
      },
      onSuccess : function(responseJSON) {
        $('sellingPriceLoading').style.display = 'none';
        if($('product_selling_price')){
          $('product_selling_price').value = responseJSON.value;
        }
      }
      }));
    }
  }
  
  window.addEvent('load', function() { 
    var locationEl = document.getElementById('location');
    var locationId = '<?php echo $this->locationId; ?>';
    var latitudeValue = '<?php echo $this->locationDetails->latitude; ?>';
    var longitudeValue = '<?php echo $this->locationDetails->longitude; ?>';
    var formattedAddressValue = '<?php echo $this->locationDetails->formatted_address; ?>';
    var addressValue = '<?php echo $this->locationDetails->address; ?>';
    var countryValue = '<?php echo $this->locationDetails->country; ?>';
    var stateValue = '<?php echo $this->locationDetails->state; ?>';
    var zipcodeValue = '<?php echo $this->locationDetails->zipcode; ?>';
    var cityValue = '<?php echo $this->locationDetails->city; ?>';
    if (locationEl && locationEl.value && locationId) {
        var locationParams = '{"location" :"' + locationEl.value + '","latitude" :"' + latitudeValue + '","longitude":"' + longitudeValue + '","formatted_address":"' + formattedAddressValue + '","address":"' + addressValue + '","country":"' + countryValue + '","state":"' + stateValue + '","zip_code":"' + zipcodeValue + '","city":"' + cityValue + '"}';
        document.getElementById('locationParams').value = locationParams;
    }
  });
  </script>