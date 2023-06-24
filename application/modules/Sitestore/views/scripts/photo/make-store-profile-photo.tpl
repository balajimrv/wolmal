<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: external-photo.tpl 7244 2010-09-01 01:49:53Z john $
 * @author     John
 */
?>
<div class='global_form_popup' style="padding: 10px;">
  <form method="post" >
    <div>
      <div>
        <h3><?php echo $this->translate('Make Store Profile Photo'); ?></h3>
        <p>
          <?php echo $this->translate("Do you want to make this photo your store profile photo?"); ?>
        </p>
        <br />
        <?php echo $this->itemPhoto($this->photo) ?>
        <br /><br />
        <p>
          <input type="hidden" name="confirm" value="true"/>
          <button type='submit'><?php echo $this->translate('Save'); ?></button>
          <?php echo $this->translate('or'); ?> <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'><?php echo $this->translate('cancel'); ?></a>
        </p>
      </div>
    </div>
  </form>
</div>
<?php if (@$this->closeSmoothbox): ?>
  <script type="text/javascript">
    TB_close();
  </script>
<?php endif; ?>