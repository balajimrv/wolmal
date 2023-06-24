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
<?php $this->headLink()	->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/styles/style_sitestoreproduct.css'); ?>
<div class="sitestore_quick_search">
  <?php echo $this->form->setAttrib('class', 'sitestoreproduct-search-box')->render($this) ?>
</div>	

<?php
  $this->headScript()
        ->appendFile($this->layout()->staticBaseUrl .  'externals/autocompleter/Observer.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');
?>

<script type="text/javascript">

  var doSearching =function(searchboxcategory_id){
    
    var categoryElementExist = <?php echo $this->categoryElementExist; ?>;
    var searchboxcategory_id = 0;
    if(categoryElementExist == 1) {
      searchboxcategory_id = $('ajaxcategory_id').value;
    }

    if(searchboxcategory_id != 0) {

      var categoriesArray = <?php echo Zend_Json_Encoder::encode(Engine_Api::_()->getDbTable('categories', 'sitestoreproduct')->getCategoriesDetails($this->categoriesLevel)); ?>;

      $('searchBox').getElementById('category_id').value = categoriesArray[searchboxcategory_id].category_id;
      $('searchBox').getElementById('subcategory_id').value = categoriesArray[searchboxcategory_id].subcategory_id;
      $('searchBox').getElementById('subsubcategory_id').value = categoriesArray[searchboxcategory_id].subsubcategory_id;
      $('searchBox').getElementById('categoryname').value = encodeURIComponent(categoriesArray[searchboxcategory_id].categoryname);
      $('searchBox').getElementById('subcategoryname').value = encodeURIComponent(categoriesArray[searchboxcategory_id].subcategoryname);
      $('searchBox').getElementById('subsubcategoryname').value = encodeURIComponent(categoriesArray[searchboxcategory_id].subsubcategoryname);      
    } else {
        $('searchBox').getElementById('category_id').value = '';
      $('searchBox').getElementById('subcategory_id').value = '';
      $('searchBox').getElementById('subsubcategory_id').value = '';
      $('searchBox').getElementById('categoryname').value = '';
      $('searchBox').getElementById('subcategoryname').value = '';
      $('searchBox').getElementById('subsubcategoryname').value = '';
    }
    
    $('searchBox').submit();
  }  
  
  en4.core.runonce.add(function()
  {
    var item_count = 0;
    var contentAutocomplete = new Autocompleter.Request.JSON('titleAjax', '<?php echo $this->url(array('action' => 'get-search-products'), "sitestoreproduct_general", true) ?>', {
      'postVar' : 'text',
      'minLength': 1,
      'selectMode': 'pick',
      'autocompleteType': 'tag',
      'className': 'tag-autosuggest seaocore-autosuggest',
      'customChoices' : true,
      'filterSubset' : true,
      'multiple' : false,
      'injectChoice': function(token) {
	      if(typeof token.label != 'undefined' ) {
          if (token.sitestoreproduct_url != 'seeMoreLink') {
            var choice = new Element('li', {'class': 'autocompleter-choices', 'html': token.photo, 'id':token.label, 'sitestoreproduct_url':token.sitestoreproduct_url, onclick:'javascript:getPageResults("'+token.sitestoreproduct_url+'")'});
            new Element('div', {'html': this.markQueryValue(token.label),'class': 'autocompleter-choice'}).inject(choice);
            this.addChoiceEvents(choice).inject(this.choices);
            choice.store('autocompleteChoice', token);
          }
          if(token.sitestoreproduct_url == 'seeMoreLink') {
            var titleAjax = $('titleAjax').value;
            var choice = new Element('li', {'class': 'autocompleter-choices1', 'html': '', 'id':'stopevent', 'sitestoreproduct_url':''});
            new Element('div', {'html': 'See More Results for '+titleAjax ,'class': 'autocompleter-choicess', onclick:'javascript:Seemore()'}).inject(choice);
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
            var url = selected.retrieve('autocompleteChoice').sitestoreproduct_url;
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
    var url = '<?php echo $this->url(array('action' => 'index'), "sitestoreproduct_general", true); ?>';
  	window.location.href= url + "?titleAjax=" + encodeURIComponent($('titleAjax').value);
  }

  function getPageResults(url) {
    if(url != 'null' ) {
      if (url == 'seeMoreLink') {
        Seemore();
      }
      else {
        window.location.href=url;
      }
    }
  }
</script>