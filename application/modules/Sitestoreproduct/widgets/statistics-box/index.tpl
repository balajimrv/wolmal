<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>
 
<h3><?php echo $this->translate("Sales Figures") ?></h3>
<div class="mbot15">
  <table class="sitestoreproduct_amount_table sitestoreproduct_data_table">
    <tr>
      <td class="txt_center">
        <span><?php echo $this->translate('Today') ?></span>
        <div class="txt_center bold"><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency(empty($this->todaySale) ? '0' : $this->todaySale) ?></div>
      </td>
      <td class="txt_center">
        <span><?php echo $this->translate('This Week') ?></span>
        <div class="txt_center bold"><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency(empty($this->weekSale) ? '0' : $this->weekSale) ?></div>
      </td>
      <td class="txt_center">
        <span><?php echo $this->translate('This Month') ?></span>
        <div class="txt_center bold"><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency(empty($this->monthSale) ? '0' : $this->monthSale) ?></div>
      </td>
    </tr> 
  </table>
  <?php if( empty($this->directPayment) ) : ?>
  <div class="f_small"><i><?php echo $this->translate("You can request payment from %s from 'Payment Requests' section after your balance payment exceeds the threshold.", $this->siteTitle); ?></i></div>
  <?php endif; ?>
</div>
