<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    SiteStoreadmincontact
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>


<h2 class="fleft"><?php echo $this->translate('Stores / Marketplace - Ecommerce Plugin'); ?></h2>
<?php if (count($this->navigationStore)): ?>
  <div class='seaocore_admin_tabs clr'>
  <?php
  // Render the menu
  //->setUlClass()
  echo $this->navigation()->menu()->setContainer($this->navigationStore)->render()
  ?>
  </div>
<?php endif; ?>
<script type="text/javascript">
  window.addEvent('domready', function() {
    var storecontactEmailDemo = "<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('store.contactemail.demo',1);?>";   
    showStoreOption(storecontactEmailDemo);
  });
function showStoreOption(option) {
  if(option == true) {
    $('store_contactemail_admin-wrapper').style.display = 'block';
  } else {
    $('store_contactemail_admin-wrapper').style.display = 'none';
  }
}
</script>

<?php if (count($this->navigation)): ?>
  <div class='tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
  </div>
<?php endif; ?>

<?php if (!empty($this->messageSent)): ?>
  <ul class="form-notices" >
    <li>
      <?php echo $this->successMessge; ?>
    </li>
  </ul>
<?php endif; ?>
<div class='clear sitestore_settings_form'>
  <div class='settings'>
    <?php echo $this->form->render($this) ?>
  </div>
</div>


<style type="text/css">
.defaultSkin iframe {
 height: 250px !important;
 width: 625px !important;
}
</style>