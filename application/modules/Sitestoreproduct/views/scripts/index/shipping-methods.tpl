<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: shipping-methods.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>
   
<?php if (empty($this->noCountryEnable)) : ?>
  <div id="no_country_tip" class="tip">
    <span>
      <?php echo $this->translate("There are no location configured by site administrator for the shipment.") ?>
    </span>
  </div>
<?php return; endif; ?> 

<script type="text/javascript">
var tableRateEnable = 0;
function selectAll()
{
  var i;
  var multidelete_form_table_rate = $('multidelete_form_table_rate');
  var inputs = multidelete_form_table_rate.elements;
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
        url : en4.core.baseUrl + 'sitestoreproduct/index/shipping-method-enable',
        method : 'POST',
        data : {
          format : 'json',
          store_id : '<?php echo sprintf('%d', $this->store_id) ?>',
          id : id
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
    var tempTableRatePaginationUrl = '<?php echo $this->url(array('action' => 'store', 'store_id' => $this->store_id, 'type' => 'index', 'menuId' => 51, 'method' => 'shipping-methods', 'page' => $this->paginator->getCurrentPageNumber() - 1), 'sitestore_store_dashboard', true); ?>';
    if(tempTableRatePaginationUrl && typeof history.pushState != 'undefined') { 
      history.pushState( {}, document.title, tempTableRatePaginationUrl );
    }

    en4.core.request.send(new Request.HTML({
      url : en4.core.baseUrl + 'sitestoreproduct/index/shipping-methods/store_id/' + <?php echo sprintf('%d', $this->store_id) ?>,
      data : {
        format : 'html',
        subject : en4.core.subject.guid,
        page : '<?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() - 1) ?>',
        call_same_action : 1
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

    var tempTableRatePaginationUrl = '<?php echo $this->url(array('action' => 'store', 'store_id' => $this->store_id, 'type' => 'index', 'menuId' => 51, 'method' => 'shipping-methods', 'page' => $this->paginator->getCurrentPageNumber() + 1), 'sitestore_store_dashboard', true); ?>';
    if(tempTableRatePaginationUrl && typeof history.pushState != 'undefined') { 
      history.pushState( {}, document.title, tempTableRatePaginationUrl );
    }
    en4.core.request.send(new Request.HTML({
      url : en4.core.baseUrl + 'sitestoreproduct/index/shipping-methods/store_id/' + <?php echo sprintf('%d', $this->store_id) ?>,
      data : {
        format : 'html',
        subject : en4.core.subject.guid,
        call_same_action : 1,
        page : '<?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>'
      },
      onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {        
        $('tablerate_spinner_next').innerHTML = '';
      }
      }), {
        'element' : anchor
      })
    });

    $('multidelete_form_table_rate').removeEvents('submit').addEvent('submit', function(e) {
      e.stop();

      var i;
      var shippingmethod_id_array = new Array();
      var multidelete_form_table_rate = $('multidelete_form_table_rate');
      var inputs = multidelete_form_table_rate.elements;
      for (i = 1; i < inputs.length; i++) {
        if (!inputs[i].disabled) {
          if(inputs[i].checked ){
            if(inputs[i].value){
                shippingmethod_id_array[i] = inputs[i].value;
            }
          }
        }
      }
       if(shippingmethod_id_array.length == 0){
          return alert("<?php echo $this->translate("You don't select any shipping method entry.Please select at least one.") ?>");
        }          
      var cofirmation = confirm("<?php echo $this->translate("Are you sure you want to delete the selected shipping methods?") ?>");
      if(cofirmation != 1){
       return;
      }

      en4.core.request.send(new Request.JSON({
        url : en4.core.baseUrl + 'sitestoreproduct/index/multidelete-shipping-methods',
        method : 'POST',
        onRequest: function(){
          $('delete_selected_shipping_spinner').innerHTML = '<img src='+en4.core.staticBaseUrl+'application/modules/Sitestoreproduct/externals/images/loading.gif height=15 width=15>';
        },
        data : {
          format : 'json',
          shippingmethod_id : shippingmethod_id_array
        },
        onSuccess : function(responseJSON) {
          $('delete_selected_shipping_spinner').innerHTML = '';
          if(responseJSON.success == 1){
            window.location.assign('<?php echo $this->url(array('action' => 'store', 'store_id' => $this->store_id, 'type' => 'index', 'menuId' => 51, 'method' => 'shipping-methods'), 'sitestore_store_dashboard', true); ?>');
          }
         }
      })
    ); 
  });  
});
</script>

<?php if(empty($this->call_same_action)) : ?>
  <div class="seaocore_tbs_cont" id="dynamic_menus_content">
    <div class="sitestoreproduct_manage_store">
      <h3><?php echo $this->translate('Shipping Methods') ?></h3>
      <p class="mbot10"><?php echo $this->translate("Manage this store's shipping methods. Buyers will be able to choose a shipping method after adding desired shippable products from your store to their carts.<br/>Shipping methods depend on shipping locations, order cost, quantities ordered and weight of ordered products. From the shipping methods configured by you, a buyer could have multiple methods to choose from depending on the order properties. Buyers will be able to choose a shipping method suitable for them based on delivery time and shipping price."); ?></p>
      <?php if( !empty($this->canEdit) ) : ?>
      <div class="mbot10">
        <a href="javascript:void(0);" id="tablerate_addlocation" class="buttonlink seaocore_icon_add" onclick='manage_store_dashboard(51, "add-shipping-method", "index")' ><?php echo $this->translate("Create Shipping Method") ?></a>&nbsp;
        
        <!--WORK FOR THE MINIMUM SHIPPING COST STARTS-->
        <?php $isMinimumShippingCost = Engine_Api::_()->getApi('settings', 'core')->getSetting( 'sitestore.minimum.shipping.cost', 0)?>
        <?php if($isMinimumShippingCost):?>
        <a href="javascript:void(0);" id="tablerate_addlocation" class="buttonlink seaocore_icon_add" onclick="Smoothbox.open('<?php echo $this->url(array('module' => 'sitestoreproduct', 'controller'=>'product', 'action'=>'set-minimum-shipping-cost', 'store_id' => $this->store_id), 'default' , false) ?>')" ><?php echo $this->translate("Set Minimum Shipping Cost") ?></a>&nbsp;
        <div class="fright">
          <?php $temp_min_ship_cost = Engine_Api::_()->sitestore()->getStoreMinShippingCost($this->store_id); ?>
          <b><?php echo $this->translate('Min. Shipping Cost: %s', Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($temp_min_ship_cost));?></b>
        </div>
        <?php endif;?>
        <!--WORK FOR THE MINIMUM SHIPPING COST ENDS-->
      </div>
      <?php endif; ?>
      
      <?php if( !empty($this->addNotice) ): ?>
        <?php if( $this->addNotice == 1 ): ?>
          <ul class="form-notices">
            <li>
              <?php echo $this->translate("Your shipping method has been successfully created."); ?>
            </li>
          </ul>
        <?php else: ?>
          <ul class="form-notices">
            <li>
              <?php echo $this->translate("Changes has been saved successfully."); ?>
            </li>
          </ul>
        <?php endif; ?>
      <?php endif; ?>
      
      <div id="manage_order_pagination">  <?php endif; ?>
      <?php if (@count($this->paginator)): ?>
        <div class="mbot5">
          <?php echo $this->translate('%s method(s) found.', $this->paginator->getTotalItemCount()) ?>
        </div>
      <?php endif; ?>
      
      <div id="store_table_rate">
        <?php if (@count($this->paginator)): ?>
        <div class="sitestoreproduct_data_table product_detail_table fleft mbot10">
        <form id='multidelete_form_table_rate' method="post">
          <table class="mbot10">
            <tr class="product_detail_table_head">
              <?php if( !empty($this->canEdit) ) : ?>
              <th class='store_table_short'><input onclick='selectAll();' type='checkbox' class='checkbox' /></th>
              <?php endif; ?>
              <th><?php echo $this->translate("Title") ?></th>
              <th><?php echo $this->translate("Country") ?></th>
              <th><?php echo $this->translate("Regions / States") ?></th>
              <th><?php echo $this->translate("Weight Limit") ?></th>
              <th><?php echo $this->translate("Delivery Time") ?></th>                
              <th><?php echo $this->translate("Dependency") ?></th>
              <th><?php echo $this->translate("Limit") ?></th>
              <th><?php echo $this->translate("Charge on") ?></th>
              <th><?php echo $this->translate("Price / Rate") ?></th>
              <th><?php echo $this->translate("Status") ?></th>
              <th><?php echo $this->translate("Creation Date") ?></th>                
              <th><?php echo $this->translate("Options") ?></th>
            </tr>
            <?php foreach ($this->paginator as $item): ?>
              <tr>
                <?php if( !empty($this->canEdit) ) : ?>
                <td><input type='checkbox' class='checkbox' name='delete_<?php echo $item->shippingmethod_id ?>' value="<?php echo $item->shippingmethod_id ?>"></td>
                <?php endif; ?>
                <td title="<?php echo $item->title ?>"><?php echo $this->string()->truncate($this->string()->stripTags($item->title), 10) ?></td>
                <td><?php echo ($item->country != 'ALL' ? Zend_Locale::getTranslation($item->country, 'country') : $this->translate('All')); ?></td>
                <td><?php echo ($item->country != 'ALL' ? (empty($item->region) ? $this->translate('All') : $item->region_name) : '-') ?></td>                  
                <td>
                  <?php if($item->dependency == 1): ?>   
                    <?php echo ' - ' ?>
                  <?php else : ?>
                    <?php echo @round($item->allow_weight_from,2) . ' - ' . (!empty($item->allow_weight_to) ? round($item->allow_weight_to,2) . " $this->weightUnit" : $this->translate('NA')) ?>
                  <?php endif; ?>
                </td>
                <td title="<?php echo $item->delivery_time; ?>">
                  <?php echo Engine_Api::_()->sitestoreproduct()->truncation($item->delivery_time, 13); ?>
                </td>
                <td>
                  <?php if ($item->dependency == 0): ?>
                    <?php echo $this->translate("Cost") ?>
                  <?php elseif($item->dependency == 1): ?>
                    <?php echo $this->translate("Weight") ?>
                  <?php else : ?>
                    <?php echo $this->translate("Quantity") ?>
                  <?php endif; ?>
                </td>
                <td>
                  <?php if($item->dependency == 0): ?>   
                    <?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($item->ship_start_limit) . ' - ' .  (!empty($item->ship_end_limit) ? Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($item->ship_end_limit) : $this->translate('NA')) ?>
                  <?php elseif($item->dependency == 1): ?>
                    <?php echo round($item->ship_start_limit, 2) . ' - ' .  (!empty($item->ship_end_limit) ? round($item->ship_end_limit, 2) . " $this->weightUnit" : $this->translate('NA')) ?>
                  <?php else : ?>
                    <?php echo $item->ship_start_limit . ' - ' . (!empty($item->ship_end_limit) ? $item->ship_end_limit : $this->translate('NA')) ?>
                  <?php endif; ?>
                </td>
                <td>
                  <?php if($item->dependency == 1): ?>
                    <?php if ($item->ship_type == 0): ?>
                      <?php echo $this->translate("Order Weight") ?>
                    <?php else: ?>
                      <?php echo $this->translate("Per Unit Weight") ?>
                    <?php endif; ?>
                  <?php else : ?>
                    <?php if ($item->ship_type == 0): ?>                   
                      <?php echo $this->translate("Per Order") ?>
                    <?php else: ?>
                      <?php echo $this->translate("Per Item") ?>
                    <?php endif; ?>
                  <?php endif; ?>
                </td>
                <td>
                  <?php if ($item->handling_type == 0): ?>
                    <?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($item->handling_fee); ?>
                  <?php else: ?>
                    <?php echo @round($item->handling_fee, 2) ?>%
                  <?php endif; ?>
                </td>
                <!-- SHOWING STATUS BUTTON ACCORDING TO STATUS IN DATABASE-->
                <td class="txt_center">
                  <?php if (!empty($item->status)): ?>
                    <?php if( !empty($this->canEdit) ) : ?>
                    <a id="show_status_image_<?php echo $item->shippingmethod_id ?>" href="javascript:void(0);" 
                       <?php if($isMinimumShippingCost && $item->handling_type == 0):?>
                        onclick="alert(en4.core.language.translate('This shipping method cannot be enabled disabled from here. Please try enable/disable by editing the shipping method.'));"
                       <?php else:?>
                        onclick="enabletablerate(<?php echo $item->shippingmethod_id ?>)"
                       <?php endif;?>
                       title="<?php echo $this->translate("Disable Method") ?>">
                    <?php endif; ?>
                      <img src="<?php echo $this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/images/region_enable1.gif' ?>" />
                    <?php if( !empty($this->canEdit) ) : ?>
                    </a>
                    <?php endif; ?>
                  <?php else: ?>
                    <?php if( !empty($this->canEdit) ) : ?>
                    <a id="show_status_image_<?php echo $item->shippingmethod_id ?>" href="javascript:void(0);"
                       <?php if($isMinimumShippingCost && $item->handling_type == 0):?>
                        onclick="alert(en4.core.language.translate('This shipping method cannot be enabled/disabled from here. Please try enable/disable by editing the shipping method.'));"
                       <?php else:?>
                        onclick="enabletablerate(<?php echo $item->shippingmethod_id ?>)"
                       <?php endif;?>
                       
                       title="<?php echo $this->translate("Enable Method") ?>">
                    <?php endif; ?>
                      <img src="<?php echo $this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/images/region_enable0.gif' ?>" />
                    <?php if( !empty($this->canEdit) ) : ?>
                    </a>
                    <?php endif; ?>
                  <?php endif; ?>
                </td>
                <td><?php echo gmdate('M d,Y, g:i A',strtotime($item->creation_date)); ?></td>
                <td>
                  <?php if( !empty($this->canEdit) ) : ?>
                  <a href="javascript:void(0);" onclick='manage_store_dashboard(51, "edit-shipping-method/method_id/<?php echo $item->shippingmethod_id; ?>", "index")' ><?php echo $this->translate("edit") ?></a>
                  | 
                  <a href="javascript:void(0);" onclick='showsmoothbox("<?php echo $this->url(array('action' => 'delete-shipping-method', 'id' => $item->shippingmethod_id, 'store_id' => $this->store_id), 'sitestoreproduct_general', true) ?>");return false;' ><?php echo $this->translate("delete") ?></a>
                  <?php else: ?>
                  -
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </table>
          <?php if( !empty($this->canEdit) ) : ?>
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
      <?php echo $this->translate("No shipping methods have been configured yet for this store.") ?>        
        </span>
      </div>
        <?php endif; ?>
    <?php if(empty($this->call_same_action)) : ?>
	</div>
</div>
<?php endif; ?>
