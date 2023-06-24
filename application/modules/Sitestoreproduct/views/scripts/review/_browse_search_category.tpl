<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _browse_search_category.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<div class="form-wrapper" id="subcategory_id-wrapper" style='display:none;'>
  <div class="form-label" id="subcategory_id-label">
    <label class="optional" for="subcategory_id"><?php echo $this->translate('Sub-Category'); ?></label>
  </div>
  <div class="form-element" id="subcategory_id-element">
    <select id="subcategory_id" name="subcategory_id" onchange='showFields(this.value, 2); addOptions(this.value, "subcat_dependency", "subsubcategory_id", 0);'></select>
  </div>
</div>

<div class="form-wrapper" id="subsubcategory_id-wrapper" style='display:none;'>
  <div class="form-label" id="subsubcategory_id-label">
    <label class="optional" for="subsubcategory_id"><?php echo $this->translate('3%s Level Category', "<sup>rd</sup>") ?></label>
  </div>
  <div class="form-element" id="subsubcategory_id-element">
    <select id="subsubcategory_id" name="subsubcategory_id" onchange='showFields(this.value, 3);  setSubSubCategorySlug(this.value);' ></select>
  </div>
</div>

<script type="text/javascript">
  function setSubSubCategorySlug(value) {
    $('subsubcategoryname').value = sitestoreproduct_categories_slug[value];
  }
</script>  