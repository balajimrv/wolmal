<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
//GET API KEY
$apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
$this->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&key=$apiKey");
?>
<?php
include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/common_style_css.tpl';
?>
<?php
//if(empty($this->sitestore_post)){return;}
/* Include the common user-end field switching javascript */
echo $this->partial('_jsSwitch.tpl', 'fields', array(
        //'topLevelId' => (int) @$this->topLevelId,
        //'topLevelValue' => (int) @$this->topLevelValue
))
?>
<?php if ($this->form): ?>
  <?php echo $this->form->setAttrib('class', 'global_form_box sitestore_advanced_search_form horizontal_search_' . $this->identity)->render($this) ?>
  <div class="" id="store_location_pops_loding_image" style="display: none;">
    <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/core/loading.gif' />
    <?php //echo $this->translate("Loading ...")  ?>
  </div>
<?php endif ?>

<script type="text/javascript">
  var flag = '<?php echo $this->advanced_search; ?>';
  var mapGetDirection;
  var myLatlng;
  window.addEvent('domready', function() {
	
    //	  if(document.getElementById('sitestore_location').value == '') {
    //			submiForm();
    //		}
		
    if ($$('.browse-separator-wrapper')) {
      $$('.browse-separator-wrapper').setStyle("display",'none');
    }
	
    if(document.getElementById('sitestore_location'))
      new google.maps.places.Autocomplete(document.getElementById('sitestore_location'));
    advancedSearchSitestores(flag);
    $$('.<?php echo 'horizontal_search_' . $this->identity ?>').addEvent('submit',function(event){
		 if($('sitestore_location') && !$('sitestore_location').get('placeholder')  && $('sitestore_location').value == "<?php echo $this->string()->escapeJavascript($this->translate('Enter a location'))?>"){
          $('sitestore_location').value ='';
      }
      var formElements = $(event.target).getElements('li');
      formElements.each( function(el) {
        var field_style = el.style.display;
        if(field_style == 'none') {
          el.destroy();
        }
      });
    });
		
  });

  function locationStore() {
    var  sitestore_location = document.getElementById('sitestore_location');
		
    // 		if (document.getElementById('sitestore_location').value) {
    // 			document.getElementById('sitestore_location') = 0; 
    // 	  }
	  
    if (document.getElementById('Latitude').value) {
      document.getElementById('Latitude').value = 0;
    }
		
    if(document.getElementById('Longitude').value) {
      document.getElementById('Longitude').value = 0;
    }
  }
  

  function advancedSearchSitestores() {
	
    if (flag == 0) {
      if ($('fieldset-grp2'))
        $('fieldset-grp2').style.display = 'none';
				
      if ($('fieldset-grp1'))
        $('fieldset-grp1').style.display = 'none';
				
      flag = 1;
      $('advanced_search').value = 0;
      if ($('sitestore_street'))
        $('sitestore_street').value = '';
      if ($('sitestore_country'))
        $('sitestore_country').value = '';
      if ($('sitestore_state'))
        $('sitestore_state').value = '';
      if ($('sitestore_city'))
        $('sitestore_city').value = '';
      if ($('profile_type'))
        $('profile_type').value = '';
      changeFields($('profile_type'));
      if ($('orderby'))
        $('orderby').value = '';
      if ($('category_id'))
        $('category_id').value = 0;

    } else {
      if ($('fieldset-grp2'))
        $('fieldset-grp2').style.display = 'block';
				
      if ($('fieldset-grp1'))
        $('fieldset-grp1').style.display = 'block';
				
      flag = 0;
      $('advanced_search').value = 1;
    }
  }

  var form;

  var location_subcategoryies = function(category_id, sub, subcatname, subsubcat) {

    if($('filter_form')) {
      form=document.getElementById('filter_form');
    } else if($('filter_form_category')){
      form=$('filter_form_category');
    }

    if($('category_id') && form.elements['category_id']){
      form.elements['category_id'].value = '<?php echo $this->category_id; ?>';
    }

    if($('subcategory_id') && form.elements['subcategory_id']){
      form.elements['subcategory_id'].value = '<?php echo $this->subcategory_id; ?>';
    }

    if($('subsubcategory_id') && form.elements['subsubcategory_id']){
      form.elements['subsubcategory_id'].value = '<?php echo $this->subsubcategory_id; ?>';
    }
    
    if(category_id != '' && form.elements['category_id']){
      form.elements['category_id'].value = category_id;
    }

    if(category_id != 0) {
      if(sub == '') {
        sub=0;
        subsubcat = 0;
      }
      changesubcategory(sub, subsubcat, subcatname);
    }

    var url = '<?php echo $this->url(array('action' => 'subcategory'), 'sitestore_general', true); ?>';
    en4.core.request.send(new Request.JSON({      	
      url : url,
      data : {
        format : 'json',
        category_id_temp : category_id
      },
      onSuccess : function(responseJSON) {
        clear('subcategory_id');
        var  subcatss = responseJSON.subcats;        
        addOption($('subcategory_id')," ", '0');
        for (i=0; i< subcatss.length; i++) {
          addOption($('subcategory_id'), subcatss[i]['category_name'], subcatss[i]['category_id']);  
          form.elements['categoryname'].value = subcatss[i]['categoryname_temp'];
          form.elements['category'].value = category_id;
          form.elements['subcategory_id'].value = sub;
        }

        if(subcatss.length == 0) {
          form.elements['categoryname'].value = 0;
        }

        if(category_id == 0) {
          clear('subcategory_id');
          clear('subsubcategory_id');
          $('subcategory_id').style.display = 'none';
          $('subcategory_id-label').style.display = 'none';
          $('subsubcategory_id').style.display = 'none';
          $('subsubcategory_id-label').style.display = 'none';
        }
      }
    }));
  };

  function clear(ddName) {
    for (var i = (document.getElementById(ddName).options.length-1); i >= 0; i--) {
      document.getElementById(ddName).options[ i ]=null; 	      
    }
  }

  function addOption(selectbox,text,value ) {
    var optn = document.createElement("OPTION");
    optn.text = text;
    optn.value = value;

    if(optn.text != '' && optn.value != '') {
      $('subcategory_id').style.display = 'inline-block';
      $('subcategory_id-label').style.display = 'inline-block';
      selectbox.options.add(optn);
    }
    else {
      $('subcategory_id').style.display = 'none';
      $('subcategory_id-label').style.display = 'none';
      selectbox.options.add(optn);
    }
  }
  
  var changesubcategory = function(subcatid, subsubcat, subcatname) {
	
    var url = '<?php echo $this->url(array('action' => 'subsubcategory'), 'sitestore_general', true); ?>';
    var request = new Request.JSON({
      url : url,
      data : {
        format : 'json',
        subcategory_id_temp : subcatid
      },
      onSuccess : function(responseJSON) {
        clear('subsubcategory_id');
        var  subsubcatss = responseJSON.subsubcats;
        addSubOption($('subsubcategory_id')," ", '0');
        for (i=0; i< subsubcatss.length; i++) {
          addSubOption($('subsubcategory_id'), subsubcatss[i]['category_name'], subsubcatss[i]['category_id']);
          if(form.elements['subsubcategory_id'])
            form.elements['subsubcategory_id'].value = subsubcat;
          if(form.elements['subsubcategory'])
            form.elements['subsubcategory'].value = subsubcat;
          if($('subsubcategory_id')) {
            $('subsubcategory_id').value = subsubcat;
          }
        }
        form.elements['subcategory'].value = subcatid;
        form.elements['subcategoryname'].value = subcatname;

        if(subcatid == 0) {
          clear('subsubcategory_id');
          if($('subsubcategory_id-label'))
            $('subsubcategory_id-label').style.display = 'none';
        }
      }
    });
    request.send();
  };

  function addSubOption(selectbox,text,value ) {
    var optn = document.createElement("OPTION");
    optn.text = text;
    optn.value = value;
		
    if(optn.text != '' && optn.value != '') {
      $('subsubcategory_id').style.display = 'block';
      if($('subsubcategory_id-wrapper'))
        $('subsubcategory_id-wrapper').style.display = 'inline-block';
      if($('subsubcategory_id-label'))
        $('subsubcategory_id-label').style.display = 'inline-block';
      selectbox.options.add(optn);
    } 
    else {
      $('subsubcategory_id').style.display = 'none';
      if($('subsubcategory_id-wrapper'))
        $('subsubcategory_id-wrapper').style.display = 'none';
      if($('subsubcategory_id-label'))
        $('subsubcategory_id-label').style.display = 'none';
      selectbox.options.add(optn);
    }
  }

  var cat = '<?php echo $this->category_id ?>';

  if(cat != '') {
    var sub = '<?php echo $this->subcategory_id; ?>';
    var subcatname = '<?php echo $this->subcategory_name; ?>';
    var subsubcat = '<?php echo $this->subsubcategory_id; ?>';

    location_subcategoryies(cat, sub, subcatname,subsubcat);
  }
</script>

<?php
//endif;?>
