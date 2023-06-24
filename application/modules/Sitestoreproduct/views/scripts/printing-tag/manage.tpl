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

<script type="text/javascript">
  var tableRateEnable = 0;
  function selectAll()
  {
    var i;
    var multidelete_form_table_printing_tag = $('multidelete_form_table_printing_tag');
    var inputs = multidelete_form_table_printing_tag.elements;
    for (i = 1; i < inputs.length; i++) {
      if (!inputs[i].disabled) {
        inputs[i].checked = inputs[0].checked;
      }
    }
  }

  function showsmoothbox(url) {
    Smoothbox.open(url);
  }

  function enabletablerate(id){
    if( tableRateEnable == 0 )
    {
      tableRateEnable = 1;
      $('show_status_image_' + id).innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitestoreproduct/externals/images/loading.gif" />';
      en4.core.request.send(new Request.JSON({
        url : en4.core.baseUrl + 'sitestoreproduct/printing-tag/printing-tag-enable',
        method : 'POST',
        data : {
          format : 'json',
          store_id : '<?php echo sprintf('%d', $this->store_id) ?>',
          tag_id : id
        },
        onSuccess : function(responseJSON) {
          tableRateEnable = 0;
          if( responseJSON.activeFlag == '0') {
            $('show_status_image_' + id).innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitestoreproduct/externals/images/region_enable0.gif" />';
          }else{
            $('show_status_image_' + id).innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitestoreproduct/externals/images/region_enable1.gif" />';
          }
        }

      })
    ); 
    }
  }

  en4.core.runonce.add(function(){
    var anchor = $('store_table_rate').getParent();
    document.getElementById('store_table_rate_previous').style.display = '<?php echo ( $this->paginator->getCurrentPageNumber() == 1 ? 'none' : '' ) ?>';
    $('store_table_rate_next').style.display = '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' ) ?>';
    $('store_table_rate_previous').removeEvents('click').addEvent('click', function(){
      $('tablerate_spinner_prev').innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitestoreproduct/externals/images/loading.gif" />';
      var tempTableRatePaginationUrl = '<?php echo $this->url(array('action' => 'store', 'store_id' => $this->store_id, 'type' => 'printing-tag', 'menuId' => 62, 'method' => 'manage', 'page' => $this->paginator->getCurrentPageNumber() - 1), 'sitestore_store_dashboard', true); ?>';
      if(tempTableRatePaginationUrl && typeof history.pushState != 'undefined') { 
        history.pushState( {}, document.title, tempTableRatePaginationUrl );
      }

      en4.core.request.send(new Request.HTML({
        url : en4.core.baseUrl + 'sitestoreproduct/printing-tag/manage/store_id/' + <?php echo sprintf('%d', $this->store_id) ?>,
        data : {
          format : 'html',
          subject : en4.core.subject.guid,
          page : '<?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() - 1) ?>'
        },
        onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {        
          $('tablerate_spinner_prev').innerHTML = '';
        }
      }),{
        'element' : anchor
      })
    });

    $('store_table_rate_next').removeEvents('click').addEvent('click', function(){
      $('tablerate_spinner_next').innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitestoreproduct/externals/images/loading.gif" />';

      var tempTableRatePaginationUrl = '<?php echo $this->url(array('action' => 'store', 'store_id' => $this->store_id, 'type' => 'printing-tag', 'menuId' => 62, 'method' => 'manage', 'page' => $this->paginator->getCurrentPageNumber() + 1), 'sitestore_store_dashboard', true); ?>';
      if(tempTableRatePaginationUrl && typeof history.pushState != 'undefined') { 
        history.pushState( {}, document.title, tempTableRatePaginationUrl );
      }
      en4.core.request.send(new Request.HTML({
        url : en4.core.baseUrl + 'sitestoreproduct/printing-tag/manage/store_id/' + <?php echo sprintf('%d', $this->store_id) ?>,
        data : {
          format : 'html',
          subject : en4.core.subject.guid,
          page : '<?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>'
        },
        onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {        
          $('tablerate_spinner_next').innerHTML = '';
        }
      }), {
        'element' : anchor
      })
    });

    $('multidelete_form_table_printing_tag').removeEvents('submit').addEvent('submit', function(e) {
      e.stop();

      var i;
      var printingtag_id_array = new Array();
      var multidelete_form_table_printing_tag = $('multidelete_form_table_printing_tag');
      var inputs = multidelete_form_table_printing_tag.elements;
      for (i = 1; i < inputs.length; i++) {
        if (!inputs[i].disabled) {
          if(inputs[i].checked ){
            if(inputs[i].value){
              printingtag_id_array[i] = inputs[i].value;
            }
          }
        }
      }
      if(printingtag_id_array.length == 0){
        return alert("<?php echo $this->translate("You don't select any printing tag entry.Please select at least one.") ?>");
      }          
      var cofirmation = confirm("<?php echo $this->translate("Are you sure you want to delete the selected printing tags?") ?>");
      if(cofirmation != 1){
        return;
      }

      en4.core.request.send(new Request.JSON({
        url : en4.core.baseUrl + 'sitestoreproduct/printing-tag/multidelete-printing-tags',
        method : 'POST',
        onRequest: function(){
          $('delete_selected_shipping_spinner').innerHTML = '<img src='+en4.core.staticBaseUrl+'application/modules/Sitestoreproduct/externals/images/loading.gif height=15 width=15>';
        },
        data : {
          format : 'json',
          tag_id : printingtag_id_array
        },
        onSuccess : function(responseJSON) {
          $('delete_selected_shipping_spinner').innerHTML = '';
          
          if(responseJSON.success == 1){
            window.location.assign('<?php echo $this->url(array('action' => 'store', 'store_id' => $this->store_id, 'type' => 'printing-tag', 'menuId' => 62, 'method' => 'manage'), 'sitestore_store_dashboard', true); ?>');
          }
        }
      })
    ); 
    });  
  });
</script>

<div class="sitestoreproduct_manage_store">
  <h3><?php echo $this->translate('Printing Tags') ?></h3>
  <p class="mbot10"><?php echo $this->translate("Store admins will be able to create unique printing tags for their products by configuring its height, width and position. Each of the created tags can be mapped with the product using 'Submit to Printing Tags' button available on 'Manage Product' page."); ?></p>
  <?php if (!empty($this->canEdit)) : ?>
  <div id="store_back_to_product">
        <a href="javascript:void(0);" onclick="manage_store_dashboard(62, 'manage', 'product');" class="buttonlink icon_previous mbot10 mright5"><?php echo $this->translate('Back to Manage Products Page'); ?></a>
      
    
    <a href="javascript:void(0);" id="tablerate_addlocation" class="buttonlink seaocore_icon_add" onclick='manage_store_dashboard(62, "create", "printing-tag")' ><?php echo $this->translate("Create Printing Tag") ?></a>
  
    </div>
  <?php endif; ?>
</div>
<?php if (!empty($this->addNotice)): ?>
  <?php if ($this->addNotice == 1): ?>
    <ul class="form-notices">
      <li>
        <?php echo $this->translate("Printing tag created successfully."); ?>
      </li>
    </ul>
  <?php else: ?>
    <ul class="form-notices">
      <li>
        <?php echo $this->translate("Changes has been saved successfully."); ?>
      </li>
    </ul>
  <?php endif; ?>
  <div id="manage_order_pagination">  <?php endif; ?>
  <?php if (@count($this->paginator)): ?>
    <div class="mbot5">
      <?php echo $this->translate('%s Printing Tag(s) found.', $this->paginator->getTotalItemCount()) ?>
    </div>
  <?php endif; ?>

  <div id="store_table_rate">
    <?php if (@count($this->paginator)): ?>
      <div class="sitestoreproduct_data_table product_detail_table fleft mbot10">
        <form id='multidelete_form_table_printing_tag' method="post">
          <table class="mbot10">
            <tr class="product_detail_table_head">
              <?php if (!empty($this->canEdit)) : ?>
                <th class='store_table_short'><input onclick='selectAll();' type='checkbox' class='checkbox' /></th>
              <?php endif; ?>
              <th><?php echo $this->translate("Tag Name") ?></th>
              <th><?php echo $this->translate("Size") ?></th>
              <!--<th class="txt_center"><?php //echo $this->translate("No. Of Products") ?></th>-->
              <th class="txt_center"><?php echo $this->translate("Status") ?></th>
              <th><?php echo $this->translate("Options") ?></th>
            </tr>
            <?php foreach ($this->paginator as $item): ?>
              <tr>
                <td><input type='checkbox' class='checkbox' name='delete_<?php echo $item->printingtag_id ?>' value="<?php echo $item->printingtag_id ?>"></td>

                <td title="<?php echo $item->tag_name ?>"><?php echo $this->string()->truncate($this->string()->stripTags($item->tag_name), 140) ?></td>
                <td>
                  <?php echo @round($item->width,2) . " X " . @round($item->height,2) . " cm"; ?>
                </td>
<!--                <td class="txt_center">
                  <?php //if (!empty($item->products)): ?>
                    <a href="javascript:void(0)" onclick="Smoothbox.open('<?php //echo $this->url(array('module' => 'sitestoreproduct', 'controller' => 'printing-tag', 'action' => 'show-products', 'tag_id' => $item->printingtag_id, 'store_id' => $this->store_id)) ?>')">
                      <?php //echo $item->products; ?></a>
                  <?php //else : ?>
                      <?php //echo "-" ?></a>
                    <?php //endif; ?>
                </td>-->
                <!-- SHOWING STATUS BUTTON ACCORDING TO STATUS IN DATABASE-->
                <td class="txt_center">
                  <?php if (!empty($item->status)): ?>
                    <a id="show_status_image_<?php echo $item->printingtag_id ?>" href="javascript:void(0);" onclick="enabletablerate(<?php echo $item->printingtag_id ?>)" title="<?php echo $this->translate("Disable Method") ?>">
                      <img src="<?php echo $this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/images/region_enable1.gif' ?>" />
                    </a>

                  <?php else: ?>

                    <a id="show_status_image_<?php echo $item->printingtag_id ?>" href="javascript:void(0);" onclick="enabletablerate(<?php echo $item->printingtag_id ?>)" title="<?php echo $this->translate("Enable Method") ?>">
                      <img src="<?php echo $this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/images/region_enable0.gif' ?>" />
                    </a>
                  <?php endif; ?>
                </td>
                <td>
                  <?php if (!empty($this->canEdit)) : ?>
                    <a href="javascript:void(0);" onclick='manage_store_dashboard(62, "edit-printing-tag/tag_id/<?php echo $item->printingtag_id; ?>", "printing-tag")' ><?php echo $this->translate("edit") ?></a>
                    | 
                    <a href="javascript:void(0);" onclick="Smoothbox.open('<?php echo $this->url(array('module' => 'sitestoreproduct', 'controller' => 'printing-tag', 'action' => 'delete-printing-tag', 'tag_id' => $item->printingtag_id, 'store_id' => $this->store_id), 'default', false) ?>')"><?php echo $this->translate("delete") ?></a>
                    | 
                    <a href="javascript:void(0);" onclick="Smoothbox.open('<?php echo $this->url(array('module' => 'sitestoreproduct', 'controller' => 'printing-tag', 'action' => 'preview-tag', 'tag_id' => $item->printingtag_id, 'store_id' => $this->store_id), 'default', false) ?>')"><?php echo $this->translate("preview") ?></a>
                  <?php else: ?>
                    -
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </table>
          <?php if (!empty($this->canEdit)) : ?>
            <div class='buttons fleft'>
              <button type='submit' name="submit"><?php echo $this->translate("Delete Selected") ?></button>
              <span id="delete_selected_shipping_spinner"></span>
            </div>
          <?php endif; ?>
          <br/>
        </form> 
      </div>
      <div class="clr dblock sitestoreproduct_data_paging">
        <div id="store_table_rate_previous" class="paginator_previous sitestoreproduct_data_paging_link">
          <?php
          echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
              'onclick' => '',
              'class' => 'buttonlink icon_previous'
          ));
          ?>
          <span id="tablerate_spinner_prev"></span>
        </div>
        <div id="store_table_rate_next" class="paginator_next sitestoreproduct_data_paging_link">
          <span id="tablerate_spinner_next"></span>
          <?php
          echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
              'onclick' => '',
              'class' => 'buttonlink_right icon_next'
          ));
          ?>
        </div>
      </div>
    <?php else: ?>
      <div id="no_location_tip" class="tip">
        <span>
          <?php echo $this->translate("No printing tags available for this store.") ?>        </span>
      </div>
    <?php endif; ?>
  </div>

