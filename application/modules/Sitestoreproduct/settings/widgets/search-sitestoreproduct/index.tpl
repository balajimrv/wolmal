<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<?php
$apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
$this->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&key=$apiKey");
$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/core.js');
?>

<?php
	$baseUrl = $this->layout()->staticBaseUrl;
	$this->headLink()
        ->prependStylesheet($baseUrl . 'application/modules/Seaocore/externals/styles/styles.css');
?>

<script type="text/javascript">
  var pageAction =function(page){
    $('page').value = page;
    $('filter_form').submit();
  }

  var searchSitestoreproducts = function() {
		var formElements = $('filter_form').getElements('li');
		formElements.each( function(el) {
			var field_style = el.style.display;
			if(field_style == 'none') {
				el.destroy();
			}
		});

    if( Browser.Engine.trident ) {
      document.getElementById('filter_form').submit();
    } else {
      $('filter_form').submit();
   }
  }
  en4.core.runonce.add(function(){
    $$('#filter_form input[type=text]').each(function(f) {
      if (f.value == '' && f.id.match(/\min$/)) {
        new OverText(f, {'textOverride':'min','element':'span'});
        //f.set('class', 'integer_field_unselected');
      }
      if (f.value == '' && f.id.match(/\max$/)) {
        new OverText(f, {'textOverride':'max','element':'span'});
        //f.set('class', 'integer_field_unselected');
      }
    });
  });
  window.addEvent('onChangeFields', function() {
    var firstSep = $$('li.browse-separator-wrapper')[0];
    var lastSep;
    var nextEl = firstSep;
    var allHidden = true;
    do {
      nextEl = nextEl.getNext();
      if( nextEl.get('class') == 'browse-separator-wrapper' ) {
        lastSep = nextEl;
        nextEl = false;
      } else {
        allHidden = allHidden && ( nextEl.getStyle('display') == 'none' );
      }
    } while( nextEl );
    if( lastSep ) {
      lastSep.setStyle('display', (allHidden ? 'none' : ''));
    }
  });

  var viewType = '<?php echo $this->viewType; ?>';
  var whatWhereWithinmile = <?php echo $this->whatWhereWithinmile; ?>;

<?php if (isset($_GET['search']) || isset($_GET['location'])): ?>
    var advancedSearch = 0;
<?php else: ?>
    var advancedSearch = <?php echo $this->advancedSearch; ?>;
<?php endif; ?>

  if (viewType == 'horizontal' && whatWhereWithinmile == 1) {

    function advancedSearchLists(showFields, domeReady) {
        
       
        var fieldElements = new Array('sitestoreproduct_street', 'sitestoreproduct_city', 'sitestoreproduct_state', 'sitestoreproduct_country', 'orderby', 'show', 'category_id', 'discount', 'has_photo', 'in_stock', 'has_review', 'minmax_slider', 'done');
      
        var fieldsStatus = 'none';
      
      if (showFields == 1) {
        fieldsStatus = 'block';
      }
      
        if ($('integer-wrapper')) {
                if (domeReady == 1) {
                    $('integer-wrapper').style.display = fieldsStatus;
                }
                else {
                    $('integer-wrapper').toggle();
                }
            }
        for (i = 0; i < fieldElements.length; i++) {
                if ($(fieldElements[i] + '-label')) {
                    if (domeReady == 1) {
                        $(fieldElements[i] + '-label').getParent().style.display = fieldsStatus;
                    }
                    else {                        
                        
                        $(fieldElements[i] + '-label').getParent().toggle();
                        showFields = $(fieldElements[i] + '-label').getParent().style.display == 'block';
                    }
                }
                
                if((fieldElements[i] == 'subcategory_id') &&  ($('subcategory_id-wrapper')) && domeReady != 1 && $('category_id').value != 0) {
                    $(fieldElements[i] + '-wrapper').toggle();
                }

                if((fieldElements[i] == 'subsubcategory_id') &&  ($('subsubcategory_id-wrapper')) && domeReady != 1 && $('subcategory_id').value != 0) {
                    $(fieldElements[i] + '-wrapper').toggle();
                }
                
            }
            
            if (showFields == 1) {
                $("filter_form").getElements(".field_toggle").each(function(multiEl){
                    if(multiEl.getParent('li')) {
                         multiEl.getParent('li').removeClass('dnone');
                    }
                 });
            }else{
                $("filter_form").getElements(".field_toggle").each(function(multiEl){
                    if(multiEl.getParent('li')) {
                        multiEl.getParent('li').removeClass('dnone').addClass('dnone');
                    }
                 });
            } 
            
            var showSlider = '<?php echo $this->priceFieldType; ?>';
            if (showSlider == 'slider') {
                <?php
                $currency_symbol = Engine_Api::_()->sitestoreproduct()->getCurrencySymbol();
                $minPrice = ($this->minPrice) ? $this->minPrice : 0;
                $maxPrice = ($this->maxPrice) ? $this->maxPrice : 999;
                $searchMinPrice = Zend_Controller_Front::getInstance()->getRequest()->getParam('minPrice');
                $searchMaxPrice = Zend_Controller_Front::getInstance()->getRequest()->getParam('maxPrice');
                ?>
                var mySlideA = new Slider($('slider_minmax_gutter_m'), $('slider_minmax_minKnobA'), $('slider_bkg_img'), {
                    start: <?php echo $minPrice ?>,
                    end: <?php echo $maxPrice ?>,
                    offset: 8,
                    snap: false,
                    onChange: function(pos) {

                        $('minPrice').value = pos.minpos;
                        $('maxPrice').value = pos.maxpos;
                        $('slider_minmax_min').innerHTML = '<?php echo $currency_symbol; ?> ' + pos.minpos;
                        $('slider_minmax_max').innerHTML = '<?php echo $currency_symbol; ?> ' + pos.maxpos;
                    }
                },
                $('slider_minmax_maxKnobA')).setMin(<?php echo ($searchMinPrice) ? $searchMinPrice : $minPrice ?>).setMax(<?php echo ($searchMaxPrice) ? $searchMaxPrice : $maxPrice ?>);
            }

    }
      if (viewType == 'horizontal' && whatWhereWithinmile == 1) {
      en4.core.runonce.add(function() {
        advancedSearchLists(advancedSearch, 1);
      });
    } else {
      advancedSearchLists(advancedSearch, 1);
    }
  }
</script>

<?php
	//if(empty($this->sitestoreproduct_post)){return;}
  /* Include the common user-end field switching javascript */
  echo $this->partial('_jsSwitch.tpl', 'fields', array(
    //'topLevelId' => (int) @$this->topLevelId,
    //'topLevelValue' => (int) @$this->topLevelValue
  ))
?>

<?php if($this->viewType == 'horizontal'): ?>
  <div class="seaocore_searchform_criteria seaocore_searchform_criteria_horizontal <?php
  if ($this->whatWhereWithinmile) {
    echo "seaocore_searchform_criteria_advanced";
  }
  ?>">
    <?php  if($this->sitestoreproduct_post == 'enabled') { echo $this->form->render($this); }else { return; } ?>
  </div>
<?php else: ?>
  <div class="seaocore_searchform_criteria">
    <?php  if($this->sitestoreproduct_post == 'enabled') { echo $this->form->render($this); }else { return; } ?>
  </div>
<?php endif; ?>

<script type="text/javascript">
  en4.core.runonce.add(function(){
		$('global_content').getElement('.browsesitestoreproducts_criteria').addEvent('keypress', function(e){   
			if( e.key != 'enter' ) return;
				searchSitestoreproducts();
		});
  });
 
</script>

<script type="text/javascript">
  
  var profile_type = 0;
  var previous_mapped_level = 0;  
  var sitestoreproduct_categories_slug = <?php echo json_encode($this->categories_slug); ?>;
  function showFields(cat_value, cat_level, parent_category_value) {
       
    if(typeof parent_category_value != 'undefined') {
        var is_parent_mapped = getProfileType(parent_category_value);
        if(is_parent_mapped) return;
    }  
       
    if(cat_level == 1 || (previous_mapped_level >= cat_level && previous_mapped_level != 1) || (profile_type == null || profile_type == '' || profile_type == 0)) {
      profile_type = getProfileType(cat_value); 
      if(profile_type == 0) { profile_type = ''; } else { previous_mapped_level = cat_level; }
      $('profile_type').value = profile_type;
      changeFields($('profile_type'));      
    }
  }

	var getProfileType = function(category_id) {
		var mapping = <?php echo Zend_Json_Encoder::encode(Engine_Api::_()->getDbTable('categories', 'sitestoreproduct')->getMapping('profile_type')); ?>;
		for(i = 0; i < mapping.length; i++) {
			if(mapping[i].category_id == category_id)
				return mapping[i].profile_type;
		}
		return 0;
	}

 function addOptions(element_value, element_type, element_updated, domready) {

    var element = $(element_updated);
    if(domready == 0){
      switch(element_type){    
        case 'cat_dependency':
          $('subcategory_id'+'-wrapper').style.display = 'none';
          clear($('subcategory_id'));
          $('subcategory_id').value = 0;
          $('categoryname').value = sitestoreproduct_categories_slug[element_value];
  
        case 'subcat_dependency':
          $('subsubcategory_id'+'-wrapper').style.display = 'none';
          clear($('subsubcategory_id'));
          $('subsubcategory_id').value = 0;
          $('subsubcategoryname').value = '';
          if(element_type=='subcat_dependency')
            $('subcategoryname').value = sitestoreproduct_categories_slug[element_value];
          else
            $('subcategoryname').value='';
      }
    }
    
    if(element_value <= 0) return;  
    <?php if( empty($this->subcategoryFiltering) ) : ?>
      return;
   <?php endif; ?>
    var url = '<?php echo $this->url(array('module' => 'sitestoreproduct', 'controller' => 'review', 'action' => 'categories'), "default", true); ?>';
    en4.core.request.send(new Request.JSON({      	
      url : url,
      data : {
        format : 'json',
        element_value : element_value,
        element_type : element_type
      },

      onSuccess : function(responseJSON) {
        var categories = responseJSON.categories;
        var option = document.createElement("OPTION");
        option.text = "";
        option.value = 0;
        element.options.add(option);
        for (i = 0; i < categories.length; i++) {
          var option = document.createElement("OPTION");
          option.text = categories[i]['category_name'];
          option.value = categories[i]['category_id'];
          element.options.add(option);
          sitestoreproduct_categories_slug[categories[i]['category_id']]=categories[i]['category_slug'];
        }

        if(categories.length  > 0 )
          $(element_updated+'-wrapper').style.display = 'inline-block';
        else
          $(element_updated+'-wrapper').style.display = 'none';
        
        if(domready == 1){
          var value=0;
          if(element_updated=='category_id'){
            value = search_category_id;
          }else if(element_updated=='subcategory_id'){
            value = search_subcategory_id;
          }else{
            value =search_subsubcategory_id;
          }
          $(element_updated).value = value;
        }
      }

    }),{'force':true});
  }

  function clear(element)
  { 
    for (var i = (element.options.length-1); i >= 0; i--)	{
      element.options[ i ] = null;
    }
  }
  
  <?php if(!empty($this->categoryInSearchForm) && !empty($this->categoryInSearchForm->display)): ?>
    var search_category_id,search_subcategory_id,search_subsubcategory_id;
    en4.core.runonce.add(function(){

      search_category_id='<?php echo $this->category_id ?>';

      if(search_category_id !=0) {

        addOptions(search_category_id,'cat_dependency', 'subcategory_id',1);

        search_subcategory_id='<?php echo $this->subcategory_id ?>';      

        if(search_subcategory_id !=0) {
          search_subsubcategory_id='<?php echo $this->subsubcategory_id ?>';
          addOptions(search_subcategory_id,'subcat_dependency', 'subsubcategory_id',1);
        }
      }   
    });
  <?php endif; ?>
  
  function show_subcat(cat_id) 
  {		
    if(document.getElementById('subcat_' + cat_id)) {
      if(document.getElementById('subcat_' + cat_id).style.display == 'block') {		
        document.getElementById('subcat_' + cat_id).style.display = 'none';
        document.getElementById('img_' + cat_id).src = '<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitestoreproduct/externals/images/bullet-right.png';
      } 
      else if(document.getElementById('subcat_' + cat_id).style.display == '') {			
        document.getElementById('subcat_' + cat_id).style.display = 'none';
        document.getElementById('img_' + cat_id).src = '<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitestoreproduct/externals/images/bullet-right.png';
      }
      else {			
        document.getElementById('subcat_' + cat_id).style.display = 'block';
        document.getElementById('img_' + cat_id).src = '<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitestoreproduct/externals/images/bullet-bottom.png';
      }		
    }
  }
  function showRadiusTip() {
    
      var url = '<?php echo $this->url(array('action' => 'show-radius-tip'), "sitestoreproduct_general", true); ?>';
      Smoothbox.open(url);
    }
    
    en4.core.runonce.add(function() {
			if ($('location')) {

					var params = {
							'detactLocation': <?php echo $this->locationDetection; ?>,
							'fieldName': 'location',
							'noSendReq': 1,
							'locationmilesFieldName': 'locationmiles',
							'locationmiles': <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationdefaultmiles', 1000); ?>,
							'reloadPage': 1,
					};
					en4.seaocore.locationBased.startReq(params);
			}
      
			locationAutoSuggest('<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.countrycities'); ?>', 'location', 'sitestoreproduct_city');
    });    
  
</script>