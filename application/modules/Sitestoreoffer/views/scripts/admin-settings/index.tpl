<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreoffer
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

<script type="text/javascript">
	if($('sitestorecoupon_isprivate-1')) {
		$('sitestorecoupon_isprivate-1').addEvent('click', function(){
				$('sitestoreoffer_offer_show_menu-wrapper').setStyle('display', ($(this).get('value') == '1'?'none':'block'));
        $('sitestoreoffer_order-wrapper').setStyle('display', ($(this).get('value') == '1'?'none':'block'));
        $('sitestoreoffer_truncation_limit-wrapper').setStyle('display', ($(this).get('value') == '1'?'none':'block'));
        
		});
		$('sitestorecoupon_isprivate-0').addEvent('click', function(){
				$('sitestoreoffer_offer_show_menu-wrapper').setStyle('display', ($(this).get('value') == '0'?'block':'none'));
        $('sitestoreoffer_order-wrapper').setStyle('display', ($(this).get('value') == '0'?'block':'none'));
        $('sitestoreoffer_truncation_limit-wrapper').setStyle('display', ($(this).get('value') == '0'?'block':'none'));
		});
		window.addEvent('domready', function() {
			$('sitestoreoffer_offer_show_menu-wrapper').setStyle('display', ($('sitestorecoupon_isprivate-0').checked ?'block':'none'));
      $('sitestoreoffer_order-wrapper').setStyle('display', ($('sitestorecoupon_isprivate-0').checked ?'block':'none'));
      $('sitestoreoffer_truncation_limit-wrapper').setStyle('display', ($('sitestorecoupon_isprivate-0').checked ?'block':'none'));
		});
	}
</script>