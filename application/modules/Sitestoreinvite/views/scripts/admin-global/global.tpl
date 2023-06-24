<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreinvite
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: global.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
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

<?php if( count($this->navigationStoreGlobal) ): ?>
  <div class='tabs'>
    <?php
      echo $this->navigation()->menu()->setContainer($this->navigationStoreGlobal)->render()
    ?>
  </div>
<?php endif; ?>

<div class='clear'>
  <a href="<?php echo $this->url(array('module' => 'seaocore', 'controller' => 'admin-settings', 'action' => 'help-invite'), 'default', true) ?>" class="buttonlink" style="background-image:url(./application/modules/Sitestoreinvite/externals/images/admin/help.gif);"><?php echo $this->translate("Guidelines to configure the applications for Contact Importer Settings") ?></a>
  <br /><br />
  <div class='settings'>
    <?php echo $this->form->render($this) ?>
  </div>
</div>

<script type="text/javascript">
if (<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('storeinvite.friend.invite.enable', 1);?> == 0) {
  $('storeinvite_show_webmail-wrapper').style.display = 'none';		
  
}
  if ($('storeinvite_friend_invite_enable-1')) {
  $('storeinvite_friend_invite_enable-1').addEvent('click', function () {
  		$('storeinvite_show_webmail-wrapper').style.display = 'block';		
  })
}
if ($('storeinvite_friend_invite_enable-0')) {
  $('storeinvite_friend_invite_enable-0').addEvent('click', function () {
  		$('storeinvite_show_webmail-wrapper').style.display = 'none';		
  })
}
</script>
