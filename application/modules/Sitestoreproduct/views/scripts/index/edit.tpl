<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: edit.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
$cateDependencyArray = Engine_Api::_()->getDbTable('categories', 'sitestoreproduct')->getCatDependancyArray();
$subCateDependencyArray  = Engine_Api::_()->getDbTable('categories', 'sitestoreproduct')->getSubCatDependancyArray();
$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'externals/calendar/calendar.compat.js');
$this->headLink()
        ->appendStylesheet($this->layout()->staticBaseUrl . 'externals/calendar/styles.css');
$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/autocompleter/SEAOAutocompleter.js')
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/autocompleter/SEAOAutocompleter.Local.js')
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/autocompleter/SEAOAutocompleter.Request.js');
?>
<?php $this->tinyMCESEAO()->addJS();?>
<script type="text/javascript">
  en4.core.runonce.add(function()
  {
    checkDraft();
    new Autocompleter.Request.JSON('tags', '<?php echo $this->url(array('module' => 'seaocore', 'controller' => 'index', 'action' => 'tag-suggest', 'resourceType' => 'sitestoreproduct_product'), 'default', true) ?>', {
      'postVar' : 'text',
      'minLength': 1,
      'selectMode': 'pick',
      'autocompleteType': 'tag',
      'className': 'tag-autosuggest',
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
  });
  
  function checkDraft(){
    if($('draft')){
      if($('draft').value==1) {
        $("search-wrapper").style.display="none";
        $("search").checked= false;
        
        if($("allow_purchase"))
          $("allow_purchase-wrapper").style.display="none";
      } else{
        $("search-wrapper").style.display="block";
        $("search").checked= true;
        if($("allow_purchase"))
          $("allow_purchase-wrapper").style.display="block";
      }
    }
  }

  var updateTextFields = function(endsettings)
  {
    var endtime_element = document.getElementById("end_date-wrapper");
    endtime_element.style.display = "none";
    if (endsettings.value == 0)
    {
      endtime_element.style.display = "none";
      return;
    }

    if (endsettings.value == 1)
    {
      endtime_element.style.display = "block";
      return;
    }
  }
  en4.core.runonce.add(function(){
    if( document.getElementById('product_ids-wrapper') ) {
      document.getElementById('product_ids-wrapper').setStyle('display', 'none');
    }
    var endtime_element = document.getElementById("end_date-wrapper");
    if('<?php echo $this->expiry_setting; ?>' !='1'){
      document.getElementById("end_date_enable-wrapper").style.display = "none";
      endtime_element.style.display = "none";
    }else{
      if($("end_date_enable-1").checked){
        endtime_element.style.display = "block";
      }else{
        endtime_element.style.display = "none";
      }
    }
    if($('end_date-date')){
      // check end date and make it the same date if it's too
      cal_end_date.calendars[0].start = new Date( "<?php echo (string) date('Y-m-d') . ' 00:00:00'; ?>" );
      // redraw calendar
      cal_end_date.navigate(cal_end_date.calendars[0], 'm', 1);
      cal_end_date.navigate(cal_end_date.calendars[0], 'm', -1);
    }
  });
 
</script>

<?php
/* Include the common user-end field switching javascript */
echo $this->partial('_jsSwitch.tpl', 'fields', array())
?>

<?php include_once APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/scripts/_DashboardNavigation.tpl'; ?>

<div class="sr_sitestoreproduct_dashboard_content">
  <?php
    if( !empty($this->sitestoreproduct) && !empty($this->sitestore) ):
      echo $this->partial('application/modules/Sitestoreproduct/views/scripts/dashboard/header.tpl', array('sitestoreproduct'=>$this->sitestoreproduct, 'sitestore'=>$this->sitestore));
    endif;
    ?>
  <div class="sitestoreproduct_create_product">
    
  <?php   

echo $this->form->setAttrib('class', 'global_form sr_sitestoreproduct_create_list_form')->render($this);
    ?>
  </div>	
</div>
      <?php if ($this->languageCount > 1 && $this->multiLanguage): ?>
       <div id="multiLanguageTitleLinkShow" class="form-wrapper">
        <div class="form-label">&nbsp;</div>
        <div class="form-element">
          <a href="javascript: void(0);" onclick="return multiLanguageTitleOption(2);" style="text-decoration: none;"><b><?php echo $this->translate("Edit Title in the multiple languages supported by this website."); ?></b></a>
        </div>
      </div>

      <div id="multiLanguageTitleLinkHide" class="form-wrapper">
        <div class="form-label">&nbsp;</div>
        <div class="form-element">
          <a href="javascript: void(0);" onclick="return multiLanguageTitleOption(1);" style="text-decoration: none;"><b><?php echo $this->translate("Edit Title in the primary language of this website."); ?></b></a>
        </div>
      
      </div>
          <div id="multiLanguageBodyLinkShow" class="form-wrapper">
        <div class="form-label">&nbsp;</div>
        <div class="form-element">
          <a href="javascript: void(0);" onclick="return multiLanguageBodyOption(2);" style="text-decoration: none;"><b><?php echo $this->translate("Edit Short Description in the multiple languages supported by this website."); ?></b></a>
        </div>
      </div>

      <div id="multiLanguageBodyLinkHide" class="form-wrapper">
        <div class="form-label">&nbsp;</div>
        <div class="form-element">
          <a href="javascript: void(0);" onclick="return multiLanguageBodyOption(1);" style="text-decoration: none;"><b><?php echo $this->translate("Edit Short Description in the primary language of this website."); ?></b></a>
        </div>
      </div>

    <?php endif; ?>
</div>
<script type="text/javascript">
  
  var category_edit = '<?php echo $this->category_edit ?>';  

  var prefieldForm = function() {
<?php
$defaultProfileId = "0_0_" . $this->defaultProfileId;
foreach ($this->form->getSubForms() as $subForm) {
  foreach ($subForm->getElements() as $element) {

    $elementGetName = $element->getName();
    $elementGetValue = $element->getValue();
    $elementGetType = $element->getType();

    if ($elementGetName != $defaultProfileId && $elementGetName != '' && $elementGetName != null && $elementGetValue != '' && $elementGetValue != null) {

      if (!is_array($elementGetValue) && $elementGetType == 'Engine_Form_Element_Radio') {
        ?>
                    $('<?php echo $elementGetName . "-" . $elementGetValue ?>').checked = 1; 
      <?php } elseif (!is_array($elementGetValue) && $elementGetType == 'Engine_Form_Element_Checkbox') { ?>
                  $('<?php echo $elementGetName ?>').checked = <?php echo $elementGetValue ?>;
        <?php
      } elseif (is_array($elementGetValue) && ($elementGetType == 'Engine_Form_Element_MultiCheckbox' || $elementGetType == 'Fields_Form_Element_Ethnicity' || $elementGetType == 'Fields_Form_Element_LookingFor' || $elementGetType == Fields_Form_Element_PartnerGender)) {
        foreach ($elementGetValue as $key => $value) {
          ?>
                            $('<?php echo $elementGetName . "-" . $value ?>').checked = 1;
          <?php
        }
      } elseif (is_array($elementGetValue) && $elementGetType == 'Engine_Form_Element_Multiselect') {
        foreach ($elementGetValue as $key => $value) {
          $key_temp = array_search($value, array_keys($element->options));
          if ($key !== FALSE) {
            ?>
                                $('<?php echo $elementGetName ?>').options['<?php echo $key_temp ?>'].selected = 1;
            <?php
          }
        }
      } elseif (!is_array($elementGetValue) && ($elementGetType == 'Engine_Form_Element_Text' || $elementGetType == 'Engine_Form_Element_Textarea' || $elementGetType == 'Fields_Form_Element_AboutMe' || $elementGetType == 'Fields_Form_Element_Aim' || $elementGetType == 'Fields_Form_Element_City' || $elementGetType == 'Fields_Form_Element_Facebook' || $elementGetType == 'Fields_Form_Element_FirstName' || $elementGetType == 'Fields_Form_Element_Interests' || $elementGetType == 'Fields_Form_Element_LastName' || $elementGetType == 'Fields_Form_Element_Location' || $elementGetType == 'Fields_Form_Element_Twitter' || $elementGetType == 'Fields_Form_Element_Website' || $elementGetType == 'Fields_Form_Element_ZipCode')) {
        ?>
                        $('<?php echo $elementGetName ?>').value = "<?php echo $this->string()->escapeJavascript($elementGetValue, false) ?>";
      <?php } elseif (!is_array($elementGetValue) && $elementGetType != 'Engine_Form_Element_Date' && $elementGetType != 'Fields_Form_Element_Birthdate' && $elementGetType != 'Engine_Form_Element_Heading') { ?>
                      $('<?php echo $elementGetName ?>').value = "<?php echo $this->string()->escapeJavascript($elementGetValue, false) ?>";
        <?php
      }
    }
  }
}
?>
  }

  window.addEvent('domready', function() {
<?php if ($this->profileType): ?>			
            $('<?php echo '0_0_' . $this->defaultProfileId ?>').value= <?php echo $this->profileType ?>;
            changeFields($('<?php echo '0_0_' . $this->defaultProfileId ?>'));
<?php endif; ?>
<?php $accordian = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.accordian', 0); ?>
<?php if(!empty($accordian) && empty($this->form_post)) : ?>
    new Fx.Accordion($('sitestoreproducts_create'), '#sitestoreproducts_create h4', '#sitestoreproducts_create .content')
    <?php endif; ?>
     
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

        if(category_edit == 1) {
          var subcatid = '<?php echo $this->sitestoreproduct->subcategory_id; ?>';
var cateDependencyArray = new Array(); 
var subCateDependencyArray = new Array(); 
           <?php foreach($cateDependencyArray as $cat) : ?>
							cateDependencyArray.push(<?php echo $cat ?>);
						<?php endforeach; ?>
           <?php foreach($subCateDependencyArray as $cat) : ?>
							subCateDependencyArray.push(<?php echo $cat ?>);
						<?php endforeach; ?>

          var show_subcat = 1;
	  var subcategories = function(category_id, subcatid, subcatname,subsubcatid)
		{
      if(subcatid > 0) {
				changesubcategory(subcatid);
      }
			if(!in_array(cateDependencyArray, category_id)) {
				if($('subcategory_id-wrapper'))
					$('subcategory_id-wrapper').style.display = 'none';
				if($('subcategory_id-label'))
					$('subcategory_id-label').style.display = 'none'; 
 
				if($('buttons-wrapper')) {
					$('buttons-wrapper').style.display = 'block';
				}   
				return;
			}


      if($('subcategory_backgroundimage'))
      $('subcategory_backgroundimage').style.display = 'block';
      if($('subcategory_id'))
      $('subcategory_id').style.display = 'none';
      if($('subsubcategory_id'))
      $('subsubcategory_id').style.display = 'none';
      if($('subcategory_id-label'))
        $('subcategory_id-label').style.display = 'none';
        if($('subcategory_backgroundimage'))
        $('subcategory_backgroundimage').innerHTML = '<div class="form-label"></div><div  class="form-element"><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitestore/externals/images/loading.gif" /></center></div>';

      
			if($('buttons-wrapper')) {
		  	$('buttons-wrapper').style.display = 'none';
			}
			if($('subsubcategory_id-wrapper'))
				$('subsubcategory_id-wrapper').style.display = 'none';
			if($('subsubcategory_id-label'))
				$('subsubcategory_id-label').style.display = 'none'; 
			var url = '<?php echo $this->url(array('action' => 'sub-category'), 'sitestoreproduct_general', true);?>';
			en4.core.request.send(new Request.JSON({      	
		    url : url,
		    data : {
		      format : 'json',
		      category_id_temp : category_id
				},
		    onSuccess : function(responseJSON) { 
		    	if($('buttons-wrapper')) {
				  	$('buttons-wrapper').style.display = 'block';
					}
          if($('subcategory_backgroundimage'))
          $('subcategory_backgroundimage').style.display = 'none';
          
		    	clear('subcategory_id');
		    	var  subcatss = responseJSON.subcats;
		      addOption($('subcategory_id')," ", '0');
          for (i=0; i< subcatss.length; i++) {
            addOption($('subcategory_id'), subcatss[i]['category_name'], subcatss[i]['category_id']);
            if(show_subcat == 0) {
              if($('subcategory_id'))
              $('subcategory_id').disabled = 'disabled';
              if($('subsubcategory_id'))
              $('subsubcategory_id').disabled = 'disabled';
            }
            if($('subcategory_id')) {
              $('subcategory_id').value = subcatid;
            }
          }
						
          if(category_id == 0) {
            clear('subcategory_id');
            if($('subcategory_id'))
            $('subcategory_id').style.display = 'none';
            if($('subcategory_id-label'))
            $('subcategory_id-label').style.display = 'none';
          }
		    }
			  }), {
          "force":true
        });
		};

function in_array(ArrayofCategories, value) {
	for(var i=0;i<ArrayofCategories.length;i++) {
		if(ArrayofCategories[i] == value) {
			return true;
		}
	}
	return false;
}



    var changesubcategory = function(subcatid)
		{
			if($('buttons-wrapper')) {
		  	$('buttons-wrapper').style.display = 'none';
			}

			if(!in_array(subCateDependencyArray, subcatid)) {
				if($('subsubcategory_id-wrapper'))
					$('subsubcategory_id-wrapper').style.display = 'none';
				if($('subsubcategory_id-label'))
					$('subsubcategory_id-label').style.display = 'none';   
				if($('buttons-wrapper')) {
					$('buttons-wrapper').style.display = 'block';
				}   
				return;
			}
      if($('subsubcategory_backgroundimage'))
      $('subsubcategory_backgroundimage').style.display = 'block';
      if($('subsubcategory_id'))   
      $('subsubcategory_id').style.display = 'none';
      if($('subsubcategory_id-label'))
        $('subsubcategory_id-label').style.display = 'none';
        if($('subsubcategory_backgroundimage'))
        $('subsubcategory_backgroundimage').innerHTML = '<div class="form-label"></div><div  class="form-element"><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitestore/externals/images/loading.gif" /></center></div>';
  
      
			if($('buttons-wrapper')) {
		  	$('buttons-wrapper').style.display = 'none';
			}
			var url = '<?php echo $this->url(array('action' => 'subsub-category'), 'sitestoreproduct_general', true);?>';
   		var request = new Request.JSON({
		    url : url,
		    data : {
		      format : 'json',
		      subcategory_id_temp : subcatid
				},
		    onSuccess : function(responseJSON) {
		    	if($('buttons-wrapper')) {
				  	$('buttons-wrapper').style.display = 'block';
					}
          if($('subsubcategory_backgroundimage'))
          $('subsubcategory_backgroundimage').style.display = 'none';

		    	clear('subsubcategory_id');
		    	var  subsubcatss = responseJSON.subsubcats;   
          if($('subsubcategory_id')){
						addSubOption($('subsubcategory_id')," ", '0');
						for (i=0; i< subsubcatss.length; i++) {
							addSubOption($('subsubcategory_id'), subsubcatss[i]['category_name'], subsubcatss[i]['category_id']);
							if($('subsubcategory_id')) {
								$('subsubcategory_id').value = subsubcatid;
							}
						}
          }
		    }
			});
      request.send();
		};

		function clear(ddName)
		{ 
			if(document.getElementById(ddName)) {
			   for (var i = (document.getElementById(ddName).options.length-1); i >= 0; i--) 
			   { 
			      document.getElementById(ddName).options[ i ]=null; 			      
			   } 
			}
		}
    function addOption(selectbox,text,value )
    {
			if($('subcategory_id')) {
				var optn = document.createElement("OPTION");
				optn.text = text;
				optn.value = value;
		
				if(optn.text != '' && optn.value != '') {
					if($('subcategory_id'))
					$('subcategory_id').style.display = 'block';
					if($('subcategory_id-wrapper'))
						$('subcategory_id-wrapper').style.display = 'block';
					if($('subcategory_id-label'))
						$('subcategory_id-label').style.display = 'block';
					selectbox.options.add(optn);
				} else {
					if($('subcategory_id'))
					$('subcategory_id').style.display = 'none';
					if($('subcategory_id-wrapper'))
						$('subcategory_id-wrapper').style.display = 'none';
					if($('subcategory_id-label'))
						$('subcategory_id-label').style.display = 'none';
					selectbox.options.add(optn);
				}
	    }
    }
	  function addSubOption(selectbox,text,value )
    {
      if($('subsubcategory_id')) {
				var optn = document.createElement("OPTION");
				optn.text = text;
				optn.value = value;
				if(optn.text != '' && optn.value != '') {
					if($('subsubcategory_id'))
					$('subsubcategory_id').style.display = 'block';
					if($('subsubcategory_id-wrapper'))
						$('subsubcategory_id-wrapper').style.display = 'block';
					if($('subsubcategory_id-label'))
						$('subsubcategory_id-label').style.display = 'block';
					selectbox.options.add(optn);
				} else {
					if($('subsubcategory_id'))
					$('subsubcategory_id').style.display = 'none';
					if($('subsubcategory_id-wrapper'))
						$('subsubcategory_id-wrapper').style.display = 'none';
					if($('subsubcategory_id-label'))
						$('subsubcategory_id-label').style.display = 'none';
					selectbox.options.add(optn);
				}
      }
    }
//           var subcategory = function(category_id, subcatid, subcatname,subsubcatid)
//           {
//             changesubcategory(subcatid, subsubcatid);
//             if($('buttons-wrapper')) {
//               $('buttons-wrapper').style.display = 'none';
//             }
//             if(subcatid == '')
//               if($('subcategory_id-wrapper'))
//                 $('subcategory_id-wrapper').style.display = 'block';
// 
//             var url = '<?php echo $this->url(array('action' => 'sub-category'), "sitestoreproduct_general", true); ?>';
//             en4.core.request.send(new Request.JSON({      	
//               url : url,
//               data : {
//                 format : 'json',
//                 category_id_temp : category_id
//               },
//               onSuccess : function(responseJSON) { 
//                 if($('buttons-wrapper')) {
//                   $('buttons-wrapper').style.display = 'block';
//                 }
//                 clear('subcategory_id');
//                 var  subcatss = responseJSON.subcats;
//                 addOption($('subcategory_id')," ", '0');
//                 for (i=0; i< subcatss.length; i++) {
//                   addOption($('subcategory_id'), subcatss[i]['category_name'], subcatss[i]['category_id']);
//                   if(show_subcat == 0) {
//                     if($('subcategory_id'))
//                       $('subcategory_id').disabled = 'disabled';
//                     if($('subsubcategory_id'))
//                       $('subsubcategory_id').disabled = 'disabled';
//                   }
//                   if($('subcategory_id')) {
//                     $('subcategory_id').value = subcatid;
//                   }
//                 }
// 
//                 if(category_id == 0) {
//                   clear('subcategory_id');
//                   if($('subcategory_id'))
//                     $('subcategory_id').style.display = 'none';
//                   if($('subcategory_id-label'))
//                     $('subcategory_id-label').style.display = 'none';
//                 }
//               }
//             }));
//           };
// 
//           var changesubcategory = function(subcatid, subsubcatid)
//           {
// 
//             $('subsubcategory_id-wrapper').style.display = 'none';
//             if(cateDependencyArray.indexOf(subcatid) == -1 || subsubcatid == 0)
//               return;
// 
//             if($('buttons-wrapper')) {
//               $('buttons-wrapper').style.display = 'none';
//             }
//             $('subsubcategory_backgroundimage').innerHTML = '<div class="form-label"></div><div  class="form-element"><img src="application/modules/Seaocore/externals/images/core/loading.gif" /></center></div>';
// 
//             $('subsubcategory_backgroundimage').style.display = 'block';
//             if(subsubcatid == '') {
//               if($('subsubcategory_id-wrapper'))
//                 $('subsubcategory_id-wrapper').style.display = 'none';
//             }
//             var url = '<?php echo $this->url(array('action' => 'subsub-category'), "sitestoreproduct_general", true); ?>';
//             var request = new Request.JSON({
//               url : url,
//               data : {
//                 format : 'json',
//                 subcategory_id_temp : subcatid
//               },
//               onSuccess : function(responseJSON) {
//                 $('subsubcategory_backgroundimage').style.display = 'none';
//                 if($('buttons-wrapper')) {
//                   $('buttons-wrapper').style.display = 'block';
//                 }
//                 clear('subsubcategory_id');
//                 var  subsubcatss = responseJSON.subsubcats;          
//                 addSubOption($('subsubcategory_id')," ", '0');
//                 for (i=0; i< subsubcatss.length; i++) {
// 
//                   addSubOption($('subsubcategory_id'), subsubcatss[i]['category_name'], subsubcatss[i]['category_id']);
//                   if($('subsubcategory_id')) {
//                     $('subsubcategory_id').value = subsubcatid;
//                   }
//                 }
//               }
//             });
//             request.send();
//           };

          var cat = '<?php echo $this->sitestoreproduct->category_id ?>';
          if(cat != '') {
            subsubcatid = '<?php echo $this->sitestoreproduct->subsubcategory_id; ?>';
            var subcatname = '<?php echo $this->subcategory_name; ?>';			 
            subcategories(cat, subcatid, subcatname,subsubcatid);
          }
        }
        var showMarkerInDate="<?php echo $this->showMarkerInDate ?>";

        var cal_discount_end_date_onShowStart = function(){      
          if( document.getElementById("discount_permanant") ) {
            if($('discount_end_date-date').value == ""){
              $("discount_permanant").checked = true;
            }else{
              $("discount_permanant").checked = false;
            }
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
            document.getElementById('handling_fee-wrapper').style.display = 'none';
            document.getElementById('discount_rate-wrapper').style.display = 'none';
            document.getElementById('discount_price-wrapper').style.display = 'none';
            document.getElementById('discount_start_date-wrapper').style.display = 'none';
            document.getElementById('discount_end_date-wrapper').style.display = 'none';
            document.getElementById('discount_permanant-wrapper').style.display = 'none';
            document.getElementById('user_type-wrapper').style.display = 'none';
          } else{
            document.getElementById('handling_fee-wrapper').style.display = 'block';
            document.getElementById('discount_start_date-wrapper').style.display = 'block';
            document.getElementById('discount_end_date-wrapper').style.display = 'block';
            document.getElementById('discount_permanant-wrapper').style.display = 'block';
            document.getElementById('user_type-wrapper').style.display = 'block';
            showDiscountType();
          }
        
        }
        function showDiscountType(){
          if($('handling_fee')){
            if($('handling_fee').value == 1) {
              document.getElementById('discount_price-wrapper').style.display = 'none';
              document.getElementById('discount_rate-wrapper').style.display = 'block';		
            } else{
              document.getElementById('discount_price-wrapper').style.display = 'block';
              document.getElementById('discount_rate-wrapper').style.display = 'none';
            }
          }
        }
</script>



<!-- Code from create file  -->

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
  });
  
  
    
  window.addEvent('domready', function() { 
    <?php if( !empty($this->allowProductCode) ) : ?>
    var e4 = $('product_code_msg-wrapper');
    if( document.getElementById('product_code_msg-wrapper') ) {
      document.getElementById('product_code_msg-wrapper').setStyle('display', 'none');
      var pageurlcontainer = $('product_code-element');
      var language = '<?php echo $this->string()->escapeJavascript($this->translate('Check Availability')) ?>';
      var newdiv = document.createElement('div');
      newdiv.id = 'product_code_varify';
      newdiv.innerHTML = "<a href='javascript:void(0);'  name='check_availability' id='check_availability' onclick='PageUrlBlur();return false;' class='check_availability_button'>"+language+"</a> <br />";

      pageurlcontainer.insertBefore(newdiv, pageurlcontainer.childNodes[2]);
      checkDraft();
    }
    <?php endif; ?>
        
    checkDraft();
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
    var url = '<?php echo $this->url(array('action' => 'product-code-validation'), 'sitestoreproduct_general', true); ?>';
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
        $("search").checked= false;
        
        if($("allow_purchase"))
          $("allow_purchase-wrapper").style.display="none";
      } else{
        $("search-wrapper").style.display="block";
        $("search").checked= true;
        
        if($("allow_purchase"))
          $("allow_purchase-wrapper").style.display="block";
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
    var showCurrentTime = false;
    var currentTime = "<?php echo date('m/d/Y', time()); ?>";

    <?php if(strtotime($this->sitestoreproduct->start_date) > time() ) : ?>
      showCurrentTime = true;
    <?php endif; ?>
    initializeCalendarDate(seao_dateFormat, cal_start_date, cal_end_date, 'start_date', 'end_date', showCurrentTime, currentTime);
    cal_start_date_onHideStart();
    
    showCurrentTime = false;
    <?php if(strtotime($this->otherInfoObj->discount_start_date) > time() ) : ?>
      showCurrentTime = true;
    <?php endif; ?>
    initializeCalendarDate(seao_dateFormat, cal_discount_start_date, cal_discount_end_date, 'discount_start_date', 'discount_end_date', showCurrentTime, currentTime);
    cal_discount_start_date_onHideStart();
  });
  
  var cal_start_date_onHideStart = function(){
    cal_starttimeDate_onHideStart(seao_dateFormat, cal_start_date, cal_end_date, 'start_date', 'end_date');
  };
  
  var cal_discount_start_date_onHideStart = function(){
    cal_starttimeDate_onHideStart(seao_dateFormat, cal_discount_start_date, cal_discount_end_date, 'discount_start_date', 'discount_end_date');
  };
// END CALENDAR WORK FOR PRODUCT START-END DATE AND DISCOUNT START-END DATE

  if($('subcategory_id'))
    $('subcategory_id').style.display = 'none';

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
  
  window.addEvent('domready',function() {
   
    var productType = $('product_type').value;
    if(productType == 'bundled'){
//      showPriceType();
      showWeightType();
    }
   
    if(productType != 'grouped'){
      showDiscount();
      showDiscountType();
      <?php if( !empty($this->showProductInventory) ) : ?>
      showOutOfStock();
      showStock();
      <?php endif; ?>
      <?php if( !empty($this->directPayment) && !empty($this->isDownPaymentEnable) ) : ?>
        showDownpayment();
      <?php endif; ?>
    }
    showEndDate();
      
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
    if($('handling_type') && document.getElementById("discount-1").checked){
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
    if(productType == 'bundled' || productType == 'grouped'){
      productidsAutocomplete = new SEAOAutocompleter.Request.JSON('product_name', '<?php echo $this->url(array('module' => 'sitestoreproduct', 'controller' => 'report', 'action' => 'suggestproducts', 'store_id' => $this->sitestore->store_id), 'default', true); ?>', {
        'postVar' : 'search',
        'postData' : {'store_ids': <?php echo $this->store_id ?>, 'product_ids': $('product_ids').value, 'create': 1, 'is_simple': is_simple, 'is_configurable': is_configurable, 'is_virtual': is_virtual, 'is_downloadable': is_downloadable},      
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
        onPush : function() {
          if ($('product_ids-wrapper')) {
            $('product_ids-wrapper').style.display='block';
          }
				
          if( $(this.options.elementValues).value.split(',').length >= maxRecipients ) {
            this.element.disabled = true;
          }
        
          productidsAutocomplete.setOptions({
            'postData' : {'store_ids': <?php echo $this->store_id ?>, 'product_ids': $('product_ids').value, 'create': 1, 'is_simple': is_simple, 'is_configurable': is_configurable, 'is_virtual': is_virtual, 'is_downloadable': is_downloadable}
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
    
    if( !is_simple && !is_configurable && !is_virtual && !is_downloadable && $("product_name") )
      $("product_name").disabled = true;
    else if( $("product_name") )
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
        // removeToValue(recipientsArray[i], toValueArray, elmentValue);
        for (var i = 0; i < toValueArray.length; i++){
          if (toValueArray[i]==recipientsArray[i]) toValueIndex =i;
        }
        toValueArray.splice(toValueIndex, 1);
        $(elmentValue).value = toValueArray.join();
      }
    } else {
      //      removeToValue(id, toValueArray, elmentValue);
      for (var i = 0; i < toValueArray.length; i++){
        if (toValueArray[i]==id) toValueIndex =i;
      }
      toValueArray.splice(toValueIndex, 1);
      $(elmentValue).value = toValueArray.join();
    }

    // hide the wrapper for element if it is empty
    if ($(elmentValue).value==""){
      $(elmentValue+'-wrapper').setStyle('height', '0');
      $(elmentValue+'-wrapper').setStyle('display', 'none');
    }
    $(element).disabled = false;
  }
 
  //  function removeToValue(id, toValueArray, elmentValue) {
  //    for (var i = 0; i < toValueArray.length; i++){
  //      if (toValueArray[i]==id) toValueIndex =i;
  //    }
  //    toValueArray.splice(toValueIndex, 1);
  //    $(elmentValue).value = toValueArray.join();
  //  }
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
      if (multiLanguage == 1 && languageCount > 1) {
        $('multiLanguageTitleLinkShow').inject(titleParent, 'after');
        $('multiLanguageTitleLinkHide').inject(titleParent, 'after');
        $('multiLanguageBodyLinkShow').inject(bodyParent, 'after');
        $('multiLanguageBodyLinkHide').inject(bodyParent, 'after');
        multiLanguageTitleOption(1);
        multiLanguageBodyOption(1);
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
  </script>