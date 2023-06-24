<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/styles/style_sitestoreproductprofile.css'); ?>
<?php if (empty($this->showMessage)): ?>

<?php if($this->showContent): ?>
<div class="global_form_popup" style="width: 450px">
  <h2><?php echo $this->translate('Product Specification');?></h2>
	<div class='sr_sitestoreproduct_pro_specs'>
		<?php if(!empty($this->otherDetails)): ?>
			<?php echo Engine_Api::_()->sitestoreproduct()->removeMapLink($this->fieldValueLoop($this->sitestoreproduct, $this->fieldStructure)) ?>
	  <?php else: ?>
	    <div class="tip">
        <span ><?php echo$this->translate("There is no information available.");  ?></span>
	    </div>
		<?php endif; ?>
	</div>
</div>
<?php endif; ?>
<?php else: ?>
<div>
  <span class="tip">
    <?php echo $this->translate('No Specifications available'); ?>
  </span>
</div>
<?php endif; ?>


<div class='buttons mtop10'>
    <button type='button' name="cancel" onclick="javascript:parent.Smoothbox.close();"><?php echo $this->translate("Close") ?></button>
</div>
