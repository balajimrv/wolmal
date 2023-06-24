<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: view-rate.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>
   
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/styles/style_sitestoreproduct_dashboard.css'); ?>

<div class="global_form_popup">
  <?php if (count($this->taxrate)): ?>
  	<div class="mbot5"><h3><?php echo $this->title.': '. $this->translate("Tax Rates") ?></h3></div>
   		<div id="tax_rate_pagination" class="product_detail_table sitestoreproduct_data_table">
      	<table>
          <tr class="product_detail_table_head">
            <th><?php echo $this->translate("Country"); ?></th>
            <th><?php echo $this->translate("Region / State"); ?></th>
            <th><?php echo $this->translate("Tax Rate") ?></th>
            <th><?php echo $this->translate("Applied From") ?></th>
          </tr>
          <?php foreach ($this->taxrate as $item): ?>				
            <tr>
              <td>
                <?php if ($item->country == 'ALL'): ?>
                  <?php echo $item->country ?>
                <?php else: ?>
                  <?php echo Zend_Locale::getTranslation($item->country, 'country') ?>
                <?php endif; ?> 
              </td>
              <td>
                <?php if ($item->country == 'ALL'): ?>
                  -
                <?php elseif($item->state == 0): ?>
                  <?php echo $this->translate('All') ?>
                <?php else: ?>
                  <?php echo $item->region ?>
                <?php endif; ?>
              </td>
              <td>
                <?php if (empty($item->handling_type)): ?>
                  <?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($item->tax_value); ?>
                <?php else: ?>
                  <?php echo round($item->tax_value, 2) ?>%
                <?php endif; ?>
              </td>
              <td><?php echo $this->timestamp(strtotime($item->creation_date)); ?></td>
            </tr>
          <?php endforeach; ?>
      </table>
    </div>
  <?php else: ?>
    <div class="tip">
      <span>
        <?php echo $this->translate("No tax location entry added for this tax.") ?>
      </span>
    </div>
  <?php endif; ?>
   <button class="fright" type="button" id="cancel" name="cancel"  onclick="javascript:parent.Smoothbox.close();" ><?php echo $this->translate("Close") ?></button>
</div>
