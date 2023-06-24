<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: monthly-bill-detail.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php $countPagination = count($this->paginator); ?>
<?php if (empty($this->search)) : ?>
  <div class="sitestoreproduct_manage_store sitestoreproduct_payment_to_me">
    <h3>
      <?php echo $this->translate("%1sYour Bill%2s > %s %s", '<a href="javascript:void(0)" onclick="manage_store_dashboard(56, \'your-bill\', \'product\');">','</a>', $this->monthName, $this->year) ?>
    </h3>
    <p class="mbot10">
      <?php echo $this->translate('Below, you can view the bill of your store in the month of %s %s.', $this->monthName, $this->year); ?>
    </p>

    <div id="payment_request_table">
<?php endif; ?>
<?php if ($countPagination): ?>
  <div><span><?php echo $this->translate('%s order(s) found.', $this->total_item) ?></span></div>
<?php endif; ?>
    <div id="monthly_bill_detail">
      <?php if ($countPagination): ?>
        <div class="product_detail_table sitestoreproduct_data_table fleft">
          <table>
            <tr class="product_detail_table_head">
              <th><?php echo $this->translate("Order Id") ?></th>
              <th><?php echo $this->translate("Order Total") ?></th>
              <th><?php echo $this->translate("Commission") ?></th>
              <th><?php echo $this->translate("Product Count") ?></th>
              <th><?php echo $this->translate("Payment") ?></th>
              <th><?php echo $this->translate("Order Date") ?></th>
              <th><?php echo $this->translate("Options") ?></th>
            </tr>
            <?php foreach ($this->paginator as $payment) : ?>        
              <tr>
                <td>
                  <a href="javascript:void(0)" onclick="manage_store_dashboard(55, 'order-view/order_id/<?php echo $payment->order_id; ?>', 'index')">
                    <?php echo '#'.$payment->order_id ?>
                  </a>
                </td>
                <td><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($payment->grand_total) ?></td>
                <td><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($payment->commission_value) ?></td>
                <td><?php echo $payment->item_count ?></td>
                <td>
                  <?php if( $payment->payment_status == 'not_paid' ) : ?>
                    <i class="seaocore_txt_red"><?php echo $this->translate("marked as non-payment") ?></i>
                  <?php elseif( $payment->payment_status == 'active' ) : ?>
                    <?php echo $this->translate("Yes") ?>
                  <?php elseif( $payment->payment_status != 'active' ) : ?>
                    <?php echo $this->translate("No") ?>
                  <?php endif; ?>
                </td>
                <td><?php echo gmdate('M d,Y, g:i A',strtotime($payment->creation_date)) ?></td>
                <td class="txt_center">
                  <a href="javascript:void(0)" onclick="manage_store_dashboard(55, 'order-view/order_id/<?php echo $payment->order_id; ?>', 'index')">
                    <?php echo $this->translate("details") ?>
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>  
          </table>
        </div>
      </div>

      <div>
        <div id="store_monthly_bill_detail_previous" class="paginator_previous">
          <?php
          echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
              'onclick' => '',
              'class' => 'buttonlink icon_previous'
          ));
          ?>
          <span id="bill_detail_spinner_prev"></span>
        </div>

        <div id="store_monthly_bill_detail_next" class="paginator_next">
          <span id="bill_detail_spinner_next"></span>
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
            ' . $this->translate("You have not any bill payment in this month.") . '
          </span>
        </div>';
      endif;
      ?>
    </div>
    <?php if (empty($this->search)) : ?>
    </div>
  </div>
<?php endif; ?>
<script type="text/javascript">

        en4.core.runonce.add(function() {

          var anchor = document.getElementById('monthly_bill_detail').getParent();
<?php if ($countPagination): ?>
            document.getElementById('store_monthly_bill_detail_previous').style.display = '<?php echo ( $this->paginator->getCurrentPageNumber() == 1 ? 'none' : '' ) ?>';
            $('store_monthly_bill_detail_next').style.display = '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' ) ?>';

            $('store_monthly_bill_detail_previous').removeEvents('click').addEvent('click', function() {
              $('bill_detail_spinner_prev').innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitestoreproduct/externals/images/loading.gif" />';

              var tempPaymentPaginationUrl = '<?php echo $this->url(array('action' => 'store', 'store_id' => $this->store_id, 'type' => 'product', 'menuId' => 56, 'method' => 'monthly-bill-detail', 'month' => $this->month, 'year' => $this->year, 'page' => $this->paginator->getCurrentPageNumber() - 1), 'sitestore_store_dashboard', true); ?>';

              if (tempPaymentPaginationUrl && typeof history.pushState != 'undefined') {
                history.pushState({}, document.title, tempPaymentPaginationUrl);
              }

              en4.core.request.send(new Request.HTML({
                url: en4.core.baseUrl + 'sitestoreproduct/product/monthly-bill-detail/store_id/' + <?php echo sprintf('%d', $this->store_id) ?>,
                data: {
                  format: 'html',
                  search: 1,
                  month: '<?php echo $this->month ?>',
                  year: '<?php echo $this->year ?>',
                  page: <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() - 1) ?>
                },
                onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
                  $('bill_detail_spinner_prev').innerHTML = '';
                }
              }), {
                'element': anchor
              })
            });

            $('store_monthly_bill_detail_next').removeEvents('click').addEvent('click', function() {
              $('bill_detail_spinner_next').innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitestoreproduct/externals/images/loading.gif" />';

              var tempPaymentPaginationUrl = '<?php echo $this->url(array('action' => 'store', 'store_id' => $this->store_id, 'type' => 'product', 'menuId' => 56, 'method' => 'monthly-bill-detail', 'month' => $this->month, 'year' => $this->year, 'page' => $this->paginator->getCurrentPageNumber() + 1), 'sitestore_store_dashboard', true); ?>';

              if (tempPaymentPaginationUrl && typeof history.pushState != 'undefined') {
                history.pushState({}, document.title, tempPaymentPaginationUrl);
              }

              en4.core.request.send(new Request.HTML({
                url: en4.core.baseUrl + 'sitestoreproduct/product/monthly-bill-detail/store_id/' + <?php echo sprintf('%d', $this->store_id) ?>,
                data: {
                  format: 'html',
                  search: 1,
                  month: '<?php echo $this->month ?>',
                  year: '<?php echo $this->year ?>',
                  page: <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>
                },
                onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
                  $('bill_detail_spinner_next').innerHTML = '';
                }
              }), {
                'element': anchor
              })
            });

<?php endif; ?>

        });
</script>