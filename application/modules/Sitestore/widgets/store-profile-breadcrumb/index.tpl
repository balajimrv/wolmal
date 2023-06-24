<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<div class="sitestore_profile_breadcrumb">
  <?php 
    $temp_general_url = $this->url(array('action'=>'home'),'sitestore_general', false );

    if($this->category_name):
      $temp_general_category = $this->url(array('action' => 'index', 'category_id' => $this->sitestore->category_id, 'categoryname' => Engine_Api::_()->getItem('sitestore_category', $this->sitestore->category_id)->getCategorySlug()), "sitestore_general");
    endif;

    if(!empty($this->subcategory_name)):
      $temp_general_subcategory = $this->url(array('action' => 'index', 'category_id' => $this->sitestore->category_id, 'categoryname' => Engine_Api::_()->getItem('sitestore_category', $this->sitestore->category_id)->getCategorySlug(), 'subcategory_id' => $this->sitestore->subcategory_id, 'subcategoryname' => Engine_Api::_()->getItem('sitestore_category', $this->sitestore->subcategory_id)->getCategorySlug()), "sitestore_general");
    endif;

    if(!empty($this->subsubcategory_name)):
      $temp_general_subsubcategory = $this->url(array('action' => 'index', 'category_id' => $this->sitestore->category_id, 'categoryname' => Engine_Api::_()->getItem('sitestore_category', $this->sitestore->category_id)->getCategorySlug(), 'subcategory_id' => $this->sitestore->subcategory_id, 'subcategoryname' => Engine_Api::_()->getItem('sitestore_category', $this->sitestore->subcategory_id)->getCategorySlug(), 'subsubcategory_id' => $this->sitestore->subsubcategory_id, 'subsubcategoryname' => Engine_Api::_()->getItem('sitestore_category', $this->sitestore->subsubcategory_id)->getCategorySlug()), "sitestore_general");
    endif;
  ?>
  <a href="<?php echo $temp_general_url;?>">
    <?php echo $this->translate("Stores Home");?>
  </a>
  <?php echo '<span class="brd-sep seaocore_txt_light">&raquo;</span>'; ?>
  <?php if ($this->category_name && !empty($temp_general_category)): ?>
    <a href="<?php echo $temp_general_category; ?>"><?php echo $this->translate($this->category_name); ?></a>
    <?php echo '<span class="brd-sep seaocore_txt_light">&raquo;</span>'; ?>
    <?php if (!empty($this->subcategory_name) && !empty($temp_general_subcategory)): ?>
      <a href="<?php echo $temp_general_subcategory; ?>"><?php echo $this->translate($this->subcategory_name); ?></a>
      <?php echo '<span class="brd-sep seaocore_txt_light">&raquo;</span>'; ?>
      <?php if (!empty($this->subsubcategory_name) && !empty($temp_general_subsubcategory)):?>
        <a href="<?php echo $temp_general_subsubcategory; ?>"><?php echo $this->translate($this->subsubcategory_name); ?></a>
        <?php echo '<span class="brd-sep seaocore_txt_light">&raquo;</span>'; ?>
      <?php endif; ?>
    <?php endif; ?>
  <?php endif; ?>
  <?php echo $this->sitestore->getTitle(); ?>
</div>

<style type="text/css">

.sitestore_profile_breadcrumb{
  font-size:11px;
}
.sitestore_profile_breadcrumb .brd-sep{
  margin:0 3px;
}

</style>