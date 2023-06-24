<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: product-type-details.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<div>
<!--  <p>
    <span>
      <b class="bold"><?php //echo $this->translate("Product Types:") ?></b>
      <?php //echo $this->translate(array('There is %s Product Type ', 'There are %s Product Types ', $this->productTypeCount), $this->locale()->toNumber($this->productTypeCount)); ?>
      <?php //echo $this->translate("available in our Community") ?>
    </span>
  </p>-->
  
  <?php if( empty($this->productType) || @in_array('simple', $this->productType) ) : ?>
    <p>
      <?php echo $this->translate("<b>- Simple Products:</b> A Simple product is a physical item that can be sold individually and has its own SKU. Simple products can be used in association with Grouped, Bundled, and Configurable Products."); ?>
    </p>
  <?php  endif; ?>

  <?php if( empty($this->productType) || @in_array('configurable', $this->productType) ) : ?>
    <p>
      <?php echo $this->translate("<b>- Configurable Products:</b> A Configurable product is a simple product with variety of options to be selected from drop-down lists. Each combination of drop-down options represents a separate simple product with a distinct SKU which enables you (the store admin) to maintain your inventory."); ?>
    </p>
  <?php  endif; ?>

  <?php if( empty($this->productType) || @in_array('virtual', $this->productType) ) : ?>
    <p>
      <?php echo $this->translate("<b>- Virtual Product:</b> A Virtual product does not have any physical existence, and it only represents something that can be sold, such as a warranty, a service, subscription, coupon, etc. Virtual products can be associated with Grouped and Bundled products."); ?>
    </p>
  <?php  endif; ?>

  <?php if( empty($this->productType) || @in_array('grouped', $this->productType) ) : ?>
    <p>
      <?php echo $this->translate("<b>- Grouped Products:</b> Grouped products allow you to create a new product using one or more existing products in your store. You can choose different variations of a product to be part of a Grouped Product. Price of a Grouped Product is summation of its component products.<br /><strong>Note:</strong> To create this type of product, you must have at least two simple products in your store."); ?>
    </p>
  <?php  endif; ?>

  <?php if( empty($this->productType) || @in_array('bundled', $this->productType) ) : ?>
    <p>
      <?php echo $this->translate("<b>- Bundled Products:</b> Bundled Products are also known as Product Kits. Price of a Bundled Product can be made less than the combined price of its component products.<br /><strong>Note:</strong> To create this type of product, you must have at least two products in your store."); ?>
    </p>
  <?php  endif; ?>

  <?php if( empty($this->productType) || @in_array('downloadable', $this->productType) ) : ?>
    <p>
      <?php echo $this->translate("<b>- Downloadable Product:</b> Downloadable products are similar to virtual products, but they include one or more digital files for download."); ?>
    </p>
  <?php  endif; ?>
</div>

<style type="text/css">
  html, body{
  height: 100%;
  }
  #global_content_simple{
  overflow: auto;
  }
 div{
   padding: 10px;
   font-size: 13px;
 }
  div p{
  margin-bottom: 15px;
  }
</style>  