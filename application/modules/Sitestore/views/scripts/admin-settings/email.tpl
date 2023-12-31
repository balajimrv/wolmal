<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: email.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script type="text/javascript">
 	var sitemailtemplates = '<?php echo $sitemailtemplates = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitemailtemplates');?>';
  window.addEvent('domready', function() { 
    var e1 = $('sitestore_insightemail-1');
    var e2 = $('sitestore_demo');
    $('sitestore_insightmail_options-wrapper').setStyle('display', (e1.checked ?'block':'none'));
    if(sitemailtemplates == 0) {
			$('sitestore_header_color-wrapper').setStyle('display', (e1.checked ?'block':'none'));
			$('sitestore_bg_color-wrapper').setStyle('display', (e1.checked ?'block':'none'));
			$('sitestore_title_color-wrapper').setStyle('display', (e1.checked ?'block':'none'));
			$('sitestore_site_title-wrapper').setStyle('display', (e1.checked ?'block':'none'));
    }
    $('sitestore_demo-wrapper').setStyle('display', (e1.checked ?'block':'none'));
    $('sitestore_admin-wrapper').setStyle('display', (e2.checked && e1.checked ?'block':'none'));
 
 
 	  
    $('sitestore_insightemail-0').addEvent('click', function(){
      $('sitestore_insightmail_options-wrapper').setStyle('display', ($(this).checked ?'none':'block'));
      if(sitemailtemplates == 0) {
				$('sitestore_header_color-wrapper').setStyle('display', ($(this).checked ?'none':'block'));
				$('sitestore_bg_color-wrapper').setStyle('display', ($(this).checked ?'none':'block'));
				$('sitestore_title_color-wrapper').setStyle('display', ($(this).checked ?'none':'block'));
				$('sitestore_site_title-wrapper').setStyle('display', ($(this).checked ?'none':'block'));
      }
      $('sitestore_demo-wrapper').setStyle('display', ($(this).checked ?'none':'block'));
      $('sitestore_admin-wrapper').setStyle('display', ($(this).checked ?'none':'block'));
    });
 
    $('sitestore_insightemail-1').addEvent('click', function(){
      $('sitestore_insightmail_options-wrapper').setStyle('display', ($(this).checked ?'block':'none'));
      if(sitemailtemplates == 0) {
				$('sitestore_header_color-wrapper').setStyle('display', ($(this).checked ?'block':'none'));
				$('sitestore_bg_color-wrapper').setStyle('display', ($(this).checked ?'block':'none'));
				$('sitestore_title_color-wrapper').setStyle('display', ($(this).checked ?'block':'none'));
				$('sitestore_site_title-wrapper').setStyle('display', ($(this).checked ?'block':'none'));
      }
      $('sitestore_demo-wrapper').setStyle('display', ($(this).checked ?'block':'none'));
      $('sitestore_admin-wrapper').setStyle('display', (e2.checked && $(this).checked ?'block':'none'));
    });
       
    $('sitestore_demo').addEvent('click', function(){
      $('sitestore_admin-wrapper').setStyle('display', ($(this).checked && e1.checked ?'block':'none'));
    });
  });
</script>

<h2 class="fleft"><?php echo $this->translate('Stores / Marketplace - Ecommerce Plugin'); ?></h2>


<?php if (count($this->navigation)): ?>
  <div class='seaocore_admin_tabs clr'>
    <?php
    // Render the menu
    //->setUlClass()
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<div class='clear seaocore_settings_form'>
  <div class='settings'>
    <?php echo $this->form->render($this); ?>
  </div>
</div>
