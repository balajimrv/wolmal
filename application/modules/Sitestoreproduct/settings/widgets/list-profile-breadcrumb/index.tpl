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

<div class="sr_sitestoreproduct_product_breadcrumb">
  <a href="<?php echo $this->url(array('action' => 'home'), 'sitestoreproduct_general', true);?>"><?php echo $this->translate("Products Home");?></a>
  <?php echo '<span class="brd-sep seaocore_txt_light">&raquo;</span>'; ?>
  <a href="<?php echo $this->url(array('action' => 'index'), 'sitestoreproduct_general', true);?>"><?php echo $this->translate("Browse Products");?></a>  
  <?php if ($this->category_name): ?>
    <?php echo '<span class="brd-sep seaocore_txt_light">&raquo;</span>'; ?>
      <a href="<?php echo $this->url(array('category_id' => $this->sitestoreproduct->category_id, 'categoryname' => Engine_Api::_()->getItem('sitestoreproduct_category', $this->sitestoreproduct->category_id)->getCategorySlug()), $this->categoryRouteName); ?>"><?php echo $this->translate($this->category_name); ?></a>
    <?php echo '<span class="brd-sep seaocore_txt_light">&raquo;</span>'; ?>
    <?php if (!empty($this->subcategory_name)): ?>
      <a href="<?php echo $this->url(array('category_id' => $this->sitestoreproduct->category_id, 'categoryname' => Engine_Api::_()->getItem('sitestoreproduct_category', $this->sitestoreproduct->category_id)->getCategorySlug(), 'subcategory_id' => $this->sitestoreproduct->subcategory_id, 'subcategoryname' => Engine_Api::_()->getItem('sitestoreproduct_category', $this->sitestoreproduct->subcategory_id)->getCategorySlug()), "sitestoreproduct_general_subcategory") ?>"><?php echo $this->translate($this->subcategory_name); ?></a>
      <?php echo '<span class="brd-sep seaocore_txt_light">&raquo;</span>'; ?>
      <?php if (!empty($this->subsubcategory_name)):?>
        <a href="<?php echo $this->url(array('category_id' => $this->sitestoreproduct->category_id, 'categoryname' => Engine_Api::_()->getItem('sitestoreproduct_category', $this->sitestoreproduct->category_id)->getCategorySlug(), 'subcategory_id' => $this->sitestoreproduct->subcategory_id, 'subcategoryname' => Engine_Api::_()->getItem('sitestoreproduct_category', $this->sitestoreproduct->subcategory_id)->getCategorySlug(), 'subsubcategory_id' => $this->sitestoreproduct->subsubcategory_id, 'subsubcategoryname' => Engine_Api::_()->getItem('sitestoreproduct_category', $this->sitestoreproduct->subsubcategory_id)->getCategorySlug()), "sitestoreproduct_general_subsubcategory") ?>"><?php echo $this->translate($this->subsubcategory_name); ?></a>
        <?php echo '<span class="brd-sep seaocore_txt_light">&raquo;</span>'; ?>
      <?php endif; ?>
    <?php endif; ?>
  <?php endif; ?>
  <?php echo $this->sitestoreproduct->getTitle(); ?>
</div>
