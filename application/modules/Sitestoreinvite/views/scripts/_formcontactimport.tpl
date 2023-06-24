<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreinvite
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formcontactimport.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<h3><?php echo $this->translate("Contact Importer Settings"); ?></h3>
<br />

<div class="form-sub-heading">
  <div><?php echo $this->translate("Yahoo Contact Importer Settings"); ?></div>
  <div><a href="javascript:void(0);" onclick="show_instruction('id_yahoo');"><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitestoreinvite/externals/images/admin/help.gif"></a></div>
</div>


<div id="yahoo_apikey-wrapper" class="form-wrapper border0">
  <div id="yahoo_apikey-label" class="form-label">
    <label for="yahoo_apikey"><?php echo $this->translate("API Key"); ?></label>
  </div>
  <div id="yahoo_apikey-element" class="form-element">
    <input name="yahoo_apikey" id="yahoo_apikey" value="<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('yahoo.apikey'); ?>" type="text">
  </div>
</div>

<div id="yahoo_secretkey-wrapper" class="form-wrapper border0">
  <div id="yahoo_secretkey-label" class="form-label">
    <label for="yahoo_secretkey"><?php echo $this->translate("Shared Secret"); ?></label>
  </div>
  <div id="yahoo_secretkey-element" class="form-element">
    <input name="yahoo_secretkey" id="yahoo_secretkey" value="<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('yahoo.secretkey'); ?>" type="text">
    <div style="display:none" id="id_yahoo">
      <div class="sitestore_admin_popup">
        <div>
          <div class="bold"><?php echo $this->translate("Getting Yahoo API Key :-"); ?></div>

          <?php echo $this->translate("1) Go here to register your application:"); ?> <a href="https://developer.apps.yahoo.com/dashboard/createKey.html" target="_blank" style="color:#5BA1CD;">https://developer.apps.yahoo.com/dashboard/createKey.html</a><br />
          <div class="bold"><?php echo $this->translate("About creating your application :-"); ?></div>
          <?php echo $this->translate("a) Write your application name. You can make this the same as your site name."); ?><br />
          <?php echo $this->translate("b) In the 'Kind of Application' field, choose : 'Web-based'."); ?><br />
          <?php echo $this->translate("c) Write a short description about your application/website."); ?><br />
          <?php echo $this->translate("d) In the 'Application URL' field, write the URL of your site (example : http://www.mysite.com)."); ?><br />

          <div class="bold"><?php echo $this->translate("Security &amp; Privacy:-"); ?></div>
          <?php echo $this->translate("a) In the 'Application Domain' field, write your sites domain (example : http://www.mysite.com)."); ?><br />
          <?php echo $this->translate("b) Choose the 'Access Scopes' field to : 'This app requires access to private user data.'. Selecting this option will give you a list of Yahoo services. Among them, choose 'Yahoo! Contacts' in 'Read' mode."); ?><br />
          <?php echo $this->translate("c) Check on the 'Terms of Use' checkbox."); ?><br />
          <?php echo $this->translate("d) Now click on the 'Get API Key' button. After clicking on this button you will be redirected to a store where it would ask you to verify your domain. So, verify your domain."); ?><br />
          <?php echo $this->translate("e) After verifying your domain successfully, you will be redirected to a success store where you will get your 'API Key' and 'Shared Secret Key'. Copy them and paste these values in your site's Yahoo contact importer settings fields."); ?>
          <br /><br />
          <button onclick='javascript:parent.Smoothbox.close()'><?php echo $this->translate('Close'); ?></button>
        </div>
      </div>	
    </div>
  </div>
</div>

<div class="form-sub-heading">
  <div><?php echo $this->translate("Windows Live Contact Importer Settings"); ?></div>
  <div><a href="javascript:void(0);" onclick="show_instruction('id_windowlive');"><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitestoreinvite/externals/images/admin/help.gif"></a></div>
</div>

<div id="windowlive_apikey-wrapper" class="form-wrapper border0">
  <div id="windowlive_apikey-label" class="form-label">
    <label for="windowlive_apikey" ><?php echo $this->translate("Appid"); ?></label>
  </div>
  <div id="windowlive_apikey-element" class="form-element">
    <input name="windowlive_apikey" id="windowlive_apikey" value="<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('windowlive.apikey'); ?>" type="text">
  </div>
</div>


<div id="windowlive_secretkey-wrapper" class="form-wrapper border0">
  <div id="windowlive_secretkey-label" class="form-label">
    <label for="windowlive_secretkey" ><?php echo $this->translate("Secret"); ?></label>
  </div>
  <div id="windowlive_secretkey-element" class="form-element">
    <input name="windowlive_secretkey" id="windowlive_secretkey" value="<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('windowlive.secretkey'); ?>" type="text">

    <div style="display:none" id="id_windowlive">
      <div class="sitestore_admin_popup">
        <div>
          <div class="bold"><?php echo $this->translate("Getting your Windows Live Application ID :-"); ?></div>
          <?php echo $this->translate("To see intructions and register your application, go here :"); ?> <a href="http://msdn.microsoft.com/en-us/library/cc287659.aspx" target="_blank" style="color:#5BA1CD;">http://msdn.microsoft.com/en-us/library/cc287659.aspx.</a><br /><br />
          <b class="bold"><?php echo $this->translate("Note: "); ?></b>
          <?php echo $this->translate("Specify the Return URL as the URL of your website (example : http://www.mysite.com)."); ?><br />
          <b class="bold"><?php echo $this->translate("Note: "); ?></b>
          <?php $link_phpinfo = "<a href='" . $this->baseUrl() . "/admin/system/php' style='color:#5BA1CD;'>http://" . $_SERVER['HTTP_HOST'] . $this->baseUrl() . "/admin/system/php</a>";
          echo $this->translate("If you get this error : 'Call to undefined function mhash()', then you would need to get the PHP Mhash package installed on your server. Please ask your server administrator to install this. After installing mhash, it should be listed here. %s .This should resolve the issue.", $link_phpinfo); ?>
          <br /><br />
          <button onclick='javascript:parent.Smoothbox.close()'><?php echo $this->translate('Close'); ?></button>
        </div>
      </div>	
    </div>
  </div>
</div>

<div id="windowlive_policyurl-wrapper" class="form-wrapper">
  <div id="windowlive_policyurl-label" class="form-label">
    <label for="windowlive_policyurl" ><?php echo $this->translate("Privacy Policy URL"); ?></label>
  </div>
  <div id="windowlive_policyurl-element" class="form-element">
    <p class='description'>
      <?php echo $this->translate("Please enter your site's privacy policy URL. This is an optional field. If you do not enter the privacy policy URL, then the default privacy policy url of your site will be taken."); ?>

    </p>	
    <?php
    $policy_url = Engine_Api::_()->getApi('settings', 'core')->getSetting('windowlive.policyurl');
    if (empty($policy_url)) {
      $policy_url = 'http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl() . '/help/privacy';
    }
    ?>

    <input name="windowlive_policyurl" id="windowlive_policyurl" value="<?php echo $policy_url; ?>" type="text">
  </div>
</div>


<div class="form-sub-heading">
	<div><?php echo $this->translate("Google Contact Importer Setting");?></div>
	<div><a href="<?php echo $this->url(array('module' => 'seaocore', 'controller' => 'admin-settings', 'action' => 'help-invite'), 'default', true); ?>" ><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitestoreinvite/externals/images/admin/help.gif"></a></div>
</div>

<div id="google_apikey-wrapper" class="form-wrapper" style="border:none;">
  <div id="google_apikey-label" class="form-label">
    <label for="google_apikey" ><?php echo $this->translate("Client ID");?></label>
  </div>
  <div id="google_apikey-element" class="form-element">
    <input name="google_apikey" id="google_apikey" value="<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('google.apikey'); ?>" type="text">
  </div>
</div>


<div class="form-sub-heading">
	<div><?php echo $this->translate("LinkedIn Contact Importer Setting");?></div>
	<div><a href="<?php echo $this->url(array('module' => 'seaocore', 'controller' => 'admin-settings', 'action' => 'help-invite'), 'default', true) ; ?>" ><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitestoreinvite/externals/images/admin/help.gif"></a></div>
</div>

<div id="linkedin_apikey-wrapper" class="form-wrapper" style="border:none;">
  <div id="linkedin_apikey-label" class="form-label">
    <label for="linkedin_apikey" ><?php echo $this->translate("API Key");?></label>
  </div>
  <div id="linkedin_apikey-element" class="form-element">
    <input name="linkedin_apikey" id="linkedin_apikey" value="<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('linkedin.apikey'); ?>" type="text">
  </div>
</div>
<div id="linkedin_secretkey-wrapper" class="form-wrapper" style="border:none;">
  <div id="linkedin_secretkey-label" class="form-label">
    <label for="linkedin_secretkey" ><?php echo $this->translate("Secret Key");?></label>
  </div>
  <div id="linkedin_secretkey-element" class="form-element">
    <input name="linkedin_secretkey" id="linkedin_secretkey" value="<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('linkedin.secretkey'); ?>" type="text">
  </div>
</div>


<script type="text/javascript">
  function show_instruction (id) {
    var show_instruct = $(id).innerHTML;
    Smoothbox.open(show_instruct);
		
  }
</script>