<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: order-statistics.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<h2 class="fleft">
  <?php echo $this->translate('Stores / Marketplace - Ecommerce Plugin');?>
</h2>


<?php if( count($this->navigation) ): ?>
	<div class='seaocore_admin_tabs'> <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?> </div>
<?php endif; ?>
  
<div class='tabs'>
  <ul class="navigation">
    <li>
    <?php echo $this->htmlLink(array('route'=>'admin_default','module'=>'sitestore','controller'=>'settings','action'=>'statistic'), $this->translate('Store Statistics'), array())
    ?>
    </li>
    <li>
    <?php
      echo $this->htmlLink(array('route'=>'admin_default','module'=>'sitestoreproduct','controller'=>'settings','action'=>'statistic'), $this->translate('Product Statistics'), array())
    ?>
    </li>
    <li class="active">
      <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestoreproduct', 'controller' => 'settings', 'action' => 'order-statistics'), $this->translate('Product Orders Statistics'), array()) ?>
    </li>    
  </ul>
</div>  
  
<div class='clear'>
  <div class='settings'>
    <form class="global_form">
      <div>
        <h3><?php echo $this->translate('Statistics for Orders');?></h3>
        <p class="description"> <?php echo $this->translate('Below are some valuable statistics for the Orders placed on this site.');?>
        </p>
        <br />
        
        <table class='admin_table sr_sitestoreproduct_statistics_table' width="100%">
          <tbody>
            <tr>
            	<td width="50%"><?php echo $this->translate("Total Stores");?> :</td>
            	<td><?php echo $this->totalStores ?></td>
            </tr> 
            <tr>
            	<td width="50%"><?php echo $this->translate("Total Orders");?> :</td>
            	<td><?php echo $this->totalOrders ?></td>
            </tr>
            <tr>
            	<td width="50%"><?php echo $this->translate("Total Received Orders");?> :</td>
            	<td><?php echo $this->adminAmountDetails->order_count ?></td>
            </tr>
            <tr>
            	<td width="50%"><?php echo $this->translate("Total Commission");?> :</td>
            	<td><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrencyAdmin($this->adminAmountDetails->commission) ?></td>
            </tr>
            <tr>
            	<td width="50%"><?php echo $this->translate("Total Admin Tax");?> :</td>
            	<td><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrencyAdmin($this->adminAmountDetails->admin_tax) ?></td>
            </tr>
            <tr>
            	<td width="50%"><?php echo $this->translate("Total Approval Pending Orders");?> :</td>
            	<td><?php echo $this->approvalPendingOrders ?></td>
            </tr>
            <tr>
            	<td width="50%"><?php echo $this->translate("Total Payment Pending Orders");?> :</td>
            	<td><?php echo $this->paymentPendingOrders ?></td>
            </tr>
            <tr>
            	<td width="50%"><?php echo $this->translate("Total Processing Orders");?> :</td>
            	<td><?php echo $this->processingOrders ?></td>
            </tr>
            <tr>
            	<td width="50%"><?php echo $this->translate("Total On Hold Orders");?> :</td>
            	<td><?php echo $this->onholdOrders ?></td>
            </tr>
            <tr>
            	<td width="50%"><?php echo $this->translate("Total Fraud Orders");?> :</td>
            	<td><?php echo $this->fraudOrders ?></td>
            </tr>                        
            <tr>
            	<td width="50%"><?php echo $this->translate("Total Completed Orders");?> :</td>
            	<td><?php echo $this->completedOrders ?></td>
            </tr>
            <tr>
            	<td width="50%"><?php echo $this->translate("Total Canceled Orders");?> :</td>
            	<td><?php echo $this->cancelOrders ?></td>
            </tr>
          </tbody>
        </table>
      </div>
    </form>
  </div>
</div>