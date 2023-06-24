<?php ?>

<form method="post" class="global_form_popup" data-ajax="false">
  <div>
    <h3><?php echo $this->translate('Delete ?'); ?></h3>
    <p>
      <?php echo $this->translate('Are you sure that you want to delete this field ?'); ?>
    </p>
    <br />
    <p>
      <button type='submit'><?php echo $this->translate('Delete'); ?></button>
      or <a href='javascript:void(0);' onclick='window.location.href="<?php echo $this->url(array(
          'module' => 'sitestoreproduct',
          'controller' => 'siteform',
          'action' => 'index-mobile',
          'product_id' => $this->product_id,
          'option_id' => $this->option_id,
      ), 'default', true);?>"'><?php echo $this->translate('cancel'); ?></a>
    </p>
  </div>
</form>