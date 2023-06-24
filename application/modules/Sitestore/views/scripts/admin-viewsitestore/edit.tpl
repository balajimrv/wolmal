<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: edit.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<h2 class="fleft"><?php echo $this->translate('Stores / Marketplace - Ecommerce Plugin'); ?></h2>


<?php if (count($this->navigation)): ?>
  <div class='seaocore_admin_tabs clr'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
  </div>
<?php endif; ?>

<div>
  <a href='<?php echo $this->url(array('module' => 'sitestore', 'controller' => 'viewsitestore'), 'admin_default', true) ?>' class="icon_sitestore_admin_back buttonlink"><?php echo $this->translate('Back to Manage Stores') ?></a>
</div>
<br />
<div class="settings">
  <?php echo $this->form->render($this) ?>
</div>
<style type="text/css">
  #buttons-label{display:none;}
</style>
