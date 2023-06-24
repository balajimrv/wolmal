<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorealbum
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script type="text/javascript">
	window.addEvent('domready', function() { 
		lightbox_activityfeed_edit("<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorealbum.photolightbox.show', 1) ?>");
	});
	function show_activitymanual(option) {
		if($('sitestorealbum_photolightbox_activityedit-0').checked) {
			if(option == 1) {
				$('sitestorealbum_show_activitymanual-wrapper').style.display = 'none';
			}
			else {
				$('sitestorealbum_show_activitymanual-wrapper').style.display = 'block';
			}
		}
		else {
			$('sitestorealbum_show_activitymanual-wrapper').style.display = 'none';
		}
	}

	function lightbox_activityfeed_edit(option) {
		if($('sitestorealbum_photolightbox_activityedit-wrapper')) {
			if(option == 1) {
				$('sitestorealbum_photolightbox_activityedit-wrapper').style.display = 'block';
				show_activitymanual(0);
			}
			else {
				$('sitestorealbum_photolightbox_activityedit-wrapper').style.display = 'none';
				show_activitymanual(1);
			}
		}
	}
</script>

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
<?php if (count($this->navigation)): ?>
  <div class='tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
  </div>
<?php endif; ?>

<div class='clear sitestore_settings_form'>
  <div class='settings'>
    <?php echo $this->form->render($this) ?>
  </div>
</div>