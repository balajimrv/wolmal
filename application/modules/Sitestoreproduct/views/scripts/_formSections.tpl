<?php
$value = $_POST;
?>
<script type="text/javascript">
  function addSection(value){
    if(value == 'new')
      $('showText').style.display= 'inline-block';
    else{
      $('showText').style.display= 'none';
      $('inputsection_id').value = null;
    }
  }
</script>
<?php
$front = Zend_Controller_Front::getInstance();
$module = $front->getRequest()->getModuleName();
$action = $front->getRequest()->getActionName();
$controller = $front->getRequest()->getControllerName();
if ($module == 'sitestoreproduct' && $controller == 'index' && $action == 'edit') {
  $product_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('product_id');
  $tableUserName = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);
  $value['section_id'] = $tableUserName->section_id;
}
$sections = Engine_Api::_()->getDbTable('sections', 'sitestoreproduct')->getStoreSections($this->store_id);
$countSection = count($sections);
$availableSections = array();
if (!empty($countSection)) {
  foreach ($sections as $section) {
    $availableSections[$section->section_id] = $section->section_name;
  }
}
?>

<div class="form-wrapper" id="section_id-wrapper">
  <div class="form-label" id="section_id-label">
    <label for="section_id">
      <?php echo $this->translate("Section"); ?>
    </label>
  </div>
  <div class="form-element" id="section_id-element">


    <?php if (!empty($countSection)) : ?>
      <select id="section_id" name="section_id" onChange="addSection(this.value)">
        <option selected="selected" label="" value="0"></option>
        <?php foreach ($sections as $section) : ?> 
          <option <?php
      if (@array_key_exists('section_id', $value) && $value['section_id'] == $section->section_id) {
        echo 'selected="selected"';
      }
          ?>  value="<?php echo $section->section_id ?>"> <?php echo $this->translate($section->section_name); ?></option>
          <?php endforeach; ?>
        <option label="<?php echo $this->translate('Add New Section'); ?>" value="new"><?php echo $this->translate('Add New Section'); ?></option>
      </select>


      <span style="display:none" id="showText"><input type="text" name="inputsection_id" id="inputsection_id" values="<?php
        if (@array_key_exists('inputsection_id', $value)) {
          echo $value['inputsection_id'];
        }
          ?>"/> </span>
      <?php else : ?>
      <input type="hidden" name="section_id" id="section_id" value="new" />
      <input type="text" name="inputsection_id" id="inputsection_id" values="<?php
      if (@array_key_exists('inputsection_id', $value)) {
        echo $value['inputsection_id'];
      }
        ?>"/>
           <?php endif; ?>
  </div>
</div>