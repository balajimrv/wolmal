               
 
<?php if(!empty($this->store_id)){ ?>
 <a href='<?php echo $this->url(array('action' => 'create', 'store_id' =>$this->store_id), 'sitestoreproduct_general', true) ?>' class='buttonlink seaocore_icon_add '><?php echo $this->translate('Create Product'); ?> </a>
<?php } else { ?>
  <a href='<?php echo $this->url(array('action' => 'list'), 'sitestoreproduct_general', true) ?>' class='buttonlink smoothbox icon_type_document_new seaocore_icon_add '><?php echo $this->translate('Create Product'); ?> </a> 
 <?php } ?>