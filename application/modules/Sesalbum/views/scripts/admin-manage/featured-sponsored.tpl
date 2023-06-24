<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesalbum
 * @package    Sesalbum
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: featured-sponsored.tpl 2015-06-16 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
?>
  <form method="post" class="global_form_popup">
    <div>
      <h3><?php echo $this->translate("Are you sure want to  ".$this->statusChange." this ".$this->params."?") ?></h3>
      <p>
        <?php //echo $this->translate("ALBUM_VIEWS_SCRIPTS_ADMINMANAGE_DELETE_DESCRIPTION") ?>
      </p>
      <br />
      <p>
        <input type="hidden" name="id" value="<?php echo $this->album_id?>"/>
         <input type="hidden" name="status" value="<?php echo $this->status?>"/>
          <input type="hidden" name="category" value="<?php echo $this->category?>"/>
          <input type="hidden" name="param" value="<?php echo $this->params?>"/>
        <button type='submit'><?php echo $this->translate("Change") ?></button>
        <?php echo $this->translate("or") ?>
        <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'>
        <?php echo $this->translate("cancel") ?></a>
      </p>
    </div>
  </form>

<?php if( @$this->closeSmoothbox ): ?>
<script type="text/javascript">
  TB_close();
</script>
<?php endif; ?>
