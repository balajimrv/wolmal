<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: transaction.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>
   
<?php $paginationCount = count($this->paginator); ?>
<?php if(empty($this->call_same_action)) : ?>
<div class="sitestoreproduct_manage_store sitestoreproduct_Transactions">
  <h3 class="mbot10">
    <?php echo $this->translate('Transactions') ?>
  </h3>
  <p class="mbot10">
    <?php echo $this->translate("Browse through the transactions made by our site administrators to make payments in response to your payment requests. The search box below will search through the transaction date, response amount and state. You can also use the filters below to filter the transactions."); ?>
  </p>
  <div class="seaocore_searchform_criteria seaocore_searchform_criteria_horizontal">
    <form method="post" class="field_search_criteria" id="filter_form">
      <div>
        <ul>
          <li>
            <span>
              <label>
                <?php echo  $this->translate("Transaction Date : ex (2000-12-25)") ?>
              </label>
            </span>
            <input type="text" name="date" id="date" />
          </li>  

          <li id="integer-wrapper">
            <label>
              <?php echo  $this->translate("Response Amount") ?>
            </label>
            <div class="form-element">
              <input type="text" name="response_min_amount" id="response_min_amount" placeholder="min"/>
            </div>
            <div class="form-element">
              <input type="text" name="response_max_amount" id="response_max_amount" placeholder="max"/>
            </div>
          </li>

          <?php if( !empty($this->transaction_state) ) : ?>
            <li>
              <span>
                <label>
                  <?php echo  $this->translate("State") ?>	
                </label>
              </span>
              <select id="state" name="state">
                <option value="0" ></option>
                  <?php foreach( $this->transaction_state as $state ) : ?>
                    <option value="<?php echo $state ?>" <?php if( $this->state == "$state" ) echo "selected";?> ><?php echo $this->translate("%s", ucfirst($state)) ?></option>
                  <?php endforeach; ?>
               </select>
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


  <div id="transaction_pagination">
    <?php endif; ?>
    <?php if($paginationCount): ?>
    <div class="mbot5">
          <?php echo $this->translate('%s record(s) found.', $this->total_item) ?>
      </div>
    <?php endif; ?>
    <div id="manage_transaction_tab">
      <?php if($paginationCount): ?>
        <div class="product_detail_table sitestoreproduct_data_table fleft">
          <table>
            <tr class="product_detail_table_head">
              <th><?php echo $this->translate('Request Id') ?> </th>
              <th><?php echo $this->translate('Gateway') ?></th>
              <th><?php echo $this->translate('Type') ?></th>
              <th><?php echo $this->translate('State') ?></th>
              <th><?php echo $this->translate('Response Amount') ?></th>
              <th><?php echo $this->translate('Date') ?></th>
              <th><?php echo $this->translate('Options') ?></th>
            </tr>
            <?php 
              foreach( $this->paginator as $store_transaction):
                if( $store_transaction['gateway_id'] == 3) :
                  $store_transaction['state'] = '-';
                endif;
                $payment_gateway = Engine_Api::_()->sitestoreproduct()->getGatwayName($store_transaction['gateway_id']);
                $detail_url = $this->url(array(
                                                "action" => "view-transaction-detail",
                                                "store_id" => $this->store_id,
                                                "request_id" => $store_transaction['request_id'],
                                                "transaction_id" => $store_transaction['transaction_id'],
                                                "payment_gateway" => $payment_gateway,
                                                "payment_type" => $store_transaction['type'],
                                                "payment_state" => $store_transaction['state'],
                                                "response_amount" => $store_transaction['response_amount'],
                                                "response_date" => $store_transaction['response_date'],
                                                "gateway_transaction_id" => $store_transaction['gateway_profile_id'],
                                              ), "sitestoreproduct_product_general", true);
            ?>
            <tr>
              <td><?php echo $store_transaction['request_id'] ?></td>
              <td><?php echo $payment_gateway ?></td>
              <td><?php echo $store_transaction['type'] ?></td>
              <td><?php echo $store_transaction['state'] ?></td>
              <td><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($store_transaction['response_amount']);?></td>
              <td><?php echo gmdate('M d,Y, g:i A',strtotime($store_transaction['response_date'])) ?></td>
              <td><a href="javascript:void(0)" onclick="Smoothbox.open('<?php echo $detail_url; ?>')"><?php echo $this->translate("details") ?></a></td>
            </tr>
            <?php endforeach;  ?>
          </table>
        </div>
  
        <div>
          <div id="store_transaction_previous" class="paginator_previous">
            <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array('onclick' => '', 'class' => 'buttonlink icon_previous')); ?>
            <span id="transaction_spinner_prev"></span>
          </div>

          <div id="store_transaction_next" class="paginator_next">
             <span id="transaction_spinner_next"></span>
             <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array('onclick' => '', 'class' => 'buttonlink_right icon_next'));?>
          </div>
        </div>
      <?php else : ?>
        <div class="tip">
          <span>
            <?php echo $this->translate('There are no transaction available yet.') ?>
          </span>
        </div>
      <?php endif; ?>
    </div>
    <?php if(empty($this->call_same_action)) : ?>
  </div>
</div>
<?php endif; ?>

<script type="text/javascript">
en4.core.runonce.add(function(){
    
  var anchor = document.getElementById('manage_transaction_tab').getParent();
<?php if($paginationCount): ?>
  document.getElementById('store_transaction_previous').style.display = '<?php echo ( $this->paginator->getCurrentPageNumber() == 1 ? 'none' : '' ) ?>';
  $('store_transaction_next').style.display = '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' ) ?>';

  $('store_transaction_previous').removeEvents('click').addEvent('click', function(){
     $('transaction_spinner_prev').innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitestoreproduct/externals/images/loading.gif" />';

  var tempTransactionPaginationUrl = '<?php echo $this->url(array('action' => 'store', 'store_id' => $this->store_id, 'type' => 'product', 'menuId' => 54, 'method' => 'transaction', 'page' => $this->paginator->getCurrentPageNumber() - 1), 'sitestore_store_dashboard', true); ?>';

  if(tempTransactionPaginationUrl && typeof history.pushState != 'undefined') { 
    history.pushState( {}, document.title, tempTransactionPaginationUrl );
  }

  en4.core.request.send(new Request.HTML({
    url : en4.core.baseUrl + 'sitestoreproduct/product/transaction/store_id/' + <?php echo sprintf('%d', $this->store_id) ?>,
    data : {
      format : 'html',
      subject : en4.core.subject.guid,
      call_same_action : 1,
      search : 1,
          date : $('date').value,
          response_min_amount : $('response_min_amount').value,
          response_max_amount : $('response_max_amount').value,
          state : $('state').value,
          store_id : <?php echo sprintf('%d', $this->store_id) ?>,
      page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() - 1) ?>
    },
     onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {        
        $('transaction_spinner_prev').innerHTML = '';
      }
    }), {
      'element' : anchor
    })
  });

  $('store_transaction_next').removeEvents('click').addEvent('click', function(){
   $('transaction_spinner_next').innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitestoreproduct/externals/images/loading.gif" />';

  var tempTransactionPaginationUrl = '<?php echo $this->url(array('action' => 'store', 'store_id' => $this->store_id, 'type' => 'product', 'menuId' => 54, 'method' => 'transaction', 'page' => $this->paginator->getCurrentPageNumber() + 1), 'sitestore_store_dashboard', true); ?>';

  if(tempTransactionPaginationUrl && typeof history.pushState != 'undefined') { 
    history.pushState( {}, document.title, tempTransactionPaginationUrl );
  }

  en4.core.request.send(new Request.HTML({
    url : en4.core.baseUrl + 'sitestoreproduct/product/transaction/store_id/' + <?php echo sprintf('%d', $this->store_id) ?>,
    data : {
      format : 'html',
      subject : en4.core.subject.guid,
      call_same_action : 1,
      search : 1,
          date : $('date').value,
          response_min_amount : $('response_min_amount').value,
          response_max_amount : $('response_max_amount').value,
          state : $('state').value,
          store_id : <?php echo sprintf('%d', $this->store_id) ?>,
      page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>
    },
     onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {        
        $('transaction_spinner_next').innerHTML = '';
      }
    }), {
      'element' : anchor
  })
  });
<?php endif; ?>
  
   $('filter_form').removeEvents('submit').addEvent('submit', function(e) {
    e.stop();
    $('search_spinner').innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitestoreproduct/externals/images/loading.gif" />';

    var stateValue = '';
    if($('state'))
      stateValue = $('state').value;

    en4.core.request.send(new Request.HTML({
      url : en4.core.baseUrl + 'sitestoreproduct/product/transaction',
      method : 'POST',
      data : {
        search : 1,
        subject : en4.core.subject.guid,
        call_same_action : 1,
        date : $('date').value,
        response_min_amount : $('response_min_amount').value,
        response_max_amount : $('response_max_amount').value,
        state : stateValue,
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