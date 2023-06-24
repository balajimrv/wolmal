<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreform
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
  $$('input[type=radio]:([name=sitestoreform_formtabseeting])').addEvent('click', function(e){
    $(this).getParent('.form-wrapper').getAllNext(':([id^=sitestoreform_captcha-element])').setStyle('display', ($(this).get('value')>0?'none':'none'));
  });
  if($('sitestoreform_formtabseeting-1')) {
    $('sitestoreform_formtabseeting-1').addEvent('click', function(){
      $('sitestoreform_captcha-wrapper').setStyle('display', ($(this).get('value')>0?'block':'none'));
    });
  }
  if($('sitestoreform_formtabseeting-0')) {	
    $('sitestoreform_formtabseeting-0').addEvent('click', function(){
      $('sitestoreform_captcha-wrapper').setStyle('display', ($(this).get('value')>0?'block':'none'));
    });
  }
	
  window.addEvent('domready', function() { 
    if($('sitestoreform_formtabseeting-1')) {
      var e4 = $('sitestoreform_formtabseeting-1');
      $('sitestoreform_captcha-wrapper').setStyle('display', (e4.checked ?'block':'none'));
    }
    if($('sitestoreform_formtabseeting-0')) {
      var e5 = $('sitestoreform_formtabseeting-0');
      $('sitestoreform_captcha-wrapper').setStyle('display', (e5.checked?'none':'block'));
    }
  });
</script>