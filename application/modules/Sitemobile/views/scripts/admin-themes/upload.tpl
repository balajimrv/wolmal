<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: upload.tpl 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<!--ADD NAVIGATION-->
<?php include APPLICATION_PATH . '/application/modules/Sitemobile/views/scripts/adminNav.tpl'; ?>
<h3>
  <?php echo $this->translate("Mobile / Tablet Theme Editor") ?>
</h3>
<p>
  <?php echo $this->translate('SITEMOBILE_VIEWS_SCRIPTS_ADMINTHEMES_THEMES_DESCRIPTION') ?>
</p>
<br>	
<div class="settings">
<?php echo $this->form->render($this) ?>
</div>