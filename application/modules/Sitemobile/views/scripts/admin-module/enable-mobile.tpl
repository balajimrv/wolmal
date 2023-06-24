<form method="post" class="global_form_popup">
  <div>
      <h3><?php echo $this->translate("Disable Directory / Page Plugin?") ?></h3>
      <p>
        <?php echo $this->translate("Are you sure that you want to disable the Directory / Page Plugin from mobile site? It will disable all Directory / Page Plugin Extensions.") ?>
      </p>
      <br />
      <p>
        <button type='submit'><?php echo $this->translate("Disable") ?></button>
        <?php echo $this->translate(" or ") ?> 
        <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'>
        <?php echo $this->translate("cancel") ?></a>
      </p>
  </div>
</form>