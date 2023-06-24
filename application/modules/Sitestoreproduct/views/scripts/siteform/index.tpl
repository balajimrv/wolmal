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

<?php include_once APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/scripts/_DashboardNavigation.tpl'; ?>

<div class="sr_sitestoreproduct_dashboard_content">
	<?php echo $this->partial('application/modules/Sitestoreproduct/views/scripts/dashboard/header.tpl', array('sitestoreproduct'=>$this->sitestoreproduct));?>
  <?php 
  $baseUrl = $this->layout()->staticBaseUrl;
  $this->headLink()
          ->appendStylesheet($this->layout()->staticBaseUrl
                  . 'application/modules/Sitestoreproduct/externals/styles/style_sitestoreproductform.css')
  ?>
  
  <script type="text/javascript">
    var option_id = '<?php echo $this->option_id; ?>';
    var product_id = '<?php echo $this->sitestoreproduct->product_id; ?>';
    '<?php $product = Engine_Api::_()->getItem('sitestoreproduct_product', $this->sitestoreproduct->product_id); 
    $productPriceAfterDiscount = Engine_Api::_()->sitestoreproduct()->getProductDiscount($product, '', '', 1); ?>';
  </script>

  <?php
  // Render the admin js
  echo $this->render('_jsAdminSitestoreproduct.tpl')
  ?>
  <div class="sitestoreproduct_form_separator b_medium"></div>
  <h3><?php echo $this->translate('Product Attributes') ?></h3>
  <p><?php echo $this->translate("Below, you can create multiple attributes for your product like size, color, etc. These attributes will be visible to buyers on the product profile page and they will be able to select desired attribute values while adding the product to cart. You will be able to create product variations with all the 'Select Box' type attributes listed here.") ?></p>
  <br />
  <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.combination', 1)): ?>
  <div class='tip'>
    <span>
      <?php echo "<b>".$this->translate("NOTE:")."</b>".$this->translate("you must create product variations using 'Select Box' type product attributes if you want buyers to choose the product variations with attributes that they want to purchase."); ?>
      </span>
   <br />
  <?php endif;?>
 
  <div class="seaocore_add mtop10">
    <a href="javascript:void(0);" onclick="void(0);" class="buttonlink seaocore_icon_add admin_fields_options_addquestion"><?php echo $this->translate("Add Product Attribute") ?></a>
    <a href="javascript:void(0);" onclick="void(0);" class="buttonlink admin_fields_options_saveorder" style="display:none;"><?php echo $this->translate("Save Order") ?></a>
  </div>
  <ul class="admin_fields">
    <?php foreach ($this->secondLevelMaps as $map): ?>
      <?php echo $this->adminFieldMeta($map, $productPriceAfterDiscount) ?>
    <?php endforeach; ?>
  </ul>
</div> 