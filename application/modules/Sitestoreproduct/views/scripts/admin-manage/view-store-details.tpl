<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: view-store-details.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<div class="sitestore_admin_popup">
  <div>
    <h3><?php echo $this->translate('Store Details'); ?></h3>
    <br />
    <table cellpadding="0" cellspacing="0" class="sitestore-view-detail-table">
      <tr>
        <td>
          <table cellpadding="0" cellspacing="0" width="350">
            <tr>
              <td width="200"><b><?php echo $this->translate('Title:'); ?></b></td>
              <td>
                 <?php echo $this->htmlLink($this->item('sitestore_store', $this->sitestoreDetail->store_id)->getHref(), $this->translate($this->sitestoreDetail->title), array('target' => '_blank')) ?>&nbsp;&nbsp;
              </td>
            </tr>
            
            <tr>
              <td><b><?php echo $this->translate('Owner:'); ?></b></td>
              <td>
                <?php echo $this->htmlLink($this->sitestoreDetail->getOwner()->getHref(), $this->sitestoreDetail->getOwner()->getTitle(), array('target' => '_blank')) ?>
              </td>
            </tr>
   
            <tr>
              <td><b><?php echo $this->translate('Number of Order’s:'); ?></b></td>
              <td><?php echo ($this->store_overview['order_count']) ? $this->store_overview['order_count'] : '0'; ?></td>
            </tr>
              
            <tr>
              <td><b><?php echo $this->translate('Grand Total:'); ?></b></td>
              <?php if( isset($this->store_overview['grand_total']) && !empty($this->store_overview['grand_total']) ) : ?>
                <?php $grandTotal = $this->store_overview['grand_total']; ?>
              <?php else: ?>
                <?php $grandTotal = 0; ?>
              <?php endif; ?>
              <td><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrencyAdmin($grandTotal) ?></td>
            </tr>
              
            <?php if( !empty($this->total_sale_this_year) ) : ?>
              <tr>
                <td><b><?php echo $this->translate('Total Sale This Year:'); ?></b></td>
                <td><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrencyAdmin($this->total_sale_this_year) ?></td>
              </tr>
            <?php endif; ?>

            <tr>
              <td><b><?php echo $this->translate('Total Commission:'); ?></b></td>
              <td><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrencyAdmin($this->store_overview['commission']) ?></td>
            </tr>

            <tr>
              <td><b><?php echo $this->translate('Total Store Tax:'); ?></b></td>
              <td><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrencyAdmin($this->store_overview['store_tax']) ?></td>
            </tr>
            
            <tr>
              <td><b><?php echo $this->translate('Total Product Sale:'); ?></b></td>
              <td><?php echo ($this->store_overview['item_count']) ? $this->store_overview['item_count'] : '0' ?></td>
            </tr>
            
            <tr>
              <td><b><?php echo $this->translate('Approval Pending Orders:'); ?></b></td>
              <td><?php echo ($this->approval_pending_orders) ? $this->approval_pending_orders : '0' ?></td>
            </tr>
            
            <tr>
              <td><b><?php echo $this->translate('Payment Pending Orders:'); ?></b></td>
              <td><?php echo ($this->payment_pending_orders) ? $this->payment_pending_orders : '0' ?></td>
            </tr>
            
            <tr>
              <td><b><?php echo $this->translate('Processing Orders:'); ?></b></td>
              <td><?php echo ($this->processing_orders) ? $this->processing_orders : '0' ?></td>
            </tr>
            
            <tr>
              <td><b><?php echo $this->translate('On Hold Orders:'); ?></b></td>
              <td><?php echo ($this->on_hold_orders) ? $this->on_hold_orders : '0' ?></td>
            </tr>
            
            <tr>
              <td><b><?php echo $this->translate('Fraud Orders:'); ?></b></td>
              <td><?php echo ($this->fraud_orders) ? $this->fraud_orders : '0' ?></td>
            </tr>
            
            <tr>
              <td><b><?php echo $this->translate('Complete Orders:'); ?></b></td>
              <td><?php echo ($this->complete_orders) ? $this->complete_orders : '0' ?></td>
            </tr>
            
            <tr>
              <td><b><?php echo $this->translate('Cancel Orders:'); ?></b></td>
              <td><?php echo ($this->cancel_orders) ? $this->cancel_orders : '0' ?></td>
            </tr>
              
          </table>
        </td>
        <td align="right">
<?php echo $this->htmlLink($this->sitestoreDetail->getHref(), $this->itemPhoto($this->sitestoreDetail, 'thumb.icon', '', array('align' => 'right')), array('target' => '_blank')) ?>
        </td>	
      </tr>
    </table>		
    <br />
    <button  onclick='javascript:parent.Smoothbox.close()' ><?php echo $this->translate('Close') ?></button>
  </div>
</div>