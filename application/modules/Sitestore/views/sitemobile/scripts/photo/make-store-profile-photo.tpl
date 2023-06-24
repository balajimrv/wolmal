<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: make-store-profile-photo.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
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
          <button type='submit' data-theme="b"><?php echo $this->translate('Save'); ?></button>
          <?php echo $this->translate('or'); ?>
          <a href="#" data-rel="back" data-role="button">
            <?php echo $this->translate('Cancel') ?>
          </a>
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