<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: savelayout.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<div class="global_form_popup">
  <h3><?php echo $this->translate('Reset Layout of Store Profiles?'); ?></h3>
  <p>
    <?php echo $this->translate('Are you sure you want to reset the layout of profiles of stores on your site to the one selected by you? The profile layouts of all the existing stores on your site will also be reset to the one selected by you.'); ?>
  </p>
  <br />
  <p>    
    <button onclick='window.parent.continuelayout(); return false;'><?php echo $this->translate('Continue'); ?></button>
    or <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'><?php echo $this->translate('cancel'); ?></a>
  </p>
</div>
<?php if (@$this->closeSmoothbox): ?>
  <script type="text/javascript">
    TB_close();
  </script>
<?php endif; ?>