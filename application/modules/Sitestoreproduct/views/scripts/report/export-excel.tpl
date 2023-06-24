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
   
<?php if(count($this->rawdata)) : ?>
  <?php
    header("Expires: 0");
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    header("Cache-Control: no-store, no-cache, must-revalidate");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
    header("Content-type: application/vnd.ms-excel;charset:UTF-8;");
    header("Content-Disposition: attachment; filename=Report.xls"); 
    print "\n"; // Add a line, unless excel error..
 
    switch($this->values['time_summary']) {
      case 'Monthly':
      $date_label = 'Month';
      break;

      case 'Daily':
      $date_label = 'Date';
      break;
    }
  ?>

  <table class='admin_table'>
    <thead>
      <tr>
        <th><?php echo $date_label ?></th>
        <th><?php echo $this->translate('Store Name') ?></th>
        <?php if( $this->values['report_depend'] == 'product' ): ?>
          <th><?php echo $this->translate('Product Title') ?></th>
          <th><?php echo $this->translate('Product SKU') ?></th>
        <?php endif; ?>
        <th><?php echo $this->translate('Order Count') ?></th>
        <th><?php echo $this->translate('Product Quantity') ?></th>
        <?php if( $this->values['report_depend'] == 'product' ): ?>
          <th><?php echo $this->translate('Total') ?></th>
        <?php endif; ?>
        <?php if( $this->values['report_depend'] == 'order' ): ?>
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
                  $temp_store_id = $data->store_id;
                  $store_title = $tempStoreTitle;
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
          <?php if( $this->values['report_depend'] == 'product' ): ?>
            <td><?php echo $data->title ?></td>
            <td><?php echo empty($data->product_code) ? '-' : $data->product_code; ?></td>
          <?php endif; ?>
          <td><?php echo $data->order_count ?></td>
          <td><?php echo $data->quantity ?></td>
          <?php if( $this->values['report_depend'] == 'product' ): ?>
            <td align="right" ><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($data->price) ?></td>
          <?php endif; ?>
          <?php if( $this->values['report_depend'] == 'order' ): ?>
            <td align="right" ><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($data->sub_total) ?></td>
            <td align="right" ><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($data->store_tax) ?></td>
            <td align="right" ><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($data->admin_tax) ?></td>
            <td align="right" ><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($data->shipping_price) ?></td>
            <td align="right" ><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($data->commission) ?></td>
            <td align="right" ><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($data->grand_total) ?></td>
          <?php endif; ?>
        </tr>	
      <?php endforeach; ?>        
    </tbody>
  </table>
<?php else:?>
	<div class="tip">
    <span>
      <?php echo $this->translate("There are no activities found in the selected date range.") ?>
    </span>
  </div>
<?php endif; ?>