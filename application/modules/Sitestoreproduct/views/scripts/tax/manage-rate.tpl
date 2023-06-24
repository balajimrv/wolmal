<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manage-rate.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>
<?php if( empty($this->adminTax) ): ?>
    <h3><?php echo $this->translate('View Tax Details') ?></h3>
<?php else: ?>
    <h3><?php echo $this->translate('Manage Locations') ?></h3>
<?php endif; ?>
<p class="mbot10 mtop10">
  <?php echo $this->translate("Below, you can manage all the locations on which this tax will be applicable."); ?>
</p>

<script type="text/javascript">
var taxRateEnable = 0;
function selectAll()
{
  var i;
  var multidelete_form_tax_rate = $('multidelete_form_tax_rate');
  var inputs = multidelete_form_tax_rate.elements;
  for (i = 1; i < inputs.length; i++) {
    if (!inputs[i].disabled) {
      inputs[i].checked = inputs[0].checked;
    }
  }
}

function showsmoothbox(url) {
  Smoothbox.open(url);
}
    
function enabletaxrate(id){
  if( taxRateEnable == 0 )
  {
    taxRateEnable = 1;
    $('show_taxrate_status_image_' + id).innerHTML = '<img src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitestoreproduct/externals/images/loading.gif" />';
    en4.core.request.send(new Request.JSON({
      url : en4.core.baseUrl + 'sitestoreproduct/tax/taxrate-enable',
      method : 'POST',
      data : {
        format : 'json',
        is_ajax : 1,
        id : id
      },
      onSuccess : function(responseJSON) {                      
        taxRateEnable = 0;
        if( responseJSON.activeFlag == '0') {
          $('show_taxrate_status_image_' + id).innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitestoreproduct/externals/images/region_enable0.gif" />';
        }else{
          $('show_taxrate_status_image_' + id).innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitestoreproduct/externals/images/region_enable1.gif" />';
        }
      }
    })
    ); 
  }   
}
    
en4.core.runonce.add(function(){
<?php if (empty($this->adminTax)) :
        $tempUrl = 'index/page/' . $this->pageNo . '/callingType/1';
      else:
        $tempUrl = 'index/page/' . $this->pageNo;
      endif;
?>
  $('store_back_to_tax').removeEvents('click').addEvent('click', function(){
    manage_store_dashboard(52, '<?php echo $tempUrl; ?>','tax');
  });

  var anchor = $('tax_rate_pagination').getParent();
  document.getElementById('store_tax_rate_previous').style.display = '<?php echo ( $this->paginator->getCurrentPageNumber() == 1 ? 'none' : '' ) ?>';
  $('store_tax_rate_next').style.display = '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' ) ?>';

  $('store_tax_rate_previous').removeEvents('click').addEvent('click', function(){
    $('taxrate_spinner_prev').innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitestoreproduct/externals/images/loading.gif" />';
   var tempTaxPaginationUrl = '<?php echo $this->url(array('action' => 'store', 'store_id' => $this->store_id, 'type' => 'tax', 'menuId' => 52, 'method' => 'manage-rate', 'tax_id' => $this->tax_id, 'page' => $this->paginator->getCurrentPageNumber() - 1, 'pageno' => $this->pageNo ), 'sitestore_store_dashboard', true); ?>';
    if(tempTaxPaginationUrl && typeof history.pushState != 'undefined') { 
      history.pushState( {}, document.title, tempTaxPaginationUrl );
    }

    en4.core.request.send(new Request.HTML({
      url : en4.core.baseUrl + 'sitestoreproduct/tax/manage-rate/tax_id/' + '<?php echo sprintf('%d', $this->tax_id) ?>' + '/pageno/' + '<?php echo $this->pageNo ?>',
      data : {
        format : 'html',
        subject : en4.core.subject.guid,
        store_id : <?php echo sprintf('%d', $this->store_id) ?>,
        page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() - 1) ?>
      },
      onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {        
        $('taxrate_spinner_prev').innerHTML = '';
      }
      }), {
        'element' : anchor
      })
    });

    $('store_tax_rate_next').removeEvents('click').addEvent('click', function(){
      $('taxrate_spinner_next').innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitestoreproduct/externals/images/loading.gif" />';
      var tempTaxPaginationUrl = '<?php echo $this->url(array('action' => 'store', 'store_id' => $this->store_id, 'type' => 'tax', 'menuId' => 52, 'method' => 'manage-rate', 'tax_id' => $this->tax_id, 'page' => $this->paginator->getCurrentPageNumber() + 1, 'pageno' => $this->pageNo ), 'sitestore_store_dashboard', true); ?>';
      if(tempTaxPaginationUrl && typeof history.pushState != 'undefined') { 
        history.pushState( {}, document.title, tempTaxPaginationUrl );
      }

      en4.core.request.send(new Request.HTML({
        url : en4.core.baseUrl + 'sitestoreproduct/tax/manage-rate/tax_id/' + '<?php echo sprintf('%d', $this->tax_id) ?>' + '/pageno/' + '<?php echo $this->pageNo ?>',
        data : {
          format : 'html',
          subject : en4.core.subject.guid,
          store_id : <?php echo sprintf('%d', $this->store_id) ?>,
          page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>
        },
        onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {    
          $('taxrate_spinner_next').innerHTML = '';
        }
        }), {
          'element' : anchor
        })
      });
    
      $('multidelete_form_tax_rate').removeEvents('submit').addEvent('submit', function(e) {
        e.stop();
        var i;
        var taxrate_id_array = new Array();
        var multidelete_form_tax_rate = $('multidelete_form_tax_rate');
        var inputs = multidelete_form_tax_rate.elements;
        for (i = 1; i < inputs.length; i++) {
          if (!inputs[i].disabled) {
            if(inputs[i].checked ){
              if(inputs[i].value){
                taxrate_id_array[i] = inputs[i].value;
              }
            }
          }
        }
        if(taxrate_id_array.length == 0){
          return alert("<?php echo $this->translate("You don't select any tax location entry. Please select at least one.") ?>");
        }          
        var cofirmation = confirm("<?php echo $this->translate("Are you sure you want to delete the selected Tax Location?") ?>");
        if(cofirmation != 1){
          return;
        }

        en4.core.request.send(new Request.JSON({
          url : en4.core.baseUrl + 'sitestoreproduct/tax/multidelete-tax',
          method : 'POST',
          onRequest: function(){
            $('delete_selected_rate_spinner').innerHTML = '<img src='+en4.core.staticBaseUrl+'application/modules/Sitestoreproduct/externals/images/loading.gif height=15 width=15>';
          },
          data : {
            format : 'json',
            method : 1,
            tax_id : taxrate_id_array
          },
          onSuccess : function(responseJSON) {
            $('delete_selected_rate_spinner').innerHTML = '';
            if(responseJSON.success == 1){
              window.location.assign('<?php echo $this->url(array('action' => 'store', 'store_id' => $this->store_id, 'type' => 'tax', 'menuId' => 52, 'method' => 'manage-rate', 'tax_id' => $this->tax_id ), 'sitestore_store_dashboard', true); ?>');
            }
          }
        })
      ); 
    });
  });
</script>
<?php if( empty($this->adminTax) ): ?>
    <?php $tempBackToTaxText =  $this->translate('Back to View Taxes') ?>
<?php else: ?>
    <?php $tempBackToTaxText = $this->translate('Back to Manage Taxes') ?>
<?php endif; ?>
<a id="store_back_to_tax" href="javascript:void(0)" class="buttonlink icon_previous mbot10 mright20"><?php echo $tempBackToTaxText; ?></a>
<?php if (!empty($this->adminTax)): ?>
  <a href="javascript:void(0);" id="tax_addrate" class="buttonlink seaocore_icon_add mbot10 mright20" onclick='showsmoothbox("<?php echo $this->url(array('module' => 'sitestoreproduct', 'controller' => 'tax', 'action' => 'add-rate', 'tax_id' => $this->tax_id, 'store_id' => $this->store_id), 'default', true) ?>");return false;' ><?php echo $this->translate("Add Location") ?></a>
<?php endif; ?>
<div class="mbot5"><h3><?php echo $this->tax_title ?></h3></div>
<div id="tax_rate_pagination"> 
  <?php if (count($this->paginator)): ?>
    <?php if (!empty($this->adminTax)): ?>
      <form id='multidelete_form_tax_rate' method="post">
    <?php endif; ?>
    <div class="product_detail_table sitestoreproduct_data_table fleft">
      <table>
        <tr class="product_detail_table_head">
          <?php if (!empty($this->adminTax)): ?>
            <th><input onclick='selectAll();' type='checkbox' class='checkbox' /></th>
            <th><?php echo $this->translate("Id"); ?></th>
          <?php endif; ?>
          <th><?php echo $this->translate("Country"); ?></th>
          <th><?php echo $this->translate("Regions / States"); ?></th>
          <th><?php echo $this->translate("Price / Rate") ?></th>
          <th><?php echo $this->translate("Creation Date") ?></th>
          <?php if(!empty($this->adminTax)): ?><th class="txt_center"><?php echo $this->translate("Status") ?></th><?php endif; ?>
          <?php if (!empty($this->adminTax)): ?>
            <th><?php echo $this->translate("Options") ?></th>
          <?php endif; ?>
        </tr>
        <?php foreach ($this->paginator as $item): ?>
          <tr>
            <?php if (!empty($this->adminTax)): ?>
              <td><input type='checkbox' class='checkbox' name='delete_<?php echo $item->taxrate_id ?>' value="<?php echo $item->taxrate_id ?>" /></td>
              <td class="admin_table_centered"><?php echo $item->taxrate_id ?></td>
            <?php endif; ?>
            <?php if ($item->country == 'ALL'): ?>
              <td><?php echo $item->country ?></td>
            <?php else: ?>
              <td><?php echo Zend_Locale::getTranslation($item->country, 'country') ?></td>
            <?php endif; ?>
            <?php if ($item->country == 'ALL'): ?>
              <td> - </td>
            <?php elseif ($item->state == 0): ?>
              <td><?php echo $this->translate('ALL') ?></td>
            <?php else: ?>
              <td><?php echo $item->region ?></td>
            <?php endif; ?>
            <?php if (empty($item->handling_type)): ?>
              <td><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($item->tax_value); ?></td>
            <?php else: ?>
             <td><?php echo round($item->tax_value, 2) ?> %</td>
            <?php endif; ?>
            <td><?php echo $this->timestamp(strtotime($item->creation_date)); ?></td>

            <!-- SOWING STATUS BUTTON ACCORDING TO STATUS IN DATABASE-->
            <?php if (!empty($this->adminTax)): ?>
              <?php if (!empty($item->status)): ?>
              <td class="txt_center"><a id="show_taxrate_status_image_<?php echo $item->taxrate_id ?>" href="javascript:void(0);" onclick="enabletaxrate(<?php echo $item->taxrate_id ?>)" title="<?php echo $this->translate("Disable Tax Location") ?>"><img src="<?php echo $this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/images/region_enable1.gif' ?>" /></a></td>
              <?php else: ?>
              <td class="txt_center"><a id="show_taxrate_status_image_<?php echo $item->taxrate_id ?>" href="javascript:void(0);" onclick="enabletaxrate(<?php echo $item->taxrate_id ?>)" title="<?php echo $this->translate("Enable Tax Location") ?>"><img src="<?php echo $this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/images/region_enable0.gif' ?>" /></a></td>
              <?php endif; ?>
            <?php endif; ?>
            <?php if (!empty($this->adminTax)): ?>
              <td><a href="javascript:void(0);" id="tax_editrate" onclick='showsmoothbox("<?php echo $this->url(array('module' => 'sitestoreproduct', 'controller' => 'tax', 'action' => 'edit-rate', 'taxrate_id' => $item->taxrate_id, 'store_id' => $this->store_id, 'tax_id' => $this->tax_id, 'type' => 'edit'), 'default', true) ?>");return false;' ><?php echo $this->translate("edit") ?></a> | <a href="javascript:void(0);" id="tax_editrate" onclick='showsmoothbox("<?php echo $this->url(array('module' => 'sitestoreproduct', 'controller' => 'tax', 'action' => 'delete-rate', 'taxrate_id' => $item->taxrate_id, 'store_id' => $this->store_id), 'default', true) ?>");return false;' ><?php echo $this->translate("delete") ?></a></td>
            <?php endif; ?>
          </tr>
        <?php endforeach; ?>
      </table>
    </div>
    <?php if (!empty($this->adminTax)): ?>
    <div class="buttons">
      <button type='submit'><?php echo $this->translate("Delete Selected") ?></button>
      <span id="delete_selected_rate_spinner"></span>
    </div>
  </form>
  <?php endif; ?>
  <div>
    <div id="store_tax_rate_previous" class="paginator_previous">
      <?php
        echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
            'onclick' => '',
            'class' => 'buttonlink icon_previous'
        ));
        ?>
      <span id="taxrate_spinner_prev"></span> </div>
    <div id="store_tax_rate_next" class="paginator_next"> <span id="taxrate_spinner_next"></span>
      <?php
        echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
            'onclick' => '',
            'class' => 'buttonlink_right icon_next'
        ));
        ?>
    </div>
  </div>
</div>
<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate("No tax location entry added for this tax.") ?>
    </span>
  </div>
<?php endif; ?>
