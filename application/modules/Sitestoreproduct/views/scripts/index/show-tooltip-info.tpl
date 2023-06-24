<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: show-tooltip-info.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<div id="manage_order_tab" class="info_tip_content_wrapper">
  <div class="sitestoreproduct_data_table product_detail_table">
  <table>
    <tr class="product_detail_table_head">
      <th><?php echo $this->translate("Title") ?></th>
      <th><?php echo $this->translate("Tax Amount") ?></th>
      <th><?php echo $this->translate("Tax Value") ?></th>
      <?php if( !empty($this->show_tax_type) ) : ?>
        <th><?php echo $this->translate("Tax Applied by") ?></th>
      <?php endif; ?>
    </tr>

    <?php foreach($this->tax as $tax): ?>
    <tr>
      <td><?php echo @ucfirst($tax['title']) ?></td>
      <td><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($tax['amount']) ?></td>
      <td><?php echo empty($tax['handling_type']) ? Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($tax['tax_value']) : number_format($tax['tax_value'], 2) . '%'; ?></td>
      <?php if( !empty($this->show_tax_type) ) : ?>
        <td><?php echo $tax['type'] ?></td>
      <?php endif; ?>
    </tr>
  <?php  endforeach; ?>
    
    
  </table>
</div>
</div>
  
  