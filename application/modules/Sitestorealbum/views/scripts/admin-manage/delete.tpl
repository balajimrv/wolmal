<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorealbum
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: delete.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<form method="post" class="global_form_popup">
  <div>
    <h3><?php echo $this->translate('Delete Store Album ?'); ?></h3>
    <p>
      <?php echo $this->translate('Are you sure that you want to delete this store album ? It will not be recoverable after being deleted.'); ?>
    </p>
    <?php if ($this->default_album_value) : ?>
      <br /><div class="tip">
        <span>
          <?php echo $this->translate('Please note that this is the default album of its store. If this album is deleted, then users other than the store admins will not be able to add photos to this store.'); ?>
        </span>     
      </div>
    <?php endif; ?>  
    <br />
    <p>
      <input type="hidden" name="confirm" value="<?php echo $this->album_id ?>"/>
      <button type='submit'><?php echo $this->translate('Delete'); ?></button>
      <?php echo $this->translate('or') ?> <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'><?php echo $this->translate('cancel'); ?></a>
    </p>
  </div>
</form>
<?php if (@$this->closeSmoothbox): ?>
  <script type="text/javascript">
    TB_close();
  </script>
<?php endif; ?>