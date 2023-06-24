<?php 

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: product-category-attributes.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>
<?php $this->headLink()
					->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/styles/style_sitestoreproductform.css') ?>

<?php include_once APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/scripts/_DashboardNavigation.tpl'; ?>

<div class="sr_sitestoreproduct_dashboard_content">
	<?php echo $this->partial('application/modules/Sitestoreproduct/views/scripts/dashboard/header.tpl', array('sitestoreproduct'=>$this->sitestoreproduct));?>
  <?php 
  $this->headLink()
          ->appendStylesheet($this->layout()->staticBaseUrl
                  . 'application/modules/Sitestoreproduct/externals/styles/style_sitestoreproductform.css')
  ?>
  
  <?php
  // Render the admin js
  echo $this->render('_jsAdminSitestoreproduct.tpl')
  ?>
  <?php if(count($this->dropDowns) != 0) :?>
  <h3><?php echo $this->translate('Product Variations') ?></h3>
  <p><?php echo $this->translate('Product Variations can be created as combinations of Product Attributes of "Select Box" type. These are the variations in which the product is available to you, like: for a particular shirt, you can have a variation like "Color: Red", "Size: Medium" with 30 Quantity.
<br/>
If there is an attribute on which you want to create a variation, then you can create that attribute of "Select Box" type from "Product Attributes" section.
<br/>
For each variation, you can specify the available quantity and price increment / decrement from base price of product. You can also relate price variation to select-box type attributes below.') ?></p>
  <br />
  
  <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.vat', 0)): ?>
    <p><?php echo $this->translate('The price for the product attributes will be displayed on the product view page according to the settings chosen by you, from the VAT section in store dashboard.');?></p>
  <?php endif; ?>
  
  
  <div class="seaocore_add mtop10">
    <a href="javascript:void(0);" onclick="void(0);" class="buttonlink seaocore_icon_add admin_fields_options_addcombinations"><?php echo $this->translate("Create Variations") ?></a>
  </div>
  
 <?php else :?>
    <div class="tip">
  <span><?php echo $this->translate('You have not created any “Select Box” type “Profile Field” for this product. Please go to the Product Attributes tab of this plugin and create some “Select Box” type profile fields to create variation products.'); ?> </span>
</div>
  <?php endif; ?>
  
  <?php if(!empty($this->success)) : ?>
  <ul class="form-notices"> <li><?php echo $this->translate('Congratulations, You have successfully saved your variation information.');?></li></ul>
  <?php endif; ?>
  
  <?php if(!empty($this->errorMessage)) : ?>
  <ul class="form-errors"> <li><?php echo $this->errorMessage;?></li></ul>
  <?php endif; ?>
  
  <br />
  
  <?php if(count($this->combinations) != 0) : ?>
  <?php if(count($this->combinations) != 0 && count($this->dropDowns) != 0): ?>
  <div class="mbot10">
    <form method="post" class='global_form'>
        <div class="sitestoreproduct_entry_edit_form">
            <h4><?php echo $this->translate('Attributes and their Price relation');?></h4>
            <ul class="sitestoreproduct_configurable_attributes">
              <?php foreach($this->dropDowns as $field_id => $dropDown) : ?>
              <li class="sitestoreproduct_attribute" id="configurable_attribute_<?php echo $field_id ;?>">
                <div class="sitestoreproduct_attribute_name fleft">
                  <b><?php echo $this->translate($dropDown['lable']); ?></b>
                </div>
                <?php if(count($this->combinationOptions[$field_id]) != 0) :?>
                <ul class="sitestoreproduct_attribute_values clr">
                  <?php foreach($this->combinationOptions[$field_id] as $option) :?>
                    <li class="sitestoreproduct_attribute_value" id="configurable_attribute_<?php echo $field_id; ?>_<?php echo $option['option_id'] ;?>">
                      <div>
                      <span class="sitestoreproduct_attribute_vlabel">
                        <?php echo $this->translate('Option:')?> <strong><?php echo $dropDown['multioptions'][$option['option_id']]; ?></strong>
                      </span>
                          <?php echo $this->priceLabel?>:
                          <select id="configurable_attribute_<?php echo $field_id; ?>_<?php echo $option['option_id']?>_price_type" name="configurable_attribute_<?php echo $field_id; ?>_<?php echo $option['option_id']?>_price_type">
                            <?php if(!empty($option['price_inc'])): ?>
                            <option value="1" selected ='selected'>+</option>
                            <option value="0">-</option>
                            <?php else: ?>
                            <option value="1">+</option>
                            <option value="0"  selected ='selected'>-</option>
                         <?php endif;?>
                            </select>
                          <input type="text" value="<?php echo $option['price']; ?>" id="configurable_attribute_<?php echo $field_id; ?>_<?php echo $option['option_id']?>_pricing" name='configurable_attribute_<?php echo $field_id; ?>_<?php echo $option['option_id']?>_pricing'>
                        </div>
                    </li>
                  <?php endforeach; ?>
                    </ul>
                 <?php endif; ?>
                    </li>
              <?php endforeach; ?>
            </ul>
            <button class="m10" type="submit"> <?php echo $this->translate('Save Settings');?> </button>
        </div>	
    </form>
  </div>
 <?php endif; ?>
  <?php if(count($this->combinations) != 0): ?>
  <form method='post'>
    <h4><?php echo $this->translate('Variations');?></h4>
    <div class="sitestoreproduct_data_table product_detail_table fleft mbot10">
        <table>
            <thead>
                <tr class="product_detail_table_head">
                  <th><?php echo $this->translate("Variation Name"); ?></th>
                  <th><?php echo $this->translate("Price"); ?></th>
                  <th><?php echo $this->translate("Available Quantity") ?></th>
                  <th class="txt_center"><?php echo $this->translate("Status") ?></th>
                  <?php if(count($this->field_ids)): ?>
                  <?php foreach($this->field_ids as $field_id) :?>
                  <th><?php echo $this->translate(Engine_Api::_()->getDbTable('cartproductFieldMeta', 'sitestoreproduct')->getFieldLabel($field_id));?></th>
                  <?php endforeach; ?>
                  <?php endif; ?>
                  <th><?php echo $this->translate("Options") ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($this->combinations as $combination_id => $combination): ?>
                <tr>
                  <td><?php echo $combination['name'] ?></td>
                  <?php if(empty($combination['price'])) : ?>
                  <td><?php echo $combination['price']; ?></td>
                  <?php else: ?>
                  <?php $price = Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($combination['price']); ?>
                  <td><?php echo ($combination['price'] > 0.00) ? '+' . $price :  $price; ?></td>
                  <?php endif; ?>
                  <td><input type="text" value="<?php echo $combination['quantity']; ?>" name='quantity_<?php echo $combination_id; ?>' style="width:50px;"></td>
                  <td class="txt_center">
                    <?php if(!empty($combination['status'])) : ?>
                    <?php echo $this->htmlLink(array('module' => 'sitestoreproduct', 'controller' => 'siteform', 'action' => 'change-status', 'combination_id' => $combination_id, 'status' => $combination['status'], 'product_id' => $this->product_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/approved.gif', '', array('title' => $this->translate('Disable Combination')))) ?> 
                    <?php else :?>
                    <?php echo $this->htmlLink(array('module' => 'sitestoreproduct', 'controller' => 'siteform', 'action' => 'change-status', 'combination_id' => $combination_id, 'status' => $combination['status'], 'product_id' => $this->product_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/disapproved.gif', '', array('title' => $this->translate('Enable Combination')))) ?>
                    <?php endif;?>
                    </td>
                  <?php if(count($this->field_ids)): ?>
                  <?php if(!empty($combination['attributes'])) :?>
                  <?php foreach($combination['attributes'] as $attribute): ?>
                  <td> <?php echo $attribute; ?></td>
                  <?php endforeach; ?>
                  <?php endif; ?>
                  <?php endif; ?>
                  <td><?php
                          echo $this->htmlLink(array('action' => 'delete-combination', 'combination_id' => $combination_id, 'reset' => false), $this->translate("delete"), array('class' => 'smoothbox'))
                          ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <button type="submit"><?php echo $this->translate('Save Changes'); ?></button>
  </form>
  <?php endif; ?>
  <?php elseif(count($this->dropDowns) != 0): ?>
  <div class="tip">
  <span><?php echo $this->translate('No Variation has been created for this product.'); ?> </span>
</div>
  <?php endif; ?>
 </div>


