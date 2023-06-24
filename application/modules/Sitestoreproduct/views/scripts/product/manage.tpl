<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manage.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$itemCount = $this->paginator->getCurrentItemCount();
$availableTagsObj = $this->printingTagsObj;
$countTags = @count($availableTagsObj);
$tempPageIdsArray = array();
?>

<script type="text/javascript" >
  var currentPageProductIds = '';
  function owner(thisobj) {
    var Obj_Url = thisobj.href;
    Smoothbox.open(Obj_Url);
  }
</script>

<?php if (empty($this->responseFlag)) : ?>
  <script type="text/javascript">
    var tempStr = '';
    var tempCurrentViewType = <?php echo $this->temViewType ?>;
  </script>
<?php endif; ?>


<script type="text/javascript">
  function paginationNext() {
    $('tablerate_spinner_next').innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitestoreproduct/externals/images/loading.gif" />';
    en4.core.request.send(new Request.HTML({
      url: en4.core.baseUrl + 'sitestoreproduct/product/manage',
      data: {
        format: 'html',
        subject: en4.core.subject.guid,
        temp_is_subject: <?php echo $this->is_subject ?>,
        page: <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>,
        store_id:<?php echo $this->store_id ?>,
        responseFlag: 1
      },
      onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
        $('tablerate_spinner_next').innerHTML = '';
        document.getElementById("selectallchecks").checked = false;
        $('id_store_manage_products').innerHTML = responseHTML;
        selectAll();
      }
    }));
  }

  function paginationPrevious() {
    $('tablerate_spinner_prev').innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitestoreproduct/externals/images/loading.gif" />';
    en4.core.request.send(new Request.HTML({
      url: en4.core.baseUrl + 'sitestoreproduct/product/manage',
      data: {
        format: 'html',
        subject: en4.core.subject.guid,
        temp_is_subject: <?php echo $this->is_subject ?>,
        page: <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() - 1) ?>,
        store_id:<?php echo $this->store_id ?>,
        responseFlag: 1
      },
      onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
        $('tablerate_spinner_prev').innerHTML = '';
        document.getElementById("selectallchecks").checked = false;
        $('id_store_manage_products').innerHTML = responseHTML;
        selectAll();
      }
    }));
  }

  en4.core.runonce.add(function()
  {
<?php if (($this->current_count > 0)): ?>
      switchview(tempCurrentViewType);
<?php endif; ?>

    var anchor = $('sitestoreproduct_search').getParent();
    document.getElementById('store_table_rate_previous').style.display = '<?php echo ( $this->paginator->getCurrentPageNumber() == 1 ? 'none' : '' ) ?>';
    document.getElementById('store_table_rate_next').style.display = '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' ) ?>';

  });

  function selectAll()
  {
    var subcatss = currentPageProductIds.split(",");
    for (var i = 0; i < subcatss.length; ++i) {
      var subcatsss = "selected_product_" + subcatss[i];
      if ($(subcatsss)) {
//      var unsubcatsss = "unselected_product_" + subcatss[i];
//        document.getElementById(unsubcatsss).disabled = true;
//        document.getElementById(unsubcatsss).checked = false;
        if (document.getElementById("selectAllProducts").checked) {
          document.getElementById(subcatsss).disabled = true;
          document.getElementById(subcatsss).checked = true;
          document.getElementById("selectAllProducts").disabled = false;
          document.getElementById("selectallchecks").checked = false;
          document.getElementById("selectallchecks").disabled = true;
        } else {
          if (document.getElementById("selectallchecks").checked) {
            document.getElementById(subcatsss).checked = true;
            
            if((tempStr.search("<" + subcatss[i] + ">")) <= 0)
              tempStr = tempStr + "," + "<" + subcatss[i] + ">";
            
            if (!document.getElementById('selected_product_' + subcatss[i]).checked)
              tempStr = tempStr.replace("<" + subcatss[i] + ">", "<0>");
            
            document.getElementById("selectAllProducts").checked = false;
          }
          else {
            tempStr = '';
            document.getElementById("selectallchecks").disabled = false;
            document.getElementById(subcatsss).disabled = false;
            document.getElementById(subcatsss).checked = false;
<?php if (empty($this->responseFlag)) : ?>
              tempStr = '';
<?php endif; ?>
          }
        }
      }
    }
  }
  function removeChecks()
  {
    var subcatss = currentPageProductIds.split(",");
    for (var i = 0; i < subcatss.length; ++i) {
      var subcatsss = "selected_product_" + subcatss[i];
      document.getElementById("selectallchecks").disabled = false;
      document.getElementById(subcatsss).disabled = false;
      document.getElementById(subcatsss).checked = false;

    }
    document.getElementById("selectAllProducts").checked = false;

//    
//    
//   var i;
//    var multidelete_form_table_printing_tag = $('multiselect_form');
//    var inputs = multidelete_form_table_printing_tag.elements;
//    for (i = 0; i < inputs.length; i++) {
//        inputs[i].disabled = false;
//        inputs[i].checked = false;
//    } 
  }
  function checkAllProducts() {
    selectAll();
  }
  function makeProductStr(productId) {
    if (document.getElementById('selected_product_' + productId).checked) {
      tempStr = tempStr.replace("<" + productId + ">", "<0>");
      tempStr = tempStr + "," + "<" + productId + ">";
    } else {
      tempStr = tempStr.replace("<" + productId + ">", "<0>");
    }
  }

  function submitFilter(tempVar) {
    if($('main_printingtag_id'))
      $('main_printingtag_id').style.display = 'none';
    
    if(tempVar != 'exist')
      $('product_bulk_edit').style.display = 'none';
    
    $('id_store_manage_products').innerHTML = '<center><img src="application/modules/Sitestore/externals/images/spinner_temp.gif" /></center>';
    var downpayment = 0;
<?php if (!empty($this->directPayment) && !empty($this->isDownPaymentEnable)) : ?>
      downpayment = $('downpayment').value;
<?php endif; ?>
    en4.core.request.send(new Request.HTML({
      url: en4.core.baseUrl + 'sitestoreproduct/product/manage',
      data: {
        format: 'html',
        subject: en4.core.subject.guid,
        search: $('title').value,
        minPrice: $('min_price').value,
        maxPrice: $('max_price').value,
        category_id: $('category_id').value,
        section: $('section_id').value,
        product_type: $('product_type').value,
        temp_in_stock: $('stock').value,
        downpayment: downpayment,
//          temp_featured : $('featured').value,
//          temp_sponsored : $('sponsored').value,
//          temp_newlabel : $('new').value,
//          temp_status : $('temp_status').value,
        store_id:<?php echo $this->store_id ?>,
        responseFlag: 1
      },
      onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
        tempStr = '';
        if (tempVar == "exist")
          $('product_bulk').innerHTML = '<ul class="form-notices"><li>Products edited successfully.</li></ul>';
        $('selectallchecks').checked = false;
        $("selectallchecks").disabled = false;
        $('selectAllProducts').checked = false;
        $('id_store_manage_products').innerHTML = responseHTML;
        if(responseHTML.search('<form') >= 0) {
          if($('main_printingtag_id')) 
            $('main_printingtag_id').style.display = 'block';
          if(tempVar != 'exist')
            $('product_bulk_edit').style.display = 'block';
        }
        if (tempVar == "exist")
          removeChecks();
      }
    }));
    return false;
  }

  // Action for "Submit to Print Tag" button
  function checkedEntries() {
    var checkAll = 0;
    if ($('selectAllProducts').checked == true)
      checkAll = 1;    
    
    var searchParams = {
      'search': $('title').value,
      'minPrice': $('min_price').value,
      'maxPrice': $('max_price').value,
      'category_id': $('category_id').value,
      'section': $('section_id').value,
      'product_type': $('product_type').value,
      'temp_in_stock': $('stock').value
    };
    searchParams = JSON.encode(searchParams);
    
    if ($('printingtag_id') && ($('selectAllProducts').checked || tempStr) && (tempStr != ',<0>') && $('printingtag_id').value && $('printingtag_id').value != 0) {
      var url = en4.core.baseUrl + 'sitestoreproduct/printing-tag/print?checked_product=' + tempStr + '&checkAll=' + checkAll + '&printingtag_id=' + $('printingtag_id').value + '&store_id=<?php echo $this->store_id ?>&searchParams=' + searchParams;
      Smoothbox.open(url);
    }

    return false;
  }

<?php $url = $this->url(array('action' => 'store', 'store_id' => $this->store_id, 'type' => 'product', 'menuId' => 62, 'method' => 'manage'), 'sitestore_store_dashboard', false) ?>
  function multiFunctionality(tempValue) {
    if($('main_printingtag_id')) 
    $('main_printingtag_id').style.display = 'none';
    $('id_store_manage_products').innerHTML = '<center><img src="application/modules/Sitestore/externals/images/spinner_temp.gif" /></center>';
    var checkAll = 0;
    if ($('selectAllProducts').checked)
      checkAll = 1;
    
    var printingtag_id = 0;
    if($('printingtag_id'))
      printingtag_id = $('printingtag_id').value;
    
//    var searchParams = {
//      'search': $('title').value,
//      'minPrice': $('min_price').value,
//      'maxPrice': $('max_price').value,
//      'category_id': $('category_id').value,
//      'section': $('section_id').value,
//      'product_type': $('product_type').value,
//      'temp_in_stock': $('stock').value
//    };
    
    en4.core.request.send(new Request.JSON({
      url: en4.core.baseUrl + 'sitestoreproduct/product/multi-functionality',
      data: {
        format: 'json',
        checked_product: tempStr,
        checkAll: checkAll,
        printingtag_id: printingtag_id,
        tempValue: tempValue,
        store_id:<?php echo $this->store_id ?>,
//        searchParams: searchParams
      },
      onSuccess: function(responseJSON) {
        setTimeout("submitFilter('exist')", 50);//Increase Time Limit if required    
      }
    }));
    return false;

  }

  function product_of_this_page(check) {
    if (check == 1) {
      if ($('selectallchecks').checked) {
        $('selectallchecks').checked = false;
      } else {
        $('selectallchecks').checked = true;
      }
    }
    selectAll();
  }

  function all_product(check) {
    if (check == 1) {
      if ($('selectAllProducts').checked) {
        $('selectAllProducts').checked = false;
      } else {
        $('selectAllProducts').checked = true;
      }
    }
    checkAllProducts();
  }
  
en4.core.runonce.add(function(){
  $('export_products').addEvent('click', function(event)
  {
    var searchParams = {
      'search': $('title').value,
      'minPrice': $('min_price').value,
      'maxPrice': $('max_price').value,
      'category_id': $('category_id').value,
      'section': $('section_id').value,
      'product_type': $('product_type').value,
      'temp_in_stock': $('stock').value
    };
    searchParams = JSON.encode(searchParams);
    
//    if ($('selectAllProducts').checked == false){
      if(this.href.search("checked_products")){
        var splitStr = this.href.split("/checked_products");
        this.href = splitStr[0] + '/checked_products/' + tempStr + '/searchParams/' + searchParams;        
      }else {
        this.href = this.href + '/checked_products/' + tempStr + '/searchParams/' + searchParams;      
      }
//    }
  });
});
  

</script>

<?php
$this->headLink()
        ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/styles/style_rating.css')
        ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/styles/style_sitestoreproduct.css');
$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/scripts/core.js');
?>
<?php
$ratingValue = $this->ratingType;
$ratingShow = 'small-star';
if ($this->ratingType == 'rating_editor') {
  $ratingType = 'editor';
} elseif ($this->ratingType == 'rating_avg') {
  $ratingType = 'overall';
} else {
  $ratingType = 'user';
}
?>

<?php if (empty($this->responseFlag) && !empty($this->is_subject) && !empty($this->viewer_id) && !empty($this->can_edit)): ?>
  <div class="seaocore_add">
    <?php if (empty($this->quota) || (!empty($this->quota) && $this->current_count < $this->quota)): ?>  
      <a href='<?php echo $this->url(array('action' => 'create', 'store_id' => $this->store_id), 'sitestoreproduct_general', true) ?>' class='buttonlink seaocore_icon_add'><?php echo $this->translate('Create Product'); ?>
      </a>
    <?php endif; ?>

    <?php if (!empty($this->allowPrintingTag) && $this->paginator->count() > 0): ?>
      <a href="javascript:void(0);" id="tablerate_addlocation" class="buttonlink sitestoreproduct_qrcode_icon mright5" onclick='manage_store_dashboard(62, "manage", "printing-tag")' ><?php echo $this->translate("Manage Printing Tag") ?></a>
    <?php endif; ?>
  </div>
<?php endif; ?>
<?php if (empty($this->responseFlag) && $this->paginator->count() > 0): ?>
  <div class="seaocore_searchform_criteria seaocore_searchform_criteria_horizontal">
    <form class="field_search_criteria" id="filter_form" onSubmit="return submitFilter();">
      <div>
        <ul>
          <li>
            <span><label><?php echo $this->translate("Title") ?></label></span>
            <?php if (empty($this->title)): ?>
              <input id="title" type="text" name="title" /> 
            <?php else: ?>
              <input id="title" type="text" name="title" value="<?php echo $this->translate($this->getTitle()) ?>"/>
            <?php endif; ?>
          </li>      
          <li id="integer-wrapper">
            <span><label><?php echo $this->translate("Price") ?></label></span>
            <div>
              <?php if ($this->price_min == ''): ?>
                <input id="min_price" type="text" name="price_min" placeholder="min" class="input_field_small" /> 
              <?php else: ?>
                <input id="min_price" type="text" name="price_min" placeholder="min" value="<?php echo $this->price_min ?>" class="input_field_small" />
              <?php endif; ?>
              <?php if ($this->price_max == ''): ?>
                <input id="max_price" type="text" name="price_max" placeholder="max" class="input_field_small" /> 
              <?php else: ?>
                <input id="max_price" type="text" name="price_max" placeholder="max" value="<?php echo $this->price_max ?>" class="input_field_small" />
              <?php endif; ?>
            </div>
          </li>
          <?php
          $categories = Engine_Api::_()->getDbtable('categories', 'sitestoreproduct')->getCategoriesList(0);
          ?> <li>
            <span> <label class="optional" for="category_id"><?php echo $this->translate('Category'); ?></label></span>

            <div class="form-element" id="category_id-element">
              <select id="category_id" name="category_id" >
                <option value="0"><?php echo $this->translate("Category") ?></option>            
                <?php foreach ($categories as $category): ?>
                  <option value="<?php echo $category->category_id; ?>" <?php if ($this->category_id == $category->category_id)
                echo "selected";
                  ?>><?php echo $this->translate($category->category_name); ?>
                  </option>
  <?php endforeach; ?>
              </select>
            </div>

          </li>
          <?php
          $sections = Engine_Api::_()->getDbTable('sections', 'sitestoreproduct')->getStoreSections($this->store_id);
          ?>
          <li>
            <span> <label class="optional" for="section_id"><?php echo $this->translate('Section'); ?></label></span>

            <div class="form-element" id="category_id-element">
              <select id="section_id" name="section_id">
                <option selected="selected" label="" value="0"><?php echo $this->translate("Section") ?></option>
                <?php foreach ($sections as $section) : ?> 
                  <option <?php
                  if (@array_key_exists('section_id', $value) && $value['section_id'] == $section->section_id) {
                    echo 'selected="selected"';
                  }
                  ?>  value="<?php echo $section->section_id ?>"> <?php echo $this->translate($section->section_name); ?></option>
  <?php endforeach; ?>
              </select></div> </li>

          <?php
          $categories = Engine_Api::_()->getDbtable('categories', 'sitestoreproduct')->getCategoriesList(0);
          ?>
          <li>
            <span><label>
  <?php echo $this->translate("Product Type") ?>	
              </label></span>

            <select id="product_type" name="product_type">
              <option value="0" ><?php echo $this->translate("Select") ?></option>
              <option value="simple" <?php if ($this->product_type == 'simple')
    echo "selected";
  ?> ><?php echo $this->translate("Simple Products") ?></option>
              <option value="grouped" <?php if ($this->product_type == 'grouped')
                      echo "selected";
  ?> ><?php echo $this->translate("Grouped Products") ?></option>
              <option value="configurable" <?php if ($this->product_type == 'configurable')
                      echo "selected";
                    ?> ><?php echo $this->translate("Configurable Products") ?></option>
              <option value="virtual" <?php if ($this->product_type == 'virtual')
                      echo "selected";
  ?> ><?php echo $this->translate("Virtual Products") ?></option>            
              <option value="bundled" <?php if ($this->product_type == 'bundled')
                      echo "selected";
                    ?> ><?php echo $this->translate("Bundled Products") ?></option> 
              <option value="downloadable" <?php if ($this->product_type == 'downloadable')
                      echo "selected";
  ?> ><?php echo $this->translate("Downloadable Products") ?></option> 
            </select>
          </li>

          <li>
            <span><label>
  <?php echo $this->translate("Out of Stock") ?>	
              </label></span>
            <select id="stock" name="stock">
              <option value="0" ><?php echo $this->translate("Select") ?></option>
              <option value="2" ><?php echo $this->translate("Yes") ?></option>
              <option value="1" ><?php echo $this->translate("No") ?></option>
            </select>
          </li>

  <?php if (!empty($this->directPayment) && !empty($this->isDownPaymentEnable)) : ?>
            <li>
              <span><label><?php echo $this->translate("Downpayment") ?></label></span>
              <select id="downpayment" name="downpayment">
                <option value="0" ><?php echo $this->translate("Select") ?></option>
                <option value="1" <?php if ($this->downpayment == 1) : ?>selected='selected' <?php endif; ?>><?php echo $this->translate("Yes") ?></option>
                <option value="2" <?php if ($this->downpayment == 2) : ?>selected='selected' <?php endif; ?>><?php echo $this->translate("No") ?></option>
              </select>
            </li>
  <?php endif; ?>
          <!--          <li>
                      <span><label>
  <?php //echo $this->translate("Featured")  ?>	
                        </label></span>
                      <select id="featured" name="featured">
                        <option value="0" ><?php // echo $this->translate("Select") ?></option>
                        <option value="2" ><?php //echo $this->translate("Yes")  ?></option>
                        <option value="1" ><?php //echo $this->translate("No")  ?></option>
                      </select>
                    </li>
                    <li>
                      <span><label>
  <?php //echo $this->translate("Sponsored")  ?>	
                        </label></span>
                      <select id="sponsored" name="sponsored">
                        <option value="0"><?php // echo $this->translate("Select") ?></option>
                        <option value="2" ><?php //echo $this->translate("Yes")  ?></option>
                        <option value="1" ><?php // echo $this->translate("No")  ?></option>
                      </select>
                    </li>
                    <li>
                      <span><label>
  <?php echo $this->translate("New") ?>	
                        </label></span>
                      <select id="new" name="new">
                        <option value="0" ><?php //echo $this->translate("Select")  ?></option>
                        <option value="2" ><?php //echo $this->translate("Yes") ?></option>
                        <option value="1" ><?php // echo $this->translate("No")  ?></option>
                      </select>
                    </li>
           
                    <li>
                      <span><label>
  <?php //echo $this->translate("Status")  ?>	
                        </label></span>
                      <select id="temp_status" name="temp_status">
                        <option value="0" ><?php // echo $this->translate("Select")  ?></option>
                        <option value="2" ><?php //echo $this->translate("Closed Products")  ?></option>
                        <option value="1"  ><?php // echo $this->translate("Open Products")  ?></option>
                      </select>
                    </li>-->
          <li class="clear mtop10">
            <button  name="search" ><?php echo $this->translate("Search") ?></button></li>
          <li>
            <span id="search_spinner"></span>
          </li>

        </ul>
      </div>
    </form>
  </div>
    <?php endif; ?>

<div id="sitestoreproduct_search">
<?php if ($this->paginator->count() > 0): ?>

    <form id='multiselect_form' name='multiselect_form' method="post" onSubmit="return checkedEntries()">

  <?php if (empty($this->responseFlag)) : ?>
    <?php if (!empty($this->can_edit)) : ?>

          <div id="product_bulk_edit" class="clr sitestore_manage_tags_options mbot10" >
            <div>
              <input type='checkbox' class='checkbox' id='selectallchecks' onclick='product_of_this_page(0);' />
              <label onclick='product_of_this_page(1);'><?php echo $this->translate("Products of this page") ?></label>
            </div>         

            <div>
              <input type='checkbox' class='checkbox' id='selectAllProducts' onclick='all_product(0)' /> 
              <label onClick="all_product(1)"><?php echo $this->translate("All Products") ?></label>
            </div> 

            <!--            <li>
                          <a class="buttonlink seaocore_icon_sponsored" onclick="multiFunctionality('sponsored')"><?php //echo $this->translate("Make Sponsored");  ?></a>
                        </li>
                        
                        <li>
                          <a class="buttonlink seaocore_icon_unsponsored" onclick="multiFunctionality('unSponsored')"><?php //echo $this->translate("Make Un-Sponsored");  ?></a>
                        </li>
                        
                        <li>
                          <a class="buttonlink seaocore_icon_featured" onclick="multiFunctionality('featured')"><?php //echo $this->translate("Make Featured");  ?></a>   
                        </li>
                        
                        <li>
                          <a class="buttonlink seaocore_icon_unfeatured" onclick="multiFunctionality('unFeatured')"><?php //echo $this->translate("Make Un-Featured");  ?></a>   
                        </li>
                        
                        <li>
                        <a class="buttonlink icon_sitestore_highlighted" onclick="multiFunctionality('highlighted')"><?php //echo $this->translate("Make Highlighted");  ?></a>
                        </li>-->

            <!--              <li>
                        <a class="buttonlink icon_sitestore_unhighlighted" onclick="multiFunctionality('unHighlighted')"><?php // echo $this->translate("Make Un-Highlighted");  ?></a>
                        </li>-->

            <div>
              <a class="buttonlink seaocore_icon_approved" onclick="multiFunctionality('enable')" href="javascript:void(0);"><?php echo $this->translate("Enable Product"); ?></a>
            </div>

            <div>
              <a class="buttonlink seaocore_icon_disapproved" onclick="multiFunctionality('disable')" href="javascript:void(0);"><?php echo $this->translate("Disable Product"); ?></a>
            </div>

            <div>
              <a class="buttonlink seaocore_icon_delete" onclick="multiFunctionality('delete')" href="javascript:void(0);"><?php echo $this->translate("Delete Product"); ?></a> 
            </div> 
            
            <div>
              <a id="export_products" href=<?php echo $this->url(array('action' => 'index', 'store_id' => $this->store_id), 'sitestoreproduct_export_general', true) ?> target='downloadframe' class="buttonlink sitestoreproduct_export_icon mright5"><?php echo $this->translate('Export Products') ?></a>
            </div>
          </div>
    <?php endif; ?>

        <?php if (!empty($this->allowPrintingTag) && (!empty($countTags)) && $this->paginator->count() > 0): ?> 
      <div id="main_printingtag_id" class="mbot5">
          <select id="printingtag_id" name="printingtag_id">                           
      <?php foreach ($availableTagsObj as $availableTag) : ?> 
              <option <?php ?>  value="<?php echo $availableTag->printingtag_id ?>"> <?php echo $this->translate($availableTag->tag_name); ?></option>
            <?php endforeach; ?>
          </select>
          <button type="submit"><?php echo $this->translate("Submit to Print Tags") ?></button>  
          <span id="printingtag_spinner_image"></span>
      </div>
    <?php endif; ?>
        <span id="product_bulk"></span>
        <div id="id_store_manage_products" class="sitestore_manage_product clr">
  <?php endif; ?>


        <div class="sr_sitestoreproduct_browse_lists_view_options b_medium" id="sr_sitestoreproduct_browse_lists_view_options_b_medium">
          <div class="fleft"> 
            <span id="product_image_tags"></span>
          <?php echo $this->translate(array('%s product found.', '%s products found.', $this->totalResults), $this->locale()->toNumber($this->totalResults)) ?>
          </div>
        </div>
            <?php if ($this->list_view): ?>

          <div id="grid_view" style="display: none;">

                <?php if (empty($this->viewType)): ?>
              <ul class="sr_sitestoreproduct_browse_list" id="sr_sitestoreproduct_browse_list">
                  <?php $tempFlag = 0; ?>
                    <?php foreach ($this->paginator as $sitestoreproduct): ?>
                      <?php $getIntrestedMemberCount = Engine_Api::_()->getDbtable('notifyemails', 'sitestoreproduct')->getNotifyEmail($sitestoreproduct->product_id, 'COUNT(notifyemail_id)')->query()->fetchColumn(); ?>
                      <?php if (!empty($sitestoreproduct->sponsored)): ?>
                    <li class="sitestoreproduct_q_v_wrap list_sponsered b_medium">
        <?php else: ?>
                    <li class="sitestoreproduct_q_v_wrap b_medium">
                      <?php endif; ?>
                    <div>
                    <?php if (!empty($this->can_edit)): ?>

                        <?php $tempPageIdsArray[] = $sitestoreproduct->product_id; ?>

                        <input type='checkbox' onclick='makeProductStr(<?php echo $sitestoreproduct->product_id; ?>)' class='checkbox' name='selected_product_<?php echo $sitestoreproduct->product_id; ?>' id='selected_product_<?php echo $sitestoreproduct->product_id; ?>' value="<?php echo $sitestoreproduct->product_id; ?>"/>
                      <?php endif; ?>
                    </div>
                      <?php $tempFlag++; ?>
                    <div class='sr_sitestoreproduct_browse_list_photo b_medium'>
                      <?php $product_id = $sitestoreproduct->product_id; ?>
                      <?php $quickViewButton = true; ?>
                      <?php include APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/scripts/_quickView.tpl'; ?>
                      <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.fs.markers', 1)): ?>
                        <?php if ($sitestoreproduct->featured): ?>
                          <span class="seaocore_list_featured_label" title="<?php echo $this->translate('Featured'); ?>"><?php echo $this->translate('Featured'); ?></span>
                        <?php endif; ?>
                        <?php if ($sitestoreproduct->newlabel): ?>
                          <i class="sr_sitestoreproduct_list_new_label" title="<?php echo $this->translate('New'); ?>"></i>
                        <?php endif; ?>
                      <?php endif; ?>

                           <?php echo $this->htmlLink($sitestoreproduct->getHref(), $this->itemPhoto($sitestoreproduct, 'thumb.normal', '', array('align' => 'center'))) ?>

                      <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.fs.markers', 1)): ?>
                        <?php if (!empty($sitestoreproduct->sponsored)): ?>
                          <div class="sr_sitestoreproduct_list_sponsored_label" style="background: <?php
              echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.sponsoredcolor', '#FC0505');
              ;
              ?>">
                          <?php echo $this->translate('SPONSORED'); ?>                 
                          </div>
                            <?php endif; ?>
                          <?php endif; ?>
                    </div>
                        <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 3)): ?>
                      <div class="sr_sitestoreproduct_browse_list_rating">
          <?php if (!empty($sitestoreproduct->rating_editor) && ($ratingValue == 'rating_both' || $ratingValue == 'rating_editor')): ?>
                          <div class="clr">	
                            <div class="sr_sitestoreproduct_browse_list_rating_stats">
                                <?php echo $this->translate("Editor Rating"); ?>
                            </div>
                                <?php $ratingData = $this->ratingTable->ratingbyCategory($sitestoreproduct->product_id, 'editor', $sitestoreproduct->getType()); ?>
                            <div class="sr_sitestoreproduct_ur_show_rating_star fnone o_hidden">
                              <span class="sr_sitestoreproduct_browse_list_rating_stars">
                                <span class="fleft">
                              <?php echo $this->showRatingStarSitestoreproduct($sitestoreproduct->rating_editor, 'editor', 'big-star'); ?>
                                </span>
            <?php if (count($ratingData) > 1): ?>
                                  <i class="fright arrow_btm"></i>
                                  <?php endif; ?>
                              </span>

                                    <?php if (count($ratingData) > 1): ?>
                                <div class="sr_sitestoreproduct_ur_show_rating br_body_bg b_medium">
                                  <div class="sr_sitestoreproduct_profile_rating_parameters sr_sitestoreproduct_ur_show_rating_box">

                                        <?php foreach ($ratingData as $reviewcat): ?>

                                      <div class="o_hidden">
                                        <?php if (!empty($reviewcat['ratingparam_name'])): ?>
                                          <div class="parameter_title">
                                            <?php echo $this->translate($reviewcat['ratingparam_name']); ?>
                                          </div>
                                          <div class="parameter_value">
                                            <?php echo $this->showRatingStarSitestoreproduct($reviewcat['avg_rating'], 'editor', 'small-box', $reviewcat['ratingparam_name']); ?>
                                          </div>
                                        <?php else: ?>
                                          <div class="parameter_title">
                                        <?php echo $this->translate("Overall Rating"); ?>
                                          </div>	
                                          <div class="parameter_value" style="margin: 0px 0px 5px;">
                                    <?php echo $this->showRatingStarSitestoreproduct($reviewcat['avg_rating'], 'editor', 'big-star'); ?>
                                          </div>
                <?php endif; ?> 
                                      </div>

                            <?php endforeach; ?>
                                  </div>
                                </div>
                              <?php endif; ?>
                            </div>
                          </div> 
                            <?php endif; ?>
                            <?php if (!empty($sitestoreproduct->rating_users) && ($ratingValue == 'rating_both' || $ratingValue == 'rating_users')): ?>
                          <div class="clr">
                            <div class="sr_sitestoreproduct_browse_list_rating_stats">
                              <?php echo $this->translate("User Ratings"); ?><br />
                            <?php
                            $totalUserReviews = $sitestoreproduct->review_count;
                            if ($sitestoreproduct->rating_editor) {
                              $totalUserReviews = $sitestoreproduct->review_count - 1;
                            }
                            ?>
                                <?php echo $this->translate(array('Based on %s review', 'Based on %s reviews', $totalUserReviews), $this->locale()->toNumber($totalUserReviews)) ?>
                            </div>
                                <?php $ratingData = $this->ratingTable->ratingbyCategory($sitestoreproduct->product_id, 'user', $sitestoreproduct->getType()); ?>
                            <div class="sr_sitestoreproduct_ur_show_rating_star fnone o_hidden">
                              <span class="sr_sitestoreproduct_browse_list_rating_stars">
                                <span class="fleft">
                              <?php echo $this->showRatingStarSitestoreproduct($sitestoreproduct->rating_users, 'user', 'big-star'); ?>
                                </span>
            <?php if (count($ratingData) > 1): ?>
                                  <i class="fright arrow_btm"></i>
                                  <?php endif; ?>
                              </span>

                                    <?php if (count($ratingData) > 1): ?>
                                <div class="sr_sitestoreproduct_ur_show_rating  br_body_bg b_medium">
                                  <div class="sr_sitestoreproduct_profile_rating_parameters sr_sitestoreproduct_ur_show_rating_box">

                                        <?php foreach ($ratingData as $reviewcat): ?>

                                      <div class="o_hidden">
                                        <?php if (!empty($reviewcat['ratingparam_name'])): ?>
                                          <div class="parameter_title">
                                            <?php echo $this->translate($reviewcat['ratingparam_name']); ?>
                                          </div>
                                          <div class="parameter_value">
                                            <?php echo $this->showRatingStarSitestoreproduct($reviewcat['avg_rating'], 'user', 'small-box', $reviewcat['ratingparam_name']); ?>
                                          </div>
                                        <?php else: ?>
                                          <div class="parameter_title">
                                        <?php echo $this->translate("Overall Rating"); ?>
                                          </div>	
                                          <div class="parameter_value" style="margin: 0px 0px 5px;">
                                    <?php echo $this->showRatingStarSitestoreproduct($reviewcat['avg_rating'], 'user', 'big-star'); ?>
                                          </div>
                <?php endif; ?> 
                                      </div>

                            <?php endforeach; ?>
                                  </div>
                                </div> 
            <?php endif; ?> 
                            </div>
                          </div>  
                            <?php endif; ?>

                          <?php if (!empty($sitestoreproduct->rating_avg) && ($ratingValue == 'rating_avg')): ?>
                          <div class="clr">
                            <div class="sr_sitestoreproduct_browse_list_rating_stats">
              <!--	                    <?php //echo $this->translate("Overall Rating");     ?><br />-->

                                <?php echo $this->translate(array('Based on %s review', 'Based on %s reviews', $sitestoreproduct->review_count), $this->locale()->toNumber($sitestoreproduct->review_count)) ?>
                            </div>
                                <?php $ratingData = $this->ratingTable->ratingbyCategory($sitestoreproduct->product_id, null, $sitestoreproduct->getType()); ?>
                            <div class="sr_sitestoreproduct_ur_show_rating_star fnone o_hidden">
                              <span class="sr_sitestoreproduct_browse_list_rating_stars">
                                <span class="fleft">
                              <?php echo $this->showRatingStarSitestoreproduct($sitestoreproduct->rating_avg, $ratingType, 'big-star'); ?>
                                </span>
            <?php if (count($ratingData) > 1): ?>
                                  <i class="fright arrow_btm"></i>
                                  <?php endif; ?>
                              </span>

                                    <?php if (count($ratingData) > 1): ?>
                                <div class="sr_sitestoreproduct_ur_show_rating  br_body_bg b_medium">
                                  <div class="sr_sitestoreproduct_profile_rating_parameters sr_sitestoreproduct_ur_show_rating_box">

                                        <?php foreach ($ratingData as $reviewcat): ?>

                                      <div class="o_hidden">
                                        <?php if (!empty($reviewcat['ratingparam_name'])): ?>
                                          <div class="parameter_title">
                                            <?php echo $this->translate($reviewcat['ratingparam_name']); ?>
                                          </div>
                                          <div class="parameter_value">
                                            <?php echo $this->showRatingStarSitestoreproduct($reviewcat['avg_rating'], $ratingType, 'small-box', $reviewcat['ratingparam_name']); ?>
                                          </div>
                                        <?php else: ?>
                                          <div class="parameter_title">
                                        <?php echo $this->translate("Overall Rating"); ?>
                                          </div>	
                                          <div class="parameter_value" style="margin: 0px 0px 5px;">
                                    <?php echo $this->showRatingStarSitestoreproduct($reviewcat['avg_rating'], 'user', 'big-star'); ?>
                                          </div>
                <?php endif; ?> 
                                      </div>

                          <?php endforeach; ?>
                                  </div>
                                </div> 
            <?php endif; ?> 
                            </div>
                          </div>  
                            <?php endif; ?>
                      </div>
        <?php endif; ?>

                    <div class='sr_sitestoreproduct_browse_list_info'>  

                      <div class='sr_sitestoreproduct_browse_list_info_header o_hidden'>
                        <div class="sr_sitestoreproduct_list_title">
        <?php echo $this->htmlLink($sitestoreproduct->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($sitestoreproduct->getTitle(), $this->title_truncation), array('title' => $sitestoreproduct->getTitle())); ?>
                        </div>
                        <div class="clear"></div>
                      </div>
                      <div class='sr_sitestoreproduct_browse_list_info_stat seaocore_txt_light'>
                        <a href="<?php echo $this->url(array('category_id' => $sitestoreproduct->category_id, 'categoryname' => $sitestoreproduct->getCategory()->getCategorySlug()), "" . $this->categoryRouteName . ""); ?>"> 
                        <?php echo $sitestoreproduct->getCategory()->getTitle(true) ?>
                        </a>
                      </div>

                      <div class='sr_sitestoreproduct_browse_list_info_stat seaocore_txt_light'>
                        <?php
                        echo $this->translate("Created On: %s", gmdate('M d,Y, g:i A', strtotime($sitestoreproduct->start_date))) . '<br />';
                        if (!empty($sitestoreproduct->end_date)) :
                          echo $this->translate("On Sale Till: %s", gmdate('M d,Y, g:i A', strtotime($sitestoreproduct->end_date))) . '<br />';
                        endif;
                        echo $this->translate(array('%s like', '%s likes', $sitestoreproduct->like_count), $this->locale()->toNumber($sitestoreproduct->like_count)) . '<br />';
                        if (!empty($getIntrestedMemberCount)):
                          echo $this->translate(array('%s buyer intrested.', '%s buyers intrested.', $getIntrestedMemberCount), $this->locale()->toNumber($getIntrestedMemberCount)) . '<br />';
                        endif;
                        ?>
                      <?php
                      $productTitle = Engine_Api::_()->sitestoreproduct()->getProductTypeName($sitestoreproduct->product_type);
                      echo $productTitle;
                      ?>
                      </div>

                      <!-- DISPLAY PRODUCTS -->
                      <?php
                      // CALLING HELPER FOR GETTING PRODUCT INFORMATIONS
                      echo $this->getProductInfo($sitestoreproduct, $this->identity, 'grid_view', 0, $this->showinStock, true);
                      ?>

                      <!--							<div class='sr_sitestoreproduct_browse_list_info_blurb'>
                      <?php //if($this->bottomLine):   ?>
                      <?php //echo $this->viewMore($sitestoreproduct->getBottomLine(), 125, 5000);?>
        <?php //else:   ?>
                      <?php //echo $this->viewMore(strip_tags($sitestoreproduct->body), 125, 5000);  ?>
                      <?php //endif;   ?>
                                    </div>-->
                      <!--                      <div class="sr_sitestoreproduct_browse_list_info_footer clr o_hidden">
                                              <div>
                        <?php //echo $this->compareButtonSitestoreproduct($sitestoreproduct, $this->identity);   ?>
                                              </div>
                                              <div>
                        <?php //echo $this->addToWishlistSitestoreproduct($sitestoreproduct, array('classIcon' => 'sr_sitestoreproduct_wishlist_href_link', 'classLink' => ''));   ?>
                                              </div>
                                            </div>-->
                      <div class='sr_sitestoreproduct_browse_list_options clr'>
                        <?php
                        if (!empty($this->can_edit)):
                          echo $this->htmlLink($sitestoreproduct->getHref(), $this->translate('View Product'), array('class' => 'buttonlink item_icon_sitestoreproduct'));
                          echo $this->htmlLink(array('route' => 'sitestoreproduct_specific', 'action' => 'edit', 'product_id' => $sitestoreproduct->product_id), $this->translate('Dashboard'), array('class' => 'buttonlink seaocore_icon_edit'));
                          echo $this->htmlLink($this->url(array('action' => 'delete', 'product_id' => $sitestoreproduct->product_id), "sitestoreproduct_general", true), $this->translate('Delete Product'), array('class' => 'buttonlink seaocore_icon_delete'));
                          if (empty($sitestoreproduct->highlighted)):
                            echo $this->htmlLink(array('route' => 'sitestoreproduct_manage', 'action' => 'highlighted', 'product_id' => $sitestoreproduct->product_id, 'is_store' => $this->is_subject), $this->translate('Make Highlighted'), array('onclick' => 'owner(this);return false', ' class' => 'buttonlink icon_sitestore_highlighted'));
                          else:

                            echo $this->htmlLink(array('route' => 'sitestoreproduct_manage', 'action' => 'highlighted', 'product_id' => $sitestoreproduct->product_id, 'is_store' => $this->is_subject), $this->translate('Make Un-highlighted'), array('onclick' => 'owner(this);return false', ' class' => 'buttonlink icon_sitestore_unhighlighted'));

                          endif;
                        endif;

                        if (!empty($this->viewer_level_id) && ($this->viewer_level_id == 1)):
                          if (empty($sitestoreproduct->featured)):
                            echo $this->htmlLink(array('route' => 'sitestoreproduct_manage', 'action' => 'featured', 'product_id' => $sitestoreproduct->product_id, 'is_store' => $this->is_subject), $this->translate('Make Featured'), array('onclick' => 'owner(this);return false', ' class' => 'buttonlink seaocore_icon_featured'));
                          else:
                            echo $this->htmlLink(array('route' => 'sitestoreproduct_manage', 'action' => 'featured', 'product_id' => $sitestoreproduct->product_id, 'is_store' => $this->is_subject), $this->translate('Make Un-featured'), array('onclick' => 'owner(this);return false', ' class' => 'buttonlink seaocore_icon_unfeatured'));
                          endif;


                          if (empty($sitestoreproduct->sponsored)):
                            echo $this->htmlLink(array('route' => 'sitestoreproduct_manage', 'action' => 'sponsored', 'product_id' => $sitestoreproduct->product_id, 'is_store' => $this->is_subject), $this->translate('Make Sponsored'), array('onclick' => 'owner(this);return false', ' class' => 'buttonlink seaocore_icon_sponsored'));
                          else:
                            echo $this->htmlLink(array('route' => 'sitestoreproduct_manage', 'action' => 'sponsored', 'product_id' => $sitestoreproduct->product_id, 'is_store' => $this->is_subject), $this->translate('Make Un-sponsored'), array('onclick' => 'owner(this);return false', ' class' => 'buttonlink seaocore_icon_unsponsored'));
                          endif;
                        endif;
                        if ($sitestoreproduct->draft == 1 && !empty($this->can_edit)):
                          echo $this->htmlLink(array('route' => "sitestoreproduct_specific", 'action' => 'publish', 'product_id' => $sitestoreproduct->product_id), $this->translate('Publish Product'), array('onclick' => 'owner(this);return false', ' class' => 'buttonlink seaocore_icon_approved'));
                        endif;

                        if (empty($sitestoreproduct->search) && !empty($this->can_edit)):
                          echo $this->htmlLink(array('route' => 'sitestoreproduct_manage', 'action' => 'enable-product', 'product_id' => $sitestoreproduct->product_id, 'flag' => 1), $this->translate('Enable Product'), array('onclick' => 'owner(this);return false', ' class' => 'buttonlink seaocore_icon_approved'));
                        endif;
                        if ($sitestoreproduct->product_type != 'grouped' && $sitestoreproduct->product_type != 'bundled'):
                          echo $this->htmlLink(array('route' => 'sitestoreproduct_general', 'action' => 'copy-product', 'product_id' => $sitestoreproduct->product_id), $this->translate('Copy Products'), array('class' => 'buttonlink seaocore_icon_copyproduct'));
                        endif;
                        ?>
                      </div>
                    </div>
                  </li>
                <?php endforeach; ?>
              </ul>

              <?php else: ?>

              <ul class="sr_sitestoreproduct_browse_list" id="sr_sitestoreproduct_browse_list">
                  <?php $tempPageIdsArray = array(); ?>
                  <?php
                  foreach ($this->paginator as $sitestoreproduct):
                    $tempPageIdsArray[] = $sitestoreproduct->product_id;
                    ?>
                      <?php $getIntrestedMemberCount = Engine_Api::_()->getDbtable('notifyemails', 'sitestoreproduct')->getNotifyEmail($sitestoreproduct->product_id, 'COUNT(notifyemail_id)')->query()->fetchColumn(); ?>
                      <?php if (!empty($sitestoreproduct->sponsored)): ?>
                    <li class="sitestoreproduct_q_v_wrap list_sponsered b_medium">
                      <?php else: ?>
                    <li class="sitestoreproduct_q_v_wrap b_medium">
                      <?php endif; ?>
                    <div class='sr_sitestoreproduct_browse_list_photo b_medium'>
                      <?php $product_id = $sitestoreproduct->product_id; ?>
                      <?php $quickViewButton = true; ?>
                      <?php include APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/scripts/_quickView.tpl'; ?>
                      <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.fs.markers', 1)): ?>
                        <?php if ($sitestoreproduct->featured): ?>
                          <span class="seaocore_list_featured_label" title="<?php echo $this->translate('Featured'); ?>"><?php echo $this->translate('Featured'); ?></span>
                          <?php endif; ?>
                          <?php if ($sitestoreproduct->newlabel): ?>
                          <i class="sr_sitestoreproduct_list_new_label" title="<?php echo $this->translate('New'); ?>"></i>
                        <?php endif; ?>
                      <?php endif; ?>
        <?php echo $this->htmlLink($sitestoreproduct->getHref(), $this->itemPhoto($sitestoreproduct, 'thumb.normal', '', array('align' => 'center'))) ?>
        <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.fs.markers', 1)): ?>
          <?php if (!empty($sitestoreproduct->sponsored)): ?>
                          <div class="sr_sitestoreproduct_list_sponsored_label" style="background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.sponsoredcolor', '#FC0505'); ?>">
            <?php echo $this->translate('SPONSORED'); ?>                 
                          </div>
                          <?php endif; ?>
                          <?php endif; ?>
                    </div>

                    <div class='sr_sitestoreproduct_browse_list_info'>

                      <div class="sr_sitestoreproduct_browse_list_price_info">

        <?php if ($sitestoreproduct->price > 0): ?>
                          <div class="sr_sitestoreproduct_price">
                        <?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($sitestoreproduct->price); ?>
                          </div>
        <?php endif; ?>

                      </div>  
                    </div>


                    <div class="sr_sitestoreproduct_browse_list_rating">
                              <?php if (!empty($sitestoreproduct->rating_editor) && ($ratingValue == 'rating_both' || $ratingValue == 'rating_editor')): ?>
                        <div class="clr">	
                          <div class="sr_sitestoreproduct_browse_list_rating_stats">
                              <?php echo $this->translate("Editor Rating"); ?>
                          </div>
                              <?php $ratingData = $this->ratingTable->ratingbyCategory($sitestoreproduct->product_id, 'editor', $sitestoreproduct->getType()); ?>
                          <div class="sr_sitestoreproduct_ur_show_rating_star fnone o_hidden">
                            <span class="sr_sitestoreproduct_browse_list_rating_stars">
                              <span class="fleft">
          <?php echo $this->showRatingStarSitestoreproduct($sitestoreproduct->rating_editor, 'editor', 'big-star'); ?>
                              </span>
                                <?php if (count($ratingData) > 1): ?>
                                <i class="fright arrow_btm"></i>
          <?php endif; ?>
                            </span>

                                    <?php if (count($ratingData) > 1): ?>
                              <div class="sr_sitestoreproduct_ur_show_rating br_body_bg b_medium">
                                <div class="sr_sitestoreproduct_profile_rating_parameters sr_sitestoreproduct_ur_show_rating_box">

                                      <?php foreach ($ratingData as $reviewcat): ?>

                                    <div class="o_hidden">
                                        <?php if (!empty($reviewcat['ratingparam_name'])): ?>
                                        <div class="parameter_title">
                <?php echo $this->translate($reviewcat['ratingparam_name']); ?>
                                        </div>
                                        <div class="parameter_value">
                                        <?php echo $this->showRatingStarSitestoreproduct($reviewcat['avg_rating'], 'editor', 'small-box', $reviewcat['ratingparam_name']); ?>
                                        </div>
              <?php else: ?>
                                        <div class="parameter_title">
                                      <?php echo $this->translate("Overall Rating"); ?>
                                        </div>	
                                        <div class="parameter_value" style="margin: 0px 0px 5px;">
                                  <?php echo $this->showRatingStarSitestoreproduct($reviewcat['avg_rating'], 'editor', 'big-star'); ?>
                                        </div>
                            <?php endif; ?> 
                                    </div>

            <?php endforeach; ?>
                                </div>
                              </div>
                            <?php endif; ?>
                          </div>
                        </div> 
                          <?php endif; ?>
                          <?php if (!empty($sitestoreproduct->rating_users) && ($ratingValue == 'rating_both' || $ratingValue == 'rating_users')): ?>
                        <div class="clr">
                          <div class="sr_sitestoreproduct_browse_list_rating_stats">
                          <?php echo $this->translate("User Ratings"); ?><br />
                          <?php
                          $totalUserReviews = $sitestoreproduct->review_count;
                          if ($sitestoreproduct->rating_editor) {
                            $totalUserReviews = $sitestoreproduct->review_count - 1;
                          }
                          ?>
                              <?php echo $this->translate(array('Based on %s review', 'Based on %s reviews', $totalUserReviews), $this->locale()->toNumber($totalUserReviews)) ?>
                          </div>
                              <?php $ratingData = $this->ratingTable->ratingbyCategory($sitestoreproduct->product_id, 'user', $sitestoreproduct->getType()); ?>
                          <div class="sr_sitestoreproduct_ur_show_rating_star fnone o_hidden">
                            <span class="sr_sitestoreproduct_browse_list_rating_stars">
                              <span class="fleft">
          <?php echo $this->showRatingStarSitestoreproduct($sitestoreproduct->rating_users, 'user', 'big-star'); ?>
                              </span>
                                <?php if (count($ratingData) > 1): ?>
                                <i class="fright arrow_btm"></i>
          <?php endif; ?>
                            </span>

                                    <?php if (count($ratingData) > 1): ?>
                              <div class="sr_sitestoreproduct_ur_show_rating  br_body_bg b_medium">
                                <div class="sr_sitestoreproduct_profile_rating_parameters sr_sitestoreproduct_ur_show_rating_box">

                                      <?php foreach ($ratingData as $reviewcat): ?>

                                    <div class="o_hidden">
                                        <?php if (!empty($reviewcat['ratingparam_name'])): ?>
                                        <div class="parameter_title">
                <?php echo $this->translate($reviewcat['ratingparam_name']); ?>
                                        </div>
                                        <div class="parameter_value">
                                        <?php echo $this->showRatingStarSitestoreproduct($reviewcat['avg_rating'], 'user', 'small-box', $reviewcat['ratingparam_name']); ?>
                                        </div>
              <?php else: ?>
                                        <div class="parameter_title">
                                      <?php echo $this->translate("Overall Rating"); ?>
                                        </div>	
                                        <div class="parameter_value" style="margin: 0px 0px 5px;">
                                  <?php echo $this->showRatingStarSitestoreproduct($reviewcat['avg_rating'], 'user', 'big-star'); ?>
                                        </div>
                            <?php endif; ?> 
                                    </div>

                          <?php endforeach; ?>
                                </div>
                              </div> 
          <?php endif; ?> 
                          </div>
                        </div>  
                        <?php endif; ?>

        <?php if (!empty($sitestoreproduct->rating_avg) && ($ratingValue == 'rating_avg')): ?>
                        <div class="clr">
                          <div class="sr_sitestoreproduct_browse_list_rating_stats">
            <!--	                    <?php //echo $this->translate("Overall Rating");    ?><br />-->

                              <?php echo $this->translate(array('Based on %s review', 'Based on %s reviews', $sitestoreproduct->review_count), $this->locale()->toNumber($sitestoreproduct->review_count)) ?>
                          </div>
                              <?php $ratingData = $this->ratingTable->ratingbyCategory($sitestoreproduct->product_id, null, $sitestoreproduct->getType()); ?>
                          <div class="sr_sitestoreproduct_ur_show_rating_star fnone o_hidden">
                            <span class="sr_sitestoreproduct_browse_list_rating_stars">
                              <span class="fleft">
          <?php echo $this->showRatingStarSitestoreproduct($sitestoreproduct->rating_avg, $ratingType, 'big-star'); ?>
                              </span>
                                <?php if (count($ratingData) > 1): ?>
                                <i class="fright arrow_btm"></i>
          <?php endif; ?>
                            </span>

                                    <?php if (count($ratingData) > 1): ?>
                              <div class="sr_sitestoreproduct_ur_show_rating  br_body_bg b_medium">
                                <div class="sr_sitestoreproduct_profile_rating_parameters sr_sitestoreproduct_ur_show_rating_box">

                                      <?php foreach ($ratingData as $reviewcat): ?>

                                    <div class="o_hidden">
                                        <?php if (!empty($reviewcat['ratingparam_name'])): ?>
                                        <div class="parameter_title">
                <?php echo $this->translate($reviewcat['ratingparam_name']); ?>
                                        </div>
                                        <div class="parameter_value">
                                        <?php echo $this->showRatingStarSitestoreproduct($reviewcat['avg_rating'], $ratingType, 'small-box', $reviewcat['ratingparam_name']); ?>
                                        </div>
              <?php else: ?>
                                        <div class="parameter_title">
                                      <?php echo $this->translate("Overall Rating"); ?>
                                        </div>	
                                        <div class="parameter_value" style="margin: 0px 0px 5px;">
                                  <?php echo $this->showRatingStarSitestoreproduct($reviewcat['avg_rating'], $ratingType, 'big-star'); ?>
                                        </div>
                            <?php endif; ?> 
                                    </div>

            <?php endforeach; ?>
                                </div>
                              </div> 
                            <?php endif; ?> 
                          </div>
                        </div>  
        <?php endif; ?>
                    </div>

                    <div class="sr_sitestoreproduct_browse_list_info">
                      <div class="sr_sitestoreproduct_browse_list_info_header">
                        <div class="sr_sitestoreproduct_list_title">
                      <?php echo $this->htmlLink($sitestoreproduct->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($sitestoreproduct->getTitle(), $this->title_truncation), array('title' => $sitestoreproduct->getTitle())); ?>
                        </div>
                      </div>  

                      <!--                  <div class='sr_sitestoreproduct_browse_list_info_blurb'>
                      <?php //if($this->bottomLine):  ?>
                      <?php //echo $this->viewMore($sitestoreproduct->getBottomLine(), 125, 5000); ?>
                      <?php //else: ?>
        <?php //echo $this->viewMore(strip_tags($sitestoreproduct->body), 125, 5000); ?>
        <?php //endif;    ?>
                                        </div>-->


                      <!--                  <div class='sr_sitestoreproduct_browse_list_info_stat seaocore_txt_light'>
        <?php //echo $this->timestamp(strtotime($sitestoreproduct->creation_date))  ?><?php // if($this->postedby):  ?> <?php // echo $this->translate('created by');  ?>
                        <?php // echo $this->htmlLink($sitestoreproduct->getOwner()->getHref(), $sitestoreproduct->getOwner()->getTitle())     ?><?php //endif;     ?>
                                        </div>-->
                      <div class='sr_sitestoreproduct_browse_list_info_stat seaocore_txt_light'>
                        <a href="<?php echo $this->url(array('category_id' => $sitestoreproduct->category_id, 'categoryname' => $sitestoreproduct->getCategory()->getCategorySlug()), "" . $this->categoryRouteName . ""); ?>"> 
                        <?php echo $sitestoreproduct->getCategory()->getTitle(true) ?>
                        </a>
                      </div>

                      <div class='sr_sitestoreproduct_browse_list_info_stat seaocore_txt_light'>
                        <?php
                        echo $this->translate(array('%s like', '%s likes', $sitestoreproduct->like_count), $this->locale()->toNumber($sitestoreproduct->like_count)) . '<br />';
                        if (!empty($getIntrestedMemberCount)):
                          echo $this->translate(array('%s buyer intrested.', '%s buyers intrested.', $getIntrestedMemberCount), $this->locale()->toNumber($getIntrestedMemberCount)) . '<br />';
                        endif;
                        ?>
        <?php
        $productTitle = Engine_Api::_()->sitestoreproduct()->getProductTypeName($sitestoreproduct->product_type);
        echo $productTitle;
        ?>
                      </div>

                      <?php if ($sitestoreproduct->price > 0): ?>
                        <div class='sr_sitestoreproduct_browse_list_info_stat'>
                          <b><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($sitestoreproduct->price); ?></b>
                        </div>
                        <?php endif; ?>
                      <!-- DISPLAY PRODUCTS -->
        <?php
        // CALLING HELPER FOR GETTING PRODUCT INFORMATIONS
        echo $this->getProductInfo($sitestoreproduct, $this->identity, 'grid_view', 0, $this->showinStock, true);
        ?>

                      <div class="mtop10 sr_sitestoreproduct_browse_list_info_footer clr o_hidden">
                        <!--                        <div>
                          <?php //echo $this->compareButtonSitestoreproduct($sitestoreproduct);  ?>
                                                </div>
                                                <div>
                          <?php //echo $this->addToWishlistSitestoreproduct($sitestoreproduct, array('classIcon' => 'sr_sitestoreproduct_wishlist_href_link', 'classLink' => ''));  ?>
                                                </div>-->
                        <div class='sitestorevideo_profile_options'>
                          <?php
                          if (!empty($this->can_edit)):
                            echo $this->htmlLink($sitestoreproduct->getHref(), $this->translate('View Product'), array('class' => 'buttonlink item_icon_sitestoreproduct'));
                            echo $this->htmlLink(array('route' => 'sitestoreproduct_specific', 'action' => 'edit', 'product_id' => $sitestoreproduct->product_id), $this->translate('Dashboard'), array('class' => 'buttonlink seaocore_icon_edit'));
                            echo $this->htmlLink($this->url(array('action' => 'delete', 'product_id' => $sitestoreproduct->product_id), "sitestoreproduct_general", true), $this->translate('Delete Product'), array('class' => 'buttonlink seaocore_icon_delete'));
                            if (empty($sitestoreproduct->highlighted)):
                              echo $this->htmlLink(array('route' => 'sitestoreproduct_manage', 'action' => 'highlighted', 'product_id' => $sitestoreproduct->product_id, 'is_store' => $this->is_subject), $this->translate('Make Highlighted'), array('onclick' => 'owner(this);return false', ' class' => 'buttonlink icon_sitestore_highlighted'));
                            else:
                              echo $this->htmlLink(array('route' => 'sitestoreproduct_manage', 'action' => 'highlighted', 'product_id' => $sitestoreproduct->product_id, 'is_store' => $this->is_subject), $this->translate('Make Un-highlighted'), array('onclick' => 'owner(this);return false', ' class' => 'buttonlink icon_sitestore_highlighted'));
                            endif;
                          endif;

                          if (!empty($this->viewer_level_id) && ($this->viewer_level_id == 1)):
                            if (empty($sitestoreproduct->featured)):
                              echo $this->htmlLink(array('route' => 'sitestoreproduct_manage', 'action' => 'featured', 'product_id' => $sitestoreproduct->product_id, 'is_store' => $this->is_subject), $this->translate('Make Featured'), array('onclick' => 'owner(this);return false', ' class' => 'buttonlink seaocore_icon_featured'));
                            else:
                              echo $this->htmlLink(array('route' => 'sitestoreproduct_manage', 'action' => 'featured', 'product_id' => $sitestoreproduct->product_id, 'is_store' => $this->is_subject), $this->translate('Make Un-featured'), array('onclick' => 'owner(this);return false', ' class' => 'buttonlink seaocore_icon_featured'));
                            endif;


                            if (empty($sitestoreproduct->sponsored)):
                              echo $this->htmlLink(array('route' => 'sitestoreproduct_manage', 'action' => 'sponsored', 'product_id' => $sitestoreproduct->product_id, 'is_store' => $this->is_subject), $this->translate('Make Sponsored'), array('onclick' => 'owner(this);return false', ' class' => 'buttonlink seaocore_icon_sponsored'));
                            else:
                              echo $this->htmlLink(array('route' => 'sitestoreproduct_manage', 'action' => 'sponsored', 'product_id' => $sitestoreproduct->product_id, 'is_store' => $this->is_subject), $this->translate('Make Un-sponsored'), array('onclick' => 'owner(this);return false', ' class' => 'buttonlink seaocore_icon_sponsored'));
                            endif;
                          endif;
                          ?>
                        </div>
                      </div>
                  </li>
      <?php endforeach; ?>
              </ul>

    <?php endif; ?>

          </div>




              <?php endif; ?>


                <?php //if (empty($this->responseFlag)) : ?>
        <div id="srijan">
          <div class="clear"></div>
          <div class="seaocore_pagination">
                <?php // echo $this->paginationControl($this->paginator, null, null, array('query' => $this->formValues, 'pageAsQuery' => true,));  ?>
            <div>
  <?php if ($this->paginator->getCurrentPageNumber() > 1): ?>
                <div id="store_table_rate_previous" class="paginator_previous">
                <?php
                echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
                    'onclick' => 'paginationPrevious()',
                    'class' => 'buttonlink icon_previous'
                ));
                ?>
                  <span id="tablerate_spinner_prev"></span>
                </div>
                <?php endif; ?>
                <?php if ($this->paginator->getCurrentPageNumber() < $this->paginator->count()): ?>
                <div id="store_table_rate_next" class="paginator_next">
                  <span id="tablerate_spinner_next"></span>
    <?php
    echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
        'onclick' => 'paginationNext()',
        'class' => 'buttonlink_right icon_next'
    ));
    ?>
                </div>
      <?php endif; ?>
            </div>
          </div>	
        </div>
  <?php //endif;  ?>
  <?php if (empty($this->responseFlag)) : ?>
        </div>
      <?php endif; ?>

        <?php elseif (isset($this->params['tag_id']) || isset($this->params['category_id'])): ?>
      <br/>
      <div class="tip mtip10">
        <span> <?php echo $this->translate('Nobody has created a product with that criteria.'); ?>
        </span> 
      </div>
    <?php else: ?>
      <div class="tip mtop10"> 
        <span> 
  <?php echo $this->translate("No products have been created in your store yet. %s and get it discovered on %s!", "<a href='" . $this->url(array('action' => 'create', 'store_id' => $this->store_id), 'sitestoreproduct_general', true) . "'>" . $this->translate("Create your first product") . "</a>", $this->site_title); ?>
        </span>
      </div>


  <?php //if (empty($this->responseFlag)) :  ?>
      <div class="clear"></div>
      <div class="seaocore_pagination">
        <div>
          <div id="store_table_rate_previous">
            <span id="tablerate_spinner_prev"></span>
          </div>
          <div id="store_table_rate_next">
            <span id="tablerate_spinner_next"></span>
          </div>
        </div>
      </div>	

  <?php //endif;  ?>
<?php endif; ?>

  </form>

</div>

<script type="text/javascript" >
  function switchview(flage) {
    if (flage == 1) {
      if ($('image_view')) {
        if ($('sr_sitestoreproduct_map_canvas_view_browse'))
          $('sr_sitestoreproduct_map_canvas_view_browse').style.display = 'none';
        if ($('grid_view'))
          $('grid_view').style.display = 'none';
        $('image_view').style.display = 'block';
      }
    } else {
      if ($('grid_view')) {
        if ($('sr_sitestoreproduct_map_canvas_view_browse'))
          $('sr_sitestoreproduct_map_canvas_view_browse').style.display = 'none';
        $('grid_view').style.display = 'block';
        if ($('image_view'))
          $('image_view').style.display = 'none';
      }
    }
    tempCurrentViewType = flage;
  }

  currentPageProductIds = '<?php echo implode(",", $tempPageIdsArray); ?>';
</script>