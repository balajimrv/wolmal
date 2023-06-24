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

<div class="headline">
  <h2><?php echo $this->translate("User Driven Store") ?></h2>
  <?php if (@count($this->navigation)): ?>
    <div class='seaocore_admin_tabs'>
      <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render(); ?>
    </div>
  <?php endif; ?>
</div>

<div class='tabs'>
  <ul class="navigation">
    <li class="<?php echo empty($this->reportType) ? 'active' : '' ?>">
      <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestoreproduct', 'controller' => 'report', 'type' => 0), $this->translate('Sales Report')) ?>
    </li>
    <li class="<?php echo !empty($this->reportType) ? 'active' : '' ?>">
      <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestoreproduct', 'controller' => 'report', 'type' => 1), $this->translate('Products Report')) ?>
    </li>
  </ul>
</div>

<?php switch($this->values['time_summary']) {
				case 'Monthly':
          $date_label = 'Month';
				break;
				case 'Daily':
  				$date_label = 'Date';
				break;
      } ?>

<?php if( empty($this->reportType) ) : ?>
  <h3><?php echo $this->translate('View Sales Report') ?></h3>
<?php else: ?>
  <h3><?php echo $this->translate('View Products Report') ?></h3>
<?php endif; ?>

<div class="clr mtop10">
  <b class="bold"><?php echo $this->translate('Time Summary') ?>:</b>
  <?php echo $this->translate($this->values['time_summary']); ?>
  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

  <b class="bold"><?php echo $this->translate('Duration') ?>:</b>
  <?php $startTime = $endTime = @date('Y-m-d');
    if(!empty($this->values['time_summary'])) :
      if($this->values['time_summary'] == 'Monthly') :
        $startTime = date('M d, Y', mktime(0, 0, 0, $this->values['month_start'], date('d'), $this->values['year_start']));
        $endTime = date('M d, Y', mktime(0, 0, 0, $this->values['month_end'], date('d'), $this->values['year_end']));
      else :
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
    echo $startTime. $this->translate(" to "). $endTime;
?>
</div>
	
<div id='stat_table' class="mtop10 clr">
  <?php if(@count($this->rawdata)) : ?>
    <table class="admin_table seaocore_admin_table" style="width:100%;">
      <thead>
        <tr>
          <th class='admin_table_short'><?php echo $date_label ?></th>
          <?php if( empty($this->reportType) ) : ?>
            <th class='admin_table_short'><?php echo $this->translate('Store Name') ?></th>
          <?php endif; ?>
          <?php if( !empty($this->reportType) ): ?>
            <th class='admin_table_short' style="width:5%"><?php echo $this->translate('Product Title') ?></th>
            <th class='admin_table_short'><?php echo $this->translate('Product SKU') ?></th>
          <?php endif; ?>
          <th class='admin_table_short admin_table_centered'><?php echo $this->translate('Order Count') ?></th>
          <th class='admin_table_short admin_table_centered'><?php echo $this->translate('Product Quantity') ?></th>
          <?php if( empty($this->reportType) ): ?>
            <th class='admin_table_short'><?php echo $this->translate('Store Subtotal') ?></th>
            <th class='admin_table_short'><?php echo $this->translate('Store Tax') ?></th>
            <th class='admin_table_short'><?php echo $this->translate('Admin Tax') ?></th>
            <th class='admin_table_short'><?php echo $this->translate('Shipping Price') ?></th>
            <th class='admin_table_short'><?php echo $this->translate('Commission') ?></th>
            <th class='admin_table_short'><?php echo $this->translate('Order Total') ?></th>
          <?php endif; ?>
        </tr>	
      </thead>
      <tbody> 	
        <?php foreach($this->rawdata as $data) : ?>
          <tr>
            <?php $sitestore = Engine_Api::_()->getItem('sitestore_store', $data->store_id); ?>
            <?php if(empty($sitestore)): continue; endif; ?>
            <?php if( !empty($this->reportType) ): ?>
              <?php $sitestoreProduct = Engine_Api::_()->getItem('sitestoreproduct_product', $data->product_id); ?>
            <?php endif; ?>
            <td><?php echo $data->creation_date ?></td>                        
            <?php if( empty($this->reportType) ) : ?>
              <td><?php echo $this->htmlLink($sitestore->getHref(), $sitestore->getTitle(), array('target' => '_blank')); ?></td>
             <?php endif; ?>
            <?php if( !empty($this->reportType) ): ?>
              <td><?php echo $this->htmlLink($sitestoreProduct->getHref(), $sitestoreProduct->getTitle(), array('target' => '_blank')); ?></td>
              <td><?php echo empty($data->product_code) ? '-' : $data->product_code; ?></td>
            <?php endif; ?>
            <td class="admin_table_centered"><?php echo $data->order_count ?></td>
            <td class="admin_table_centered"><?php echo $data->quantity ?></td>
            <?php if( empty($this->reportType) ): ?>
              <td><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrencyAdmin($data->sub_total) ?></td>
              <td><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrencyAdmin($data->store_tax) ?></td>
              <td><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrencyAdmin($data->admin_tax) ?></td>
              <td><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrencyAdmin($data->shipping_price) ?></td>
              <td><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrencyAdmin($data->commission) ?></td>
              <td><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrencyAdmin($data->grand_total) ?></td>
            <?php endif; ?>
          </tr>	
          <?php endforeach; ?>
        </tbody>	
      </table>

  <?php elseif(!count($this->rawdata) && $this->post == 1) :?>
    <div class="tip">
      <span>
        <?php echo $this->translate("There are no activities found in the selected date range.") ?>
      </span>
    </div>
  <?php endif; ?>
</div>