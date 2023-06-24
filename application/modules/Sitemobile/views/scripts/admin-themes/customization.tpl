 
<?php 
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2014-10-09 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitemobile/externals/styles/mooRainbow.css');
$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitemobile/externals/scripts/mooRainbow.js');
?>

<?php include APPLICATION_PATH . '/application/modules/Sitemobile/views/scripts/adminNav.tpl'; ?>
<div class="sm-help-links">
        <a href="<?php echo $this->url(array('module' => 'sitemobile', 'controller' => 'themes', 'action' => 'customization'), 'admin_default', true) ?>" class="buttonlink icon_help" ><?php echo $this->translate("Customize Theme."); ?></a>
	<a href="<?php echo $this->url(array('module' => 'sitemobile', 'controller' => 'themes', 'action' => 'guidelines'), 'admin_default', true) ?>/#create-theme" class="buttonlink icon_help" ><?php echo $this->translate("Guidelines for creating a new theme."); ?></a>
  <a href="<?php echo $this->url(array('module' => 'sitemobile', 'controller' => 'themes', 'action' => 'guidelines'), 'admin_default', true) ?>/#edit-theme" class="buttonlink icon_help"><?php echo $this->translate("Guidelines for editing the active theme."); ?></a>
</div>	

<?php
  $this->form->setDescription("Below, you will be able to choose color scheme for your theme by selecting the radio buttons given below. You can also select the 'Custom Colors' option to customize your theme according to your site from the various available options. <br /><br />  [<b>Note:<b> If you are unable to customize your theme color scheme by yourself then you can purchase our service <a href='http://www.socialengineaddons.com/services/socialengineaddons-theme-customization-service' target='_blank'>SocialEngineAddOns Theme Customization Service</a>. Purchasing of this service will allow you to have our seamless support and assistance in customizing color scheme of your theme.]");
  $this->form->getDecorator('Description')->setOption('escape', false);
?>
<div class='seaocore_settings_form'>
  <div class='settings'>
    <?php echo $this->form->render($this); ?>
  </div>
</div>



<script type="text/javascript">
  function changeThemeCustomization(){
    if($("theme_customization-4").checked){
      $("sitemobile_theme_color-wrapper").style.display = 'block';
      $("sitemobile_theme_button_border_color-wrapper").style.display = 'block';
      $("sitemobile_landingpage_signinbtn-wrapper").style.display = 'block';
      $("sitemobile_landingpage_signupbtn-wrapper").style.display = 'block'; 
    }else {
      $("sitemobile_theme_color-wrapper").style.display = 'none';
      $("sitemobile_theme_button_border_color-wrapper").style.display = 'none';
      $("sitemobile_landingpage_signinbtn-wrapper").style.display = 'none';
      $("sitemobile_landingpage_signupbtn-wrapper").style.display = 'none'; 
    }
  }
  
  window.addEvent('domready', function() { 
    changeThemeCustomization();
  });
</script>
 