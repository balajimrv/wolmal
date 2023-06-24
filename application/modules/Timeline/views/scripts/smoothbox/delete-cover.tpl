<div>
  <script type="text/javascript">
      setTimeout(function()
      {
        parent.Smoothbox.close();
      }, 1000);
      parent.$('timeline_cover').set('src', '<?php echo $this->default_cover ?>');
      parent.$('timeline_delete_cover').setStyle('display', 'none');
  </script>

  <div class="global_form_popup_message">
    <?php echo $this->translate('Your cover deleted.') ?>
  </div>
</div>