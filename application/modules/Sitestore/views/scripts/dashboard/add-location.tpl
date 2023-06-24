<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: add-location.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
$this->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&key=$apiKey");
?>
<?php
$this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/styles/style_sitestore_profile.css');
?>
<div class="sitestore_tellafriend_popup">
  <?php echo $this->form->render($this); ?>
</div>
<script type="text/javascript">
en4.core.runonce.add(function(){
	if(document.getElementById('location') && (('<?php echo !Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationspecific', 0);?>') || ('<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationspecific', 0);?>' && '<?php echo !Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationspecificcontent', 0); ?>'))) {
		var autocompleteSECreateLocation = new google.maps.places.Autocomplete(document.getElementById('location'));
		<?php include APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/location.tpl'; ?>
	}
});

  function showUpdateWarning(){
    if( $('product_location').checked){
      var alertMessage = '<?php echo $this->string()->escapeJavascript($this->translate("After submitting this form, all store products' location will be edited with this location. This action cannot be undone.")); ?>';
      var r=confirm(alertMessage);
      if (r==false)
      {
        $('product_location').checked=false;
      }
    }
  }
</script>

