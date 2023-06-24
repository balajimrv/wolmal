<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreurl
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
<?php  $is_element = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreurl.is.enable', 0);?>
<?php if(empty($is_element)):?>
	<div class="tip">
		<span>
			<?php echo $this->translate('This plugin enables you to set a limit for the number of Likes for a Store before the simplified short URL is assigned to it. This solves 2 purposes: One, more Likes of a Store would be indicative of its genuineness, and thus validity of its short URL. Second, a limit on Likes for these URLs to be valid for the respective Stores will motivate the Store Owners to gather more Likes for their Stores on your site.
If the short URL of any Store on your site is similar to the URL of a standard plugin store, then that URL will open that Store profile and not the standard plugin store. To avoid such a situation, edit the URL of such a Store using the “Manage Banned Store URLs” section.');?>
		</span>
	</div>
<?php endif;?>

<?php if( count($this->navigationStoreGlobal) ): ?>
  <div class='tabs'>
    <?php
      echo $this->navigation()->menu()->setContainer($this->navigationStoreGlobal)->render()
    ?>
  </div>
<?php endif; ?>



<div class='clear sitestore_settings_form'>
  <div class='settings'>
    <?php echo $this->form->render($this) ?>
  </div>
</div>
<?php if(!empty($is_element)):?>
	<script type="text/javascript">
	window.addEvent('domready', function() {
			showurl("<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.change.url', 1) ?>");
			showediturl("<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.showurl.column', 1) ?>");
		});	

		function showurl(option) {
			if(option == 1) {
				$('sitestore_likelimit_forurlblock-wrapper').style.display = 'block';
			}
			else {
				$('sitestore_likelimit_forurlblock-wrapper').style.display = 'none';
			}
		}

		function showediturl(option) {
			if(option == 1) {
				$('sitestore_edit_url-wrapper').style.display = 'block';
			}
			else {
				$('sitestore_edit_url-wrapper').style.display = 'none';
			}
		}

	</script>
<?php endif;?>