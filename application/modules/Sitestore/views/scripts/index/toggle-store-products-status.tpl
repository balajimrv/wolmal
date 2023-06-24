<form method="post" class="global_form_popup">
  <div>
    <h3><?php $enable = $this->enable;
if ($enable):
  echo $this->translate('Enable Store Products?');
else:
  echo $this->translate('Disable Store Products?');
endif;
?></h3>
    <p>
      <?php if ($enable):
        echo $this->translate('Are you sure that you want to enable all the products of this Store?');
      else:
        echo $this->translate('Are you sure that you want to disable all the products of this Store?');
      endif;  ?>
    </p>
    <br />
    <p>
      <input type="hidden" name="confirm" value="<?php echo $this->store_id ?>"/>
      <input type="hidden" name="status" value="<?php echo $enable ?>"/>
      
      <button type='submit' name="Yes" ><?php echo $this->translate('Yes'); ?></button>  
      <button type='submit' name="No" ><?php echo $this->translate('No'); ?></button>
    </p>
  </div>
</form>
<?php if (@$this->closeSmoothbox): ?>
  <script type="text/javascript">
    TB_close();
  </script>
<?php endif; ?>