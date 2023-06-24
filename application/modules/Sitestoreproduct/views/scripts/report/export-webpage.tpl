<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: export-webpage.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>
   
<?php 
  include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/payment_navigation_views.tpl';
  include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/edit_tabs.tpl';
?>

<div class="layout_middle">
  <div class="sitestore_edit_content">
    <div class="sitestoreproduct_manage_account">
			<h3><?php echo $this->translate('View Sale Report') ?></h3>
    	<a class="buttonlink icon_previous mbot5" href="<?php echo $this->url(array('store_id' => $this->store_id), 'sitestoreproduct_report_general', true) ?>">
        <?php echo $this->translate("Back to Sale Report") ?>
      </a>
	
			<div class="sitestoreproduct_data_table product_detail_table fleft">
        <table>
          <tr class="product_detail_table_head">
            <th><?php echo $this->translate('Summarize By') ?></th>
            <th><?php echo $this->translate('Time Summary') ?></th>
            <th><?php echo $this->translate('Duration') ?></th>
          </tr>	
          <tr>
            <td>
              <?php 
                if( $this->report_type == 'product' ):
                  echo $this->translate('Product');
                elseif( $this->report_type == 'order' ):
                  echo $this->translate('Order');
                endif;
              ?>
            </td>
            <td>
              <?php echo $this->translate($this->values['time_summary']); ?>
            </td>
            <td>
              <?php
                $startTime = $endTime = date('Y-m-d');
                if(!empty($this->values['time_summary'])) :
                  if($this->values['time_summary'] == 'Monthly') :
                    $startTime = date('M d, Y', mktime(0, 0, 0, $this->values['month_start'], date('d'), $this->values['year_start']));
                    $endTime = date('M d, Y', mktime(0, 0, 0, $this->values['month_end'], date('d'), $this->values['year_end']));
                  else:
                    if (!empty($this->values['start_daily_time'])) :
                      $start = $this->values['start_daily_time'];
                    endif;
                    if (!empty($this->values['start_daily_time'])) :
                      $end = $this->values['end_daily_time'];
                    endif;
                    $startTime = date('M d, Y', $start);
                    $endTime = date('M d, Y', $end);
                  endif;
                endif;
                echo $this->timestamp($startTime). $this->translate(" to "). $this->timestamp($endTime);
              ?>
            </td>
          </tr>
        </table>
      </div>
	
			<div id='stat_table' style="clear:both;">
        <?php if( @COUNT($this->rawdata) > 0 ) : ?>
          <div class="sitestoreproduct_data_table product_detail_table fleft">
            <table class="widthfull">
              <tr class="product_detail_table_head">
                <th><?php echo $this->translate('Date') ?></th>
                <th><?php echo $this->translate('Store Name') ?></th>
                <?php if( $this->report_type == 'product' ): ?>
                  <th><?php echo $this->translate('Product Title') ?></th>
                  <th><?php echo $this->translate('Product SKU') ?></th>
                <?php endif; ?>
                <th><?php echo $this->translate('Order Count') ?></th>
                <th><?php echo $this->translate('Product Quantity') ?></th>
                <?php if( $this->report_type == 'order' ): ?>
                  <th><?php echo $this->translate('Store Subtotal') ?></th>
                  <th><?php echo $this->translate('Store Tax') ?></th>
                  <th><?php echo $this->translate('Admin Tax') ?></th>
                  <th><?php echo $this->translate('Shipping Price') ?></th>
                  <th><?php echo $this->translate('Commission') ?></th>
                  <th><?php echo $this->translate('Order Total') ?></th>
                <?php endif; ?>
            	</tr>	
					  	<?php foreach($this->rawdata as $data) : ?>
				      <tr>                
                <?php $sitestore = Engine_Api::_()->getItem('sitestore_store', $data->store_id); ?>
                <td><?php echo $this->timestamp($data->creation_date) ?></td>
                <td><?php echo $this->htmlLink($sitestore->getHref(), $sitestore->getTitle(), array('target' => '_blank')); ?></td>
                <?php if( $this->report_type == 'product' ): ?>
                  <td><?php echo $data->title ?></td>
                  <td><?php echo empty($data->product_code) ? '-' : $data->product_code; ?></td>
                <?php endif; ?>
                <td><?php echo $data->order_count ?></td>
                <td><?php echo $data->quantity ?></td>
                <?php if( $this->report_type == 'order' ): ?>
                  <td><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($data->sub_total) ?></td>
                  <td><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($data->store_tax) ?></td>
                  <td><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($data->admin_tax) ?></td>
                  <td><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($data->shipping_price) ?></td>
                  <td><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($data->commission) ?></td>
                  <td><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($data->grand_total) ?></td>
                <?php endif; ?>
              </tr>	
              <?php endforeach; ?>
            </table>
          </div>
				<?php else :?>
          <div class="tip">
            <span>
              <?php echo $this->translate("There are no activities found in the selected date range.") ?>
            </span>
          </div>
				<?php endif; ?>
       </div> 
    </div>
  </div>
</div>