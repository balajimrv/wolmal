<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<!--ADD NAVIGATION-->
<?php include APPLICATION_PATH . '/application/modules/Sitemobile/views/scripts/adminNav.tpl';?>
<div class="sm_admin_link">
<a href="<?php echo $this->url(array('module' => 'sitemobile', 'controller' => 'tablet-content'), 'admin_default', true) ?>" class="buttonlink seaocore_icon_back" ><?php echo $this->translate("Back to Tablet Layout Editor"); ?></a></div>

<div class='seaocore_settings_form'>
  <div class='settings'>
    <?php echo $this->form->render($this); ?>
  </div>
</div>

