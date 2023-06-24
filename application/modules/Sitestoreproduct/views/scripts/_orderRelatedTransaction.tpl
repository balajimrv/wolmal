<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _orderRelatedTransaction.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<?php if(empty($this->call_same_action)) : ?>
<div class="clr">
  <h4><?php echo $this->translate('Order Related Transactions') ?></h4>
  <p class="mbot10"><?php echo $this->translate("Browse through the transactions made by users for products of this store. The search box below will search through the buyer names, date, order total and payment gateway."); ?></p>
    <div class="seaocore_searchform_criteria seaocore_searchform_criteria_horizontal">
      <form method="post" class="field_search_criteria" id="filter_form">
        <div>
          <ul>
            <li>
              <span><label><?php echo  $this->translate("Buyer Name") ?></label></span>
              <input type="text" name="username" id="username"/> 
            </li>      
            <li id="integer-wrapper">
              <?php 
                //MAKE THE STARTTIME AND ENDTIME FILTER
                $starttime = $this->locale()->toDateTime(time());
                $attributes = array();
                $attributes['dateFormat'] = $this->locale()->useDateLocaleFormat(); //'ymd';
                $attributes['is_ajax'] = 1;

                $form = new Engine_Form_Element_CalendarDateTime('starttime');
                $attributes['options'] = $form->getMultiOptions();
                $attributes['id'] = 'starttime';

                if( !empty($this->starttime) ) :
                  $attributes['starttimeDate'] = $this->starttime;
                endif;

                echo '<span>'.$this->translate('Duration').'</span>';
                echo '<div class="form-element">';
                echo $this->formCalendarDateTimeElement('starttime', $starttime, array_merge(array('label' => $this->translate('From')), $attributes), $attributes['options'] );
                if( !empty($this->endtime) ) :
                  $attributes['endtimeDate'] = $this->endtime;
                endif;
                $attributes['starttimeDate'] = '';
                echo $this->formCalendarDateTimeElement('endtime', $starttime, array_merge(array('label' => $this->translate('To')), $attributes), $attributes['options'] );
                echo '</div>';
              ?>
            </li>
            <li id="integer-wrapper">
              <label><?php echo  $this->translate("Order Total") ?></label>
              <div class="form-element">
                <input type="text" name="order_min_amount" id="order_min_amount" placeholder="min"/>
              </div>
              <div class="form-element">
                <input type="text" name="order_max_amount" id="order_max_amount" placeholder="max"/> 	      
              </div>
            </li>
            <?php if( !empty($this->storeEnabledgateway) ) : ?>
            <li id="integer-wrapper">
              <label><?php echo  $this->translate("Payment Gateway") ?></label>
              <div class="form-element">
                <select id="gateway" name="gateway">
                  <option value="0"></option>
                  <?php foreach($this->storeEnabledgateway as $key => $gateway) : ?>
                    <?php if( $key == 'paypal' ) : ?>
                      <?php $gatewayName = $this->translate('PayPal'); ?>
                      <?php $gatewayValue = 2; ?>
                    <?php elseif( $key == 'cheque' ) : ?>
                      <?php $gatewayName = $this->translate('By Cheque'); ?>
                      <?php $gatewayValue = 3; ?>
                    <?php elseif( $key == 'cod' ) : ?>
                      <?php $gatewayName = $this->translate('Cash on Delivery'); ?>
                      <?php $gatewayValue = 4; ?>
                    <?php elseif (Engine_Api::_()->hasModuleBootstrap('sitegateway')) : ?>
                        <?php $gateway = Engine_Api::_()->sitegateway()->getGatewayColumn(array('fetchRow' => true, 'plugin' => 'Sitegateway_Plugin_Gateway_'.ucfirst($key)));?>
                        <?php $gatewayName = $this->translate($gateway->title); ?>
                        <?php $gatewayValue =  $gateway->gateway_id; ?>
                    <?php endif; ?>
                    <option id="<?php echo $key ?>" value="<?php echo $gatewayValue ?>"><?php echo $gatewayName ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </li>
            <?php endif; ?>
            <li class="clear mtop10">
              <button type="submit" name="search" ><?php echo $this->translate("Search") ?></button>        
            </li>
            <li>
              <span id="search_spinner"></span>
            </li>
          </ul>
    		</div>
      </form>
    </div>


  <div id="manage_order_pagination">  <?php endif; ?>
    <?php if($paginationCount): ?>
      <div class="mbot5">
        <?php echo $this->translate('%s transaction(s) found.', $this->total_item) ?>
      </div>
    <?php endif; ?>
    <div id="manage_order_tab">
      <?php if($paginationCount): ?>
       <div class="product_detail_table sitestoreproduct_data_table fleft mbot10">
          <table>
            <tr class="product_detail_table_head">
              <th class="txt_center"><?php echo $this->translate('Id') ?></th>
              <th class="txt_center"><?php echo $this->translate('Order Id') ?></th>
              <th><?php echo $this->translate('Buyer') ?></th>
              <th><?php echo $this->translate('Gateway') ?></th>
              <th class="txt_center"><?php echo $this->translate('Payment') ?></th>
              <th class="txt_right"><?php echo $this->translate('Order Total') ?></th>
              <th><?php echo $this->translate('Date') ?></th>
              <th><?php echo $this->translate('Options') ?></th>
            </tr>	
            <?php foreach( $this->paginator as $item ): ?>
            <tr>
              <td><?php echo $item->transaction_id; ?></td>
              <td><a href="javascript:void(0)" onclick="manage_store_dashboard(55, 'order-view/order_id/<?php echo $item->order_id; ?>', 'index')"><?php echo "#" . $item->order_id; ?></a></td>
              <td>
                <?php if( empty($item->buyer_id) ) : ?>
                  <?php echo $this->translate('Guest') ?>
                <?php else: ?>
                  <?php $user = Engine_Api::_()->getItem('user', $item->buyer_id); ?>
                  <?php echo $this->htmlLink($user->getHref(), $user->getTitle()) ?>
                <?php endif; ?>
              </td>
              <td>
                <?php $paymentMethod = Engine_Api::_()->sitestoreproduct()->getGatwayName($item->gateway_id); echo $this->translate($paymentMethod);?>
              </td>
              <td>
                <?php if( $item->payment_status != 'active' ): ?>
                  <?php echo $this->translate("No") ?>
                <?php else: ?>
                  <?php echo $this->translate("Yes") ?>
                <?php endif; ?>
              </td>
              <td class="txt_right"><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($item->grand_total); ?></td>
              <td><?php echo gmdate('M d,Y, g:i A',strtotime($item->date)); ?></td>
              <td>
                <a href="javascript:void(0)" onclick="Smoothbox.open('<?php echo $this->url(array('module' => 'sitestoreproduct', 'controller' => 'product', 'action' => 'view-order-transaction-detail', 'grand_total' => $item->grand_total, 'store_id' =>$this->store_id, 'transaction_id' => $item->transaction_id, 'order_id' => $item->order_id, 'payment_gateway' => $item->gateway_id, 'payment_type' => $item->type, 'payment_state' => $item->state, 'date' => $item->date, 'gateway_transaction_id' => $item->gateway_transaction_id), 'default', true) ?>')"><?php echo $this->translate("details") ?></a>
              </td>
            </tr>
            <?php endforeach; ?>
          </table>
        </div>
    </div>
    <div class="clr dblock sitestoreproduct_data_paging">
      <div id="store_manage_order_previous" class="paginator_previous sitestoreproduct_data_paging_link">
        <?php
              echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
                  'onclick' => '',
                  'class' => 'buttonlink icon_previous'
              ));
        ?>
        <span id="manage_spinner_prev"></span>
      </div>
      
      <div id="store_manage_order_next" class="paginator_next sitestoreproduct_data_paging_link">
         <span id="manage_spinner_next"></span>
        <?php
              echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
                  'onclick' => '',
                  'class' => 'buttonlink_right icon_next'
              ));
        ?>
      </div>
    
    <?php else: ?>
      <div class="tip"><span>
      <?php echo $this->translate('There are no order transactions available yet.') ?>
        </span></div>
    <?php endif; ?>
    </div>
    <?php if(empty($this->call_same_action)) : ?>
  </div>
</div>
<?php endif; ?>

<script type="text/javascript">

en4.core.runonce.add(function(){

initializeCalendar(); 

<?php if( !empty($this->newOrderStatus) ) : ?>
  document.getElementById('order_status').selectedIndex = <?php echo $this->newOrderStatus ?>;
<?php endif; ?>    
var anchor = document.getElementById('manage_order_tab').getParent();
<?php if($paginationCount): ?>
  document.getElementById('store_manage_order_previous').style.display = '<?php echo ( $this->paginator->getCurrentPageNumber() == 1 ? 'none' : '' ) ?>';
  $('store_manage_order_next').style.display = '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' ) ?>';
  $('store_manage_order_previous').removeEvents('click').addEvent('click', function(){
    $('manage_spinner_prev').innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitestoreproduct/externals/images/loading.gif" />';
    var tempManagePaginationUrl = '<?php echo $this->url(array('action' => 'store', 'store_id' => $this->store_id, 'type' => 'index', 'menuId' => 55, 'method' => 'manage-order', 'page' => $this->paginator->getCurrentPageNumber() - 1), 'sitestore_store_dashboard', true); ?>';
    if(tempManagePaginationUrl && typeof history.pushState != 'undefined') { 
      history.pushState( {}, document.title, tempManagePaginationUrl );
    }
    en4.core.request.send(new Request.HTML({
      url : en4.core.baseUrl + 'sitestoreproduct/index/manage-order/store_id/' + <?php echo sprintf('%d', $this->store_id) ?>,
      data : {
        format : 'html',
        search : 1,
            subject : en4.core.subject.guid,
            call_same_action : 1,
            username : $('username').value,
            order_min_amount : $('order_min_amount').value,
            order_max_amount : $('order_max_amount').value,
            gateway : $('gateway').value,
            starttime : $('calendar_output_span_starttime-date').innerHTML,
            endtime : $('calendar_output_span_endtime-date').innerHTML,
            store_id : <?php echo sprintf('%d', $this->store_id) ?>,           
        page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() - 1) ?>
      },
       onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {        
          $('manage_spinner_prev').innerHTML = '';
        }
      }), {
        'element' : anchor
      })
    });

    $('store_manage_order_next').removeEvents('click').addEvent('click', function(){
      $('manage_spinner_next').innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitestoreproduct/externals/images/loading.gif" />';
      var tempManagePaginationUrl = '<?php echo $this->url(array('action' => 'store', 'store_id' => $this->store_id, 'type' => 'index', 'menuId' => 55, 'method' => 'manage-order', 'page' => $this->paginator->getCurrentPageNumber() + 1), 'sitestore_store_dashboard', true); ?>';
      if(tempManagePaginationUrl && typeof history.pushState != 'undefined') { 
        history.pushState( {}, document.title, tempManagePaginationUrl );
      }

      en4.core.request.send(new Request.HTML({
        url : en4.core.baseUrl + 'sitestoreproduct/index/manage-order/store_id/' + <?php echo sprintf('%d', $this->store_id) ?>,
        data : {
          format : 'html',
          search : 1,
              subject : en4.core.subject.guid,
              call_same_action : 1,
              username : $('username').value,
              order_min_amount : $('order_min_amount').value,
              order_max_amount : $('order_max_amount').value,
              gateway : $('gateway').value,
              starttime : $('calendar_output_span_starttime-date').innerHTML,
              endtime : $('calendar_output_span_endtime-date').innerHTML,
              store_id : <?php echo sprintf('%d', $this->store_id) ?>,
          page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>
        },
         onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {        
            $('manage_spinner_next').innerHTML = '';

          }
      }), {
        'element' : anchor
    })
  });
  <?php endif; ?>

  $('filter_form').removeEvents('submit').addEvent('submit', function(e) {
    e.stop();
    $('search_spinner').innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitestoreproduct/externals/images/loading.gif" />';

    en4.core.request.send(new Request.HTML({
      url : en4.core.baseUrl + "sitestoreproduct/product/store-transaction/tab/<?php echo $this->tab ?>",
      method : 'POST',
      data : {
        search : 1,
        subject : en4.core.subject.guid,
        call_same_action : 1,
        username : $('username').value,
        order_min_amount : $('order_min_amount').value,
        order_max_amount : $('order_max_amount').value,
        gateway : $('gateway').value,
        starttime : $('calendar_output_span_starttime-date').innerHTML,
        endtime : $('calendar_output_span_endtime-date').innerHTML,
        store_id : <?php echo sprintf('%d', $this->store_id) ?>
      },
      onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
              $('search_spinner').innerHTML = '';                    
      }
    }), {
      'element' : anchor
    })
  });
});
</script>