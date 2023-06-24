<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: payment-to-me.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
$user_max_requested_amount = @round($this->total_amount + $this->remaining_amount, 2);
$countPagination = @count($this->paginator);

if ($this->minimum_requested_amount < $user_max_requested_amount):
  $class = "highlight";
else:
  $class = "";
endif;
?>
<?php if (empty($this->call_same_action)) : ?>
  <div class="sitestoreproduct_manage_store sitestoreproduct_payment_to_me">
    <h3><?php echo $this->translate('Payment to me') ?></h3>
    <p class="mbot10">
            <?php if(Engine_Api::_()->hasModuleBootstrap('sitegateway') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegateway.stripeconnect', 0)): ?>
                <?php echo $this->translate("Below, you can view the history of payment requests made by you and can also make a new payment request after your 'Balance Amount' exceeds the Threshold Amount. All amounts below exclude general taxes and commissions. [The payments made using 'Stripe Connect' gateway are not added here because those payments are already transferred into your connected account at the time of order placed.]"); ?>
            <?php else: ?>
                <?php echo $this->translate('Below, you can view the history of payment requests made by you and can also make a new payment request after your "Balance Amount" exceeds the Threshold Amount. All amounts below exclude general taxes and commissions.'); ?>
            <?php endif; ?>           
    </p>
    <p><strong><?php echo $this->translate('Threshold Amount: %s', Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($this->threshold_amount)) ?></strong></p><br/>

    <table class="sitestoreproduct_amount_table sitestoreproduct_data_table">
      <tr>
        <td class="<?php echo $class ?> txt_center">
          <span><?php echo $this->translate('Balance Amount [A = B+C]') ?></span>
          <span class="txt_center bold f_small dblock"><?php echo $this->translate('(Pending to be requested)') ?></span>
          <div class="txt_center bold"><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($user_max_requested_amount); ?></div>
        </td>
        <td class="txt_center">
          <span><?php echo $this->translate('New Sales [B]') ?></span>
          <span class="txt_center bold f_small dblock"><?php echo $this->translate('(Since last payment request)'); ?></span>
          <div class="txt_center bold"><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($this->total_amount); ?></div>
        </td>
        <td class="txt_center">
          <span><?php echo $this->translate('Remaining Amount [C]') ?></span>
          <div class="txt_center bold"><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($this->remaining_amount) ?></div>
        </td>
        <td class="txt_center">
          <span><?php echo $this->translate('Pending Requested Amount') ?></span>
          <div class="txt_center bold"><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($this->requesting_amount) ?></div>
        </td>
      </tr> 
    </table>
   
    <div class="clr mbot10">
      <a class="buttonlink sitestoreproduct_icon_payment" href="javascript:void(0)" onClick="Smoothbox.open('sitestoreproduct/product/payment-request/store_id/<?php echo $this->store_id ?>/order_count/<?php echo $this->order_count ?>');"><?php echo $this->translate("Request for Payment") ?></a>
      <br/>
        <?php         
        if(!$this->enablePaymentRequest) { ?>
        <div class="tip">
        <?php
           echo $this->translate("* Please First provide credentials of Payment method enabled for payment request. ") ?>
           </div>
        <?php }  ?>
    </div>  


    <div class="seaocore_searchform_criteria seaocore_searchform_criteria_horizontal">
      <form method="post" class="field_search_criteria" id="filter_form">
        <div>
          <ul>
            <li>
              <span>
                <label>
                  <?php echo $this->translate("Request Date : ex (2000-12-25)") ?>
                </label>
              </span>
              <input type="text" name="request_date" id="request_date" />
            </li>   
            <li>
              <span>
                <label>
                  <?php echo $this->translate("Response Date : ex (2000-12-25)") ?>
                </label>
              </span>
              <input type="text" name="response_date" id="response_date" />
            </li>
            <li id="integer-wrapper">
              <label>
                <?php echo $this->translate("Requested Amount") ?>
              </label>
              <div class="form-element">
                <input type="text" name="request_min_amount" id="request_min_amount" placeholder="min"/>	      
              </div>
              <div class="form-element">
                <input type="text" name="request_max_amount" id="request_max_amount" placeholder="max"/> 	      
              </div>
            </li>
            <li id="integer-wrapper">
              <label>
                <?php echo $this->translate("Response Amount") ?>
              </label>
              <div class="form-element">
                <input type="text" name="response_min_amount" id="response_min_amount" placeholder="min"/>
              </div>
              <div class="form-element">
                <input type="text" name="response_max_amount" id="response_max_amount" placeholder="max"/> 	      
              </div>
            </li>
            <li>
              <span>
                <label>
                  <?php echo $this->translate("Status") ?>	
                </label>
              </span>
              <select id="request_status" name="request_status" >
                <option value="0" ></option>
                <option value="1" ><?php echo $this->translate("Requesting") ?></option>
                <option value="3" ><?php echo $this->translate("Completed") ?></option>
                <option value="2" ><?php echo $this->translate("Canceled") ?></option>
              </select>
            </li>
            <li class="clear mtop10">
              <button type="submit" name="search" ><?php echo $this->translate("Search") ?></button> 
              <span id="search_spinner" style="float: right;"></span>
            </li>
          </ul>
        </div>
      </form>
    </div>

    <div id="payment_request_table">
    <?php endif; ?>
    <?php if ($countPagination): ?>
      <div><span><?php echo $this->translate('%s request(s) found.', $this->total_item) ?></span></div>
    <?php endif; ?>
    <div id="manage_order_tab">

      <?php if ($countPagination): ?>
        <div class="product_detail_table sitestoreproduct_data_table fleft">
          <table>
            <tr class="product_detail_table_head">
              <th><?php echo $this->translate("Request Id") ?></th>
              <th><?php echo $this->translate("Requested Amount") ?></th>
              <th><?php echo $this->translate("Request Date") ?></th>
              <th><?php echo $this->translate("Request Message") ?></th>
              <th><?php echo $this->translate("Response Amount") ?></th>
              <th><?php echo $this->translate("Response Date") ?></th>
              <th><?php echo $this->translate("Response Messages") ?></th>
              <th><?php echo $this->translate("Remaining Amount") ?></th>
              <th><?php echo $this->translate("Status") ?></th>
              <th><?php echo $this->translate("Payment") ?></th>
              <th><?php echo $this->translate("Options") ?></th>
            </tr>
            <?php
            foreach ($this->paginator as $payment) :
              if ($payment->request_status == 0):
                $request_status = 'Requested';
              elseif ($payment->request_status == 1):
                $request_status = '<i><font color="red">Deleted</font></i>';
              elseif ($payment->request_status == 2):
                $request_status = '<i><font color="green">Completed</font></i>';
              endif;

              if ($payment->payment_status != 'active'):
                $payment_status = 'No';
              else:
                $payment_status = 'Yes';
              endif;
              ?>        
              <tr>
                <td><?php echo $payment->request_id ?></td>
                <td><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($payment->request_amount) ?></td>
                <td><?php echo $payment->request_date ?></td>
                <td><?php echo $this->string()->truncate($this->string()->stripTags(empty($payment->request_message) ? '-' : $payment->request_message), 30) ?></td>
                <td><?php echo empty($payment->response_amount) ? '-' : Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($payment->response_amount) ?></td>
                <td><?php echo empty($payment->response_amount) ? '-' : $payment->response_date ?></td>
                <td><?php echo $this->string()->truncate($this->string()->stripTags(empty($payment->response_message) ? '-' : $payment->response_message), 30) ?></td>
                <td><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($payment->remaining_amount) ?></td>
                <td><?php echo $this->translate($request_status) ?></td>
                <td class="txt_center"><?php echo $this->translate("%s", $payment_status) ?></td>
                <td class="txt_center"><?php
                  if ($payment->request_status == 0):
                    echo '<a href="javascript:void(0)" onClick="Smoothbox.open(\'sitestoreproduct/product/edit-payment-request/request_id/' . $payment->request_id . '\')"> ' . $this->translate("edit") . ' </a> | ';
                    echo '<a href="javascript:void(0)" onClick="Smoothbox.open(\'sitestoreproduct/product/delete-payment-request/request_id/' . $payment->request_id . '\')"> ' . $this->translate("cancel") . ' </a> | ';
                  endif;
                  echo '<a href="javascript:void(0)" onClick="Smoothbox.open(\'sitestoreproduct/product/view-payment-request/request_id/' . $payment->request_id . '/store_id/' . $this->store_id . '\')"> ' . $this->translate("details") . ' </a>';
                  ?>
                </td>   
              </tr>
            <?php endforeach; ?>  

          </table>
        </div>
      </div>

      <div>
        <div id="store_payment_request_previous" class="paginator_previous">
          <?php
          echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
              'onclick' => '',
              'class' => 'buttonlink icon_previous'
          ));
          ?>
          <span id="payment_spinner_prev"></span>
        </div>

        <div id="store_payment_request_next" class="paginator_next">
          <span id="payment_spinner_next"></span>
          <?php
          echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
              'onclick' => '',
              'class' => 'buttonlink_right icon_next'
          ));
          ?>
        </div>

        <?php
      else:
        echo '<div class="tip">
          <span>
            ' . $this->translate("You have not made any payment request yet.") . '
          </span>
        </div>';
      endif;
      ?>
    </div>
    <?php if (empty($this->call_same_action)) : ?>
    </div>
  </div>
<?php endif; ?>
<script type="text/javascript">


        en4.core.runonce.add(function() {

          var anchor = document.getElementById('manage_order_tab').getParent();
<?php if ($countPagination): ?>
            document.getElementById('store_payment_request_previous').style.display = '<?php echo ( $this->paginator->getCurrentPageNumber() == 1 ? 'none' : '' ) ?>';
            $('store_payment_request_next').style.display = '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' ) ?>';

            $('store_payment_request_previous').removeEvents('click').addEvent('click', function() {
              $('payment_spinner_prev').innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitestoreproduct/externals/images/loading.gif" />';

              var tempPaymentPaginationUrl = '<?php echo $this->url(array('action' => 'store', 'store_id' => $this->store_id, 'type' => 'product', 'menuId' => 56, 'method' => 'payment-to-me', 'page' => $this->paginator->getCurrentPageNumber() - 1), 'sitestore_store_dashboard', true); ?>';

              if (tempPaymentPaginationUrl && typeof history.pushState != 'undefined') {
                history.pushState({}, document.title, tempPaymentPaginationUrl);
              }

              en4.core.request.send(new Request.HTML({
                url: en4.core.baseUrl + 'sitestoreproduct/product/payment-to-me/store_id/' + <?php echo sprintf('%d', $this->store_id) ?>,
                data: {
                  format: 'html',
                  search: 1,
                  request_date: $('request_date').value,
                  response_date: $('response_date').value,
                  subject: en4.core.subject.guid,
                  call_same_action: 1,
                  request_min_amount: $('request_min_amount').value,
                  request_max_amount: $('request_max_amount').value,
                  response_min_amount: $('response_min_amount').value,
                  response_max_amount: $('response_max_amount').value,
                  request_status: $('request_status').value,
                  store_id: <?php echo sprintf('%d', $this->store_id) ?>,
                  page: <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() - 1) ?>
                },
                onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
                  $('payment_spinner_prev').innerHTML = '';
                }
              }), {
                'element': anchor
              })
            });

            $('store_payment_request_next').removeEvents('click').addEvent('click', function() {
              $('payment_spinner_next').innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitestoreproduct/externals/images/loading.gif" />';

              var tempPaymentPaginationUrl = '<?php echo $this->url(array('action' => 'store', 'store_id' => $this->store_id, 'type' => 'product', 'menuId' => 56, 'method' => 'payment-to-me', 'page' => $this->paginator->getCurrentPageNumber() + 1), 'sitestore_store_dashboard', true); ?>';

              if (tempPaymentPaginationUrl && typeof history.pushState != 'undefined') {
                history.pushState({}, document.title, tempPaymentPaginationUrl);
              }

              en4.core.request.send(new Request.HTML({
                url: en4.core.baseUrl + 'sitestoreproduct/product/payment-to-me/store_id/' + <?php echo sprintf('%d', $this->store_id) ?>,
                data: {
                  format: 'html',
                  search: 1,
                  request_date: $('request_date').value,
                  response_date: $('response_date').value,
                  subject: en4.core.subject.guid,
                  call_same_action: 1,
                  request_min_amount: $('request_min_amount').value,
                  request_max_amount: $('request_max_amount').value,
                  response_min_amount: $('response_min_amount').value,
                  response_max_amount: $('response_max_amount').value,
                  request_status: $('request_status').value,
                  store_id: <?php echo sprintf('%d', $this->store_id) ?>,
                  page: <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>
                },
                onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
                  $('payment_spinner_next').innerHTML = '';
                }
              }), {
                'element': anchor
              })
            });

<?php endif; ?>

          $('filter_form').removeEvents('submit').addEvent('submit', function(e) {
            e.stop();
            $('search_spinner').innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitestoreproduct/externals/images/loading.gif" />';

            en4.core.request.send(new Request.HTML({
              url: en4.core.baseUrl + 'sitestoreproduct/product/payment-to-me',
              method: 'POST',
              data: {
                search: 1,
                request_date: $('request_date').value,
                response_date: $('response_date').value,
                subject: en4.core.subject.guid,
                call_same_action: 1,
                request_min_amount: $('request_min_amount').value,
                request_max_amount: $('request_max_amount').value,
                response_min_amount: $('response_min_amount').value,
                response_max_amount: $('response_max_amount').value,
                request_status: $('request_status').value,
                store_id: <?php echo sprintf('%d', $this->store_id) ?>
              },
              onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
                $('search_spinner').innerHTML = '';
              }
            }), {
              'element': anchor
            })
          });
        });
</script>