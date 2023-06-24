<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: export-excel.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>
 
<?php if(@count($this->rawdata)) :?>
  <?php
      header("Expires: 0");
      header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
      header("Cache-Control: no-store, no-cache, must-revalidate");
      header("Cache-Control: post-check=0, pre-check=0", false);
      header("Pragma: no-cache");
      header("Content-type: application/vnd.ms-excel;charset:UTF-8");
      header("Content-Disposition: attachment; filename=Report.xls"); 
      print "\n"; // Add a line, unless excel error..
    ?>

  <?php switch($this->values['time_summary']) {
          case 'Monthly':
            $date_label = 'Month';
          break;
          case 'Daily':
            $date_label = 'Date';
          break;
        } ?>
<div id='stat_table'>
  <table>
    <thead>
      <tr>
        <th><?php echo $date_label ?></th>
        <th><?php echo $this->translate('Store Name') ?></th>
        <?php if( !empty($this->reportType) ): ?>
          <th><?php echo $this->translate('Product Title') ?></th>
          <th><?php echo $this->translate('Product SKU') ?></th>
        <?php endif; ?>
        <th><?php echo $this->translate('Order Count') ?></th>
        <th><?php echo $this->translate('Product Quantity') ?></th>
        <?php if( $this->reportType == 'product' ): ?>
          <th><?php echo $this->translate('Total') ?></th>
        <?php endif; ?>
        <?php if( empty($this->reportType) ): ?>
          <th><?php echo $this->translate('Store Subtotal') ?></th>
          <th><?php echo $this->translate('Store Tax') ?></th>
          <th><?php echo $this->translate('Admin Tax') ?></th>
          <th><?php echo $this->translate('Shipping Price') ?></th>
          <th><?php echo $this->translate('Commission') ?></th>
          <th><?php echo $this->translate('Order Total') ?></th>
        <?php endif; ?>
      </tr>	
    </thead>
      
    <tbody>
      <?php foreach( $this->rawdata as $data ): ?>   
        <?php $tempStoreTitle = Engine_Api::_()->getDbtable('stores', 'sitestore')->getStoreName($data->store_id); ?>
        <?php if( $temp_creation_date != $data->creation_date):
                $temp_creation_date = $data->creation_date;
                $temp_date = $data->creation_date;
                $store_title = $tempStoreTitle;
                $temp_store_id = $data->store_id;
                echo '<tr><td>&nbsp;</td></tr>';
              else:
                if( $temp_store_id != $data->store_id):
                  $temp_store_id = $data->store_id;
                  $store_title = $tempStoreTitle;
                else:
                  $store_title = "";
                  $temp_store_id = $data->store_id;
                endif; 
                $temp_date = "";
                $temp_creation_date = $data->creation_date;
              endif; 
        ?>
        <tr>
          <td><b><?php echo $temp_date ?></b></td>
          <td><b><?php echo $store_title ?></b></td>
          <?php if( !empty($this->reportType) ): ?>
            <td><?php echo $data->title ?></td>
            <td><?php echo empty($data->product_code) ? '-' : $data->product_code; ?></td>
          <?php endif; ?>
          <td><?php echo $data->order_count ?></td>
          <td><?php echo $data->quantity ?></td>
          <?php if( !empty($this->reportType) ): ?>
            <td><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrencyAdmin($data->price) ?></td>
          <?php endif; ?>
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
</div>
<?php else:?>
	<div class="tip">
    <span>
      <?php echo $this->translate("There are no activities found in the selected date range.") ?>
    </span>
  </div>
<?php endif; ?>