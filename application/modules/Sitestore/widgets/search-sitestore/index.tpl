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
$this->headScript()->appendFile("http://maps.googleapis.com/maps/api/js?libraries=places&key=$apiKey");
?>
<script type="text/javascript">
  if(document.getElementById('sitestore_location')) {
    en4.core.runonce.add(function(){
      new google.maps.places.Autocomplete(document.getElementById('sitestore_location'));
    });
  }
</script>

<?php
	$baseUrl = $this->layout()->staticBaseUrl;
	$this->headLink()
        ->prependStylesheet($baseUrl . 'application/modules/Seaocore/externals/styles/styles.css');
?>
<script type="text/javascript">
  var storeAction=function(store){
    $('store').value = store;
    $('filter_form').submit();
  };
  var searchSitestores = function() {

		var formElements = $('filter_form').getElements('li');
		formElements.each( function(el) {
			var field_style = el.style.display;
			if(field_style == 'none') {
				el.destroy();
			}
		});

    if($("tag"))
      $("tag").value='';
    if( Browser.Engine.trident ) {
      document.getElementById('filter_form').submit();
    } else {  
      $('filter_form').submit();
    }
  };
//  en4.core.runonce.add(function(){
//    $$('#filter_form input[type=text]').each(function(f) {
//      if (f.value == '' && f.id.match(/\min$/)) {
//        new OverText(f, {'textOverride':'min','element':'span'});
//        //f.set('class', 'integer_field_unselected');
//      }
//      if (f.value == '' && f.id.match(/\max$/)) {
//        new OverText(f, {'textOverride':'max','element':'span'});
//        //f.set('class', 'integer_field_unselected');
//      }
//    });
//  });
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
</script>
<?php
//if(empty($this->sitestore_post)){return;}
/* Include the common user-end field switching javascript */
echo $this->partial('_jsSwitch.tpl', 'fields', array(
        //'topLevelId' => (int) @$this->topLevelId,
        //'topLevelValue' => (int) @$this->topLevelValue
))
?>

<div class="seaocore_searchform_criteria <?php if($this->viewType == 'horizontal'): ?>seaocore_searchform_criteria_horizontal<?php endif; ?>">
	<?php if ($this->sitestore_post == 'enabled') {
		echo $this->form->render($this);
	} else {
		return;
	} ?>
</div>

<?php $row = Engine_Api::_()->getDbTable('searchformsetting', 'seaocore')->getFieldsOptions('sitestore', 'category_id');
if(!empty($row->display)):
?>
<script type="text/javascript">
  var form;

  var subcategoryies = function(category_id, sub, subcatname, subsubcat)
  { 
	  if($('filter_form')) {
	    form=document.getElementById('filter_form');
	  } else if($('filter_form_category')){
			form=$('filter_form_category');
		}

    if($('category_id') && form.elements['category_id']){
      form.elements['category_id'].value = '<?php echo $this->category_id;?>';
    }
    if($('subcategory_id') && form.elements['subcategory_id']){
      form.elements['subcategory_id'].value = '<?php echo $this->subcategory_id;?>';
    }
    if($('subsubcategory_id') && form.elements['subsubcategory_id']){
      form.elements['subsubcategory_id'].value = '<?php echo $this->subsubcategory_id;?>';
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
    
  	var url = '<?php echo $this->url(array('action' => 'subcategory'), 'sitestore_general', true);?>';
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
          //$('subcategory_id').value = sub;
          //form.elements['subcategory'].value = sub;
        	form.elements['categoryname'].value = subcatss[i]['categoryname_temp'];
          form.elements['category'].value = category_id;
          form.elements['subcategory_id'].value = sub;
          //form.elements['subcategory'].value = sub;
//           if(form.elements['subsubcategory'])
//           form.elements['subsubcategory'].value = subsubcat;
//           if(form.elements['subsubcategory_id'])
//           form.elements['subsubcategory_id'].value = subsubcat;
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
  function clear(ddName)
  { 
    for (var i = (document.getElementById(ddName).options.length-1); i >= 0; i--) 
    { 
      document.getElementById(ddName).options[ i ]=null; 	      
    } 
  }
  function addOption(selectbox,text,value )
  {
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
  
    var changesubcategory = function(subcatid, subsubcat, subcatname)
		{
			var url = '<?php echo $this->url(array('action' => 'subsubcategory'), 'sitestore_general', true);?>';
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

    function addSubOption(selectbox,text,value )
    {
      var optn = document.createElement("OPTION");
      optn.text = text;
      optn.value = value;
      if(optn.text != '' && optn.value != '') {
        $('subsubcategory_id').style.display = 'inline-block';
         if($('subsubcategory_id-wrapper'))
          $('subsubcategory_id-wrapper').style.display = 'inline-block';
         if($('subsubcategory_id-label'))
          $('subsubcategory_id-label').style.display = 'inline-block';
        selectbox.options.add(optn);
      } else {
        $('subsubcategory_id').style.display = 'none';
         if($('subsubcategory_id-wrapper'))
          $('subsubcategory_id-wrapper').style.display = 'none';
         if($('subsubcategory_id-label'))
          $('subsubcategory_id-label').style.display = 'none';
        selectbox.options.add(optn);
      }
    }

  var cat = '<?php echo $this->category_id ?>';		

  if(cat != '' && cat != 0) {
    var sub = '<?php echo $this->subcategory_id; ?>';
    var subcatname = '<?php echo $this->subcategory_name; ?>';
    var subsubcat = '<?php echo $this->subsubcategory_id; ?>';

    subcategoryies(cat, sub, subcatname,subsubcat);
  }
  
  function show_subcat(cat_id) 
  {		
    if(document.getElementById('subcat_' + cat_id)) {
      if(document.getElementById('subcat_' + cat_id).style.display == 'block') {		
        document.getElementById('subcat_' + cat_id).style.display = 'none';
        document.getElementById('img_' + cat_id).src = '<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitestore/externals/images/icons/plus16.gif';
      } 
      else if(document.getElementById('subcat_' + cat_id).style.display == '') {			
        document.getElementById('subcat_' + cat_id).style.display = 'none';
        document.getElementById('img_' + cat_id).src = '<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitestore/externals/images/icons/plus16.gif';
      }
      else {			
        document.getElementById('subcat_' + cat_id).style.display = 'block';
        document.getElementById('img_' + cat_id).src = '<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitestore/externals/images/icons/minus16.gif';
      }		
    }
  }
  
</script>

<?php endif;?>

<script>

 en4.core.runonce.add(function()
  {
    var item_count = 0;
    var contentAutocomplete = new Autocompleter.Request.JSON('search', '<?php echo $this->url(array('action' => 'get-search-stores','category_id' => $this->category_id), 'sitestore_general', true) ?>', {
      'postVar' : 'text',
      'minLength': 1,
      'selectMode': 'pick',
      'autocompleteType': 'tag',
      'className': 'searchbox_autosuggest',
      'customChoices' : true,
      'filterSubset' : true,
      'multiple' : false,
      'injectChoice': function(token) {      				
	      if(typeof token.label != 'undefined' ) {
          if (token.store_url != 'seeMoreLink') {
            var choice = new Element('li', {'class': 'autocompleter-choices1', 'html': token.photo, 'id':token.label, 'store_url':token.store_url, onclick:'javascript:getStoreResults("'+token.store_url+'")'});
            new Element('div', {'html': this.markQueryValue(token.label),'class': 'autocompleter-choice'}).inject(choice);
            this.addChoiceEvents(choice).inject(this.choices);
            choice.store('autocompleteChoice', token);
          }
          if(token.store_url == 'seeMoreLink') {
            var title = $('title').value;
            var choice = new Element('li', {'class': 'autocompleter-choices1', 'html': '', 'id':'stopevent', 'store_url':''});
            new Element('div', {'html': 'See More Results for '+title ,'class': 'autocompleter-choicess', onclick:'javascript:Seemore()'}).inject(choice);
            this.addChoiceEvents(choice).inject(this.choices);
            choice.store('autocompleteChoice', token);
          }
         }
       }
    });

    contentAutocomplete.addEvent('onSelection', function(element, selected, value, input) {
      window.addEvent('keyup', function(e) {
        if(e.key == 'enter') {
          if(selected.retrieve('autocompleteChoice') != 'null' ) {
            var url = selected.retrieve('autocompleteChoice').store_url;
            if (url == 'seeMoreLink') {
              Seemore();
            }
            else {
              window.location.href=url;
            }
          }
        }
      });      
    });
  });
  
  function Seemore() {
    $('stopevent').removeEvents('click');
    var url = '<?php echo $this->url(array('action' => 'index'), 'sitestore_general', true); ?>';
  	window.location.href= url + "?search=" + encodeURIComponent($('title').value);
  }

  function getStoreResults(url) {
    if(url != 'null' ) {
      if (url == 'seeMoreLink') {
        Seemore();
      }
      else {
        window.location.href=url;
      }
    }
  }
    
  en4.core.runonce.add(function() {
    if ($('sitestore_location')) {
      var params = {
        'detactLocation': <?php echo $this->locationDetection; ?>,
        'fieldName': 'sitestore_location',
        'noSendReq': 1,
        'locationmilesFieldName': 'locationmiles',
        'locationmiles': <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationdefaultmiles', 1000); ?>,
        'reloadPage': 1,
      };
      en4.seaocore.locationBased.startReq(params);
    }
  });

  locationAutoSuggest('<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.countrycities'); ?>', 'sitestore_location', 'sitestore_city');
</script>
