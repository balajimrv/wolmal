<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formSubcategory.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */ 
?>
<?php
$tabel = Engine_Api::_()->getDbTable('categories', 'sitestoreproduct');
$subCategories = $tabel->getCategoriesByLevel('subcategory');

if (count($subCategories) == 0)
  return;

$request = Zend_Controller_Front::getInstance()->getRequest();
$module = $request->getModuleName();
$controller = $request->getControllerName();
$action = $request->getActionName();
$params = $request->getParams();
$catParams = array();
if ((isset($params['subcategory_id']) && $cat = $params['subcategory_id']) || (isset($params['subcategory']) && $cat = $params['subcategory'])) {
  $catParams[] = array('type' => 'subcategory', 'value' => $cat, 'isChildSet' => 1);
  if ((isset($params['subsubcategory_id']) && $cat = $params['subsubcategory_id']) || (isset($params['subsubcategory']) && $cat = $params['subsubcategory'])) {
    $catParams[] = array('type' => 'subsubcategory', 'value' => $cat);
  }
}


$subsubCategories = $tabel->getCategoriesByLevel('subsubcategory');
?>

<script type="text/javascript">

  sm4.sitestoreproduct.searchArray={
    '<?php echo $module . '_' . $controller . "_" . $action ?>':{
      profile_type: 0,
      previous_mapped_level:0
    }
  };
   
  function showSMFields(cat_value, cat_level) { 
    var content= sm4.sitestoreproduct.searchArray.<?php echo $module . '_' . $controller . "_" . $action ?>; 
    if(cat_level == 1 || (content.previous_mapped_level >= cat_level && content.previous_mapped_level != 1) || (content.profile_type == null || content.profile_type == '' || content.profile_type == 0)) {
      content.profile_type = getSMProfileType(cat_value); 
      if(content.profile_type == 0) { content.profile_type = ''; } else { content.previous_mapped_level = cat_level; }
      $.mobile.activePage.find('#profile_type').value = content.profile_type;
      changeFields($.mobile.activePage.find('#profile_type'));      
    }
  }
  
 
  var getSMProfileType = function(category_id) {
    var mapping = <?php echo Zend_Json_Encoder::encode(Engine_Api::_()->getDbTable('categories', 'sitestoreproduct')->getMapping(null, 'profile_type')); ?>;
    for(i = 0; i < mapping.length; i++) {
      if(mapping[i].category_id == category_id)
        return mapping[i].profile_type;
    }
    return 0;
  }
</script>


<div id='subcategory_id-wrapper' class='form-wrapper dnone'>
  <div class='form-label'><label><?php echo $this->translate('Subcategory', "<sup>rd</sup>") ?></label></div>
  <div class='form-element'>
    <select name='subcategory_id' id='subcategory_id' onchange="showSMFields(this.value, 2);sm4.core.category.set(this.value,'subsubcategory');">
      <option value="0" ></option>
      <?php foreach ($subCategories as $category): ?>
        <option class="subcategory_option dnone" value="<?php echo $category->getIdentity() ?>" data-parent_category="<?php echo "sp_cat_" . $category->cat_dependency; ?>" ><?php echo $this->translate($category->getTitle(true)); ?></option>
      <?php endforeach; ?>
    </select>
  </div>
</div>
<?php if (count($subsubCategories) > 0): ?>
  <div id='subsubcategory_id-wrapper' class='form-wrapper dnone'>
    <div class='form-label'><label><?php echo $this->translate('3%s Level Category', "<sup>rd</sup>") ?> </label></div>
    <div class='form-element'>
      <select name='subsubcategory_id' id='subsubcategory_id' onchange="showSMFields(this.value, 3);sm4.core.category.onChange('subsubcategory',this.value);">
        <option value="0" ></option>
        <?php foreach ($subsubCategories as $category): ?>
          <option class="subsubcategory_option dnone" value="<?php echo $category->getIdentity() ?>" data-parent_category="<?php echo "sp_cat_" . $category->cat_dependency; ?>" ><?php echo $this->translate($category->getTitle(true)); ?></option>
        <?php endforeach; ?>
      </select>
    </div>
  </div>
<?php endif; ?>  
<script type="text/javascript">
  sm4.core.runonce.add(function(){
    sm4.core.category.setDefault(<?php echo $this->jsonInline($catParams) ?>);
  });
</script>